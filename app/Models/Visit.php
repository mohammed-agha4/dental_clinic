<?php

namespace App\Models;

use App\Models\Staff;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\Service;
use App\Models\Inventory;
use App\Models\Appointment;
use App\Models\InventoryVisit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Visit extends Model
{
    use SoftDeletes;
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


    public function payments()
    {
        return $this->hasMany(Payment::class);
    }




}
