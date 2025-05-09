@extends('layouts.master.master')
@section('title', 'Patient Information')

@section('content')
    <div class="container-fluid">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h5>Patient Details</h5>
                <div>
                    @can('patients.update')
                    <a href="{{ route('dashboard.patients.edit', $patient->id) }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-edit fa-sm"></i> Edit
                    </a>
                    @endcan
                    @can('patients.delete')
                    <button type="button" class="btn btn-outline-danger btn-sm delete-btn" data-id="{{ $patient->id }}"
                        data-name="{{ $patient->fname }} {{ $patient->lname }}">
                        <i class="fas fa-trash fa-sm"></i> Delete
                    </button>
                    @endcan
                    <a href="{{ route('dashboard.patients.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left fa-sm"></i> Back
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Personal Information -->
                    <div class="col-md-6">
                        <div class="card mb-4 h-100">
                            <div class="card-header">
                                <h6 class="m-0 font-weight-bold text-success">Personal Information</h6>
                            </div>
                            <div class="card-body">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td class="p-1 font-weight-bold">Name:</td>
                                        <td class="p-1">{{ $patient->fname }} {{ $patient->lname }}</td>
                                    </tr>
                                    <tr>
                                        <td class="p-1 font-weight-bold">Email:</td>
                                        <td class="p-1">{{ $patient->email }}</td>
                                    </tr>
                                    <tr>
                                        <td class="p-1 font-weight-bold">Phone:</td>
                                        <td class="p-1">{{ $patient->phone ?? 'Not Recorded' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="p-1 font-weight-bold">Date of Birth:</td>
                                        <td class="p-1">{{ $patient->DOB }}</td>
                                    </tr>
                                    <tr>
                                        <td class="p-1 font-weight-bold">Gender:</td>
                                        <td class="p-1">{{ $patient->gender }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Emergency Information -->
                    <div class="col-md-6">
                        <div class="card mb-4 h-100">
                            <div class="card-header">
                                <h6 class="m-0 font-weight-bold text-success">Emergency Information</h6>
                            </div>
                            <div class="card-body">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td class="p-1 font-weight-bold">Contact Name:</td>
                                        <td class="p-1">{{ $patient->Emergency_contact_name ?? 'Not provided' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="p-1 font-weight-bold">Contact Phone:</td>
                                        <td class="p-1">{{ $patient->Emergency_contact_phone ?? 'Not provided' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Medical History -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="m-0 font-weight-bold text-success">Medical History</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <h6 class="font-weight-bold">Medical History</h6>
                                    <p class="whitespace-pre-line small">{{ $patient->medical_history ?? 'No medical history recorded' }}</p>
                                </div>
                                <div>
                                    <h6 class="font-weight-bold">Allergies</h6>
                                    <p class="whitespace-pre-line small">{{ $patient->allergies ?? 'No allergies recorded' }}</p>
                                </div>
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
            const patientId = this.getAttribute('data-id');
            const patientName = this.getAttribute('data-name');

            Swal.fire({
                title: 'Are you sure?',
                html: `You are about to delete patient: <strong>${patientName}</strong>`,
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
    </script>
@endpush
