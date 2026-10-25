@extends('layouts.admin')

@section('title', 'Document Requests - Barangay MIS')

@section('content')
<div class="page-surface">
  
  <!-- Header -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h4 class="mb-1 fw-bold text-primary">Document Requests</h4>
      <p class="text-muted mb-0">Manage resident document requests and fees</p>
    </div>
    <button class="btn btn-primary" id="btnOpenCreate" type="button">
        <i class="bi bi-plus-lg me-1"></i> New Request
    </button>
  </div>

  @if(session('success'))
    <div class="alert alert-success d-flex align-items-center shadow-sm border-0 mb-4">
        <i class="bi bi-check-circle-fill fs-4 me-2"></i>
        <div>{{ session('success') }}</div>
    </div>
  @endif
  @if(session('error'))
    <div class="alert alert-danger d-flex align-items-center shadow-sm border-0 mb-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill fs-4 me-2"></i>
        <div>{{ session('error') }}</div>
    </div>
  @endif

  @if($errors->any())
    <div class="alert alert-danger shadow-sm border-0 mb-4">
      <div class="fw-bold mb-2"><i class="bi bi-exclamation-triangle-fill me-2"></i>Validation Error</div>
      <ul class="mb-0 ps-3">
        @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
      </ul>
    </div>
  @endif

  <!-- Search -->
  <div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <div class="row g-3 align-items-center">
            <div class="col-md-8">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input id="search" type="text" class="form-control border-start-0 ps-0" placeholder="Search control no, resident, or document...">
                </div>
            </div>
            <div class="col-md-4 text-md-end text-muted small">
                <span>Press <strong>Enter</strong> to search</span>
            </div>
        </div>
    </div>
  </div>

  <div id="message" class="alert" style="display:none;"></div>

  <!-- Table -->
  <div class="card border-0 shadow-sm">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="bg-light text-uppercase small text-muted">
            <tr>
              <th class="px-4 py-3 border-0">Control No</th>
              <th class="py-3 border-0">Resident</th>
              <th class="py-3 border-0">Document</th>
              <th class="py-3 border-0">Fee</th>
              <th class="py-3 border-0">Request At</th>
              <th class="py-3 border-0">Status</th>
              <th class="py-3 border-0 text-end pe-4">Action</th>
            </tr>
          </thead>
          <tbody id="tbody">
            <tr><td colspan="7" class="text-center py-5 text-muted">Loading...</td></tr>
          </tbody>
        </table>
      </div>
    </div>
    <div class="card-footer bg-white border-top-0 py-3">
      <div class="d-flex justify-content-end">
        <nav aria-label="Document requests pagination">
          <ul id="document-requests-pagination" class="pagination pagination-modern mb-0"></ul>
        </nav>
      </div>
    </div>
  </div>
</div>

