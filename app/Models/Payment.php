<?php

namespace App\Models;

use App\Models\Staff;
use App\Models\Visit;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $guarded = [];

    public function visit()
    {
        return $this->belongsTo(Visit::class);
    }

    /**
     * Get the staff member associated with the payment.
     */
    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }
}
