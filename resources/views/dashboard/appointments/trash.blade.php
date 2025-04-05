@extends('layouts.master.master')

@section('title', 'Trashed Appointments')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light d-flex justify-content-between align-items-center py-3">
            <h4 class="mb-0">Trashed Appointments</h4>
            <a href="{{ route('dashboard.appointments.index') }}" class="btn btn-dark btn-sm">
                <i class="fas fa-arrow-left fa-sm"></i> Back to Main
            </a>
        </div>

        <div class="card-body">
            @if (session()->has('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if (session()->has('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-striped table-hover small">
                    <thead class="table-light">
                        <tr class="text-center">
                            <th>ID</th>
                            <th>Patient</th>
                            <th>Staff</th>
                            <th>Service</th>
                            <th>Appointment Date</th>
                            <th>Duration</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($appointments as $appointment)
                            <tr class="text-center">
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $appointment->patient->fname }} {{ $appointment->patient->lname }}</td>
                                <td>{{ $appointment->dentist->user->name ?? 'N/A' }}</td>
                                <td>{{ $appointment->service->service_name }}</td>
                                <td>{{ $appointment->appointment_date}}</td>
                                <td>{{ $appointment->duration }} minutes</td>
                                <td>{{ $appointment->status}} </td>
                                <td>
                                    <form action="{{ route('dashboard.appointments.restore', $appointment->id) }}" method="post" class="d-inline">
                                        @method('PUT')
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-trash-restore"></i> Restore
                                        </button>
                                    </form>

                                    <form action="{{ route('dashboard.appointments.force-delete', $appointment->id) }}" method="post" class="d-inline">
                                        @method('DELETE')
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to permanently delete this appointment?')">
                                            <i class="fas fa-trash-alt"></i> Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">No trashed appointments found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $appointments->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
