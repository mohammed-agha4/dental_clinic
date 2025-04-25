<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Patient extends Model
{
    use SoftDeletes, Notifiable;

    protected $guarded = [];

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function visits()
    {
        return $this->hasMany(Visit::class);
    }

    public function getFullNameAttribute() {
        return ucwords("{$this->fname} {$this->lname}");
    }

    protected $casts = [
        'DOB' => 'datetime',
    ];


    // === Business Logic ===

    /**
     * Get initials (used for fallback avatars)
     */
    public function getInitialsAttribute()
    {
        return strtoupper(
            mb_substr($this->fname, 0, 1) . mb_substr($this->lname, 0, 1)
        );
    }

    /**
     * Create or update a patient from the request
     */
    public static function findOrCreateFromRequest($request, $isWalkIn = false)
    {
        $patient = self::where('phone', $request->phone)->first();

        if (!$patient) {
            $patient = new self();
        }

        $patient->fill([
            'fname' => $request->fname,
            'lname' => $request->lname,
            'phone' => $request->phone,
            'email' => $request->email ?? null,
            'DOB' => $request->DOB ?? now()->subYears(30)->format('Y-m-d'),
            'gender' => $request->gender ?? 'male',
            'medical_history' => $request->medical_history ?? '',
            'allergies' => $request->allergies ?? '',
            'Emergency_contact_name' => $request->Emergency_contact_name ?? null,
            'Emergency_contact_phone' => $request->Emergency_contact_phone ?? null,
        ]);

        $patient->save();

        return $patient;
    }

    /**
     * Update patient with request data
     */
    public function updateFromRequest($request)
    {
        $this->fill([
            'fname' => $request->fname,
            'lname' => $request->lname,
            'DOB' => $request->DOB,
            'gender' => $request->gender,
            'phone' => $request->phone,
            'email' => $request->email,
            'medical_history' => $request->medical_history,
            'allergies' => $request->allergies,
            'Emergency_contact_name' => $request->Emergency_contact_name,
            'Emergency_contact_phone' => $request->Emergency_contact_phone,
        ]);

        $this->save();
    }

    public function scopeForDentist($query, $staffId)
{
    return $query->whereHas('appointments', function($q) use ($staffId) {
        $q->where('staff_id', $staffId);
    });
}
}
