@extends('layouts.master.master')

@section('title', 'Staff')

@section('content')
    <div class="container-fluid">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light d-flex justify-content-between align-items-center py-3">
                <h4 class="mb-0">Staff</h4>
                <div>
                    @can('staff.trash')
                        <a href="{{ route('dashboard.staff.trash') }}" class="btn btn-secondary btn-sm me-2">
                            <i class="fas fa-trash-alt fa-sm"></i> Trash
                        </a>
                    @endcan
                    @can('staff.create')
                        <a href="{{ route('dashboard.staff.create') }}" class="btn btn-dark btn-sm">
                            <i class="fas fa-plus fa-sm"></i> New Staff
                        </a>
                    @endcan
                </div>
            </div>

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

            <div class="table-responsive" style="overflow-x: auto;">
                <table class="table small" style="min-width: 800px;">
                    <thead>
                        <tr class="text-center">
                            <th>#</th>
                            <th>User Name</th>
                            <th>Phone</th>
                            <th>License Number</th>
                            <th>Working Hours</th>
                            <th>Role</th>
                            <th>Status</th>
                            @if (auth()->user()->can('staff.update') || auth()->user()->can('staff.delete'))
                                <th>Action</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($staff as $st)
                            <tr class="text-center">
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ ucfirst($st->user->name) }}</td>
                                <td>{{ $st->phone }}</td>
                                <td>{{ $st->license_number }}</td>
                                <td>{{ $st->working_hours }} Hour</td>
                                <td>{{ ucfirst($st->role) }}</td>
                                <td>
                                    <span class="badge {{ $st->is_active ? 'bg-success' : 'bg-danger' }} text-white">
                                        {{ $st->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                @if (auth()->user()->can('staff.update') || auth()->user()->can('staff.delete'))
                                    <td>
                                        @can('staff.show')
                                            <a class="btn btn-outline-success btn-sm"
                                                href="{{ route('dashboard.staff.show', $st->id) }}">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        @endcan
                                        @can('staff.update')
                                            <a class="btn btn-outline-primary btn-sm"
                                                href="{{ route('dashboard.staff.edit', $st->id) }}">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endcan
                                        @can('staff.delete')
                                            <button class="btn btn-outline-danger btn-sm delete-btn"
                                                data-id="{{ $st->id }}" data-name="{{ $st->user->name }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endcan
                                    </td>
                                @endif
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

    <form id="delete-form" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
    
@endsection

@push('js')
    <x-delete-alert route="dashboard.staff.destroy" itemName="staff" deleteBtnClass="delete-btn" />
@endpush
