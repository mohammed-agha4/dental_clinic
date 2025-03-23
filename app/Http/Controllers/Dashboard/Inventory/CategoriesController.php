<?php

namespace App\Http\Controllers\Dashboard\Inventory;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::latest('id')->paginate(8);
        return view('dashboard.inventory.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $category = new Category;
        return view('dashboard.inventory.categories.create', compact('category'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $request->validate([
            'name' => 'required|string',
        ]);

        $category = Category::create($request->all());
        return redirect()->route('dashboard.inventory.categories.index')->with('success', 'Category Created Successfuly');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        return view('dashboard.inventory.categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string',

        ]);

        $category->update($request->all());
        return redirect()->route('dashboard.inventory.categories.index')->with('success', 'Category updated Successfuly');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()
        ->route('dashboard.inventory.categories.index')
        ->with('success', 'Category deleted successfully.');
    }
}
