@extends('layouts.master.master')

@section('title', 'Inventory Transactions')

@section('content')
    <div class="container-fluid">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light d-flex justify-content-between align-items-center py-3">
                <h4 class="mb-0">Inventory Transactions</h4>
                @can('transactions.create')
                    <a href="{{ route('dashboard.inventory.inventory-transactions.create') }}" class="btn btn-dark btn-sm">
                        <i class="fas fa-plus fa-sm"></i> New Transaction
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

                <!-- Table with forced horizontal scrolling -->
                <div class="table-responsive" style="overflow-x: auto;">
                    <table class="table table-striped table-hover small" style="min-width: 1100px;">
                        <thead class="table-light">
                            <tr class="text-center">
                                <th>#</th>
                                <th>Inventory</th>
                                <th>Staff</th>
                                <th>Type</th>
                                <th>Quantity</th>
                                <th>Unit Price</th>
                                <th>Transaction Date</th>
                                <th>Notes</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($inventory_transactions as $inventory_transaction)
                                <tr class="text-center">
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $inventory_transaction->inventory->name }}</td>
                                    <td>{{ Str::ucfirst($inventory_transaction->staff->user->name) ?? '' }}</td>
                                    <td>{{ $inventory_transaction->type }}</td>
                                    <td>{{ $inventory_transaction->quantity }}</td>
                                    <td>{{ $inventory_transaction->unit_price }}</td>
                                    <td>{{ $inventory_transaction->transaction_date->format('M j, Y') }}</td>
                                    <td>{{ $inventory_transaction->notes }}</td>
                                    <td>
                                        @can('transactions.update')
                                            <a class="btn btn-outline-primary btn-sm" href="{{ route('dashboard.inventory.inventory-transactions.edit', $inventory_transaction->id) }}">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endcan
                                        @can('transactions.delete')
                                            <button class="btn btn-outline-danger btn-sm delete-btn" data-id="{{ $inventory_transaction->id }}" data-name="{{ $inventory_transaction->inventory->name }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4">No data found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="mt-3">
                    {{ $inventory_transactions->links() }}
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
    route="dashboard.inventory.inventory-transactions.destroy"
    itemName="transaction"
    deleteBtnClass="delete-btn"/>
@endpush
