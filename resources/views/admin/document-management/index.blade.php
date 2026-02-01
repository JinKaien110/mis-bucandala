<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Document Types</title>

  <style>
    body { font-family: Arial, sans-serif; max-width: 1100px; margin: 32px auto; padding: 0 16px; }
    .row { display:flex; gap:12px; align-items:center; flex-wrap:wrap; }
    .btn { padding:10px 12px; border:1px solid #ccc; background:#fff; cursor:pointer; border-radius:8px; }
    .btn.primary { border-color:#111; }
    .btn.danger { border-color:#c00; }
    .btn:disabled { opacity:.6; cursor:not-allowed; }
    input, select { padding:10px; border:1px solid #ccc; border-radius:8px; width:100%; box-sizing:border-box; }
    table { width:100%; border-collapse: collapse; margin-top: 14px; }
    th, td { border-bottom:1px solid #eee; padding:10px; text-align:left; vertical-align: top; }
    th { background:#fafafa; }
    .pill { padding:4px 8px; border-radius:999px; border:1px solid #ddd; font-size:12px; display:inline-block; }
    .pill.active { border-color:#0a0; }
    .pill.inactive { border-color:#c00; }
    .muted { color:#666; font-size: 13px; }
    .small { font-size: 12px; color: #666; }
    .nowrap { white-space: nowrap; }

    .alert { margin-top: 14px; padding: 10px; border:1px solid #eee; border-radius:8px; background:#fafafa; white-space: pre-wrap; }
    .alert.success { border-color:#bde5c8; background:#f0fff4; }
    .alert.error { border-color:#f5b5b5; background:#fff5f5; }
    ul { margin: 8px 0 0; padding-left: 18px; }

    /* Modal */
    .backdrop { position:fixed; inset:0; background: rgba(0,0,0,.35); display:none; align-items:center; justify-content:center; padding:16px; }
    .modal { width:100%; max-width:720px; background:#fff; border-radius:12px; border:1px solid #eee; padding:16px; }
    .modal h3 { margin:0 0 10px; }
    .grid { display:grid; gap:10px; grid-template-columns: 1fr 1fr; }
    .grid .full { grid-column: 1 / -1; }
    .actions { display:flex; gap:10px; justify-content:flex-end; margin-top:12px; }
  </style>
</head>
<body>

  <h2>Document Types</h2>
  <p class="muted">Admin manages certificate/letter types here. Upload a .docx template (optional for now).</p>

  {{-- Blade flash messages --}}
  @if (session('success'))
    <div class="alert success">{{ session('success') }}</div>
  @endif

  @if ($errors->any())
    <div class="alert error">
      <strong>Validation error:</strong>
      <ul>
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <div class="row" style="margin-top: 12px;">
    <input id="search" type="text" placeholder="Search document type..." style="max-width: 340px;" />
    <button class="btn" id="btnReload" type="button">Reload</button>
    <button class="btn primary" id="btnOpenCreate" type="button">+ Add Document Type</button>
  </div>

  {{-- JS message box --}}
  <div id="message" class="alert" style="display:none;"></div>

  <table>
    <thead>
      <tr>
        <th style="width:60px;">ID</th>
        <th>Document Type</th>
        <th style="width:110px;">Fee</th>
        <th style="width:150px;">Template</th>
        <th style="width:110px;">Status</th>
        <th style="width:220px;">Actions</th>
      </tr>
    </thead>
    <tbody id="tbody">
      <tr><td colspan="6" class="muted">Loading...</td></tr>
    </tbody>
  </table>

  <!-- Create Modal (Blade submit) -->
  <div class="backdrop" id="createModal">
    <div class="modal">
      <h3>Add Document Type</h3>

      <form method="POST" action="{{ route('admin.document-types.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="grid">
          <input class="full" name="name" placeholder="Document name (e.g., Barangay Clearance)" value="{{ old('name') }}" />

          <input name="fee" type="number" step="0.01" min="0" placeholder="Fee (e.g., 50.00)" value="{{ old('fee') }}" />

          <div class="full">
            <label class="small">Template (.docx) optional</label>
            <input type="file" name="template" accept=".docx" />
            <div class="small">You can upload later. This is the base Word file.</div>
          </div>
        </div>

        <div class="actions">
          <button class="btn" type="button" id="btnCloseCreate">Cancel</button>
          <button class="btn primary" type="submit">Save</button>
        </div>
      </form>

      <p class="small" style="margin-top:10px;">
        Create uses Blade submit so you return back with success message.
      </p>
    </div>
  </div>

  <!-- Edit Modal (Blade submit, action set dynamically) -->
  <div class="backdrop" id="editModal">
    <div class="modal">
      <h3>Edit Document Type</h3>

      <form method="POST" id="editForm" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="grid">
          <input id="e_name" class="full" name="name" placeholder="Document name" />

          <input id="e_fee" name="fee" type="number" step="0.01" min="0" placeholder="Fee" />

          <select id="e_status" name="status" class="full">
            <option value="active">status: active</option>
            <option value="inactive">inactive</option>
          </select>

          <div class="full">
            <label class="small">Replace template (.docx) optional</label>
            <input type="file" name="template" accept=".docx" />
            <div class="small">If you upload a new template, it will replace the old one.</div>
          </div>

          <div class="full small" id="e_template_info"></div>
        </div>

        <div class="actions">
          <button class="btn" type="button" id="btnCloseEdit">Cancel</button>
          <button class="btn primary" type="submit">Update</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    const tbody = document.getElementById('tbody');
    const msgBox = document.getElementById('message');

    function showMsg(obj) {
      msgBox.style.display = 'block';
      msgBox.textContent = typeof obj === 'string' ? obj : JSON.stringify(obj, null, 2);
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

    function pillStatus(status) {
      const cls = status === 'active' ? 'pill active' : 'pill inactive';
      return `<span class="${cls}">${escapeHtml(status)}</span>`;
    }

    function openModal(id) { document.getElementById(id).style.display = 'flex'; }
    function closeModal(id) { document.getElementById(id).style.display = 'none'; }

    document.getElementById('btnOpenCreate').addEventListener('click', () => openModal('createModal'));
    document.getElementById('btnCloseCreate').addEventListener('click', () => closeModal('createModal'));
    document.getElementById('btnCloseEdit').addEventListener('click', () => closeModal('editModal'));

    async function apiGet(url) {
      const res = await fetch(url, { credentials: 'same-origin', headers: { 'Accept': 'application/json' } });
      const data = await res.json().catch(() => ({}));
      return { res, data };
    }

    async function loadDocumentTypes() {
      hideMsg();
      tbody.innerHTML = `<tr><td colspan="6" class="muted">Loading...</td></tr>`;

      const q = document.getElementById('search').value.trim();
      const url = q ? `/api/v1/document-types?q=${encodeURIComponent(q)}` : `/api/v1/document-types`;

      const { res, data } = await apiGet(url);

      if (!res.ok) {
        showMsg({ status: res.status, data });
        tbody.innerHTML = `<tr><td colspan="6" class="muted">Failed to load document types</td></tr>`;
        return;
      }
      console.log(data)
      const docs = data.types || [];
      if (!docs.length) {
        tbody.innerHTML = `<tr><td colspan="6" class="muted">No document types found.</td></tr>`;
        return;
      }

      tbody.innerHTML = docs.map(d => {
        const id = d.id;
        const name = escapeHtml(d.name);
        const fee = Number(d.fee ?? 0).toFixed(2);
        const status = escapeHtml(d.status ?? 'active');

        const template = d.template_path
          ? `<span class="small">Uploaded</span>`
          : `<span class="small">None</span>`;

        const toggleLabel = status === 'active' ? 'Deactivate' : 'Activate';

        return `
          <tr>
            <td class="nowrap">${id}</td>
            <td>${name}</td>
            <td class="nowrap">${fee}</td>
            <td>${template}</td>
            <td>${pillStatus(status)}</td>
            <td class="row" style="gap:8px;">
              <button class="btn" data-action="edit" data-id="${id}">Edit</button>
              <form method="POST" action="/admin/document-types/${id}/toggle" style="display:inline;">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="_method" value="PATCH">
                <button class="btn" type="submit">${toggleLabel}</button>
              </form>
            </td>
          </tr>
        `;
      }).join('');
    }

    // Edit button handler (fetch details then open modal)
    tbody.addEventListener('click', async (e) => {
      const btn = e.target.closest('button');
      if (!btn) return;

      const action = btn.getAttribute('data-action');
      const id = btn.getAttribute('data-id');
      if (action !== 'edit') return;

      const { res, data } = await apiGet(`/api/v1/document-types/${id}`);
      if (!res.ok) return showMsg({ status: res.status, data });

      const d = data.document_type;

      // set form action
      const form = document.getElementById('editForm');
      form.action = `/admin/document-types/${id}`;

      document.getElementById('e_name').value = d.name ?? '';
      document.getElementById('e_fee').value = d.fee ?? 0;
      document.getElementById('e_status').value = d.status ?? 'active';

      const info = d.template_path
        ? `Current template: <strong>${escapeHtml(d.template_path)}</strong>`
        : `Current template: <strong>None</strong>`;
      document.getElementById('e_template_info').innerHTML = info;

      openModal('editModal');
    });

    // Search + reload
    document.getElementById('btnReload').addEventListener('click', loadDocumentTypes);
    document.getElementById('search').addEventListener('keydown', (e) => {
      if (e.key === 'Enter') loadDocumentTypes();
    });

    // Close by clicking backdrop
    document.getElementById('createModal').addEventListener('click', (e) => {
      if (e.target.id === 'createModal') closeModal('createModal');
    });
    document.getElementById('editModal').addEventListener('click', (e) => {
      if (e.target.id === 'editModal') closeModal('editModal');
    });

    // If validation errors happened, auto-open create modal
    @if ($errors->any())
      openModal('createModal');
    @endif

    loadDocumentTypes();
  </script>

</body>
</html>
