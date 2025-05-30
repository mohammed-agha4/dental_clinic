@extends('layouts.master.master')

@section('title', 'Visits')

@section('content')
    <div class="container-fluid">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light d-flex justify-content-between align-items-center py-3">
                <h4 class="mb-0">Visits</h4>
                @can('visits.trash')
                    <a href="{{ route('dashboard.visits.trash') }}" class="btn btn-secondary btn-sm me-2">
                        <i class="fas fa-trash-alt fa-sm"></i> Trash
                    </a>
                @endcan
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
                <table class="table small table-striped" style="min-width: 1000px;">
                    <thead>
                        <tr class="text-center">
                            <th>#</th>
                            <th>Appointment</th>
                            <th>Status</th>
                            <th>Patient</th>
                            <th>Staff</th>
                            <th>Service</th>
                            <th>Visit Date</th>
                            <th>Tools</th>
                            @if (auth()->user()->can('visits.show') || auth()->user()->can('visits.update') || auth()->user()->can('visits.delete'))
                                <th>Action</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($visits as $v)
                            <tr class="text-center">
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $v->appointment->appointment_date->format('M j, Y g:i A') ?? 'Not Recorded' }}</td>
                                <td>{{ $v->appointment->status }}</td>
                                <td>{{ $v->patient->FullName }}</td>
                                <td>{{ ucfirst($v->staff->user->name) }}</td>
                                <td>{{ $v->service->service_name }}</td>
                                <td>{{ $v->visit_date->format('M j, Y g:i A') }}</td>
                                <td>
                                    @if ($v->inventoryItems->isNotEmpty())
                                        <ol style="margin: 0; padding: 0; list-style: none;">
                                            @foreach ($v->inventoryItems as $inventoryItem)
                                                <li>
                                                    {{ $inventoryItem->name }}
                                                    ({{ $inventoryItem->pivot->quantity_used }})
                                                </li>
                                            @endforeach
                                        </ol>
                                    @else
                                        No items used
                                    @endif
                                </td>
                                @if (auth()->user()->can('visits.show') || auth()->user()->can('visits.update') || auth()->user()->can('visits.delete'))
                                    <td>
                                        @can('visits.show')
                                            <a href="{{ route('dashboard.visits.show', $v->id) }}"
                                                class="btn btn-sm btn-outline-success">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        @endcan
                                        @can('visits.update')
                                            <a class="btn btn-outline-primary btn-sm"
                                                href="{{ route('dashboard.visits.edit', $v->id) }}">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endcan
                                        @can('visits.delete')
                                            <button class="btn btn-outline-danger btn-sm delete-btn"
                                                data-id="{{ $v->id }}" data-name="{{ $v->patient->FullName }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endcan
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    @if (auth()->user()->hasAbility('view-own-visits') && !auth()->user()->hasAbility('view-all-visits'))
                                        <p>No visits scheduled for you</p>
                                    @else
                                        <p>No visits found</p>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Links -->
            <div class="mt-3">
                {{ $visits->links() }}
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
        route="dashboard.visits.destroy"
        itemName="visit"
        deleteBtnClass="delete-btn"
    />
@endpush
