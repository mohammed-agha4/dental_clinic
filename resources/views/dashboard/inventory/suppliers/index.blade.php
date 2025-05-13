@extends('layouts.master.master')

@section('title', 'Suppliers')

@section('content')
    <div class="container-fluid">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light py-3 d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Suppliers</h4>
                @can('suppliers.create')
                    <a href="{{ route('dashboard.inventory.suppliers.create') }}" class="btn btn-dark btn-sm">
                        <i class="fas fa-plus fa-sm"></i> New Supplier
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


            <!-- Search and Filter Section -->
            <div class="card-body border-bottom">
                <form class="row g-3">
                    <div class="col-md-8">
                        <div class="input-group">
                            <input type="text" class="form-control form-control-sm" name="search"
                                placeholder="Search suppliers by company, contact, email"
                                value="{{ request('search') }}">
                            <button class="btn btn-outline-secondary" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-4 d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-filter"></i> Search
                        </button>
                        <a href="{{ route('dashboard.inventory.suppliers.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-sync-alt"></i> Reset
                        </a>
                    </div>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table small table-striped table-hover">
                    <thead class="table-light">
                        <tr class="text-center">
                            <th>#</th>
                            <th>Company Name</th>
                            <th>Contact Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Address</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($suppliers as $supplier)
                            <tr class="text-center">
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $supplier->company_name }}</td>
                                <td>{{ $supplier->contact_name }}</td>
                                <td>{{ $supplier->email }}</td>
                                <td>{{ $supplier->phone }}</td>
                                <td>{{ Str::limit($supplier->address, 30) }}</td>
                                <td>
                                    @can('suppliers.update')
                                        <a class="btn btn-outline-primary btn-sm"
                                            href="{{ route('dashboard.inventory.suppliers.edit', $supplier->id) }}">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endcan
                                    @can('suppliers.delete')
                                        <button class="btn btn-outline-danger btn-sm delete-btn" data-id="{{ $supplier->id }}"
                                            data-name="{{ $supplier->company_name }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">No suppliers found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Links -->
            <div class="card-footer">
                {{ $suppliers->withQueryString()->links() }}
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
    route="dashboard.inventory.suppliers.destroy"
    itemName="supplier"
    deleteBtnClass="delete-btn"/>
@endpush
