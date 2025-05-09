<?php

namespace App\Http\Controllers\Dashboard\Inventory;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Database\QueryException;

class SuppliersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Gate::authorize('suppliers.view');

        $suppliers = Supplier::when(request('search'), function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('company_name', 'like', "%{$search}%")
                    ->orWhere('contact_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%");
            });
        })->latest('id')->paginate(8);


        return view('dashboard.inventory.suppliers.index', compact('suppliers'));
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        Gate::authorize('suppliers.create');
        $supplier = new Supplier;
        return view('dashboard.inventory.suppliers.create', compact('supplier'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Gate::authorize('suppliers.create');

        try {

            $validated = $request->validate([
                'company_name' => 'required|string|max:255',
                'contact_name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:suppliers,email',
                'phone' => [
                    'required',
                    'string',
                    'max:20',
                    'unique:suppliers,phone',
                    'regex:/^\+?[0-9\s\-\(\)]{7,20}$/'
                ],
                'address' => 'nullable|string|max:500',
            ]);

            Supplier::create($validated);
            return redirect()->route('dashboard.inventory.suppliers.index')->with('success', 'Supplier Added Successfuly');
        } catch (QueryException $e) {
            return redirect()->route('dashboard.inventory.suppliers.index')->with('error', 'can\'t add supplier, something wrong');
        }
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Supplier $supplier)
    {
        Gate::authorize('suppliers.update');

        return view('dashboard.inventory.suppliers.edit', compact('supplier'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Supplier $supplier)
    {
        Gate::authorize('suppliers.update');

        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'contact_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:suppliers,email,' . $supplier->id,
            'phone' => [
                'required',
                'string',
                'max:20',
                Rule::unique('suppliers')->ignore($supplier->id),
                'regex:/^\+?[0-9\s\-\(\)]{7,20}$/'
            ],
            'address' => 'nullable|string|max:500',
        ]);

        $supplier->update($validated);
        return redirect()->route('dashboard.inventory.suppliers.index')->with('success', 'Supplier updated Successfuly');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Supplier $supplier)
    {
        Gate::authorize('suppliers.delete');
        $supplier->delete();
        return redirect()
            ->route('dashboard.inventory.suppliers.index')
            ->with('success', 'Supplier deleted successfully.');
    }
}
