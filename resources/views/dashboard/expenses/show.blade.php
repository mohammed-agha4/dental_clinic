@extends('layouts.master.master')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h4 class="card-title">Expense Details</h4>
                        <div class="card-tools">
                            <a href="{{ route('dashboard.expenses.index') }}" class="btn btn-sm btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Main
                            </a>
                        </div>
                    </div>
                    <div class="card-body">

                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <th style="width: 30%">ID</th>
                                        <td>{{ $expense->id }}</td>
                                    </tr>
                                    <tr>
                                        <th>Title</th>
                                        <td>{{ $expense->title }}</td>
                                    </tr>
                                    <tr>
                                        <th>Staff Member</th>
                                        <td>{{ $expense->staff->user->name }} ({{ $expense->staff->role }})</td>
                                    </tr>
                                    <tr>
                                        <th>Amount</th>
                                        <td>${{ number_format($expense->amount, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Category</th>
                                        <td> {{$expense->category}} </td>
                                    </tr>
                                    <tr>
                                        <th>Expense Date</th>
                                        <td>{{ $expense->expense_date->format('F d, Y') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Description</th>
                                        <td>{{ $expense->description ?? 'No description provided' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Created At</th>
                                        <td>{{ $expense->created_at->format('F d, Y H:i:s') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Last Updated</th>
                                        <td>{{ $expense->updated_at->format('F d, Y H:i:s') }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            <div class="btn-group">
                                <a href="{{ route('dashboard.expenses.edit', $expense) }}" class="btn btn-primary">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <button type="button" class="btn btn-danger" onclick="confirmDelete()">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </div>
                        </div>

                        <form id="delete-form" action="{{ route('dashboard.expenses.destroy', $expense) }}" method="POST"
                            style="display: none;">
                            @csrf
                            @method('DELETE')
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function confirmDelete() {
                if (confirm('Are you sure you want to delete this expense?')) {
                    document.getElementById('delete-form').submit();
                }
            }
        </script>
    @endpush
@endsection
