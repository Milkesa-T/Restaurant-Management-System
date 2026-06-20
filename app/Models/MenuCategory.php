<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuCategory extends Model
{
    protected $fillable = ['branch_id', 'name', 'sort_order', 'status'];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function menuItems()
    {
        return $this->hasMany(MenuItem::class, 'category_id')->orderBy('name');
    }
}
