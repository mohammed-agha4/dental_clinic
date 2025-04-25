<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Role;
use App\Models\User;
use App\Models\RoleUser;
use App\Models\RoleAbility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;

class UsersRolesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Gate::authorize('user_roles.view');
        //     $roles = Role::with('role')->get();
        //     $roleAbilities = RoleAbility::all();
        $role_users = RoleUser::with(['user', 'role'])->paginate(8);
        return view('dashboard.user_roles.index', compact('role_users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
    Gate::authorize('user_roles.create');
        // $role_abilities = RoleAbility::all();
        $roles = Role::all();
        $users = User::all();
        return view('dashboard.user_roles.create', compact('roles', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Gate::authorize('user_roles.create');
        // dd($request->all());
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'role_id' => 'required|exists:roles,id',
        ]);

        // Check if the relationship already exists
        $exists = RoleUser::where('user_id', $validated['user_id'])
            ->where('role_id', $validated['role_id'])
            ->exists();

        if ($exists) {
            return redirect()->route('dashboard.user-roles.index')
                ->with('error', 'This role is already assigned to this user.');
        }

        RoleUser::create($validated);

        return redirect()->route('dashboard.user-roles.index')
            ->with('success', 'User Role assigned successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     * This method handles the resource route for edit
     */


    /**
     * Show the form for editing with composite keys
     */
    public function editComposite($user_id, $role_id)
    {
        Gate::authorize('user_roles.update');
        // Find the relationship by composite key
        $user_role = RoleUser::where('user_id', $user_id)
            ->where('role_id', $role_id)
            ->firstOrFail();

        $users = User::all();
        $roles = Role::all();

        return view('dashboard.user_roles.edit', compact('user_role', 'users', 'roles'));
    }



    public function updateComposite(Request $request, $user_id, $role_id)
    {
        Gate::authorize('user_roles.update');

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'role_id' => 'required|exists:roles,id',
        ]);

        // Check if we're trying to update to an existing combination
        if ($validated['user_id'] != $user_id || $validated['role_id'] != $role_id) {
            $exists = RoleUser::where('user_id', $validated['user_id'])
                ->where('role_id', $validated['role_id'])
                ->exists();

            if ($exists) {
                return redirect()->route('dashboard.user-roles.index')
                    ->with('error', 'This role is already assigned to this user.');
            }

            // Instead of updating, delete the old record and create a new one
            RoleUser::where('user_id', $user_id)
                ->where('role_id', $role_id)
                ->delete();

            RoleUser::create($validated);
        }
        // If we're not changing the keys, no need to do anything since they're the same

        return redirect()->route('dashboard.user-roles.index')
            ->with('success', 'User Role Updated Successfully.');
    }


    /**
     * Remove with composite keys
     */
    public function destroyComposite($user_id, $role_id)
    {
        Gate::authorize('user_roles.delete');
        // Find and delete by composite key
        $deleted = RoleUser::where('user_id', $user_id)
            ->where('role_id', $role_id)
            ->delete();

        if ($deleted) {
            return redirect()->route('dashboard.user-roles.index')
                ->with('success', 'User Role Deleted Successfully.');
        }

        return redirect()->route('dashboard.user-roles.index')
            ->with('error', 'User Role not found.');
    }
}
