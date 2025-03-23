<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Staff;
use App\Models\Service;
use App\Models\ServiceStaff;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ServiceStaffController extends Controller
{
    public function index()
    {
        $service_staff = ServiceStaff::with(['dentist.user', 'service'])->latest('id')->paginate(8);
        return view('dashboard.service_staff.index', compact('service_staff'));
    }

    public function create()
    {
        $staff = Staff::with('user')->get();
        $services = Service::all();
        $service_staff = new ServiceStaff;
        return view('dashboard.service_staff.create', compact('service_staff', 'staff', 'services'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'staff_id' => 'required|exists:staff,id',
            'service_id' => 'required|exists:services,id',
        ]);

        ServiceStaff::create($validated);

        return redirect()->route('dashboard.service-staff.index')
            ->with('success', 'Staff service assigned successfully.');
    }

    public function edit(ServiceStaff $service_staff)
    {
        $staff = Staff::with('user')->get();
        $services = Service::all();
        // dd('d');
        return view('dashboard.service_staff.edit', compact('service_staff', 'staff', 'services'));
    }

    public function update(Request $request, ServiceStaff $service_staff)
    {
        $validated = $request->validate([
            'staff_id' => 'required|exists:staff,id',
            'service_id' => 'required|exists:services,id',
        ]);

        $service_staff->update($validated);
        return redirect()->route('dashboard.service-staff.index')
            ->with('success', 'Staff service updated successfully.');
    }

    public function destroy(ServiceStaff $service_staff)
    {
        $service_staff->delete();

        return redirect()->route('dashboard.service-staff.index')
            ->with('success', 'Staff service deleted successfully.');
    }
}
