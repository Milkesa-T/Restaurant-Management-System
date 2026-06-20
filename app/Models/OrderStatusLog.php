<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderStatusLog extends Model
{
    protected $table = 'order_status_logs';

    protected $fillable = [
        'order_id', 'order_item_id', 'from_status', 'to_status',
        'changed_by_user_id', 'changed_by_role', 'changed_at',
        'duration_seconds', 'sla_breached', 'note', 'device_source'
    ];

    protected $casts = [
        'changed_at' => 'datetime',
        'sla_breached' => 'boolean',
    ];

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
