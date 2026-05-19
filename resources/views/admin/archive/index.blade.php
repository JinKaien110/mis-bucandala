@extends('layouts.admin')

@section('title', 'Archive - Barangay MIS')

@section('content')
<div class="page-surface">

  <!-- Header with stats -->
  <div class="d-flex justify-content-between align-items-start mb-4">
    <div>
      <h4 class="mb-2 fw-bold text-primary d-flex align-items-center gap-2">
        <i class="bi bi-archive-fill text-warning"></i>
        Archived Records
      </h4>
      <p class="text-muted mb-0">View and manage all archived data across the system</p>
    </div>
    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
      <i class="bi bi-arrow-left me-1"></i> Back to Dashboard
    </a>
  </div>

  <!-- Stats Cards -->
  <div class="row g-3 mb-4">
    <div class="col-md-4">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <div class="text-muted small text-uppercase fw-bold mb-1">Archived Residents</div>
              <div class="h3 fw-bold text-primary mb-0" id="stat-residents">0</div>
            </div>
            <div class="bg-primary bg-gradient text-white rounded-circle p-3" style="width: 56px; height: 56px; display: flex; align-items: center; justify-content: center;">
              <i class="bi bi-people-fill fs-4"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <div class="text-muted small text-uppercase fw-bold mb-1">Archived Templates</div>
              <div class="h3 fw-bold text-warning mb-0" id="stat-documents">0</div>
            </div>
            <div class="bg-warning bg-gradient text-white rounded-circle p-3" style="width: 56px; height: 56px; display: flex; align-items: center; justify-content: center;">
              <i class="bi bi-file-earmark-text-fill fs-4"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <div class="text-muted small text-uppercase fw-bold mb-1">Archived Requests</div>
              <div class="h3 fw-bold text-success mb-0" id="stat-requests">0</div>
            </div>
            <div class="bg-success bg-gradient text-white rounded-circle p-3" style="width: 56px; height: 56px; display: flex; align-items: center; justify-content: center;">
              <i class="bi bi-clipboard-check-fill fs-4"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Flash Messages -->
  <div id="message" class="alert mb-4" style="display:none;"></div>

  <!-- Main Archive Card -->
  <div class="card border-0 shadow-sm overflow-hidden">
    <!-- Tab Navigation -->
    <div class="card-header bg-white border-0 px-4 pt-4 pb-0">
      <ul class="nav nav-pills nav-fill" id="archiveTabs" role="tablist">
        <li class="nav-item" role="presentation">
          <button class="nav-link active rounded-pill px-4 py-2" id="residents-tab" data-bs-toggle="tab" data-bs-target="#residents" type="button" role="tab">
            <i class="bi bi-people me-2"></i>Residents
            <span class="badge bg-primary ms-1" id="badge-residents">0</span>
          </button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link rounded-pill px-4 py-2" id="documents-tab" data-bs-toggle="tab" data-bs-target="#documents" type="button" role="tab">
            <i class="bi bi-file-earmark-text me-2"></i>Templates
            <span class="badge bg-warning ms-1" id="badge-documents">0</span>
          </button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link rounded-pill px-4 py-2" id="requests-tab" data-bs-toggle="tab" data-bs-target="#requests" type="button" role="tab">
            <i class="bi bi-journal-text me-2"></i>Requests
            <span class="badge bg-success ms-1" id="badge-requests">0</span>
          </button>
        </li>
      </ul>
    </div>

    <!-- Tab Content -->
    <div class="card-body">
      <div class="tab-content" id="archiveTabsContent">
        <!-- Residents Tab -->
        <div class="tab-pane fade show active" id="residents" role="tabpanel">
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="bg-light text-uppercase small text-muted">
                <tr>
                  <th class="px-3 py-3">#</th>
                  <th class="py-3">Resident</th>
                  <th class="py-3">Civil Status</th>
                  <th class="py-3">Contact</th>
                  <th class="py-3">Archived On</th>
                  <th class="py-3 text-end pe-4">Actions</th>
                </tr>
              </thead>
              <tbody id="residents-tbody">
                <tr>
                  <td colspan="6" class="text-center py-5">
                    <div class="d-flex flex-column align-items-center">
                      <div class="spinner-border text-primary mb-2" role="status"></div>
                      <small class="text-muted">Loading archived residents...</small>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Document Types Tab -->
        <div class="tab-pane fade" id="documents" role="tabpanel">
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="bg-light text-uppercase small text-muted">
                <tr>
                  <th class="px-3 py-3">#</th>
                  <th class="py-3">Template Name</th>
                  <th class="py-3">Fee</th>
                  <th class="py-3">Archived On</th>
                  <th class="py-3 text-end pe-4">Actions</th>
                </tr>
              </thead>
              <tbody id="documents-tbody">
                <tr>
                  <td colspan="5" class="text-center py-5">
                    <div class="d-flex flex-column align-items-center">
                      <div class="spinner-border text-warning mb-2" role="status"></div>
                      <small class="text-muted">Loading archived templates...</small>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Document Requests Tab -->
        <div class="tab-pane fade" id="requests" role="tabpanel">
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="bg-light text-uppercase small text-muted">
                <tr>
                  <th class="px-3 py-3">Control No.</th>
                  <th class="py-3">Resident</th>
                  <th class="py-3">Document</th>
                  <th class="py-3">Purpose</th>
                  <th class="py-3">Archived On</th>
                  <th class="py-3 text-end pe-4">Actions</th>
                </tr>
              </thead>
              <tbody id="requests-tbody">
                <tr>
                  <td colspan="6" class="text-center py-5">
                    <div class="d-flex flex-column align-items-center">
                      <div class="spinner-border text-success mb-2" role="status"></div>
                      <small class="text-muted">Loading archived requests...</small>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Info Banner -->
  <div class="mt-4">
    <div class="alert alert-info border-0 shadow-sm">
      <div class="d-flex align-items-center">
        <i class="bi bi-info-circle-fill fs-4 me-3 text-info"></i>
        <div class="small">
          <strong>Restoring Records:</strong> Click the restore button to return an archived record to the main system. The record will reappear in the active tables and can be used normally again.
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('styles')
<style>
  .nav-pills .nav-link {
    color: #6c757d;
    font-weight: 500;
    transition: all 0.2s ease;
    border: 1px solid transparent;
  }
  .nav-pills .nav-link:hover {
    background-color: rgba(13, 110, 253, 0.08);
    color: #0d6efd;
  }
  .nav-pills .nav-link.active {
    background-color: #0d6efd;
    color: white;
    box-shadow: 0 2px 8px rgba(13, 110, 253, 0.3);
  }
  .nav-pills .nav-link.active .badge {
    background-color: white;
    color: #0d6efd;
  }
  .badge {
    font-weight: 500;
  }
  .table thead th {
    border-bottom: 2px solid #dee2e6;
    font-size: 0.75rem;
    letter-spacing: 0.5px;
    text-transform: uppercase;
  }
  .table tbody tr {
    transition: background-color 0.15s ease;
  }
  .table tbody tr:hover {
    background-color: rgba(0, 0, 0, 0.02);
  }
  .btn-icon {
    width: 36px;
    height: 36px;
    padding: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: all 0.2s ease;
  }
  .btn-icon:hover {
    transform: scale(1.1);
  }
  .card {
    border-radius: 12px;
  }
  .stat-card {
    border-left: 4px solid #0d6efd;
  }
  .stat-card.warning { border-left-color: #ffc107; }
  .stat-card.success { border-left-color: #198754; }
</style>
@endpush

@push('scripts')
<script>
const csrf = () => document.querySelector('meta[name="csrf-token"]').content;

async function api(url, options = {}) {
  const method = (options.method || 'GET').toUpperCase();
  const res = await fetch(url, {
    credentials: 'same-origin',
    method,
    headers: {
      'Accept': 'application/json',
      ...(method !== 'GET' ? { 'X-CSRF-TOKEN': csrf() } : {}),
      ...(options.headers || {})
    },
    body: options.body
  });
  try {
    const data = await res.json();
    return { res, data };
  } catch (e) {
    const text = await res.text();
    console.error('API error:', e, text);
    return { res, data: { error: text } };
  }
}

function escapeHtml(s) {
  return String(s ?? '')
    .replaceAll('&', '&amp;')
    .replaceAll('<', '&lt;')
    .replaceAll('>', '&gt;')
    .replaceAll('"', '&quot;')
    .replaceAll("'", '&#039;');
}

function showMsg(obj, type = 'info') {
  const msgBox = document.getElementById('message');
  msgBox.style.display = 'block';
  const alertClass = type === 'success' ? 'alert-success' : (type === 'error' ? 'alert-danger' : 'alert-info');
  msgBox.className = `alert ${alertClass} border-0 shadow-sm mb-4`;
  
  const icon = type === 'success' ? 'bi-check-circle-fill' : (type === 'error' ? 'bi-exclamation-triangle-fill' : 'bi-info-circle-fill');
  const content = typeof obj === 'string' ? obj : (obj.message || JSON.stringify(obj));
  
  msgBox.innerHTML = `<i class="bi ${icon} me-2"></i> ${content}`;
  msgBox.scrollIntoView({ behavior: 'smooth', block: 'start' });
  
  setTimeout(() => {
    msgBox.style.display = 'none';
  }, 5000);
}

function hideMsg(){ 
  const msgBox = document.getElementById('message');
  msgBox.style.display = 'none'; 
  msgBox.textContent = '';
}

// Table elements
const tbodyResidents = document.getElementById('residents-tbody');
const tbodyDocuments = document.getElementById('documents-tbody');
const tbodyRequests = document.getElementById('requests-tbody');

// Load archived residents
async function loadArchivedResidents() {
  tbodyResidents.innerHTML = `<tr><td colspan="6" class="text-center py-4"><div class="d-flex flex-column align-items-center"><div class="spinner-border text-primary mb-2"></div><small class="text-muted">Loading...</small></div></td></tr>`;
  
  try {
    const { res, data } = await api('/api/v1/archive/residents');
    if (!res.ok) throw new Error(data.message || 'Failed to load');
    
    const list = data.residents || [];
    document.getElementById('stat-residents').textContent = list.length;
    document.getElementById('badge-residents').textContent = list.length;

    if (list.length === 0) {
      tbodyResidents.innerHTML = `<tr><td colspan="6" class="text-center py-5 text-muted"><i class="bi bi-people fs-1 d-block mb-2 opacity-25"></i>No archived residents</td></tr>`;
      return;
    }

    tbodyResidents.innerHTML = list.map(r => {
      const archivedDate = r.archived_at ? new Date(r.archived_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : '-';
      const fullName = `${escapeHtml(r.first_name || '')} ${escapeHtml(r.last_name || '')}`.trim();
      
      return `
        <tr>
          <td class="px-3 py-3 text-muted small">${r.id}</td>
          <td>
            <div class="fw-bold text-dark">${fullName || 'Unknown'}</div>
            <div class="small text-muted">${r.civil_status || 'N/A'}</div>
          </td>
          <td><span class="badge bg-light text-dark border">${r.civil_status || 'N/A'}</span></td>
          <td class="small text-muted">${escapeHtml(r.contact_no || r.address_line || '-')}</td>
          <td class="small">${archivedDate}</td>
          <td class="text-end pe-4">
            <button class="btn btn-sm btn-success rounded-circle btn-icon" data-action="restore" data-id="${r.id}" title="Restore">
              <i class="bi bi-arrow-counterclockwise"></i>
            </button>
          </td>
        </tr>
      `;
    }).join('');
  } catch (err) {
    tbodyResidents.innerHTML = `<tr><td colspan="6" class="text-center py-4 text-danger"><i class="bi bi-exclamation-triangle me-2"></i>Failed to load residents</td></tr>`;
  }
}

// Load archived document types
async function loadArchivedDocuments() {
  tbodyDocuments.innerHTML = `<tr><td colspan="5" class="text-center py-4"><div class="d-flex flex-column align-items-center"><div class="spinner-border text-warning mb-2"></div><small class="text-muted">Loading...</small></div></td></tr>`;
  
  try {
    const { res, data } = await api('/api/v1/archive/document-types');
    if (!res.ok) throw new Error(data.message || 'Failed to load');
    
    const list = data.document_types || [];
    document.getElementById('stat-documents').textContent = list.length;
    document.getElementById('badge-documents').textContent = list.length;

    if (list.length === 0) {
      tbodyDocuments.innerHTML = `<tr><td colspan="5" class="text-center py-5 text-muted"><i class="bi bi-file-earmark-text fs-1 d-block mb-2 opacity-25"></i>No archived templates</td></tr>`;
      return;
    }

    tbodyDocuments.innerHTML = list.map(d => `
      <tr>
        <td class="px-3 py-3 text-muted small">${d.id}</td>
        <td>
          <div class="fw-bold text-dark">${escapeHtml(d.name)}</div>
          ${d.description ? `<div class="small text-muted text-truncate" style="max-width: 300px;">${escapeHtml(d.description)}</div>` : ''}
        </td>
        <td class="small">₱${parseFloat(d.fee).toFixed(2)}</td>
        <td class="small">${d.archived_at ? new Date(d.archived_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : '-'}</td>
        <td class="text-end pe-4">
          <button class="btn btn-sm btn-success rounded-circle btn-icon" data-action="restore-doctype" data-id="${d.id}" title="Restore">
            <i class="bi bi-arrow-counterclockwise"></i>
          </button>
        </td>
      </tr>
    `).join('');
  } catch (err) {
    tbodyDocuments.innerHTML = `<tr><td colspan="5" class="text-center py-4 text-danger"><i class="bi bi-exclamation-triangle me-2"></i>Failed to load templates</td></tr>`;
  }
}

// Load archived document requests
async function loadArchivedRequests() {
  tbodyRequests.innerHTML = `<tr><td colspan="6" class="text-center py-4"><div class="d-flex flex-column align-items-center"><div class="spinner-border text-success mb-2"></div><small class="text-muted">Loading...</small></div></td></tr>`;
  
  try {
    const { res, data } = await api('/api/v1/archive/document-requests');
    if (!res.ok) throw new Error(data.message || 'Failed to load');
    
    const list = data.document_requests || [];
    document.getElementById('stat-requests').textContent = list.length;
    document.getElementById('badge-requests').textContent = list.length;

    if (list.length === 0) {
      tbodyRequests.innerHTML = `<tr><td colspan="6" class="text-center py-5 text-muted"><i class="bi bi-journal-text fs-1 d-block mb-2 opacity-25"></i>No archived requests</td></tr>`;
      return;
    }

    tbodyRequests.innerHTML = list.map(req => `
      <tr>
        <td class="px-3 py-3 text-muted small"><span class="fw-bold">${escapeHtml(req.control_no)}</span></td>
        <td>
          <div class="fw-bold text-dark">${escapeHtml(req.resident?.first_name || '')} ${escapeHtml(req.resident?.last_name || '')}</div>
        </td>
        <td>${escapeHtml(req.document_type?.name || 'N/A')}</td>
        <td class="small text-muted text-truncate" style="max-width: 200px;">${escapeHtml(req.purpose?.substring(0, 40) || '-')}</td>
        <td class="small">${req.archived_at ? new Date(req.archived_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : '-'}</td>
        <td class="text-end pe-4">
          <button class="btn btn-sm btn-success rounded-circle btn-icon" data-action="restore-request" data-id="${req.id}" title="Restore">
            <i class="bi bi-arrow-counterclockwise"></i>
          </button>
        </td>
      </tr>
    `).join('');
  } catch (err) {
    tbodyRequests.innerHTML = `<tr><td colspan="6" class="text-center py-4 text-danger"><i class="bi bi-exclamation-triangle me-2"></i>Failed to load requests</td></tr>`;
  }
}

// Event delegation for restore buttons
document.addEventListener('click', async (e) => {
  const btn = e.target.closest('button');
  if (!btn) return;
  
  const action = btn.getAttribute('data-action');
  const id = btn.getAttribute('data-id');

  if (action === 'restore') {
    if (!confirm('Are you sure you want to restore this resident? They will return to the active residents list.')) return;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
    
    const { res, data } = await api(`/api/v1/residents/${id}/restore`, { method: 'POST' });
    btn.disabled = false;
    btn.innerHTML = '<i class="bi bi-arrow-counterclockwise"></i>';
    
    if (!res.ok) {
      showMsg(data.message || 'Failed to restore resident', 'error');
      return;
    }
    
    showMsg(data.message || 'Resident restored successfully', 'success');
    loadArchivedResidents();
  }

  if (action === 'restore-doctype') {
    if (!confirm('Restore this document template? It will become available for document requests.')) return;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
    
    const { res, data } = await api(`/admin/document-types/${id}/restore`, { method: 'POST' });
    btn.disabled = false;
    btn.innerHTML = '<i class="bi bi-arrow-counterclockwise"></i>';
    
    if (!res.ok) {
      showMsg(data.message || 'Failed to restore template', 'error');
      return;
    }
    
    showMsg(data.message || 'Template restored successfully', 'success');
    loadArchivedDocuments();
  }

  if (action === 'restore-request') {
    if (!confirm('Restore this document request? It will return to the active requests list.')) return;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
    
    const { res, data } = await api(`/admin/document-requests/${id}/restore`, { method: 'POST' });
    btn.disabled = false;
    btn.innerHTML = '<i class="bi bi-arrow-counterclockwise"></i>';
    
    if (!res.ok) {
      showMsg(data.message || 'Failed to restore request', 'error');
      return;
    }
    
    showMsg(data.message || 'Request restored successfully', 'success');
    loadArchivedRequests();
  }
});

// Initial load
document.addEventListener('DOMContentLoaded', () => {
  loadArchivedResidents();
  loadArchivedDocuments();
  loadArchivedRequests();
});

// Refresh on tab change
document.getElementById('archiveTabs').addEventListener('shown.bs.tab', (e) => {
  const target = e.target.getAttribute('data-bs-target');
  if (target === '#residents') loadArchivedResidents();
  if (target === '#documents') loadArchivedDocuments();
  if (target === '#requests') loadArchivedRequests();
});
</script>
@endpush
