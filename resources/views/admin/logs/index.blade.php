@extends('layouts.admin')

@section('title', 'Activity Logs - Barangay MIS')

@section('content')
<style>
  .log-detail-modal {
    z-index: 1060 !important;
  }
  .log-detail-modal .modal-dialog {
    z-index: 1061 !important;
  }
</style>
<div class="page-surface">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h4 class="mb-1">Activity Logs</h4>
      <p class="text-muted mb-0">Audit trail of all system actions</p>
    </div>
  </div>

  <!-- Filters -->
  <div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
      <form method="GET" class="row g-3">
        <div class="col-md-2">
          <label class="form-label small fw-semibold">Module</label>
          <select name="module" class="form-select">
            <option value="">All Modules</option>
            @foreach($modules as $module)
              <option value="{{ $module }}" {{ ($filters['module'] ?? '') == $module ? 'selected' : '' }}>
                {{ ucfirst($module) }}
              </option>
            @endforeach
          </select>
        </div>
        <div class="col-md-2">
          <label class="form-label small fw-semibold">Action</label>
          <select name="action" class="form-select">
            <option value="">All Actions</option>
            @foreach($actions as $action)
              <option value="{{ $action }}" {{ ($filters['action'] ?? '') == $action ? 'selected' : '' }}>
                {{ ucfirst($action) }}
              </option>
            @endforeach
          </select>
        </div>
        <div class="col-md-2">
          <label class="form-label small fw-semibold">Date From</label>
          <input type="date" name="date_from" class="form-control" value="{{ $filters['date_from'] ?? '' }}">
        </div>
        <div class="col-md-2">
          <label class="form-label small fw-semibold">Date To</label>
          <input type="date" name="date_to" class="form-control" value="{{ $filters['date_to'] ?? '' }}">
        </div>
        <div class="col-md-3">
          <label class="form-label small fw-semibold">Search</label>
          <input type="text" name="search" class="form-control" placeholder="Search..." value="{{ $filters['search'] ?? '' }}">
        </div>
        <div class="col-md-1 d-flex align-items-end">
          <button type="submit" class="btn btn-primary w-100">
            <i class="bi bi-filter"></i> Filter
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Logs Table -->
  <div class="card border-0 shadow-sm">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead class="table-light">
            <tr>
              <th class="px-4 py-3">Date/Time</th>
              <th class="py-3">User</th>
              <th class="py-3">Module</th>
              <th class="py-3">Action</th>
              <th class="py-3">Record ID</th>
              <th class="py-3">IP Address</th>
              <th class="py-3">Details</th>
            </tr>
          </thead>
          <tbody>
            @forelse($logs as $log)
              <tr>
                <td class="px-4">
                  <small class="text-muted">{{ $log->created_at->format('M d, Y') }}</small><br>
                  <span class="fw-semibold">{{ $log->created_at->format('h:i A') }}</span>
                </td>
                <td>
                  @if($log->user)
                    <div class="fw-semibold">{{ $log->user->name }}</div>
                    <small class="text-muted">{{ $log->user->email }}</small>
                  @else
                    <span class="text-muted">System</span>
                  @endif
                </td>
                <td>
                  <span class="badge bg-primary bg-opacity-10 text-primary">{{ ucfirst($log->module) }}</span>
                </td>
                <td>
                  @switch($log->action)
                    @case('created')
                      <span class="badge bg-success bg-opacity-10 text-success"><i class="bi bi-plus-circle"></i> Created</span>
                      @break
                    @case('updated')
                      <span class="badge bg-warning bg-opacity-10 text-warning"><i class="bi bi-pencil"></i> Updated</span>
                      @break
                    @case('deleted')
                      <span class="badge bg-danger bg-opacity-10 text-danger"><i class="bi bi-trash"></i> Deleted</span>
                      @break
                    @case('printed')
                      <span class="badge bg-info bg-opacity-10 text-info"><i class="bi bi-printer"></i> Printed</span>
                      @break
                    @case('released')
                      <span class="badge bg-success bg-opacity-10 text-success"><i class="bi bi-check-circle"></i> Released</span>
                      @break
                    @default
                      <span class="badge bg-secondary bg-opacity-10 text-secondary">{{ ucfirst($log->action) }}</span>
                  @endswitch
                </td>
                <td>{{ $log->record_id ?? '-' }}</td>
                <td><small class="text-muted">{{ $log->ip_address ?? '-' }}</small></td>
                <td>
  @if($log->new_data || $log->old_data)
    <button class="btn btn-sm btn-info rounded-circle" style="width: 32px; height: 32px;" data-bs-toggle="modal" data-bs-target="#logModal{{ $log->id }}" title="View Details">
      <i class="bi bi-eye-fill"></i>
    </button>
  @else
    <span class="text-muted">-</span>
  @endif
