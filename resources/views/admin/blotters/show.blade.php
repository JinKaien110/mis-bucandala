@extends('layouts.admin')

@section('title', 'Blotter Details - Barangay MIS')

@section('content')
<div class="page-surface">
  
  <!-- Header -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h4 class="mb-1 fw-bold text-primary">Blotter #{{ $blotter->blotter_no }}</h4>
      <p class="text-muted mb-0">
        <i class="bi bi-calendar-event me-1"></i> {{ $blotter->incident_date->format('M d, Y h:i A') }} 
        <span class="mx-2">•</span> 
        <span class="badge bg-light text-dark border">{{ $blotter->incident_type }}</span>
      </p>
    </div>

    <div class="d-flex gap-2">
      <a class="btn btn-outline-secondary" href="{{ route('admin.blotters.index') }}">
        <i class="bi bi-arrow-left me-1"></i> Back
      </a>

      @if(!$blotter->case)
        <button class="btn btn-warning text-dark" data-bs-toggle="modal" data-bs-target="#openCaseModal">
          <i class="bi bi-folder-plus me-1"></i> Open Case (Ongoing)
        </button>
      @else
        <a class="btn btn-warning text-dark" href="{{ route('admin.cases.show', $blotter->case) }}">
            <i class="bi bi-folder2-open me-1"></i> View Case
        </a>
      @endif
    </div>
  </div>

  @if(session('success'))
    <div class="alert alert-success d-flex align-items-center shadow-sm border-0 mb-4">
        <i class="bi bi-check-circle-fill fs-4 me-2"></i>
        <div>{{ session('success') }}</div>
    </div>
  @endif
  @if(session('error'))
    <div class="alert alert-danger d-flex align-items-center shadow-sm border-0 mb-4">
        <i class="bi bi-exclamation-triangle-fill fs-4 me-2"></i>
        <div>{{ session('error') }}</div>
    </div>
  @endif

  <div class="row g-4">
    <!-- Parties Involved -->
    <div class="col-md-12">
    <div class="row g-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold text-primary"><i class="bi bi-person-fill me-2"></i>Complainant</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar avatar-md bg-primary-subtle text-primary rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="bi bi-person-fill fs-4"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0">
                                {{-- Always use the blotter's saved name --}}
                                {{ strtoupper($blotter->complainant_name) }}
                            </h5>
                            <span class="badge bg-light text-muted border mt-1">
                                {{ $blotter->complainantResident ? 'Resident' : 'Non-Resident' }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="border-top pt-3">
                        <div class="mb-2">
                            <small class="text-muted text-uppercase fw-bold d-block">Contact</small>
                            <span>
                                {{-- Use the blotter's saved contact --}}
                                {{ $blotter->complainant_contact ?? 'N/A' }}
                            </span>
                        </div>
                        <div>
                            <small class="text-muted text-uppercase fw-bold d-block">Email</small>
                            <span>
                                {{-- Use the blotter's saved email --}}
                                {{ $blotter->complainant_email ?? 'N/A' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold text-danger"><i class="bi bi-person-x-fill me-2"></i>Respondent</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar avatar-md bg-danger-subtle text-danger rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="bi bi-person-x-fill fs-4"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0">
                                {{-- Always use the blotter's saved name --}}
                                {{ strtoupper($blotter->respondent_name) }}
                            </h5>
                            <span class="badge bg-light text-muted border mt-1">
                                {{ $blotter->respondentResident ? 'Resident' : 'Non-Resident' }}
                            </span>
                        </div>
                    </div>

                    <div class="border-top pt-3">
                        <div class="mb-2">
                            <small class="text-muted text-uppercase fw-bold d-block">Contact</small>
                            <span>
                                {{-- Use the blotter's saved contact --}}
                                {{ $blotter->respondent_contact ?? 'N/A' }}
                            </span>
                        </div>
                        <div>
                            <small class="text-muted text-uppercase fw-bold d-block">Email</small>
                            <span>
                                {{-- Use the blotter's saved email --}}
                                {{ $blotter->respondent_email ?? 'N/A' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

    <!-- Incident Details -->
    <div class="col-md-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold text-primary"><i class="bi bi-file-text-fill me-2"></i>Incident Details</h6>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <label class="small text-muted text-uppercase fw-bold d-block mb-1">Location</label>
                    <div class="d-flex align-items-center">
                        <i class="bi bi-geo-alt-fill text-danger me-2"></i>
                        <span class="fs-5">{{ $blotter->incident_location }}</span>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="small text-muted text-uppercase fw-bold d-block mb-2">Narrative</label>
                    <div class="p-3 bg-light rounded border">
                        {{ $blotter->narrative }}
                    </div>
                </div>

                @if($blotter->remarks)
                <div>
                    <label class="small text-muted text-uppercase fw-bold d-block mb-2">Remarks</label>
                    <div class="p-3 bg-light rounded border text-muted fst-italic">
                        {{ $blotter->remarks }}
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
  </div>
@endsection

@push('modals')
@if(!$blotter->case)
  <div class="modal fade" id="openCaseModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">
            <i class="bi bi-folder-plus me-2"></i>Open Case
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="text-center mb-3">
            <div class="mb-3">
              <i class="bi bi-exclamation-circle-fill text-warning" style="font-size: 3rem;"></i>
            </div>
            <p class="mb-0">Are you sure you want to open a case for this blotter?</p>
          </div>
          <div class="detail-card">
            <div class="detail-item">
              <span class="detail-label">Blotter No</span>
              <span class="detail-value">{{ $blotter->blotter_no }}</span>
            </div>
            <div class="detail-item">
              <span class="detail-label">Incident</span>
              <span class="detail-value">{{ $blotter->incident_type }}</span>
            </div>
            <div class="detail-item">
              <span class="detail-label">Date</span>
              <span class="detail-value">{{ $blotter->incident_date->format('M d, Y') }}</span>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <form method="POST" action="{{ route('admin.blotters.openCase', $blotter) }}">
            @csrf
            <button type="submit" class="btn btn-primary">
              <i class="bi bi-folder-plus me-1"></i> Open Case
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
@endif

<style>
#openCaseModal .modal-content {
  border: none;
  border-radius: 16px;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
  overflow: hidden;
}

#openCaseModal .modal-header {
  background: linear-gradient(135deg, #1055C9 0%, #0d47a1 100%);
  color: white;
  border: none;
  padding: 20px 24px;
}

#openCaseModal .modal-header .btn-close {
  filter: invert(1);
  opacity: 0.8;
}

#openCaseModal .modal-header .btn-close:hover {
  opacity: 1;
}

#openCaseModal .modal-title {
  font-weight: 600;
  display: flex;
  align-items: center;
}

#openCaseModal .modal-body {
  padding: 24px;
}

#openCaseModal .detail-card {
  background: #f8fafc;
  border-radius: 12px;
  padding: 16px;
}

#openCaseModal .detail-item {
  display: flex;
  justify-content: space-between;
  padding: 8px 0;
  border-bottom: 1px solid #e2e8f0;
}

#openCaseModal .detail-item:last-child {
  border-bottom: none;
}

#openCaseModal .detail-label {
  color: #64748b;
  font-size: 0.875rem;
}

#openCaseModal .detail-value {
  font-weight: 600;
  color: #1e293b;
}

#openCaseModal .modal-footer {
  border-top: 1px solid #e9ecef;
  padding: 16px 24px;
}

#openCaseModal .btn {
  border-radius: 12px;
  padding: 10px 20px;
  font-weight: 600;
}

#openCaseModal .btn-secondary {
  background: #e2e8f0;
  border: none;
  color: #475569;
}

#openCaseModal .btn-secondary:hover {
  background: #cbd5e1;
}

#openCaseModal .btn-primary {
  background: linear-gradient(135deg, #1055C9 0%, #0d47a1 100%);
  border: none;
  box-shadow: 0 4px 15px rgba(16, 85, 201, 0.3);
}

#openCaseModal .btn-primary:hover {
  background: linear-gradient(135deg, #0d47a1 0%, #0a3d8f 100%);
  box-shadow: 0 6px 20px rgba(16, 85, 201, 0.4);
}
</style>
@endpush
