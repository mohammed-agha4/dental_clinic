<?php

namespace App\Http\Controllers\Dashboard\Inventory;

use App\Models\Staff;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\InventoryTransaction;
use Illuminate\Support\Facades\Auth;

class InventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $inventory = Inventory::with(['supplier', 'category'])->latest('id')->paginate(8);
        return view('dashboard.inventory.inventory.index', compact('inventory'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::select('name','id')->get();
        $suppliers = Supplier::select('company_name','id')->get();
        $inventory = Inventory::where('is_active', true)->get();
        return view('dashboard.inventory.inventory.create', compact('categories', 'inventory', 'suppliers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{


    // $request->validate([
    //     'category_id' => 'required|exists:categories,id',
    //     'supplier_id' => 'required|exists:suppliers,id',
    //     'name' => 'required|string|max:255',
    //     'SKU' => 'required|string|max:255|unique:inventories,SKU',
    //     'description' => 'nullable|string',
    //     'quantity' => 'required|integer|min:1',
    //     'reorder_level' => 'required|integer|min:0',
    //     'unit_price' => 'required|numeric|min:1',
    //     'expiry_date' => 'nullable|date',
    //     'is_active' => 'required|in:0,1',
    //     'transaction_date' => 'required|date',
    //     'transaction_notes' => 'nullable|string',
    // ]);



    DB::beginTransaction();
    try {

        $inventory = Inventory::create([
            'category_id' => $request->category_id,
            'supplier_id' => $request->supplier_id,
            'name' => $request->name,
            'SKU' => $request->SKU,
            'description' => $request->description,
            'quantity' => $request->quantity,
            'reorder_level' => $request->reorder_level,
            'unit_price' => $request->unit_price,
            'expiry_date' => $request->expiry_date,
            'is_active' => $request->is_active,
        ]);


        // $inventory->checkReorderLevel(); // for the notifications

        $user = auth()->user();

        if ($user) {
            $staff = Staff::where('user_id', $user->id)->first();
        }

        if (!$staff) {
            \Log::warning('User ID ' . ($user ? $user->id : 'null') . ' attempted to create inventory but has no associated staff record.');
        }
        // dd($staff);
        InventoryTransaction::create([
            'inventory_id' => $inventory->id,
            'staff_id' => $staff ? $staff->id : null,
            'type' => 'purchase', // Since this is a new item, it's always a purchase
            'quantity' => $request->quantity,
            'unit_price' => $request->unit_price,
            'transaction_date' => $request->transaction_date,
            'notes' => $request->transaction_notes,
            // dd($request->transaction_date)
        ]);
        // dd();



        // Handle additional transactions if they exist
        // if ($request->has('transaction_type') && is_array($request->transaction_type)) {
        //     foreach ($request->transaction_type as $key => $type) {
        //         // Skip if this is an empty entry
        //         if (empty($type)) continue;

        //         InventoryTransaction::create([
        //             'inventory_id' => $inventory->id,
        //             'staff_id' => $staff ? $staff->id : null,
        //             'type' => $type,
        //             'quantity' => $request->transaction_quantity[$key] ?? $request->quantity,
        //             'unit_price' => $request->transaction_price[$key] ?? $request->unit_price,
        //             'transaction_date' => $request->transaction_date[$key] ?? now(),
        //             'notes' => $request->transaction_notes[$key] ?? null,
        //         ]);
        //     }
        // }

        DB::commit();

        return redirect()->route('dashboard.inventory.inventory.index')
            ->with('success', 'Tool Added Successfully');

    } catch (\Exception $e) {
        // DB::rollBack();
        dd('error', 'Failed to add tool: ' . $e->getMessage())
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
    public function edit(Inventory $inventory)
    {

        $categories = Category::select('name', 'id')->get();
        $suppliers = Supplier::select('company_name', 'id')->get();
        return view('dashboard.inventory.inventory.edit', compact('inventory', 'categories', 'suppliers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Inventory $inventory)
    {

        // $validated = $request->validate([
        //     'category_id' => 'nullable|exists:categories,id',
        //     'supplier_id' => 'nullable|exists:suppliers,id',
        //     'name' => 'required|string',
        //     'SKU' => 'required|string',
        //     'description' => 'nullable|string',
        //     'quantity' => 'required|integer|min:0',
        //     'reorder_level' => 'required|integer|min:0',
        //     'unit_price' => 'required|numeric|min:0',
        //     'expiry_date' => 'nullable|date',
        //     'is_active' => 'required|in:0,1',
        //     'transaction_date' => 'required|date',
        //     'transaction_notes' => 'nullable|string',
        // ]);
        // dd();


        $inventory->update($request->all());
        // for the notifications
        return redirect()->route('dashboard.inventory.inventory.index')->with('success', 'Tool nformation Updated Successfuly');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Inventory $inventory)
    {
        dd('v');
        $inventory->delete();
        return redirect()->route('dashboard.inventory.inventory.index')->with('success', 'Staff Deleted Successfuly');

    }
}
