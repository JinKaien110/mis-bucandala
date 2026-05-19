@extends('layouts.admin')

@section('title', 'Resident Reports - Barangay MIS')

@section('content')
<div class="page-surface">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 fw-bold text-primary">Resident Reports</h4>
            <p class="text-muted mb-0">Resident demographics and statistics</p>
        </div>
        <a class="btn btn-outline-secondary" href="{{ route('admin.reports.index') }}">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="fs-2 fw-bold text-primary">{{ number_format($demographics['total']) }}</div>
                    <div class="text-muted small">Total Residents</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="fs-2 fw-bold text-info">{{ number_format($demographics['male']) }}</div>
                    <div class="text-muted small">Male</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="fs-2 fw-bold text-danger">{{ number_format($demographics['female']) }}</div>
                    <div class="text-muted small">Female</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="fs-2 fw-bold text-success">{{ number_format($demographics['voters']) }}</div>
                    <div class="text-muted small">Voters</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}" placeholder="Date From">
                </div>
                <div class="col-md-3">
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}" placeholder="Date To">
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
                            <th class="py-3">Name</th>
                            <th class="py-3">Gender</th>
                            <th class="py-3">Age</th>
                            <th class="py-3">Voter</th>
                            <th class="py-3">Registered</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($residents as $resident)
                            <tr>
                                <td class="px-4">#{{ $resident->id }}</td>
                                <td>{{ $resident->first_name }} {{ $resident->last_name }}</td>
                                <td>{{ ucfirst($resident->gender ?? 'N/A') }}</td>
                                <td>{{ $resident->age }}</td>
                                <td>
                                    @if($resident->is_voter)
                                        <span class="badge bg-success-subtle text-success rounded-pill">Yes</span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary rounded-pill">No</span>
                                    @endif
                                </td>
                                <td>{{ $resident->created_at->format('M d, Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">No residents found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($residents->hasPages())
            <div class="card-footer bg-white py-3">
                {{ $residents->links() }}
            </div>
        @endif
    </div>
</div>
@endsection