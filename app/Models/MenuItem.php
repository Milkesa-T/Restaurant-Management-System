<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    protected $fillable = [
        'category_id', 
        'kitchen_station_id', 
        'name', 
        'description', 
        'price', 
        'image_path', 
        'estimated_prep_time', 
        'availability_status',
        'status', 
        'proposed_by_user_id', 
        'proposed_price', 
        'approved_by_user_id', 
        'approved_at'
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'proposed_price' => 'decimal:2',
        'price' => 'decimal:2',
        'estimated_prep_time' => 'integer',
    ];

    public function category()
    {
        return $this->belongsTo(MenuCategory::class, 'category_id');
    }

    public function kitchenStation()
    {
        return $this->belongsTo(KitchenStation::class, 'kitchen_station_id');
    }

    public function variants()
    {
        return $this->hasMany(ItemVariant::class, 'menu_item_id');
    }

    public function addons()
    {
        return $this->belongsToMany(ItemAddon::class, 'menu_item_addon', 'menu_item_id', 'item_addon_id');
    }

    public function recipes()
    {
        return $this->hasMany(Recipe::class, 'menu_item_id');
    }

    public function proposedBy()
    {
        return $this->belongsTo(User::class, 'proposed_by_user_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by_user_id');
    }
}
