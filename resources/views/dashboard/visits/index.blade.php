@extends('layouts.master.master')

@section('title', 'Visits')

@section('content')
    <div class="container-fluid">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light d-flex justify-content-between align-items-center py-3">
                <h4 class="mb-0">Visits</h4>

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

                <div class="table-responsive mx-3">
                    <table class="table table-striped table-hover small">
                        <a href="{{ route('dashboard.visits.trash') }}" class="btn btn-sm mb-2 btn-dark">
                            <i class="fas fa-plus"></i> Trash
                        </a>
                        <thead class="table-light">
                            <tr class="text-center">
                                <th>ID</th>
                                <th>Appointment</th>
                                <th>Appointment Status</th>
                                <th>Patient</th>
                                <th>Staff</th>
                                <th>Service</th>
                                <th>Visit Date</th>
                                {{-- <th>Chief Complaint</th>
                            <th>Diagnosis</th>
                            <th>Treatment Notes</th>
                            <th>Next Visit Notes</th> --}}
                                <th>Tools</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($visits as $v)
                                <tr class="text-center">
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $v->appointment->appointment_date }}</td>
                                    <td>{{ $v->appointment->status }}</td>
                                    <td>{{ $v->patient->fname }} {{ $v->patient->lname }}</td>
                                    <td>{{ $v->staff->user->name }}</td>
                                    <td>{{ $v->service->service_name }}</td>
                                    <td>{{ $v->visit_date }}</td>
                                    {{-- <td>{{ $v->cheif_complaint }}</td>
                                <td>{{ $v->diagnosis }}</td>
                                <td>{{ $v->treatment_notes }}</td>
                                <td>{{ $v->next_visit_notes }}</td> --}}
                                    <td>
                                        @if ($v->inventoryItems->isNotEmpty())
                                            <ol style="margin: 0; padding: 0; list-style: none;">
                                                @foreach ($v->inventoryItems as $inventoryItem)
                                                    <li>
                                                        {{ $inventoryItem->name }}
                                                        (Quantity Used: {{ $inventoryItem->pivot->quantity_used }})
                                                    </li>
                                                @endforeach
                                            </ol>
                                        @else
                                            No inventory items used.
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('dashboard.visits.show', $v->id) }}"
                                            class="btn btn-sm btn-outline-success">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a class="btn btn-outline-primary btn-sm"
                                            href="{{ route('dashboard.visits.edit', $v->id) }}">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button class="btn btn-outline-danger btn-sm delete-btn"
                                            data-id="{{ $v->id }}" data-name="{{ $v->patient->fname }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="13" class="text-center py-4">No data found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $visits->links() }}
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
        });
    </script>
@endpush
