<?php

namespace App\Http\Controllers\Dashboard\Visits;

use Exception;
use App\Models\Staff;
use App\Models\Visit;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\Service;
use App\Models\Inventory;
use App\Models\Appointment;
use Illuminate\Http\Request;
use App\Models\InventoryVisit;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\InventoryTransaction;
use Illuminate\Support\Facades\Gate;

class VisitsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
{
    Gate::authorize('visits.view');

    $query = Visit::with([
            'appointment',
            'patient',
            'staff.user',
            'service',
            'inventoryItems'
        ])->latest('id');

    if (auth()->user()->hasAbility('view-own-visits') &&
        !auth()->user()->hasAbility('view-all-visits')) {
        $query->where('staff_id', auth()->user()->staff->id);
    }

    $visits = $query->paginate(8);
    return view('dashboard.visits.index', compact('visits'));
}

    public function create(Appointment $appointment)
    {
        Gate::authorize('visits.create');
        $appointment->load(['patient', 'dentist.user', 'service']);

        $services = Service::all();
        $patients = Patient::all();
        $staff = Staff::with('user')->get();
        $visit = new Visit();
        $inventory = Inventory::where('is_active', true)->get();

        return view('dashboard.visits.create', compact(
            'appointment',
            'services',
            'staff',
            'visit',
            'patients',
            'inventory'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Gate::authorize('visits.create');

        $validated = $this->validateVisitRequest($request);

        DB::beginTransaction();

        try {
            $appointment = Appointment::findOrFail($validated['appointment_id']);

            // Create the visit record
            $visit = Visit::createVisit($validated);

            // Update appointment status
            $appointment->update(['status' => $validated['status']]);

            // Process inventory transactions if any
            if ($request->has('transaction_inventory_ids')) {
                $visit->processInventoryTransactions(Visit::prepareInventoryData($request));
            }

            DB::commit();

            return redirect()->route('dashboard.visits.index')
                ->with('success', 'Visit Added Successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Visit creation failed: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Failed to add visit: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Visit $visit)
    {
        Gate::authorize('visits.show');
        $visit->load([
            'appointment',
            'patient',
            'staff.user',
            'service',
            'inventoryItems',
            'payments'
        ]);

        return view('dashboard.visits.show', compact('visit'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Visit $visit)
    {
        Gate::authorize('visits.update');
        $visit->load(['appointment', 'inventoryItems']);

        $appointments = Appointment::all();
        $services = Service::all();
        $patients = Patient::all();
        $staff = Staff::with('user')->get();
        $inventory = Inventory::where('is_active', true)->get();

        return view('dashboard.visits.edit', compact(
            'visit',
            'appointments',
            'services',
            'patients',
            'staff',
            'inventory'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Visit $visit)
    {
        Gate::authorize('visits.update');

        $validated = $this->validateVisitRequest($request);

        DB::beginTransaction();

        try {
            $appointment = Appointment::findOrFail($validated['appointment_id']);

            // Update visit record
            $visit->updateVisit($validated);

            // Update appointment status
            $appointment->update(['status' => $validated['status']]);

            // Process inventory transactions if any
            if ($request->has('transaction_inventory_ids')) {
                $visit->processInventoryTransactions(Visit::prepareInventoryData($request));
            }

            DB::commit();

            return redirect()->route('dashboard.visits.index')
                ->with('success', 'Visit Updated Successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Visit update failed: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Failed to update visit: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Visit $visit)
    {
        Gate::authorize('visits.delete');

        DB::beginTransaction();

        try {
            if (!$visit->canBeDeleted()) {
                throw new Exception('Cannot delete - visit has payment records');
            }

            // Delete related inventory visits
            InventoryVisit::where('visit_id', $visit->id)->delete();

            // Delete the visit
            $visit->delete();

            DB::commit();

            return redirect()->route('dashboard.visits.index')
                ->with('success', 'Visit deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Visit deletion failed: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Failed to delete visit: ' . $e->getMessage());
        }
    }

    public function trash()
    {
        Gate::authorize('visits.trash');
        $visits = Visit::onlyTrashed()
            ->with(['patient', 'staff.user', 'appointment'])
            ->latest('deleted_at')
            ->paginate(10);

        return view('dashboard.visits.trash', compact('visits'));
    }

    /**
     * Restore a soft-deleted visit
     */
    public function restore($id)
    {
        Gate::authorize('visits.restore');
        try {
            DB::beginTransaction();

            $visit = Visit::onlyTrashed()->findOrFail($id);
            $visit->restore();

            // Restore related appointment if exists and was deleted
            if ($visit->appointment_id && $visit->appointment?->trashed()) {
                $visit->appointment->restore();
            }

            DB::commit();

            return redirect()->route('dashboard.visits.trash')
                ->with('success', 'Visit restored successfully');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->route('dashboard.visits.trash')
                ->with('error', 'Error restoring visit: ' . $e->getMessage());
        }
    }

    /**
     * Permanently delete a visit
     */
    public function forceDelete($id)
    {
        Gate::authorize('visits.force_delete');
        try {
            DB::beginTransaction();

            $visit = Visit::onlyTrashed()
                ->with(['payments', 'inventoryItems'])
                ->findOrFail($id);

            // Check for related records
            if ($visit->payments()->exists()) {
                throw new Exception('Cannot delete - visit has payment records');
            }

            // Detach inventory items
            $visit->inventoryItems()->detach();

            $visit->forceDelete();

            DB::commit();

            return redirect()->route('dashboard.visits.trash')
                ->with('success', 'Visit permanently deleted');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->route('dashboard.visits.trash')
                ->with('error', 'Permanent deletion failed: ' . $e->getMessage());
        }
    }

    /**
     * Validate visit request data
     */
    protected function validateVisitRequest(Request $request): array
    {
        return $request->validate([
            'appointment_id' => 'required|exists:appointments,id',
            'patient_id' => 'required|exists:patients,id',
            'staff_id' => 'required|exists:staff,id',
            'service_id' => 'required|exists:services,id',
            'visit_date' => 'required|date',
            'status' => 'required|in:scheduled,walk_in,completed,rescheduled,canceled',
            'cheif_complaint' => 'nullable|string',
            'diagnosis' => 'nullable|string',
            'treatment_notes' => 'nullable|string',
            'next_visit_notes' => 'nullable|string',

            // Inventory transaction validations
            'transaction_inventory_ids' => 'nullable|array',
            'transaction_inventory_ids.*' => 'required_with:transaction_inventory_ids|exists:inventories,id',
            'transaction_types' => 'nullable|array',
            'transaction_types.*' => 'required_with:transaction_inventory_ids|in:purchase,use,adjustment,return',
            'transaction_quantities' => 'nullable|array',
            'transaction_quantities.*' => 'required_with:transaction_inventory_ids|integer|min:1',
            'transaction_prices' => 'nullable|array',
            'transaction_prices.*' => 'required_with:transaction_inventory_ids|numeric|min:0',
            'transaction_dates' => 'nullable|array',
            'transaction_dates.*' => 'required_with:transaction_inventory_ids|date',
            'transaction_notes' => 'nullable|array',
        ]);
    }
}
