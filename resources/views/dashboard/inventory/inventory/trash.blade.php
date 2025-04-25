@extends('layouts.master.master')
@section('title', 'Trashed Inventory Tools')

@section('content')
    <div class="container-fluid">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light d-flex justify-content-between align-items-center py-3">
                <h4 class="mb-0">Trashed Inventory Tools</h4>
                <a href="{{ route('dashboard.inventory.inventory.index') }}" class="btn btn-dark btn-sm">
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
                                <th>Name</th>
                                <th>SKU</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($inventories as $inv)
                                <tr class="text-center">
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $inv->name }}</td>
                                    <td>{{ $inv->SKU }}</td>
                                    <td>
                                        @can('inventory.restore')
                                            <form action="{{ route('dashboard.inventory.inventory.restore', $inv->id) }}"
                                                method="post" class="d-inline">
                                                @method('PUT')
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-trash-restore"></i> Restore
                                                </button>
                                            </form>
                                        @endcan
                                        @can('inventory.force_delete')
                                            <form action="{{ route('dashboard.inventory.inventory.force-delete', $inv->id) }}"
                                                method="post" class="d-inline">
                                                @method('DELETE')
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                                    onclick="return confirm('Are you sure you want to permanently delete this tool?')">
                                                    <i class="fas fa-trash-alt"></i> Delete
                                                </button>
                                            </form>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4">No trashed Tools found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $inventories->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
