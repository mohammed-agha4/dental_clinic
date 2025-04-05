<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Service;
use App\Models\Staff;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AppointmentsController extends Controller
{

        /**
     * Display a listing of all appointments.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $appointments = Appointment::with(['patient', 'dentist.user', 'service'])
            ->orderBy('appointment_date', 'desc')
            ->paginate(10);

        return view('dashboard.appointments.index', compact('appointments'));
    }

    /**
     * Display the form for creating a new appointment.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Get all active services
        $services = Service::where('is_active', true)->orderBy('service_name')->get();

        // Create an empty patient object for the form
        $patients = new Patient();

        return view('dashboard.appointments.create', compact('services', 'patients'));
    }

    /**
     * Store a newly created appointment in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Check if this is a walk-in appointment
        $isWalkIn = $request->has('appointment_type') && $request->appointment_type === 'walk_in';

        // Define validation rules
        $rules = [
            'fname' => 'required|string|max:255',
            'lname' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'service_id' => 'required|exists:services,id',
            'staff_id' => 'required|exists:staff,id',
        ];

        // Add rules specific to regular appointments
        if (!$isWalkIn) {
            $rules['email'] = 'required|email|max:255';
            $rules['DOB'] = 'required|date';
            $rules['gender'] = 'required|in:male,female';
            $rules['appointment_date_time'] = 'required|date';
        } else {
            $rules['email'] = 'nullable|email|max:255';
            $rules['cheif_complaint'] = 'required|string';
        }

        // Validate request
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Please fix the errors below.');
        }

        try {
            DB::beginTransaction();

            // Find or create patient
            $patient = Patient::where('phone', $request->phone)->first();

            if (!$patient) {
                $patient = new Patient();
                $patient->fname = $request->fname;
                $patient->lname = $request->lname;
                $patient->phone = $request->phone;
                $patient->email = $request->email ?? null;

                if (!$isWalkIn) {
                    // Only required for regular appointments
                    $patient->DOB = $request->DOB;
                    $patient->gender = $request->gender;
                    $patient->medical_history = $request->medical_history ?? '';
                    $patient->allergies = $request->allergies ?? '';
                    $patient->Emergency_contact_name = $request->Emergency_contact_name;
                    $patient->Emergency_contact_phone = $request->Emergency_contact_phone;
                } else {
                    // Default values for walk-in if not provided
                    $patient->DOB = $request->DOB ?? now()->subYears(30)->format('Y-m-d'); // Default age
                    $patient->gender = $request->gender ?? 'male';
                    $patient->medical_history = '';
                    $patient->allergies = '';
                }

                $patient->save();
            } else {
                // Update existing patient information if this is a regular appointment
                if (!$isWalkIn) {
                    $patient->fname = $request->fname;
                    $patient->lname = $request->lname;
                    $patient->DOB = $request->DOB;
                    $patient->gender = $request->gender;
                    $patient->email = $request->email;
                    $patient->medical_history = $request->medical_history ?? $patient->medical_history;
                    $patient->allergies = $request->allergies ?? $patient->allergies;
                    $patient->Emergency_contact_name = $request->Emergency_contact_name ?? $patient->Emergency_contact_name;
                    $patient->Emergency_contact_phone = $request->Emergency_contact_phone ?? $patient->Emergency_contact_phone;
                    $patient->save();
                }
            }

            // Create appointment
            $appointment = new Appointment();
            $appointment->patient_id = $patient->id;
            $appointment->staff_id = $request->staff_id;
            $appointment->service_id = $request->service_id;

            // Get service duration
            $service = Service::findOrFail($request->service_id);
            $appointment->duration = $service->duration;

            if ($isWalkIn) {
                // Walk-in appointment is for now
                $appointment->appointment_date = now();
                $appointment->status = 'walk_in';
            } else {
                // Regular appointment uses the selected date and time
                $appointment->appointment_date = $request->appointment_date_time;
                $appointment->status = 'scheduled';
            }

            $appointment->notes = $request->notes;
            $appointment->cancellation_reason = $request->cancellation_reason;
            $appointment->reminder_sent = false;
            $appointment->save();

            // For walk-in appointments, create a visit record immediately
            if ($isWalkIn) {
                $visit = \App\Models\Visit::create([
                    'appointment_id' => $appointment->id,
                    'patient_id' => $patient->id,
                    'staff_id' => $request->staff_id,
                    'service_id' => $request->service_id,
                    'visit_date' => now(),
                    'cheif_complaint' => $request->cheif_complaint,
                ]);
            }

            DB::commit();

            return redirect()->route('dashboard.appointments.index')
                ->with('success', $isWalkIn ? 'Walk-in appointment created successfully.' : 'Appointment scheduled successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error creating appointment: ' . $e->getMessage());
        }
    }

    /**
     * Get available time slots for a specific date and service.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    // public function getAvailableSlots(Request $request)
    // {
    //     $serviceId = $request->service_id;
    //     $date = $request->date;
    //     $duration = $request->duration;

    //     // Validate inputs
    //     if (!$serviceId || !$date || !$duration) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Missing required parameters'
    //         ]);
    //     }

    //     try {
    //         // Parse the date
    //         $dateObj = Carbon::parse($date);

    //         // Get clinic opening hours
    //         $openingTime = Carbon::parse($date . ' 09:00:00');
    //         $closingTime = Carbon::parse($date . ' 17:00:00');

    //         // Don't allow booking in the past
    //         $now = Carbon::now();
    //         if ($dateObj->isToday() && $now->greaterThan($openingTime)) {
    //             $openingTime = $now->copy()->addHours(1)->startOfHour();
    //         }

    //         // Get all staff that can perform this service
    //         $staffIds = Staff::where('is_active', true)
    //             ->whereHas('services', function($query) use ($serviceId) {
    //                 $query->where('service_id', $serviceId);
    //             })
    //             ->pluck('id');

    //         if ($staffIds->isEmpty()) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'No staff available for this service'
    //             ]);
    //         }

    //         // Get all appointments for this date
    //         $existingAppointments = Appointment::whereDate('appointment_date', $dateObj)
    //             ->whereIn('staff_id', $staffIds)
    //             ->where('status', '!=', 'cancelled')
    //             ->with(['service', 'dentist'])
    //             ->get();

    //         // For debugging, let's see what appointments we're dealing with
    //         \Log::info('Date: ' . $date);
    //         \Log::info('Existing appointments for this date: ' . $existingAppointments->count());

    //         foreach ($existingAppointments as $index => $appointment) {
    //             \Log::info('Appointment #' . ($index + 1) . ': ' .
    //                        'Start: ' . $appointment->appointment_date . ', ' .
    //                        'Duration: ' . $appointment->service->duration . ', ' .
    //                        'Staff: ' . $appointment->staff_id);
    //         }

    //         // Generate time slots
    //         $slots = [];
    //         $currentTime = $openingTime->copy();
    //         $durationInMinutes = (int)$duration;

    //         while ($currentTime->lessThan($closingTime)) {
    //             $slotEndTime = $currentTime->copy()->addMinutes($durationInMinutes);

    //             if ($slotEndTime->greaterThan($closingTime)) {
    //                 break;
    //             }

    //             $timeString = $currentTime->format('H:i');
    //             $available = true;

    //             // Completely rewritten overlap detection
    //             foreach ($existingAppointments as $appointment) {
    //                 // Get appointment start and end times
    //                 $appointmentStart = Carbon::parse($appointment->appointment_date);
    //                 $appointmentEnd = Carbon::parse($appointment->appointment_date)
    //                     ->addMinutes($appointment->service->duration);

    //                 // Debug each slot check
    //                 \Log::info('Checking slot: ' . $currentTime->format('H:i') . ' - ' . $slotEndTime->format('H:i') .
    //                            ' against appointment: ' . $appointmentStart->format('H:i') . ' - ' . $appointmentEnd->format('H:i'));

    //                 // Simple overlap check: if appointment's start is before slot's end AND
    //                 // appointment's end is after slot's start, there's an overlap
    //                 if ($appointmentStart->lt($slotEndTime) && $appointmentEnd->gt($currentTime)) {
    //                     $available = false;
    //                     \Log::info('OVERLAP DETECTED: Slot ' . $timeString . ' is unavailable');
    //                     break;
    //                 }
    //             }

    //             $slots[] = [
    //                 'time' => $timeString,
    //                 'available' => $available
    //             ];

    //             $currentTime->addMinutes($durationInMinutes);
    //         }

    //         return response()->json([
    //             'success' => true,
    //             'slots' => $slots
    //         ]);

    //     } catch (\Exception $e) {
    //         \Log::error('Error in getAvailableSlots: ' . $e->getMessage());
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Error generating time slots: ' . $e->getMessage()
    //         ]);
    //     }
    // }

    public function getAvailableSlots(Request $request)
    {
        $serviceId = $request->service_id;
        $date = $request->date;
        $duration = $request->duration;
        $currentAppointmentId = $request->current_appointment_id ?? null;

        // Validate inputs
        if (!$serviceId || !$date || !$duration) {
            return response()->json([
                'success' => false,
                'message' => 'Missing required parameters'
            ]);
        }

        try {
            // Parse the date
            $dateObj = Carbon::parse($date);

            // Get clinic opening hours (customize based on your needs)
            $openingTime = Carbon::parse($date . ' 09:00:00');
            $closingTime = Carbon::parse($date . ' 17:00:00');

            // Don't allow booking in the past
            $now = Carbon::now();
            if ($dateObj->isToday() && $now->greaterThan($openingTime)) {
                $openingTime = $now->copy()->addHours(1)->startOfHour();
            }

            \Log::info('Parsed date: ' . $dateObj->toDateTimeString());
            \Log::info('Opening time: ' . $openingTime->toDateTimeString());
            \Log::info('Closing time: ' . $closingTime->toDateTimeString());

            // Get all staff that can perform this service
            $staffIds = Staff::where('is_active', true)
                ->whereHas('services', function($query) use ($serviceId) {
                    $query->where('service_id', $serviceId);
                })
                ->pluck('id');

            if ($staffIds->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No staff available for this service'
                ]);
            }

            // Get all appointments for this date, excluding the current appointment if editing
            $appointmentsQuery = Appointment::whereDate('appointment_date', $dateObj)
                ->whereIn('staff_id', $staffIds)
                ->where('status', '!=', 'canceled');

            // Exclude current appointment if we're editing
            if ($currentAppointmentId) {
                $appointmentsQuery->where('id', '!=', $currentAppointmentId);
            }

            $existingAppointments = $appointmentsQuery->with('service')->get();

            // Generate time slots
            $slots = [];
            $currentTime = $openingTime->copy();
            $durationInMinutes = (int)$duration;

            while ($currentTime->lessThan($closingTime)) {
                $slotEndTime = $currentTime->copy()->addMinutes($durationInMinutes);

                if ($slotEndTime->greaterThan($closingTime)) {
                    break;
                }

                $timeString = $currentTime->format('H:i');

                // Check if this specific slot is available
                $available = true;
                foreach ($existingAppointments as $appointment) {
                    $appointmentStart = Carbon::parse($appointment->appointment_date);

                    // Get the service duration safely
                    $appointmentDuration = $appointment->service ? $appointment->service->duration : $durationInMinutes;
                    $appointmentEnd = Carbon::parse($appointment->appointment_date)->addMinutes($appointmentDuration);

                    // Simple overlap check
                    if ($appointmentStart->lt($slotEndTime) && $appointmentEnd->gt($currentTime)) {
                        $available = false;
                        break;
                    }
                }

                $slots[] = [
                    'time' => $timeString,
                    'available' => $available
                ];

                // Move to next slot
                $currentTime->addMinutes($durationInMinutes);
            }

            return response()->json([
                'success' => true,
                'slots' => $slots
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in getAvailableSlots: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error generating time slots: ' . $e->getMessage()
            ]);
        }
    }




    /**
     * Get available dentists for a specific service and time.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAvailableDentists(Request $request)
    {
        $serviceId = $request->input('service_id');
        $appointmentDate = $request->input('appointment_date');
        $isWalkIn = $request->input('is_walk_in', false);

        if (!$serviceId) {
            return response()->json([
                'success' => false,
                'message' => 'Service ID is required'
            ]);
        }

        try {
            // Find all dentists who can perform this service
            $dentists = Staff::with(['user'])
                ->where('role', 'dentist')
                ->where('is_active', true)
                ->whereHas('services', function ($query) use ($serviceId) {
                    $query->where('service_id', $serviceId);
                })
                ->get();

            if (!$isWalkIn && $appointmentDate) {
                // For regular appointments, filter out dentists who have conflicts
                $appointmentStart = Carbon::parse($appointmentDate);
                $service = Service::findOrFail($serviceId);
                $appointmentEnd = (clone $appointmentStart)->addMinutes($service->duration);

                // Filter out dentists with conflicting appointments
                $availableDentists = $dentists->filter(function ($dentist) use ($appointmentStart, $appointmentEnd) {
                    $conflicts = Appointment::where('staff_id', $dentist->id)
                        ->where('status', '!=', 'canceled')
                        ->where(function ($query) use ($appointmentStart, $appointmentEnd) {
                            $query->where(function ($q) use ($appointmentStart, $appointmentEnd) {
                                // Appointment starts during our slot
                                $q->where('appointment_date', '>=', $appointmentStart)
                                ->where('appointment_date', '<', $appointmentEnd);
                            })->orWhere(function ($q) use ($appointmentStart, $appointmentEnd) {
                                // Appointment ends during our slot
                                $q->whereRaw("DATE_ADD(appointment_date, INTERVAL duration MINUTE) > ?", [$appointmentStart])
                                ->whereRaw("appointment_date < ?", [$appointmentEnd]);
                            });
                        })
                        ->count();

                    return $conflicts === 0;
                });

                $dentists = $availableDentists->values();
            }

            return response()->json([
                'success' => true,
                'dentists' => $dentists
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching available dentists: ' . $e->getMessage()
            ]);
        }
    }



    /**
     * Display the specified appointment.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $appointment = Appointment::with(['patient', 'dentist.user', 'service'])
            ->findOrFail($id);

        return view('dashboard.appointments.show', compact('appointment'));
    }

    /**
     * Show the form for editing the specified appointment.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $appointment = Appointment::with(['patient', 'dentist.user', 'service'])
            ->findOrFail($id);

        $services = Service::where('is_active', true)->orderBy('service_name')->get();
        $patients = Patient::all();
        $staff = Staff::with('user')->get();
        // dd($patients);

        return view('dashboard.appointments.edit', compact('appointment','staff', 'services', 'patients'));
    }

    /**
     * Update the specified appointment in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Appointment $appointment)
    {
        // Similar validation as store method
        $validator = Validator::make($request->all(), [
            'patient_id' => 'required|exists:patients,id',
            'service_id' => 'required|exists:services,id',
            'staff_id' => 'required|exists:staff,id',
            'appointment_date_time' => 'required|date',
            'status' => 'required|in:scheduled,rescheduled,completed,canceled,walk_in',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Please fix the errors below.');
        }

        try {
            DB::beginTransaction();

            // Update appointment
            $appointment->patient_id = $request->patient_id;
            $appointment->staff_id = $request->staff_id;
            $appointment->service_id = $request->service_id;

            // Get service duration
            $service = Service::findOrFail($request->service_id);
            $appointment->duration = $service->duration;

            // Use the date_time from time slot selection
            $appointment->appointment_date = $request->appointment_date_time;
            $appointment->status = $request->status;
            $appointment->notes = $request->notes;

            // If status changed to canceled, record the cancellation reason
            if ($request->status === 'canceled' && $appointment->getOriginal('status') !== 'canceled') {
                if (empty($request->cancellation_reason)) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Cancellation reason is required when canceling an appointment.');
                }
                $appointment->cancellation_reason = $request->cancellation_reason;
            }

            $appointment->save();

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
     * Remove the specified appointment from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
{
    try {
        $appointment = Appointment::with('patient')->findOrFail($id);

        // Check if there are any visits associated with this appointment
        if ($appointment->visits()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete this appointment because it has associated visits.');
        }

        // Get the patient before deleting appointment
        $patient = $appointment->patient;
        $patientName = $patient->fname . ' ' . $patient->lname;

        // Delete the appointment
        $appointment->delete();

        // Delete the patient (soft delete if using SoftDeletes)
        $patient->delete();

        return redirect()->route('dashboard.appointments.index')
            ->with('success', "Appointment and patient ($patientName) deleted successfully.");

    } catch (\Exception $e) {
        return redirect()->back()
            ->with('error', 'Error deleting appointment: ' . $e->getMessage());
    }
}

    /**
     * Change the status of an appointment.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function changeStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:scheduled,rescheduled,completed,canceled,walk_in',
            'cancellation_reason' => 'required_if:status,canceled',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Please provide a reason for cancellation.');
        }

        try {
            $appointment = Appointment::findOrFail($id);
            $appointment->status = $request->status;

            if ($request->status === 'canceled') {
                $appointment->cancellation_reason = $request->cancellation_reason;
            }

            if ($request->status === 'completed' && !$appointment->visits()->exists()) {
                // Create a visit record for completed appointments
                \App\Models\Visit::create([
                    'appointment_id' => $appointment->id,
                    'patient_id' => $appointment->patient_id,
                    'staff_id' => $appointment->staff_id,
                    'service_id' => $appointment->service_id,
                    'visit_date' => $appointment->appointment_date,
                ]);
            }

            $appointment->save();

            return redirect()->back()
                ->with('success', 'Appointment status updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating appointment status: ' . $e->getMessage());
        }
    }


    public function getAvailableStaff(Request $request)
    {
        $serviceId = $request->service_id;
        $dateTime = $request->date_time;
        $currentAppointmentId = $request->current_appointment_id ?? null;

        // Validate inputs
        if (!$serviceId || !$dateTime) {
            return response()->json([
                'success' => false,
                'message' => 'Missing required parameters'
            ]);
        }

        try {
            // Parse the datetime
            $appointmentDateTime = Carbon::parse($dateTime);

            // Get service details
            $service = Service::findOrFail($serviceId);
            $duration = $service->duration;

            // Calculate appointment end time
            $appointmentEndTime = $appointmentDateTime->copy()->addMinutes($duration);

            // Get all staff that can perform this service
            $staffQuery = Staff::where('is_active', true)
                ->whereHas('services', function($query) use ($serviceId) {
                    $query->where('service_id', $serviceId);
                });

            $allStaff = $staffQuery->with('user')->get();

            // Get all appointments for this time
            $conflictingAppointmentsQuery = Appointment::where(function($query) use ($appointmentDateTime, $appointmentEndTime) {
                $query->where(function($q) use ($appointmentDateTime, $appointmentEndTime) {
                    // Appointment starts during our slot
                    $q->where('appointment_date', '>=', $appointmentDateTime)
                    ->where('appointment_date', '<', $appointmentEndTime);
                })->orWhere(function($q) use ($appointmentDateTime, $appointmentEndTime) {
                    // Appointment ends during our slot
                    $q->whereRaw("DATE_ADD(appointment_date, INTERVAL duration MINUTE) > ?", [$appointmentDateTime])
                    ->whereRaw("DATE_ADD(appointment_date, INTERVAL duration MINUTE) <= ?", [$appointmentEndTime]);
                })->orWhere(function($q) use ($appointmentDateTime, $appointmentEndTime) {
                    // Appointment contains our slot
                    $q->where('appointment_date', '<=', $appointmentDateTime)
                    ->whereRaw("DATE_ADD(appointment_date, INTERVAL duration MINUTE) >= ?", [$appointmentEndTime]);
                });
            })->where('status', '!=', 'canceled');

            // Exclude current appointment if we're editing
            if ($currentAppointmentId) {
                $conflictingAppointmentsQuery->where('id', '!=', $currentAppointmentId);
            }

            // Get staff IDs that are busy
            $busyStaffIds = $conflictingAppointmentsQuery->pluck('staff_id')->toArray();

            // Filter available staff
            $availableStaff = $allStaff->filter(function($staff) use ($busyStaffIds) {
                return !in_array($staff->id, $busyStaffIds);
            })->map(function($staff) {
                return [
                    'id' => $staff->id,
                    'name' => $staff->user->name
                ];
            })->values();

            return response()->json([
                'success' => true,
                'staff' => $availableStaff
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error finding available staff: ' . $e->getMessage()
            ]);
        }
    }


    public function trash()
{
    $appointments = Appointment::onlyTrashed()
        ->with([
            'patient' => function($query) {
                $query->withTrashed(); // Include trash patients
            },
            'dentist.user',
            'service'
        ])
        ->latest()
        ->paginate(10);

    return view('dashboard.appointments.trash', compact('appointments'));
}

    public function restore(Request $request, $id) {
        $appointment = Appointment::onlyTrashed()->findOrFail($id);
        $appointment->restore();
        return redirect()->route('dashboard.appointments.trash')->with('success', 'Appointment Restored.');
    }

    public function forceDelete($id)
{
    try {
        $appointment = Appointment::withTrashed()->with('patient')->findOrFail($id);

        // Get the patient (including trash patients)
        $patient = $appointment->patient()->withTrashed()->first();
        $patientName = $patient->fname . ' ' . $patient->lname;

        // Permanently delete the appointment
        $appointment->forceDelete();

        // Permanently delete the patient
        $patient->forceDelete();

        return redirect()->route('dashboard.appointments.trash')
            ->with('success', "Appointment and patient ($patientName) permanently deleted.");

    } catch (\Exception $e) {
        return redirect()->route('dashboard.appointments.trash')
            ->with('error', 'Error permanently deleting: ' . $e->getMessage());
    }
}




}
