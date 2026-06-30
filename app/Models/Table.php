<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    protected $fillable = ['area_id', 'table_number', 'qr_code', 'qr_url', 'capacity', 'status'];

    public function diningArea()
    {
        return $this->belongsTo(DiningArea::class, 'area_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function diningSessions()
    {
        return $this->hasMany(DiningSession::class);
    }
}
