<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemAddon extends Model
{
    protected $fillable = ['branch_id', 'name', 'price'];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function menuItems()
    {
        return $this->belongsToMany(MenuItem::class, 'menu_item_addon', 'item_addon_id', 'menu_item_id');
    }
}
