<?php

namespace App\Models;

use App\Concerns\HasRoles;
use App\Models\User;
use App\Models\Visit;
use App\Models\Service;
use App\Models\Appointment;
use App\Models\InventoryTransaction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Staff extends Model
{
    use SoftDeletes;

    protected $table = 'staff';
    protected $guarded = [];
    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'date',
    ];

    public function scopeNonAdmin($query)
    {
        return $query->where('role', '!=', 'admin');
    }

    public function transactions()
    {
        return $this->hasMany(InventoryTransaction::class);
    }

    public function visits()
    {
        return $this->hasMany(Visit::class, 'staff_id');
    }

    public function services()
    {
        return $this->belongsToMany(Service::class, 'service_staff', 'staff_id', 'service_id');
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'staff_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withDefault();
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'staff_id');
    }
    
    public function expenses()
    {
        return $this->hasMany(Expense::class, 'staff_id');
    }
}
