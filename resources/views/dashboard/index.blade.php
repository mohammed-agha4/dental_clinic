@extends('layouts.master.master')
@section('title', 'Dashboard')
@section('css')
    <style>
        /* Base Styles */
        th {
            color: rgb(126, 121, 121) !important;
        }

        .cont {
            width: 95%;
            margin: 0 auto;
        }

        /* Metrics Cards */
        .metrics-container {
            margin-bottom: 1.5rem;
        }

        .parent {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
        }

        .div1 {
            width: 32%;
            height: 100px;
            border-radius: 0.5rem;
            color: white;
            display: flex;
            align-items: center;
            padding: 0 15px;
        }

        .icon-container {
            width: 60px;
            height: 60px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
        }

        .icon-container img {
            width: 45px;
            height: auto;
        }

        .last-icon {
            width: 60px !important;
        }

        /* Scrollable Sections */
        .scrollable-card-body {
            height: 300px;
            overflow-y: auto;
        }

        .scrollable-table-container {
            max-height: 400px;
            overflow-y: auto;
        }

        .centered-content {
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-thumb {
            background-color: rgba(0, 0, 0, 0.2);
            border-radius: 3px;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        @if ($role == 'dentist')
            {{-- DENTIST DASHBOARD --}}
            <div class="row">
                <div class="col-lg-8">
                    <!-- Metrics Cards -->
                    <div class="metrics-container">
                        <div class="parent">
                            <div class="div1 text-light" style="background-color: rgb(25, 26, 90);">
                                <div class="icon-container">
                                    <img class="last-icon" src="{{ asset('front/assets/icons/noun-patient-visit.png') }}"
                                        alt="Patients Icon">
                                </div>
                                <div>
                                    <small>Patients This Month</small> <br>
                                    <span>{{ $patientsSeenThisMonth ?? 0 }}</span>
                                </div>
                            </div>

                            <div class="div1 text-light" style="background-color: rgb(72, 25, 90);">
                                <div class="icon-container">
                                    <img src="{{ asset('front/assets/icons/Tooth.png') }}" alt="Service Icon">
                                </div>
                                <div>
                                    <small>Services Performed</small> <br>
                                    <span>{{ $servicesPerformedCount ?? 0 }}</span>
                                </div>
                            </div>

                            <div class="div1 text-light" style="background-color: rgb(11, 74, 51);">
                                <div class="icon-container">
                                    <img src="{{ asset('front/assets/icons/money-bag-9.svg') }}" alt="Revenue Icon">
                                </div>
                                <div>
                                    <small>Revenue Generated</small> <br>
                                    <span>${{ number_format($revenueGenerated ?? 0, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Upcoming Appointments -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">My Upcoming Appointments</h5>
                        </div>
                        <div class="card-body p-0 scrollable-table-container">
                            @if (isset($doctorAppointments) && count($doctorAppointments) > 0)
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>Patient</th>
                                            <th>Service</th>
                                            <th>Date & Time</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($doctorAppointments as $appointment)
                                            <tr>
                                                <td>{{ $appointment->patient->fname }} {{ $appointment->patient->lname }}</td>
                                                <td>{{ $appointment->service->service_name ?? 'N/A' }}</td>
                                                <td>{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('M d, Y h:i A') }}</td>
                                                <td>{{$appointment->status}}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <div class="text-center py-4 centered-content">
                                    <i class="fas fa-calendar-check fa-3x text-muted mb-3"></i>
                                    <p>No upcoming appointments scheduled.</p>
                                </div>
                            @endif
                        </div>
                        <div class="card-footer">
                            <a href="{{ route('dashboard.appointments.index') }}"
                                class="btn btn-sm btn-outline-primary">View All</a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <a href="{{ route('dashboard.appointments.create') }}" class="btn text-light mb-4 "
                        style="background-color: rgb(11, 74, 51);"><i class="fa-solid fa-clock-rotate-left"></i> Schedule Appointment</a>

                    <!-- Today's Appointments -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Today's Schedule</h5>
                        </div>
                        <div class="card-body p-0 scrollable-card-body">
                            @if (isset($todayAppointments) && count($todayAppointments) > 0)
                                <table class="table table-hover mb-0">
                                    <tbody>
                                        @foreach ($todayAppointments as $appointment)
                                            <tr>
                                                <td>
                                                    <strong>{{ $appointment->patient->fname }} {{ $appointment->patient->lname }}</strong><br>
                                                    <small>{{ $appointment->service->service_name ?? 'N/A' }}</small><br>
                                                    <small>{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('h:i A') }}</small>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <div class="text-center py-4 centered-content">
                                    <i class="fas fa-calendar-day fa-3x text-muted mb-3"></i>
                                    <p>No appointments today.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Recent Visits -->
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Recent Patient Visits</h5>
                            <a href="{{ route('dashboard.visits.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                        </div>
                        <div class="card-body p-0 scrollable-card-body">
                            @if (isset($recentVisits) && count($recentVisits) > 0)
                                <table class="table table-hover mb-0">
                                    <tbody>
                                        @foreach ($recentVisits as $visit)
                                            <tr>
                                                <td>
                                                    <strong>{{ $visit->patient->fname }} {{ $visit->patient->lname }}</strong><br>
                                                    <small>{{ $visit->service->service_name ?? 'N/A' }}</small><br>
                                                    <small>{{ \Carbon\Carbon::parse($visit->visit_date)->format('M d, Y') }}</small>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <div class="text-center py-4 centered-content">
                                    <i class="fas fa-notes-medical fa-3x text-muted mb-3"></i>
                                    <p>No recent visits found.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @else
            {{-- ADMIN DASHBOARD --}}
            <div class="row">
                <div class="col-lg-8">
                    <!-- Metrics Cards -->
                    <div class="metrics-container">
                        <div class="parent">
                            <div class="div1 text-light" style="background-color: rgb(11, 74, 51);">
                                <div class="icon-container">
                                    <img src="{{ asset('front/assets/icons/money-bag-9.svg') }}" alt="Revenue Icon">
                                </div>
                                <div>
                                    <small>Total Revenue</small> <br>
                                    <span>${{ $payment }}</span>
                                </div>
                            </div>

                            <div class="div1 text-light" style="background-color: rgb(72, 25, 90);">
                                <div class="icon-container">
                                    <img src="{{ asset('front/assets/icons/Tooth.png') }}" alt="Patient Icon">
                                </div>
                                <div>
                                    <small>Total Patients</small> <br>
                                    <span>{{ $patient_count }}</span>
                                </div>
                            </div>

                            <div class="div1 text-light" style="background-color: rgb(25, 26, 90);">
                                <div class="icon-container">
                                    <img class="last-icon" src="{{ asset('front/assets/icons/noun-doctor-7015784.svg') }}"
                                        alt="Doctor Icon">
                                </div>
                                <div>
                                    <small>Total Dentists</small> <br>
                                    <span>{{ \App\Models\Staff::where('role', 'dentist')->count() ?? 0 }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Upcoming Appointments -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Tomorrow's Appointments</h5>
                        </div>
                        <div class="card-body p-0 scrollable-table-container">
                            @if (isset($tomorrowAppointments) && count($tomorrowAppointments) > 0)
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>Patient</th>
                                            <th>Service</th>
                                            <th>Time</th>
                                            <th>Dentist</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($tomorrowAppointments as $appointment)
                                            <tr>
                                                <td>{{ $appointment->patient->fname }} {{ $appointment->patient->lname }}</td>
                                                <td>{{ $appointment->service->service_name ?? 'N/A' }}</td>
                                                <td>{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('h:i A') }}</td>
                                                <td>{{ $appointment->dentist->user->name ?? 'N/A' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <div class="text-center py-4 centered-content">
                                    <i class="fas fa-calendar-check fa-3x text-muted mb-3"></i>
                                    <p>No appointments scheduled for tomorrow.</p>
                                </div>
                            @endif
                        </div>
                        <div class="card-footer">
                            <a href="{{ route('dashboard.appointments.index') }}"
                                class="btn btn-sm btn-outline-primary">View All</a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <!-- Recent Patients -->
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Recent Patients</h5>
                            <a href="{{ route('dashboard.patients.index') }}"
                                class="btn btn-sm btn-outline-primary">View All</a>
                        </div>
                        <div class="card-body p-0 scrollable-card-body">
                            @if (isset($recentPatients) && count($recentPatients) > 0)
                                <table class="table table-hover mb-0">
                                    <tbody>
                                        @foreach ($recentPatients as $patient)
                                            <tr>
                                                <td>
                                                    <strong>{{ $patient->fname }} {{ $patient->lname }}</strong><br>
                                                    <small>{{ $patient->phone }}</small><br>
                                                    <small>{{ $patient->appointments_count }} appointments</small>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <div class="text-center py-4 centered-content">
                                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                    <p>No recent patients found.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Inventory Alerts -->
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Low Stock Items</h5>
                            <a href="{{ route('dashboard.inventory.inventory.index') }}"
                                class="btn btn-sm btn-outline-primary">View All</a>
                        </div>
                        <div class="card-body p-0 scrollable-card-body">
                            @if (isset($lowStockItems) && count($lowStockItems) > 0)
                                <table class="table table-hover mb-0">
                                    <tbody>
                                        @foreach ($lowStockItems as $item)
                                            <tr>
                                                <td>
                                                    <strong>{{ $item->name }}</strong><br>
                                                    <small>Stock: {{ $item->quantity }}</small><br>
                                                    <small>Reorder at: {{ $item->reorder_level }}</small>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <div class="text-center py-4 centered-content">
                                    <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                                    <p>All inventory items are well stocked.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize tooltips if needed
            const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
            tooltips.forEach(tooltip => {
                new bootstrap.Tooltip(tooltip);
            });
        });
    </script>
@endpush
