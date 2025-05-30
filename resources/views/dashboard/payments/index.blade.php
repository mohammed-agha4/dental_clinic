@extends('layouts.master.master')
@section('title', 'Payment')

@section('content')
    <div class="row">
        <!-- Main Content -->
        <div class="container-fluid">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h4 class="mb-0">Payments</h4>
                    @can('payments.create')
                        <a href="{{ route('dashboard.payments.create') }}" class="btn btn-dark btn-sm">
                            <i class="fas fa-plus fa-sm"></i> New Payment
                        </a>
                    @endcan
                </div>
                <div class="card-body">

                    @if (session()->has('success'))
                        <div id="flash-msg" class="alert alert-success alert-dismissible fade show">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    @if (session()->has('error'))
                        <div id="flash-msg" class="alert alert-danger alert-dismissible fade show">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="mb-4 border-bottom pb-3">
                        <form action="{{ route('dashboard.payments.index') }}" method="GET" class="row g-3">
                            <div class="col-md-3">
                                <select class="form-select form-select-sm" name="status">
                                    <option value="">All Statuses</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending
                                    </option>
                                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>
                                        Completed</option>
                                    <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed
                                    </option>
                                    <option value="refunded" {{ request('status') == 'refunded' ? 'selected' : '' }}>
                                        Refunded</option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <select class="form-select form-select-sm" name="method">
                                    <option value="">All Methods</option>
                                    <option value="cash" {{ request('method') == 'cash' ? 'selected' : '' }}>Cash</option>
                                    <option value="credit_card" {{ request('method') == 'credit_card' ? 'selected' : '' }}>
                                        Credit Card</option>
                                    <option value="insurance" {{ request('method') == 'insurance' ? 'selected' : '' }}>
                                        Insurance</option>
                                    <option value="online" {{ request('method') == 'online' ? 'selected' : '' }}>Online
                                    </option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <input type="date" class="form-control form-control-sm" name="from_date"
                                    value="{{ request('from_date') }}" placeholder="From Date">
                            </div>

                            <div class="col-md-3">
                                <input type="date" class="form-control form-control-sm" name="to_date" value="{{ request('to_date') }}"
                                    placeholder="To Date">
                            </div>

                            <div class="col-md-12 d-flex justify-content-end">
                                <button type="submit" class="btn btn-sm btn-primary me-2">
                                    <i class="fas fa-filter"></i> Filter
                                </button>
                                <a href="{{ route('dashboard.payments.index') }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-sync-alt"></i> Reset
                                </a>
                            </div>
                        </form>
                    </div>

                    <div class="table-responsive" style="overflow-x: auto;">
                        <table class="table small table-striped" style="min-width: 1000px;">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Patient</th>
                                    <th>Visit Date</th>
                                    <th>Amount</th>
                                    <th>Method</th>
                                    <th>Status</th>
                                    <th>Payment Date</th>
                                    @if (auth()->user()->can('payments.show') ||
                                            auth()->user()->can('payments.update') ||
                                            auth()->user()->can('payments.delete'))
                                        <th>Action</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($payments as $payment)
                                    <tr>
                                        <td>{{ $payment->id }}</td>
                                        <td>{{ $payment->visit->patient->FullName }}</td>
                                        <td>{{ $payment->visit->visit_date->format('M j, Y g:i A') }}</td>
                                        <td>{{ number_format($payment->amount, 2) }}</td>
                                        <td>{{ ucfirst(str_replace('_', ' ', $payment->method)) }}</td>
                                        <td>{{ ucfirst($payment->status) }}</td>
                                        <td>{{ $payment->created_at->format('M d, Y') }}</td>
                                        @if (auth()->user()->can('payments.show') ||
                                                auth()->user()->can('payments.update') ||
                                                auth()->user()->can('payments.delete'))
                                            <td>
                                                @can('payments.show')
                                                    <a href="{{ route('dashboard.payments.show', $payment) }}"
                                                        class="btn btn-outline-success btn-sm">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                @endcan
                                                @can('payments.update')
                                                    <a href="{{ route('dashboard.payments.edit', $payment) }}"
                                                        class="btn btn-outline-primary btn-sm">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endcan
                                                @can('payments.delete')
                                                    <button class="btn btn-outline-danger btn-sm delete-btn"
                                                        data-id="{{ $payment->id }}"
                                                        data-name="Payment ID: {{ $payment->id }}">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                @endcan
                                            </td>
                                        @endif
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            @if (auth()->user()->hasAbility('view-own-payments') && !auth()->user()->hasAbility('view-all-payments'))
                                                <p class="text-muted">When payments are recorded for your visits, they'll
                                                    appear here.</p>
                                            @else
                                                <p>No payments found</p>
                                            @endif
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{ $payments->withQueryString()->links() }}
                </div>
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
        route="dashboard.payments.destroy"
        itemName="payment"
        deleteBtnClass="delete-btn"
    />
@endpush

