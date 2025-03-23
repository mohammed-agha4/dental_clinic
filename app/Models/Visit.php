<?php

namespace App\Models;

use App\Models\Staff;
use App\Models\Patient;
use App\Models\Service;
use App\Models\Inventory;
use App\Models\Appointment;
use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    protected $guarded = [];


    public function appointment()
    {
        return $this->belongsTo(Appointment::class)->withDefault();
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class)->withDefault();
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class)->withDefault();
    }

    public function service()
    {
        return $this->belongsTo(Service::class)->withDefault();
    }

    public function inventoryItems()
    {
        return $this->belongsToMany(Inventory::class, 'inventory_visits') // Match migration name
                    ->withPivot('quantity_used', 'notes')
                    ->withTimestamps();
    }


}
