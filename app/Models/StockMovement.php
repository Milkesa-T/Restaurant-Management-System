<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    protected $table = 'stock_movements';

    protected $fillable = ['inventory_item_id', 'type', 'quantity_in', 'quantity_out', 'reference_type', 'reference_id', 'unit_cost', 'created_by_user_id', 'created_at'];

    public $timestamps = false; // We track created_at custom timestamp

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }
}
