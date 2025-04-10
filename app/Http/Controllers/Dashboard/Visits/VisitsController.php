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

// class VisitsController extends Controller
// {
//     /**
//      * Display a listing of the resource.
//      */
//     public function index()
//     {
//         $visits = Visit::with(['appointment', 'patient', 'staff.user', 'service', 'inventoryItems'])->latest('id')->paginate(8);
//         return view('dashboard.visits.index', compact('visits'));
//     }



//     public function create(Appointment $appointment)
//     {
//         $appointment->load(['patient', 'dentist.user', 'service']);

//         $services = Service::all();
//         $patients = Patient::all();
//         $staff = Staff::with('user')->get();
//         $visit = new Visit();
//         $inventory = Inventory::where('is_active', true)->get();
//         $inventory_trans = InventoryTransaction::all();
//         return view('dashboard.visits.create', compact('appointment', 'services', 'staff', 'visit', 'patients', 'inventory', 'inventory_trans'));
//     }


//     /**
//      * Store a newly created resource in storage.
//      */

//     public function store(Request $request)
//     {
//         // dd($request->all());
//         $request->validate([
//             'appointment_id' => 'required|exists:appointments,id',
//             'patient_id' => 'required|exists:patients,id',
//             'staff_id' => 'required|exists:staff,id',
//             'service_id' => 'required|exists:services,id',
//             'visit_date' => 'required|date',
//             'status' => 'required|in:scheduled,walk_in,completed,rescheduled,canceled',

//             'transaction_inventory_ids' => 'nullable|array',
//             'transaction_inventory_ids.*' => 'required_with:transaction_inventory_ids|exists:inventories,id',
//             'transaction_types' => 'nullable|array',
//             'transaction_types.*' => 'required_with:transaction_inventory_ids|in:purchase,use,adjustment,return',
//             'transaction_quantities' => 'nullable|array',
//             'transaction_quantities.*' => 'required_with:transaction_inventory_ids|integer|min:1',
//             'transaction_prices' => 'nullable|array',
//             'transaction_prices.*' => 'required_with:transaction_inventory_ids|numeric|min:0',
//             'transaction_dates' => 'nullable|array',
//             'transaction_dates.*' => 'required_with:transaction_inventory_ids|date',
//             'transaction_notes' => 'nullable|array',
//         ]);

//         DB::beginTransaction();

//         try {

//             $appointment = Appointment::findOrFail($request->appointment_id);

//             $visit = Visit::create([
//                 'appointment_id' => $appointment->id,
//                 'patient_id' => $request->patient_id,
//                 'staff_id' => $request->staff_id,
//                 'service_id' => $request->service_id,
//                 'visit_date' => $request->visit_date,
//                 'cheif_complaint' => $request->cheif_complaint,
//                 'diagnosis' => $request->diagnosis,
//                 'treatment_notes' => $request->treatment_notes,
//                 'next_visit_notes' => $request->next_visit_notes,
//             ]);

//             $appointment->update([
//                 'status' => $request->status
//             ]);

//             if ($request->has('transaction_inventory_ids') && is_array($request->transaction_inventory_ids)) {
//                 $transactionInventoryIds = $request->transaction_inventory_ids;
//                 $transactionTypes = $request->transaction_types;
//                 $transactionQuantities = $request->transaction_quantities;
//                 $transactionPrices = $request->transaction_prices;
//                 $transactionDates = $request->transaction_dates;
//                 $transactionNotes = $request->transaction_notes;

//                 foreach ($transactionInventoryIds as $index => $inventoryId) {  // $transactionInventoryIds is a var for the field name whitch is an associative array, the $index is the key of the array, and the $inventoryId is the value of the array
//                     if (empty($inventoryId)) continue; // Skip empty selections

//                     $type = $transactionTypes[$index] ?? 'use';
//                     $quantity = $transactionQuantities[$index] ?? 1;
//                     $unitPrice = $transactionPrices[$index] ?? 0;
//                     $date = $transactionDates[$index] ?? date('Y-m-d');
//                     $note = $transactionNotes[$index] ?? null;

//                     // Get the inventory item and create transaction
//                     $inventoryItem = Inventory::findOrFail($inventoryId);
//                     InventoryTransaction::create([
//                         'inventory_id' => $inventoryId,
//                         'staff_id' => $request->staff_id,
//                         'type' => $type,
//                         'quantity' => $quantity,
//                         'unit_price' => $unitPrice,
//                         'transaction_date' => $date,
//                         'notes' => $note ? $note : "Transaction during visit #$visit->id"
//                     ]);

//                     // create inventory_visit relation for items that are "used"
//                     if ($type == 'use') {
//                         InventoryVisit::create([
//                             'visit_id' => $visit->id,
//                             'inventory_id' => $inventoryId,
//                             'quantity_used' => $quantity,
//                             'notes' => $note
//                         ]);
//                     }

//                     // Update inventory quantity based on transaction type


