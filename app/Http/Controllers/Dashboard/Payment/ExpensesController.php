<?php

namespace App\Http\Controllers\Dashboard\Payment;

use Exception;
use App\Models\Staff;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;

class ExpensesController extends Controller
{



    public function index(Request $request)
    {
        // dd($request->all());
        Gate::authorize('expenses.view');
        $staffMembers = Staff::with('user')->where('is_active', true)->get();
        $categories = Expense::CATEGORIES;

        // Base query
        $query = Expense::with('staff.user');

        // Date range filter
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('expense_date', [$request->start_date, $request->end_date]);
        }

        // Category filter
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Staff filter
        if ($request->filled('staff_id')) {
            $query->where('staff_id', $request->staff_id);
        }

        // Get paginated results
        $expenses = $query->latest()->paginate(8);

        // Calculate total amount with same filters
        $totalQuery = Expense::query();

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $totalQuery->whereBetween('expense_date', [$request->start_date, $request->end_date]);
        }

        if ($request->filled('category')) {
            $totalQuery->where('category', $request->category);
        }

        if ($request->filled('staff_id')) {
            $totalQuery->where('staff_id', $request->staff_id);
        }

        $totalAmount = $totalQuery->sum('amount');

        return view('dashboard.expenses.index', compact(
            'expenses',
            'staffMembers',
            'categories',
            'totalAmount'
        ));
    }




    public function create()
    {
        Gate::authorize('expenses.create');
        $staffMembers = Staff::with('user')->where('is_active', true)->get();
        $categories = Expense::CATEGORIES;

        return view('dashboard.expenses.create', compact('staffMembers', 'categories'));
    }




    public function store(Request $request)
    {
        Gate::authorize('expenses.create');

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'staff_id' => 'required|exists:staff,id',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0.01',
            'category' => 'required|in:' . implode(',', array_keys(Expense::CATEGORIES)),
            'expense_date' => 'required|date|before_or_equal:today',
        ]);

        Expense::create($validated);

        return redirect()->route('dashboard.expenses.index')
            ->with('success', 'Expense created successfully.');
    }




    public function show(Expense $expense)
    {
        Gate::authorize('expenses.show');
        $expense->load('staff.user');

        return view('dashboard.expenses.show', compact('expense'));
    }




    public function edit(Expense $expense)
    {
        Gate::authorize('expenses.update');
        $staffMembers = Staff::with('user')->where('is_active', true)->get();
        $categories = Expense::CATEGORIES;

        return view('dashboard.expenses.edit', compact('expense', 'staffMembers', 'categories'));
    }



    public function update(Request $request, Expense $expense)
    {
        Gate::authorize('expenses.update');

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'staff_id' => 'required|exists:staff,id',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0.01',
            'category' => 'required|in:' . implode(',', array_keys(Expense::CATEGORIES)),
            'expense_date' => 'required|date|before_or_equal:today',
        ]);


        $expense->update($validated);

        return redirect()->route('dashboard.expenses.index')
            ->with('success', 'Expense updated successfully');
    }

    /**
     * Remove the specified expense from storage.
     *
     * @param  \App\Models\Expense  $expense
     * @return \Illuminate\Http\Response
     */
    public function destroy(Expense $expense)
    {
        Gate::authorize('expenses.delete');
        $expense->delete();

        return redirect()->route('dashboard.expenses.index')
            ->with('success', 'Expense deleted successfully');
    }




    public function trash()
    {
        Gate::authorize('expenses.trash');
        $expenses = Expense::onlyTrashed()
            ->with(['staff.user'])
            ->latest('deleted_at')
            ->paginate(10);

        return view('dashboard.expenses.trash', compact('expenses'));
    }

    /**
     * Restore a soft-deleted expense
     */
    public function restore($id)
    {
        Gate::authorize('expenses.restore');
        try {
            DB::beginTransaction();

            $expense = Expense::onlyTrashed()->findOrFail($id);
            $expense->restore();

            DB::commit();

            return redirect()->route('dashboard.expenses.trash')
                ->with('success', 'Expense restored successfully');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->route('dashboard.expenses.trash')
                ->with('error', 'Error restoring expense: ' . $e->getMessage());
        }
    }

    /**
     * Permanently delete an expense
     */
    public function forceDelete($id)
    {
        Gate::authorize('expenses.force_delete');
        try {
            DB::beginTransaction();

            $expense = Expense::onlyTrashed()->findOrFail($id);
            $expense->forceDelete();

            DB::commit();

            return redirect()->route('dashboard.expenses.trash')
                ->with('success', 'Expense permanently deleted');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->route('dashboard.expenses.trash')
                ->with('error', 'Permanent deletion failed: ' . $e->getMessage());
        }
    }
}
