@extends('layouts.master.master')
@section('title', 'Staff Details')

@section('content')
    <div class="container-fluid">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h5>Staff Details</h5>
                <div>
                    @can('staff.update')
                        <a href="{{ route('dashboard.staff.edit', $staff->id) }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-edit fa-sm"></i> Edit
                        </a>
                    @endcan
                    @can('staff.delete')
                        <button type="button" class="btn btn-outline-danger btn-sm delete-btn" data-id="{{ $staff->id }}"
                            data-name="{{ $staff->user->name }}">
                            <i class="fas fa-trash fa-sm"></i> Delete
                        </button>
                    @endcan
                    <a href="{{ route('dashboard.staff.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left fa-sm"></i> Back
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Personal Information -->
                    <div class="col-md-6">
                        <div class="card mb-4 h-100">
                            <div class="card-header bg-light">
                                <h6 class="m-0 font-weight-bold text-primary">Personal Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="text-center mb-3">
                                    @if ($staff->user->profile->profile_photo)
                                        <img src="{{ asset('storage/' . $staff->user->profile->profile_photo) }}"
                                            class="rounded-circle object-fit-cover" height="150" width="150" alt="Profile Photo">
                                    @else
                                        <div class="bg-secondary rounded-circle d-inline-flex align-items-center justify-content-center"
                                            style="width: 150px; height: 150px;">
                                            <i class="fas fa-user text-white" style="font-size: 3rem;"></i>
                                        </div>
                                    @endif
                                </div>
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td class="p-1 font-weight-bold" width="40%">Name:</td>
                                        <td class="p-1">{{ $staff->user->name }}</td>
                                    </tr>
                                    <tr>
                                        <td class="p-1 font-weight-bold">Email:</td>
                                        <td class="p-1">{{ $staff->user->email }}</td>
                                    </tr>
                                    <tr>
                                        <td class="p-1 font-weight-bold">Phone:</td>
                                        <td class="p-1">{{ $staff->phone ?? 'Not Recorded' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="p-1 font-weight-bold">Role:</td>
                                        <td class="p-1"> {{ ucfirst($staff->role) }} </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Professional Information -->
                    <div class="col-md-6">
                        <div class="card mb-4 h-100">
                            <div class="card-header bg-light">
                                <h6 class="m-0 font-weight-bold text-primary">Professional Information</h6>
                            </div>
                            <div class="card-body">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td class="p-1 font-weight-bold" width="40%">Department:</td>
                                        <td class="p-1">{{ $staff->department ?? 'Not Recorded' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="p-1 font-weight-bold">License Number:</td>
                                        <td class="p-1">{{ $staff->license_number ?? 'Not Recorded' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="p-1 font-weight-bold">Working Hours:</td>
                                        <td class="p-1">{{ $staff->working_hours ?? 'Not Recorded' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="p-1 font-weight-bold">Status:</td>
                                        <td class="p-1">
                                            <span class="badge {{ $staff->is_active ? 'bg-success' : 'bg-secondary' }}">
                                                {{ $staff->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="p-1 font-weight-bold">Member Since:</td>
                                        <td class="p-1">{{ $staff->created_at->format('d M Y') }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Assigned Services Section -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="card mb-4">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                <h6 class="m-0 font-weight-bold text-primary">Assigned Services</h6>
                                {{-- @can('service_staff.create')
                                    <a href="{{ route('dashboard.service-staff.create', ['staff_id' => $staff->id]) }}"
                                        class="btn btn-sm btn-primary">
                                        <i class="fas fa-plus"></i> Assign Services
                                    </a>
                                @endcan --}}
                            </div>
                            <div class="card-body">
                                @if ($staff->services->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th>#</th>
                                                    <th>Service Name</th>
                                                    <th>Duration</th>
                                                    <th>Price</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($staff->services as $service)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>{{ $service->service_name }}</td>
                                                        <td>{{ $service->duration }} minutes</td>
                                                        <td>${{ number_format($service->service_price, 2) }}</td>
                                                        <td>
                                                            @can('service_staff.delete')
                                                                <form
                                                                    action="{{ route('dashboard.service-staff.destroy', $service->id) }}"
                                                                    method="POST" style="display: inline-block;">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-sm btn-outline-danger"
                                                                        onclick="return confirm('Are you sure you want to remove this service assignment?')">
                                                                        <i class="fas fa-trash-alt"></i>
                                                                    </button>
                                                                </form>
                                                            @endcan
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="alert alert-info mb-0 text-center">
                                        This staff member has no assigned services.
                                    </div>
                                @endif
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
        document.querySelector('.delete-btn')?.addEventListener('click', function() {
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
    </script>
@endpush
