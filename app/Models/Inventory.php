<?php

namespace App\Models;

use App\Models\Visit;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\InventoryTransaction;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
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




    // protected static function booted() {
    //     static::created(function(InventoryTransaction $trans) {
    //         $trans->inventory_id == $this->id;
    //     });
    // }

}
