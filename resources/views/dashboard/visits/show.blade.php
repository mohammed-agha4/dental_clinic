@extends('layouts.master.master')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-800">Visit Details</h1>
            <div class="d-flex justify-content-between align-items-center my-2">
                <div>
                    <a href="{{ route('dashboard.visits.edit', $visit->id) }}" class="btn btn-primary">
                        Edit
                    </a>

                    <button type="button" class="btn btn-danger delete-btn" data-id="{{ $visit->id }}"
                        data-name="{{ $visit->patient->fname }} {{ $visit->patient->lname }}">
                        Delete
                    </button>
                </div>

                <div>
                    <a href="{{ route('dashboard.visits.index') }}" class="btn btn-dark">
                        Back
                    </a>
                </div>
            </div>
        </div>
    
        <!-- Hidden Delete Form -->
        <form id="delete-form" method="POST" style="display: none;">
            @csrf
            @method('DELETE')
        </form>

        <div class="flex flex-col lg:flex-row gap-4">
            <!-- Left Column - Patient & Visit Info -->
            <div class="flex-1 space-y-4">
                <!-- Patient Card -->
                <div class="bg-white rounded-lg shadow p-4">
                    <h2 class="font-semibold text-gray-700 border-b pb-2 mb-2">Patient Information</h2>
                    <div class="space-y-1 text-sm">
                        <p><span class="font-medium">Name:</span> {{ $visit->patient->fname }} {{ $visit->patient->lname }}
                        </p>
                        <p><span class="font-medium">Phone:</span> {{ $visit->patient->phone ?? 'N/A' }}</p>
                        <p><span class="font-medium">Email:</span> {{ $visit->patient->email }}</p>
                    </div>
                </div>

                <!-- Visit Card -->
                <div class="bg-white rounded-lg shadow p-4">
                    <h2 class="font-semibold text-gray-700 border-b pb-2 mb-2">Visit Details</h2>
                    <div class="space-y-1 text-sm">
                        <p><span class="font-medium">Date:</span> {{ $visit->visit_date }}</p>
                        @if ($visit->appointment)
                            <p><span class="font-medium">Appointment:</span> {{ $visit->appointment->appointment_date }}
                            </p>
                        @endif
                        @if ($visit->service)
                            <p><span class="font-medium">Service:</span> {{ $visit->service->service_name }}</p>
                        @endif
                    </div>
                </div>

                <!-- Dentist Card -->
                <div class="bg-white rounded-lg shadow p-4">
                    <h2 class="font-semibold text-gray-700 border-b pb-2 mb-2">Dentist</h2>
                    <div class="space-y-1 text-sm">
                        <p><span class="font-medium">Name:</span> {{ $visit->staff->user->name }}</p>
                        <p><span class="font-medium">Specialization:</span>
                            {{ $visit->staff->specialization ?? 'General' }}</p>
                    </div>
                </div>
            </div>

            <!-- Middle Column - Clinical Notes -->
            <div class="flex-1 space-y-4">
                <!-- Chief Complaint -->
                <div class="bg-white rounded-lg shadow p-4">
                    <h2 class="font-semibold text-gray-700 border-b pb-2 mb-2">Chief Complaint</h2>
                    <p class="text-sm whitespace-pre-line text-gray-700">{{ $visit->cheif_complaint ?? 'Not recorded' }}
                    </p>
                </div>

                <!-- Diagnosis -->
                <div class="bg-white rounded-lg shadow p-4">
                    <h2 class="font-semibold text-gray-700 border-b pb-2 mb-2">Diagnosis</h2>
                    <p class="text-sm whitespace-pre-line text-gray-700">{{ $visit->diagnosis ?? 'Not recorded' }}</p>
                </div>

                <!-- Treatment -->
                <div class="bg-white rounded-lg shadow p-4">
                    <h2 class="font-semibold text-gray-700 border-b pb-2 mb-2">Treatment Notes</h2>
                    <p class="text-sm whitespace-pre-line text-gray-700">{{ $visit->treatment_notes ?? 'Not recorded' }}
                    </p>
                </div>
            </div>

            <!-- Right Column - Next Visit & Additional Info -->
            <div class="flex-1 space-y-4">
                <!-- Next Visit -->
                <div class="bg-white rounded-lg shadow p-4">
                    <h2 class="font-semibold text-gray-700 border-b pb-2 mb-2">Next Visit</h2>
                    <p class="text-sm whitespace-pre-line text-gray-700">
                        {{ $visit->next_visit_notes ?? 'No follow-up planned' }}</p>
                </div>

                <!-- Materials Used -->
                @if ($visit->inventoryItems && $visit->inventoryItems->count() > 0)
                    <div class="bg-white rounded-lg shadow p-4">
                        <h2 class="font-semibold text-gray-700 border-b pb-2 mb-2">Materials Used</h2>
                        <div class="space-y-2 text-sm">
                            @foreach ($visit->inventoryItems as $item)
                                <div class="flex justify-between">
                                    <span>{{ $item->name }}</span>
                                    <span class="font-medium">x{{ $item->pivot->quantity_used }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Payments -->
                @if ($visit->payments && $visit->payments->count() > 0)
                    <div class="bg-white rounded-lg shadow p-4">
                        <h2 class="font-semibold text-gray-700 border-b pb-2 mb-2">Payments</h2>
                        <div class="space-y-2 text-sm">
                            @foreach ($visit->payments as $payment)
                                <div class="flex justify-between items-center">
                                    <div>
                                        <span class="font-medium">${{ number_format($payment->amount, 2) }}</span>
                                        <span
                                            class="text-xs text-gray-500 ml-2">{{ ucfirst($payment->payment_method) }}</span>
                                    </div>
                                    <span
                                        class="px-2 py-0.5 text-xs rounded-full {{ $payment->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ ucfirst($payment->status) }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
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
