@extends('layouts.admin')

@section('title', 'Households - Barangay MIS')

@section('content')

<div class="page-surface">

  <!-- Header -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h4 class="mb-1 fw-bold text-primary">Households</h4>
      <p class="text-muted mb-0">Manage household records and residency</p>
    </div>

    <a href="{{ route('admin.households.create') }}" class="btn btn-primary shadow-sm">
      <i class="bi bi-plus-lg me-1"></i> Add Household
    </a>
  </div>

  <!-- Stats & Search -->
  <div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100 bg-primary text-white">
            <div class="card-body d-flex align-items-center">
                <div class="rounded-circle bg-white bg-opacity-25 p-3 me-3">
                    <i class="bi bi-houses fs-3 text-white"></i>
                </div>
                <div>
                    <h6 class="card-title mb-0 text-white-50">Total Households</h6>
                    <h3 class="mb-0 fw-bold">{{ $households->total() }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <form method="GET" class="w-100">
                    <div class="input-group input-group-lg">
                        <span class="input-group-text bg-white border-end-0 text-muted">
                            <i class="bi bi-search"></i>
                        </span>
                        <input 
                          type="text" 
                          name="q" 
                          class="form-control border-start-0 ps-0" 
                          placeholder="Search household code, address, or purok..."
                          value="{{ $search }}"
                        >
                        <button type="submit" class="btn btn-primary px-4">Search</button>
                        @if($search)
                          <a href="{{ route('admin.households.index') }}" class="btn btn-outline-secondary px-3">
                            <i class="bi bi-x-lg"></i>
                          </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
  </div>

  <!-- Table -->
  <div class="card border-0 shadow-sm">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
          <thead class="bg-light text-uppercase small text-muted">
            <tr>
              <th class="px-4 py-3 border-0">Household Code</th>
              <th class="py-3 border-0">Address</th>
              <th class="py-3 border-0 text-center">Members</th>
              <th class="py-3 border-0 text-end pe-4">Actions</th>
            </tr>
          </thead>

          <tbody>
            @forelse($households as $household)
              <tr>
                <td class="px-4">
                  <div class="d-flex align-items-center">
                    <div class="avatar avatar-sm bg-primary-subtle text-primary rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="bi bi-house-door-fill"></i>
                    </div>
                    <div>
                        <span class="fw-bold text-dark d-block">
                            {{ $household->household_code }}
                        </span>
                        <small class="text-muted">ID: {{ $household->id }}</small>
                    </div>
                  </div>
                </td>

                <td>
                    <span class="text-dark">{{ $household->address_line }}</span>
                </td>

                <td class="text-center">
                  <span class="badge rounded-pill bg-info-subtle text-info border border-info-subtle px-3 py-2">
                    <i class="bi bi-people-fill me-1"></i> {{ $household->members_count }}
                  </span>
                </td>

                <td class="text-end pe-4">
                  <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.households.show', $household) }}"
                       class="btn btn-sm btn-info rounded-circle"
                       style="width: 32px; height: 32px;"
                       data-bs-toggle="tooltip"
                       title="View Details">
                      <i class="bi bi-eye-fill"></i>
                    </a>

                    <a href="{{ route('admin.households.edit', $household) }}"
                       class="btn btn-sm btn-warning rounded-circle"
                       style="width: 32px; height: 32px;"
                       data-bs-toggle="tooltip"
                       title="Edit Household">
                      <i class="bi bi-pencil-fill"></i>
                    </a>

                    <form action="{{ route('admin.households.destroy', $household) }}" method="POST" class="d-inline">
                      @csrf
                      @method('DELETE')
                      <button type="submit"
                              class="btn btn-sm btn-danger rounded-circle"
                              style="width: 32px; height: 32px;"
                              data-bs-toggle="tooltip"
                              title="Archive Household"
                              onclick="return confirm('Are you sure you want to archive this household?')">
                        <i class="bi bi-archive-fill"></i>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="text-center py-5">
                  <div class="text-muted opacity-50">
                    <i class="bi bi-house-slash fs-1 d-block mb-3"></i>
                    <h5 class="fw-normal">No households found</h5>
                    <p class="small">Try adjusting your search or add a new household.</p>
                  </div>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
    
    @if($households->hasPages())
    <div class="card-footer bg-white border-top-0 py-3">
        <div class="d-flex justify-content-between align-items-center">
            <div class="text-muted small">
              Showing {{ $households->firstItem() ?? 0 }} to {{ $households->lastItem() ?? 0 }} of {{ $households->total() }} results
            </div>
            <div>
                {{ $households->links() }}
            </div>
        </div>
    </div>
    @endif
  </div>

</div>

@endsection

@push('scripts')
<script>
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl)
    })
</script>
@endpush
