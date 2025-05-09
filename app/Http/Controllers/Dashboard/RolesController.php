<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Role;
use App\Models\RoleAbility;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;

class RolesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Gate::authorize('roles.view');
        $roles = Role::paginate(8);
        return view('dashboard.roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        Gate::authorize('roles.create');
        $role = new Role();
        return view('dashboard.roles.create', compact('role'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Gate::authorize('roles.create');
        $request->validate([
            'name' => 'required|unique:roles,name|string|max:255',
            'abilities' => 'required|array',
        ]);

        $role = Role::createWithAbilities($request);
        return redirect()->route('dashboard.roles.index')->with('success', 'Role Created Successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        Gate::authorize('roles.update');
        // pluck -> instead of reterning object, it returnes an assossiative array , the first argument(type) is the value, and the second one (ability) is the key
        $role_abilities = $role->abilities()->pluck('type', 'ability')->toArray();
        return view('dashboard.roles.edit', compact('role', 'role_abilities'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        Gate::authorize('roles.update');
        $request->validate([
            'name' => 'required|string|max:255',
            'abilities' => 'required|array',
        ]);

        $role->updateWithAbilities($request);

        return redirect()->route('dashboard.roles.index')->with('success', 'Role Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Gate::authorize('roles.delete');

        $role = Role::withCount('users')->findOrFail($id);

        if ($role->users_count > 0) {
            return redirect()
                ->route('dashboard.roles.index')
                ->with('error', 'Cannot delete role: There are users assigned to this role.');
        }

        $role->delete();

        return redirect()
            ->route('dashboard.roles.index')
            ->with('success', 'Role deleted successfully');
    }
}
