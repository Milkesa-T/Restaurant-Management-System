<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    protected $fillable = ['menu_item_id', 'item_variant_id', 'inventory_item_id', 'quantity_required', 'unit'];

    public function menuItem()
    {
        return $this->belongsTo(MenuItem::class);
    }

    public function itemVariant()
    {
        return $this->belongsTo(ItemVariant::class, 'item_variant_id');
    }

    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class);
    }
}
