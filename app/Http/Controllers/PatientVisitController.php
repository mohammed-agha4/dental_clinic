<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Appointment;
use App\Models\Visit;
use App\Services\PatientVisitService;
use Illuminate\Http\Request;

class PatientVisitController extends Controller
{
    protected $visitService;

    public function __construct(PatientVisitService $visitService)
    {
        $this->visitService = $visitService;
    }

    /**
     * Show check-in form
     */
    public function showCheckInForm()
    {
        $patients = Patient::all(); // For patient dropdown
        $todayAppointments = Appointment::whereDate('appointment_date', today())
            ->with(['patient', 'dentist', 'service'])
            ->get();

        return view('dashboard.visits.check-in', [
            'patients' => $patients,
            'appointments' => $todayAppointments
        ]);
    }

    /**
     * Handle patient arrival
     */
    public function checkIn(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'appointment_id' => 'nullable|exists:appointments,id',
            'emergency_symptoms' => 'nullable|string',
        ]);

        $patient = Patient::findOrFail($request->patient_id);
        $hasAppointment = $request->has('appointment_id');
        $appointment = $hasAppointment ? Appointment::find($request->appointment_id) : null;

        $result = $this->visitService->handlePatientArrival($patient, $hasAppointment, $appointment);

        // Flash appropriate message based on result
        if ($result['status'] === 'emergency') {
            return redirect()->route('dashboard.visits.check-in')
                ->with('success', 'Emergency case registered. Patient will be seen ASAP.');
        } elseif ($result['status'] === 'scheduled') {
            return redirect()->route('dashboard.visits.check-in')
                ->with('success', "Patient checked in. Estimated wait time: {$result['wait_time']} minutes");
        } else {
            return redirect()->route('dashboard.visits.check-in')
                ->with('warning', "No immediate slots available. Next available: {$result['next_available']}");
        }
    }

    /**
     * Handle emergency walk-in
     */
    public function emergencyWalkIn(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'emergency_symptoms' => 'required|string',
        ]);

        $patient = Patient::findOrFail($request->patient_id);
        $result = $this->visitService->handleWalkInPatient($patient);

        return redirect()->route('dashboard.visits.check-in')
            ->with('success', 'Emergency walk-in registered successfully.');
    }
}
