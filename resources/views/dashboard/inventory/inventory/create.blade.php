@extends('layouts.master.master')

@section('title', 'Insert Tool')

@section('content')
    <div class="container-fluid">
        <div class="card shadow-sm mb-4">

            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h5>Insert Tool</h5>
                <a href="{{ route('dashboard.inventory.inventory.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left fa-sm"></i> Back to Main
                </a>
            </div>


            <div class="card-body">
                <form action="{{ route('dashboard.inventory.inventory.store') }}" method="post">


                    @csrf

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="form-group">

                                <label>Tool Name:</label>
                                <input type="text" name="name"
                                    class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}"
                                    required>
                                @error('name')
                                    <small class="text-danger alert-danger">{{ $message }}</small>
                                @enderror

                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Category:</label>
                                <select name="category_id" class="form-control @error('category_id') is-invalid @enderror">
                                    <option selected disabled>--select--</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Supplier:</label>
                                <select name="supplier_id" class="form-control @error('supplier_id') is-invalid @enderror">
                                    <option selected disabled>--select--</option>
                                    @foreach ($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}" @selected(old('supplier_id') == $supplier->id)>
                                            {{ $supplier->company_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('supplier_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">

                                <label>SKU:</label>
                                <input type="text" name="SKU" class="form-control @error('SKU') is-invalid @enderror"
                                    value="{{ old('SKU') }}" required>
                                @error('SKU')
                                    <small class="text-danger alert-danger">{{ $message }}</small>
                                @enderror

                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="form-group">
                                <x-form.textarea label='Description:' name='description' placeholder="Enter description" />
                                @error('description')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="form-group">

                                <label>Quantity:</label>
                                <input type="number" name="quantity"
                                    class="form-control @error('quantity') is-invalid @enderror"
                                    value="{{ old('quantity') }}" required>
                                @error('quantity')
                                    <small class="text-danger alert-danger">{{ $message }}</small>
                                @enderror

                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">

                                <label>Reorder Level:</label>
                                <input type="number" name="reorder_level"
                                    class="form-control @error('reorder_level') is-invalid @enderror"
                                    value="{{ old('reorder_level') }}" required>
                                @error('reorder_level')
                                    <small class="text-danger alert-danger">{{ $message }}</small>
                                @enderror

                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">

                                <label>Unit Price:</label>
                                <input type="number" name="unit_price"
                                    class="form-control @error('unit_price') is-invalid @enderror"
                                    value="{{ old('unit_price') }}" required>
                                @error('unit_price')
                                    <small class="text-danger alert-danger">{{ $message }}</small>
                                @enderror

                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">

                            <div class="form-group">

                                <label>Expiry Date</label>
                                <input type="date" class="form-control date @error('expiry_date') is-invalid @enderror"
                                    name='expiry_date' value="{{ old('expiry_date') }}" placeholder='Expiry Date' required>
                                @error('expiry_date')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror

                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Active:</label>
                                <select name="is_active" class="form-control @error('is_active') is-invalid @enderror">
                                    <option value="1">Active</option>
                                    <option value="0">Not Active</option>
                                </select>
                                @error('is_active')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Initial Purchase Transaction Section -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h4 class="mb-0">Initial Purchase Transaction</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Transaction Date</label>
                                        <input type="date"
                                            class="form-control date @error('transaction_date') is-invalid @enderror"
                                            name="transaction_date" value="{{ date('Y-m-d') }}" required>
                                        @error('transaction_date')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-12 mt-3">
                                    <div class="form-group">
                                        <label>Transaction Notes</label>
                                        <textarea class="form-control @error('transaction_notes') is-invalid @enderror" name="transaction_notes" rows="2"
                                            placeholder="Initial purchase notes"></textarea>
                                        @error('transaction_notes')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Transactions Section -->
                    <div class="card mb-4">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                            <h4 class="mb-0">Additional Inventory Transactions</h4>
                            <button type="button" class="btn btn-sm btn-primary" id="addTransactionBtn">
                                <i class="fas fa-plus"></i> Add Transaction
                            </button>
                        </div>
                        <div class="card-body">
                            <div id="transactionsContainer">
                                <!-- Dynamic transactions will be added here -->
                            </div>
                        </div>
                    </div>

                    <!-- Transaction Template -->
                    <template id="transactionTemplate">
                        <div class="transaction-item border p-3 mb-3">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Transaction Type</label>
                                        <select class="form-control" name="transaction_type[]">
                                            <option value="purchase" selected>Purchase</option>
                                            <option value="adjustment">Adjustment</option>
                                            <option value="return">Return</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Quantity</label>
                                        <input type="number" class="form-control" name="transaction_quantity[]"
                                            min="1" value="1" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Unit Price</label>
                                        <input type="number" step="0.01" class="form-control"
                                            name="transaction_price[]" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Date</label>
                                        <input type="date" class="form-control date" name="transaction_date[]"
                                            value="{{ date('Y-m-d') }}" required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group mt-2">
                                <label>Notes</label>
                                <textarea class="form-control" name="transaction_notes[]" rows="1"></textarea>
                            </div>
                            <button type="button" class="btn btn-sm btn-danger mt-2" onclick="removeTransaction(this)">
                                <i class="fas fa-trash"></i> Remove
                            </button>
                        </div>
                    </template>

                    <!-- Submit Button -->
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Insert Tool
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add transaction button handler
            document.getElementById('addTransactionBtn')?.addEventListener('click', addTransaction);

            // Add a new transaction row
            function addTransaction() {
                const template = document.getElementById('transactionTemplate').innerHTML;
                const container = document.getElementById('transactionsContainer');
                const div = document.createElement('div');
                div.innerHTML = template;
                container.appendChild(div.firstElementChild);

                // Copy the unit price from the main form to this transaction's price field
                const mainUnitPrice = document.querySelector('input[name="unit_price"]').value;
                const newTransactionPrice = div.querySelector('input[name="transaction_price[]"]');
                if (mainUnitPrice && newTransactionPrice) {
                    newTransactionPrice.value = mainUnitPrice;
                }
            }

            // Remove a transaction row
            window.removeTransaction = function(button) {
                button.closest('.transaction-item').remove();
            };

            // Listen for changes to the main unit price and update empty transaction prices
            document.querySelector('input[name="unit_price"]').addEventListener('change', function() {
                const prices = document.querySelectorAll('input[name="transaction_price[]"]');
                prices.forEach(price => {
                    if (!price.value) {
                        price.value = this.value;
                    }
                });
            });
        });
    </script>
@endpush
