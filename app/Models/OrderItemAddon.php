<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItemAddon extends Model
{
    protected $table = 'order_item_addons';

    protected $fillable = ['order_item_id', 'item_addon_id', 'quantity', 'unit_price', 'total_price'];

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function addon()
    {
        return $this->belongsTo(ItemAddon::class, 'item_addon_id');
    }
}
