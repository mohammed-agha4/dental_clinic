@extends('layouts.master.master')
@section('title', 'Payment')


@section('content')
    <div class="container-fluid">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h4 class="mb-0">Payments</h4>
                <div class="dropdown no-arrow">
                    @can('payments.create')
                        <a href="{{ route('dashboard.payments.create') }}" class="btn btn-dark btn-sm">
                            <i class="fas fa-plus fa-sm"></i> New Payment
                        </a>
                    @endcan
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped small" width="100%">
                        <thead>
                            <tr>
                                <th>ID</th>
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
                                    <td> {{ $payment->method }} </td>
                                    <td> {{ $payment->status }} </td>
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
                                                    data-id="{{ $payment->id }}" data-name="Payment ID: {{ $payment->id }}">
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
                                            <i class="fas fa-money-bill-wave fa-3x text-muted mb-3"></i>
                                            <h5>No payments recorded for your visits</h5>
                                            <p class="text-muted">When payments are recorded for your visits, they'll appear
                                                here.</p>
                                        @else
                                            <i class="fas fa-money-bill-alt fa-3x text-muted mb-3"></i>
                                            <h5>No payments found</h5>
                                            <p class="text-muted">When payments are recorded, they'll appear here.</p>
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    {{ $payments->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden Delete Form -->
    <form id="delete-form" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
@endsection

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle delete button clicks
            document.querySelectorAll('.delete-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const paymentId = this.getAttribute('data-id');
                    const paymentName = this.getAttribute('data-name');

                    Swal.fire({
                        title: 'Are you sure?',
                        html: `You are about to delete the payment: <strong>${paymentName}</strong>`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc3545',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Yes, delete it!',
                        cancelButtonText: 'Cancel',
                        reverseButtons: true,
                        focusCancel: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const form = document.getElementById('delete-form');
                            form.action =
                                "{{ route('dashboard.payments.destroy', '') }}/" +
                                paymentId;
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>
@endpush
