<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    protected $fillable = [
<<<<<<< Updated upstream
        'branch_id', 'table_id', 'original_table_id', 'dining_area_id',
=======
        'branch_id', 'table_id', 'original_table_id', 'dining_area_id', 'dining_session_id',
>>>>>>> Stashed changes
        'order_number', 'source',
        'created_by_user_id', 'waiter_id', 'chef_id', 'cashier_id',
        'subtotal', 'discount_amount', 'tax_amount', 'service_charge', 'grand_total',
        'current_status', 'payment_status',
        'submitted_at', 'approved_at', 'rejected_at',
        'sent_to_kitchen_at', 'chef_started_at', 'chef_completed_at',
        'waiter_picked_at', 'served_at', 'bill_requested_at',
        'paid_at', 'closed_at', 'cancelled_at',
        'table_transfer_count', 'last_table_transfer_at', 'waiter_alerted_at',
        'cancellation_reason', 'rejection_reason',
    ];

    protected $casts = [
        'submitted_at'          => 'datetime',
        'approved_at'           => 'datetime',
        'rejected_at'           => 'datetime',
        'sent_to_kitchen_at'    => 'datetime',
        'chef_started_at'       => 'datetime',
        'chef_completed_at'     => 'datetime',
        'waiter_picked_at'      => 'datetime',
        'served_at'             => 'datetime',
        'bill_requested_at'     => 'datetime',
        'paid_at'               => 'datetime',
        'closed_at'             => 'datetime',
        'cancelled_at'          => 'datetime',
        'last_table_transfer_at' => 'datetime',
        'waiter_alerted_at'     => 'datetime',
        'table_transfer_count'  => 'integer',
        'subtotal'              => 'decimal:2',
        'discount_amount'       => 'decimal:2',
        'tax_amount'            => 'decimal:2',
        'service_charge'        => 'decimal:2',
        'grand_total'           => 'decimal:2',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function table()
    {
        return $this->belongsTo(Table::class);
    }

<<<<<<< Updated upstream
=======
    public function diningSession()
    {
        return $this->belongsTo(DiningSession::class);
    }

>>>>>>> Stashed changes
    public function originalTable()
    {
        return $this->belongsTo(Table::class, 'original_table_id');
    }

    public function diningArea()
    {
        return $this->belongsTo(DiningArea::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function waiter()
    {
        return $this->belongsTo(User::class, 'waiter_id');
    }

    public function chef()
    {
        return $this->belongsTo(User::class, 'chef_id');
    }

    public function cashier()
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function statusLogs()
    {
        return $this->hasMany(OrderStatusLog::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function performance()
    {
        return $this->hasOne(OrderPerformance::class);
    }
}
