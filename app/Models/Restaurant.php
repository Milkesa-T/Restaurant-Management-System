<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
{
    protected $fillable = ['name', 'logo_path', 'address', 'phone', 'currency', 'tax_settings', 'working_hours'];

    protected $casts = [
        'tax_settings' => 'array',
        'working_hours' => 'array',
    ];

    public function branches()
    {
        return $this->hasMany(Branch::class);
    }
}
