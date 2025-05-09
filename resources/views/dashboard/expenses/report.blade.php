@extends('layouts.master.master')
@section('title', 'Reports')

@section('css')
<style>
    @media print {

        .non-printable,
        .card-tools {
            display: none !important;
        }

        .card {
            border: none !important;
            box-shadow: none !important;
        }

        .card-header {
            background-color: #f8f9fa !important;
            color: #000 !important;
            border-bottom: 1px solid #dee2e6 !important;
        }

        body {
            padding: 0;
            margin: 0;
        }

        .container-fluid {
            width: 100%;
            padding: 0;
        }
    }
</style>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Expense Report</h4>
                        <div class="card-tools">
                            <button onclick="window.print();" class="btn btn-primary btn-sm">
                                <i class="fas fa-print"></i> Print Report
                            </button>
                            <a href="{{ route('dashboard.expenses.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left"></i> Back to Expenses
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Report Filters Form -->
                        <div class="non-printable mb-4">
                            <form action="{{ route('dashboard.expenses.report') }}" method="GET" class="row">
                                <div class="col-md-3 mb-2">
                                    <label for="start_date">Start Date</label>
                                    <input type="date" name="start_date" id="start_date" class="form-control"
                                        value="{{ request('start_date') }}" required>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <label for="end_date">End Date</label>
                                    <input type="date" name="end_date" id="end_date" class="form-control"
                                        value="{{ request('end_date') }}" required>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <label for="category">Category (Optional)</label>
                                    <select name="category" id="category" class="form-control">
                                        <option value="">All Categories</option>
                                        @foreach (\App\Models\Expense::CATEGORIES as $key => $value)
                                            <option value="{{ $key }}"
                                                {{ request('category') == $key ? 'selected' : '' }}>
                                                {{ $value }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <label for="staff_id">Staff Member (Optional)</label>
                                    <select name="staff_id" id="staff_id" class="form-control">
                                        <option value="">All Staff</option>
                                        @foreach (\App\Models\Staff::with('user')->get() as $staff)
                                            <option value="{{ $staff->id }}"
                                                {{ request('staff_id') == $staff->id ? 'selected' : '' }}>
                                                {{ $staff->user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-12 mt-2">
                                    <button type="submit" class="btn btn-primary">Generate Report</button>
                                </div>
                            </form>
                        </div>

                        @if (isset($expenses))
                            <!-- Report Header -->
                            <div class="report-header text-center mb-4">
                                <h2>Dental Clinic Expense Report</h2>
                                <h4>Period: {{ \Carbon\Carbon::parse(request('start_date'))->format('M d, Y') }} -
                                    {{ \Carbon\Carbon::parse(request('end_date'))->format('M d, Y') }}</h4>
                                @if (request('category'))
                                    <h5>Category: {{ \App\Models\Expense::CATEGORIES[request('category')] }}</h5>
                                @endif
                                @if (request('staff_id'))
                                    <h5>Staff: {{ \App\Models\Staff::find(request('staff_id'))->user->name }}</h5>
                                @endif
                                <p>Generated on: {{ now()->format('F d, Y H:i:s') }}</p>
                            </div>

                            <!-- Summary Section -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header bg-primary text-white">
                                            <h4 class="card-title">Summary</h4>
                                        </div>
                                        <div class="card-body">
                                            <table class="table table-bordered">
                                                <tr>
                                                    <th>Total Expenses</th>
                                                    <td>${{ number_format($totalAmount, 2) }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Number of Expenses</th>
                                                    <td>{{ $expenses->count() }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Average Expense</th>
                                                    <td>${{ $expenses->count() > 0 ? number_format($totalAmount / $expenses->count(), 2) : '0.00' }}
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header bg-info text-white">
                                            <h4 class="card-title">Expense Distribution</h4>
                                        </div>
                                        <div class="card-body">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Category</th>
                                                        <th>Count</th>
                                                        <th>Total</th>
                                                        <th>Percentage</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($categoryTotals as $category => $data)
                                                        <tr>
                                                            <td>{{ \App\Models\Expense::CATEGORIES[$category] }}</td>
                                                            <td>{{ $data['count'] }}</td>
                                                            <td>${{ number_format($data['total'], 2) }}</td>
                                                            <td>{{ number_format(($data['total'] / $totalAmount) * 100, 1) }}%
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Detailed Expenses Table -->
                            <div class="card">
                                <div class="card-header bg-secondary text-white">
                                    <h4 class="card-title">Detailed Expense List</h4>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Date</th>
                                                    <th>Title</th>
                                                    <th>Category</th>
                                                    <th>Staff</th>
                                                    <th>Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($expenses as $expense)
                                                    <tr>
                                                        <td>{{ $expense->id }}</td>
                                                        <td>{{ $expense->expense_date->format('M d, Y') }}</td>
                                                        <td>{{ $expense->title }}</td>
                                                        <td>{{ \App\Models\Expense::CATEGORIES[$expense->category] }}</td>
                                                        <td>{{ $expense->staff->user->name }}</td>
                                                        <td class="text-right">${{ number_format($expense->amount, 2) }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th colspan="5" class="text-right">Total:</th>
                                                    <th class="text-right">${{ number_format($totalAmount, 2) }}</th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-info">
                                Please select a date range to generate the expense report.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection




