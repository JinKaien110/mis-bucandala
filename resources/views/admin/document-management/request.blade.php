<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Document Requests</title>

  <style>
    body { font-family: Arial, sans-serif; max-width: 1100px; margin: 32px auto; padding: 0 16px; }
    .row { display:flex; gap:12px; align-items:center; flex-wrap:wrap; }
    .btn { padding:10px 12px; border:1px solid #ccc; background:#fff; cursor:pointer; border-radius:8px; }
    .btn.primary { border-color:#111; }
    .btn.danger { border-color:#c00; }
    input, select, textarea { padding:10px; border:1px solid #ccc; border-radius:8px; width:100%; box-sizing:border-box; }
    table { width:100%; border-collapse: collapse; margin-top: 14px; }
    th, td { border-bottom:1px solid #eee; padding:10px; text-align:left; vertical-align: top; }
    th { background:#fafafa; }
    .muted { color:#666; font-size: 13px; }
    .small { font-size: 12px; color: #666; }
    .nowrap { white-space: nowrap; }

    .pill { padding:4px 8px; border-radius:999px; border:1px solid #ddd; font-size:12px; display:inline-block; }
    .pill.pending { border-color:#b58900; }
    .pill.approved { border-color:#0a0; }
    .pill.released { border-color:#0a0; }
    .pill.cancelled { border-color:#c00; }

    .alert { margin-top: 14px; padding: 10px; border:1px solid #eee; border-radius:8px; background:#fafafa; white-space: pre-wrap; }
    .alert.success { border-color:#bde5c8; background:#f0fff4; }
    .alert.error { border-color:#f5b5b5; background:#fff5f5; }

    .backdrop { position:fixed; inset:0; background: rgba(0,0,0,.35); display:none; align-items:center; justify-content:center; padding:16px; }
    .modal { width:100%; max-width:760px; background:#fff; border-radius:12px; border:1px solid #eee; padding:16px; }
    .modal h3 { margin:0 0 10px; }
    .grid { display:grid; gap:10px; grid-template-columns: 1fr 1fr; }
    .grid .full { grid-column: 1 / -1; }
    .actions { display:flex; gap:10px; justify-content:flex-end; margin-top:12px; }
  </style>
</head>
<body>

  <h2>Document Requests</h2>
  <p class="muted">Create requests here. Control number + fee are set automatically by the server.</p>

  @if (session('success'))
    <div class="alert success">{{ session('success') }}</div>
  @endif

  @if ($errors->any())
    <div class="alert error">
      <strong>Validation error:</strong>
      <ul style="margin:8px 0 0; padding-left:18px;">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <div class="row" style="margin-top: 12px;">
    <input id="search" type="text" placeholder="Search control no / resident / document..." style="max-width: 380px;" />
    <button class="btn" id="btnReload" type="button">Reload</button>
    <button class="btn primary" id="btnOpenCreate" type="button">+ New Request</button>
  </div>

  <div id="message" class="alert" style="display:none;"></div>

  <table>
    <thead>
      <tr>
        <th style="width:170px;">Control No</th>
        <th>Resident</th>
        <th>Document</th>
        <th style="width:90px;">Fee</th>
        <th style="width:120px;">Status</th>
        <th style="width:160px;">Created</th>
        <th style="width:120px;">Action</th>

      </tr>
    </thead>
    <tbody id="tbody">
      <tr><td colspan="6" class="muted">Loading...</td></tr>
    </tbody>
  </table>

  <!-- Create Modal (Blade submit) -->
  <div class="backdrop" id="createModal">
    <div class="modal">
      <h3>New Document Request</h3>

      <form method="POST" action="{{ route('admin.document-requests.store') }}">
        @csrf

        <div class="grid">
          <div class="full">
            <label class="small">Resident</label>
            <select name="resident_id" id="c_resident" required></select>
          </div>

          <div class="full">
            <label class="small">Document Type</label>
            <select name="document_type_id" id="c_doc_type" required></select>
          </div>

          <div>
            <label class="small">Fee (auto)</label>
            <input id="c_fee" type="text" disabled value="0.00" />
          </div>

          <div>
            <label class="small">Control No (auto)</label>
            <input type="text" disabled value="Auto generated after save" />
          </div>

          <textarea class="full" name="purpose" placeholder="Purpose (optional)" rows="2"></textarea>
          <textarea class="full" name="remarks" placeholder="Remarks (optional)" rows="2"></textarea>
        </div>

        <div class="actions">
          <button class="btn" type="button" id="btnCloseCreate">Cancel</button>
          <button class="btn primary" type="submit">Create</button>
        </div>
      </form>

      <p class="small" style="margin-top:10px;">
        Uses Blade submit so you return back with success message.
      </p>
    </div>
  </div>

  <script>
    const tbody = document.getElementById('tbody');
    const msgBox = document.getElementById('message');

    function showMsg(obj) {
      msgBox.style.display = 'block';
      msgBox.textContent = typeof obj === 'string' ? obj : JSON.stringify(obj, null, 2);
    }
    function hideMsg() { msgBox.style.display = 'none'; msgBox.textContent = ''; }

    function escapeHtml(s) {
      return String(s ?? '')
        .replaceAll('&','&amp;')
        .replaceAll('<','&lt;')
        .replaceAll('>','&gt;')
        .replaceAll('"','&quot;')
        .replaceAll("'","&#039;");
    }

    function pill(status) {
      const s = (status || 'pending').toLowerCase();
      const cls = `pill ${s}`;
      return `<span class="${cls}">${escapeHtml(s)}</span>`;
    }

    function openModal(id) { document.getElementById(id).style.display = 'flex'; }
    function closeModal(id) { document.getElementById(id).style.display = 'none'; }

    document.getElementById('btnOpenCreate').addEventListener('click', () => openModal('createModal'));
    document.getElementById('btnCloseCreate').addEventListener('click', () => closeModal('createModal'));

    document.getElementById('createModal').addEventListener('click', (e) => {
      if (e.target.id === 'createModal') closeModal('createModal');
    });

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
      cResident.innerHTML = residents.map(r => {
        const name = `${r.last_name}, ${r.first_name} ${r.middle_name ? r.middle_name : ''}`.trim();
        const address = r.address_line ? ` — ${r.address_line}` : '';
        return `<option value="${r.id}">${escapeHtml(name + address)}</option>`;
      }).join('') || `<option value="">No residents found</option>`;

      // document types
      const r2 = await apiGet('/api/v1/document-types/options');
      if (!r2.res.ok) return showMsg({ status: r2.res.status, data: r2.data });

      docTypesCache = r2.data.document_types || [];
      const cDoc = document.getElementById('c_doc_type');
      cDoc.innerHTML = docTypesCache.map(d => {
        return `<option value="${d.id}" data-fee="${d.fee ?? 0}">${escapeHtml(d.name)}</option>`;
      }).join('') || `<option value="">No active document types</option>`;

      // set initial fee
      updateFee();
    }

    function updateFee() {
      const select = document.getElementById('c_doc_type');
      const opt = select.options[select.selectedIndex];
      const fee = opt ? Number(opt.getAttribute('data-fee') || 0) : 0;
      document.getElementById('c_fee').value = fee.toFixed(2);
    }

    document.getElementById('c_doc_type').addEventListener('change', updateFee);

    async function loadRequests() {
      hideMsg();
      tbody.innerHTML = `<tr><td colspan="6" class="muted">Loading...</td></tr>`;

      const q = document.getElementById('search').value.trim();
      const url = q ? `/api/v1/document-requests?q=${encodeURIComponent(q)}` : `/api/v1/document-requests`;

      const { res, data } = await apiGet(url);
      if (!res.ok) {
        showMsg({ status: res.status, data });
        tbody.innerHTML = `<tr><td colspan="6" class="muted">Failed to load requests</td></tr>`;
        return;
      }

      const items = data.document_requests || [];
      if (!items.length) {
        tbody.innerHTML = `<tr><td colspan="6" class="muted">No requests found.</td></tr>`;
        return;
      }

      tbody.innerHTML = items.map(x => {
        const resident = x.resident
          ? `${x.resident.last_name}, ${x.resident.first_name} ${x.resident.middle_name ? x.resident.middle_name : ''}`.trim()
          : '—';

        const doc = x.document_type ? x.document_type.name : '—';
        const fee = Number(x.fee ?? (x.document_type?.fee ?? 0)).toFixed(2);
        console.log(resident)

        return `
          <tr>
            <td class="nowrap">${escapeHtml(x.control_no)}</td>
            <td>${escapeHtml(resident)}<div class="small">${escapeHtml(x.resident?.address_line || '')}</div></td>
            <td>${escapeHtml(doc)}</td>
            <td class="nowrap">${fee}</td>
            <td>${pill(x.status)}</td>
            <td class="nowrap">${escapeHtml(String(x.created_at ?? '').replace('T',' ').slice(0,19))}</td>
            <td><a class="btn" href="/admin/document-requests/${x.id}/download" target="_blank">Download</a></td>
          </tr>
        `;
      }).join('');
    }

    document.getElementById('btnReload').addEventListener('click', loadRequests);
    document.getElementById('search').addEventListener('keydown', (e) => {
      if (e.key === 'Enter') loadRequests();
    });

    // auto-open create modal if validation errors
    @if ($errors->any())
      openModal('createModal');
    @endif

    // init
    loadDropdowns();
    loadRequests();
  </script>

</body>
</html>
