<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class ServicesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $services = Service::latest('id')->paginate(8);
        return view('dashboard.services.index', compact('services'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $service = new Service();
        return view('dashboard.services.create', compact('service'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_name' => 'required|string',
            'description' => 'nullable|string',
            'service_price' => 'required|numeric|min:0',
            'duration' => 'required|integer',
            'is_active' => 'required|boolean',
        ]);

        $service = Service::create($validated);
        return redirect()->route('dashboard.services.index')->with('success', 'Service Added Successfuly');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Service $service)
    {
        return view('dashboard.services.edit',compact('service'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Service $service)
    {
        $validated = $request->validate([
            'service_name' => 'required|string',
            'description' => 'nullable|string',
            'service_price' => 'required|numeric|min:0',
            'duration' => 'required|integer',
            'is_active' => 'required|boolean',
        ]);


        $service->update($validated);
        return redirect()->route('dashboard.services.index');
    }

    /**
     * Remove the specified resource from storage.
     */

    public function destroy($id)
    {

        $service = Service::findOrFail($id);
        $service->delete();
        return redirect()
                ->route('dashboard.services.index')
                ->with('success', 'Service deleted successfully.');
    }
}
