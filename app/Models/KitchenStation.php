<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KitchenStation extends Model
{
    protected $fillable = ['branch_id', 'name', 'status'];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function menuItems()
    {
        return $this->hasMany(MenuItem::class, 'kitchen_station_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_kitchen_stations', 'station_id', 'user_id')
                    ->withPivot('is_primary', 'assigned_at')
                    ->withTimestamps();
    }
}
