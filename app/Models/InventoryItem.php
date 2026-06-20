<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryItem extends Model
{
    protected $table = 'inventory_items';

    protected $fillable = ['branch_id', 'name', 'category', 'unit', 'current_stock', 'min_stock', 'reorder_level', 'average_cost', 'status'];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function recipes()
    {
        return $this->hasMany(Recipe::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function purchaseItems()
    {
        return $this->hasMany(PurchaseItem::class);
    }
}
