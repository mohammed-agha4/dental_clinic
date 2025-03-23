<?php

namespace App\Http\Controllers\Dashboard\Payment;

use App\Models\Staff;
use App\Models\Visit;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class PaymentsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Payment::with(['visit.patient', 'staff']);

        // Filter by status if provided
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by payment method if provided
        if ($request->has('method')) {
            $query->where('method', $request->method);
        }

        // Filter by date range if provided
        if ($request->has('from_date') && $request->has('to_date')) {
            $query->whereBetween('created_at', [$request->from_date, $request->to_date]);
        }

        $payments = $query->latest()->paginate(10);

        return view('dashboard.Payments.index', compact('payments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        $visits = Visit::with(['patient', 'service'])
        // ->whereDoesntHave('payments', function($query) {
        //     $query->where('status', 'completed');
        // })
        ->latest()
        ->get();

        // dd('d');
        $staff = Staff::where('is_active', true)->with('user')->get();
        $paymentMethods = ['cash', 'credit_card', 'bank_transfer'];

        return view('dashboard.payments.create', compact('visits', 'staff', 'paymentMethods'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $validated = $request->validate([
            'visit_id' => 'required|exists:visits,id',
            'staff_id' => 'required|exists:staff,id',
            'amount' => 'required|numeric|min:0',
            'method' => 'required|in:cash,credit_card,bank_transfer',
            'transaction_id' => 'nullable|string|max:255',
            'status' => 'required|in:pending,completed,failed',
            'notes' => 'nullable|string',
        ]);


        try {
            DB::beginTransaction();

            // Create payment
            // dd($request->all());
            $payment = Payment::create($validated);
            // If payment is completed, update the visit status or other related records if needed
            if ($payment->status === 'completed') {
                // Additional business logic if needed
            }

            DB::commit();

            return redirect()->route('dashboard.payments.show', $payment)
                ->with('success', 'Payment recorded successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            dd('error', 'Error recording payment: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Payment $payment)
    {
        $payment->load(['visit.patient', 'visit.service', 'staff']);
        return view('dashboard.payments.show', compact('payment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Payment $payment)
    {
        $payment->load('visit.patient', 'staff');
        $staff = Staff::where('is_active', true)->with('user')->get();
        $paymentMethods = ['cash', 'credit_card', 'bank_transfer'];
        $statuses = ['pending', 'completed', 'failed'];

        return view('dashboard.payments.edit', compact('payment', 'staff', 'paymentMethods', 'statuses'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Payment $payment)
    {
        $validated = $request->validate([
            'staff_id' => 'required|exists:staff,id',
            'amount' => 'required|numeric|min:0',
            'method' => 'required|in:cash,credit_card,bank_transfer',
            'transaction_id' => 'nullable|string|max:255',
            'status' => 'required|in:pending,completed,failed',
            'notes' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            // Store old status to check if it changed
            $oldStatus = $payment->status;

            // Update payment
            $payment->update($validated);

            // If payment status changed to completed, update related records if needed
            if ($oldStatus !== 'completed' && $payment->status === 'completed') {
                // Additional business logic if needed
            }

            DB::commit();

            return redirect()->route('dashboard.payments.show', $payment)
                ->with('success', 'Payment updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error updating payment: ' . $e->getMessage())->withInput();
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Payment $payment)
    {
        try {
            DB::beginTransaction();

            // Delete the payment
            $payment->delete();

            DB::commit();

            return redirect()->route('dashboard.payments.index')
                ->with('success', 'Payment deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error deleting payment: ' . $e->getMessage());
        }
    }
}
