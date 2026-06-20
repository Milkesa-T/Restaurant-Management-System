<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiningArea extends Model
{
    protected $fillable = ['branch_id', 'name', 'floor', 'status'];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function tables()
    {
        return $this->hasMany(Table::class, 'area_id');
    }
}
