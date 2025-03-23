<?php

namespace App\Models;

use App\Models\Visit;
use App\Models\Inventory;
use Illuminate\Database\Eloquent\Model;

class InventoryVisit extends Model
{

    protected $table = 'inventory_visits';

    protected $fillable = [
        'visit_id',
        'inventory_id',
        'quantity_used',
        'notes'
    ];

    public function visit()
    {
        return $this->belongsTo(Visit::class);
    }

    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }
}
