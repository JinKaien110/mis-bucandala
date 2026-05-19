@extends('layouts.admin')

@section('title', 'Document Types - Barangay MIS')

@section('content')
<div class="page-surface">
  
  <!-- Header -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h4 class="mb-1 fw-bold text-primary">Document Types</h4>
      <p class="text-muted mb-0">Manage certificate templates and fees</p>
    </div>
    <button class="btn btn-primary" id="btnOpenCreate" type="button">
        <i class="bi bi-plus-lg me-1"></i> Add Document Type
    </button>
  </div>

  @if(session('success'))
    <div class="alert alert-success d-flex align-items-center shadow-sm border-0 mb-4">
        <i class="bi bi-check-circle-fill fs-4 me-2"></i>
        <div>{{ session('success') }}</div>
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
                    <input id="search" type="text" class="form-control border-start-0 ps-0" placeholder="Search document type...">
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
              <th class="px-4 py-3 border-0">ID</th>
              <th class="py-3 border-0">Document Type</th>
              <th class="py-3 border-0">Fee</th>
              <th class="py-3 border-0">Template</th>
              <th class="py-3 border-0">File Name</th>
              <th class="py-3 border-0">Status</th>
              <th class="py-3 border-0 text-end pe-4">Actions</th>
            </tr>
          </thead>
          <tbody id="tbody">
            <tr><td colspan="7" class="text-center py-5 text-muted">Loading...</td></tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Create Modal -->
