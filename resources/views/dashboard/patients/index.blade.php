@extends('layouts.master.master')

@section('title', 'Patients')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light d-flex justify-content-between align-items-center py-3">
            <h4 class="mb-0">Patients</h4>
            @can('patients.trash')
                <a href="{{ route('dashboard.patients.trash') }}" class="btn btn-secondary btn-sm me-2">
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

        <div class="table-responsive" style="overflow-x: auto;">
            <table class="table small table-striped " style="min-width: 1000px;">
                <thead>
                    <tr class="text-center">
                        <th>#</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Gender</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>DOB</th>
                        <th>Address</th>
                        @if (auth()->user()->can('patients.show') || auth()->user()->can('patients.update') || auth()->user()->can('patients.delete'))
                            <th>Action</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse ($patients as $patient)
                        <tr class="text-center">
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ Str::ucfirst($patient->fname) }}</td>
                            <td>{{ Str::ucfirst($patient->lname) }}</td>
                            <td>{{ $patient->gender }}</td>
                            <td title="{{ $patient->email }}">{{ Str::limit($patient->email, 15) }}</td>
                            <td>{{ $patient->phone }}</td>
                            <td>{{ $patient->DOB->format('M j, Y') }}</td>
                            <td>{{ Str::limit($patient->address, 20) }}</td>
                            @if (auth()->user()->can('patients.show') || auth()->user()->can('patients.update') || auth()->user()->can('patients.delete'))
                                <td>
                                    <div class="d-flex justify-content-center gap-1">
                                        @can('patients.show')
                                            <a class="btn btn-sm btn-outline-success"
                                                href="{{ route('dashboard.patients.show', $patient->id) }}">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        @endcan
                                        @can('patients.update')
                                            <a class="btn btn-sm btn-outline-primary"
                                                href="{{ route('dashboard.patients.edit', $patient->id) }}">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endcan
                                        @can('patients.delete')
                                            <button class="btn btn-sm btn-outline-danger delete-btn"
                                                data-id="{{ $patient->id }}"
                                                data-name="{{ $patient->fname }} {{ $patient->lname }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endcan
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                @if (auth()->user()->hasAbility('view-own-patients') && !auth()->user()->hasAbility('view-all-patients'))
                                    <p>No patients assigned to you</p>
                                @else
                                    <p>No data found</p>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $patients->links() }}
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
        route="dashboard.patients.destroy"
        itemName="patient"
        deleteBtnClass="delete-btn"
    />
@endpush
