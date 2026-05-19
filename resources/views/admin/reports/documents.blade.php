@extends('layouts.admin')

@section('title', 'Document Reports - Barangay MIS')

@section('content')
<div class="page-surface">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 fw-bold text-primary">Document Reports</h4>
            <p class="text-muted mb-0">Document request statistics</p>
        </div>
        <a class="btn btn-outline-secondary" href="{{ route('admin.reports.index') }}">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="fs-2 fw-bold text-primary">{{ $summary['total_requests'] }}</div>
                    <div class="text-muted small">Total Requests</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="fs-2 fw-bold text-warning">{{ $summary['pending'] }}</div>
                    <div class="text-muted small">Pending</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="fs-2 fw-bold text-info">{{ $summary['ready'] }}</div>
                    <div class="text-muted small">Ready</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="fs-2 fw-bold text-success">{{ $summary['released'] }}</div>
                    <div class="text-muted small">Released</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <form method="GET" class="row g-3">
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="ready" {{ request('status') === 'ready' ? 'selected' : '' }}>Ready</option>
                        <option value="released" {{ request('status') === 'released' ? 'selected' : '' }}>Released</option>
                    </select>
                </div>
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
                            <th class="py-3">Resident</th>
                            <th class="py-3">Document Type</th>
                            <th class="py-3">Status</th>
                            <th class="py-3">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($documents as $doc)
                            <tr>
                                <td class="px-4">#{{ $doc->id }}</td>
                                <td>{{ $doc->resident?->first_name }} {{ $doc->resident?->last_name }}</td>
                                <td>{{ $doc->documentType?->name }}</td>
                                <td>
                                    @php $statusColor = match($doc->status) { 'pending' => 'warning', 'ready' => 'info', 'released' => 'success', default => 'secondary' }; @endphp
                                    <span class="badge bg-{{ $statusColor }}-subtle text-{{ $statusColor }} rounded-pill">{{ ucfirst($doc->status) }}</span>
                                </td>
                                <td>{{ $doc->created_at->format('M d, Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">No documents found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($documents->hasPages())
            <div class="card-footer py-3">{{ $documents->links() }}</div>
        @endif
    </div>
</div>
@endsection