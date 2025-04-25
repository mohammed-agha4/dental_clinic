@extends('layouts.master.master')

@section('title', 'Payment Information')

@section('content')

    <form action="{{ route('dashboard.payments.update', $payment->id) }}" method="post">

        @method('put')
        @csrf
        <div class="container-fluid">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h4>Edit Payment</h4>
                    <a href="{{ route('dashboard.payments.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left fa-sm"></i> Back to Main
                    </a>
                </div>
                <div class="card-body">
                    <form action="{{ route('dashboard.payments.update', $payment) }}" method="POST">
                        @csrf
                        @method('PUT')




                        <h5 class="m-0">Visit Information:</h5>
                        <div class="row  card-body mt-1 border mx-3">
                            <div class="form-group col-4">
                                <label>Patient Name</label>
                                <input class="form-control"
                                    value=" {{ $payment->visit->patient->fname }} {{ $payment->visit->patient->lname }}"
                                    disabled>
                            </div>
                            <div class="form-group col-4">
                                <label>Visit Date</label>
                                <input class="form-control" value="{{ $payment->visit->visit_date }}" disabled>
                            </div>
                            <div class="form-group col-4">
                                <label>Service Name</label>
                                <input class="form-control"
                                    value="{{ $payment->visit->service->service_name ?? 'No service' }}" disabled>
                            </div>
                        </div>




                        <h5 class="my-3">Payment Information:</h5>
                        <div class="border mx-3 p-3">
                            <div class="row">
                                <div class="col-md-6 my-2">
                                    <div class="form-group">
                                        <label for="staff_id">Staff Member</label>
                                        <select name="staff_id" id="staff_id"
                                            class="form-control @error('staff_id') is-invalid @enderror" required>
                                            <option value="">Select Staff</option>
                                            @foreach ($staff as $member)
                                                <option value="{{ $member->id }}"
                                                    {{ old('staff_id', $payment->staff_id) == $member->id ? 'selected' : '' }}>
                                                    {{ $member->user->name }} ({{ $member->role }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('staff_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6 my-2">
                                    <div class="form-group">
                                        <label for="amount">Amount</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">$</span>
                                            </div>
                                            <input type="number" name="amount" id="amount" step="0.01"
                                                class="form-control @error('amount') is-invalid @enderror"
                                                value="{{ old('amount', $payment->amount) }}" required>
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
                                            @foreach ($paymentMethods as $method)
                                                <option value="{{ $method }}"
                                                    {{ old('method', $payment->method) == $method ? 'selected' : '' }}>
                                                    {{ ucfirst(str_replace('_', ' ', $method)) }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('method')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6 my-2" id="transaction_id_group">
                                    <div class="form-group">
                                        <label for="transaction_id">Transaction ID</label>
                                        <input type="text" name="transaction_id" id="transaction_id"
                                            class="form-control @error('transaction_id') is-invalid @enderror"
                                            value="{{ old('transaction_id', $payment->transaction_id) }}">
                                        @error('transaction_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group my-2">
                                <label for="status">Status</label>
                                <select name="status" id="status"
                                    class="form-control @error('status') is-invalid @enderror" required>
                                    @foreach ($statuses as $status)
                                        <option value="{{ $status }}"
                                            {{ old('status', $payment->status) == $status ? 'selected' : '' }}>
                                            {{ ucfirst($status) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group my-2">
                                <label for="notes">Notes</label>
                                <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" rows="3">{{ old('notes', $payment->notes) }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mt-4">
                                <button type="submit" class="btn btn-primary">Update Payment</button>
                                <a href="{{ route('dashboard.payments.index') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>


    </form>

@endsection


@push('js')
    <script>
        $(document).ready(function() {
            // Display transaction ID field only for card or bank transfer
            function toggleTransactionId() {
                var method = $('#method').val();
                if (method === 'credit_card' || method === 'bank_transfer') {
                    $('#transaction_id_group').show();
                } else {
                    $('#transaction_id_group').hide();
                    $('#transaction_id').val('');
                }
            }

            $('#method').change(toggleTransactionId);

            // Trigger on page load
            toggleTransactionId();
        });
    </script>
@endpush
