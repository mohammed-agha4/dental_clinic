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
             DB::beginTransaction();
     
             $staff = Staff::with(['user', 'appointments', 'expenses', 'visits', 'inventoryTransactions', 'payments'])
                 ->findOrFail($id);
             $staffName = $staff->user->name ?? 'Staff Member';
     
             // Check for active dentist appointments
             if ($staff->role === 'dentist') {
                 $hasActiveAppointments = $staff->appointments()
                     ->whereIn('status', ['scheduled', 'rescheduled', 'walk_in'])
                     ->exists();
     
                 if ($hasActiveAppointments) {
                     throw new Exception('Cannot delete dentist with active appointments (scheduled/rescheduled/walk-in)');
                 }
             }
     
             // Check all foreign key constraints
             $errorMessages = [];
     
             // 1. Check appointments (any status)
             if ($staff->appointments()->exists()) {
                 $errorMessages[] = 'has historical appointment records';
             }
     
             // 2. Check visits (restrict)
             if ($staff->visits()->exists()) {
                 $errorMessages[] = 'has patient visit records';
             }
     
             // 3. Check expenses (restrict)
             if ($staff->expenses()->exists()) {
                 $errorMessages[] = 'has expense records';
             }
     
             // 4. Check payments (set null, but we might want to track)
             if ($staff->payments()->exists()) {
                 $errorMessages[] = 'has payment processing records';
             }
     
             // 5. Check inventory transactions
             if ($staff->inventoryTransactions()->exists()) {
                 $errorMessages[] = 'has inventory transaction records';
             }
     
             // 6. Check service_staff pivot table (cascade)
             // No check needed as it cascades
     
             if (!empty($errorMessages)) {
                 throw new Exception(
                     'Cannot delete staff member who ' . implode(', ', $errorMessages) . 
                     '. Please reassign these records first.'
                 );
             }
     
             // Soft delete the staff member if no constraints violated
             $staff->delete();
     
             DB::commit();
     
             return redirect()
                 ->route('dashboard.staff.index')
                 ->with('success', "{$staffName} has been deactivated (soft deleted)");
     
         } catch (\Exception $e) {
             DB::rollBack();
             logger()->error("Staff deletion failed: " . $e->getMessage());
     
             return redirect()
                 ->route('dashboard.staff.index')
                 ->with('error', $e->getMessage());
         }
     }





    public function trash()
    {
        $staff = Staff::onlyTrashed()
            ->with('user')
            ->latest('deleted_at')
            ->paginate(8);

        return view('dashboard.staff.trash', compact('staff'));
    }

    /**
     * Restore a soft-deleted staff member
     */
    public function restore($id)
    {
        try {
            DB::beginTransaction();

            $staff = Staff::onlyTrashed()->findOrFail($id);

            // Check if the associated user exists
            if (!$staff->user) {
                throw new Exception('Associated user account not found');
            }

            $staff->restore();

            DB::commit();

            return redirect()->route('dashboard.staff.trash')
                ->with('success', 'Staff member restored successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('dashboard.staff.trash')
                ->with('error', 'Error restoring staff: ' . $e->getMessage());
        }
    }

    /**
     * Permanently delete a staff member
     */
    public function forceDelete($id)
{
    try {
        DB::beginTransaction();

        $staff = Staff::onlyTrashed()
            ->with(['user', 'appointments', 'visits', 'expenses', 'payments', 'transactions'])
            ->findOrFail($id);
        
        $staffName = $staff->user->name ?? 'Deleted Staff';
        $errorMessages = [];

        // Check for active dentist appointments (scheduled/rescheduled/walk-in)
        if ($staff->role === 'dentist') {
            $hasActiveAppointments = $staff->appointments()
                ->whereIn('status', ['scheduled', 'rescheduled', 'walk_in'])
                ->exists();

            if ($hasActiveAppointments) {
                $errorMessages[] = 'has active appointments (scheduled/rescheduled/walk-in)';
            }
        }

        // Check all restricted foreign key relationships
        if ($staff->visits()->exists()) {
            $errorMessages[] = 'has associated patient visits';
        }

        if ($staff->expenses()->exists()) {
            $errorMessages[] = 'has expense records';
        }

        if ($staff->payments()->exists()) {
            // Payments has set null, but we should still warn
            $errorMessages[] = 'has payment records (these will be unassigned)';
        }


        // Check inventory transactions
        if ($staff->transactions()->exists()) {
            $errorMessages[] = 'has inventory transaction records';
        }

        if (!empty($errorMessages)) {
            $message = 'Cannot permanently delete staff member who ' . implode(', ', $errorMessages);
            
            // Add specific instructions for each case
            if (in_array('has associated patient visits', $errorMessages)) {
                $message .= '. Please reassign visits first.';
            }
            elseif (in_array('has expense records', $errorMessages)) {
                $message .= '. Please reassign expenses first.';
            }
            else {
                $message .= '. Please resolve these dependencies first.';
            }
            
            throw new Exception($message);
        }

        // If we get here, safe to force delete
        $staff->forceDelete();

        DB::commit();

        return redirect()
            ->route('dashboard.staff.trash')
            ->with('success', "{$staffName} has been permanently deleted");

    } catch (\Exception $e) {
        DB::rollBack();
        logger()->error("Staff forceDelete failed: " . $e->getMessage());
        
        return redirect()
            ->route('dashboard.staff.trash')
            ->with('error', $e->getMessage());
    }
}}
