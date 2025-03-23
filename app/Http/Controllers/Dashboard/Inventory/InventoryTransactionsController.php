<?php

namespace App\Http\Controllers\Dashboard\Inventory;

use App\Models\Staff;
use App\Models\Inventory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\InventoryTransaction;

class InventoryTransactionsController extends Controller
{
    //
    public function index()
    {
        // dd('d');
        $inventory_transactions = InventoryTransaction::with([ 'inventory', 'staff.user'])->latest('id')->paginate(8);
        return view('dashboard.inventory.inventory_transactions.index', compact('inventory_transactions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $inventory = Inventory::select('name','id')->get();
        $staff = Staff::with('user')->get();
        $inventory_transaction = new InventoryTransaction();
        return view('dashboard.inventory.inventory_transactions.create', compact('inventory', 'staff', 'inventory_transaction'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([

            'inventory_id' => 'nullable|exists:inventories,id',
            'staff_id' => 'nullable|exists:staff,id',
            'type' => 'required|in:purchase,use,adjustment,return',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
            'transaction_date' => 'required|date',
            'notes' => 'nullable|string',



        ]);

        $inventory_transaction = InventoryTransaction::create($request->all()); // doesn't need save
            return redirect()->route('dashboard.inventory.inventory-transactions.index')->with('success', 'Transaction Added Successfuly');
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
    public function edit(string $id)
    {
        // dd($id);
        $inventory_transaction = InventoryTransaction::findOrFail($id);
        // $product = Product::find($id);
        // dd($inventory_transaction);

        $inventory = Inventory::select('name','id')->get();
        $staff = Staff::with('user')->get();
        return view('dashboard.inventory.inventory_transactions.edit', compact('inventory_transaction', 'inventory', 'staff'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
{
    // Validate the request data
    $request->validate([
        'inventory_id' => 'required|exists:inventories,id',
        'staff_id' => 'required|exists:staff,id',
        'type' => 'required|in:purchase,use,adjustment,return',
        'quantity' => 'required|integer|min:1',
        'unit_price' => 'required|numeric|min:0',
        'transaction_date' => 'required|date',
        'notes' => 'nullable|string',
    ]);

    // Find the transaction by ID
    $inventory_transaction = InventoryTransaction::findOrFail($id);

    // Update the transaction
    $inventory_transaction->update([
        'inventory_id' => $request->inventory_id,
        'staff_id' => $request->staff_id,
        'type' => 'adjustment', // Set the type to 'adjustment'
        'quantity' => $request->quantity,
        'unit_price' => $request->unit_price,
        'transaction_date' => $request->transaction_date,
        'notes' => $request->notes,
    ]);

    return redirect()->route('dashboard.inventory.inventory-transactions.index')->with('success', 'Transaction Updated Successfully');
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $inventory_transaction = InventoryTransaction::findOrFail($id);
        $inventory_transaction->delete();
        return redirect()->route('dashboard.inventory.inventory-transactions.index')->with('success', 'Transaction Deleted Successfuly');

    }
}
