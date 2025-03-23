<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Visit;
use App\Models\Patient;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class PatientsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $patients = Patient::latest('id')->paginate(8);
        return view('dashboard.patients.index', compact('patients'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        $patient = Patient::all();
        return view('dashboard.patients.create', compact('patient'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Patient $patient)
    {
        return view('dashboard.patients.edit',compact('patient'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Patient $patient)
    {
        $rules = [
            'fname' => 'required|string',
            'lname' => 'required|string',
            'DOB' => 'required|date',
            'gender' => 'required|in:male,female',
            'phone' => 'required|string|unique:patients,phone|max:20',
            'email' => 'required|email|unique:patients,email',
            'medical_history' => 'nullable|string',
            'allergies' => 'nullable|string',
            'Emergency_contact_name' => 'nullable|string',
            'Emergency_contact_phone' => 'nullable|string|max:20',
            'last_visit_date' => 'nullable|date',
        ];


        $patient->update($request->all());
        return redirect()->route('dashboard.patients.index');
    }

    /**
     * Remove the specified resource from storage.
     */

    public function destroy(Patient $patient)
    {
            DB::beginTransaction();


            // Check if patient has appointments
            $hasAppointments = Appointment::where('patient_id', $patient->id)->exists();
            $hasVisitation = Visit::where('patient_id', $patient->id)->exists();

            if ($hasAppointments || $hasVisitation) {
                // dd($hasAppointments);
                $activeAppointments = Appointment::where('patient_id', $patient->id)
                    ->where('status', ['scheduled', 'rescheduled'])
                    ->exists();
                $activeVisits = Visit::where('patient_id', $patient->id);


                if ($activeAppointments || $activeVisits) {
                    // dd('d');
                    DB::rollBack();
                    return redirect()->route('dashboard.patients.index')
                        ->with('error', 'Cannot delete patient. They have active appointments or a visitation.');
                }

                // If all appointments are cancelled, soft delete them first
                Appointment::where('patient_id', $patient->id)->get()->each(function ($appointment) {
                    $appointment->delete();
                });
            }

            $patient->delete();
            DB::commit();
            return redirect()->route('dashboard.patients.index')
                ->with('success', 'Patient and their appointments deleted successfully.');

    }

}
