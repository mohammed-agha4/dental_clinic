@extends('layouts.master.master')

@section('title', 'Trashed Patients')

@section('content')
    <div class="container-fluid">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light d-flex justify-content-between align-items-center py-3">
                <h4 class="mb-0">Trashed Patients</h4>
                <a href="{{ route('dashboard.patients.index') }}" class="btn btn-dark btn-sm">
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
                                <th>ID</th>
                                <th>Fname</th>
                                <th>Lname</th>
                                <th>Email</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($patients as $patient)
                                <tr class="text-center">
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $patient->fname }}</td>
                                    <td>{{ $patient->lname }}</td>
                                    <td>{{ $patient->email }}</td>
                                    <td>
                                        @can('patients.restore')
                                            <form action="{{ route('dashboard.patients.restore', $patient->id) }}"
                                                method="post" class="d-inline">
                                                @method('PUT')
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-trash-restore"></i> Restore
                                                </button>
                                            </form>
                                        @endcan
                                        @can('patients.force_delete')
                                            <form action="{{ route('dashboard.patients.force-delete', $patient->id) }}"
                                                method="post" class="d-inline">
                                                @method('DELETE')
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                                    onclick="return confirm('Are you sure you want to permanently delete this patient?')">
                                                    <i class="fas fa-trash-alt"></i> Delete
                                                </button>
                                            </form>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4">No trashed patients found</td>
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
    </div>
@endsection
