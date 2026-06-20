<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemVariant extends Model
{
    protected $fillable = ['menu_item_id', 'name', 'price_delta'];

    public function menuItem()
    {
        return $this->belongsTo(MenuItem::class, 'menu_item_id');
    }

    public function recipes()
    {
        return $this->hasMany(Recipe::class, 'item_variant_id');
    }
}
