@extends('layouts.admin')

@section('title', 'Fees / Payments - Barangay MIS')

@section('content')
<div class="page-surface">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 fw-bold text-primary">Fees / Payments</h4>
            <p class="text-muted mb-0">Manage financial transactions</p>
        </div>
        <a class="btn btn-primary" href="{{ route('admin.payments.create') }}">
            <i class="bi bi-plus-lg me-1"></i> New Payment
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success d-flex align-items-center shadow-sm border-0 mb-4">
            <i class="bi bi-check-circle-fill fs-4 me-2"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-muted small text-uppercase">Total Collected</div>
                    <div class="fs-3 fw-bold text-success">₱{{ number_format($stats['total_collected'], 2) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-muted small text-uppercase">Total Transactions</div>
                    <div class="fs-3 fw-bold text-primary">{{ $stats['success_count'] + $stats['pending_count'] }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <form method="GET" action="{{ route('admin.payments.index') }}" class="row g-3">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control" placeholder="Search..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="success" {{ request('status') === 'success' ? 'selected' : '' }}>Success</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-outline-primary w-100">Filter</button>
                </div>
                <div class="col-md-1">
                    <a href="{{ route('admin.payments.index') }}" class="btn btn-outline-secondary w-100">Clear</a>
                </div>
            </form>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-uppercase small text-muted">
                        <tr>
                            <th class="px-4 py-3">Control No.</th>
                            <th class="py-3">Document Type</th>
                            <th class="py-3">Type</th>
                            <th class="py-3">Resident</th>
                            <th class="py-3">Amount</th>
                            <th class="py-3">Status</th>
                            <th class="py-3">Date</th>
                            <th class="py-3 text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $payment)
                            <tr>
                                <td class="px-4">
                                    <span class="fw-bold text-primary">{{ $payment->documentRequest?->control_no ?? '-' }}</span>
                                </td>
                                <td>{{ $payment->documentRequest?->documentType?->name ?? $payment->description }}</td>
                                <td>
                                    <span class="badge bg-light text-dark border">
                                        {{ ucfirst(str_replace('_', ' ', $payment->payment_type)) }}
                                    </span>
                                </td>
                                <td>
                                    @if($payment->resident)
                                        {{ $payment->resident->first_name }} {{ $payment->resident->last_name }}
                                    @elseif($payment->documentRequest?->resident)
                                        {{ $payment->documentRequest->resident->first_name }} {{ $payment->documentRequest->resident->last_name }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="fw-bold">₱{{ number_format($payment->amount, 2) }}</td>
                                <td>
                                    @php
                                        $statusColor = match($payment->status) {
                                            'success' => 'success',
                                            'pending' => 'warning',
                                            'cancelled' => 'secondary',
                                            'failed' => 'danger',
                                            default => 'secondary'
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $statusColor }}-subtle text-{{ $statusColor }} border rounded-pill">
                                        {{ ucfirst($payment->status) }}
                                    </span>
                                </td>
                                <td>{{ $payment->created_at->format('M d, Y') }}</td>
                                <td class="text-end pe-4">
                                    <a class="btn btn-sm btn-info rounded-circle" style="width: 32px; height: 32px;" href="{{ route('admin.payments.show', $payment) }}">
                                        <i class="bi bi-eye-fill"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div class="text-muted opacity-50">
                                        <i class="bi bi-cash-stack fs-1 d-block mb-3"></i>
                                        <h5 class="fw-normal">No payments found</h5>
                                        <p class="small">Create a new payment to get started.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($payments->hasPages())
            <div class="card-footer bg-white border-top-0 py-3">
                {{ $payments->links() }}
            </div>
        @endif
    </div>
</div>
@endsection