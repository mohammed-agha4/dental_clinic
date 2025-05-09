@extends('layouts.master.master')

@section('title', 'Services')

@section('content')
    <div class="container-fluid">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light d-flex justify-content-between align-items-center py-3">
                <h4 class="mb-0">Services</h4>
                @can('services.create')
                    <a href="{{ route('dashboard.services.create') }}" class="btn btn-dark btn-sm">
                        <i class="fas fa-plus fa-sm"></i> New Service
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

                <div class="table-responsive" style="overflow-x: auto;">
                    <table class="table table-striped table-hover small" style="min-width: 800px;">
                        <thead class="table-light">
                            <tr class="text-center">
                                <th>#</th>
                                <th>Service Name</th>
                                <th>Description</th>
                                <th>Service Price</th>
                                <th>Duration</th>
                                <th>Activity</th>
                                @if (auth()->user()->can('services.update') || auth()->user()->can('services.delete'))
                                    <th>Action</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($services as $service)
                                <tr class="text-center">
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $service->service_name }}</td>
                                    <td>{{ Str::limit($service->description, 50) }}</td>
                                    <td>{{ $service->service_price }} $</td>
                                    <td>{{ $service->duration }} minute</td>
                                    <td class="text-center">
                                        <span
                                            class="badge {{ $service->is_active ? 'bg-success' : 'bg-danger' }} text-white">
                                            {{ $service->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    @if (auth()->user()->can('services.update') || auth()->user()->can('services.delete'))
                                        <td>
                                            @can('services.update')
                                                <a class="btn btn-sm btn-outline-primary"
                                                    href="{{ route('dashboard.services.edit', $service->id) }}">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endcan
                                            @can('services.delete')
                                                <button class="btn btn-sm btn-outline-danger delete-btn"
                                                    data-id="{{ $service->id }}" data-name="{{ $service->service_name }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @endcan
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <h5>No services found</h5>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $services->links() }}
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
    <x-delete-alert route="dashboard.services.destroy" itemName="service" deleteBtnClass="delete-btn" />
@endpush
