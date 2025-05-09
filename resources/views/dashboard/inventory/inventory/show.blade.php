@extends('layouts.master.master')
@section('title', 'Tool Information')

@section('content')
    <div class="container-fluid">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h5>Inventory Item Details</h5>
                <div>
                    @can('inventory.update')
                        <a href="{{ route('dashboard.inventory.inventory.edit', $inventory->id) }}"
                            class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-edit fa-sm"></i> Edit
                        </a>
                    @endcan
                    @can('inventory.delete')
                        <button type="button" class="btn btn-outline-danger btn-sm delete-btn" data-id="{{ $inventory->id }}"
                            data-name="{{ $inventory->name }}">
                            <i class="fas fa-trash fa-sm"></i> Delete
                        </button>
                    @endcan
                    <a href="{{ route('dashboard.inventory.inventory.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left fa-sm"></i> Back
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb-4 h-100">
                            <div class="card-header">
                                <h6 class="m-0 font-weight-bold text-success">Item Information</h6>
                            </div>
                            <div class="card-body">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td class="p-1 font-weight-bold">Name:</td>
                                        <td class="p-1">{{ $inventory->name }}</td>
                                    </tr>
                                    <tr>
                                        <td class="p-1 font-weight-bold">SKU:</td>
                                        <td class="p-1"> {{ $inventory->SKU }} </td>
                                    </tr>
                                    <tr>
                                        <td class="p-1 font-weight-bold">Description:</td>
                                        <td class="p-1">{{ $inventory->description ?? 'No Description' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="p-1 font-weight-bold">Status:</td>
                                        <td class="p-1">
                                            <span class="badge {{ $inventory->is_active ? 'bg-success' : 'bg-danger' }}">
                                                {{ $inventory->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card mb-4 h-100">
                            <div class="card-header">
                                <h6 class="m-0 font-weight-bold text-success">Stock Information</h6>
                            </div>
                            <div class="card-body">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td class="p-1 font-weight-bold">Quantity:</td>
                                        <td
                                            class="p-1 {{ $inventory->quantity <= $inventory->reorder_level ? 'text-danger font-weight-bold' : '' }}">
                                            {{ $inventory->quantity }}
                                            @if ($inventory->quantity <= $inventory->reorder_level)
                                                <span class="small">(Low stock!)</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="p-1 font-weight-bold">Reorder Level:</td>
                                        <td class="p-1">{{ $inventory->reorder_level }}</td>
                                    </tr>
                                    <tr>
                                        <td class="p-1 font-weight-bold">Unit Price:</td>
                                        <td class="p-1">${{ number_format($inventory->unit_price, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="p-1 font-weight-bold">Expiry Date:</td>
                                        <td class="p-1">{{ $inventory->expiry_date->format('M j, Y g: i A') }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb-4 h-100">
                            <div class="card-header">
                                <h6 class="m-0 font-weight-bold text-success">Category Information</h6>
                            </div>
                            <div class="card-body">
                                @if ($inventory->category)
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <td class="p-1 font-weight-bold">Name:</td>
                                            <td class="p-1">{{ $inventory->category->name ?? 'No Category' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="p-1 font-weight-bold">Description:</td>
                                            <td class="p-1">{{ $inventory->category->description ?? 'No Description' }}
                                            </td>
                                        </tr>
                                    </table>
                                @else
                                    <p class="small text-muted">No category assigned</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card mb-4 h-100">
                            <div class="card-header">
                                <h6 class="m-0 font-weight-bold text-success">Supplier Information</h6>
                            </div>
                            <div class="card-body">
                                @if ($inventory->supplier->id)
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <td class="p-1 font-weight-bold">Name:</td>
                                            <td class="p-1">{{ $inventory->supplier->contact_name ?? 'Not Recorded' }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="p-1 font-weight-bold">Company:</td>
                                            <td class="p-1">{{ $inventory->supplier->company_name ?? 'Not Recorded' }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="p-1 font-weight-bold">Phone:</td>
                                            <td class="p-1">{{ $inventory->supplier->phone ?? 'Not Recorded' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="p-1 font-weight-bold">Email:</td>
                                            <td class="p-1">{{ $inventory->supplier->email ?? 'Not Recorded' }}</td>
                                        </tr>
                                    </table>
                                @else
                                    <p class="small text-muted">No supplier assigned</p>
                                @endif
                            </div>
                        </div>
                    </div>
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
    <script>
        document.querySelector('.delete-btn').addEventListener('click', function() {
            const inventoryId = this.getAttribute('data-id');
            const inventoryName = this.getAttribute('data-name');

            Swal.fire({
                title: 'Are you sure?',
                html: `You are about to delete: <strong>${inventoryName}</strong>`,
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
    </script>
@endpush
