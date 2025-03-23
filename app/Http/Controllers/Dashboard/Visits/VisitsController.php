<?php

namespace App\Http\Controllers\Dashboard\Visits;

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

class VisitsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $visits = Visit::with(['appointment', 'patient', 'staff.user', 'service', 'inventoryItems'])->latest('id')->paginate(8);
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
    $inventory_trans = InventoryTransaction::all();
    return view('dashboard.visits.create', compact('appointment', 'services', 'staff', 'visit', 'patients', 'inventory', 'inventory_trans'));
    }


    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request)
    {
        // dd($request->all());
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

            $appointment->update([
                'status' => $request->status
            ]);

            if ($request->has('transaction_inventory_ids') && is_array($request->transaction_inventory_ids)) {
                $transactionInventoryIds = $request->transaction_inventory_ids;
                $transactionTypes = $request->transaction_types;
                $transactionQuantities = $request->transaction_quantities;
                $transactionPrices = $request->transaction_prices;
                $transactionDates = $request->transaction_dates;
                $transactionNotes = $request->transaction_notes;

                foreach ($transactionInventoryIds as $index => $inventoryId) {  // $transactionInventoryIds is a var for the field name whitch is an associative array, the $index is the key of the array, and the $inventoryId is the value of the array
                    if (empty($inventoryId)) continue; // Skip empty selections

                    $type = $transactionTypes[$index] ?? 'use';
                    $quantity = $transactionQuantities[$index] ?? 1;
                    $unitPrice = $transactionPrices[$index] ?? 0;
                    $date = $transactionDates[$index] ?? date('Y-m-d');
                    $note = $transactionNotes[$index] ?? null;

                    // Get the inventory item and create transaction
                    $inventoryItem = Inventory::findOrFail($inventoryId);
                    InventoryTransaction::create([
                        'inventory_id' => $inventoryId,
                        'staff_id' => $request->staff_id,
                        'type' => $type,
                        'quantity' => $quantity,
                        'unit_price' => $unitPrice,
                        'transaction_date' => $date,
                        'notes' => $note ? $note : "Transaction during visit #$visit->id"
                    ]);

                    // create inventory_visit relation for items that are "used"
                    if ($type == 'use') {
                        InventoryVisit::create([
                            'visit_id' => $visit->id,
                            'inventory_id' => $inventoryId,
                            'quantity_used' => $quantity,
                            'notes' => $note
                        ]);
                    }

                    // Update inventory quantity based on transaction type


                    if ($type == 'use' && $quantity > 0) {
                        if ($inventoryItem->quantity < $quantity) {
                            throw new \Exception("Not enough inventory for item: {$inventoryItem->name}");
                        }
                        $inventoryItem->decrement('quantity', $quantity);
                    }



                    // switch ($type) {
                    //     case 'purchase':
                    //         $inventoryItem->increment('quantity', $quantity);
                    //         break;
                    //     case 'use':
                    //     case 'return':
                    //         // Verify there's enough quantity before decrementing
                    //         if ($inventoryItem->quantity < $quantity) {
                    //             throw new \Exception("Not enough inventory for item: {$inventoryItem->name}");
                    //         }
                    //         $inventoryItem->decrement('quantity', $quantity);
                    //         break;
                    //     case 'adjustment':
                    //         // For adjustment, directly set the quantity
                    //         $inventoryItem->update(['quantity' => $quantity]);
                    //         break;
                    // }
                }
            }

            DB::commit();

            return redirect()->route('dashboard.visits.index')
                ->with('success', 'Visit Added Successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to add visit: ' . $e->getMessage())
                ->withInput();
        }
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
    public function edit(Visit $visit)
    {

        $appointments = Appointment::all();
        $services = Service::all();
        $patients = Patient::all();
        $staff = Staff::with('user')->get();
        return view('dashboard.visits.edit',compact( 'visit', 'appointments', 'services', 'patients', 'staff'));
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
        'status' => 'required|in:scheduled,walk_in,completed,rescheduled,canceled'
    ]);

    DB::beginTransaction();

    try {
        $appointment = Appointment::findOrFail($request->appointment_id);

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

        $appointment->update([
            'status' => $request->status
        ]);

        DB::commit();

        return redirect()->route('dashboard.visits.index')->with('success', 'Visit Updated Successfully');

    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->with('error', 'Failed to update visit: ' . $e->getMessage())->withInput();
    }
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Visit $visit)
    {
        // dd('s');
        try {

            $LinkedPayment = Payment::where('visit_id', $visit->id)->first();
                if ($LinkedPayment) {
                    return redirect()->back()->with('error', 'Cannot delete visit with linked Payment');
                }

            $visit->delete();
            return redirect()
                    ->route('dashboard.visits.index')
                    ->with('success', 'visit deleted successfully.');

        } catch (\Exception $e) {
            // dd('d');
            DB::rollBack();
            dd($e->getMessage());
        }
    }
}





