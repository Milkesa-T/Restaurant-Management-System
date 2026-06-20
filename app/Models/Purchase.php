<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $fillable = ['branch_id', 'supplier_id', 'invoice_number', 'total_amount', 'purchased_at', 'approved_by_user_id', 'status'];

    protected $casts = [
        'purchased_at' => 'datetime',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by_user_id');
    }

    public function purchaseItems()
    {
        return $this->hasMany(PurchaseItem::class);
    }
}
