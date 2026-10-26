@extends('layouts.resident', ['currentRoute' => 'resident.document-requests'])

@section('content')
<div class="container document-requests-page">
  <div class="document-requests-header d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <div class="d-flex align-items-center gap-3">
      <div class="document-requests-header-icon" aria-hidden="true"><i class="bi bi-file-earmark-text"></i></div>
      <div>
        <div class="small text-uppercase document-requests-eyebrow">Resident Services</div>
        <h2 class="mb-1">Document Requests</h2>
        <p class="opacity-75 mb-0">Request an official document and follow its progress here.</p>
      </div>
    </div>
    <a href="{{ route('resident.dashboard') }}" class="btn btn-glass document-requests-back"><i class="bi bi-arrow-left me-2"></i>Dashboard</a>
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
    <div class="document-section-heading mb-4">
      <div class="document-section-icon" aria-hidden="true"><i class="bi bi-file-earmark-plus"></i></div>
      <div>
        <div class="small text-uppercase document-form-eyebrow">New request</div>
        <h4 class="mb-1">Request a Document</h4>
        <p class="small opacity-75 mb-0">Tell us what you need. We’ll show the applicable fee before you submit.</p>
      </div>
    </div>
    @if($documentTypes->isEmpty())
      <div class="alert alert-warning mb-0">Online requests for these documents are not currently available. Please contact the Barangay Hall.</div>
    @else
      <form method="POST" action="{{ route('resident.document-requests.store') }}" id="documentRequestForm" class="document-request-form">
        @csrf
        <div class="document-request-form-panel">
          <div class="row g-4">
            <div class="col-lg-6">
              <label for="document_type_id" class="form-label"><span class="document-field-number">1</span>Document Type <span class="text-warning">*</span></label>
              <select id="document_type_id" name="document_type_id" class="form-select" required>
                <option value="" disabled @selected(!old('document_type_id'))>-- Select a document --</option>
                @foreach($documentTypes as $documentType)
                  <option value="{{ $documentType->id }}" data-fee="{{ $documentType->fee ?? 0 }}" @selected((string) old('document_type_id') === (string) $documentType->id)>{{ $documentType->name }}</option>
                @endforeach
              </select>
              @error('document_type_id')<div class="small text-warning mt-1">{{ $message }}</div>@enderror
            </div>
            <div class="col-lg-6">
              <label for="purpose_type" class="form-label"><span class="document-field-number">2</span>Purpose of Request <span class="text-warning">*</span></label>
              <select id="purpose_type" name="purpose_type" class="form-select" required>
                <option value="" disabled @selected(!old('purpose_type'))>-- Select a purpose --</option>
                @foreach(['Employment', 'School or Educational Requirement', 'Scholarship Application', 'Financial Assistance', 'Medical Assistance', 'Business Requirement', 'Government Transaction', 'Personal Requirement', 'Others'] as $purpose)
                  <option value="{{ $purpose }}" @selected(old('purpose_type') === $purpose)>{{ $purpose }}</option>
                @endforeach
              </select>
              @error('purpose_type')<div class="small text-warning mt-1">{{ $message }}</div>@enderror
            </div>
            <div class="col-lg-6" id="otherPurposeWrap" style="{{ old('purpose_type') === 'Others' ? '' : 'display:none;' }}">
              <label for="other_purpose" class="form-label">Other Purpose <span class="text-warning">*</span></label>
              <input id="other_purpose" name="other_purpose" class="form-control" value="{{ old('other_purpose') }}" maxlength="255" @required(old('purpose_type') === 'Others')>
              @error('other_purpose')<div class="small text-warning mt-1">{{ $message }}</div>@enderror
            </div>
            <div class="col-lg-6">
              <div class="document-fee-card">
                <div class="document-fee-icon" aria-hidden="true"><i class="bi bi-receipt"></i></div>
                <div class="document-fee-copy">
                  <span class="document-fee-caption">Applicable fee</span>
                  <div class="input-group document-fee-field">
                    <span class="input-group-text" aria-hidden="true">₱</span>
                    <input id="documentFee" class="form-control" value="Select a document" readonly aria-live="polite" aria-label="Document fee">
                  </div>
                </div>
              </div>
            </div>
            <div class="col-12">
              <div class="document-request-form-footer">
                <div class="document-request-form-hint">
                  <i class="bi bi-shield-check" aria-hidden="true"></i>
                  <span>Your request will be submitted to Barangay staff for processing.</span>
                </div>
                <button id="submitDocumentRequestButton" type="submit" class="btn btn-warning fw-semibold document-request-submit">
                  <i class="bi bi-send document-request-submit-icon" aria-hidden="true"></i>
                  <span>Submit Request</span>
                  <i class="bi bi-arrow-right document-request-submit-arrow" aria-hidden="true"></i>
                </button>
              </div>
            </div>
          </div>
        </div>
      </form>
    @endif
  </section>

  <section class="glass p-4 document-request-section">
    <div class="document-section-heading mb-4">
      <div class="document-section-icon" aria-hidden="true"><i class="bi bi-clock-history"></i></div>
      <div>
        <h4 class="mb-1">My Request History</h4>
        <p class="small opacity-75 mb-0">Review the status, fee, and instructions for your submitted requests.</p>
      </div>
    </div>
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
            <div class="document-request-item-heading">
              <div class="document-request-title">
                <div class="small text-uppercase history-label">Document Request</div>
                <h5 class="mb-1">{{ $request->documentType?->name ?? 'Document Request' }}</h5>
                <div class="small history-label"><i class="bi bi-hash me-1" aria-hidden="true"></i>{{ $request->control_no }}</div>
              </div>
              <span class="badge {{ $statusClass }} document-request-status">{{ $request->statusEnum()->label() }}</span>
            </div>
            <div class="document-request-status-note">
              <i class="bi {{ $status === 'pickup' ? 'bi-info-circle-fill' : ($status === 'pending' ? 'bi-hourglass-split' : 'bi-chat-left-text') }}" aria-hidden="true"></i>
              <p class="mb-0">
                @if($status === 'pickup')
                  Your document is ready for pickup at the barangay hall. Please bring your ID.
                @elseif($status === 'pending')
                  Barangay staff are processing your request.
                @else
                  Please contact the barangay hall if you need assistance with this request.
                @endif
              </p>
            </div>
            <div class="row g-2 document-request-details">
              <div class="col-sm-6 col-lg-3"><div class="document-request-detail"><span>Purpose</span><strong>{{ $request->purpose ?: '—' }}</strong></div></div>
              <div class="col-sm-6 col-lg-3"><div class="document-request-detail"><span>Fee</span><strong>{{ (float) $request->fee_amount > 0 ? '₱' . number_format((float) $request->fee_amount, 2) : 'Free' }}</strong></div></div>
              <div class="col-sm-6 col-lg-3"><div class="document-request-detail"><span>Requested</span><strong><i class="bi bi-calendar3 me-1" aria-hidden="true"></i>{{ $request->created_at->format('M d, Y') }}</strong></div></div>
              <div class="col-sm-6 col-lg-3"><div class="document-request-detail"><span>Instructions / Remarks</span><strong>{{ $request->remarks ?: '—' }}</strong></div></div>
            </div>
            @if(in_array($status, ['pending', 'pickup'], true))
              <div class="document-request-actions mt-3">
                @if($status === 'pending')
                  <form method="POST" action="{{ route('resident.document-requests.cancel', $request) }}" onsubmit="return confirm('Cancel this document request?')">
                    @csrf
                    <button type="submit" class="btn document-request-action document-request-action-cancel fw-semibold">
                      <i class="bi bi-x-circle me-2"></i>Cancel Request
                    </button>
                  </form>
                @else
                  <form method="POST" action="{{ route('resident.document-requests.received', $request) }}">
                    @csrf
                    <button type="submit" class="btn document-request-action document-request-action-received fw-semibold">
                      <i class="bi bi-check2-circle me-2"></i>Confirm Received
                    </button>
                  </form>
                @endif
              </div>
            @endif
          </article>
        @endforeach
      </div>
    @endif
  </section>
