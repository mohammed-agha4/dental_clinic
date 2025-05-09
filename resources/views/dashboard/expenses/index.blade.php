@extends('layouts.master.master')

@section('title', 'Expenses')

@section('content')
    <div class="row">
        <div class="container-fluid col-10">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="card-title">Expenses Management</h4>
                            <div class="card-tools">
                                @can('expenses.trash')
                                    <a href="{{ route('dashboard.expenses.trash') }}" class="btn btn-secondary btn-sm me-2">
                                        <i class="fas fa-trash-alt fa-sm"></i> Trash
                                    </a>
                                @endcan
                                @can('expenses.create')
                                    <a href="{{ route('dashboard.expenses.create') }}" class="btn btn-dark btn-sm">
                                        <i class="fas fa-plus"></i> Add New Expense
                                    </a>
                                @endcan
                            </div>
                        </div>
                        <div class="card-body">

                            @if (session()->has('success'))
                                <div id="flash-msg" class="alert alert-success alert-dismissible fade show">
                                    {{ session('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                            @endif
                            @if (session()->has('error'))
                                <div id="flash-msg" class="alert alert-danger alert-dismissible fade show">
                                    {{ session('error') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                            @endif

                            <!-- Replace the current filter div with this: -->
                            <form action="{{ route('dashboard.expenses.index') }}" method="GET">
                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <input type="date" class="form-control form-control-sm" name="start_date"
                                            value="{{ request('start_date') }}" placeholder="From Date">
                                    </div>
                                    <div class="col-md-3">
                                        <input type="date" class="form-control form-control-sm" name="end_date"
                                            value="{{ request('end_date') }}" placeholder="To Date">
                                    </div>
                                    <div class="col-md-3">
                                        <select class="form-control form-control-sm" name="category">
                                            <option value="">All Categories</option>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category }}"
                                                    {{ request('category') == $category ? 'selected' : '' }}>
                                                    {{ ucfirst($category) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <select class="form-control form-control-sm" name="staff_id">
                                            <option value="">All Staff</option>
                                            @foreach ($staffMembers as $staff)
                                                <option value="{{ $staff->id }}"
                                                    {{ request('staff_id') == $staff->id ? 'selected' : '' }}>
                                                    {{ $staff->user->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-12 mt-2">
                                        <button type="submit" class="btn btn-primary btn-sm">
                                            <i class="fas fa-filter"></i> Filter
                                        </button>
                                        <a href="{{ route('dashboard.expenses.index') }}" class="btn btn-secondary btn-sm">
                                            <i class="fas fa-sync-alt"></i> Reset
                                        </a>
                                    </div>
                                </div>
                            </form>

                            <!-- Expenses Table -->
                            <div class="table-responsive ">
                                <table class="table small table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Title</th>
                                            <th>Staff</th>
                                            <th>Amount</th>
                                            <th>Category</th>
                                            <th>Date</th>
                                            @if (auth()->user()->can('expenses.show') ||
                                                    auth()->user()->can('expenses.update') ||
                                                    auth()->user()->can('expenses.delete'))
                                                <th>Action</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($expenses as $expense)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $expense->title }}</td>
                                                <td>{{ ucfirst($expense->staff->user->name) ?? '' }}</td>
                                                <td>{{ number_format($expense->amount, 2) }}</td>
                                                <td>{{ $expense->category }}</td>
                                                <td>{{ $expense->expense_date->format('M d, Y') }}</td>
                                                @if (auth()->user()->can('expenses.show') ||
                                                        auth()->user()->can('expenses.update') ||
                                                        auth()->user()->can('expenses.delete'))
                                                    <td>
                                                        <div class="btn-group">
                                                            @can('expenses.show')
                                                                <a href="{{ route('dashboard.expenses.show', $expense) }}"
                                                                    class="btn btn-sm btn-outline-success">
                                                                    <i class="fas fa-eye"></i>
                                                                </a>
                                                            @endcan
                                                            @can('expenses.update')
                                                                <a href="{{ route('dashboard.expenses.edit', $expense) }}"
                                                                    class="btn btn-sm btn-outline-primary">
                                                                    <i class="fas fa-edit"></i>
                                                                </a>
                                                            @endcan
                                                            @can('expenses.delete')
                                                                <button type="button"
                                                                    class="btn btn-sm btn-outline-danger delete-btn"
                                                                    data-id="{{ $expense->id }}"
                                                                    data-name="{{ $expense->title }}">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            @endcan
                                                        </div>
                                                    </td>
                                                @endif
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center">No expenses found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div class="mt-3">
                                {{ $expenses->appends(request()->all())->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Original Total Amount Card -->
        <div class="col-2 card" style="max-height: 100px">
            <div class="info-box-content card-header">
                <h6 class="card-text text-center"><i class="fas fa-money-bill"></i> Total Expenses</h6>
            </div>
            <div class="ms-2 mt-4">
                <span>{{ number_format($totalAmount, 2) }} $</span>
            </div>
        </div>
    </div>


    <form id="delete-form" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
@endsection

@push('js')
    <x-delete-alert
        route="dashboard.expenses.destroy"
        itemName="expense"
        deleteBtnClass="delete-btn"
    />
@endpush
