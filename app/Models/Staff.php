<?php

namespace App\Models;

use App\Models\User;
use App\Models\Visit;
use App\Models\Service;
use App\Models\Appointment;
use App\Models\InventoryTransaction;
use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{

    protected $table = 'staff';


    protected $guarded = [];



    public function transactions()
    {
        return $this->hasMany(InventoryTransaction::class);
    }

    public function visits()
    {
        return $this->hasMany(Visit::class);
    }

    public function services()
    {
        return $this->belongsToMany(Service::class, 'service_staff', 'staff_id', 'service_id');
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withDefault();
    }




}
