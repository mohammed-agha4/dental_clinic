<?php

namespace App\Models;

use App\Models\User;
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


    // public function checkAndNotifyReorderLevel()
    // {
    //     // Check if the current quantity is less than or equal to the reorder level
    //     if ($this->quantity <= $this->reorder_level) {
    //         try {
    //             // Send notification to users with inventory management role
    //             $users = User::whereHas('roles', function($query) {
    //                 $query->where('name', 'inventory_manager');
    //             })->orWhere('is_admin', true)->get();

    //             foreach ($users as $user) {
    //                 $user->notify(new ReorderNotification($this));
    //             }

    //             // Log the low inventory notification
    //             Log::info("Low inventory alert for {$this->name}. Current quantity: {$this->quantity}, Reorder level: {$this->reorder_level}");
    //         } catch (\Exception $e) {
    //             Log::error("Failed to send reorder level notification: " . $e->getMessage());
    //         }
    //     }

    //     return $this;
    // }


    // protected static function booted() {
    //     static::created(function(InventoryTransaction $trans) {
    //         $trans->inventory_id == $this->id;
    //     });
    // }

}
