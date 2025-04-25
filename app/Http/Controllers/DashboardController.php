<?php
namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Visit;
use App\Models\Patient;
use App\Models\Service;
use App\Models\Payment;
use App\Models\Inventory;
use App\Models\Appointment;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

            $revenueGenerated = Payment::whereHas('visit', function($query) use ($staffId) {
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
                'patient_count'
            ));
        }
    }
}
