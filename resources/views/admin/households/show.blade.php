@extends('layouts.admin')

@section('title', 'Household Details - Barangay MIS')

@section('content')
<div class="page-surface">
  
  <!-- Header -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h4 class="mb-1 fw-bold text-primary">Household Details</h4>
      <p class="text-muted mb-0">View household information and members</p>
    </div>
    <div class="d-flex gap-2">
      <a href="{{ route('admin.households.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back
      </a>
      <a href="{{ route('admin.households.edit', $household) }}" class="btn btn-primary">
        <i class="bi bi-pencil me-1"></i> Edit Household
      </a>
      <form method="POST" action="{{ route('admin.households.destroy', $household) }}" onsubmit="return confirm('Are you sure you want to delete this household? This action cannot be undone.')">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger"><i class="bi bi-trash me-1"></i> Delete</button>
      </form>
    </div>
  </div>

  <div class="row g-4">
    <!-- Household Info Card -->
    <div class="col-md-4">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-header bg-white py-3">
          <h6 class="mb-0 fw-bold text-primary"><i class="bi bi-info-circle-fill me-2"></i>Household Information</h6>
        </div>
        <div class="card-body text-center py-4">
            <div class="avatar avatar-xl bg-primary-subtle text-primary rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                <i class="bi bi-house-door-fill fs-1"></i>
            </div>
            <h5 class="fw-bold mb-1">{{ $household->household_code }}</h5>
            <p class="text-muted small mb-4">Household Code</p>

            <div class="text-start border-top pt-3">
                <div class="mb-3">
                    <label class="small text-muted text-uppercase fw-bold d-block">Address</label>
                    <span class="fs-6 text-dark"><i class="bi bi-geo-alt me-2 text-secondary"></i>{{ $household->address_line }}</span>
                </div>
                <div>
                    <label class="small text-muted text-uppercase fw-bold d-block">Total Members</label>
                    <span class="fs-5 fw-bold text-primary">{{ $household->members->count() }}</span>
                </div>
            </div>
        </div>
      </div>
    </div>

    <!-- Members List Card -->
    <div class="col-md-8">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
          <h6 class="mb-0 fw-bold text-primary"><i class="bi bi-people-fill me-2"></i>Household Members</h6>
          <a href="{{ route('admin.households.edit', $household) }}" class="btn btn-sm btn-outline-primary">
            <i class="bi bi-pencil me-1"></i> Manage Members
          </a>
        </div>
        <div class="card-body p-0">
         @if($household->members->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-uppercase small text-muted">
                        <tr>
                            <th class="px-4 py-3 border-0">Name</th>
                            <th class="py-3 border-0">Email</th>
                            <th class="py-3 border-0">Birth Date</th>
                            <th class="py-3 border-0">Relationship</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($household->members as $member)
                            <tr>
                                <td class="px-4">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm bg-secondary-subtle text-secondary rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                            <i class="bi bi-person-fill"></i>
                                        </div>
                                        <div>
                                            <span class="fw-bold text-dark d-block">
                                                @php
                                                    // Use linked resident's full name if registered, 
                                                    // otherwise fallback to the names stored in the household_members table.
                                                    $residentName = $member->resident ? $member->resident->full_name : '';
                                                    $localName = trim($member->first_name . ' ' . $member->last_name);
                                                    
                                                    $displayName = $residentName ?: $localName;
                                                @endphp
                                                {{ $displayName ?: 'Unnamed Member' }}
                                            </span>
                                            @if($member->resident_id)
                                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill" style="font-size: 0.65rem;">
                                                    <i class="bi bi-check-circle-fill me-1"></i>Registered
                                                </span>
                                            @else
                                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill" style="font-size: 0.65rem;">
                                                    Unregistered
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="text-muted">{{ $member->email ?? '-' }}</td>
                                <td>
                                    @if($member->birth_date)
                                        <div>{{ \Carbon\Carbon::parse($member->birth_date)->format('M d, Y') }}</div>
                                        <small class="text-muted">{{ \Carbon\Carbon::parse($member->birth_date)->age }} yrs old</small>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border fw-normal text-uppercase">
                                        {{ $member->relation ?? $member->relationship ?? 'Member' }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <div class="mb-3">
                    <i class="bi bi-people fs-1 text-muted opacity-25"></i>
                </div>
                <h6 class="text-muted fw-normal">No household members assigned yet.</h6>
                <a href="{{ route('admin.households.edit', $household) }}" class="btn btn-sm btn-primary mt-2">
                    Add Members
                </a>
            </div>
        @endif
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
