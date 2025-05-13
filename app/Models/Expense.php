<?php

namespace App\Models;

use App\Models\Staff;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{

    use SoftDeletes;

    protected $fillable = [
        'title',
        'staff_id',
        'description',
        'amount',
        'category',
        'expense_date',
    ];

    protected $casts = [
        'expense_date' => 'date',
        'amount' => 'decimal:2',
    ];

    // Categories as a constant for easy reference
    const CATEGORIES = [
        'utilities' => 'Utilities',
        'supplies' => 'Supplies',
        'equipment' => 'Equipment',
        'salary' => 'Salary',
        'maintenance' => 'Maintenance',
        'other' => 'Other'
    ];

    // Relationship with staff
    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }


}
