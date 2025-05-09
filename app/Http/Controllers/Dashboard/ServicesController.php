<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;

class ServicesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Gate::authorize('services.view');
        $services = Service::latest('id')->paginate(8);
        return view('dashboard.services.index', compact('services'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        Gate::authorize('services.create');
        $service = new Service();
        return view('dashboard.services.create', compact('service'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Gate::authorize('services.create');

        $validated = $request->validate([
            'service_name' => 'required|string|unique:services,service_name|max:255',
            'description' => 'nullable|string',
            'service_price' => 'required|numeric|min:0',
            'duration' => 'required|integer|gt:0',
            'is_active' => 'required|boolean',
        ]);

        Service::create($validated);
        return redirect()->route('dashboard.services.index')->with('success', 'Service Added Successfuly');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Service $service)
    {
        Gate::authorize('services.update');
        return view('dashboard.services.edit', compact('service'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Service $service)
    {
        Gate::authorize('services.update');

        $validated = $request->validate([
            'service_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('services')->ignore($service->id)

            ],
            'description' => 'nullable|string',
            'service_price' => 'required|numeric|min:0',
            'duration' => 'required|integer|gt:0',
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
        Gate::authorize('services.delete');

        $service = Service::withCount('appointments')->findOrFail($id); // withCount(): eager load the count of related records for model relationship without loading the related data

        if ($service->appointments_count > 0) {
            return redirect()
                ->route('dashboard.services.index')
                ->with('error', 'Cannot delete service because it has related appointments.');
        }

        try {
            $service->delete();
            return redirect()
                ->route('dashboard.services.index')
                ->with('success', 'Service deleted successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->route('dashboard.services.index')
                ->with('error', 'Failed to delete service: ' . $e->getMessage());
        }
    }
}
