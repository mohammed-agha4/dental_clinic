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

    // Scope to filter expenses by date range
    public function scopeDateRange($query, $startDate, $endDate)
    {
        if ($startDate && $endDate) {
            return $query->whereBetween('expense_date', [$startDate, $endDate]);
        }
        return $query;
    }

    // Scope to filter expenses by category
    public function scopeCategory($query, $category)
    {
        if ($category) {
            return $query->where('category', $category);
        }
        return $query;
    }

    // Scope to filter expenses by staff
    public function scopeByStaff($query, $staffId)
    {
        if ($staffId) {
            return $query->where('staff_id', $staffId);
        }
        return $query;
    }
}
