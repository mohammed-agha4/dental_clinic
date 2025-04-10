<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Service;
use App\Models\Staff;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AppointmentsController extends Controller
{
    /**
     * Display a listing of all appointments.
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
     */
    public function create()
    {
        $services = Service::where('is_active', true)->orderBy('service_name')->get();
        $patients = new Patient();

        return view('dashboard.appointments.create', compact('services', 'patients'));
    }

    /**
     * Store a newly created appointment in storage.
     */
    public function store(Request $request)
    {
        $isWalkIn = $request->has('appointment_type') && $request->appointment_type === 'walk_in';

        $rules = [
            'fname' => 'required|string|max:255',
            'lname' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'service_id' => 'required|exists:services,id',
            'staff_id' => 'required|exists:staff,id',
        ];

        if (!$isWalkIn) {
            $rules['email'] = 'required|email|max:255';
            $rules['DOB'] = 'required|date';
            $rules['gender'] = 'required|in:male,female';
            $rules['appointment_date_time'] = 'required|date';
        } else {
            $rules['email'] = 'nullable|email|max:255';
            $rules['cheif_complaint'] = 'required|string';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Please fix the errors below.');
        }

        try {
            DB::beginTransaction();

            $patient = Patient::where('phone', $request->phone)->first();

            if (!$patient) {
                $patient = new Patient();
                $patient->fname = $request->fname;
                $patient->lname = $request->lname;
                $patient->phone = $request->phone;
                $patient->email = $request->email ?? null;

                if (!$isWalkIn) {
                    $patient->DOB = $request->DOB;
                    $patient->gender = $request->gender;
                    $patient->medical_history = $request->medical_history ?? '';
                    $patient->allergies = $request->allergies ?? '';
                    $patient->Emergency_contact_name = $request->Emergency_contact_name;
                    $patient->Emergency_contact_phone = $request->Emergency_contact_phone;
                } else {
                    $patient->DOB = $request->DOB ?? now()->subYears(30)->format('Y-m-d');
                    $patient->gender = $request->gender ?? 'male';
                    $patient->medical_history = '';
                    $patient->allergies = '';
                }

                $patient->save();
            } else {
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

            $appointment = new Appointment();
            $appointment->patient_id = $patient->id;
            $appointment->staff_id = $request->staff_id;
            $appointment->service_id = $request->service_id;

            $service = Service::findOrFail($request->service_id);
            $appointment->duration = $service->duration;

            if ($isWalkIn) {
                $appointment->appointment_date = now();
                $appointment->status = 'walk_in';
            } else {
                $appointment->appointment_date = $request->appointment_date_time;
                $appointment->status = 'scheduled';
            }

            $appointment->notes = $request->notes;
            $appointment->cancellation_reason = $request->cancellation_reason;
            $appointment->reminder_sent = false;
            $appointment->save();

            if ($isWalkIn) {
                \App\Models\Visit::create([
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
     * Get available time slots for a service on a specific date
     */
    public function getAvailableSlots(Request $request)
    {
        $serviceId = $request->service_id;
        $date = $request->date;
        $duration = $request->duration;
        $currentAppointmentId = $request->current_appointment_id ?? null;

        if (!$serviceId || !$date || !$duration) {
            return response()->json([
                'success' => false,
                'message' => 'Missing required parameters'
            ]);
        }

        try {
            $dateObj = Carbon::parse($date);
            $openingTime = Carbon::parse($date . ' 09:00:00');
            $closingTime = Carbon::parse($date . ' 17:00:00');

            $now = Carbon::now();
            if ($dateObj->isToday() && $now->greaterThan($openingTime)) {
                $openingTime = $now->copy()->addHours(1)->startOfHour();
            }

            $staffIds = Staff::where('is_active', true)
                ->whereHas('services', function ($query) use ($serviceId) {
                    $query->where('service_id', $serviceId);
                })
                ->pluck('id');

            if ($staffIds->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No staff available for this service'
                ]);
            }

            $appointmentsQuery = Appointment::whereDate('appointment_date', $dateObj)
                ->whereIn('staff_id', $staffIds)
                ->where('status', '!=', 'canceled');

            if ($currentAppointmentId) {
                $appointmentsQuery->where('id', '!=', $currentAppointmentId);
            }

            $existingAppointments = $appointmentsQuery->with('service')->get();

            $slots = [];
            $currentTime = $openingTime->copy();
            $durationInMinutes = (int)$duration;

            while ($currentTime->lessThan($closingTime)) {
                $slotEndTime = $currentTime->copy()->addMinutes($durationInMinutes);

                if ($slotEndTime->greaterThan($closingTime)) {
                    break;
                }

                $timeString = $currentTime->format('H:i');
                $available = true;

                foreach ($existingAppointments as $appointment) {
                    $appointmentStart = Carbon::parse($appointment->appointment_date);
                    $appointmentDuration = $appointment->service ? $appointment->service->duration : $durationInMinutes;
                    $appointmentEnd = Carbon::parse($appointment->appointment_date)->addMinutes($appointmentDuration);

                    if ($appointmentStart->lt($slotEndTime) && $appointmentEnd->gt($currentTime)) {
                        $available = false;
                        break;
                    }
                }

                $slots[] = [
                    'time' => $timeString,
                    'available' => $available
                ];

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
     * Get available dentists for a specific service and time
     */
    public function getAvailableDentists(Request $request)
    {
        $serviceId = $request->input('service_id');
        $appointmentDate = $request->input('appointment_date');
        $currentAppointmentId = $request->input('current_appointment_id');
        $isWalkIn = $request->input('is_walk_in', false);

        if (!$serviceId) {
            return response()->json([
                'success' => false,
                'message' => 'Service ID is required'
            ]);
        }

        try {
            $dentists = Staff::with(['user'])
                ->where('role', 'dentist')
                ->where('is_active', true)
                ->whereHas('services', function ($query) use ($serviceId) {
                    $query->where('service_id', $serviceId);
                })
                ->get();

            if (!$isWalkIn && $appointmentDate) {
                $appointmentStart = Carbon::parse($appointmentDate);
                $service = Service::findOrFail($serviceId);
                $appointmentEnd = (clone $appointmentStart)->addMinutes($service->duration);

                $availableDentists = $dentists->filter(function ($dentist) use ($appointmentStart, $appointmentEnd, $currentAppointmentId) {
                    $conflicts = Appointment::where('staff_id', $dentist->id)
                        ->where('status', '!=', 'canceled')
                        ->where('id', '!=', $currentAppointmentId)
                        ->where(function ($query) use ($appointmentStart, $appointmentEnd) {
                            $query->where(function ($q) use ($appointmentStart, $appointmentEnd) {
                                $q->where('appointment_date', '>=', $appointmentStart)
                                    ->where('appointment_date', '<', $appointmentEnd);
                            })->orWhere(function ($q) use ($appointmentStart, $appointmentEnd) {
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
     * Display the specified appointment
     */
    public function show($id)
    {
        $appointment = Appointment::with(['patient', 'dentist.user', 'service'])
            ->findOrFail($id);

        return view('dashboard.appointments.show', compact('appointment'));
    }

    /**
     * Show the form for editing the specified appointment
     */
    public function edit($id)
    {
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

            if ($patient->email != $request->email) {
                $emailExists = Patient::where('email', $request->email)
                    ->where('id', '!=', $patient->id)
                    ->exists();

                if ($emailExists) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Email already exists for another patient.');
                }
            }

            if ($patient->phone != $request->phone) {
                $phoneExists = Patient::where('phone', $request->phone)
                    ->where('id', '!=', $patient->id)
                    ->exists();

                if ($phoneExists) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Phone number already exists for another patient.');
                }
            }

            $patient->fname = $request->fname;
            $patient->lname = $request->lname;
            $patient->DOB = $request->DOB;
            $patient->gender = $request->gender;
            $patient->phone = $request->phone;
            $patient->email = $request->email;
            $patient->medical_history = $request->medical_history;
            $patient->allergies = $request->allergies;
            $patient->Emergency_contact_name = $request->Emergency_contact_name;
            $patient->Emergency_contact_phone = $request->Emergency_contact_phone;
            $patient->save();

            $appointment->staff_id = $request->staff_id;
            $appointment->service_id = $request->service_id;

            $service = Service::findOrFail($request->service_id);
            $appointment->duration = $service->duration;

            if (in_array($request->status, ['scheduled', 'rescheduled'])) {
                $appointment->appointment_date = $request->appointment_date_time;

                if ($appointment->getOriginal('status') !== 'rescheduled' && $request->status === 'rescheduled') {
                    $appointment->status = 'rescheduled';
                } else {
                    $appointment->status = $request->status;
                }
            } else {
                if ($request->status !== $appointment->getOriginal('status')) {
                    $appointment->status = $request->status;
                }
            }

            if ($request->status === 'canceled') {
                if (empty($request->cancellation_reason)) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Cancellation reason is required when canceling an appointment.');
                }
                $appointment->cancellation_reason = $request->cancellation_reason;
            }

            $appointment->notes = $request->notes;
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
     * Remove the specified appointment from storage
     */
    public function destroy($id)
    {
        try {
            $appointment = Appointment::with('patient')->findOrFail($id);

            if ($appointment->visits()->count() > 0) {
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
        $appointment = Appointment::onlyTrashed()->findOrFail($id);
        $appointment->restore();
        return redirect()->route('dashboard.appointments.trash')->with('success', 'Appointment Restored.');
    }

    /**
     * Permanently delete an appointment
     */
    public function forceDelete($id)
    {
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
}
