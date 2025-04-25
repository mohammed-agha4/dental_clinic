@extends('layouts.master.master')

@section('title', 'Inventory')

@section('content')
    <div class="container-fluid">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light d-flex justify-content-between align-items-center py-3">
                <h4 class="mb-0">Inventory</h4>
                <div>
                    @can('inventory.trash')
                        <a href="{{ route('dashboard.inventory.inventory.trash') }}" class="btn btn-secondary btn-sm me-2">
                            <i class="fas fa-trash-alt fa-sm"></i> Trash
                        </a>
                    @endcan
                    @can('inventory.create')
                        <a href="{{ route('dashboard.inventory.inventory.create') }}" class="btn btn-dark btn-sm">
                            <i class="fas fa-plus fa-sm"></i> New Tool
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

                <div class="table-responsive">
                    <table class="table table-striped table-hover small">
                        <thead class="table-light">
                            <tr class="text-center">
                                <th>ID</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Supplier</th>
                                <th>SKU</th>
                                <th>Quantity</th>
                                <th>Reorder Level</th>
                                <th>Unit Price</th>
                                <th>Expiry Date</th>
                                <th>Status</th>
                                @if (auth()->user()->can('inventory.show') ||
                                        auth()->user()->can('inventory.update') ||
                                        auth()->user()->can('inventory.delete'))
                                    <th>Action</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($inventory as $inv)
                                <tr class="text-center">
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $inv->name }}</td>
                                    <td>{{ $inv->category->name }}</td>
                                    <td>{{ Str::ucfirst($inv->supplier->company_name) }}</td>
                                    <td>{{ $inv->SKU }}</td>
                                    <td>{{ $inv->quantity }}</td>
                                    <td>{{ $inv->reorder_level }}</td>
                                    <td>{{ $inv->unit_price }}</td>
                                    <td>{{ $inv->expiry_date->format('M j, Y') }}</td>
                                    <td>
                                        <span
                                            class="{{ $inv->is_active ? 'bg-success' : 'bg-danger' }} text-white py-1 px-2 rounded">
                                            {{ $inv->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    @if (auth()->user()->can('inventory.show') ||
                                            auth()->user()->can('inventory.update') ||
                                            auth()->user()->can('inventory.delete'))
                                        <td>
                                            @can('inventory.show')
                                                <a href="{{ route('dashboard.inventory.inventory.show', $inv->id) }}"
                                                    class="btn btn-sm btn-outline-success">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            @endcan
                                            @can('inventory.update')
                                                <a class="btn btn-outline-primary btn-sm"
                                                    href="{{ route('dashboard.inventory.inventory.edit', $inv->id) }}">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endcan
                                            @can('inventory.delete')
                                                <button class="btn btn-outline-danger btn-sm delete-btn"
                                                    data-id="{{ $inv->id }}" data-name="{{ $inv->name }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @endcan
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ auth()->user()->can('inventory.show') || auth()->user()->can('inventory.update') || auth()->user()->can('inventory.delete') ? 11 : 10 }}"
                                        class="text-center py-4">No data found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $inventory->links() }}
                </div>
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
        // Setup delete confirmation with SweetAlert
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function() {
                const inventoryId = this.getAttribute('data-id');
                const inventoryName = this.getAttribute('data-name');

                Swal.fire({
                    title: 'Are you sure?',
                    html: `You are about to delete the inventory item: <strong>${inventoryName}</strong>`,
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
                        form.action = "{{ route('dashboard.inventory.inventory.destroy', '') }}/" +
                            inventoryId;
                        form.submit();
                    }
                });
            });
        });
    </script>
@endpush
