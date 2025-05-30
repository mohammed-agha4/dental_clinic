<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Visit;
use App\Models\Patient;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;

class PatientsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Gate::authorize('patients.view');

        $query = Patient::query()->latest('id');

        // If user is dentist but not admin
        if (auth()->user()->hasAbility('view-own-patients') && !auth()->user()->hasAbility('view-all-patients')) {
            $query->whereHas('appointments', function ($q) {
                $q->where('staff_id', auth()->user()->staff->id);
            });
        }

        $patients = $query->paginate(8);
        return view('dashboard.patients.index', compact('patients'));
    }


    public function show($id)
    {
        Gate::authorize('patients.show');
        $patient = Patient::findOrFail($id);

        return view('dashboard.patients.show', compact('patient'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Patient $patient)
    {
        Gate::authorize('patients.update');
        return view('dashboard.patients.edit', compact('patient'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Patient $patient)
    {
        Gate::authorize('patients.update');

        $validated = $request->validate([
            'fname' => 'required|string',
            'lname' => 'required|string',
            'DOB' => 'required|date',
            'gender' => 'required|in:male,female',
            'phone' => 'required|string|unique:patients,phone,' . $patient->id . '|max:20|regex:/^\+?[0-9\s\-\(\)]{7,20}$/',
            'email' => 'required|email|unique:patients,email,' . $patient->id,
            'medical_history' => 'nullable|string',
            'allergies' => 'nullable|string',
            'Emergency_contact_name' => 'nullable|string',
            'Emergency_contact_phone' => 'nullable|string|max:20',
            'last_visit_date' => 'nullable|date',
        ]);

        $patient->update($validated);
        return redirect()->route('dashboard.patients.index')->with('success', 'patient info updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */

    public function destroy(Patient $patient)
    {
        Gate::authorize('patients.delete');


        DB::beginTransaction();

        $hasAppointments = Appointment::where('patient_id', $patient->id)->exists();
        $hasVisitation = Visit::where('patient_id', $patient->id)->exists();

        if ($hasAppointments || $hasVisitation) {
            // dd($hasAppointments);
            $activeAppointments = Appointment::where('patient_id', $patient->id)
                ->where('status', ['scheduled', 'rescheduled'])
                ->exists();
            $activeVisits = Visit::where('patient_id', $patient->id)->exists();

            // dd($activeVisits);

            if ($activeAppointments || $activeVisits) {
                // dd('d');
                DB::rollBack();
                return redirect()->route('dashboard.patients.index')
                    ->with('error', 'Cannot delete patient. They have active appointments or a visitation.');
            }

            Appointment::where('patient_id', $patient->id)->get()->each(function ($appointment) {
                $appointment->delete();
            });
        }

        $patient->delete();
        DB::commit();
        return redirect()->route('dashboard.patients.index')
            ->with('success', 'Patient and their appointments deleted successfully.');
    }


    public function trash()
    {
        Gate::authorize('patients.trash');
        $patients = Patient::onlyTrashed()
            ->latest('deleted_at')
            ->paginate(10);

        return view('dashboard.patients.trash', compact('patients'));
    }



    public function restore($id)
    {
        Gate::authorize('patients.restore');
        try {
            $patient = Patient::onlyTrashed()->findOrFail($id);
            $appointment = Appointment::where('patient_id', $patient->id);
            $patient->restore();
            $appointment->restore();

            return redirect()->route('dashboard.patients.trash')
                ->with('success', 'Patient restored successfully');
        } catch (\Exception $e) {
            return redirect()->route('dashboard.patients.trash')
                ->with('error', 'Error restoring patient: ' . $e->getMessage());
        }
    }


    public function forceDelete($id)
    {
        Gate::authorize('patients.force_delete');
        try {
            $patient = Patient::onlyTrashed()->findOrFail($id);

            if ($patient->appointments()->count() > 0) {
                return redirect()->route('dashboard.patients.trash')
                    ->with('error', 'Cannot permanently delete - patient has existing appointments');
            }

            $patientName = $patient->FullName;
            $patient->forceDelete();

            return redirect()->route('dashboard.patients.trash')
                ->with('success', "Patient $patientName permanently deleted");
        } catch (\Exception $e) {
            return redirect()->route('dashboard.patients.trash')
                ->with('error', 'Error deleting patient: ' . $e->getMessage());
        }
    }


    public function search(Request $request)
    {
        $query = Patient::query();
        

        if ($request->phone) {
            $query->where('phone', $request->phone);
        }

        if ($request->email && !$request->phone) {
            $query->where('email', $request->email);
        }

        $patient = $query->first();

        return response()->json([
            'patient' => $patient,
            'success' => $patient !== null
        ]);
    }
}
