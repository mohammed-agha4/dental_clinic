@extends('layouts.master.master')

@section('title', 'Roles')

@section('content')
    <div class="container-fluid">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light d-flex justify-content-between align-items-center py-3">
                <h4 class="mb-0">Roles</h4>
                <a href="{{ route('dashboard.roles.create') }}" class="btn btn-dark btn-sm">
                    <i class="fas fa-plus fa-sm"></i> New Role
                </a>
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

            <!-- Table with forced horizontal scrolling -->
            <div class="table-responsive" style="overflow-x: auto;">
                <table class="table small" style="min-width: 600px;">
                    <thead>
                        <tr class="text-center">
                            <th>#</th>
                            <th>Name</th>
                            <th>Created At</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($roles as $role)
                            <tr class="text-center">
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $role->name }}</td>
                                <td>{{ $role->created_at->diffForHumans() }}</td>
                                <td>
                                    <a class="btn btn-outline-primary btn-sm"
                                        href="{{ route('dashboard.roles.edit', $role->id) }}">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button class="btn btn-outline-danger btn-sm delete-btn"
                                            data-id="{{ $role->id }}"
                                            data-name="{{ $role->name }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
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
                {{ $roles->withQueryString()->links() }}
            </div>
        </div>
    </div>


    <form id="delete-form" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
@endsection
@push('js')
    <x-delete-alert
        route="dashboard.roles.destroy"
        itemName="role"
        deleteBtnClass="delete-btn"
    />
@endpush
