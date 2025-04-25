@extends('layouts.master.master')

@section('title', 'Edit Expense')

@section('content')
    <div class="container">
        <div class="row">
            <div class="card">
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('dashboard.expenses.update', $expense) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="container-fluid">
                            <div class="card shadow-sm mb-4">
                                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h5>Edit Expense #{{ $expense->id }}</h5>
                                    <a href="{{ route('dashboard.expenses.index') }}" class="btn btn-secondary btn-sm">
                                        <i class="fas fa-arrow-left fa-sm"></i> Back to Main
                                    </a>
                                </div>

                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="form-group">
                                            <label>Title:</label>
                                            <input type="text" name="title"
                                                class="form-control @error('title') is-invalid @enderror"
                                                placeholder="Enter expense title"
                                                value="{{ old('title', $expense->title) }}" required>
                                            @error('title')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="form-group">
                                            <label>Staff Member:</label>
                                            <select name="staff_id"
                                                class="form-control @error('staff_id') is-invalid @enderror" required>
                                                <option value="">Select Staff Member</option>
                                                @foreach ($staffMembers as $staff)
                                                    <option value="{{ $staff->id }}"
                                                        {{ old('staff_id', $expense->staff_id) == $staff->id ? 'selected' : '' }}>
                                                        {{ $staff->user->name }} ({{ $staff->role }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('staff_id')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Amount:</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">$</span>
                                                    <input type="number" name="amount" step="0.01" min="0.01"
                                                        class="form-control @error('amount') is-invalid @enderror"
                                                        placeholder="Enter amount"
                                                        value="{{ old('amount', $expense->amount) }}" required>
                                                </div>
                                                @error('amount')
                                                    <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Category:</label>
                                                <select name="category"
                                                    class="form-control @error('category') is-invalid @enderror" required>
                                                    <option value="">Select Category</option>
                                                    @foreach ($categories as $key => $value)
                                                        <option value="{{ $key }}"
                                                            {{ old('category', $expense->category) == $key ? 'selected' : '' }}>
                                                            {{ $value }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('category')
                                                    <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Expense Date:</label>
                                                <input type="date" name="expense_date"
                                                    class="form-control @error('expense_date') is-invalid @enderror"
                                                    value="{{ old('expense_date', $expense->expense_date->format('Y-m-d')) }}"
                                                    max="{{ date('Y-m-d') }}" required>
                                                @error('expense_date')
                                                    <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="form-group">
                                            <label>Description:</label>
                                            <textarea name="description" rows="4" class="form-control @error('description') is-invalid @enderror"
                                                placeholder="Enter expense description">{{ old('description', $expense->description) }}</textarea>
                                            @error('description')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save"></i> Update Expense
                                            </button>
                                            <a href="{{ route('dashboard.expenses.index') }}" class="btn btn-secondary">
                                                <i class="fas fa-arrow-left"></i> Cancel
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
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
