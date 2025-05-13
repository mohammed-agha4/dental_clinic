@extends('layouts.master.master')
@section('title', 'Payment Receipt')

@section('css')
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
            font-size: 14px;
        }

@media print {
    @page {
        margin: 0;
    }

    body {
        margin: 0;
        padding: 0;
        visibility: hidden;
        background: none;
    }

    .receipt-container {
        visibility: visible;
        position: relative;
        left: auto;
        top: auto;
        width: 100%;
        max-width: 100%;
        padding: 8px;
        margin: 0;
        border: none;
        page-break-after: avoid;
        page-break-inside: avoid;
    }

    .receipt-container * {
        visibility: visible;
    }

    .no-print {
        display: none !important;
    }

    /* Reset table and other elements to prevent shifting */
    table {
        width: 100% !important;
    }

    /* Ensure no extra margins on elements */
    .section, .clinic-header, .receipt-info, .receipt-footer {
        margin: 0;
        padding: 0;
    }
}

        .receipt-container {
            max-width: 700px;
            margin: 10px auto;
            padding: 15px;
            border: 1px solid #e0e0e0;
            background-color: white;
        }

        .clinic-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }

        .clinic-info {
            display: flex;
            align-items: center;
        }

        .clinic-name {
            font-weight: 600;
            margin-left: 10px;
            font-size: 16px;
        }

        .receipt-info {
            text-align: right;
            font-size: 13px;
        }

        .section {
            margin-bottom: 12px;
        }

        .section-title {
            font-weight: 600;
            font-size: 14px;
            margin-bottom: 8px;
            color: #333;
            display: flex;
            align-items: center;
        }

        .section-title i {
            margin-right: 5px;
            font-size: 13px;
        }

        .table-sm {
            font-size: 13px;
        }

        .table-sm th {
            padding: 5px 8px !important;
            background-color: #f8f9fa !important;
        }

        .table-sm td {
            padding: 5px 8px !important;
        }

        .payment-method {
            display: inline-block;
            padding: 3px 8px;
            background: #f8f9fa;
            border-radius: 3px;
            font-size: 13px;
        }

        .receipt-footer {
            margin-top: 15px;
            padding-top: 10px;
            border-top: 1px solid #eee;
            text-align: center;
            font-size: 13px;
        }

        .status-badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 12px;
            display: inline-block;
        }

        .notes {
            background: #f8f9fa;
            padding: 8px;
            border-radius: 3px;
            font-size: 13px;
        }
    </style>
@endsection

@section('content')
    <div class="receipt-container">
        <div class="no-print" style="text-align: right; margin-bottom: 10px;">
            <button class="btn" onclick="window.print()"
                style="background: none; border: 1px solid #b1adad; padding: 3px 8px; margin-right: 5px;">
                <i class="fas fa-print"></i> Print
            </button>

        </div>

        <!-- Header -->
        <div class="clinic-header">
            <div class="clinic-info">
                <img src="{{ asset('front/assets/icons/logo.png') }}" height="100" alt="Logo">
            </div>
            <div class="receipt-info">
                <div><strong>Receipt #:</strong> {{ str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}</div>
                <div><strong>Date:</strong> {{ $payment->created_at->format('M d, Y h:i A') }}</div>
                <div>
                    <strong>Status:</strong>
                    <span class="status-badge"
                        style="background: {{ $payment->status == 'completed' ? '#0d6321' : ($payment->status == 'pending' ? '#ffc107' : '#dc3545') }}; color: white;">
                        {{ ucfirst($payment->status) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Patient Details -->
        <div class="section">
            <div class="section-title">
                <i class="fas fa-user"></i> Patient Information
            </div>
            <div style="display: flex; justify-content: space-around;">
                <div>
                    <div style="font-size: 13px; color: #777;">Patient Name</div>
                    <div>{{ $payment->visit->patient->FullName }}</div>
                </div>
                <div>
                    <div style="font-size: 13px; color: #777;">Patient ID</div>
                    <div>{{ $payment->visit->patient->id }}</div>
                </div>
            </div>
        </div>

        <!-- Visit Details -->
        <div class="section">
            <div class="section-title">
                <i class="fas fa-calendar-check"></i> Visit Details
            </div>
            <table class="table table-sm" style="width: 100%;">
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
                        <td>{{ $payment->visit->service->service_name ?? 'N/A' }}</td>
                        <td>{{ $payment->staff->user->name ?? 'N/A' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Payment Details -->
        <div class="section">
            <div class="section-title">
                <i class="fas fa-money-bill-wave"></i> Payment Details
            </div>
            <table class="table table-sm" style="width: 100%;">
                <thead>
                    <tr>
                        <th>Description</th>
                        <th style="text-align: right;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Payment for visit #{{ $payment->visit->id }}</td>
                        <td style="text-align: right;">{{ number_format($payment->amount, 2) }}</td>
                    </tr>
                    <tr style="background-color: #f8f9fa;">
                        <td style="text-align: right;"><strong>Total</strong></td>
                        <td style="text-align: right;"><strong>{{ number_format($payment->amount, 2) }}</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="section">
            <div class="section-title">
                <i class="fas fa-credit-card"></i> Payment Method
            </div>
            <div class="payment-method">
                @if ($payment->method == 'credit_card')
                    <i class="far fa-credit-card"></i>
                @elseif($payment->method == 'cash')
                    <i class="fas fa-money-bill-alt"></i>
                @elseif($payment->method == 'bank_transfer')
                    <i class="fas fa-university"></i>
                @else
                    <i class="fas fa-wallet"></i>
                @endif
                {{ str_replace('_', ' ', ucfirst($payment->method)) }}
            </div>
            @if ($payment->transaction_id)
                <div style="font-size: 12px; color: #777; margin-top: 3px;">
                    Transaction ID: {{ $payment->transaction_id }}
                </div>
            @endif
        </div>

        <!-- Notes -->
        @if ($payment->notes)
            <div class="section">
                <div class="section-title">
                    <i class="fas fa-clipboard"></i> Notes
                </div>
                <div class="notes">
                    {{ $payment->notes }}
                </div>
            </div>
        @endif

        <!-- Footer -->
        <div class="receipt-footer">
            <div style="color: #0d6321; margin-bottom: 5px;">
                <i class="fas fa-check-circle"></i> Payment Received
            </div>
            <div style="color: #777;">
                Thank you for choosing {{ config('app.name') }}
            </div>
        </div>
    </div>
@endsection
