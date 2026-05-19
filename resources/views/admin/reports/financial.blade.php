@extends('layouts.admin')

@section('title', 'Financial Reports - Barangay MIS')

@section('content')
<div class="page-surface">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 fw-bold text-primary">Financial Reports</h4>
            <p class="text-muted mb-0">Payment collections and transactions</p>
        </div>
        <a class="btn btn-outline-secondary" href="{{ route('admin.reports.index') }}">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="fs-2 fw-bold text-success">₱{{ number_format($summary['total_collected'], 2) }}</div>
                    <div class="text-muted small">Total Collected</div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="fs-2 fw-bold text-primary">{{ number_format($summary['total_transactions']) }}</div>
                    <div class="text-muted small">Total Transactions</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-3">
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-outline-primary w-100">Filter</button>
                </div>
            </form>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light text-uppercase small text-muted">
                        <tr>
                            <th class="px-4 py-3">ID</th>
                            <th class="py-3">Description</th>
                            <th class="py-3">Amount</th>
                            <th class="py-3">Resident</th>
                            <th class="py-3">Paid Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $payment)
                            <tr>
                                <td class="px-4">#{{ $payment->id }}</td>
                                <td>{{ $payment->description }}</td>
                                <td class="fw-bold">₱{{ number_format($payment->amount, 2) }}</td>
                                <td>{{ $payment->resident?->first_name }} {{ $payment->resident?->last_name ?? '-' }}</td>
                                <td>{{ $payment->paid_at?->format('M d, Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">No payments found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($payments->hasPages())
            <div class="card-footer py-3">{{ $payments->links() }}</div>
        @endif
    </div>
</div>
@endsection