<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Staff;
use App\Models\Visit;
use App\Models\Expense;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\Service;
use App\Models\Inventory;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\InventoryTransaction;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $tomorrow = Carbon::tomorrow();
        $tomorrowStart = $tomorrow->copy()->startOfDay();
        $tomorrowEnd = $tomorrow->copy()->endOfDay();

        $today = Carbon::today();
        $todayStart = $today->copy()->startOfDay();
        $todayEnd = $today->copy()->endOfDay();

        $user = Auth::user();
        $role = $user->staff ? $user->staff->role : null;
        $staffId = $user->staff ? $user->staff->id : null;

        if ($role == 'dentist') {
            // Dentist-specific data
            $doctorAppointments = Appointment::where('staff_id', $staffId)
                ->where('appointment_date', '>=', Carbon::now())
                ->where('appointment_date', '<=', Carbon::now()->addDays(7))
                ->whereIn('status', ['scheduled', 'rescheduled'])
                ->with(['patient', 'service'])
                ->orderBy('appointment_date')
                ->take(5)
                ->get();

            $recentVisits = Visit::where('staff_id', $staffId)
                ->with(['patient', 'service'])
                ->orderBy('visit_date', 'desc')
                ->take(2)
                ->get();

            $currentMonth = Carbon::now()->startOfMonth();
            $patientsSeenThisMonth = Visit::where('staff_id', $staffId)
                ->where('visit_date', '>=', $currentMonth)
                ->distinct('patient_id')
                ->count('patient_id');

            $servicesPerformedCount = Visit::where('staff_id', $staffId)->count();

            $revenueGenerated = Payment::whereHas('visit', function ($query) use ($staffId) {
                $query->where('staff_id', $staffId);
            })
                ->where('status', 'completed')
                ->sum('amount');

            $todayAppointments = Appointment::where('staff_id', $staffId)
                ->whereBetween('appointment_date', [$todayStart, $todayEnd])
                ->with(['patient', 'service'])
                ->orderBy('appointment_date')
                ->take(2)
                ->get();

            return view('dashboard.index', compact(
                'role',
                'doctorAppointments',
                'recentVisits',
                'patientsSeenThisMonth',
                'servicesPerformedCount',
                'revenueGenerated',
                'todayAppointments'
            ));
        } else {
            // Admin/staff data
            $tomorrowAppointments = Appointment::with(['patient', 'service', 'dentist'])
                ->whereBetween('appointment_date', [$tomorrowStart, $tomorrowEnd])
                ->orderBy('appointment_date')
                ->get();

            $totalRevenue = Payment::where('status', 'completed')->sum('amount');
            $totalPatients = Patient::count();
            $totalDentists = Staff::where('role', 'dentist')->count();

            $recentPatients = Patient::withCount('appointments')
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();

            $lowStockItems = Inventory::where('quantity', '<', DB::raw('reorder_level'))
                ->where('is_active', true)
                ->orderBy('quantity')
                ->take(5)
                ->get();

            $expiringItems = Inventory::whereNotNull('expiry_date')
                ->where('expiry_date', '>', now())
                ->where('expiry_date', '<', now()->addDays(30))
                ->orderBy('expiry_date')
                ->take(5)
                ->get();

            $payment = number_format(Payment::where('status', 'completed')->sum('amount') ?? 0, 2);
            $patient_count = Patient::count() ?? 0;

            // Add profit calculation
            $profitData = $this->calculateProfit(
                request('period', 'month'),
                request('start_date'),
                request('end_date')
            );

            return view('dashboard.index', compact(
                'role',
                'tomorrowAppointments',
                'totalRevenue',
                'totalPatients',
                'totalDentists',
                'recentPatients',
                'lowStockItems',
                'expiringItems',
                'payment',
                'patient_count',
                'profitData' // Add this
            ));
        }
    }

    // Add this method to your DashboardController
    private function calculateProfit($timePeriod = 'month', $customStart = null, $customEnd = null)
    {
        // Determine date range
        switch ($timePeriod) {
            case 'day':
                $start = Carbon::today()->startOfDay();
                $end = Carbon::today()->endOfDay();
                break;
            case 'week':
                $start = Carbon::now()->startOfWeek();
                $end = Carbon::now()->endOfWeek();
                break;
            case 'month':
                $start = Carbon::now()->startOfMonth();
                $end = Carbon::now()->endOfMonth();
                break;
            case 'year':
                $start = Carbon::now()->startOfYear();
                $end = Carbon::now()->endOfYear();
                break;
            case 'custom':
                $start = $customStart ? Carbon::parse($customStart)->startOfDay() : Carbon::createFromDate(-9999, 1, 1)->startOfDay();
                $end = $customEnd ? Carbon::parse($customEnd)->endOfDay() : Carbon::createFromDate(9999, 12, 31)->endOfDay();
                break;
            default:
                $start = Carbon::now()->startOfMonth();
                $end = Carbon::now()->endOfMonth();
        }

        // 1. Calculate Total Revenue (completed payments)
        $revenue = Payment::where('status', 'completed')
            ->whereBetween('created_at', [$start, $end])
            ->sum('amount');

        // 2. Calculate Total Expenses
        $expenses = Expense::whereBetween('expense_date', [$start, $end])
            ->sum('amount');

        // 3. Calculate Cost of Goods Sold (COGS)
        $cogs = InventoryTransaction::where('type', 'use')
            ->whereBetween('transaction_date', [$start, $end])
            ->sum(DB::raw('quantity * unit_price'));

        // 4. Calculate Inventory Purchases (capital expenditure)
        $purchases = InventoryTransaction::where('type', 'purchase')
            ->whereBetween('transaction_date', [$start, $end])
            ->sum(DB::raw('quantity * unit_price'));

        // 5. Calculate Negative Adjustments (losses)
        $negativeAdjustments = InventoryTransaction::where('type', 'adjustment')
            ->where('quantity', '<', 0)
            ->whereBetween('transaction_date', [$start, $end])
            ->sum(DB::raw('ABS(quantity) * unit_price'));

        // 6. Calculate Returns (reductions in expenses)
        $returns = InventoryTransaction::where('type', 'return')
            ->whereBetween('transaction_date', [$start, $end])
            ->sum(DB::raw('quantity * unit_price'));

        // Net Profit Calculation
        $totalExpenses = $expenses + $cogs + $purchases + $negativeAdjustments - $returns;
        $profit = $revenue - $totalExpenses;

        return [
            'revenue' => $revenue,
            'expenses' => $expenses,
            'cogs' => $cogs,
            'purchases' => $purchases,
            'negativeAdjustments' => $negativeAdjustments,
            'returns' => $returns,
            'totalExpenses' => $totalExpenses,
            'profit' => $profit,
            'period' => [
                'start' => $start,
                'end' => $end,
                'label' => $this->getPeriodLabel($timePeriod, $start, $end)
            ]
        ];
    }

    private function getPeriodLabel($timePeriod, $start, $end)
    {
        switch ($timePeriod) {
            case 'day':
                return 'Today';
            case 'week':
                return 'This Week';
            case 'month':
                return 'This Month';
            case 'year':
                return 'This Year';
            case 'custom':
                return $start->format('M d, Y') . ' - ' . $end->format('M d, Y');
            default:
                return 'Current Period';
        }
    }
}
