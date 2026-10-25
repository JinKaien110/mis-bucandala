@extends('layouts.resident', ['currentRoute' => 'resident.document-requests'])

@section('content')
<div class="container document-requests-page">
  <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <div>
      <h2 class="mb-1">Document Requests</h2>
      <p class="opacity-75 mb-0">Request a Certificate of Residency or Barangay Clearance and track its status.</p>
    </div>
    <a href="{{ route('resident.dashboard') }}" class="btn btn-glass"><i class="bi bi-arrow-left me-2"></i>Dashboard</a>
  </div>

  @if(session('success'))
    <div class="document-request-alert mb-4" role="status">
      <i class="bi bi-check-circle-fill me-2" aria-hidden="true"></i>{{ session('success') }}
    </div>
  @endif
  @if($errors->any())
    <div class="alert alert-danger" role="alert">
      <div class="fw-bold mb-1">Please check the information below.</div>
      <ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
  @endif

  <section class="glass p-4 mb-4 document-request-section">
    <h4 class="mb-3"><i class="bi bi-file-earmark-plus me-2"></i>Request a Document</h4>
    @if($documentTypes->isEmpty())
      <div class="alert alert-warning mb-0">Online requests for these documents are not currently available. Please contact the Barangay Hall.</div>
    @else
      <form method="POST" action="{{ route('resident.document-requests.store') }}" id="documentRequestForm">
        @csrf
        <div class="row g-3 align-items-end">
          <div class="col-lg-4">
            <label for="document_type_id" class="form-label">Document Type <span class="text-warning">*</span></label>
            <select id="document_type_id" name="document_type_id" class="form-select" required>
              <option value="" disabled @selected(!old('document_type_id'))>-- Select a document --</option>
              @foreach($documentTypes as $documentType)
                <option value="{{ $documentType->id }}" data-fee="{{ $documentType->fee ?? 0 }}" @selected((string) old('document_type_id') === (string) $documentType->id)>{{ $documentType->name }}</option>
              @endforeach
            </select>
            @error('document_type_id')<div class="small text-warning mt-1">{{ $message }}</div>@enderror
          </div>
          <div class="col-lg-3">
            <label for="purpose_type" class="form-label">Purpose of Request <span class="text-warning">*</span></label>
            <select id="purpose_type" name="purpose_type" class="form-select" required>
              <option value="" disabled @selected(!old('purpose_type'))>-- Select a purpose --</option>
              @foreach(['Employment', 'School or Educational Requirement', 'Scholarship Application', 'Financial Assistance', 'Medical Assistance', 'Business Requirement', 'Government Transaction', 'Personal Requirement', 'Others'] as $purpose)
                <option value="{{ $purpose }}" @selected(old('purpose_type') === $purpose)>{{ $purpose }}</option>
              @endforeach
            </select>
            @error('purpose_type')<div class="small text-warning mt-1">{{ $message }}</div>@enderror
          </div>
          <div class="col-lg-3" id="otherPurposeWrap" style="{{ old('purpose_type') === 'Others' ? '' : 'display:none;' }}">
            <label for="other_purpose" class="form-label">Other Purpose <span class="text-warning">*</span></label>
            <input id="other_purpose" name="other_purpose" class="form-control" value="{{ old('other_purpose') }}" maxlength="255" @required(old('purpose_type') === 'Others')>
            @error('other_purpose')<div class="small text-warning mt-1">{{ $message }}</div>@enderror
          </div>
          <div class="col-lg-2">
            <label for="documentFee" class="form-label">Fee</label>
            <div class="input-group">
              <span class="input-group-text">₱</span>
              <input id="documentFee" class="form-control" value="Select a document" readonly aria-live="polite">
            </div>
          </div>
          <div class="col-12 d-flex justify-content-end">
            <button id="submitDocumentRequestButton" type="submit" class="btn btn-warning fw-semibold">
              <i class="bi bi-send me-2"></i><span>Submit Request</span>
            </button>
          </div>
        </div>
      </form>
    @endif
  </section>

  <section class="glass p-4 document-request-section">
    <h4 class="mb-3"><i class="bi bi-clock-history me-2"></i>My Request History</h4>
    @if($requests->isEmpty())
      <div class="text-center py-5">
        <i class="bi bi-inbox fs-1 opacity-75"></i>
        <h5 class="mt-3">No document requests yet</h5>
        <p class="mb-0 opacity-75">Your submitted requests and their status will appear here.</p>
      </div>
    @else
      <div class="document-request-history">
        @foreach($requests as $request)
          @php
            $status = $request->statusEnum()->value;
            $statusClass = match($status) {
              'completed' => 'bg-success',
              'pickup' => 'bg-info',
              'cancelled', 'rejected' => 'bg-secondary',
              default => 'bg-warning text-dark',
            };
          @endphp
          <article class="document-request-item">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
              <div>
                <div class="small text-uppercase history-label">Document Request</div>
                <h5 class="mb-1">{{ $request->documentType?->name ?? 'Document Request' }}</h5>
                <div class="small history-label">{{ $request->control_no }}</div>
              </div>
              <p class="opacity-75 mb-0">
                @if($status === 'pickup')
                  Your document is ready for pickup at the barangay hall. Please bring your ID.
                @elseif($status === 'pending')
                  Barangay staff are processing your request.
                @else
                  Please contact the barangay hall if you need assistance with this request.
                @endif
              </p>
              <span class="badge {{ $statusClass }} px-3 py-2">{{ $request->statusEnum()->label() }}</span>
            </div>
            <hr class="border-light opacity-25">
            <div class="row g-3">
              <div class="col-sm-6 col-lg-3"><strong>Purpose</strong><div>{{ $request->purpose ?: '—' }}</div></div>
              <div class="col-sm-6 col-lg-3"><strong>Fee</strong><div>{{ (float) $request->fee_amount > 0 ? '₱' . number_format((float) $request->fee_amount, 2) : 'Free' }}</div></div>
              <div class="col-sm-6 col-lg-3"><strong>Requested</strong><div>{{ $request->created_at->format('M d, Y') }}</div></div>
              <div class="col-sm-6 col-lg-3"><strong>Instructions / Remarks</strong><div>{{ $request->remarks ?: '—' }}</div></div>
            </div>
          </article>
        @endforeach
      </div>
    @endif
  </section>
