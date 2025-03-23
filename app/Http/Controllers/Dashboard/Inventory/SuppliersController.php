<?php

namespace App\Http\Controllers\Dashboard\Inventory;

use App\Models\Supplier;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SuppliersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $suppliers = Supplier::latest('id')->paginate(8);
        return view('dashboard.inventory.suppliers.index', compact('suppliers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $supplier = new Supplier;
        return view('dashboard.inventory.suppliers.create', compact('supplier'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $request->validate([
            'company_name' => 'required|string',
            'contact_name' => 'required|string',
            'email'=> 'required|email',
            'phone'=> 'required',
            'address'=> 'required',
        ]);

        $supplier = Supplier::create($request->all()); // doesn't need save
        return redirect()->route('dashboard.inventory.suppliers.index')->with('success', 'Supplier Added Successfuly');
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Supplier $supplier)
    {

        return view('dashboard.inventory.suppliers.edit', compact('supplier'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Supplier $supplier)
    {
        $request->validate([
            'company_name' => 'required',
            'contact_name' => 'required',
            'email'=> 'required',
            'phone'=> 'required',
            'address'=> 'required',
        ]);

        $supplier->update($request->all());
        return redirect()->route('dashboard.inventory.suppliers.index')->with('success', 'Supplier updated Successfuly');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Supplier $supplier)
    {
        $supplier->delete();
        return redirect()
        ->route('dashboard.inventory.suppliers.index')
        ->with('success', 'Supplier deleted successfully.');
    }
}
