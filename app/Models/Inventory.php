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


    public function category()
    {
        return $this->belongsTo(Category::class)->withDefault();
    }

    /**
     * Get the supplier associated with the inventory.
     */
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



}
