<?php

namespace App\Http\Controllers\Dashboard\Payment;

use Exception;
use App\Models\Staff;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class ExpensesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $staffMembers = Staff::with('user')->where('is_active', true)->get();
        $categories = Expense::CATEGORIES;

        $expenses = Expense::with('staff.user')
            ->dateRange($request->start_date, $request->end_date)
            ->category($request->category)
            ->byStaff($request->staff_id)
            ->latest()
            ->paginate(8);

        // Calculate total for filtered expenses
        $totalAmount = Expense::dateRange($request->start_date, $request->end_date)
            ->category($request->category)
            ->byStaff($request->staff_id)
            ->sum('amount');

        return view('dashboard.expenses.index', compact(
            'expenses',
            'staffMembers',
            'categories',
            'totalAmount'
        ));
    }

    /**
     * Show the form for creating a new expense.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $staffMembers = Staff::with('user')->where('is_active', true)->get();
        $categories = Expense::CATEGORIES;

        return view('dashboard.expenses.create', compact('staffMembers', 'categories'));
    }

    /**
     * Store a newly created expense in storage.
     *
     * @param  \App\Http\Requests\StoreExpenseRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

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

    /**
     * Display the specified expense.
     *
     * @param  \App\Models\Expense  $expense
     * @return \Illuminate\Http\Response
     */
    public function show(Expense $expense)
    {
        $expense->load('staff.user');

        return view('dashboard.expenses.show', compact('expense'));
    }

    /**
     * Show the form for editing the specified expense.
     *
     * @param  \App\Models\Expense  $expense
     * @return \Illuminate\Http\Response
     */
    public function edit(Expense $expense)
    {
        $staffMembers = Staff::with('user')->where('is_active', true)->get();
        $categories = Expense::CATEGORIES;

        return view('dashboard.expenses.edit', compact('expense', 'staffMembers', 'categories'));
    }

    /**
     * Update the specified expense in storage.
     *
     * @param  \App\Http\Requests\UpdateExpenseRequest  $request
     * @param  \App\Models\Expense  $expense
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Expense $expense)
    {

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
        $expense->delete();

        return redirect()->route('dashboard.expenses.index')
            ->with('success', 'Expense deleted successfully');
    }

    /**
     * Generate expense report
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function report(Request $request)
    {
        // dd('dd');
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $expenses = Expense::with('staff.user')
            ->dateRange($request->start_date, $request->end_date)
            ->category($request->category)
            ->byStaff($request->staff_id)
            ->get();

        $totalAmount = $expenses->sum('amount');
        $categoryTotals = $expenses->groupBy('category')
            ->map(function ($group) {
                return [
                    'count' => $group->count(),
                    'total' => $group->sum('amount')
                ];
            });

        return view('expenses.report', compact(
            'expenses',
            'totalAmount',
            'categoryTotals'
        ));
    }


    public function trash()
    {
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
