@extends('layouts.admin')

@section('title', 'Blotter Reports - Barangay MIS')

@section('content')
<div class="page-surface">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 fw-bold text-primary">Blotter Reports</h4>
            <p class="text-muted mb-0">Incident and case statistics</p>
        </div>
        <a class="btn btn-outline-secondary" href="{{ route('admin.reports.index') }}">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="fs-2 fw-bold text-primary">{{ $summary['total_blotters'] }}</div>
                    <div class="text-muted small">Total Blotters</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="fs-2 fw-bold text-warning">{{ $summary['with_case'] }}</div>
                    <div class="text-muted small">With Case</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="fs-2 fw-bold text-info">{{ $summary['ongoing_cases'] }}</div>
                    <div class="text-muted small">Ongoing Cases</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="fs-2 fw-bold text-success">{{ $summary['settled_cases'] }}</div>
                    <div class="text-muted small">Settled Cases</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light text-uppercase small text-muted">
                        <tr>
                            <th class="px-4 py-3">Blotter No</th>
                            <th class="py-3">Incident</th>
                            <th class="py-3">Location</th>
                            <th class="py-3">Case Status</th>
                            <th class="py-3">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($blotters as $blotter)
                            <tr>
                                <td class="px-4 fw-bold">{{ $blotter->blotter_no }}</td>
                                <td>{{ $blotter->incident_type }}</td>
                                <td>{{ $blotter->incident_location }}</td>
                                <td>
                                    @if($blotter->case)
                                        <span class="badge bg-warning text-dark rounded-pill">{{ ucfirst($blotter->case->status) }}</span>
                                    @else
                                        <span class="badge bg-secondary rounded-pill">No Case</span>
                                    @endif
                                </td>
                                <td>{{ $blotter->incident_date->format('M d, Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">No blotters found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($blotters->hasPages())
            <div class="card-footer py-3">{{ $blotters->links() }}</div>
        @endif
    </div>
</div>
@endsection