@extends('layouts.master.master')

@section('title', 'Trashed Staff')

@section('content')
    <div class="container-fluid">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light d-flex justify-content-between align-items-center py-3">
                <h4 class="mb-0">Trashed Staff</h4>
                <a href="{{ route('dashboard.staff.index') }}" class="btn btn-dark btn-sm">
                    <i class="fas fa-arrow-left fa-sm"></i> Back to Main
                </a>
            </div>

            <div class="card-body">
                @if (session()->has('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if (session()->has('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-striped table-hover small">
                        <thead class="table-light">
                            <tr class="text-center">
                                <th>#</th>
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
                                        @can('staff.restore')
                                            <form action="{{ route('dashboard.staff.restore', $st->id) }}" method="post"
                                                class="d-inline">
                                                @method('PUT')
                                                @csrf

                                                <button type="submit" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-trash-restore"></i> Restore
                                                </button>
                                            @endcan
                                        </form>
                                        @can('staff.force_delete')
                                            <form action="{{ route('dashboard.staff.force-delete', $st->id) }}" method="post"
                                                class="d-inline">
                                                @method('DELETE')
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                                    onclick="return confirm('Are you sure you want to permanently delete this staff?')">
                                                    <i class="fas fa-trash-alt"></i> Delete
                                                </button>
                                            </form>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">No trashed staff found</td>
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
@endsection
