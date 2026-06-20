<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = ['branch_id', 'name', 'phone', 'email', 'address', 'status'];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }
}
