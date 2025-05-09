@extends('layouts.master.master')

@section('title', 'Create Visitation')

@section('content')
    <div class="container-fluid">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Insert Patient Visit</h4>
            </div>

            @if (session()->has('error'))
                <div id="flash-msg" class="alert alert-danger alert-dismissible fade show ">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card-body">
                <form action="{{ route('dashboard.visits.store') }}" method="post">
                    @csrf
                    <input type="hidden" name="appointment_id" value="{{ $appointment->id }}">

                    <div class="row">
                        <div class="col-3 mb-3">
                            <div class="form-group">
                                <label class="form-label">Patient:</label>
                                <select name="patient_id" class="form-select @error('patient_id') is-invalid @enderror">
                                    <option selected disabled>--select--</option>
                                    @foreach ($patients as $patient)
                                        <option value="{{ $patient->id }}" @selected(old('patient_id', $appointment->patient_id) == $patient->id)>
                                            {{ $patient->fname }}</option>
                                    @endforeach
                                </select>
                                @error('patient_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="col-3 mb-3">
                            <div class="form-group">
                                <label class="form-label">Service:</label>
                                <select name="service_id" class="form-select @error('service_id') is-invalid @enderror">
                                    <option selected disabled>--select--</option>
                                    @foreach ($services as $service)
                                        <option value="{{ $service->id }}" @selected(old('service_id', $appointment->service_id) == $service->id)>
                                            {{ $service->service_name }}</option>
                                    @endforeach
                                </select>
                                @error('service_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="col-3 mb-3">
                            <div class="form-group">
                                <label class="form-label">Staff:</label>
                                <select name="staff_id" class="form-select @error('staff_id') is-invalid @enderror">
                                    <option selected disabled>--select--</option>
                                    @foreach ($staff as $staff)
                                        <option value="{{ $staff->id }}" @selected(old('staff_id', $appointment->staff_id) == $staff->id)>
                                            {{ $staff->user->name }}</option>
                                    @endforeach
                                </select>
                                @error('staff_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="col-3 mb-3">
                            <div class="form-group">
                                <label class="form-label">Appointment Status:</label>
                                <select name="status" class="form-select @error('status') is-invalid @enderror">
                                    <option selected disabled>--select--</option>
                                    <option @selected(old('status', $appointment->status) == 'scheduled') value="scheduled">Scheduled</option>
                                    <option @selected(old('status', $appointment->status) == 'walk_in') value="walk_in">Walk In</option>
                                    <option @selected(old('status', $appointment->status) == 'completed') value="completed">Completed</option>
                                    <option @selected(old('status', $appointment->status) == 'rescheduled') value="rescheduled">Rescheduled</option>
                                    <option @selected(old('status', $appointment->status) == 'canceled') value="canceled">Canceled</option>
                                </select>
                                @error('status')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <x-form.input label='Visit Date:' type='datetime-local' name='visit_date' :value='$appointment->appointment_date'
                                placeholder='Visit Date' />
                        </div>
                    </div>

                    <!-- Visit Information Card -->
                    <div class="card mb-4 border-primary">
                        <div class="card-header bg-light text-white">
                            <h5 class="mb-0 text-dark">Visit Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6 mb-3">
                                    <x-form.textarea label='Chief Complaint:' name='cheif_complaint' :value='$visit->cheif_complaint'
                                        placeholder="Enter Chief Complaint" />
                                </div>

                                <div class="col-6 mb-3">
                                    <x-form.textarea label='Diagnosis:' name='diagnosis' :value='$visit->diagnosis'
                                        placeholder="Diagnosis" />
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-6 mb-3">
                                    <x-form.textarea label='Treatment Notes:' name='treatment_notes' :value='$visit->treatment_notes'
                                        placeholder="Treatment Notes" />
                                </div>

                                <div class="col-6 mb-3">
                                    <x-form.textarea label='Next Visit Notes:' name='next_visit_notes' :value='$visit->next_visit_notes'
                                        placeholder="Enter Next Visit Notes" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Inventory Transactions Card -->
                    <div class="card mb-4 border-info">
                        <div
                            class="card-header bg-secondary-subtle text-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 text-dark">Inventory Transactions</h5>
                            <div>
                                <button type="button" class="btn btn-sm btn-light me-2" id="addInventoryItemBtn">
                                    <i class="fas fa-plus-circle"></i> Add Item
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            {{-- <div class="alert alert-info mb-3">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Note:</strong> Items will be automatically linked to this visit.
                                Unit prices are loaded from inventory.
                            </div> --}}

                            <div
                                class="transaction-summary mb-3 d-flex justify-content-between align-items-center bg-light p-2 rounded">
                                <div>
                                    <span class="badge bg-dark me-2 total-items">0</span> items
                                </div>
                            </div>

                            <div id="inventoryItemsContainer">
                                <!-- Dynamic transactions will be added here -->
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="row mb-3">
                        <div class="col-md-12 d-flex justify-content-between">
                            <a href="{{ route('dashboard.visits.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Back to Visits
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Insert Visit
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Template for inventory transaction -->
    <template id="inventoryItemTemplate">
        <div class="inventory-item border p-3 mb-3 rounded">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">Inventory Item</label>
                        <select class="form-select inventory-select" name="transaction_inventory_ids[]" required>
                            <option value="">Select Item</option>
                            @foreach ($inventory as $item)
                                <option value="{{ $item->id }}" data-price="{{ $item->unit_price }}"
                                    data-available="{{ $item->quantity }}" data-sku="{{ $item->SKU }}"
                                    data-expiry="{{ $item->expiry_date ? $item->expiry_date->format('Y-m-d') : '' }}"
                                    @if ($item->expiry_date && $item->expiry_date->isPast()) class="text-danger" @endif>
                                    {{ $item->name }} ({{ $item->quantity }} available) - {{ $item->SKU }}
                                    @if ($item->expiry_date)
                                        @if ($item->expiry_date->isPast())
                                            [EXPIRED]
                                        @else
                                            [Exp: {{ $item->expiry_date->format('m/d/Y') }}]
                                        @endif
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        <div class="d-flex justify-content-between mt-1">
                            <small class="text-muted">SKU: <span class="item-sku">-</span></small>
                            <small class="text-muted available-qty">Available: -</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="form-label">Transaction Type</label>
                        <select class="form-select transaction-type" name="transaction_types[]" required>
                            <option value="use" selected>Use</option>
                            <option value="purchase">Purchase</option>
                            <option value="adjustment">Adjustment</option>
                            <option value="return">Return</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="form-label">Quantity</label>
                        <input type="number" class="form-control transaction-quantity" name="transaction_quantities[]"
                            min="1" value="1" required>
                        <small class="text-danger quantity-warning" style="display: none;">Exceeds available
                            quantity!</small>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="form-label">Unit Price</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" class="form-control transaction-price"
                                name="transaction_prices[]" readonly>
                        </div>
                        <input type="hidden" name="transaction_dates[]" value="{{ date('Y-m-d') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="form-label d-none d-md-block">&nbsp;</label>
                        <button type="button" class="btn btn-outline-danger" onclick="removeInventoryItem(this)">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="form-group mt-2">
                <label class="form-label">Notes</label>
                <textarea class="form-control" name="transaction_notes[]" rows="1"></textarea>
            </div>
            <div class="transaction-indicator mt-2"></div>
        </div>
    </template>
@endsection

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize item counters
            updateItemCounters();

            // Add a new inventory item row
            const addInventoryItemBtn = document.getElementById('addInventoryItemBtn');
            if (addInventoryItemBtn) {
                addInventoryItemBtn.addEventListener('click', function() {
                    addInventoryItem();
                });
            }

            // Function to add a new inventory item
            function addInventoryItem() {
                const template = document.getElementById('inventoryItemTemplate');
                const container = document.getElementById('inventoryItemsContainer');

                // Clone the template content
                const clone = document.importNode(template.content, true);

                // Add event listeners to the new item
                setupInventoryItemEventListeners(clone);

                // Append the new item to the container
                container.appendChild(clone);

                // Update counters
                updateItemCounters();
            }

            // Function to set up event listeners for an inventory item
            function setupInventoryItemEventListeners(itemElement) {
                const inventorySelect = itemElement.querySelector('.inventory-select');
                const quantityInput = itemElement.querySelector('.transaction-quantity');
                const priceInput = itemElement.querySelector('.transaction-price');
                const typeSelect = itemElement.querySelector('.transaction-type');
                const skuSpan = itemElement.querySelector('.item-sku');
                const availableQtySpan = itemElement.querySelector('.available-qty');
                const quantityWarning = itemElement.querySelector('.quantity-warning');
                const transactionIndicator = itemElement.querySelector('.transaction-indicator');

                // Event listener for inventory selection
                inventorySelect.addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];

                    if (selectedOption.value) {
                        const price = selectedOption.getAttribute('data-price');
                        const available = selectedOption.getAttribute('data-available');
                        const sku = selectedOption.getAttribute('data-sku');
                        const expiryDate = selectedOption.getAttribute('data-expiry');
                        const today = new Date().toISOString().split('T')[0];

                        // Update fields
                        priceInput.value = price;
                        skuSpan.textContent = sku;
                        availableQtySpan.textContent = `Available: ${available}`;

                        // Check for expired item
                        if (expiryDate && expiryDate < today) {
                            transactionIndicator.innerHTML = `
                        <div class="alert alert-danger p-2 mb-0">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            This item expired on ${new Date(expiryDate).toLocaleDateString()}
                        </div>
                    `;
                            this.classList.add('is-invalid');
                        } else {
                            transactionIndicator.innerHTML = '';
                            this.classList.remove('is-invalid');
                        }

                        // Check quantity against available
                        validateQuantity(quantityInput, available, quantityWarning);
                    } else {
                        // Reset fields if no option selected
                        priceInput.value = '';
                        skuSpan.textContent = '-';
                        availableQtySpan.textContent = 'Available: -';
                        quantityWarning.style.display = 'none';
                        transactionIndicator.innerHTML = '';
                        this.classList.remove('is-invalid');
                    }
                });

                // Event listener for quantity changes
                quantityInput.addEventListener('input', function() {
                    const selectedOption = inventorySelect.options[inventorySelect.selectedIndex];

                    if (selectedOption.value) {
                        const available = selectedOption.getAttribute('data-available');
                        validateQuantity(this, available, quantityWarning);
                    }
                });



                // Trigger change events to initialize the fields

                typeSelect.dispatchEvent(new Event('change'));
            }
            // Add form submission validation
            const form = document.querySelector('form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    const expiredItems = [];

                    document.querySelectorAll('.inventory-select').forEach(select => {
                        const selectedOption = select.options[select.selectedIndex];
                        if (selectedOption.value) {
                            const expiryDate = selectedOption.getAttribute('data-expiry');
                            const today = new Date().toISOString().split('T')[0];

                            if (expiryDate && expiryDate < today) {
                                expiredItems.push(selectedOption.text.trim());
                            }
                        }
                    });

                    if (expiredItems.length > 0) {
                        e.preventDefault();
                        alert(`Cannot submit visit with expired items:\n\n${expiredItems.join('\n')}`);
                    }
                });
            }

            // Function to validate quantity against available stock
            function validateQuantity(quantityInput, available, warningElement) {
                const quantity = parseInt(quantityInput.value, 10);
                const availableQty = parseInt(available, 10);
                const transactionType = quantityInput.closest('.inventory-item').querySelector('.transaction-type')
                    .value;

                if (quantity > availableQty && transactionType === 'use') {
                    warningElement.style.display = 'block';
                    quantityInput.setCustomValidity('Quantity exceeds available stock.');
                } else {
                    warningElement.style.display = 'none';
                    quantityInput.setCustomValidity('');
                }
            }

            // Function to remove an inventory item
            function removeInventoryItem(button) {
                const item = button.closest('.inventory-item');
                item.remove();
                updateItemCounters();
            }

            // Function to update the total items counter
            function updateItemCounters() {
                const totalItems = document.querySelectorAll('.inventory-item').length;
                document.querySelector('.total-items').textContent = totalItems;
            }

            // Initialize any existing inventory items on page load
            document.querySelectorAll('.inventory-item').forEach(item => {
                setupInventoryItemEventListeners(item);
            });
        });
    </script>
@endpush
