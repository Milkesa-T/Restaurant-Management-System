<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = [
        'branch_id', 'category_id', 'vendor', 'amount', 'payment_method',
        'expense_date', 'approval_status', 'approved_by_user_id',
        'receipt_attachment_path', 'note', 'is_recurring', 'created_by_user_id'
    ];

    protected $casts = [
        'expense_date' => 'date',
        'is_recurring' => 'boolean',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class, 'category_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by_user_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }
}