</div>
@endsection

@push('styles')
<style>
  .document-requests-header {
    padding: 1.25rem 1.5rem;
    background: linear-gradient(115deg, rgba(13, 71, 161, 0.86), rgba(7, 37, 87, 0.72));
    border: 1px solid rgba(255, 215, 0, 0.3);
    border-radius: 18px;
    box-shadow: 0 14px 34px rgba(0, 0, 0, 0.16);
  }

  .document-requests-header-icon,
  .document-section-icon {
    display: inline-flex;
    flex: 0 0 auto;
    align-items: center;
    justify-content: center;
    color: #ffd700;
    background: rgba(255, 215, 0, 0.12);
    border: 1px solid rgba(255, 215, 0, 0.3);
  }

  .document-requests-header-icon {
    width: 3.25rem;
    height: 3.25rem;
    border-radius: 16px;
    font-size: 1.5rem;
  }

  .document-requests-eyebrow {
    color: #ffe680;
    font-weight: 700;
    letter-spacing: 0.12em;
  }

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
    border-radius: 18px;
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.14);
  }

  .document-section-heading {
    display: flex;
    align-items: center;
    gap: 0.9rem;
  }

  .document-section-icon {
    width: 2.75rem;
    height: 2.75rem;
    border-radius: 13px;
    font-size: 1.2rem;
  }

  .document-form-eyebrow {
    margin-bottom: 0.15rem;
    color: #ffe680;
    font-size: 0.7rem;
    font-weight: 800;
    letter-spacing: 0.13em;
  }

  .document-request-form-panel {
    padding: 1.35rem;
    background: linear-gradient(145deg, rgba(5, 30, 72, 0.62), rgba(8, 43, 91, 0.52));
    border: 1px solid rgba(255, 255, 255, 0.12);
    border-radius: 16px;
  }

  .document-field-number {
    display: inline-flex;
    width: 1.45rem;
    height: 1.45rem;
    align-items: center;
    justify-content: center;
    margin-right: 0.5rem;
    color: #ffe680;
    background: rgba(255, 215, 0, 0.12);
    border: 1px solid rgba(255, 215, 0, 0.35);
    border-radius: 50%;
    font-size: 0.72rem;
    vertical-align: 1px;
  }

  .document-fee-card {
    display: flex;
    min-height: 80px;
    align-items: center;
    gap: 0.85rem;
    padding: 0.8rem 1rem;
    background: rgba(255, 215, 0, 0.075);
    border: 1px solid rgba(255, 215, 0, 0.24);
    border-radius: 12px;
  }

  .document-fee-icon {
    display: flex;
    width: 2.6rem;
    height: 2.6rem;
    flex: 0 0 auto;
    align-items: center;
    justify-content: center;
    color: #ffe680;
    background: rgba(255, 215, 0, 0.13);
    border-radius: 10px;
    font-size: 1.15rem;
  }

  .document-fee-copy {
    flex: 1;
    min-width: 0;
  }

  .document-fee-caption {
    display: block;
    margin-bottom: 0.25rem;
    color: rgba(255, 255, 255, 0.72);
    font-size: 0.75rem;
    font-weight: 700;
    letter-spacing: 0.06em;
    text-transform: uppercase;
  }

  .document-request-form-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    padding-top: 1.1rem;
    border-top: 1px solid rgba(255, 255, 255, 0.12);
  }

  .document-request-form-hint {
    display: flex;
    align-items: center;
    gap: 0.55rem;
    color: rgba(255, 255, 255, 0.74);
    font-size: 0.88rem;
  }

  .document-request-form-hint i {
    color: #ffe680;
    font-size: 1.05rem;
  }

  .document-request-item {
    padding: 1.35rem;
    color: #ffffff;
    background: linear-gradient(145deg, rgba(7, 37, 87, 0.88), rgba(10, 48, 102, 0.76));
    border: 1px solid rgba(255, 255, 255, 0.13);
    border-left: 4px solid rgba(255, 215, 0, 0.72);
    border-radius: 16px;
    transition: transform 160ms ease, box-shadow 160ms ease, border-color 160ms ease;
  }

  .document-request-item:hover {
    transform: translateY(-2px);
    border-color: rgba(255, 215, 0, 0.42);
    box-shadow: 0 12px 26px rgba(0, 0, 0, 0.2);
  }

  .document-request-item + .document-request-item {
    margin-top: 1rem;
  }

  .document-request-item h5 {
    color: #ffffff;
    font-weight: 700;
  }

  .document-request-item-heading {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 1rem;
  }

  .document-request-title {
    min-width: 0;
  }

  .document-request-status {
    flex: 0 0 auto;
    padding: 0.55rem 0.8rem;
    border-radius: 999px;
    text-transform: capitalize;
  }

  .document-request-status-note {
    display: flex;
    align-items: flex-start;
    gap: 0.65rem;
    margin: 1rem 0;
    padding: 0.8rem 0.95rem;
    color: rgba(255, 255, 255, 0.9);
    background: rgba(255, 255, 255, 0.07);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 11px;
  }

  .document-request-status-note i {
    margin-top: 0.15rem;
    color: #ffe680;
  }

  .document-request-detail {
    height: 100%;
    min-height: 76px;
    padding: 0.8rem 0.9rem;
    background: rgba(255, 255, 255, 0.055);
    border: 1px solid rgba(255, 255, 255, 0.09);
    border-radius: 11px;
    overflow-wrap: anywhere;
  }

  .document-request-detail span,
  .document-request-detail strong {
    display: block;
  }

  .document-request-detail span {
    margin-bottom: 0.35rem;
    color: rgba(255, 255, 255, 0.67);
    font-size: 0.78rem;
    font-weight: 700;
    letter-spacing: 0.04em;
    text-transform: uppercase;
  }

  .document-request-detail strong {
    color: #ffffff;
    font-weight: 600;
    line-height: 1.45;
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

  .document-requests-page .form-control,
  .document-requests-page .form-select {
    border-radius: 10px;
  }

  .document-requests-page .input-group-text {
    border-radius: 10px 0 0 10px;
  }

  .document-fee-field .form-control {
    border-radius: 0 10px 10px 0;
    font-weight: 700;
  }

  .document-request-submit {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 220px;
    min-height: 54px;
    padding: 0.7rem 1.2rem;
    color: #102a50;
    background: linear-gradient(135deg, #ffe98a 0%, #ffd43b 52%, #ffc107 100%);
    border: 1px solid rgba(255, 239, 170, 0.85);
    border-radius: 13px;
    box-shadow: 0 8px 20px rgba(255, 193, 7, 0.26), inset 0 1px 0 rgba(255, 255, 255, 0.45);
    font-size: 1rem;
    letter-spacing: 0.01em;
    gap: 0.7rem;
    transition: transform 150ms ease, box-shadow 150ms ease, background-color 150ms ease, border-color 150ms ease;
  }

  .document-request-submit:hover:not(:disabled) {
    color: #102a50;
    background: linear-gradient(135deg, #fff0ad 0%, #ffdf66 52%, #ffd044 100%);
    border-color: #fff0ad;
    box-shadow: 0 11px 26px rgba(255, 193, 7, 0.34), inset 0 1px 0 rgba(255, 255, 255, 0.6);
  }

  .document-request-submit:active:not(:disabled) {
    transform: translateY(1px);
    box-shadow: 0 4px 12px rgba(255, 193, 7, 0.22);
  }

  .document-request-submit-icon {
    display: inline-flex;
    width: 1.9rem;
    height: 1.9rem;
    align-items: center;
    justify-content: center;
    color: #ffe680;
    background: #123b78;
    border-radius: 9px;
    font-size: 0.95rem;
  }

  .document-request-submit-arrow {
    margin-left: 0.15rem;
    transition: transform 150ms ease;
  }

  .document-request-submit:hover:not(:disabled) .document-request-submit-arrow {
    transform: translateX(3px);
  }

  .document-request-submit:disabled {
    cursor: wait;
    color: #314665;
    background: linear-gradient(135deg, #e6d18a, #d8b84e);
    border-color: rgba(255, 239, 170, 0.55);
    box-shadow: none;
  }

  .document-request-submit.is-submitting .document-request-submit-arrow {
    display: none;
  }

  .document-requests-page .btn-glass,
  .document-request-action {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.15rem;
    min-height: 46px;
    padding: 0.7rem 1.15rem;
    border-radius: 11px;
    font-weight: 700;
    text-decoration: none;
    transition: transform 150ms ease, box-shadow 150ms ease, background-color 150ms ease, border-color 150ms ease;
  }

  .document-requests-page .btn-glass:hover,
  .document-requests-page .document-request-submit:hover:not(:disabled),
  .document-request-action:hover {
    transform: translateY(-1px);
  }

  .document-requests-page .btn-glass:focus-visible,
  .document-requests-page .document-request-submit:focus-visible,
  .document-request-action:focus-visible {
    outline: 3px solid rgba(255, 230, 128, 0.9);
    outline-offset: 3px;
    box-shadow: 0 0 0 5px rgba(7, 37, 87, 0.85);
  }

  .document-request-action {
    min-width: 190px;
    border: 1px solid transparent;
  }

  .document-request-action-received {
    color: #172b4d;
    background: linear-gradient(135deg, #ffe680, #ffc928);
    border-color: rgba(255, 230, 128, 0.9);
    box-shadow: 0 7px 18px rgba(255, 193, 7, 0.22);
  }

  .document-request-action-received:hover {
    color: #172b4d;
    background: linear-gradient(135deg, #ffedaa, #ffd34f);
    box-shadow: 0 10px 22px rgba(255, 193, 7, 0.3);
  }

  .document-request-action-cancel {
    color: #ffe4e6;
    background: rgba(190, 24, 46, 0.16);
    border-color: rgba(252, 165, 165, 0.52);
  }

  .document-request-action-cancel:hover {
    color: #ffffff;
    background: rgba(190, 24, 46, 0.34);
    border-color: rgba(252, 165, 165, 0.82);
    box-shadow: 0 8px 18px rgba(127, 29, 29, 0.22);
  }

  @media (max-width: 767.98px) {
    .document-requests-header {
      padding: 1rem;
    }

    .document-requests-header-icon {
      width: 2.8rem;
      height: 2.8rem;
    }

    .document-requests-page .document-request-section {
      padding: 1rem !important;
    }

    .document-request-form-panel {
      padding: 1rem;
    }

    .document-request-form-footer {
      align-items: stretch;
      flex-direction: column;
    }

    .document-request-item {
      padding: 1rem;
    }

    .document-request-item-heading {
      flex-wrap: wrap;
    }

    .document-request-status {
      order: -1;
    }

    .document-requests-back {
      width: 100%;
    }

    .document-requests-page .document-request-submit {
      width: 100%;
    }

    .document-request-actions,
    .document-request-actions form,
    .document-request-action {
      width: 100%;
    }
  }

  @media (prefers-reduced-motion: reduce) {
    .document-request-item,
    .document-requests-page .btn-glass,
    .document-requests-page .document-request-submit,
    .document-request-action {
      transition: none;
    }

    .document-request-item:hover,
    .document-requests-page .btn-glass:hover,
    .document-requests-page .document-request-submit:hover:not(:disabled),
    .document-request-action:hover {
      transform: none;
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
      button.classList.add('is-submitting');
      button.setAttribute('aria-busy', 'true');
      button.querySelector('span').textContent = 'Submitting...';
      button.insertAdjacentHTML('afterbegin', '<span class="spinner-border spinner-border-sm me-2" role="status" aria-label="Submitting"></span>');
    });
  }
</script>
@endpush
