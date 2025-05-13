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

    public function getFullNameAttribute()
    {
        return ucwords("{$this->fname} {$this->lname}");
    }

    protected $casts = [
        'DOB' => 'datetime',
    ];



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
        // First try to find by phone (most reliable unique identifier)
        $patient = self::where('phone', $request->phone)->first();

        // If not found by phone, try by email if provided
        if (!$patient && !empty($request->email)) {
            $patient = self::where('email', $request->email)->first();
        }

        // Determine if we're creating a new patient
        $isNewPatient = !$patient;

        // Initialize new patient if not found
        if ($isNewPatient) {
            $patient = new self();
        }

        // Base fields - always take from request if provided
        $patientData = [
            'fname' => $request->fname,
            'lname' => $request->lname,
            'phone' => $request->phone,
            'email' => $request->email,
        ];

        // Conditional fields - handle differently for new vs existing patients
        if ($isNewPatient) {
            // For new patients, use request data with fallback defaults
            $patientData = array_merge($patientData, [
                'DOB' => $request->DOB ?? now()->subYears(30)->format('Y-m-d'),
                'gender' => $request->gender,
                'medical_history' => $request->medical_history ?? '',
                'allergies' => $request->allergies ?? '',
                'Emergency_contact_name' => $request->Emergency_contact_name,
                'Emergency_contact_phone' => $request->Emergency_contact_phone,
            ]);
        } else {
            // For existing patients, only update if request has new values
            // $patientData = array_merge($patientData, [
            //     'DOB' => $request->DOB ?? $patient->DOB,
            //     'gender' => $request->gender ?? $patient->gender,
            //     'medical_history' => $request->medical_history ?? $patient->medical_history,
            //     'allergies' => $request->allergies ?? $patient->allergies,
            //     'Emergency_contact_name' => $request->Emergency_contact_name ?? $patient->Emergency_contact_name,
            //     'Emergency_contact_phone' => $request->Emergency_contact_phone ?? $patient->Emergency_contact_phone,
            // ]);
        }

        $patient->fill($patientData);
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
        return $query->whereHas('appointments', function ($q) use ($staffId) {
            $q->where('staff_id', $staffId);
        });
    }
}
