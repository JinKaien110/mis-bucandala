@extends('layouts.admin')

@section('title', 'Pet Registration - Barangay MIS')

@section('content')
<div class="page-surface">
  <!-- Alert Message -->
  <div id="message" class="alert" style="display:none;"></div>

  @if(session('success'))
    <div class="alert alert-success d-flex align-items-center shadow-sm border-0 mb-4" id="successAlert">
      <i class="bi bi-check-circle-fill fs-4 me-2"></i>
      <div>{{ session('success') }}</div>
    </div>
    <script>
      setTimeout(() => document.getElementById('successAlert')?.remove(), 5000);
    </script>
  @endif

  @if(session('error'))
    <div class="alert alert-danger d-flex align-items-center shadow-sm border-0 mb-4" id="errorAlert">
      <i class="bi bi-exclamation-circle-fill fs-4 me-2"></i>
      <div>{{ session('error') }}</div>
    </div>
    <script>
      setTimeout(() => document.getElementById('errorAlert')?.remove(), 5000);
    </script>
  @endif

  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h4 class="mb-1 fw-bold text-primary">
        <i class="bi bi-paw me-2"></i>Pet Registration
      </h4>
      <p class="text-muted mb-0">Manage registered pets in the barangay</p>
    </div>
    <a href="{{ route('admin.pets.create') }}" class="btn btn-success">
      <i class="bi bi-plus-lg me-1"></i> Register Pet
    </a>
  </div>

   <!-- Stats Cards -->
   <div class="row g-3 mb-4">
     <div class="col-md-4">
      <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);">
        <div class="card-body py-3">
          <div class="d-flex align-items-center">
            <div class="rounded-circle bg-primary bg-opacity-25 p-3 me-3">
              <i class="bi bi-paw text-primary fs-4"></i>
            </div>
            <div>
              <div class="text-muted small">Total Pets</div>
              <div class="fs-4 fw-bold text-primary">{{ $pets->total() }}</div>
            </div>
          </div>
        </div>
      </div>
    </div>
     <div class="col-md-4">
       <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);">
        <div class="card-body py-3">
          <div class="d-flex align-items-center">
            <div class="rounded-circle bg-success bg-opacity-25 p-3 me-3">
              <i class="bi bi-capsule text-success fs-4"></i>
            </div>
            <div>
              <div class="text-muted small">Vaccinated</div>
              <div class="fs-4 fw-bold text-success">{{ $pets->where('vaccination_status', 'up-to-date')->count() }}</div>
            </div>
          </div>
        </div>
      </div>
    </div>
     <div class="col-md-4">
       <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #ffebee 0%, #ffcdd2 100%);">
        <div class="card-body py-3">
          <div class="d-flex align-items-center">
            <div class="rounded-circle bg-danger bg-opacity-25 p-3 me-3">
              <i class="bi bi-x-circle-fill text-danger fs-4"></i>
            </div>
            <div>
              <div class="text-muted small">Not Vaccinated</div>
              <div class="fs-4 fw-bold text-danger">{{ $pets->where('vaccination_status', 'not-vaccinated')->count() }}</div>
            </div>
          </div>
        </div>
       </div>
     </div>
   </div>
  </div>

  <!-- Filters -->
  <div class="card border-0 shadow-sm mb-4">
    <div class="card-body py-3">
      <form method="GET" class="row g-3 align-items-center">
        <div class="col-md-4">
          <div class="input-group">
            <span class="input-group-text bg-light border-0">
              <i class="bi bi-search text-muted"></i>
            </span>
            <input type="text" name="q" class="form-control border-0 bg-light" placeholder="Search pet name or owner..." value="{{ $search }}">
          </div>
        </div>
        <div class="col-md-4">
          <select name="species" class="form-select border-0 bg-light">
            <option value="">All Species</option>
            <option value="dog" {{ $species === 'dog' ? 'selected' : '' }}>🐕 Dog</option>
            <option value="cat" {{ $species === 'cat' ? 'selected' : '' }}>🐈 Cat</option>
            <option value="bird" {{ $species === 'bird' ? 'selected' : '' }}>🐦 Bird</option>
            <option value="other" {{ $species === 'other' ? 'selected' : '' }}>🐾 Other</option>
          </select>
        </div>
        <div class="col-md-2">
          <button type="submit" class="btn btn-primary w-100">
            <i class="bi bi-funnel me-1"></i> Filter
          </button>
        </div>
        <div class="col-md-2">
          <a href="{{ route('admin.pets.index') }}" class="btn btn-outline-secondary w-100">
            <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
          </a>
        </div>
      </form>
    </div>
  </div>

  <!-- Pets Table -->
  <div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 border-0">
      <h6 class="mb-0 fw-bold text-primary">
        <i class="bi bi-list-ul me-2"></i>Registered Pets
      </h6>
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead class="table-light" style="background: linear-gradient(135deg, #e3f2fd 0%, #e8f5e9 100%);">
            <tr>
              <th class="px-4 py-3 fw-semibold text-primary">
                <i class="bi bi-paw me-1"></i> Pet Name
              </th>
              <th class="py-3 fw-semibold text-primary">
                <i class="bi bi-tag me-1"></i> Species / Breed
              </th>
              <th class="py-3 fw-semibold text-primary">
                <i class="bi bi-calendar me-1"></i> Age
              </th>
              <th class="py-3 fw-semibold text-primary">
                <i class="bi bi-person me-1"></i> Owner Name
              </th>
              <th class="py-3 fw-semibold text-primary">
                <i class="bi bi-telephone me-1"></i> Contact
              </th>
              <th class="py-3 fw-semibold text-primary">
                <i class="bi bi-capsule me-1"></i> Vaccination
              </th>
              <th class="py-3 fw-semibold text-primary">
                <i class="bi bi-calendar-check me-1"></i> Registered
              </th>
              <th class="py-3 fw-semibold text-primary text-end pe-4">
                <i class="bi bi-gear me-1"></i> Actions
              </th>
            </tr>
          </thead>
          <tbody>
            @forelse($pets as $pet)
              <tr class="pet-row" style="transition: all 0.2s ease;">
                <td class="px-4 py-3">
                  <div class="d-flex align-items-center">
                    <div class="avatar-circle bg-{{ $pet->species === 'dog' ? 'primary' : ($pet->species === 'cat' ? 'info' : 'secondary') }} bg-opacity-10 text-{{ $pet->species === 'dog' ? 'primary' : ($pet->species === 'cat' ? 'info' : 'secondary') }} rounded-circle p-2 me-3">
                      <i class="bi bi-paw"></i>
                    </div>
                    <div>
                      <strong class="text-dark">{{ $pet->nickname }}</strong>
                      @if($pet->color)
                        <br><small class="text-muted">{{ $pet->color }}</small>
                      @endif
                    </div>
                  </div>
                </td>
                <td>
                  <span class="badge bg-{{ $pet->species === 'dog' ? 'primary' : ($pet->species === 'cat' ? 'info' : 'secondary') }}-subtle text-{{ $pet->species === 'dog' ? 'primary' : ($pet->species === 'cat' ? 'info' : 'secondary') }}">
                    {{ ucfirst($pet->species) }}
                  </span>
                  <small class="d-block text-muted mt-1">{{ $pet->breed ?? 'Mixed' }}</small>
                </td>
                <td>
                  <span class="text-dark fw-medium">{{ $pet->age ?? 'N/A' }}</span>
                  <small class="d-block text-muted">year(s)</small>
                </td>
                <td>
                  @if($pet->resident)
                    <span class="fw-medium">{{ $pet->resident->full_name }}</span>
                  @else
                    <span class="text-muted">-</span>
                  @endif
                </td>
                <td>
                  @if($pet->resident && $pet->resident->phone)
                    <a href="tel:{{ $pet->resident->phone }}" class="text-decoration-none">
                      <i class="bi bi-telephone text-success me-1"></i>{{ $pet->resident->phone }}
                    </a>
                  @else
                    <span class="text-muted">-</span>
                  @endif
                </td>
                <td>
                  @switch($pet->vaccination_status)
                    @case('up-to-date')
                      <span class="badge bg-success bg-opacity-10 text-success border border-success">
                        <i class="bi bi-check-circle-fill me-1"></i> Up to Date
                      </span>
                      @break
                    @case('overdue')
                      <span class="badge bg-danger bg-opacity-10 text-danger border border-danger">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i> Overdue
                      </span>
                      @break
                    @default
                      <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary">
                        <i class="bi bi-dash-circle me-1"></i> Not Vaccinated
                      </span>
                  @endswitch
                </td>
                <td>
                  <small class="text-muted">{{ $pet->created_at->format('M d, Y') }}</small>
                </td>
                <td class="text-end pe-4">
                  <div class="btn-group">
                    <a href="{{ route('admin.pets.show', $pet) }}" class="btn btn-sm btn-info rounded" title="View Details">
                      <i class="bi bi-eye"></i>
                    </a>
                    <a href="{{ route('admin.pets.edit', $pet) }}" class="btn btn-sm btn-warning rounded" title="Edit">
                      <i class="bi bi-pencil"></i>
                    </a>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="8" class="text-center py-5">
                  <div class="empty-state">
                    <div class="avatar-circle bg-light text-muted rounded-circle p-4 mb-3" style="width: 80px; height: 80px; margin: 0 auto;">
                      <i class="bi bi-paw fs-1"></i>
                    </div>
                    <h5 class="fw-normal text-muted">No pets found</h5>
                    <p class="text-muted small">Start by registering a new pet to the barangay.</p>
                    <a href="{{ route('admin.pets.create') }}" class="btn btn-primary">
                      <i class="bi bi-plus-lg me-1"></i> Register Pet
                    </a>
                  </div>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="mt-4">
    {{ $pets->links() }}
  </div>
