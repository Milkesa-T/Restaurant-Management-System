<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = ['branch_id', 'name', 'slug', 'description'];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'permission_role');
    }
}
