@extends('layouts.master.master')

@section('title', 'Create Payment')

@section('content')
    <form action="{{ route('dashboard.payments.store') }}" method="post">
        <div class="container-fluid">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h4>Create New Payment</h4>
                    <a href="{{ route('dashboard.payments.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left fa-sm"></i> Back to Payments
                    </a>
                </div>
                <div class="card-body">
                    <form action="{{ route('dashboard.payments.store') }}" method="POST">
                        @csrf

                        <div class="form-group my-2">
                            <label for="visit_id">Select Visit</label>
                            <select name="visit_id" id="visit_id"
                                class="form-control select2 @error('visit_id') is-invalid @enderror" required>
                                <option value="">Select Visit</option>
                                @foreach ($visits as $visit)
                                    @if ($visit->staff->user->id == Auth::user()->id || Auth::user()->staff->role == 'admin')
                                        <option value="{{ $visit->id }}"
                                            {{ old('visit_id') == $visit->id ? 'selected' : '' }}
                                            data-service="{{ $visit->service->service_name ?? 'No service' }}"
                                            data-price="{{ $visit->service->service_price ?? 0 }}">
                                            {{ $visit->patient->FullName }} -
                                            {{ $visit->visit_date->format('M d, Y H:i') }} -
                                            {{ $visit->service->service_name ?? 'No service' }}
                                            {{ $visit->staff->user->name }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                            @error('visit_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            @if (Auth::user()->staff->role == 'admin')
                                <div class="col-md-6 my-2">
                                    <div class="form-group">
                                        <label for="staff_id">Staff Member</label>
                                        <select name="staff_id" id="staff_id"
                                            class="form-control @error('staff_id') is-invalid @enderror" required>
                                            <option value="">Select Staff</option>
                                            @foreach ($staff as $member)
                                                <option value="{{ $member->id }}"
                                                    {{ old('staff_id') == $member->id ? 'selected' : '' }}>
                                                    {{ $member->user->name }} ({{ $member->role }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('staff_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            @else
                                <div class="col-md-6 my-2">
                                    <div class="form-group">
                                        <label for="staff_id">Staff Member</label>
                                        <input type="text" class="form-control" value="{{ Auth::user()->name }}" readonly>
                                        <input type="hidden" name="staff_id" value="{{ Auth::user()->id }}">
                                        @error('staff_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            @endif

                            <div class="col-md-6 my-2">
                                <div class="form-group">
                                    <label for="amount">Amount</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">$</span>
                                        </div>
                                        <input type="number" name="amount" id="amount" step="0.01"
                                            class="form-control @error('amount') is-invalid @enderror"
                                            value="{{ old('amount') }}" required>
                                        @error('amount')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 my-2">
                                <div class="form-group">
                                    <label for="method">Payment Method</label>
                                    <select name="method" id="method"
                                        class="form-control @error('method') is-invalid @enderror" required>
                                        <option value="">Select Method</option>
                                        @foreach ($paymentMethods as $method)
                                            <option value="{{ $method }}"
                                                {{ old('method') == $method ? 'selected' : '' }}>
                                                {{ ucfirst(str_replace('_', ' ', $method)) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('method')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6 my-2">
                                <div class="form-group">
                                    <label for="transaction_id">Transaction ID</label>
                                    <input type="text" name="transaction_id" id="transaction_id"
                                        class="form-control @error('transaction_id') is-invalid @enderror"
                                        value="{{ old('transaction_id') }}">
                                    @error('transaction_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group my-2">
                            <label for="status">Status</label>
                            <select name="status" id="status" class="form-control @error('status') is-invalid @enderror"
                                required>
                                <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed
                                </option>
                                <option value="failed" {{ old('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group my-2">
                            <label for="notes">Notes</label>
                            <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" rows="3">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary">Save Payment</button>
                            <a href="{{ route('dashboard.payments.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-fill amount based on service price
            const visitSelect = document.getElementById('visit_id');
            const amountInput = document.getElementById('amount');

            visitSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const servicePrice = selectedOption.dataset.price;

                if (servicePrice) {
                    amountInput.value = servicePrice;
                }
            });

            // Display transaction ID field only for card or bank transfer
            const methodSelect = document.getElementById('method');
            const transactionIdField = document.getElementById('transaction_id').parentElement.parentElement;

            methodSelect.addEventListener('change', function() {
                const method = this.value;
                if (method === 'credit_card' || method === 'bank_transfer') {
                    transactionIdField.style.display = 'block';
                } else {
                    transactionIdField.style.display = 'none';
                    document.getElementById('transaction_id').value = '';
                }
            });

            // Trigger change on page load
            methodSelect.dispatchEvent(new Event('change'));
        });
    </script>
@endpush
