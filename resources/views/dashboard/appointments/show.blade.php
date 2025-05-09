@extends('layouts.master.master')
@section('title', 'Appointment Information')

@section('content')
    <div class="container-fluid">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h5>Appointment Details</h5>
                <div>
                    @can('appointments.update')
                    <a href="{{ route('dashboard.appointments.edit', $appointment->id) }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-edit fa-sm"></i> Edit
                    </a>
                    @endcan
                    @can('appointments.delete')
                    <button type="button" class="btn btn-outline-danger btn-sm delete-btn" data-id="{{ $appointment->id }}"
                        data-name="Appointment for {{ $appointment->patient->fname }} {{ $appointment->patient->lname }} on {{ $appointment->appointment_date }}">
                        <i class="fas fa-trash fa-sm"></i> Delete
                    </button>
                    @endcan
                    <a href="{{ route('dashboard.appointments.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left fa-sm"></i> Back
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Patient & Dentist Info -->
                    <div class="col-md-6">
                        <div class="card mb-4 h-100">
                            <div class="card-header">
                                <h6 class="m-0 font-weight-bold text-success">Patient Information</h6>
                            </div>
                            <div class="card-body">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td class="p-1 font-weight-bold">Name:</td>
                                        <td class="p-1">{{ $appointment->patient->fname }} {{ $appointment->patient->lname }}</td>
                                    </tr>
                                    <tr>
                                        <td class="p-1 font-weight-bold">Email:</td>
                                        <td class="p-1">{{ $appointment->patient->email }}</td>
                                    </tr>
                                    <tr>
                                        <td class="p-1 font-weight-bold">Phone:</td>
                                        <td class="p-1">{{ $appointment->patient->phone ?? 'Not Recorded' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card mb-4 h-100">
                            <div class="card-header">
                                <h6 class="m-0 font-weight-bold text-success">Dentist Information</h6>
                            </div>
                            <div class="card-body">
                                @if ($appointment->staff_id)
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td class="p-1 font-weight-bold">Name:</td>
                                        <td class="p-1">{{ $appointment->dentist->user->name }}</td>
                                    </tr>
                                    <tr>
                                        <td class="p-1 font-weight-bold">Email:</td>
                                        <td class="p-1">{{ $appointment->dentist->user->email }}</td>
                                    </tr>
                                    <tr>
                                        <td class="p-1 font-weight-bold">Specialization:</td>
                                        <td class="p-1">{{ $appointment->dentist->specialization ?? 'General' }}</td>
                                    </tr>
                                </table>
                                @else
                                <p class="small text-muted">No dentist assigned yet</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Appointment Details -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="m-0 font-weight-bold text-success">Appointment Details</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <h6 class="font-weight-bold">Service Information</h6>
                                        <table class="table table-sm table-borderless">
                                            <tr>
                                                <td class="p-1">Service:</td>
                                                <td class="p-1">{{ $appointment->service->service_name }}</td>
                                            </tr>
                                            <tr>
                                                <td class="p-1">Fee:</td>
                                                <td class="p-1">${{ number_format($appointment->service->service_price, 2) }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="col-md-4">
                                        <h6 class="font-weight-bold">Timing</h6>
                                        <table class="table table-sm table-borderless">
                                            <tr>
                                                <td class="p-1">Date & Time:</td>
                                                <td class="p-1">{{ $appointment->appointment_date }}</td>
                                            </tr>
                                            <tr>
                                                <td class="p-1">Duration:</td>
                                                <td class="p-1">{{ $appointment->duration }} minutes</td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="col-md-4">
                                        <h6 class="font-weight-bold">Status</h6>
                                        <table class="table table-sm table-borderless">
                                            <tr>
                                                <td class="p-1">Status:</td>
                                                <td class="p-1"> {{ ucfirst($appointment->status) }} </td>
                                            </tr>
                                            @if ($appointment->status == 'canceled' && $appointment->cancellation_reason)
                                            <tr>
                                                <td class="p-1">Reason:</td>
                                                <td class="p-1">{{ $appointment->cancellation_reason }}</td>
                                            </tr>
                                            @endif
                                            <tr>
                                                <td class="p-1">Reminder:</td>
                                                <td class="p-1">{{ $appointment->reminder_sent ? 'Sent' : 'Not sent' }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notes -->
                @if ($appointment->notes)
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="m-0 font-weight-bold text-success">Notes</h6>
                            </div>
                            <div class="card-body">
                                <p class="whitespace-pre-line small">{{ $appointment->notes }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>


    <form id="delete-form" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
@endsection

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.querySelector('.delete-btn').addEventListener('click', function() {
            const appointmentId = this.getAttribute('data-id');
            const appointmentName = this.getAttribute('data-name');

            Swal.fire({
                title: 'Are you sure?',
                html: `You are about to delete: <strong>${appointmentName}</strong>`,
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
    </script>
@endpush
