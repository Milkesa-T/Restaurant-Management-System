<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['branch_id', 'key', 'value'];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
