@extends('layouts.master.master')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-800">Inventory Item Details</h1>
            <div class="d-flex justify-content-between align-items-center my-2">
                <div>
                    <a href="{{ route('dashboard.inventory.inventory.edit', $inventory->id) }}" class="btn btn-primary">
                        Edit
                    </a>

                    <button type="button" class="btn btn-danger delete-btn" data-id="{{ $inventory->id }}"
                        data-name="{{ $inventory->name }}">
                        Delete
                    </button>
                </div>

                <div>
                    <a href="{{ route('dashboard.inventory.inventory.index') }}" class="btn btn-dark">
                        Back to Inventory
                    </a>
                </div>
            </div>
        </div>

        <!-- Hidden Delete Form -->
        <form id="delete-form" method="POST" style="display: none;">
            @csrf
            @method('DELETE')
        </form>




        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="p-6">
                <!-- Basic Information -->
                <div class="d-flex md:grid-cols-2 gap-6 mb-6">
                    <!-- Item Details -->
                    <div class="border rounded-lg p-4 w-50">
                        <h2 class="text-lg font-semibold mb-4 text-gray-700 border-b pb-2">Item Information</h2>
                        <div class="space-y-3">
                            <p><span class="font-medium">Name:</span> {{ $inventory->name }}</p>
                            <p><span class="font-medium">SKU:</span> <span
                                    class="font-mono bg-gray-100 px-2 py-1 rounded">{{ $inventory->SKU }}</span></p>
                            <p><span class="font-medium">Description:</span> {{ $inventory->description ?? 'N/A' }}</p>
                            <p>
                                <span class="font-medium">Status:</span>
                                <span
                                    class="px-2 py-1 rounded-full text-xs {{ $inventory->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $inventory->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </p>
                        </div>
                    </div>

                    <!-- Stock Details -->
                    <div class="border rounded-lg p-4 w-50">
                        <h2 class="text-lg font-semibold mb-4 text-gray-700 border-b pb-2">Stock Information</h2>
                        <div class="space-y-3">
                            <p>
                                <span class="font-medium">Quantity:</span>
                                <span
                                    class="{{ $inventory->quantity <= $inventory->reorder_level ? 'text-red-600 font-bold' : 'text-gray-800' }}">
                                    {{ $inventory->quantity }}
                                    @if ($inventory->quantity <= $inventory->reorder_level)
                                        <span class="text-xs text-red-500 ml-1">(Low stock!)</span>
                                    @endif
                                </span>
                            </p>
                            <p><span class="font-medium">Reorder Level:</span> {{ $inventory->reorder_level }}</p>
                            <p><span class="font-medium">Unit Price:</span> ${{ number_format($inventory->unit_price, 2) }}
                            </p>
                            <p><span class="font-medium">Expiry Date:</span>
                                {{ $inventory->expiry_date ? $inventory->expiry_date : 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Related Information -->
                <div class="d-flex md:grid-cols-2 gap-6 mb-6">
                    <!-- Category Information -->
                    <div class="border rounded-lg p-4 w-50">
                        <h2 class="text-lg font-semibold mb-4 text-gray-700 border-b pb-2">Category Information</h2>
                        @if ($inventory->category)
                            <div class="space-y-2">
                                <p><span class="font-medium">Name:</span> {{ $inventory->category->name }}</p>
                                <p><span class="font-medium">Description:</span>
                                    {{ $inventory->category->description ?? 'N/A' }}</p>
                            </div>
                        @else
                            <p class="text-gray-500">No category assigned</p>
                        @endif
                    </div>

                    <!-- Supplier Information -->
                    <div class="border rounded-lg p-4 w-50">
                        <h2 class="text-lg font-semibold mb-4 text-gray-700 border-b pb-2">Supplier Information</h2>
                        @if ($inventory->supplier)
                            <div class="space-y-2">
                                <p><span class="font-medium">Name:</span> {{ $inventory->supplier->name }}</p>
                                <p><span class="font-medium">Contact:</span>
                                    {{ $inventory->supplier->contact_person ?? 'N/A' }}</p>
                                <p><span class="font-medium">Phone:</span> {{ $inventory->supplier->phone ?? 'N/A' }}</p>
                                <p><span class="font-medium">Email:</span> {{ $inventory->supplier->email ?? 'N/A' }}</p>
                            </div>
                        @else
                            <p class="text-gray-500">No supplier assigned</p>
                        @endif
                    </div>
                </div>

                <!-- Additional Information -->
                <div class="border rounded-lg p-4">
                    <h2 class="text-lg font-semibold mb-4 text-gray-700 border-b pb-2">Additional Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <p><span class="font-medium">Created At:</span>
                                {{ $inventory->created_at->format('M d, Y H:i') }}</p>
                        </div>
                        <div>
                            <p><span class="font-medium">Updated At:</span>
                                {{ $inventory->updated_at->format('M d, Y H:i') }}</p>
                        </div>
                        <div>
                            <p><span class="font-medium">Deleted At:</span>
                                {{ $inventory->deleted_at ? $inventory->deleted_at->format('M d, Y H:i') : 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <!-- Include SweetAlert Library -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
