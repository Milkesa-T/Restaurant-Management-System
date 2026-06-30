<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id', 
        'menu_item_id', 
        'variant_id', 
        'quantity', 
        'notes', 
        'unit_price', 
        'total_price', 
        'current_status', 
        'chef_id',
        'scheduled_fire_at',
        'cooking_started_at',
        'cooking_completed_at',
    ];

    protected $casts = [
        'scheduled_fire_at' => 'datetime',
        'cooking_started_at' => 'datetime',
        'cooking_completed_at' => 'datetime',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'quantity' => 'integer',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function menuItem()
    {
        return $this->belongsTo(MenuItem::class);
    }

    public function variant()
    {
        return $this->belongsTo(ItemVariant::class, 'variant_id');
    }

    public function chef()
    {
        return $this->belongsTo(User::class, 'chef_id');
    }

    public function orderItemAddons()
    {
        return $this->hasMany(OrderItemAddon::class);
    }
}
