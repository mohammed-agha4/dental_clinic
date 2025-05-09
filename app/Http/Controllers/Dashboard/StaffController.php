<?php

namespace App\Http\Controllers\Dashboard;

use Exception;
use App\Models\User;
use App\Models\Staff;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
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

        $staff = Staff::whereDoesntHave('user', function ($query) { // to retrieve the staff without the admins
            $query->where('role', 'admin');
        })
            ->latest('id')
            ->with('user')
            ->paginate(8);

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

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'role' => 'required|in:admin,dentist,assistant,receptionist',
            'phone' => [
                'required',
                'string',
                'max:20',
                'unique:staff,phone',
                'regex:/^\+?[0-9\s\-\(\)]{7,20}$/'
            ],
            'department' => 'nullable|string|max:255',
            'license_number' => 'nullable|string|max:255',
            'working_hours' => 'nullable|string|max:255',
            'is_active' => 'required|boolean',
        ]);

        try {
            DB::transaction();
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            Staff::create([
                'user_id' => $user->id,
                'role' => $validated['role'],
                'phone' => $validated['phone'],
                'department' => $validated['department'] ?? null,
                'license_number' => $validated['license_number'] ?? null,
                'working_hours' => $validated['working_hours'] ?? null,
                'is_active' => $validated['is_active'],
            ]);
            DB::commit();

            return redirect()
                ->route('dashboard.staff.index')
                ->with('success', 'Staff added successfully');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Error creating staff: ' . $e->getMessage())
                ->withInput();
        }
    }


    public function show($id)
    {
        Gate::authorize('staff.view');

        $staff = Staff::where('role', '!=', 'admin')
            ->with(['user', 'services'])
            ->findOrFail($id);

        return view('dashboard.staff.show', compact('staff'));
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

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $staff->user_id,
            'password' => 'nullable|min:8|confirmed',
            'role' => 'required|in:admin,dentist,assistant,receptionist',
            'phone' => [
                'nullable',
                'string',
                'max:20',
                'unique:staff,phone,' . $staff->id,
                'regex:/^\+?[0-9\s\-\(\)]{7,20}$/'
            ],
            'department' => 'nullable|string|max:255',
            'license_number' => 'nullable|string|max:255',
            'working_hours' => 'nullable|string|max:255',
            'is_active' => 'required|boolean',
        ]);

        try {
            DB::transaction(function () use ($validated, $staff) {
                $user = $staff->user;
                $user->name = $validated['name'];
                $user->email = $validated['email'];

                if (!empty($validated['password'])) {
                    $user->password = Hash::make($validated['password']);
                }

                $user->save();

                $staff->update([
                    'role' => $validated['role'],
                    'phone' => $validated['phone'] ?? null,
                    'department' => $validated['department'] ?? null,
                    'license_number' => $validated['license_number'] ?? null,
                    'working_hours' => $validated['working_hours'] ?? null,
                    'is_active' => $validated['is_active'],
                ]);
            });

            return redirect()
                ->route('dashboard.staff.index')
                ->with('success', 'Staff updated successfully');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Error updating staff: ' . $e->getMessage())
                ->withInput();
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

            $staff = Staff::with(['user', 'appointments', 'expenses', 'visits', 'transactions', 'payments'])
                ->findOrFail($id);
            $staffName = $staff->user->name ?? 'Staff Member';


            if ($staff->role === 'dentist') {
                $hasActiveAppointments = $staff->appointments()
                    ->whereIn('status', ['scheduled', 'rescheduled', 'walk_in'])
                    ->exists();

                if ($hasActiveAppointments) {

                    DB::rollBack();
                    return redirect()->back()->with('error', 'Cannot delete dentist with active appointments');
                }
            }

            if ($staff->visits()->exists()) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Cannot delete dentist with patient visit records');
            }

            if ($staff->expenses()->exists()) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Cannot delete dentist with expense records');
            }

            if ($staff->payments()->exists()) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Cannot delete dentist with assoceated payments');
            }

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
                ->with(['user', 'appointments', 'visits', 'expenses', 'payments', 'transactions'])
                ->findOrFail($id);

            $staffName = $staff->user->name ?? 'Deleted Staff';

            $userId = $staff->user_id;

            $staff->forceDelete();

            if ($userId) {
                User::destroy($userId);
            }

            DB::commit();

            return redirect()
                ->route('dashboard.staff.trash')
                ->with('success', "{$staffName} has been permanently deleted");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->route('dashboard.staff.trash')->with('error', "Staff forceDelete failed: " . $e->getMessage());
        }
    }
}
