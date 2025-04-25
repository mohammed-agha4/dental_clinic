@extends('layouts.master.master')

@section('title', 'Patients')

@section('content')
    <div class="container-fluid">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light d-flex justify-content-between align-items-center py-3">
                <h4 class="mb-0">Patients</h4>

                @can('patients.trash')
                    <a href="{{ route('dashboard.patients.trash') }}" class="btn btn-secondary btn-sm me-2">
                        <i class="fas fa-trash-alt fa-sm"></i> Trash
                    </a>
                @endcan
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
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Gender</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>DOB</th>
                                @if (auth()->user()->can('patients.show') ||
                                        auth()->user()->can('patients.update') ||
                                        auth()->user()->can('patients.delete'))
                                    <th>Action</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($patients as $patient)
                                <tr class="text-center">
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ Str::ucfirst($patient->fname) }}</td>
                                    <td>{{ Str::ucfirst($patient->lname) }}</td>
                                    <td>{{ $patient->gender }}</td>
                                    <td title="{{ $patient->email }}">{{ Str::limit($patient->email, 12) }}</td>
                                    <td>{{ $patient->phone }}</td>
                                    <td>{{ $patient->DOB->format('M j, Y') }}</td>
                                    @if (auth()->user()->can('patients.show') ||
                                            auth()->user()->can('patients.update') ||
                                            auth()->user()->can('patients.delete'))
                                        <td>
                                            @can('patients.show')
                                                <a class="btn btn-outline-success btn-sm"
                                                    href="{{ route('dashboard.patients.show', $patient->id) }}">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            @endcan
                                            @can('patients.update')
                                                <a class="btn btn-outline-primary btn-sm"
                                                    href="{{ route('dashboard.patients.edit', $patient->id) }}">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endcan
                                            @can('patients.delete')
                                                <button class="btn btn-outline-danger btn-sm delete-btn"
                                                    data-id="{{ $patient->id }}"
                                                    data-name="{{ $patient->fname }} {{ $patient->lname }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @endcan
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        @if (auth()->user()->hasAbility('view-own-patients') && !auth()->user()->hasAbility('view-all-patients'))
                                            <i class="fas fa-user-times fa-3x text-muted mb-3"></i>
                                            <h5>No patients assigned to you</h5>
                                            <p class="text-muted">When patients book appointments with you, they'll appear
                                                here.</p>
                                        @else
                                            <i class="fas fa-users-slash fa-3x text-muted mb-3"></i>
                                            <h5>No patients found</h5>
                                            <p class="text-muted">When patients are registered, they'll appear here.</p>
                                        @endif
                                    </td>
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
