<?php

namespace App\Models;

use App\Models\Staff;
use App\Models\Visit;
use App\Models\Appointment;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $guarded = [];

    public function visits()
    {
        return $this->hasMany(Visit::class);
    }

    public function staff()
    {
        return $this->belongsToMany(Staff::class, 'service_staff', 'service_id', 'staff_id');

    }
    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    protected $casts = [
        'created_at' => 'date',
    ];

}

