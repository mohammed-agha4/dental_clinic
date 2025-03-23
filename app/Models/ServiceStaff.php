<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceStaff extends Model
{
    protected $table = 'service_staff';

    protected $fillable = ['staff_id', 'service_id'];


    // Relationship to Staff
    public function dentist()
    {
        return $this->belongsTo(Staff::class, 'staff_id')->withDefault();
    }

    // Relationship to Service
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id')->withDefault();
    }
}
