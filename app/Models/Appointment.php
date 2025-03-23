<?php

namespace App\Models;

use App\Models\Staff;
use App\Models\Visit;
use App\Models\Patient;
use App\Models\Service;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Appointment extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    public function patient()
    {
        return $this->belongsTo(Patient::class)->withDefault();
    }

    public function service()
    {
        return $this->belongsTo(Service::class)->withDefault();
    }

    public function dentist()
    {
        return $this->belongsTo(Staff::class, 'staff_id')->withDefault();
    }

    public function visits()
    {
        return $this->hasMany(Visit::class);
    }
}
