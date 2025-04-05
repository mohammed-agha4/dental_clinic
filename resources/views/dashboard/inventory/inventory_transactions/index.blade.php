@extends('layouts.master.master')

@section('title', 'Inventory Transactions')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light d-flex justify-content-between align-items-center py-3">
            <h4 class="mb-0">Inventory Transactions</h4>
            <a href="{{ route('dashboard.inventory.inventory-transactions.create') }}" class="btn btn-dark btn-sm">
                <i class="fas fa-plus fa-sm"></i> New Transaction
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
                <table class="table table-striped table-hover small ">
                    <thead class="table-light">
                        <tr class="text-center">
                            <th>ID</th>
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
                                <td>{{ $inventory_transaction->staff->user->name  ?? '' }}</td>
                                <td>{{ $inventory_transaction->type }}</td>
                                <td>{{ $inventory_transaction->quantity }}</td>
                                <td>{{ $inventory_transaction->unit_price }}</td>
                                <td>{{ $inventory_transaction->transaction_date }}</td>
                                <td>{{ $inventory_transaction->notes }}</td>
                                <td>
                                    <a class="btn btn-outline-primary btn-sm" href="{{ route('dashboard.inventory.inventory-transactions.edit', $inventory_transaction->id) }}">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button class="btn btn-outline-danger btn-sm delete-btn" data-id="{{ $inventory_transaction->id }}" data-name="{{ $inventory_transaction->inventory->name }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
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
            {{ $inventory_transactions->links() }}
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

<script>
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function() {
            const transactionId = this.getAttribute('data-id');
            const transactionName = this.getAttribute('data-name');

            Swal.fire({
                title: 'Are you sure?',
                html: `You are about to delete the transaction for: <strong>${transactionName}</strong>`,
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
                    form.action = "{{ route('dashboard.inventory.inventory-transactions.destroy', '') }}/" + transactionId;
                    form.submit();
                }
            });
        });
    });
</script>
@endpush