</td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="text-center py-5">
                  <div class="text-muted">
                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                    No activity logs found
                  </div>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Pagination -->
  <div class="d-flex justify-content-between align-items-center mt-4">
    <div class="text-muted small">
      Showing {{ $logs->firstItem() ?? 0 }} to {{ $logs->lastItem() ?? 0 }} of {{ $logs->total() }} results
    </div>
    {{ $logs->links() }}
  </div>
</div>
@foreach($logs as $log)
  @if($log->new_data || $log->old_data)
    <div class="modal fade" id="logModal{{ $log->id }}" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
          <div class="modal-header bg-light">
            <h5 class="modal-title fw-bold">
              <i class="bi bi-clock-history me-2"></i>Activity Details
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <!-- Activity Info -->
            <div class="alert alert-info bg-info-subtle border-0 mb-4">
              <div class="row">
                <div class="col-md-4">
                  <small class="text-muted d-block">User</small>
                  <strong>{{ $log->user->name ?? 'System' }}</strong>
                </div>
                <div class="col-md-4">
                  <small class="text-muted d-block">Module</small>
                  <strong>{{ ucfirst($log->module) }}</strong>
                </div>
                <div class="col-md-4">
                  <small class="text-muted d-block">Action</small>
                  <span class="badge @if($log->action === 'created') bg-success @elseif($log->action === 'updated') bg-warning @elseif($log->action === 'deleted') bg-danger @else bg-secondary @endif">
                    {{ ucfirst($log->action) }}
                  </span>
                </div>
              </div>
              <div class="row mt-2">
                <div class="col-md-4">
                  <small class="text-muted d-block">Record ID</small>
                  <strong>#{{ $log->record_id }}</strong>
                </div>
                <div class="col-md-4">
                  <small class="text-muted d-block">Date/Time</small>
                  <strong>{{ $log->created_at->format('M d, Y h:i A') }}</strong>
                </div>
                <div class="col-md-4">
                  <small class="text-muted d-block">IP Address</small>
                  <strong>{{ $log->ip_address ?? 'N/A' }}</strong>
                </div>
              </div>
            </div>

            <div class="row">
              @if($log->old_data)
                <div class="col-md-6">
                  <div class="card border-danger">
                    <div class="card-header bg-danger-subtle text-danger py-2">
                      <i class="bi bi-arrow-left-circle me-1"></i>Previous Data
                    </div>
                    <div class="card-body p-0">
                      <table class="table table-sm mb-0">
                        @foreach($log->old_data as $key => $value)
                          <tr>
                            <td class="text-muted small text-end" style="width: 40%;">{{ ucfirst(str_replace('_', ' ', $key)) }}</td>
                            <td class="small">{{ is_array($value) ? json_encode($value) : ($value ?? '-') }}</td>
                          </tr>
                        @endforeach
                      </table>
                    </div>
                  </div>
                </div>
              @endif
              @if($log->new_data)
                <div class="col-md-6">
                  <div class="card border-success">
                    <div class="card-header bg-success-subtle text-success py-2">
                      <i class="bi bi-arrow-right-circle me-1"></i>New Data
                    </div>
                    <div class="card-body p-0">
                      <table class="table table-sm mb-0">
                        @foreach($log->new_data as $key => $value)
                          <tr>
                            <td class="text-muted small text-end" style="width: 40%;">{{ ucfirst(str_replace('_', ' ', $key)) }}</td>
                            <td class="small">{{ is_array($value) ? json_encode($value) : ($value ?? '-') }}</td>
                          </tr>
                        @endforeach
                      </table>
                    </div>
                  </div>
                </div>
              @endif
            </div>
          </div>
          <div class="modal-footer bg-light">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>
  @endif
@endforeach
@endsection
