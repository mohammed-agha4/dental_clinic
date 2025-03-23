@extends('layouts.master.master')

@section('content')
    <div class="container-fluid">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h4 class="mb-0">Payments</h4>
                <div class="dropdown no-arrow">
                    <a href="{{ route('dashboard.payments.create') }}" class="btn btn-dark btn-sm">
                        <i class="fas fa-plus fa-sm"></i> New Payment
                    </a>
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
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($payments as $payment)
                                <tr>
                                    <td>{{ $payment->id }}</td>
                                    <td>{{ $payment->visit->patient->fname }} {{ $payment->visit->patient->lname }}</td>
                                    <td>{{ $payment->visit->visit_date }}</td>
                                    <td>{{ number_format($payment->amount, 2) }}</td>
                                    <td>{{ $payment->method }}</td>
                                    <td>{{ $payment->status }}</td>
                                    <td>{{ $payment->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <a href="{{ route('dashboard.payments.show', $payment) }}"
                                            class="btn btn-outline-success btn-sm">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('dashboard.payments.edit', $payment) }}"
                                            class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button class="btn btn-outline-danger btn-sm delete-btn"
                                                data-id="{{ $payment->id }}"
                                                data-name="Payment ID: {{ $payment->id }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">No payments found</td>
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
                        form.action = "{{ route('dashboard.payments.destroy', '') }}/" + paymentId;
                        form.submit();
                    }
                });
            });
        });
    });
</script>
@endpush