//                     if ($type == 'use' && $quantity > 0) {
//                         if ($inventoryItem->quantity < $quantity) {
//                             throw new \Exception("Not enough inventory for item: {$inventoryItem->name}");
//                         }
//                         $inventoryItem->decrement('quantity', $quantity);



//                     }



//                     // switch ($type) {
//                     //     case 'purchase':
//                     //         $inventoryItem->increment('quantity', $quantity);
//                     //         break;
//                     //     case 'use':
//                     //     case 'return':
//                     //         // Verify there's enough quantity before decrementing
//                     //         if ($inventoryItem->quantity < $quantity) {
//                     //             throw new \Exception("Not enough inventory for item: {$inventoryItem->name}");
//                     //         }
//                     //         $inventoryItem->decrement('quantity', $quantity);
//                     //         break;
//                     //     case 'adjustment':
//                     //         // For adjustment, directly set the quantity
//                     //         $inventoryItem->update(['quantity' => $quantity]);
//                     //         break;
//                     // }
//                 }
//             }

//             DB::commit();

//             return redirect()->route('dashboard.visits.index')
//                 ->with('success', 'Visit Added Successfully');
//         } catch (\Exception $e) {
//             DB::rollBack();
//             return redirect()->back()
//                 ->with('error', 'Failed to add visit: ' . $e->getMessage())
//                 ->withInput();
//         }
//     }

//     /**
//      * Display the specified resource.
//      */
//     public function show(string $id)
//     {
//         //
//     }

//     /**
//      * Show the form for editing the specified resource.
//      */
//     public function edit(Visit $visit)
//     {

//         $appointments = Appointment::all();
//         $services = Service::all();
//         $patients = Patient::all();
//         $staff = Staff::with('user')->get();
//         return view('dashboard.visits.edit',compact( 'visit', 'appointments', 'services', 'patients', 'staff'));
//     }

//     /**
//      * Update the specified resource in storage.
//      */

//     public function update(Request $request, Visit $visit)
// {

//     $request->validate([
//         'appointment_id' => 'required|exists:appointments,id',
//         'service_id' => 'required|exists:services,id',
//         'patient_id' => 'required|exists:patients,id',
//         'staff_id' => 'required|exists:staff,id',
//         'visit_date' => 'required|date',
//         'status' => 'required|in:scheduled,walk_in,completed,rescheduled,canceled'
//     ]);

//     DB::beginTransaction();

//     try {
//         $appointment = Appointment::findOrFail($request->appointment_id);

//         $visit->update([
//             'appointment_id' => $request->appointment_id,
//             'service_id' => $request->service_id,
//             'patient_id' => $request->patient_id,
//             'staff_id' => $request->staff_id,
//             'visit_date' => $request->visit_date,
//             'cheif_complaint' => $request->cheif_complaint,
//             'diagnosis' => $request->diagnosis,
//             'treatment_notes' => $request->treatment_notes,
//             'next_visit_notes' => $request->next_visit_notes,
//         ]);

//         $appointment->update([
//             'status' => $request->status
//         ]);

//         DB::commit();

//         return redirect()->route('dashboard.visits.index')->with('success', 'Visit Updated Successfully');

//     } catch (\Exception $e) {
//         DB::rollBack();
//         return redirect()->back()->with('error', 'Failed to update visit: ' . $e->getMessage())->withInput();
//     }
// }

//     /**
//      * Remove the specified resource from storage.
//      */
//     public function destroy(Visit $visit)
//     {
//         // dd('s');
//         try {

//             $LinkedPayment = Payment::where('visit_id', $visit->id)->first();
//                 if ($LinkedPayment) {
//                     return redirect()->back()->with('error', 'Cannot delete visit with linked Payment');
//                 }

//             $visit->delete();
//             return redirect()
//                     ->route('dashboard.visits.index')
//                     ->with('success', 'visit deleted successfully.');

//         } catch (\Exception $e) {
//             // dd('d');
//             DB::rollBack();
//             dd($e->getMessage());
//         }
//     }
// }




class VisitsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $visits = Visit::with(['appointment', 'patient', 'staff.user', 'service', 'inventoryItems'])
                    ->latest('id')
                    ->paginate(8);

        return view('dashboard.visits.index', compact('visits'));
    }

    public function create(Appointment $appointment)
    {
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
        $request->validate([
            'appointment_id' => 'required|exists:appointments,id',
            'patient_id' => 'required|exists:patients,id',
            'staff_id' => 'required|exists:staff,id',
            'service_id' => 'required|exists:services,id',
            'visit_date' => 'required|date',
            'status' => 'required|in:scheduled,walk_in,completed,rescheduled,canceled',

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

        DB::beginTransaction();

        try {
            $appointment = Appointment::findOrFail($request->appointment_id);

            // Create the visit record
            $visit = Visit::create([
                'appointment_id' => $appointment->id,
                'patient_id' => $request->patient_id,
                'staff_id' => $request->staff_id,
                'service_id' => $request->service_id,
                'visit_date' => $request->visit_date,
                'cheif_complaint' => $request->cheif_complaint,
                'diagnosis' => $request->diagnosis,
                'treatment_notes' => $request->treatment_notes,
                'next_visit_notes' => $request->next_visit_notes,
            ]);

            // Update appointment status
            $appointment->update(['status' => $request->status]);

            // Process inventory transactions if any
            if ($request->has('transaction_inventory_ids')) {
                $this->processInventoryTransactions($request, $visit);
            }

            DB::commit();

            return redirect()->route('dashboard.visits.index')
                ->with('success', 'Visit Added Successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Visit creation failed: ' . $e->getMessage());

            dd('error', 'Failed to add visit: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Process inventory transactions for a visit
     */
    protected function processInventoryTransactions(Request $request, Visit $visit)
    {
        $transactionInventoryIds = $request->transaction_inventory_ids;
        $transactionTypes = $request->transaction_types;
        $transactionQuantities = $request->transaction_quantities;
        $transactionPrices = $request->transaction_prices;
        $transactionDates = $request->transaction_dates;
        $transactionNotes = $request->transaction_notes;

        foreach ($transactionInventoryIds as $index => $inventoryId) {
            if (empty($inventoryId)) continue;

            $type = $transactionTypes[$index] ?? 'use';
            $quantity = $transactionQuantities[$index] ?? 1;
            $unitPrice = $transactionPrices[$index] ?? 0;
            $date = $transactionDates[$index] ?? now()->format('Y-m-d');
            $note = $transactionNotes[$index] ?? null;

            $inventoryItem = Inventory::findOrFail($inventoryId);

            // Create inventory transaction
            $transaction = InventoryTransaction::create([
                'inventory_id' => $inventoryId,
                'staff_id' => $request->staff_id,
                'type' => $type,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'transaction_date' => $date,
                'notes' => $note ?: "Transaction during visit #{$visit->id}"
            ]);

            // Handle inventory quantity updates
            $this->updateInventoryQuantity($inventoryItem, $type, $quantity);

            // Create inventory_visit relation for items that are "used"
            if ($type === 'use') {
                InventoryVisit::create([
                    'visit_id' => $visit->id,
                    'inventory_id' => $inventoryId,
                    'quantity_used' => $quantity,
                    'notes' => $note
                ]);
            }
        }
    }

    /**
     * Update inventory quantity based on transaction type
     */
    protected function updateInventoryQuantity(Inventory $inventory, string $type, int $quantity)
{
    switch ($type) {
        case 'purchase':
            $inventory->increment('quantity', $quantity);
            break;

        case 'use':
        case 'return':
            if ($inventory->quantity < $quantity) {
                throw new \Exception("Not enough inventory for item: {$inventory->name}");
            }
            $inventory->decrement('quantity', $quantity); // Observer handles notifications
            break;

        case 'adjustment':
            $inventory->update(['quantity' => $quantity]);
            break;
    }
}



    /**
     * Display the specified resource.
     */
    public function show(Visit $visit)
    {
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
        $request->validate([
            'appointment_id' => 'required|exists:appointments,id',
            'service_id' => 'required|exists:services,id',
            'patient_id' => 'required|exists:patients,id',
            'staff_id' => 'required|exists:staff,id',
            'visit_date' => 'required|date',
            'status' => 'required|in:scheduled,walk_in,completed,rescheduled,canceled',

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

        DB::beginTransaction();

        try {
            $appointment = Appointment::findOrFail($request->appointment_id);

            // Update visit record
            $visit->update([
                'appointment_id' => $request->appointment_id,
                'service_id' => $request->service_id,
                'patient_id' => $request->patient_id,
                'staff_id' => $request->staff_id,
                'visit_date' => $request->visit_date,
                'cheif_complaint' => $request->cheif_complaint,
                'diagnosis' => $request->diagnosis,
                'treatment_notes' => $request->treatment_notes,
                'next_visit_notes' => $request->next_visit_notes,
            ]);

            // Update appointment status
            $appointment->update(['status' => $request->status]);

            // Process inventory transactions if any
            if ($request->has('transaction_inventory_ids')) {
                $this->processInventoryTransactions($request, $visit);
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
        DB::beginTransaction();

        try {
            // Check for linked payments
            if (Payment::where('visit_id', $visit->id)->exists()) {
                return redirect()->back()
                    ->with('error', 'Cannot delete visit with linked payments');
            }

            // Delete related inventory visits first
            InventoryVisit::where('visit_id', $visit->id)->delete();

            // Finally delete the visit
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
        try {
            DB::beginTransaction();

            $visit = Visit::onlyTrashed()
                ->with(['payments', 'inventoryItems'])
                ->findOrFail($id);

            // Check for related records
            if ($visit->payments()->exists()) {
                throw new Exception('Cannot delete - visit has payment records');
            }

            if ($visit->inventoryItems()->exists()) {
                // Detach inventory items rather than blocking
                $visit->inventoryItems()->detach();
            }

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
}
