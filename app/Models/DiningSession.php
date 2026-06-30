<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiningSession extends Model
{
    protected $fillable = [
        'branch_id',
        'table_id',
        'status',
        'opened_at',
        'closed_at',
    ];

    protected $casts = [
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
