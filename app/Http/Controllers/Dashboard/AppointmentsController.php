<?php

namespace App\Http\Controllers\Dashboard;

use Carbon\Carbon;
use App\Models\Staff;
use App\Models\Patient;
use App\Models\Service;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AppointmentsController extends Controller
{
    /**
     * Display a listing of all appointments.
     */
    public function index()
    {
        Gate::authorize('appointments.view');

        $query = Appointment::with(['patient', 'dentist.user', 'service'])
            ->orderBy('appointment_date', 'desc');

        if (
            auth()->user()->hasAbility('view-own-appointments') &&
            !auth()->user()->hasAbility('view-all-appointments')
        ) {
            $query->where('staff_id', auth()->user()->staff->id);
        }

        $appointments = $query->paginate(10);
        return view('dashboard.appointments.index', compact('appointments'));
    }

    /**
     * Display the form for creating a new appointment.
     */
    public function create()
    {
        Gate::authorize('appointments.create');
        $services = Service::where('is_active', true)->orderBy('service_name')->get();
        $patients = new Patient();

        return view('dashboard.appointments.create', compact('services', 'patients'));
    }

    /**
     * Store a newly created appointment in storage.
     */
    public function store(Request $request)
    {
        Gate::authorize('appointments.create');
        $isWalkIn = $request->has('appointment_type') && $request->appointment_type === 'walk_in';

        $validator = $this->validateAppointmentRequest($request, $isWalkIn);

        if ($validator->fails()) {
            // Log validation errors for debugging
            Log::error('Appointment validation failed', [
                'errors' => $validator->errors()->all(),
                'input' => $request->all()
            ]);

            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Please fix the errors below.');
        }

        try {
            DB::beginTransaction();

            // Handle existing patient
            if ($request->filled('existing_patient_id')) {
                $patient = Patient::findOrFail($request->existing_patient_id);

                if (!$patient) {
                    throw new \Exception('Patient not found');
                }
            } else {
                $patient = Patient::findOrCreateFromRequest($request, $isWalkIn);
            }

            // Create appointment
            $appointment = Appointment::createAppointment([
                'patient_id' => $patient->id,
                'staff_id' => $request->staff_id,
                'service_id' => $request->service_id,
                'appointment_date' => $isWalkIn ? now() : $request->appointment_date_time,
                'notes' => $request->notes,
                'cancellation_reason' => $request->cancellation_reason,
            ], $isWalkIn);

            DB::commit();

            return redirect()->route('dashboard.appointments.index')
                ->with('success', $isWalkIn ? 'Walk-in appointment created successfully.' : 'Appointment scheduled successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Appointment creation failed: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error creating appointment: ' . $e->getMessage());
        }
    }

    /**
     * Get available time slots for a service on a specific date
     */
    public function getAvailableSlots(Request $request)
    {
        try {
            $slots = Appointment::getAvailableSlots(
                $request->service_id,
                $request->date,
                $request->duration,
                $request->current_appointment_id ?? null
            );

            return response()->json(['success' => true, 'slots' => $slots]);
        } catch (\Exception $e) {
            Log::error('Error in getAvailableSlots: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error generating time slots: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get available dentists for a specific service and time
     */
    public function getAvailableDentists(Request $request)
    {
        try {
            $dentists = Appointment::getAvailableDentists(
                $request->input('service_id'),
                $request->input('appointment_date'),
                $request->input('current_appointment_id'),
                $request->input('is_walk_in', false)
            );

            return response()->json(['success' => true, 'dentists' => $dentists]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching available dentists: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Display the specified appointment
     */
    public function show($id)
    {
        Gate::authorize('appointments.show');
        $appointment = Appointment::with(['patient', 'dentist.user', 'service'])
            ->findOrFail($id);

        return view('dashboard.appointments.show', compact('appointment'));
    }

    /**
     * Show the form for editing the specified appointment
     */
    public function edit($id)
    {
        Gate::authorize('appointments.update');
        $appointment = Appointment::findOrFail($id);
        $patient = Patient::findOrFail($appointment->patient_id);
        $services = Service::where('is_active', true)->get();

        return view('dashboard.appointments.edit', [
            'appointment' => $appointment,
            'patient' => $patient,
            'services' => $services
        ]);
    }

    /**
     * Update the specified appointment in storage
     */
    public function update(Request $request, Appointment $appointment)
    {
        Gate::authorize('appointments.update');
        $validator = Validator::make($request->all(), [
            'appointment_id' => 'required|exists:appointments,id',
            'service_id' => 'required|exists:services,id',
            'staff_id' => 'required|exists:staff,id',
            'appointment_date_time' => 'required_if:status,scheduled,rescheduled',
            'status' => 'required|in:scheduled,rescheduled,completed,canceled,walk_in',
            'fname' => 'required|string|max:255',
            'lname' => 'required|string|max:255',
            'DOB' => 'required|date',
            'gender' => 'required|in:male,female',
            'phone' => 'required|string',
            'email' => 'required|email',
            'cancellation_reason' => 'required_if:status,canceled',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Please fix the errors below.');
        }

        try {
            DB::beginTransaction();

            $patient = Patient::findOrFail($appointment->patient_id);
            $this->validatePatientUpdate($request, $patient);
            $patient->updateFromRequest($request);

            $appointment->updateAppointment([
                'staff_id' => $request->staff_id,
                'service_id' => $request->service_id,
                'appointment_date' => $request->appointment_date_time,
                'status' => $request->status,
                'notes' => $request->notes,
                'cancellation_reason' => $request->cancellation_reason,
            ]);

            DB::commit();
            return redirect()->route('dashboard.appointments.index')
                ->with('success', 'Appointment updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error updating appointment: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified appointment from storage
     */
    public function destroy($id)
    {
        Gate::authorize('appointments.delete');
        try {
            $appointment = Appointment::with('patient')->findOrFail($id);

            if (!$appointment->canBeDeleted()) {
                return redirect()->back()
                    ->with('error', 'Cannot delete this appointment because it has associated visits.');
            }

            $patient = $appointment->patient;
            $patientName = $patient->fname . ' ' . $patient->lname;

            $appointment->delete();
            $patient->delete();

            return redirect()->route('dashboard.appointments.index')
                ->with('success', "Appointment and patient ($patientName) deleted successfully.");
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error deleting appointment: ' . $e->getMessage());
        }
    }

    /**
     * Display trashed appointments
     */
    public function trash()
    {
        Gate::authorize('appointments.trash');
        $appointments = Appointment::onlyTrashed()
            ->with([
                'patient' => function ($query) {
                    $query->withTrashed();
                },
                'dentist.user',
                'service'
            ])
            ->latest()
            ->paginate(10);

        return view('dashboard.appointments.trash', compact('appointments'));
    }

    /**
     * Restore a trashed appointment
     */
    public function restore(Request $request, $id)
    {
        Gate::authorize('appointments.restore');
        $appointment = Appointment::onlyTrashed()->findOrFail($id);
        $appointment->restore();
        return redirect()->route('dashboard.appointments.trash')->with('success', 'Appointment Restored.');
    }

    /**
     * Permanently delete an appointment
     */
    public function forceDelete($id)
    {
        Gate::authorize('appointments.force_delete');
        try {
            $appointment = Appointment::withTrashed()->with('patient')->findOrFail($id);
            $patient = $appointment->patient()->withTrashed()->first();
            $patientName = $patient->fname . ' ' . $patient->lname;

            $appointment->forceDelete();
            $patient->forceDelete();

            return redirect()->route('dashboard.appointments.trash')
                ->with('success', "Appointment and patient ($patientName) permanently deleted.");
        } catch (\Exception $e) {
            return redirect()->route('dashboard.appointments.trash')
                ->with('error', 'Error permanently deleting: ' . $e->getMessage());
        }
    }

    /**
     * Validate appointment request data
     */
    protected function validateAppointmentRequest(Request $request, bool $isWalkIn): \Illuminate\Validation\Validator
{
    $rules = [
        'service_id' => 'required|exists:services,id',
        'staff_id' => 'required|exists:staff,id',
        'notes' => 'nullable|string',
        'cancellation_reason' => 'nullable|string',
    ];

    if ($isWalkIn) {
        // Walk-in specific validation
        $rules['fname'] = 'required|string|max:255';
        $rules['lname'] = 'required|string|max:255';
        $rules['phone'] = 'required|string|max:20';
        $rules['email'] = 'nullable|email|max:255';
        $rules['gender'] = 'required|in:male,female';
        $rules['cheif_complaint'] = 'required|string';
    } else {
        // Regular appointment validation
        if ($request->filled('existing_patient_id')) {
            $rules['existing_patient_id'] = 'required|exists:patients,id';
        } else {
            $rules['fname'] = 'required|string|max:255';
            $rules['lname'] = 'required|string|max:255';
            $rules['DOB'] = 'required|date';
            $rules['gender'] = 'required|in:male,female';
            $rules['phone'] = 'required|string|max:20';
            $rules['email'] = 'nullable|email|max:255';
            $rules['medical_history'] = 'nullable|string';
            $rules['allergies'] = 'nullable|string';
        }

        $rules['appointment_date_time'] = 'required|date|after:now';
    }

    return Validator::make($request->all(), $rules);
}

    /**
     * Validate patient update data
     */
    protected function validatePatientUpdate(Request $request, Patient $patient): void
    {
        if ($patient->email != $request->email) {
            $emailExists = Patient::where('email', $request->email)
                ->where('id', '!=', $patient->id)
                ->exists();

            if ($emailExists) {
                throw new \Exception('Email already exists for another patient.');
            }
        }

        if ($patient->phone != $request->phone) {
            $phoneExists = Patient::where('phone', $request->phone)
                ->where('id', '!=', $patient->id)
                ->exists();

            if ($phoneExists) {
                throw new \Exception('Phone number already exists for another patient.');
            }
        }
    }
}
