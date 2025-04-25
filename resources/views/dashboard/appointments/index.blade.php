@extends('layouts.master.master')

@section('title', 'Appointments')

@section('content')
    <div class="container-fluid">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light d-flex justify-content-between align-items-center py-3">
                <h4 class="mb-0">Appointments</h4>
                <div>
                    @can('appointments.trash')
                        <a href="{{ route('dashboard.appointments.trash') }}" class="btn btn-secondary btn-sm me-2">
                            <i class="fas fa-trash-alt fa-sm"></i> Trash
                        </a>
                    @endcan
                    @can('appointments.create')
                        <a href="{{ route('dashboard.appointments.create') }}" class="btn btn-dark btn-sm">
                            <i class="fas fa-plus fa-sm"></i> Schedule Appointment
                        </a>
                    @endcan
                </div>
            </div>

            <div class="card-body">
                @if (session()->has('success'))
                    <div id="flash-msg" class="alert alert-success alert-dismissible fade show">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if (session()->has('error'))
                    <div id="flash-msg" class="alert alert-danger alert-dismissible fade show ">
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
                                <th>Reminder Sent</th>
                                @if (auth()->user()->can('appointments.show') ||
                                        auth()->user()->can('appointments.update') ||
                                        auth()->user()->can('appointments.delete'))
                                    <th>Action</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($appointments as $appointment)
                                <tr class="text-center">
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $appointment->patient->FullName }}</td>
                                    <td>{{ Str::ucfirst($appointment->dentist->user->name) ?? 'N/A' }}</td>
                                    <td>{{ $appointment->service->service_name }}</td>
                                    <td>{{ $appointment->appointment_date->format('M j, Y g:i A') }}</td>
                                    <td>{{ $appointment->duration }} min</td>
                                    <td>{{ $appointment->status }}</td>
                                    <td>{{ $appointment->reminder_sent == 1 ? 'Yes' : 'No' }}</td>
                                    @if (auth()->user()->can('appointments.show') ||
                                            auth()->user()->can('appointments.update') ||
                                            auth()->user()->can('appointments.delete'))
                                        <td>
                                            @can('visits.create')
                                                <a class="btn btn-outline-success btn-sm"
                                                    href="{{ route('dashboard.visits.create', $appointment->id) }}">
                                                    Record Visit
                                                </a>
                                            @endcan
                                            @can('appointments.show')
                                                <a href="{{ route('dashboard.appointments.show', $appointment->id) }}"
                                                    class="btn btn-sm btn-outline-success">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            @endcan

                                            @can('appointments.update')
                                                <a class="btn btn-outline-primary btn-sm"
                                                    href="{{ route('dashboard.appointments.edit', $appointment->id) }}">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endcan
                                            @can('appointments.delete')
                                                <button class="btn btn-outline-danger btn-sm delete-btn"
                                                    data-id="{{ $appointment->id }}"
                                                    data-name="{{ $appointment->patient->fname }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @endcan
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center py-4">No data found</td>
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

    <!-- Hidden Delete Form -->
    <form id="delete-form" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
@endsection

@push('js')
    <!-- Include SweetAlert Library -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Setup delete confirmation with SweetAlert
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function() {
                const appointmentId = this.getAttribute('data-id');
                const appointmentName = this.getAttribute('data-name');

                Swal.fire({
                    title: 'Are you sure?',
                    html: `You are about to delete the appointment for: <strong>${appointmentName}</strong>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true,
                    focusCancel: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.getElementById('delete-form');
                        form.action = "{{ route('dashboard.appointments.destroy', '') }}/" +
                            appointmentId;
                        form.submit();
                    }
                });
            });
        });
    </script>
@endpush