<div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header border-bottom-0 pb-0">
        <h5 class="modal-title fw-bold text-primary">Add Document Type</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form method="POST" action="{{ route('admin.document-types.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label class="form-label small text-muted fw-bold">Document Name <span class="text-danger">*</span></label>
                <input name="name" class="form-control" placeholder="e.g. Barangay Clearance" value="{{ old('name') }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label small text-muted fw-bold">File Name (Optional)</label>
                <input name="file_name" class="form-control" placeholder="e.g. KP-Form-01" value="{{ old('file_name') }}">
                <div class="form-text">If left blank, the <strong>original filename</strong> of the upload will be used.</div>
            </div>
            <div class="mb-3">
                <label class="form-label small text-muted fw-bold">Fee (PHP)</label>
                <input name="fee" type="number" step="0.01" min="0" class="form-control" placeholder="0.00" value="{{ old('fee') }}">
            </div>
            <div class="mb-3">
                <label class="form-label small text-muted fw-bold">Template (.docx)</label>
                <input type="file" name="template" accept=".docx" class="form-control">
                <div class="form-text">Select the .docx file you want to use.</div>
            </div>
            <div class="text-end mt-4">
                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary px-4">Save</button>
            </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header border-bottom-0 pb-0">
        <h5 class="modal-title fw-bold text-primary">Edit Document Type</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form method="POST" id="editForm" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label class="form-label small text-muted fw-bold">Document Name <span class="text-danger">*</span></label>
                <input id="e_name" name="name" class="form-control" placeholder="Document name" required>
            </div>
            <div class="mb-3">
                <label class="form-label small text-muted fw-bold">File Name (Internal)</label>
                <input id="e_file_name" name="file_name" class="form-control" placeholder="e.g. clearance-template">
                <div class="form-text">Changing this will rename the file stored in the system.</div>
            </div>
            <div class="mb-3">
                <label class="form-label small text-muted fw-bold">Fee (PHP)</label>
                <input id="e_fee" name="fee" type="number" step="0.01" min="0" class="form-control" placeholder="0.00">
            </div>
            <div class="mb-3">
                <label class="form-label small text-muted fw-bold">Replace Template (.docx)</label>
                <input type="file" name="template" accept=".docx" class="form-control">
                <div class="form-text">If you upload a new file and leave 'File Name' blank, the new actual filename will be used.</div>
                <div id="e_template_info" class="small text-primary mt-1"></div>
            </div>
            <div class="text-end mt-4">
                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary px-4">Update</button>
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

  // Initialize Bootstrap Modals
  const createModal = new bootstrap.Modal(document.getElementById('createModal'));
  const editModal = new bootstrap.Modal(document.getElementById('editModal'));

  function showMsg(obj) {
    msgBox.style.display = 'block';
    msgBox.className = 'alert alert-info shadow-sm border-0';
    msgBox.innerHTML = `<i class="bi bi-info-circle-fill me-2"></i> ${typeof obj === 'string' ? obj : JSON.stringify(obj, null, 2)}`;
  }
  function hideMsg() {
    msgBox.style.display = 'none';
    msgBox.textContent = '';
  }

  function escapeHtml(s) {
    return String(s ?? '')
      .replaceAll('&','&')
      .replaceAll('<','<')
      .replaceAll('>','>')
      .replaceAll('"','"')
      .replaceAll("'","&#039;");
  }

  function pillStatus(status) {
    const s = (status || 'inactive').toLowerCase();
    if (s === 'active') {
      return `<span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">ACTIVE</span>`;
    }
    return `<span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill">INACTIVE</span>`;
  }

  // UPDATED: Set default prefix when opening Create Modal
  document.getElementById('btnOpenCreate').addEventListener('click', () => {
    const createForm = document.querySelector('#createModal form');
    if(createForm) {
        createForm.reset();
        createForm.querySelector('input[name="file_name"]').value = "KP-Form-";
    }
    createModal.show();
  });

  async function apiGet(url) {
    const res = await fetch(url, { credentials: 'same-origin', headers: { 'Accept': 'application/json' } });
    const data = await res.json().catch(() => ({}));
    return { res, data };
  }

  async function loadDocumentTypes() {
    hideMsg();
    tbody.innerHTML = `<tr><td colspan="7" class="text-center py-5 text-muted"><div class="spinner-border text-primary mb-2" role="status"></div><div class="small">Loading...</div></td></tr>`;

    const q = document.getElementById('search').value.trim();
    const url = q ? `/api/v1/document-types?q=${encodeURIComponent(q)}` : `/api/v1/document-types`;

    const { res, data } = await apiGet(url);

    if (!res.ok) {
      showMsg({ status: res.status, data });
      tbody.innerHTML = `<tr><td colspan="7" class="text-center py-5 text-danger">Failed to load document types</td></tr>`;
      return;
    }

    const docs = data.types || [];
    if (!docs.length) {
      tbody.innerHTML = `<tr><td colspan="7" class="text-center py-5 text-muted"><i class="bi bi-file-earmark-x fs-1 d-block mb-2 opacity-25"></i>No document types found.</td></tr>`;
      return;
    }

    tbody.innerHTML = docs.map(d => {
      const id = d.id;
      const name = escapeHtml(d.name);
      const fee = Number(d.fee ?? 0).toFixed(2);
      const status = escapeHtml(d.status ?? 'active');
      const fileName = escapeHtml(d.file_name ?? '-');

      const template = d.template_path
        ? `<span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill"><i class="bi bi-file-earmark-word me-1"></i>Uploaded</span>`
        : `<span class="text-muted small">None</span>`;

      const toggleLabel = status === 'active' ? 'Deactivate' : 'Activate';
      const toggleIcon = status === 'active' ? 'bi-toggle-on text-success' : 'bi-toggle-off text-secondary';

      return `
        <tr>
          <td class="px-4 text-muted small">${id}</td>
          <td class="fw-bold text-dark">${name}</td>
          <td>₱${fee}</td>
          <td>${template}</td>
          <td><code class="small">${fileName}</code></td>
          <td>${pillStatus(status)}</td>
          <td class="text-end pe-4">
            <div class="d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-sm btn-warning rounded-circle" style="width: 32px; height: 32px;" data-action="edit" data-id="${id}" title="Edit">
                    <i class="bi bi-pencil-fill"></i>
                </button>
                <form method="POST" action="/admin/document-types/${id}/toggleStatus" style="display:inline;">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="_method" value="PATCH">
                <button class="btn btn-sm btn-secondary rounded-circle" style="width: 32px; height: 32px;" type="submit" title="${toggleLabel}">
                    <i class="bi ${toggleIcon} fs-6"></i>
                </button>
                </form>
                <form method="POST" action="/admin/document-types/${id}" style="display:inline;" class="archive-form">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="_method" value="DELETE">
                <button class="btn btn-sm btn-danger rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" type="submit" title="Archive">
                    <i class="bi bi-archive fs-6"></i>
                </button>
                </form>
            </div>
          </td>
        </tr>
      `;
    }).join('');
  }

  tbody.addEventListener('click', async (e) => {
    const btn = e.target.closest('button[data-action="edit"]');
    if (!btn) return;

    const id = btn.getAttribute('data-id');
    const { res, data } = await apiGet(`/api/v1/document-types/${id}`);
    if (!res.ok) return showMsg({ status: res.status, data });

    const d = data.document_type;
    const form = document.getElementById('editForm');
    form.action = `/admin/document-types/${id}`;

    document.getElementById('e_name').value = d.name ?? '';
    document.getElementById('e_fee').value = d.fee ?? 0;
    
    // Fill filename - if null, we provide the prefix as a base
    document.getElementById('e_file_name').value = d.file_name ?? 'KP-Form-';
    
    const info = d.template_path
        ? `<div class="alert alert-secondary py-2 small mb-0 mt-2">
            <i class="bi bi-file-earmark-check-fill text-success me-1"></i>
            Current: <strong>${escapeHtml(d.file_name)}.docx</strong>
           </div>`
        : `<div class="text-muted small italic mt-2"><i class="bi bi-info-circle me-1"></i>No template file uploaded.</div>`;
    
    document.getElementById('e_template_info').innerHTML = info;
    editModal.show();
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
        loadDocumentTypes();
      } else {
        alert(data.message || 'Error archiving item');
      }
    } catch (err) {
      alert('Error: ' + err.message);
    }
  });

  document.getElementById('search').addEventListener('keydown', (e) => {
    if (e.key === 'Enter') loadDocumentTypes();
  });

  @if ($errors->any())
    createModal.show();
  @endif

  loadDocumentTypes();
</script>
@endpush
@endsection
