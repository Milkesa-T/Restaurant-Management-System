<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    protected $fillable = ['category_id', 'name', 'description', 'price', 'image_path', 'estimated_prep_time', 'availability_status'];

    public function category()
    {
        return $this->belongsTo(MenuCategory::class, 'category_id');
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
}
