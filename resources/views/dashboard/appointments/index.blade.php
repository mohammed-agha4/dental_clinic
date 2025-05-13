@extends('layouts.master.master')

@section('title', 'Appointments')

@section('content')
    <div class="container-fluid">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light d-flex justify-content-between align-items-center py-3">
                <h4 class="mb-0">Appointments</h4>
                <div>
                    @can('appointments.trash')
                        <a href="{{ route('dashboard.appointments.trash') }}" class="btn btn-secondary btn-sm me-2">
                            <i class="fas fa-trash-alt fa-sm"></i> Trash
                        </a>
                    @endcan
                    @can('appointments.create')
                        <a href="{{ route('dashboard.appointments.create') }}" class="btn btn-dark btn-sm">
                            <i class="fas fa-plus fa-sm"></i> Schedule Appointment
                        </a>
                    @endcan
                </div>
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

                <div class="mb-4">
                    <form class="row g-3">
                        <div class="col-md-4">
                            <div class="input-group">
                                <input type="text" class="form-control form-control-sm" name="search"
                                    placeholder="Search patients, dentists, services..." value="{{ request('search') }}">
                                <button class="btn btn-sm btn-outline-secondary" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select form-select-sm" name="status">
                                <option value="">All Statuses</option>
                                <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Scheduled
                                </option>
                                <option value="walk_in" {{ request('status') == 'walk_in' ? 'selected' : '' }}>Walk In
                                </option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed
                                </option>
                                <option value="rescheduled" {{ request('status') == 'rescheduled' ? 'selected' : '' }}>
                                    Rescheduled</option>
                                <option value="canceled" {{ request('status') == 'canceled' ? 'selected' : '' }}>Canceled
                                </option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group">
                                <span class="input-group-text text-sm">From</span>
                                <input type="date" class="form-control form-control-sm" name="date_from"
                                    value="{{ request('date_from') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group">
                                <span class="input-group-text">To</span>
                                <input type="date" class="form-control form-control-sm" name="date_to" value="{{ request('date_to') }}">
                            </div>
                        </div>
                        <div class="col-md-12 d-flex justify-content-end">
                            <button type="submit" class="btn btn-sm btn-primary me-2">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                            <a href="{{ route('dashboard.appointments.index') }}" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-sync-alt"></i> Reset
                            </a>
                        </div>
                    </form>
                </div>

                <div class="table-responsive" style="overflow-x: auto;">
                    <table class="table table-striped table-hover small" style="min-width: 1200px;">
                        <thead class="table-light">
                            <tr class="text-center">
                                <th>#</th>
                                <th>Patient </th>
                                <th>Staff </th>
                                <th>Service</th>
                                <th>Appointment Date</th>
                                <th>Duration</th>
                                <th> Status </th>
                                <th>Reminder Sent</th>
                                @if (auth()->user()->can('appointments.show') ||
                                        auth()->user()->can('appointments.update') ||
                                        auth()->user()->can('appointments.delete'))
                                    <th>Action</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($appointments as $appointment)
                                <tr class="text-center">
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $appointment->patient->FullName }}</td>
                                    <td>{{ Str::ucfirst($appointment->dentist->user->name) ?? '' }}</td>
                                    <td>{{ $appointment->service->service_name }}</td>
                                    <td>{{ $appointment->appointment_date->format('M j, Y g:i A') ?? '' }}</td>
                                    <td>{{ $appointment->duration }} min</td>
                                    <td>{{ $appointment->status }}</td>
                                    <td>
                                        @if ($appointment->reminder_sent)
                                            <span class="badge bg-success">Yes</span>
                                        @else
                                            <span class="badge bg-secondary">No</span>
                                        @endif
                                    </td>
                                    @if (auth()->user()->can('appointments.show') ||
                                            auth()->user()->can('appointments.update') ||
                                            auth()->user()->can('appointments.delete'))
                                        <td>
                                            @can('visits.create')
                                                <a class="btn btn-outline-success btn-sm"
                                                    href="{{ route('dashboard.visits.create', $appointment->id) }}">
                                                    Record Visit
                                                </a>
                                            @endcan
                                            @can('appointments.show')
                                                <a href="{{ route('dashboard.appointments.show', $appointment->id) }}"
                                                    class="btn btn-sm btn-outline-success">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            @endcan

                                            @can('appointments.update')
                                                <a class="btn btn-outline-primary btn-sm"
                                                    href="{{ route('dashboard.appointments.edit', $appointment->id) }}">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endcan
                                            @can('appointments.delete')
                                                <button class="btn btn-outline-danger btn-sm delete-btn"
                                                    data-id="{{ $appointment->id }}"
                                                    data-name="{{ $appointment->patient->fname }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @endcan
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center py-4">No appointments found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $appointments->withQueryString()->links() }}
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
        route="dashboard.appointments.destroy"
        itemName="appointment"
        deleteBtnClass="delete-btn"
    />
@endpush
