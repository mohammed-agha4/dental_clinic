@extends('layouts.master.master')
@section('title', 'Visit Information')

@section('content')
    <div class="container-fluid">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h5>Visit Details</h5>
                <div>
                    <a href="{{ route('dashboard.visits.edit', $visit->id) }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-edit fa-sm"></i> Edit
                    </a>
                    <button type="button" class="btn btn-outline-danger btn-sm delete-btn" data-id="{{ $visit->id }}"
                        data-name="{{ $visit->patient->fname }} {{ $visit->patient->lname }}">
                        <i class="fas fa-trash fa-sm"></i> Delete
                    </button>
                    <a href="{{ route('dashboard.visits.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left fa-sm"></i> Back
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Patient & Visit Info -->
                    <div class="col-md-4">
                        <div class="card mb-4 h-100">
                            <div class="card-header">
                                <h6 class="m-0 font-weight-bold text-success">Patient & Visit Information</h6>
                            </div>
                            <div class="card-body">
                                <h6 class="font-weight-bold">Patient Details</h6>
                                <table class="table table-sm table-borderless mb-3">
                                    <tr>
                                        <td class="p-1">Name:</td>
                                        <td class="p-1">{{ $visit->patient->fname }} {{ $visit->patient->lname }}</td>
                                    </tr>
                                    <tr>
                                        <td class="p-1">Phone:</td>
                                        <td class="p-1">{{ $visit->patient->phone ?? 'Not Recorded' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="p-1">Email:</td>
                                        <td class="p-1">{{ $visit->patient->email }}</td>
                                    </tr>
                                </table>

                                <h6 class="font-weight-bold mt-3">Visit Details</h6>
                                <table class="table table-sm table-borderless mb-3">
                                    <tr>
                                        <td class="p-1">Date:</td>
                                        <td class="p-1">{{ $visit->visit_date->format('M j, Y g:i A') }}</td>
                                    </tr>
                                    @if ($visit->appointment)
                                    <tr>
                                        <td class="p-1">Appointment:</td>
                                        <td class="p-1">{{ $visit->appointment->appointment_date->format('M j, Y g:i A') }}</td>
                                    </tr>
                                    @endif
                                    @if ($visit->service)
                                    <tr>
                                        <td class="p-1">Service:</td>
                                        <td class="p-1">{{ $visit->service->service_name }}</td>
                                        <td class="p-1">{{ $visit->service->service_price }}</td>
                                    </tr>
                                    @endif
                                </table>

                                <h6 class="font-weight-bold mt-3">Dentist</h6>
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td class="p-1">Name:</td>
                                        <td class="p-1">{{ $visit->staff->user->name }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Clinical Notes -->
                    <div class="col-md-4">
                        <div class="card mb-4 h-100">
                            <div class="card-header">
                                <h6 class="m-0 font-weight-bold text-success">Clinical Notes</h6>
                            </div>
                            <div class="card-body">
                                <h6 class="font-weight-bold">Chief Complaint</h6>
                                <p class="whitespace-pre-line small">{{ $visit->cheif_complaint ?? 'Not recorded' }}</p>

                                <h6 class="font-weight-bold mt-3">Diagnosis</h6>
                                <p class="whitespace-pre-line small">{{ $visit->diagnosis ?? 'Not recorded' }}</p>

                                <h6 class="font-weight-bold mt-3">Treatment Notes</h6>
                                <p class="whitespace-pre-line small">{{ $visit->treatment_notes ?? 'Not recorded' }}</p>

                                <h6 class="font-weight-bold mt-3">Next Visit</h6>
                                <p class="whitespace-pre-line small">{{ $visit->next_visit_notes ?? 'No follow-up planned' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Payments & Materials -->
                    <div class="col-md-4">
                        <div class="card mb-4 h-100">
                            <div class="card-header">
                                <h6 class="m-0 font-weight-bold text-success">Payments & Materials</h6>
                            </div>
                            <div class="card-body">
                                @if ($visit->payments && $visit->payments->count() > 0)
                                <h6 class="font-weight-bold">Payments</h6>
                                <div class="mb-3">
                                    @foreach ($visit->payments as $payment)
                                        <div class="d-flex justify-content-between align-items-center small py-1 border-bottom">
                                            <div>
                                                <span class="font-weight-bold">${{ number_format($payment->amount, 2) }}</span>
                                                <span class="text-muted ml-2">{{ ucfirst($payment->payment_method) }}</span>
                                            </div>
                                            <span class="badge {{ $payment->status === 'completed' ? 'badge-success' : 'badge-warning' }}">
                                                {{ ucfirst($payment->status) }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                                @endif

                                @if ($visit->inventoryItems && $visit->inventoryItems->count() > 0)
                                <h6 class="font-weight-bold">Materials Used</h6>
                                <div class="small">
                                    @foreach ($visit->inventoryItems as $item)
                                        <div class="d-flex justify-content-between py-1 border-bottom">
                                            <span>{{ $item->name }}</span>
                                            <span class="font-weight-bold">x{{ $item->pivot->quantity_used }}</span>
                                        </div>
                                    @endforeach
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
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
            const visitId = this.getAttribute('data-id');
            const patientName = this.getAttribute('data-name');

            Swal.fire({
                title: 'Are you sure?',
                html: `You are about to delete the visit for patient: <strong>${patientName}</strong>`,
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
                    form.action = "{{ route('dashboard.visits.destroy', '') }}/" + visitId;
                    form.submit();
                }
            });
        });
    </script>
@endpush
