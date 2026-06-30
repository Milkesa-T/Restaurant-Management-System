<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class UserKitchenStation extends Pivot
{
    protected $table = 'user_kitchen_stations';

    protected $fillable = [
        'user_id', 
        'station_id', 
        'is_primary', 
        'assigned_at', 
        'approved_by_user_id', 
        'approved_at'
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'assigned_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by_user_id');
    }
}
