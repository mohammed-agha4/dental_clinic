@extends('layouts.master.master')

@section('title', 'User Roles')

@section('content')
    <div class="container-fluid">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light d-flex justify-content-between align-items-center py-3">
                <h4 class="mb-0">User Roles</h4>
                @can('user_roles.create')
                    <a href="{{ route('dashboard.user-roles.create') }}" class="btn btn-dark btn-sm">
                        <i class="fas fa-plus fa-sm"></i> New User Role
                    </a>
                @endcan
            </div>

            <!-- Flash Messages -->
            @if (session()->has('success'))
                <div id="flash-msg" class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if (session()->has('error'))
                <div id="flash-msg" class="alert alert-danger alert-dismissible fade show">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table small">
                    <thead>
                        <tr class="text-center">
                            <th>ID</th>
                            <th>Authorizable Name</th>
                            <th>Role</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($role_users as $role_user)
                            <tr class="text-center">
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $role_user->user->name }}</td>
                                <td>{{ $role_user->role->name }}</td>
                                <td>
                                    @can('user_roles.update')
                                        <a class="btn btn-outline-primary btn-sm"
                                            href="{{ route('dashboard.user-roles.edit-composite', ['user_id' => $role_user->user_id, 'role_id' => $role_user->role_id]) }}">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endcan
                                    @can('user_roles.delete')
                                        <button class="btn btn-outline-danger btn-sm delete-btn"
                                            data-user-id="{{ $role_user->user_id }}" data-role-id="{{ $role_user->role_id }}"
                                            data-name="{{ $role_user->role->name }}"
                                            data-username="{{ $role_user->user->name }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">No data found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Links -->
            <div class="mt-3">
                {{ $role_users->withQueryString()->links() }}
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle delete button clicks
            document.querySelectorAll('.delete-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const userId = this.getAttribute('data-user-id');
                    const roleId = this.getAttribute('data-role-id');
                    const roleName = this.getAttribute('data-name');
                    const userName = this.getAttribute('data-username');

                    Swal.fire({
                        title: 'Are you sure?',
                        html: `You are about to delete the role: <strong>${roleName}</strong> for the staff: <strong>${userName}</strong>`,
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
                            form.action = "{{ route('dashboard.user-roles.destroy-composite', ['user_id' => ':userId', 'role_id' => ':roleId']) }}".replace(':userId', userId).replace(':roleId', roleId);
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>
@endpush
