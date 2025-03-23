@extends('layouts.master.master')

@section('title', 'Services')

@section('content')
    <div class="container-fluid">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light d-flex justify-content-between align-items-center py-3">
                <h4 class="mb-0">Services</h4>
                <a href="{{ route('dashboard.services.create') }}" class="btn btn-dark btn-sm">
                    <i class="fas fa-plus fa-sm"></i> New Service
                </a>
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
                    <table class="table table-striped table-hover">
                        <thead class="table-light">
                            <tr class="text-center">
                                <th>ID</th>
                                <th>Service Name</th>
                                <th>Description</th>
                                <th>Service Price</th>
                                <th>Duration</th>
                                <th>Activity</th>
                                <th>Action</th>
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
                                        <span class="badge {{ $service->is_active ? 'bg-success' : 'bg-danger' }} text-white">
                                            {{ $service->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        <a class="btn btn-sm btn-outline-primary" href="{{ route('dashboard.services.edit', $service->id) }}">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button class="btn btn-sm btn-outline-danger delete-btn"
                                                data-id="{{ $service->id }}"
                                                data-name="{{ $service->service_name }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">No services found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Links -->
                <div class="mt-3">
                    {{ $services->links() }}
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>

        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function() {
                const serviceId = this.getAttribute('data-id');
                const serviceName = this.getAttribute('data-name');

                Swal.fire({
                    title: 'Are you sure?',
                    html: `You are about to delete the service: <strong>${serviceName}</strong>`,
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
                        form.action = "{{ route('dashboard.services.destroy', '') }}/" + serviceId;
                        form.submit();
                    }
                });
            });
        });
    </script>
@endpush
