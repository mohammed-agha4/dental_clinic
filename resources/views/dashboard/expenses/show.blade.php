@extends('layouts.master.master')
@section('title', 'Expense Information')

@section('content')
    <div class="container-fluid">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h5>Expense Details</h5>
                <div>
                    @can('expenses.update')
                    <a href="{{ route('dashboard.expenses.edit', $expense) }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-edit fa-sm"></i> Edit
                    </a>
                    @endcan
                    @can('expenses.delete')
                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="confirmDelete()">
                        <i class="fas fa-trash fa-sm"></i> Delete
                    </button>
                    @endcan
                    <a href="{{ route('dashboard.expenses.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left fa-sm"></i> Back
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Main Expense Info -->
                    <div class="col-md-6">
                        <div class="card mb-4 h-100">
                            <div class="card-header">
                                <h6 class="m-0 font-weight-bold text-success">Expense Information</h6>
                            </div>
                            <div class="card-body">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td class="p-1 font-weight-bold">ID:</td>
                                        <td class="p-1">{{ $expense->id }}</td>
                                    </tr>
                                    <tr>
                                        <td class="p-1 font-weight-bold">Title:</td>
                                        <td class="p-1">{{ $expense->title }}</td>
                                    </tr>
                                    <tr>
                                        <td class="p-1 font-weight-bold">Amount:</td>
                                        <td class="p-1">${{ number_format($expense->amount, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="p-1 font-weight-bold">Category:</td>
                                        <td class="p-1">{{ $expense->category }}</td>
                                    </tr>
                                    <tr>
                                        <td class="p-1 font-weight-bold">Date:</td>
                                        <td class="p-1">{{ $expense->expense_date->format('M d, Y') }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Staff & Additional Info -->
                    <div class="col-md-6">
                        <div class="card mb-4 h-100">
                            <div class="card-header">
                                <h6 class="m-0 font-weight-bold text-success">Additional Information</h6>
                            </div>
                            <div class="card-body">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td class="p-1 font-weight-bold">Staff:</td>
                                        <td class="p-1">{{ $expense->staff->user->name }} ({{ ucfirst($expense->staff->role) }})</td>
                                    </tr>
                                    <tr>
                                        <td class="p-1 font-weight-bold">Created:</td>
                                        <td class="p-1">{{ $expense->created_at->format('M d, Y H:i') }}</td>
                                    </tr>
                                    <tr>
                                        <td class="p-1 font-weight-bold">Updated:</td>
                                        <td class="p-1">{{ $expense->updated_at->format('M d, Y H:i') }}</td>
                                    </tr>
                                    <tr>
                                        <td class="p-1 font-weight-bold align-top">Description:</td>
                                        <td class="p-1">{{ $expense->description ?? 'No description provided' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form id="delete-form" action="{{ route('dashboard.expenses.destroy', $expense) }}" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
@endsection

@push('scripts')
    <script>
        function confirmDelete() {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form').submit();
                }
            })
        }
    </script>
@endpush
