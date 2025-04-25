<?php

namespace App\Models;

use App\Models\Staff;
use App\Models\Inventory;
use App\Models\InventoryVisit;
use Illuminate\Database\Eloquent\Model;

class InventoryTransaction extends Model
{
    protected $guarded = [];



    public function inventory()
    {
        return $this->belongsTo(Inventory::class)->withDefault();
    }

    /**
     * Get the staff member associated with the transaction.
     */
    public function staff()
    {
        return $this->belongsTo(Staff::class)->withDefault();
    }

    protected $casts = [
        'transaction_date' => 'datetime',  // This ensures it's always a Carbon instance

    ];

}
