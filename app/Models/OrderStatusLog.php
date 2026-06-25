<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderStatusLog extends Model
{
    protected $table = 'order_status_logs';

    protected $fillable = [
        'order_id', 'order_item_id', 'from_status', 'to_status',
        'changed_by_user_id', 'changed_by_role', 'changed_at',
        'duration_in_previous_status_sec', 'sla_threshold_sec',
        'sla_breached', 'note', 'device_source'
    ];

    protected $casts = [
        'changed_at' => 'datetime',
        'sla_breached' => 'boolean',
    ];

    protected static function booted()
    {
        static::creating(function ($log) {
            if (empty($log->changed_at)) {
                $log->changed_at = now();
            }

            // Find the previous log for this order to compute duration
            $query = self::where('order_id', $log->order_id);
            if ($log->id) {
                $query->where('id', '!=', $log->id);
            }
            $prevLog = $query->latest('changed_at')
                ->latest('id')
                ->first();

            if ($prevLog) {
                $log->from_status = $prevLog->to_status;
                $duration = abs(\Carbon\Carbon::parse($log->changed_at)->diffInSeconds($prevLog->changed_at));
                $log->duration_in_previous_status_sec = $duration;
            } else {
                $log->from_status = $log->from_status ?? 'none';
                $log->duration_in_previous_status_sec = 0;
            }

            // Look up SLA configuration
            $sla = SlaThreshold::where('from_status', $log->from_status)
                ->where('to_status', $log->to_status)
                ->first();

            if ($sla) {
                $log->sla_threshold_sec = $sla->target_seconds;
                $log->sla_breached = $log->duration_in_previous_status_sec > $sla->target_seconds;
            } else {
                $log->sla_threshold_sec = null;
                $log->sla_breached = false;
            }
        });

        static::created(function ($log) {
            // Update order_performance record
            $perf = OrderPerformance::firstOrCreate(['order_id' => $log->order_id]);

            // Map standard transitions to performance columns
            if ($log->from_status === 'pending' && $log->to_status === 'approved') {
                $perf->approval_duration_sec = $log->duration_in_previous_status_sec;
                $perf->approval_sla_breached = $log->sla_breached;
            } elseif ($log->from_status === 'approved' && $log->to_status === 'preparing') {
                $perf->kitchen_queue_wait_sec = $log->duration_in_previous_status_sec;
            } elseif ($log->from_status === 'preparing' && $log->to_status === 'ready') {
                $perf->chef_prep_duration_sec = $log->duration_in_previous_status_sec;
                $perf->prep_sla_breached = $log->sla_breached;
            } elseif ($log->from_status === 'ready' && $log->to_status === 'served') {
                $perf->serving_duration_sec = $log->duration_in_previous_status_sec;
                $perf->service_sla_breached = $log->sla_breached;
            }

            // Support optional waiter pick-up flow
            if ($log->from_status === 'ready' && $log->to_status === 'picked_up') {
                $perf->pickup_delay_sec = $log->duration_in_previous_status_sec;
                $perf->pickup_sla_breached = $log->sla_breached;
            } elseif ($log->from_status === 'picked_up' && $log->to_status === 'served') {
                $perf->serving_duration_sec = $log->duration_in_previous_status_sec;
                $perf->service_sla_breached = $log->sla_breached;
            }

            // Support payment duration mapping
            if (in_array($log->from_status, ['served', 'bill_requested']) && $log->to_status === 'paid') {
                $perf->payment_duration_sec = $log->duration_in_previous_status_sec;
            }

            // Update overall service/total lifecycle if order is finished
            if (in_array($log->to_status, ['served', 'paid', 'closed', 'cancelled', 'rejected'])) {
                $firstLog = self::where('order_id', $log->order_id)
                    ->orderBy('changed_at', 'asc')
                    ->orderBy('id', 'asc')
                    ->first();

                if ($firstLog) {
                    $totalSec = abs(\Carbon\Carbon::parse($log->changed_at)->diffInSeconds($firstLog->changed_at));
                    $perf->total_lifecycle_sec = $totalSec;

                    $overallSla = SlaThreshold::where('from_status', 'pending')
                        ->where('to_status', 'served')
                        ->first();

                    $lifecycleBreached = $overallSla ? ($perf->total_lifecycle_sec > $overallSla->target_seconds) : false;
                    $perf->overall_sla_breached = $lifecycleBreached 
                        || $perf->approval_sla_breached 
                        || $perf->prep_sla_breached 
                        || $perf->pickup_sla_breached 
                        || $perf->service_sla_breached;
                }
            }

            $perf->last_calculated_at = now();
            $perf->save();
        });
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'changed_by_user_id');
    }
}