<!-- Create Modal -->
<div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header border-bottom-0 pb-0">
        <h5 class="modal-title fw-bold text-primary">New Document Request</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="createForm" method="POST" action="{{ route('admin.document-requests.store') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label small text-muted fw-bold">Resident <span class="text-danger">*</span></label>
                <select name="resident_id" id="c_resident" class="form-select" data-placeholder="Search resident by first or last name" required></select>
            </div>
            <div class="mb-3">
                <label class="form-label small text-muted fw-bold">Document Type <span class="text-danger">*</span></label>
                <select name="document_type_id" id="c_doc_type" class="form-select" required></select>
            </div>
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label small text-muted fw-bold">Fee (Auto)</label>
                    <input id="c_fee" class="form-control bg-light" disabled value="0.00">
                </div>
                <div class="col-md-6">
                    <label class="form-label small text-muted fw-bold">Control No (Auto)</label>
                    <input class="form-control bg-light" disabled value="Generated after save">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label small text-muted fw-bold">Purpose</label>
                <textarea class="form-control" name="purpose" placeholder="Optional" rows="2"></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label small text-muted fw-bold">Remarks</label>
                <textarea class="form-control" name="remarks" placeholder="Optional" rows="2"></textarea>
            </div>
            <div class="text-end mt-4">
                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary px-4">Create Request</button>
            </div>
        </form>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
  const tbody = document.getElementById('tbody');
  const msgBox = document.getElementById('message');
  const pagination = document.getElementById('document-requests-pagination');

  // Initialize Bootstrap Modal
  const createModal = new bootstrap.Modal(document.getElementById('createModal'));

  // Handle create form submission
  document.getElementById('createForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const form = this;
    const formData = new FormData(form);
    
    try {
      const res = await fetch(form.action, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Accept': 'application/json'
        },
        body: formData
      });
      
      const data = await res.json();
      
       if (data.success || res.ok) {
         showMsg({ success: true, message: data.message || 'Document request created successfully!' });
         // Clear focus to avoid aria-hidden warning
         if (document.activeElement) document.activeElement.blur();
         createModal.hide();
         form.reset();
         $('#c_resident').val(null).trigger('change');
         loadRequests();
         setTimeout(() => hideMsg(), 3000);
       }
    } catch (err) {
      showMsg({ success: false, message: 'Error: ' + err.message });
    }
  });

  function showMsg(obj) {
    msgBox.style.display = 'block';
    const isSuccess = obj?.success === true;
    const icon = isSuccess ? 'bi-check-circle-fill' : 'bi-exclamation-circle-fill';
    const alertClass = isSuccess ? 'alert-success' : 'alert-danger';
    msgBox.className = `alert ${alertClass} shadow-sm border-0`;
    msgBox.innerHTML = `<i class="bi ${icon} me-2"></i> ${obj?.message || JSON.stringify(obj)}`;
  }
  function hideMsg() {
    msgBox.style.display = 'none';
    msgBox.textContent = '';
  }

  function escapeHtml(s) {
    return String(s ?? '')
      .replaceAll('&','&amp;')
      .replaceAll('<','&lt;')
      .replaceAll('>','&gt;')
      .replaceAll('"','&quot;')
      .replaceAll("'","&#039;");
  }

  document.getElementById('btnOpenCreate').addEventListener('click', () => createModal.show());

  async function apiGet(url) {
    const res = await fetch(url, { credentials: 'same-origin', headers: { 'Accept': 'application/json' } });
    const data = await res.json().catch(() => ({}));
    return { res, data };
  }

  let docTypesCache = [];

  async function loadDropdowns() {
    // residents
    const r1 = await apiGet('/api/v1/residents/options');
    if (!r1.res.ok) return showMsg({ status: r1.res.status, data: r1.data });

    const residents = r1.data.residents || [];
    const cResident = document.getElementById('c_resident');
    cResident.innerHTML = '<option value=""></option>' + residents.map(r => {
      const name = `${r.last_name}, ${r.first_name} ${r.middle_name ? r.middle_name : ''}`.trim();
      const address = r.address_line ? ` — ${r.address_line}` : '';
      return `<option value="${r.id}">${escapeHtml(name + address)}</option>`;
    }).join('');
    if (!residents.length) {
      cResident.innerHTML = '<option value=""></option>';
    }
    $('#c_resident').select2({
      theme: 'bootstrap-5',
      placeholder: $('#c_resident').data('placeholder'),
      allowClear: true,
      width: '100%',
      dropdownParent: $('#createModal'),
      language: {
        noResults: () => 'No residents found'
      }
    });

    // document types
    const r2 = await apiGet('/api/v1/document-types/options');
    if (!r2.res.ok) return showMsg({ status: r2.res.status, data: r2.data });

    docTypesCache = r2.data.document_types || [];
    const cDoc = document.getElementById('c_doc_type');
    cDoc.innerHTML = docTypesCache.map(d => {
      return `<option value="${d.id}" data-fee="${d.fee ?? ''}">${escapeHtml(d.name)}</option>`;
    }).join('') || `<option value="">No active document types</option>`;

    // set initial fee
    updateFee();
  }

  function updateFee() {
    const select = document.getElementById('c_doc_type');
    const opt = select.options[select.selectedIndex];

    const raw = opt ? opt.getAttribute('data-fee') : '';

    // if null/empty -> Free
    if (raw === null || raw === undefined || String(raw).trim() === '' || raw === 0) {
      document.getElementById('c_fee').value = 'Free';
      return;
    }

    const fee = Number(raw);

    // if not valid number -> Free fallback
    if (Number.isNaN(fee)) {
      document.getElementById('c_fee').value = 'Free';
      return;
    }

    document.getElementById('c_fee').value = fee.toFixed(2);
  }

  document.getElementById('c_doc_type').addEventListener('change', updateFee);

  function renderPagination(paging) {
    const current = paging?.current_page ?? 1;
    const last = paging?.last_page ?? 1;
    if (last <= 1) {
      pagination.innerHTML = '';
      return;
    }

    let html = `<li class="page-item ${current <= 1 ? 'disabled' : ''}">`;
    html += current <= 1
      ? '<span class="page-link">‹</span>'
      : `<a class="page-link" href="#" data-page="${current - 1}">‹</a>`;
    html += '</li>';

    for (let page = 1; page <= last; page++) {
      html += `<li class="page-item ${page === current ? 'active' : ''}">`;
      html += page === current
        ? `<span class="page-link">${page}</span>`
        : `<a class="page-link" href="#" data-page="${page}">${page}</a>`;
      html += '</li>';
    }

    html += `<li class="page-item ${current >= last ? 'disabled' : ''}">`;
    html += current >= last
      ? '<span class="page-link">›</span>'
      : `<a class="page-link" href="#" data-page="${current + 1}">›</a>`;
    html += '</li>';
    pagination.innerHTML = html;
  }

  async function loadRequests(page = 1) {
    tbody.innerHTML = `<tr><td colspan="7" class="text-center py-5 text-muted"><div class="spinner-border text-primary mb-2" role="status"></div><div class="small">Loading...</div></td></tr>`;

    const q = document.getElementById('search').value.trim();
    const params = new URLSearchParams({ page });
    if (q) params.set('q', q);
    const url = `/api/v1/document-requests?${params}`;

    const { res, data } = await apiGet(url);
    if (!res.ok) {
      showMsg({ status: res.status, data });
      tbody.innerHTML = `<tr><td colspan="7" class="text-center py-5 text-danger">Failed to load requests</td></tr>`;
      pagination.innerHTML = '';
      return;
    }

    const items = data.document_requests || [];
    renderPagination(data.pagination);
    if (!items.length) {
      tbody.innerHTML = `<tr><td colspan="7" class="text-center py-5 text-muted"><i class="bi bi-file-earmark-x fs-1 d-block mb-2 opacity-25"></i>No requests found.</td></tr>`;
      return;
    }

    tbody.innerHTML = items.map(x => {
      const resident = x.resident
        ? `${x.resident.last_name}, ${x.resident.first_name} ${x.resident.middle_name ? x.resident.middle_name : ''}`.trim()
        : '—';

      const dt = x.document_type || x.documentType || null;
      const doc = dt ? dt.name : '—';

      const feeNum = Number(x.fee ?? (x.document_type?.fee ?? 0));
      const fee = (!feeNum || Number.isNaN(feeNum)) ? 'Free' : '₱' + feeNum.toFixed(2);
      const storedStatus = String(x.status || 'pending').toLowerCase();
      const status = storedStatus === 'released' ? 'completed' : storedStatus;
      const statusClass = status === 'completed'
        ? 'bg-success-subtle text-success border border-success-subtle'
        : status === 'pending'
          ? 'bg-warning-subtle text-warning-emphasis border border-warning-subtle'
          : status === 'pickup'
            ? 'bg-info-subtle text-info border border-info-subtle'
            : 'bg-secondary-subtle text-secondary border border-secondary-subtle';
      const statusLabel = status === 'pickup' ? 'For Pickup' : status.charAt(0).toUpperCase() + status.slice(1);
      const downloadAction = status === 'pending' || status === 'pickup'
        ? `<button class="btn btn-sm btn-primary rounded-circle d-flex align-items-center justify-content-center print-pdf-button" style="width: 32px; height: 32px;" type="button" data-url="/admin/document-requests/${encodeURIComponent(x.id)}/print-complete" title="Download and print PDF" aria-label="Download and print PDF">
             <i class="bi bi-file-earmark-pdf"></i>
           </button>
           ${status === 'pending' ? `<button class="btn btn-sm btn-outline-primary rounded-circle d-flex align-items-center justify-content-center set-pickup-button" style="width: 32px; height: 32px;" type="button" data-url="/admin/document-requests/${encodeURIComponent(x.id)}/pickup" title="Set for pickup" aria-label="Set for pickup">
             <i class="bi bi-bag-check"></i>
           </button>` : ''}`
        : status === 'completed'
          ? `<a class="btn btn-sm btn-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" href="/admin/document-requests/${encodeURIComponent(x.id)}/download" title="Download DOCX">
             <i class="bi bi-file-earmark-word"></i>
           </a>`
          : `<button class="btn btn-sm btn-secondary rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" type="button" title="This request cannot be downloaded" disabled>
               <i class="bi bi-file-earmark-word"></i>
             </button>`;

      return `
        <tr>
          <td class="px-4 fw-bold text-primary">${escapeHtml(x.control_no)}</td>
          <td>
            <div class="fw-bold text-dark">${escapeHtml(resident)}</div>
            <div class="small text-muted">${escapeHtml(x.resident?.address_line || '')}</div>
          </td>
          <td><span class="badge bg-light text-dark border">${escapeHtml(doc)}</span></td>
          <td>${fee}</td>
          <td class="text-muted small">${escapeHtml(String(x.created_at ?? '').replace('T',' ').slice(0,19))}</td>
          <td><span class="badge rounded-pill ${statusClass}">${escapeHtml(statusLabel)}</span></td>
            <td class="text-end pe-4">
              <div class="d-flex justify-content-end gap-2">
                  ${downloadAction}
                  <form method="POST" action="/admin/document/requests/${x.id}" style="display:inline;" class="archive-form">
                  <input type="hidden" name="_token" value="{{ csrf_token() }}">
                  <input type="hidden" name="_method" value="DELETE">

                  </form>
              </div>
            </td>
        </tr>
      `;
    }).join('');
  }

  pagination.addEventListener('click', (event) => {
    const link = event.target.closest('a[data-page]');
    if (!link) return;
    event.preventDefault();
    loadRequests(Number(link.dataset.page));
  });

  // Archive form handling
  tbody.addEventListener('submit', async (e) => {
    const form = e.target.closest('.archive-form');
    if (!form) return;
    
    e.preventDefault();
    if (!confirm('Are you sure you want to archive this item?')) return;
    
    const formData = new FormData(form);
    const url = form.action;
    
    try {
      const res = await fetch(url, {
        method: 'POST',
        body: formData,
        headers: {
          'X-CSRF-TOKEN': formData.get('_token'),
          'X-HTTP-Method-Override': 'DELETE'
        }
      });
      
      const data = await res.json();
      if (res.ok) {
        alert(data.message || 'Archived successfully');
        loadRequests();
      } else {
        alert(data.message || 'Error archiving item');
      }
    } catch (err) {
      alert('Error: ' + err.message);
    }
  });

  document.getElementById('search').addEventListener('keydown', (e) => {
    if (e.key === 'Enter') {
      hideMsg();
      loadRequests(1);
    }
  });

  tbody.addEventListener('click', async (event) => {
    const printButton = event.target.closest('.print-pdf-button');
    const pickupButton = event.target.closest('.set-pickup-button');
    if (!printButton && !pickupButton) return;

    const button = printButton || pickupButton;
    if (printButton && !confirm('Generate, download, and print the PDF? This will set the request for pickup and create a pending payment entry.')) return;
    if (pickupButton && !confirm('Set this document request for pickup?')) return;

    const printWindow = printButton ? window.open('', '_blank') : null;
    if (printButton && !printWindow) {
      showMsg({ success: false, message: 'Allow pop-ups to open the PDF print window, then try again.' });
      return;
    }
    button.disabled = true;
    try {
      const response = await fetch(button.dataset.url, {
        method: printButton ? 'POST' : 'PATCH',
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Accept': printButton ? 'application/pdf' : 'application/json'
        },
        credentials: 'same-origin'
      });

      if (!response.ok) {
        const responseText = await response.text();
        let errorMessage = '';
        try {
          errorMessage = JSON.parse(responseText).message || '';
        } catch {
          errorMessage = responseText.replace(/<[^>]*>/g, ' ').replace(/\s+/g, ' ').trim();
        }
        throw new Error(errorMessage || `The requested action failed (HTTP ${response.status}).`);
      }

      if (printButton) {
        const contentType = response.headers.get('Content-Type') || '';
        if (!contentType.includes('application/pdf')) {
          throw new Error('The generated document was not a PDF.');
        }
        const blob = await response.blob();
        const pdfUrl = URL.createObjectURL(blob);
        const disposition = response.headers.get('Content-Disposition') || '';
        const filename = disposition.match(/filename="?([^";]+)"?/i)?.[1] || 'document-request.pdf';
        const downloadLink = document.createElement('a');
        downloadLink.href = pdfUrl;
        downloadLink.download = filename;
        document.body.appendChild(downloadLink);
        downloadLink.click();
        downloadLink.remove();

        if (printWindow) {
          const iframe = printWindow.document.createElement('iframe');
          iframe.title = 'Document PDF';
          iframe.src = pdfUrl;
          iframe.style.cssText = 'width:100%;height:100%;border:0';
          printWindow.document.body.style.margin = '0';
          printWindow.document.body.appendChild(iframe);
          iframe.addEventListener('load', () => {
            printWindow.focus();
            setTimeout(() => printWindow.print(), 500);
          }, { once: true });
          printWindow.addEventListener('afterprint', () => URL.revokeObjectURL(pdfUrl), { once: true });
        }
        showMsg({ success: true, message: 'PDF downloaded; the print dialog was opened. Request marked completed.' });
      } else {
        const data = await response.json();
        showMsg({ success: true, message: data.message || 'Document request set for pickup.' });
      }

      await loadRequests();
      setTimeout(hideMsg, 3500);
    } catch (error) {
      if (printWindow && !printWindow.closed) printWindow.close();
      button.disabled = false;
      showMsg({ success: false, message: error.message });
    }
  });

  // auto-open create modal if validation errors
  @if ($errors->any())
    createModal.show();
  @endif

  // init
  loadDropdowns();
  loadRequests();
</script>
@endpush
@endsection
