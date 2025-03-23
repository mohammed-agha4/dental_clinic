<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Exception;
use App\Models\Staff;
use App\Models\User;
use App\Models\Appointment;
use Illuminate\Support\Facades\DB;

class StaffController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $staff = Staff::latest('id')->with('user')->paginate(8);
        return view('dashboard.staff.index', compact('staff'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        $users = User::select('id', 'name')->get();
        $staff = new Staff;
        return view('dashboard.staff.create', compact('users', 'staff'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|in:admin,dentist,assistant,receptionist',
            'phone' => 'nullable|string|max:20',
            'department' => 'nullable|string',
            'license_number' => 'nullable|string',
            'working_hours' => 'nullable|string',
            'is_active' => 'required|boolean',
        ]);


        $staff = Staff::create($validated);
        return redirect()->route('dashboard.staff.index')->with('success', 'Staff Added Successfuly');
    }


    // function show() {
    //     dd('f');
    // }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Staff $staff)
    {
        $users = User::all();
        return view('dashboard.staff.edit',compact('staff', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Staff $staff)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|in:admin,dentist,assistant,receptionist',
            'phone' => 'nullable|string|max:20',
            'department' => 'nullable|string',
            'license_number' => 'nullable|string',
            'working_hours' => 'nullable|string',
            'is_active' => 'required|boolean',
        ]);

        $staff->update($validated);
        return redirect()->route('dashboard.staff.index')->with('success', 'Staff Updated Successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */


    public function destroy($id)
    {
        try {
            $staff = Staff::findOrFail($id);


            $catchDentists = $staff->role === 'dentist';
            $catchActiveAppointment = Appointment::where('staff_id', $id)
            ->whereIn('status', ['scheduled', 'rescheduled', 'walk_in'])
            ->exists();

            // Check if staff has active appointments
            if ($catchDentists && $catchActiveAppointment) {
                return redirect()
                    ->route('dashboard.staff.index')
                    ->with('error', 'Cannot delete staff member with active appointments.');
            }

            // Delete staff if no issues
            DB::beginTransaction();
            $staff->delete();
            DB::commit();

            return redirect()
                ->route('dashboard.staff.index')
                ->with('success', 'Staff member deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->route('dashboard.staff.index')
                ->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }


}