</div>

<style>
  .pet-row:hover {
    background: linear-gradient(135deg, #e3f2fd 0%, #e8f5e9 100%) !important;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  }
  
  .avatar-circle {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
  }
  
  .btn {
    transition: all 0.2s ease;
  }
  
  .btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
  }
  
  .table thead th {
    border-bottom: 2px solid #e0e0e0;
  }
  
  .badge {
    font-weight: 500;
  }
  
  .input-group-text {
    border-radius: 8px 0 0 8px;
  }
  
  .form-control, .form-select {
    border-radius: 0 8px 8px 0;
  }
  
  .card {
    border-radius: 12px;
  }
  
  .page-surface {
    background: #f8fafc;
  }
</style>

@if(session('success'))
  <div class="alert alert-success d-flex align-items-center shadow-sm border-0 mb-4" id="successAlert">
    <i class="bi bi-check-circle-fill fs-4 me-2"></i>
    <div>{{ session('success') }}</div>
  </div>
  <script>
    setTimeout(() => document.getElementById('successAlert').style.display = 'none', 5000);
  </script>
@endif

@if(session('error'))
<div class="alert alert-danger d-flex align-items-center shadow-sm border-0 mb-4" id="errorAlert">
      <i class="bi bi-exclamation-circle-fill fs-4 me-2"></i>
      <div>{{ session('error') }}</div>
    </div>
    <script>
      setTimeout(() => document.getElementById('errorAlert')?.remove(), 5000);
    </script>
  @endif

@endsection