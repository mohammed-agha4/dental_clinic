<?php

namespace App\Models;

use App\Models\User;
use App\Models\Visit;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\InventoryTransaction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inventory extends Model
{
    use SoftDeletes;
    protected $guarded = [];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'expiry_date' => 'date',
        'is_active' => 'boolean',
    ];


    public function category()
    {
        return $this->belongsTo(Category::class)->withDefault();
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class)->withDefault();
    }

    public function transactions()
    {
        return $this->hasMany(InventoryTransaction::class);
    }

    public function visits()
    {
        return $this->belongsToMany(Visit::class, 'inventory_visits')
            ->withPivot('quantity_used', 'notes')
            ->withTimestamps();
    }

    public function getIsExpiredAttribute()
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }
}
