<?php

namespace App\Http\Controllers\Dashboard\Inventory;

use Exception;
use App\Models\Staff;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\InventoryTransaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class InventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Gate::authorize('inventory.view');

        $query = Inventory::with(['supplier', 'category']);

        if ($search = request('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('SKU', 'like', "%{$search}%")
                    ->orWhereHas('category', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('supplier', function ($q) use ($search) {
                        $q->where('company_name', 'like', "%{$search}%");
                    });
            });
        }

        if ($status = request('status')) {
            if ($status === 'active') {
                $query->where('is_active', true);
            } elseif ($status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        if ($stock = request('stock')) {
            if ($stock === 'low') {
                $query->whereColumn('quantity', '<=', 'reorder_level');
            } elseif ($stock === 'out') {
                $query->where('quantity', 0);
            }
        }

        $inventory = $query->latest('id')->paginate(8);

        return view('dashboard.inventory.inventory.index', compact('inventory'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        Gate::authorize('inventory.create');
        $categories = Category::select('name', 'id')->get();
        $suppliers = Supplier::select('company_name', 'id')->get();
        $inventory = Inventory::where('is_active', true)->get();
        return view('dashboard.inventory.inventory.create', compact('categories', 'inventory', 'suppliers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Gate::authorize('inventory.update');

        $request->validate([
            'category_id' => 'exists:categories,id',
            'supplier_id' => 'exists:suppliers,id',
            'name' => 'required|string|max:255',
            'SKU' => 'required|string|max:255|unique:inventories,SKU',
            'description' => 'nullable|string',
            'quantity' => 'required|integer|min:1',
            'reorder_level' => 'required|integer|min:0',
            'unit_price' => 'required|numeric|min:1',
            'expiry_date' => 'nullable|date',
            'is_active' => 'required|in:0,1',
            'transaction_date' => 'required|date',
            'transaction_notes' => 'nullable|string',
        ]);

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

            $user = auth()->user();

            if ($user) {
                $staff = Staff::where('user_id', $user->id)->first();
            }

            if (!$staff) {
                Log::warning('User ID ' . ($user ? $user->id : 'null') . ' attempted to create inventory but has no associated staff record.');
            }

            InventoryTransaction::create([
                'inventory_id' => $inventory->id,
                'staff_id' => $staff ? $staff->id : null,
                'type' => 'purchase',
                'quantity' => $request->quantity,
                'unit_price' => $request->unit_price,
                'transaction_date' => $request->transaction_date,
                'notes' => $request->transaction_notes,
            ]);


            DB::commit();

            return redirect()->route('dashboard.inventory.inventory.index')
                ->with('success', 'Tool Added Successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to add tool: ' . $e->getMessage())
                ->withInput(); // to refill the form again with the data if error happened
        }
    }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        Gate::authorize('inventory.show');
        $inventory = Inventory::with(['Supplier', 'Category'])
            ->findOrFail($id);

        return view('dashboard.inventory.inventory.show', compact('inventory'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Inventory $inventory)
    {
        Gate::authorize('inventory.update');

        $categories = Category::select('name', 'id')->get();
        $suppliers = Supplier::select('company_name', 'id')->get();
        return view('dashboard.inventory.inventory.edit', compact('inventory', 'categories', 'suppliers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Inventory $inventory)
    {
        Gate::authorize('inventory.update');

        $validated = $request->validate([
            'category_id' => 'nullable|exists:categories,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'name' => 'required|string',
            'SKU' => 'required|string',
            'description' => 'nullable|string',
            'quantity' => 'required|integer|min:0',
            'reorder_level' => 'required|integer|min:0',
            'unit_price' => 'required|numeric|min:0',
            'expiry_date' => 'nullable|date',
            'is_active' => 'required|in:0,1',

        ]);

        $inventory->update($validated);
        return redirect()->route('dashboard.inventory.inventory.index')->with('success', 'Tool Information Updated Successfuly');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Inventory $inventory)
    {
        Gate::authorize('inventory.delete');

        if ($inventory->transactions()->exists()) {

            return redirect()->route('dashboard.inventory.inventory.index')
                ->with('error', "can't delete item with existing transactions");
        }
        $inventory->delete();
        return redirect()->route('dashboard.inventory.inventory.index')->with('success', 'Tool Deleted Successfuly');
    }


    public function trash()
    {
        Gate::authorize('inventory.trash');

        $inventories = Inventory::onlyTrashed()
            ->with(['category', 'supplier'])
            ->latest('deleted_at')
            ->paginate(8);
        return view('dashboard.inventory.inventory.trash', compact('inventories'));
    }

    /**
     * Restore a soft-deleted inventory item
     */
    public function restore($id)
    {
        Gate::authorize('inventory.restore');
        try {
            DB::beginTransaction();
            $inventory = Inventory::onlyTrashed()->findOrFail($id);
            $inventory->restore();

            DB::commit();

            return redirect()->route('dashboard.inventory.inventory.trash')
                ->with('success', 'Inventory item restored successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('dashboard.inventory.inventory.trash')
                ->with('error', 'Error restoring inventory: ' . $e->getMessage());
        }
    }

    /**
     * Permanently delete an inventory item
     */
    public function forceDelete($id)
    {
        Gate::authorize('inventory.force_delete');

        try {
            DB::beginTransaction();

            $inventory = Inventory::onlyTrashed()
                ->with(['transactions', 'visits'])
                ->findOrFail($id);

            $itemName = $inventory->name;

            if ($inventory->transactions()->exists()) {
                return redirect()->back()
                    ->with('error', 'Cannot delete: Item has associated transaction records.');
            }

            if ($inventory->visits()->exists()) {
                return redirect()->back()
                    ->with('error', 'Cannot delete: Item was used in patient visits.');
            }

            $inventory->forceDelete();

            DB::commit();

            return redirect()->back()
                ->with('success', "Inventory item '{$itemName}' permanently deleted");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Permanent deletion failed: ' . $e->getMessage());
        }
    }
}
