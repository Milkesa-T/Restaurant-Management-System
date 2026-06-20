<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
    protected $table = 'purchase_items';

    protected $fillable = ['purchase_id', 'inventory_item_id', 'quantity', 'unit_cost', 'expiry_date', 'batch_number'];

    protected $casts = [
        'expiry_date' => 'date',
    ];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class);
    }
}
