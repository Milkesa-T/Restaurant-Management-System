<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class OrderPerformance extends Model
{
    protected $table = 'order_performance';

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'order_id',
        'approval_duration_sec',
        'kitchen_queue_wait_sec',
        'chef_prep_duration_sec',
        'pickup_delay_sec',
        'serving_duration_sec',
        'payment_duration_sec',
        'total_lifecycle_sec',
        'approval_sla_breached',
        'prep_sla_breached',
        'pickup_sla_breached',
        'service_sla_breached',
        'overall_sla_breached',
        'last_calculated_at'
    ];

    protected $casts = [
        'approval_sla_breached' => 'boolean',
        'prep_sla_breached' => 'boolean',
        'pickup_sla_breached' => 'boolean',
        'service_sla_breached' => 'boolean',
        'overall_sla_breached' => 'boolean',
        'last_calculated_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
