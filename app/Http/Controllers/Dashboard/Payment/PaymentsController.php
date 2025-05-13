<?php

namespace App\Http\Controllers\Dashboard\Payment;

use Carbon\Carbon;
use App\Models\Staff;
use App\Models\Visit;
use App\Models\Payment;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Support\Facades\Gate;

class PaymentsController extends Controller
{

    public function index(Request $request)
    {
        Gate::authorize('payments.view');


        $query = Payment::with(['visit.patient', 'staff.user']);

        if (
            auth()->user()->hasAbility('view-own-payments') &&
            !auth()->user()->hasAbility('view-all-payments')
        ) {
            $query->whereHas('visit', function ($q) {
                $q->where('staff_id', auth()->user()->staff->id);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('method')) {
            $query->where('method', $request->method);
        }

        if ($request->filled('from_date')) {
            dd($request->from_date);
            $query->where('created_at', '>=', Carbon::parse($request->from_date)->startOfDay());
        }

        if ($request->filled('to_date')) {
            $query->where('created_at', '<=', Carbon::parse($request->to_date)->endOfDay());
        }

        $payments = $query->latest()->paginate(8);

        return view('dashboard.payments.index', compact('payments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        Gate::authorize('payments.create');

        $visits = Visit::with(['patient', 'service', 'staff.user'])
            ->whereDoesntHave('payments', function ($query) {
                $query->where('status', 'completed');
            })->latest()->get();

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

        Gate::authorize('payments.create');
        // dd($request->all());
        $validated = $request->validate([
            'visit_id' => 'required|exists:visits,id',
            'staff_id' => 'required|exists:staff,id',
            'amount' => 'required|numeric|min:0',
            'method' => 'required|in:cash,credit_card,bank_transfer',
            'transaction_id' => 'nullable|string|unique:payments,transaction_id|max:255',
            'status' => 'required|in:pending,completed,failed',
            'notes' => 'nullable|string',
        ]);


        try {
            DB::beginTransaction();

            // dd($request->all());
            $payment = Payment::create($validated);

            if ($payment->status === 'completed') {
                $payment->visit->appointment->update(['status' => 'completed']);
            }

            DB::commit();

            return redirect()->route('dashboard.payments.index', $payment)
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
        Gate::authorize('payments.show');
        $payment->load(['visit.patient', 'visit.service', 'staff']);
        return view('dashboard.payments.show', compact('payment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Payment $payment)
    {
        Gate::authorize('payments.update');
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
        Gate::authorize('payments.update');
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
                $payment->visit->appointment->update(['status' => 'completed']);
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
        Gate::authorize('payments.delete');
        try {
            DB::beginTransaction();
            if ($payment->status == 'completed') {
                DB::rollBack();
                return redirect()->route('dashboard.payments.index')->with('error', 'Completed payments status cannot be deleted');
            }

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



    /**
     * Generate payment receipt
     */
    public function receipt(Payment $payment)
    {
        Gate::authorize('payments.show');

        $payment->load(['visit.patient', 'visit.service', 'staff.user']);

        return view('dashboard.payments.receipt', compact('payment'));
    }
}
