@extends('layouts.master.master')
@section('title', 'Generate Receipt')

@section('css')
    <style>
        :root {
            --secondary-color: #2c3e50;


        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
            color: var(--text-color);
        }

        @media print {
            body {
                background-color: white;
            }
            body * {
                visibility: hidden;
            }
            .receipt-container, .receipt-container * {
                visibility: visible;
            }
            .receipt-container {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                box-shadow: none !important;
                border: none !important;
            }
            .no-print {
                display: none !important;
            }
        }

        .receipt-container {
            max-width: 800px;
            margin: 40px auto;
            padding: 30px;
            border: 1px solid var(--border-color);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            background-color: white;
            border-radius: 10px;
        }

        .logo-container {
            display: flex;
            align-items: center;
            margin-bottom: 5px;
        }

        .clinic-name {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--secondary-color);
            margin-bottom: 0;
            margin-left: 15px;
        }

        .clinic-address {
            font-size: 0.9rem;
            color: var(--light-text);
        }

        .clinic-contact {
            font-size: 0.85rem;
            color: var(--light-text);
        }

        .receipt-header {
            border-bottom: 2px solid var(--border-color);
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .receipt-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 20px;
            text-align: center;
        }

        .receipt-info-label {
            color: var(--light-text);
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .receipt-info-value {
            font-weight: 500;
            margin-bottom: 15px;
        }

        table th {
            background-color: var(--light-gray) !important;
            color: var(--secondary-color);
            font-weight: 600;
        }

        .table-container {
            border-radius: 5px;
            overflow: hidden;
            border: 1px solid var(--border-color);
        }

        .table {
            margin-bottom: 0;
        }

        .section-title {
            color: var(--secondary-color);
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 15px;
            padding-bottom: 5px;
            border-bottom: 1px solid var(--border-color);
        }

        .section-title i {
            color: var(--primary-color);
        }

        .payment-method-badge {
            display: inline-block;
            padding: 5px 12px;
            background-color: var(--light-gray);
            border-radius: 20px;
            font-weight: 500;
            font-size: 0.9rem;
        }

        .payment-method-badge i {
            color: var(--primary-color);
        }

        .receipt-footer {
            border-top: 2px solid var(--border-color);
            padding-top: 20px;
            margin-top: 30px;
            text-align: center;
        }

        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .total-row {
            background-color: var(--light-gray);
            font-weight: 700;
        }

        .action-btn {
            transition: all 0.3s;
        }

        .action-btn:hover {
            transform: translateY(-2px);
        }

        .section {
            margin-bottom: 30px;
        }

        .notes-container {
            background-color: var(--light-gray);
            padding: 15px;
            border-radius: 5px;
            font-style: italic;
        }

        .watermark {
            position: absolute;
            opacity: 0.03;
            font-size: 150px;
            transform: rotate(-45deg);
            z-index: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-weight: bold;
            color: var(--secondary-color);
            pointer-events: none;
        }

        /* Customize the colors for icons */
        .text-muted {
            color: var(--light-text) !important;
        }

        .text-success {
            color: var(--success-color) !important;
        }

        /* Make the download button use primary color */
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: #2980b9;
            border-color: #2980b9;
        }
    </style>
@endsection

