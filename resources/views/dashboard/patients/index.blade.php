@extends('layouts.master.master')

@section('title', 'Patients')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light d-flex justify-content-between align-items-center py-3">
            <h4 class="mb-0">Patients</h4>
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
                            <th>Fname</th>
                            <th>Lname</th>
                            <th>Gender</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>DOB</th>
                            <th>Medical History</th>
                            <th>Allergies</th>
                            <th>Emergency Contact Name</th>
                            <th>Emergency Contact Phone</th>
                            <th>Last Visit Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($patients as $patient)
                            <tr class="text-center">
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $patient->fname }}</td>
                                <td>{{ $patient->lname }}</td>
                                <td>{{ $patient->gender }}</td>
                                <td>{{ $patient->email }}</td>
                                <td>{{ $patient->phone }}</td>
                                <td>{{ $patient->DOB }}</td>
                                <td>{{ $patient->medical_history }}</td>
                                <td>{{ $patient->allergies }}</td>
                                <td>{{ $patient->Emergency_contact_name }}</td>
                                <td>{{ $patient->Emergency_contact_phone }}</td>
                                <td>{{ $patient->last_visit_date }}</td>
                                <td>
                                    <a class="btn btn-outline-primary btn-sm" href="{{ route('dashboard.patients.edit', $patient->id) }}">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button class="btn btn-outline-danger btn-sm delete-btn" data-id="{{ $patient->id }}" data-name="{{ $patient->fname }} {{ $patient->lname }}">
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
                {{ $patients->links() }}
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
            const patientId = this.getAttribute('data-id');
            const patientName = this.getAttribute('data-name');

            Swal.fire({
                title: 'Are you sure?',
                html: `You are about to delete the patient: <strong>${patientName}</strong>`,
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
                    form.action = "{{ route('dashboard.patients.destroy', '') }}/" + patientId;
                    form.submit();
                }
            });
        });
    });
</script>
@endpush
