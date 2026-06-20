<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $table = 'audit_logs';

    protected $fillable = ['user_id', 'action', 'module', 'record_id', 'old_value', 'new_value', 'ip_address', 'created_at'];

    public $timestamps = false; // We track created_at custom timestamp

    protected $casts = [
        'old_value' => 'array',
        'new_value' => 'array',
        'created_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
