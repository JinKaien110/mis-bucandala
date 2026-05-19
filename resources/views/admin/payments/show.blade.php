@extends('layouts.admin')

@section('title', 'Payment Details - Barangay MIS')

@section('content')
<div class="page-surface">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 fw-bold text-primary">Payment - {{ $payment->documentRequest?->control_no ?? $payment->id }}</h4>
            <p class="text-muted mb-0">{{ $payment->documentRequest?->documentType?->name ?? $payment->description }}</p>
        </div>
        <div class="d-flex gap-2">
            <a class="btn btn-outline-secondary" href="{{ route('admin.payments.index') }}">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
            @if($payment->status === 'pending')
                <form method="POST" action="{{ route('admin.payments.markPaid', $payment) }}">
                    @csrf
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-lg me-1"></i> Mark as Paid
                    </button>
                </form>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success d-flex align-items-center shadow-sm border-0 mb-4">
            <i class="bi bi-check-circle-fill fs-4 me-2"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold text-primary">Payment Details</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded">
                                <small class="text-muted text-uppercase fw-bold d-block">Amount</small>
                                <span class="fs-4 fw-bold text-success">₱{{ number_format($payment->amount, 2) }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded">
                                <small class="text-muted text-uppercase fw-bold d-block">Status</small>
                                @php
                                    $statusColor = match($payment->status) {
                                        'success' => 'success',
                                        'pending' => 'warning',
                                        'cancelled' => 'secondary',
                                        'failed' => 'danger',
                                        default => 'secondary'
                                    };
                                @endphp
                                <span class="badge bg-{{ $statusColor }}-subtle text-{{ $statusColor }} border rounded-pill fs-6">
                                    {{ ucfirst($payment->status) }}
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted text-uppercase fw-bold d-block">Payment Type</small>
                            <span>{{ ucfirst(str_replace('_', ' ', $payment->payment_type)) }}</span>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted text-uppercase fw-bold d-block">OR Number</small>
                            <span>{{ $payment->or_number ?? 'N/A' }}</span>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted text-uppercase fw-bold d-block">Resident</small>
                            <span>{{ $payment->resident?->first_name ?? $payment->documentRequest?->resident?->first_name }} {{ $payment->resident?->last_name ?? $payment->documentRequest?->resident?->last_name ?? 'N/A' }}</span>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted text-uppercase fw-bold d-block">Document Request</small>
                            <span>{{ $payment->documentRequest?->control_no ?? 'N/A' }}</span>
                        </div>
                        <div class="col-md-12">
                            <small class="text-muted text-uppercase fw-bold d-block">Description</small>
                            <span>{{ $payment->description }}</span>
                        </div>
                        @if($payment->notes)
                            <div class="col-md-12">
                                <small class="text-muted text-uppercase fw-bold d-block">Notes</small>
                                <span>{{ $payment->notes }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold text-primary">Timestamps</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted text-uppercase fw-bold d-block">Created</small>
                        <span>{{ $payment->created_at->format('M d, Y h:i A') }}</span>
                    </div>
                    @if($payment->paid_at)
                        <div class="mb-3">
                            <small class="text-muted text-uppercase fw-bold d-block">Paid</small>
                            <span>{{ $payment->paid_at->format('M d, Y h:i A') }}</span>
                        </div>
                    @endif
                    @if($payment->collector)
                        <div>
                            <small class="text-muted text-uppercase fw-bold d-block">Collected By</small>
                            <span>{{ $payment->collector->name }}</span>
                        </div>
                    @endif
                </div>
            </div>
            <div class="mt-3 d-flex gap-2">
                <a href="{{ route('admin.payments.edit', $payment) }}" class="btn btn-outline-primary flex-grow-1">
                    <i class="bi bi-pencil me-1"></i> Edit
                </a>
                @if($payment->status === 'pending')
                    <form method="POST" action="{{ route('admin.payments.cancel', $payment) }}" class="flex-grow-1">
                        @csrf
                        <button type="submit" class="btn btn-outline-warning w-100">
                            <i class="bi bi-x-lg me-1"></i> Cancel
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection