@extends('layouts.master.master')

@section('title', 'payments')

@section('content')

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Edit Expense #{{ $expense->id }}</h4>
                    </div>
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

                            <div class="form-group row mb-3">
                                <label for="title" class="col-md-3 col-form-label">Title <span
                                        class="text-danger">*</span></label>
                                <div class="col-md-9">
                                    <input type="text" name="title" id="title"
                                        class="form-control @error('title') is-invalid @enderror"
                                        value="{{ old('title', $expense->title) }}" required>
                                </div>
                            </div>

                            <div class="form-group row mb-3">
                                <label for="staff_id" class="col-md-3 col-form-label">Staff Member <span
                                        class="text-danger">*</span></label>
                                <div class="col-md-9">
                                    <select name="staff_id" id="staff_id"
                                        class="form-control @error('staff_id') is-invalid @enderror" required>
                                        <option value="">Select Staff Member</option>
                                        @foreach ($staffMembers as $staff)
                                            <option value="{{ $staff->id }}"
                                                {{ old('staff_id', $expense->staff_id) == $staff->id ? 'selected' : '' }}>
                                                {{ $staff->user->name }} ({{ $staff->role }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row mb-3">
                                <label for="amount" class="col-md-3 col-form-label">Amount <span
                                        class="text-danger">*</span></label>
                                <div class="col-md-9">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">$</span>
                                        </div>
                                        <input type="number" name="amount" id="amount" step="0.01" min="0.01"
                                            class="form-control @error('amount') is-invalid @enderror"
                                            value="{{ old('amount', $expense->amount) }}" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row mb-3">
                                <label for="category" class="col-md-3 col-form-label">Category <span
                                        class="text-danger">*</span></label>
                                <div class="col-md-9">
                                    <select name="category" id="category"
                                        class="form-control @error('category') is-invalid @enderror" required>
                                        <option value="">Select Category</option>
                                        @foreach ($categories as $key => $value)
                                            <option value="{{ $key }}"
                                                {{ old('category', $expense->category) == $key ? 'selected' : '' }}>
                                                {{ $value }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row mb-3">
                                <label for="expense_date" class="col-md-3 col-form-label">Expense Date <span
                                        class="text-danger">*</span></label>
                                <div class="col-md-9">
                                    <input type="date" name="expense_date" id="expense_date"
                                        class="form-control @error('expense_date') is-invalid @enderror"
                                        value="{{ old('expense_date', $expense->expense_date->format('Y-m-d')) }}"
                                        max="{{ date('Y-m-d') }}" required>
                                </div>
                            </div>

                            <div class="form-group row mb-3">
                                <label for="description" class="col-md-3 col-form-label">Description</label>
                                <div class="col-md-9">
                                    <textarea name="description" id="description" rows="4"
                                        class="form-control @error('description') is-invalid @enderror">{{ old('description', $expense->description) }}</textarea>
                                </div>
                            </div>

                            <div class="form-group row mb-0">
                                <div class="col-md-9 offset-md-3">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Update Expense
                                    </button>
                                    <a href="{{ route('dashboard.expenses.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left"></i> Cancel
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
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