@section('content')
    <div class="receipt-container position-relative">
        <!-- Watermark visible only in print -->
        <div class="watermark d-none d-print-block">PAID</div>

        <!-- Action Buttons -->
        <div class="d-flex justify-content-end mb-4 no-print">
            <button onclick="window.print()" class="btn btn-sm btn-outline-secondary me-2 action-btn">
                <i class="fas fa-print me-1"></i> Print
            </button>
            <a href="{{ route('dashboard.payments.receipt', ['payment' => $payment, 'download' => true]) }}"
               class="btn btn-sm btn-primary action-btn">
                <i class="fas fa-download me-1"></i> Download PDF
            </a>
        </div>

        <!-- Clinic Header -->
        <div class="receipt-header">
            <div class="row">
                <div class="col-md-8">
                    <div class="logo-container">
                        <img src="{{ asset('front/assets/icons/logo.png') }}" height="80" alt="Logo">
                        <h2 class="clinic-name">{{ config('app.name') }}</h2>
                    </div>
                    <p class="clinic-address mb-0">Gaza</p>
                    <p class="clinic-contact">
                        <i class="fas fa-phone-alt me-1 text-muted"></i> +970592351151
                        <i class="fas fa-envelope ms-3 me-1 text-muted"></i> OralOasis@gmail.com
                    </p>
                </div>
                <div class="col-md-4 text-md-end">
                    <div class="receipt-info">
                        <p class="mb-1"><strong>Receipt #:</strong> {{ str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}</p>
                        <p class="mb-1"><strong>Date:</strong> {{ $payment->created_at->format('M d, Y h:i A') }}</p>
                        <p class="mb-0"><strong>Status:</strong>
                            <span class="status-badge bg-{{ $payment->status == 'completed' ? 'success' : ($payment->status == 'pending' ? 'warning' : 'danger') }}">
                                {{ ucfirst($payment->status) }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <h1 class="receipt-title">Payment Receipt</h1>

        <!-- Patient Details -->
        <div class="section">
            <h5 class="section-title"><i class="fas fa-user me-2"></i>Patient Information</h5>
            <div class="row">
                <div class="col-md-6">
                    <div class="receipt-info-label">Patient Name</div>
                    <div class="receipt-info-value">{{ $payment->visit->patient->FullName }}</div>
                </div>
                <div class="col-md-6">
                    <div class="receipt-info-label">Patient ID</div>
                    <div class="receipt-info-value">{{ $payment->visit->patient->id }}</div>
                </div>
            </div>
        </div>

        <!-- Visit Details -->
        <div class="section">
            <h5 class="section-title"><i class="fas fa-calendar-check me-2"></i>Visit Details</h5>
            <div class="table-container">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Visit Date</th>
                            <th>Service</th>
                            <th>Staff</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ $payment->visit->visit_date->format('M d, Y h:i A') }}</td>
                            <td>{{ $payment->visit->service->name ?? 'Not Recorded' }}</td>
                            <td>{{ $payment->staff->user->name ?? 'Not Recorded' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Payment Details -->
        <div class="section">
            <h5 class="section-title"><i class="fas fa-money-bill-wave me-2"></i>Payment Details</h5>
            <div class="table-container">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th class="text-end">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Payment for visit #{{ $payment->visit->id }}</td>
                            <td class="text-end">{{ number_format($payment->amount, 2) }}</td>
                        </tr>
                        <tr class="total-row">
                            <td class="text-end"><strong>Total</strong></td>
                            <td class="text-end"><strong>{{ number_format($payment->amount, 2) }}</strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Payment Method -->
        <div class="section">
            <h5 class="section-title"><i class="fas fa-credit-card me-2"></i>Payment Method</h5>
            <div class="payment-method-badge">
                @if($payment->method == 'credit_card')
                    <i class="far fa-credit-card me-2"></i>
                @elseif($payment->method == 'cash')
                    <i class="fas fa-money-bill-alt me-2"></i>
                @elseif($payment->method == 'bank_transfer')
                    <i class="fas fa-university me-2"></i>
                @else
                    <i class="fas fa-wallet me-2"></i>
                @endif
                {{ str_replace('_', ' ', ucfirst($payment->method)) }}
            </div>
            @if($payment->transaction_id)
                <p class="mt-2 mb-0 small text-muted">Transaction ID: {{ $payment->transaction_id }}</p>
            @endif
        </div>

        <!-- Notes -->
        @if($payment->notes)
        <div class="section">
            <h5 class="section-title"><i class="fas fa-clipboard me-2"></i>Notes</h5>
            <div class="notes-container">
                {{ $payment->notes }}
            </div>
        </div>
        @endif

        <!-- Footer -->
        <div class="receipt-footer">
            <div class="mb-3" style="color: rgb(18, 120, 46);">
                <i class="fas fa-check-circle text-success me-2" ></i>
                <span class="fw-bold">Official Receipt</span>
            </div>
            <p class="mb-1">Thank you for choosing {{ config('app.name') }}</p>
            <p class="small text-muted">For any questions regarding this receipt, please contact our billing department.</p>
        </div>
    </div>
@endsection
