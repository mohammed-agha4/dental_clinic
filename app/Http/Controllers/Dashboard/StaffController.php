<?php

namespace App\Http\Controllers\Dashboard;

use Exception;
use App\Models\User;
use App\Models\Staff;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;

class StaffController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Gate::authorize('staff.view');
        $staff = Staff::latest('id')->with('user')->paginate(8);
        return view('dashboard.staff.index', compact('staff'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        Gate::authorize('staff.create');
        $staff = new Staff;
        return view('dashboard.staff.create', compact('staff'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Gate::authorize('staff.create');
        // dd($request->all());
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'role' => 'required|in:admin,dentist,assistant,receptionist',
            'phone' => 'nullable|string|max:20',
            'department' => 'nullable|string',
            'license_number' => 'nullable|string',
            'working_hours' => 'nullable|string',
            'is_active' => 'required|boolean',
        ]);

        try {
            DB::beginTransaction();

            // Create user first
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']), // Don't forget to hash the password!
            ]);

            // Then create staff with user_id reference
            $staff = Staff::create([
                'user_id' => $user->id,
                'role' => $validated['role'],
                'phone' => $validated['phone'] ?? null,
                'department' => $validated['department'] ?? null,
                'license_number' => $validated['license_number'] ?? null,
                'working_hours' => $validated['working_hours'] ?? null,
                'is_active' => $validated['is_active'],
            ]);

            DB::commit();
            return redirect()->route('dashboard.staff.index')->with('success', 'Staff Added Successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error creating staff: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Staff $staff)
    {
        Gate::authorize('staff.update');
        // Load the associated user
        $staff->load('user');
        return view('dashboard.staff.edit', compact('staff'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Staff $staff)
    {
        Gate::authorize('staff.update');
        $validatedStaff = $request->validate([
            'role' => 'required|in:admin,dentist,assistant,receptionist',
            'phone' => 'nullable|string|max:20',
            'department' => 'nullable|string',
            'license_number' => 'nullable|string',
            'working_hours' => 'nullable|string',
            'is_active' => 'required|boolean',
        ]);

        $validatedUser = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $staff->user_id,
        ]);

        // Only validate password if it's provided
        if ($request->filled('password')) {
            $request->validate([
                'password' => 'min:8',
            ]);
        }

        try {
            DB::beginTransaction();

            // Update user information
            $user = User::findOrFail($staff->user_id);
            $user->name = $validatedUser['name'];
            $user->email = $validatedUser['email'];

            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }

            $user->save();

            // Update staff information
            $staff->update($validatedStaff);

            DB::commit();
            return redirect()->route('dashboard.staff.index')->with('success', 'Staff Updated Successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error updating staff: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        Gate::authorize('staff.delete');
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
        Gate::authorize('staff.trash');
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
        Gate::authorize('staff.restore');
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
        Gate::authorize('staff.force_delete');
        try {
            DB::beginTransaction();

            $staff = Staff::onlyTrashed()
                ->with(['user', 'appointments', 'visits', 'expenses', 'payments', 'inventoryTransactions'])
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
            if ($staff->inventoryTransactions()->exists()) {
                $errorMessages[] = 'has inventory transaction records';
            }

            if (!empty($errorMessages)) {
                $message = 'Cannot permanently delete staff member who ' . implode(', ', $errorMessages);

                // Add specific instructions for each case
                if (in_array('has associated patient visits', $errorMessages)) {
                    $message .= '. Please reassign visits first.';
                } elseif (in_array('has expense records', $errorMessages)) {
                    $message .= '. Please reassign expenses first.';
                } else {
                    $message .= '. Please resolve these dependencies first.';
                }

                throw new Exception($message);
            }

            // If we get here, safe to force delete
            // First get the user_id to delete the user after
            $userId = $staff->user_id;

            // Force delete staff
            $staff->forceDelete();

            // Also delete the associated user
            if ($userId) {
                User::destroy($userId);
            }

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
    }
}
