@extends('layouts.master.master')

@section('title', 'Link Staff to Service')

@section('content')
    <div class="container-fluid">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light d-flex justify-content-between align-items-center py-3">
                <h4 class="mb-0">Staff Services</h4>
                @can('service_staff.create')
                    <a href="{{ route('dashboard.service-staff.create') }}" class="btn btn-dark btn-sm">
                        <i class="fas fa-plus fa-sm"></i> New Staff Service
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
                    <div id="flash-msg" class="alert alert-danger alert-dismissible fade show">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="table-responsive" style="overflow-x: auto;">
                    <table class="table table-striped table-hover small" style="min-width: 600px;">
                        <thead class="table-light">
                            <tr class="text-center">
                                <th>#</th>
                                <th>Staff</th>
                                <th>Services</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($service_staff as $staff_service)
                                <tr class="text-center">
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ ucfirst($staff_service->dentist->user->name) }}</td>
                                    <td>{{ ucfirst($staff_service->service->service_name) }}</td>
                                    <td>
                                        <div class="d-flex flex-wrap justify-content-center gap-1">
                                            @can('service_staff.update')
                                                <a class="btn btn-outline-primary btn-sm"
                                                    href="{{ route('dashboard.service-staff.edit', $staff_service->id) }}">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endcan
                                            @can('service_staff.delete')
                                                <button class="btn btn-outline-danger btn-sm delete-btn"
                                                    data-id="{{ $staff_service->id }}"
                                                    data-name="{{ ucfirst($staff_service->dentist->user->name) }} - {{ ucfirst($staff_service->service->service_name) }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4">No Data Found </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $service_staff->links() }}
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
    <x-delete-alert
    route="dashboard.service-staff.destroy"
    itemName="staff_service"
    deleteBtnClass="delete-btn"/>
@endpush
