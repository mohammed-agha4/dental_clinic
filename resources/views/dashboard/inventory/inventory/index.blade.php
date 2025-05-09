@extends('layouts.master.master')

@section('title', 'Inventory')

@section('content')
    <div class="container-fluid">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light d-flex justify-content-between align-items-center py-3">
                <h4 class="mb-0">Inventory</h4>
                <div class="d-flex flex-wrap gap-2">
                    @can('inventory.trash')
                        <a href="{{ route('dashboard.inventory.inventory.trash') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-trash-alt fa-sm"></i> Trash
                        </a>
                    @endcan
                    @can('inventory.create')
                        <a href="{{ route('dashboard.inventory.inventory.create') }}" class="btn btn-dark btn-sm">
                            <i class="fas fa-plus fa-sm"></i> New Item
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
                    <div id="flash-msg" class="alert alert-danger alert-dismissible fade show">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif


                <div class="mb-4">
                    <form class="row g-2 g-md-3 align-items-center">
                        <div class="col-12 col-md-4">
                            <div class="input-group">
                                <input type="text" class="form-control form-control-sm" name="search"
                                    placeholder="Search by name, SKU." value="{{ request('search') }}">
                                <button class="btn btn-outline-secondary btn-sm" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-6 col-md-2">
                            <select class="form-select form-select-sm" name="status">
                                <option value="">All Statuses</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive
                                </option>
                            </select>
                        </div>
                        <div class="col-6 col-md-2">
                            <select class="form-select form-select-sm" name="stock">
                                <option value="">All Stock</option>
                                <option value="low" {{ request('stock') == 'low' ? 'selected' : '' }}>Low Stock</option>
                                <option value="out" {{ request('stock') == 'out' ? 'selected' : '' }}>Out of Stock
                                </option>
                            </select>
                        </div>
                        <div class="col-12 col-md-4 d-flex justify-content-md-end gap-2 mt-2 mt-md-0">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                            <a href="{{ route('dashboard.inventory.inventory.index') }}"
                                class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-sync-alt"></i> Reset
                            </a>
                        </div>
                    </form>
                </div>

                <div class="table-responsive" style="overflow-x: auto;">
                    <table class="table table-striped table-hover small" style="min-width: 1100px;">
                        <thead class="table-light">
                            <tr class="text-center">
                                <th>#</th>
                                <th>Item Name</th>
                                <th>Category</th>
                                <th>Supplier</th>
                                <th>SKU</th>
                                <th>Qty</th>
                                <th>Reorder</th>
                                <th>Price</th>
                                <th>Expiry</th>
                                <th>Status</th>
                                @if (auth()->user()->can('inventory.show') ||
                                        auth()->user()->can('inventory.update') ||
                                        auth()->user()->can('inventory.delete'))
                                    <th>Actions</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($inventory as $inv)
                                <tr class="text-center">
                                    <td>{{ $loop->iteration }}</td>
                                    <td class="text-start">{{ Str::limit($inv->name, 20) }}</td>
                                    <td>{{ $inv->category->name }}</td>
                                    <td>{{ Str::limit($inv->supplier->company_name, 15) }}</td>
                                    <td>{{ $inv->SKU }}</td>
                                    <td class="{{ $inv->quantity <= $inv->reorder_level ? 'text-danger fw-bold' : '' }}">
                                        {{ $inv->quantity }}
                                    </td>
                                    <td>{{ $inv->reorder_level }}</td>
                                    <td>{{ number_format($inv->unit_price, 2) }}</td>
                                    <td
                                        class="{{ $inv->expiry_date && $inv->expiry_date->isPast() ? 'text-danger' : '' }}">
                                        {{ $inv->expiry_date ? $inv->expiry_date->format('M j, Y') : 'Not Recorded' }}
                                    </td>
                                    <td>
                                        <span class="badge {{ $inv->is_active ? 'bg-success' : 'bg-danger' }}">
                                            {{ $inv->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    @if (auth()->user()->can('inventory.show') ||
                                            auth()->user()->can('inventory.update') ||
                                            auth()->user()->can('inventory.delete'))
                                        <td>
                                            <div class="d-flex justify-content-center gap-1">
                                                @can('inventory.show')
                                                    <a href="{{ route('dashboard.inventory.inventory.show', $inv->id) }}"
                                                        class="btn btn-sm btn-outline-success" title="View">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                @endcan
                                                @can('inventory.update')
                                                    <a href="{{ route('dashboard.inventory.inventory.edit', $inv->id) }}"
                                                        class="btn btn-sm btn-outline-primary" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endcan
                                                @can('inventory.delete')
                                                    <button class="btn btn-sm btn-outline-danger delete-btn"
                                                        data-id="{{ $inv->id }}" data-name="{{ $inv->name }}"
                                                        title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                @endcan
                                            </div>
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11" class="text-center py-4">
                                        <h5>No inventory items found</h5>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $inventory->withQueryString()->links() }}
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
    route="dashboard.inventory.inventory.destroy"
    deleteBtnClass="delete-btn"/>
@endpush
