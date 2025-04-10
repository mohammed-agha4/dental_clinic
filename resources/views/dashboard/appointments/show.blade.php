@extends('layouts.master.master')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-800">Appointment Details</h1>
            <div class="d-flex justify-content-between align-items-center my-2">
                <div>
                    <a href="{{ route('dashboard.appointments.edit', $appointment->id) }}" class="btn btn-primary">
                        Edit
                    </a>

                    <button type="button" class="btn btn-danger delete-btn" data-id="{{ $appointment->id }}"
                        data-name="Appointment for {{ $appointment->patient->name }} on {{ $appointment->appointment_date }}">
                        Delete
                    </button>
                </div>

                <div>
                    <a href="{{ route('dashboard.appointments.index') }}" class="btn btn-dark">
                        Back to main
                    </a>
                </div>
            </div>
        </div>

        <!-- Hidden Delete Form -->
        <form id="delete-form" method="POST" style="display: none;">
            @csrf
            @method('DELETE')
        </form>

        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="p-6">
                <!-- Patient and Dentist Information in same row -->
                <div class="d-flex flex-col md:flex-row gap-6 mb-6">
                    <!-- Patient Information -->
                    <div class="border rounded-lg p-4 flex-1 w-50">
                        <h2 class="text-lg font-semibold mb-4 text-gray-700 border-b pb-2">Patient Information</h2>
                        <div class="space-y-2">
                            <p><span class="font-medium">Name:</span> {{ $appointment->patient->fname }}
                                {{ $appointment->patient->lname }}</p>
                            <p><span class="font-medium">Email:</span> {{ $appointment->patient->email }}</p>
                            <p><span class="font-medium">Phone:</span> {{ $appointment->patient->phone ?? 'N/A' }}</p>
                        </div>
                    </div>

                    <!-- Dentist Information -->
                    <div class="border rounded-lg p-4 flex-1 w-50">
                        <h2 class="text-lg font-semibold mb-4 text-gray-700 border-b pb-2">Dentist Information</h2>
                        @if ($appointment->staff_id)
                            <div class="space-y-2">
                                <p><span class="font-medium">Name:</span> {{ $appointment->dentist->user->name }}</p>
                                <p><span class="font-medium">Email:</span> {{ $appointment->dentist->user->email }}</p>
                                <p><span class="font-medium">Specialization:</span>
                                    {{ $appointment->dentist->specialization ?? 'General' }}</p>
                            </div>
                        @else
                            <p class="text-gray-500">No dentist assigned yet</p>
                        @endif
                    </div>
                </div>

                <!-- Appointment Details -->
                <div class="border rounded-lg p-4 mb-6">
                    <h2 class="text-lg font-semibold mb-4 text-gray-700 border-b pb-2">Appointment Details</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <p><span class="font-medium">Service:</span> {{ $appointment->service->name }}</p>
                            <p><span class="font-medium">Fee:</span> ${{ number_format($appointment->service->price, 2) }}
                            </p>
                        </div>
                        <div>
                            <p><span class="font-medium">Date & Time:</span> {{ $appointment->appointment_date }}</p>
                            <p><span class="font-medium">Duration:</span> {{ $appointment->duration }} minutes</p>
                        </div>
                        <div>
                            <p>
                                <span class="font-medium">Status:</span>
                                <span
                                    class="px-2 py-1 rounded-full text-xs
                                @if ($appointment->status == 'completed') bg-green-100 text-green-800
                                @elseif($appointment->status == 'canceled') bg-red-100 text-red-800
                                @elseif($appointment->status == 'scheduled') bg-blue-100 text-blue-800
                                @elseif($appointment->status == 'rescheduled') bg-yellow-100 text-yellow-800
                                @else bg-purple-100 text-purple-800 @endif">
                                    {{ ucfirst($appointment->status) }}
                                </span>
                            </p>
                            @if ($appointment->status == 'canceled' && $appointment->cancellation_reason)
                                <p><span class="font-medium">Cancellation Reason:</span>
                                    {{ $appointment->cancellation_reason }}</p>
                            @endif
                            <p><span class="font-medium">Reminder Sent:</span>
                                {{ $appointment->reminder_sent ? 'Yes' : 'No' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Notes -->
                @if ($appointment->notes)
                    <div class="border rounded-lg p-4">
                        <h2 class="text-lg font-semibold mb-2 text-gray-700 border-b pb-2">Notes</h2>
                        <p class="text-gray-700 whitespace-pre-line">{{ $appointment->notes }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
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
                        form.action = "{{ route('dashboard.appointments.destroy', '') }}/" +
                            appointmentId;
                        form.submit();
                    }
                });
            });
        });
    </script>
@endpush
