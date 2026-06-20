<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $fillable = ['restaurant_id', 'name', 'address', 'phone', 'is_main'];

    protected $casts = [
        'is_main' => 'boolean',
    ];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function diningAreas()
    {
        return $this->hasMany(DiningArea::class);
    }

    public function menuCategories()
    {
        return $this->hasMany(MenuCategory::class);
    }

    public function itemAddons()
    {
        return $this->hasMany(ItemAddon::class);
    }

    public function inventoryItems()
    {
        return $this->hasMany(InventoryItem::class);
    }

    public function suppliers()
    {
        return $this->hasMany(Supplier::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function settings()
    {
        return $this->hasMany(Setting::class);
    }
}
