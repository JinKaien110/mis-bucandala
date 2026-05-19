@extends('layouts.admin')

@section('title', 'Reports - Barangay MIS')

@section('content')
<div class="page-surface">
    <div class="mb-4">
        <h4 class="mb-1 fw-bold text-primary">Reports Dashboard</h4>
        <p class="text-muted mb-0">System statistics and reports (Captain Access Only)</p>
    </div>

    <div class="row g-4">
        <div class="col-md-4">
            <a href="{{ route('admin.reports.residents') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center py-4">
                        <i class="bi bi-people-fill fs-1 text-primary mb-3"></i>
                        <h5 class="fw-bold">Resident Reports</h5>
                        <p class="text-muted small mb-0">Demographics, voters, age groups</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('admin.reports.financial') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center py-4">
                        <i class="bi bi-cash-stack fs-1 text-success mb-3"></i>
                        <h5 class="fw-bold">Financial Reports</h5>
                        <p class="text-muted small mb-0">Collections, payments, transactions</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('admin.reports.blotters') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center py-4">
                        <i class="bi bi-shield-exclamation fs-1 text-warning mb-3"></i>
                        <h5 class="fw-bold">Blotter Reports</h5>
                        <p class="text-muted small mb-0">Incidents, cases, status</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('admin.reports.documents') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center py-4">
                        <i class="bi bi-file-earmark-text fs-1 text-info mb-3"></i>
                        <h5 class="fw-bold">Document Reports</h5>
                        <p class="text-muted small mb-0">Requests, types, status</p>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-white py-3">
            <h6 class="mb-0 fw-bold text-primary">Quick Statistics</h6>
        </div>
        <div class="card-body">
            <div class="row g-4">
                <div class="col-md-3 text-center">
                    <div class="fs-3 fw-bold text-primary">{{ number_format($stats['total_residents']) }}</div>
                    <div class="text-muted small">Total Residents</div>
                </div>
                <div class="col-md-3 text-center">
                    <div class="fs-3 fw-bold text-info">{{ number_format($stats['total_households']) }}</div>
                    <div class="text-muted small">Total Households</div>
                </div>
                <div class="col-md-3 text-center">
                    <div class="fs-3 fw-bold text-warning">{{ number_format($stats['total_blotters']) }}</div>
                    <div class="text-muted small">Total Blotters</div>
                </div>
                <div class="col-md-3 text-center">
                    <div class="fs-3 fw-bold text-success">₱{{ number_format($stats['total_collected'], 2) }}</div>
                    <div class="text-muted small">Total Collected</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection