<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Staff;
use App\Models\Service;
use App\Models\ServiceStaff;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;

class ServiceStaffController extends Controller
{
    public function index()
    {
        Gate::authorize('service_staff.view');
        $service_staff = ServiceStaff::with(['dentist.user', 'service'])->latest('id')->paginate(8);
        return view('dashboard.service_staff.index', compact('service_staff'));
    }

    public function create()
    {
        Gate::authorize('service_staff.create');
        $staff = Staff::where('role', 'dentist')->with('user')->get();
        $services = Service::all();
        $service_staff = new ServiceStaff;
        return view('dashboard.service_staff.create', compact('service_staff', 'staff', 'services'));
    }

    public function store(Request $request)
    {
        Gate::authorize('service_staff.create');

        $validated = $request->validate([
            'staff_id' => 'required|exists:staff,id',
            'service_ids' => 'required|array|min:1',
            'service_ids.*' => 'exists:services,id',
        ]);

        // Create a record for each selected service
        foreach ($validated['service_ids'] as $service_id) {
            ServiceStaff::create([
                'staff_id' => $validated['staff_id'],
                'service_id' => $service_id
            ]);
        }

        return redirect()->route('dashboard.service-staff.index')
            ->with('success', 'Staff services assigned successfully.');
    }

    public function edit(ServiceStaff $service_staff)
    {
        Gate::authorize('service_staff.update');
        $staff = Staff::with('user')->get();
        $services = Service::all();
        // dd('d');
        return view('dashboard.service_staff.edit', compact('service_staff', 'staff', 'services'));
    }

    public function update(Request $request, ServiceStaff $service_staff)
    {
        Gate::authorize('service_staff.update');
        $validated = $request->validate([
            'staff_id' => 'nullable|exists:staff,id',
            'service_id' => 'nullable|exists:services,id',
        ]);

        $service_staff->update($validated);
        return redirect()->route('dashboard.service-staff.index')
            ->with('success', 'Staff service updated successfully.');
    }

    public function destroy(ServiceStaff $service_staff)
    {

        Gate::authorize('service_staff.delete');
        $service_staff->delete();

        return redirect()->route('dashboard.service-staff.index')
            ->with('success', 'Staff service deleted successfully.');
    }
}