</div>
@endsection

@push('styles')
<style>
  .document-request-alert {
    padding: 1rem 1.25rem;
    color: #ffffff;
    background: rgba(13, 71, 161, 0.92);
    border: 1px solid rgba(134, 239, 172, 0.55);
    border-left: 5px solid #86efac;
    border-radius: 0.75rem;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
  }

  .document-request-section {
    background: rgba(13, 71, 161, 0.82);
    border-color: rgba(255, 215, 0, 0.4);
  }

  .document-request-item {
    padding: 1.25rem;
    color: #ffffff;
    background: rgba(7, 37, 87, 0.72);
    border: 1px solid rgba(255, 215, 0, 0.32);
    border-radius: 14px;
  }

  .document-request-item + .document-request-item {
    margin-top: 1rem;
  }

  .document-request-item h5 {
    color: #ffffff;
  }

  .document-request-item strong {
    display: inline-block;
    margin-bottom: 0.25rem;
  }

  .history-label {
    color: rgba(255, 255, 255, 0.76);
  }

  .document-requests-page .form-control,
  .document-requests-page .form-select,
  .document-requests-page .input-group-text {
    min-height: 48px;
    color: #ffffff !important;
    background-color: #123b78 !important;
    border: 1px solid rgba(255, 215, 0, 0.58) !important;
  }

  .document-requests-page .form-select option {
    color: #ffffff;
    background-color: #123b78;
  }

  .document-requests-page .form-control:focus,
  .document-requests-page .form-select:focus {
    color: #ffffff !important;
    background-color: #0c2f65 !important;
    border-color: #ffd700 !important;
    box-shadow: 0 0 0 0.2rem rgba(255, 215, 0, 0.2) !important;
  }

  .document-requests-page .input-group-text {
    display: flex;
    align-items: center;
    color: #ffd700 !important;
  }

  .document-requests-page .form-label {
    color: #ffffff;
    font-weight: 600;
  }

  @media (max-width: 767.98px) {
    .document-requests-page .document-request-section {
      padding: 1rem !important;
    }

    .document-request-item {
      padding: 1rem;
    }
  }
</style>
@endpush

@push('scripts')
<script>
  const documentTypeSelect = document.getElementById('document_type_id');
  const documentFee = document.getElementById('documentFee');
  if (documentTypeSelect && documentFee) {
    const updateDocumentFee = () => {
      const fee = Number(documentTypeSelect.selectedOptions[0]?.dataset.fee);
      documentFee.value = documentTypeSelect.value
        ? (fee > 0 ? fee.toFixed(2) : 'Free')
        : 'Select a document';
    };
    documentTypeSelect.addEventListener('change', updateDocumentFee);
    updateDocumentFee();
  }

  const purposeSelect = document.getElementById('purpose_type');
  const otherPurposeWrap = document.getElementById('otherPurposeWrap');
  const otherPurpose = document.getElementById('other_purpose');
  if (purposeSelect && otherPurposeWrap && otherPurpose) {
    const updateOtherPurpose = () => {
      const isOther = purposeSelect.value === 'Others';
      otherPurposeWrap.style.display = isOther ? '' : 'none';
      otherPurpose.required = isOther;
      if (!isOther) otherPurpose.value = '';
    };
    purposeSelect.addEventListener('change', updateOtherPurpose);
    updateOtherPurpose();
  }

  const documentRequestForm = document.getElementById('documentRequestForm');
  if (documentRequestForm) {
    documentRequestForm.addEventListener('submit', () => {
      const button = document.getElementById('submitDocumentRequestButton');
      button.disabled = true;
      button.querySelector('span').textContent = 'Submitting...';
      button.insertAdjacentHTML('afterbegin', '<span class="spinner-border spinner-border-sm me-2" role="status" aria-label="Submitting"></span>');
    });
  }
</script>
@endpush
