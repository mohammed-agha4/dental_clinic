<?php

namespace App\Services;

use App\Models\{Appointment, Patient, Staff, Visit, Service};
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PatientVisitService
{
    /**
     * Handle patient arrival at the clinic
     */
    public function handlePatientArrival(Patient $patient, bool $hasAppointment, ?Appointment $appointment = null)
    {
        if ($hasAppointment) {
            return $this->handleScheduledPatient($patient, $appointment);
        }

        return $this->handleWalkInPatient($patient);
    }

    /**
     * Handle walk-in patient
     */
    private function handleWalkInPatient(Patient $patient)
    {
        return DB::transaction(function () use ($patient) {
            // Check for available slots in the current timeframe
            $availableSlot = $this->findNextAvailableSlot();

            if ($availableSlot) {
                // Create an immediate appointment
                $appointment = Appointment::create([
                    'patient_id' => $patient->id,
                    'staff_id' => $availableSlot['staff_id'],
                    'service_id' => null, // Will be set after assessment
                    'appointment_date' => $availableSlot['start_time'],
                    'duration' => 30, // Default emergency slot duration
                    'status' => 'scheduled',
                    'notes' => 'Walk-in patient'
                ]);

                return [
                    'status' => 'scheduled',
                    'appointment' => $appointment,
                    'wait_time' => $this->calculateWaitTime($availableSlot['start_time'])
                ];
            }

            // Handle emergency cases when no slots available
            if ($this->isEmergencyCase()) {
                return $this->handleEmergencyCase($patient);
            }

            return [
                'status' => 'no_slots',
                'next_available' => $this->getNextAvailableAppointment()
            ];
        });
    }

    /**
     * Handle emergency cases
     */
    private function handleEmergencyCase(Patient $patient)
    {
        // Find the most suitable dentist for emergency
        $availableDentist = Staff::where('role', 'dentist')
            ->where('is_active', true)
            ->first();

        // Create emergency visit record
        $visit = Visit::create([
            'patient_id' => $patient->id,
            'staff_id' => $availableDentist->id,
            'visit_date' => now(),
            'cheif_complaint' => 'Emergency case',
            'appointment_id' => null
        ]);

        return [
            'status' => 'emergency',
            'visit' => $visit,
            'message' => 'Patient will be seen as soon as possible'
        ];
    }

    /**
     * Find next available slot considering current appointments
     */
    private function findNextAvailableSlot()
    {
        $now = Carbon::now();
        $endOfDay = Carbon::now()->endOfDay();

        // Get all appointments for today
        $appointments = Appointment::whereBetween('appointment_date', [$now, $endOfDay])
            ->orderBy('appointment_date')
            ->get();

        // Find gaps between appointments
        $availableSlots = [];
        $lastEndTime = $now;

        foreach ($appointments as $appointment) {
            $startTime = Carbon::parse($appointment->appointment_date);
            $duration = $appointment->duration;

            // If there's a gap of at least 30 minutes
            if ($lastEndTime->diffInMinutes($startTime) >= 30) {
                $availableSlots[] = [
                    'start_time' => $lastEndTime,
                    'staff_id' => $this->findAvailableStaff($lastEndTime)
                ];
            }

            $lastEndTime = $startTime->addMinutes($duration);
        }

        return !empty($availableSlots) ? $availableSlots[0] : null;
    }

    /**
     * Find available staff member for the given time slot
     */
    private function findAvailableStaff($timeSlot)
    {
        return Staff::where('role', 'dentist')
            ->where('is_active', true)
            ->whereDoesntHave('appointments', function ($query) use ($timeSlot) {
                $query->where('appointment_date', $timeSlot)
                    ->where('status', '!=', 'canceled');
            })
            ->first()
            ->id;
    }

    /**
     * Calculate estimated wait time
     */
    private function calculateWaitTime($slotTime)
    {
        return now()->diffInMinutes($slotTime);
    }

    /**
     * Emergency assessment based on symptoms/conditions
     */
    private function isEmergencyCase()
    {
        // This would be implemented based on your emergency criteria
        // Example: severe pain, bleeding, trauma, etc.
        return request()->has('emergency_symptoms');
    }
}
