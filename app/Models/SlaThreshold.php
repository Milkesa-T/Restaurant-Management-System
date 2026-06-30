<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SlaThreshold extends Model
{
    protected $table = 'sla_thresholds';

    protected $fillable = [
        'branch_id', 'from_status', 'to_status', 'target_seconds', 'warning_seconds', 'description'
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
