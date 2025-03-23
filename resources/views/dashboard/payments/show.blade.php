@extends('layouts.master.master')

@section('content')

{{-- @dd( $payment->visit->visit_date->format('M d, Y H:i')) --}}

    <div class="container-fluid">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h5>Payment Details</h5>
                <div>

                    <a href="#" class="btn btn-success btn-sm" target="_blank">
                        <i class="fas fa-file-invoice fa-sm"></i> Receipt
                    </a>
                    <a href="{{ route('dashboard.payments.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left fa-sm"></i> Back to Main
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 ">
                        <div class="card mb-4 h-full">
                            <div class="card-header">
                                <h6 class="m-0 font-weight-bold text-success">Payment Information</h6>
                            </div>
                            <div class="card-body">
                                <table class="table table-borderless ">

                                    <tr>
                                        <th>Amount:</th>
                                        <td>{{ number_format($payment->amount, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Payment Method:</th>
                                        <td>{{ $payment->method }}</td>
                                    </tr>
                                    <tr>
                                        <th>Status:</th>
                                        <td>{{ $payment->status }}</td>
                                    </tr>
                                    @if ($payment->transaction_id)
                                        <tr>
                                            <th>Transaction ID:</th>
                                            <td>{{ $payment->transaction_id }}</td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <th>Created By:</th>
                                        <td>{{ $payment->staff->user->name }} ({{ ucfirst($payment->staff->role) }})</td>
                                    </tr>
                                    <tr>
                                        <th>Date Created:</th>
                                        <td>{{ $payment->created_at->format('M d, Y H:i') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Last Updated:</th>
                                        <td>{{ $payment->updated_at->format('M d, Y H:i') }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="m-0 font-weight-bold text-success">Visit Information</h6>
                            </div>
                            <div class="card-body">
                                <table class="table table-borderless">
                                    <tr>
                                        <th>Patient:</th>
                                        <td>
                                            <a class=" text-dark-emphasis text-bold " href="{{ route('dashboard.patients.show', $payment->visit->patient) }}">
                                                {{ $payment->visit->patient->fname }}
                                                {{ $payment->visit->patient->lname }}
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Visit Date:</th>
                                        <td>{{ $payment->visit->visit_date/*->format('M d, Y') */}}</td>
                                    </tr>
                                    <tr>
                                        <th>Service:</th>
                                        <td>{{ $payment->visit->service->service_name ?? 'No service' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Service Price:</th>
                                        <td>{{ number_format($payment->visit->service->service_price ?? 0, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Dentist:</th>
                                        <td>{{ $payment->visit->staff->user->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Notes:</th>
                                        <td>{{ $payment->visit->treatment_notes ?: 'No notes' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                @if ($payment->notes)
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="m-0 font-weight-bold text-success">Payment Notes</h6>
                                </div>
                                <div class="card-body">
                                    <p>{{ $payment->notes }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Buttons for actions -->
                <div class="mt-4">
                    <a href="{{ route('dashboard.payments.edit', $payment) }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Edit Payment
                    </a>
                    {{-- <a href="{{ route('payments.receipt', $payment) }}" class="btn btn-success" target="_blank">
                        <i class="fas fa-file-invoice"></i> Generate Receipt
                    </a> --}}
                    <form action="{{ route('dashboard.payments.destroy', $payment) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger"
                            onclick="return confirm('Are you sure you want to delete this payment?')">
                            <i class="fas fa-trash"></i> Delete Payment
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
