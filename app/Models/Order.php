<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'branch_id', 'table_id', 'order_number', 'customer_name', 'customer_phone',
        'source', 'current_status', 'payment_status', 'subtotal', 'discount_amount',
        'tax_amount', 'service_charge', 'grand_total', 'waiter_id', 'cashier_id',
        'submitted_at', 'approved_at', 'sent_to_kitchen_at', 'chef_started_at', 'chef_completed_at',
        'waiter_picked_at', 'served_at', 'bill_requested_at', 'paid_at', 'closed_at',
        'cancelled_at', 'rejected_at'
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
        'sent_to_kitchen_at' => 'datetime',
        'chef_started_at' => 'datetime',
        'chef_completed_at' => 'datetime',
        'waiter_picked_at' => 'datetime',
        'served_at' => 'datetime',
        'bill_requested_at' => 'datetime',
        'paid_at' => 'datetime',
        'closed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    public function waiter()
    {
        return $this->belongsTo(User::class, 'waiter_id');
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
