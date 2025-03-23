@extends('layouts.master.master')

@section('title', 'Staff')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light d-flex justify-content-between align-items-center py-3">
            <h4 class="mb-0">Staff</h4>
            <a href="{{ route('dashboard.staff.create') }}" class="btn btn-dark btn-sm">
                <i class="fas fa-plus fa-sm"></i> New Staff
            </a>
        </div>

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
                <table class="table table-striped table-hover">
                    <thead class="table-light">
                        <tr class="text-center">
                            <th>ID</th>
                            <th>User Name</th>
                            <th>Phone</th>
                            <th>License Number</th>
                            <th>Working Hours</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($staff as $st)
                        <tr class="text-center">
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $st->user->name }}</td>
                            <td>{{ $st->phone }}</td>
                            <td>{{ $st->license_number }}</td>
                            <td>{{ $st->working_hours }} Hour</td>
                            <td>
                                <span class="text-dark">
                                    {{ ucfirst($st->role) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge {{ $st->is_active ? 'bg-success' : 'bg-danger' }} text-white">
                                    {{ $st->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <a class="btn btn-outline-primary btn-sm" href="{{ route('dashboard.staff.edit', $st->id) }}">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button class="btn btn-outline-danger btn-sm delete-btn"
                                        data-id="{{ $st->id }}"
                                        data-name="{{ $st->user->name }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">No staff records found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $staff->links() }}
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

    <script>
        // Setup delete confirmation
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function() {
                const staffId = this.getAttribute('data-id');
                const staffName = this.getAttribute('data-name');

                Swal.fire({
                    title: 'Are you sure?',
                    html: `You are about to delete staff member: <strong>${staffName}</strong>`,
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
                        form.action = "{{ route('dashboard.staff.destroy', '') }}/" + staffId;
                        form.submit();
                    }
                });
            });
        });
    </script>
@endpush
