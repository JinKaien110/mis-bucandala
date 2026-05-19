@extends('layouts.admin')

@section('title', 'Cases - Barangay MIS')

@section('content')
<div class="page-surface">
  
  <!-- Header -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h4 class="mb-1 fw-bold text-primary">Cases</h4>
      <p class="text-muted mb-0">Manage ongoing and closed cases</p>
    </div>
  </div>

  <div class="card border-0 shadow-sm">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="bg-light text-uppercase small text-muted">
            <tr>
              <th class="px-4 py-3 border-0">Case No</th>
              <th class="py-3 border-0">Blotter No</th>
              <th class="py-3 border-0">Status</th>
              <th class="py-3 border-0">Opened</th>
              <th class="py-3 border-0 text-end pe-4">Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($cases as $c)
              <tr>
                <td class="px-4">
                    <span class="fw-bold text-primary">{{ $c->case_no }}</span>
                </td>
                <td>
                    <a href="{{ route('admin.blotters.show', $c->blotter) }}" class="text-decoration-none text-dark">
                        {{ $c->blotter->blotter_no }}
                    </a>
                </td>
                <td>
                    @php
                        $statusColor = match(strtolower($c->status)) {
                            'ongoing' => 'warning',
                            'settled' => 'success',
                            'dismissed' => 'secondary',
                            'referred' => 'info',
                            'archived' => 'dark',
                            default => 'primary'
                        };
                    @endphp
                    <span class="badge bg-{{ $statusColor }}-subtle text-{{ $statusColor }} border border-{{ $statusColor }}-subtle rounded-pill">
                        {{ ucfirst($c->status) }}
                    </span>
                </td>
                <td>
                    <div class="d-flex align-items-center">
                        <i class="bi bi-calendar-event text-muted me-2"></i>
                        {{ $c->opened_at->format('M d, Y') }}
                        <small class="text-muted ms-1">{{ $c->opened_at->format('h:i A') }}</small>
                    </div>
                </td>
                <td class="text-end pe-4">
                  <a class="btn btn-sm btn-info rounded-circle" style="width: 32px; height: 32px;" href="{{ route('admin.cases.show', $c) }}" title="View Details">
                    <i class="bi bi-eye-fill"></i>
                  </a>
                </td>
              </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center py-5">
                        <div class="text-muted opacity-50">
                            <i class="bi bi-folder-x fs-1 d-block mb-3"></i>
                            <h5 class="fw-normal">No cases found</h5>
                            <p class="small">Cases are created from blotter records.</p>
                        </div>
                    </td>
                </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
    @if($cases->hasPages())
        <div class="card-footer bg-white border-top-0 py-3">
            {{ $cases->links() }}
        </div>
    @endif
  </div>
</div>
@endsection
