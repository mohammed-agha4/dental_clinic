@extends('layouts.master.master')

@section('title', 'Appointments')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light d-flex justify-content-between align-items-center py-3">
            <h4 class="mb-0">Appointments</h4>
            <a href="{{ route('dashboard.appointments.create') }}" class="btn btn-dark btn-sm">
                <i class="fas fa-plus fa-sm"></i> Schedule Appointment
            </a>

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
                            <th>Cancellation Reason</th>
                            <th>Notes</th>
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
                                <td>{{ $appointment->appointment_date }}</td>
                                <td>{{ $appointment->duration }}</td>
                                <td>{{ $appointment->status }}</td>
                                <td>{{ $appointment->cancellation_reason }}</td>
                                <td>{{ $appointment->notes }}</td>
                                <td>
                                    <a class="btn btn-outline-success btn-sm" href="{{ route('dashboard.visits.create', $appointment->id) }}">
                                        Record Visit
                                    </a>
                                    <a class="btn btn-outline-primary btn-sm" href="{{ route('dashboard.appointments.edit', $appointment->id) }}">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button class="btn btn-outline-danger btn-sm delete-btn" data-id="{{ $appointment->id }}" data-name="{{ $appointment->patient->fname }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>

                        @empty

                            <tr>
                                <td colspan="10" class="text-center py-4">No data found</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <a href="{{ route('dashboard.appointments.trash') }}" class="btn btn-dark btn-sm">
                        <i class="fas fa-plus fa-sm"></i> trash
                    </a>
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
                    form.action = "{{ route('dashboard.appointments.destroy', '') }}/" + appointmentId;
                    form.submit();
                }
            });
        });
    });
</script>
@endpush
