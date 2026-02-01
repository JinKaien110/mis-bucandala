{{-- resources/views/admin/blotters/index.blade.php --}}
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Blotter Records</title>

  <style>
    body { font-family: Arial, sans-serif; max-width: 1100px; margin: 32px auto; padding: 0 16px; }
    .row { display:flex; gap:12px; align-items:center; flex-wrap:wrap; }
    .btn { padding:10px 12px; border:1px solid #ccc; background:#fff; cursor:pointer; border-radius:8px; }
    .btn.primary { border-color:#111; }
    .btn.danger { border-color:#c00; }
    .btn:disabled { opacity:.6; cursor:not-allowed; }
    input, select, textarea { padding:10px; border:1px solid #ccc; border-radius:8px; width:100%; box-sizing:border-box; }
    textarea { min-height: 110px; resize: vertical; }
    table { width:100%; border-collapse: collapse; margin-top: 14px; }
    th, td { border-bottom:1px solid #eee; padding:10px; text-align:left; vertical-align: top; }
    th { background:#fafafa; }
    .pill { padding:4px 8px; border-radius:999px; border:1px solid #ddd; font-size:12px; display:inline-block; }
    .pill.filed { border-color:#999; }
    .pill.ongoing { border-color:#0a7; }
    .pill.settled { border-color:#06c; }
    .pill.archived { border-color:#555; }
    .pill.cancelled { border-color:#c00; }
    .muted { color:#666; font-size: 13px; }
    .small { font-size: 12px; color: #666; }
    .nowrap { white-space: nowrap; }

    .alert { margin-top: 14px; padding: 10px; border:1px solid #eee; border-radius:8px; background:#fafafa; white-space: pre-wrap; }
    .alert.success { border-color:#bde5c8; background:#f0fff4; }
    .alert.error { border-color:#f5b5b5; background:#fff5f5; }
    ul { margin: 8px 0 0; padding-left: 18px; }

    /* Modal */
    .backdrop { position:fixed; inset:0; background: rgba(0,0,0,.35); display:none; align-items:center; justify-content:center; padding:16px; }
    .modal { width:100%; max-width:820px; background:#fff; border-radius:12px; border:1px solid #eee; padding:16px; }
    .modal h3 { margin:0 0 10px; }
    .grid { display:grid; gap:10px; grid-template-columns: 1fr 1fr; }
    .grid .full { grid-column: 1 / -1; }
    .actions { display:flex; gap:10px; justify-content:flex-end; margin-top:12px; }
    .topline { display:flex; justify-content:space-between; align-items:baseline; gap:10px; flex-wrap:wrap; }
  </style>
</head>
<body>

  <div class="topline">
    <div>
      <h2>Blotter Records</h2>
      <p class="muted">Record incidents (filed/ongoing/settled/archived). For now, name fields are manual. Resident linking can be added later.</p>
    </div>
  </div>

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
    <input id="search" type="text" placeholder="Search blotter no / type / name / location..." style="max-width: 360px;" />
    <select id="status" style="max-width: 220px;">
      <option value="">All Status</option>
      <option value="filed">filed</option>
      <option value="ongoing">ongoing</option>
      <option value="settled">settled</option>
      <option value="archived">archived</option>
      <option value="cancelled">cancelled</option>
    </select>
    <input id="date_from" type="date" style="max-width: 190px;" />
    <input id="date_to" type="date" style="max-width: 190px;" />

    <button class="btn" id="btnReload" type="button">Reload</button>
    <button class="btn primary" id="btnOpenCreate" type="button">+ Add Blotter</button>
  </div>

  {{-- JS message box --}}
  <div id="message" class="alert" style="display:none;"></div>

  <table>
    <thead>
      <tr>
        <th style="width:80px;">ID</th>
        <th style="width:170px;">Blotter No</th>
        <th>Incident</th>
        <th style="width:130px;">Status</th>
        <th style="width:240px;">Actions</th>
      </tr>
    </thead>
    <tbody id="tbody">
      <tr><td colspan="5" class="muted">Loading...</td></tr>
    </tbody>
  </table>

  <!-- Create Modal -->
  <div class="backdrop" id="createModal">
    <div class="modal">
      <h3>Add Blotter</h3>

      <div class="grid">
        <div class="full">
          <label class="small">Incident date & time</label>
          <input id="c_incident_date" type="datetime-local" />
        </div>

        <div>
          <label class="small">Incident type</label>
          <input id="c_incident_type" placeholder="e.g., Threat / Theft / Assault" />
        </div>

        <div>
          <label class="small">Location</label>
          <input id="c_incident_location" placeholder="e.g., Purok 2, near chapel" />
        </div>

        <div class="grid">
  {{-- COMPLAINANT --}}
  <div class="full">
    <label class="small">Complainant (Resident or Outsider)</label>
    <div class="row">
      <select id="c_complainant_resident_id" style="max-width: 420px;">
        <option value="">— Select resident —</option>
        <option value="__outsider__">Outsider / Not listed</option>
      </select>
      <input id="c_complainant_name" placeholder="If outsider, type full name here" style="max-width: 520px;" disabled />
    </div>
    <div class="small">Choose a resident OR select “Outsider” then type name.</div>
  </div>

         {{-- RESPONDENT --}}
  <div class="full">
    <label class="small">Respondent (Resident or Outsider)</label>
    <div class="row">
      <select id="c_respondent_resident_id" style="max-width: 420px;">
        <option value="">— Select resident —</option>
        <option value="__outsider__">Outsider / Not listed</option>
      </select>
      <input id="c_respondent_name" placeholder="If outsider, type full name here" style="max-width: 520px;" disabled />
    </div>
    <div class="small">Choose a resident OR select “Outsider” then type name.</div>
  </div>

        <div>
    <label class="small">Complainant contact (optional)</label>
    <input id="c_complainant_contact" placeholder="09xxxxxxxxx" />
  </div>

         <div>
    <label class="small">Respondent contact (optional)</label>
    <input id="c_respondent_contact" placeholder="09xxxxxxxxx" />
  </div>


        <div class="full">
          <label class="small">Narrative</label>
          <textarea id="c_narrative" placeholder="Write incident details here..."></textarea>
        </div>

        <div class="full">
          <label class="small">Remarks (optional)</label>
          <textarea id="c_remarks" placeholder="Optional notes..."></textarea>
        </div>
      </div>

      <div class="actions">
        <button class="btn" type="button" id="btnCloseCreate">Cancel</button>
        <button class="btn primary" type="button" id="btnCreateSave">Save</button>
      </div>

      <p class="small" style="margin-top:10px;">
        Create uses fetch() to API. Your cookies auth will work as long as you're logged-in in the same browser.
      </p>
    </div>
  </div>

  <!-- Edit Modal -->
  <div class="backdrop" id="editModal">
    <div class="modal">
      <h3>Edit Blotter</h3>

      <div class="grid">
        <div class="full">
          <div class="row" style="justify-content:space-between;">
            <div class="small" id="e_meta"></div>
            <div class="row" style="gap:8px;">
              <select id="e_status" style="max-width: 210px;">
                <option value="filed">status: filed</option>
                <option value="ongoing">ongoing</option>
                <option value="settled">settled</option>
                <option value="archived">archived</option>
                <option value="cancelled">cancelled</option>
              </select>
              <button class="btn" id="btnSaveStatus" type="button">Update status</button>
            </div>
          </div>
        </div>

        <div class="full">
          <label class="small">Incident date & time</label>
          <input id="e_incident_date" type="datetime-local" />
        </div>

        <div>
          <label class="small">Incident type</label>
          <input id="e_incident_type" />
        </div>

        <div>
          <label class="small">Location</label>
          <input id="e_incident_location" />
        </div>

        <div class="full">
          <label class="small">Complainant</label>
          <input id="e_complainant_name" />
        </div>

        <div class="full">
          <label class="small">Respondent</label>
          <input id="e_respondent_name" />
        </div>

        <div>
          <label class="small">Complainant contact (optional)</label>
          <input id="e_complainant_contact" />
        </div>

        <div>
          <label class="small">Respondent contact (optional)</label>
          <input id="e_respondent_contact" />
        </div>

        <div class="full">
          <label class="small">Narrative</label>
          <textarea id="e_narrative"></textarea>
        </div>

        <div class="full">
          <label class="small">Remarks (optional)</label>
          <textarea id="e_remarks"></textarea>
        </div>
      </div>

      <div class="actions">
        <button class="btn" type="button" id="btnCloseEdit">Cancel</button>
        <button class="btn primary" type="button" id="btnEditSave">Update</button>
      </div>
    </div>
  </div>

  <script>
    const tbody = document.getElementById('tbody');
    const msgBox = document.getElementById('message');
    const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    let editId = null;
    let editBlotterNo = '';

    function showMsg(obj, type = '') {
      msgBox.className = 'alert' + (type ? ` ${type}` : '');
      msgBox.style.display = 'block';
      msgBox.textContent = typeof obj === 'string' ? obj : JSON.stringify(obj, null, 2);
    }
    function hideMsg() {
      msgBox.style.display = 'none';
      msgBox.textContent = '';
      msgBox.className = 'alert';
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
      const st = (status ?? 'filed').toLowerCase();
      const cls = `pill ${st}`;
      return `<span class="${cls}">${escapeHtml(st)}</span>`;
    }

    function openModal(id) { document.getElementById(id).style.display = 'flex'; }
    function closeModal(id) { document.getElementById(id).style.display = 'none'; }

    document.getElementById('btnOpenCreate').addEventListener('click', () => openModal('createModal'));
    document.getElementById('btnCloseCreate').addEventListener('click', () => closeModal('createModal'));
    document.getElementById('btnCloseEdit').addEventListener('click', () => closeModal('editModal'));

    document.getElementById('createModal').addEventListener('click', (e) => {
      if (e.target.id === 'createModal') closeModal('createModal');
    });
    document.getElementById('editModal').addEventListener('click', (e) => {
      if (e.target.id === 'editModal') closeModal('editModal');
    });

    async function apiGet(url) {
      const res = await fetch(url, { credentials: 'same-origin', headers: { 'Accept': 'application/json' } });
      const data = await res.json().catch(() => ({}));
      return { res, data };
    }

    async function apiSend(url, method, payload) {
      const res = await fetch(url, {
        method,
        credentials: 'same-origin',
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrf,
        },
        body: payload ? JSON.stringify(payload) : null
      });

      const data = await res.json().catch(() => ({}));
      return { res, data };
    }

    function fmtIncident(d) {
      if (!d) return '';
      try { return new Date(d).toLocaleString(); } catch { return d; }
    }

    async function loadBlotters() {
      hideMsg();
      tbody.innerHTML = `<tr><td colspan="5" class="muted">Loading...</td></tr>`;

      const q = document.getElementById('search').value.trim();
      const status = document.getElementById('status').value;
      const df = document.getElementById('date_from').value;
      const dt = document.getElementById('date_to').value;

      const params = new URLSearchParams();
      if (q) params.set('search', q);
      if (status) params.set('status', status);
      if (df) params.set('date_from', df);
      if (dt) params.set('date_to', dt);

      const url = params.toString() ? `/api/v1/blotters?${params.toString()}` : `/api/v1/blotters`;
      const { res, data } = await apiGet(url);

      if (!res.ok) {
        showMsg({ status: res.status, data }, 'error');
        tbody.innerHTML = `<tr><td colspan="5" class="muted">Failed to load blotters</td></tr>`;
        return;
      }

      const items = data.data ?? data.blotters ?? data.items ?? [];
      if (!items.length) {
        tbody.innerHTML = `<tr><td colspan="5" class="muted">No blotter records found.</td></tr>`;
        return;
      }

      tbody.innerHTML = items.map(b => {
        const id = b.id;
        const blotterNo = escapeHtml(b.blotter_no ?? '');
        const incident = `
          <div><strong>${escapeHtml(b.incident_type ?? '')}</strong></div>
          <div class="small">${escapeHtml(b.incident_location ?? '')}</div>
          <div class="small">${escapeHtml(fmtIncident(b.incident_date))}</div>
          <div class="small">C: ${escapeHtml(b.complainant_name ?? '-')} • R: ${escapeHtml(b.respondent_name ?? '-')}</div>
        `;

        return `
          <tr>
            <td class="nowrap">${id}</td>
            <td class="nowrap">${blotterNo}</td>
            <td>${incident}</td>
            <td>${pillStatus(b.status)}</td>
            <td class="row" style="gap:8px;">
              <button class="btn" data-action="edit" data-id="${id}">Edit</button>
              <button class="btn" data-action="status" data-id="${id}" data-status="ongoing">Ongoing</button>
              <button class="btn" data-action="status" data-id="${id}" data-status="settled">Settled</button>
              <button class="btn" data-action="status" data-id="${id}" data-status="archived">Archive</button>
              <button class="btn danger" data-action="status" data-id="${id}" data-status="cancelled">Cancel</button>
            </td>
          </tr>
        `;
      }).join('');
    }

    function renderResidentOptions(residents) {
    // expects [{id, name}] - adjust if your API is different
    return residents.map(r => `<option value="${r.id}">${escapeHtml(r.name)}</option>`).join('');
  }

  async function loadResidentOptions() {
    // uses your existing endpoint from DocumentRequestController
    const { res, data } = await apiGet('/api/v1/residents/options');
    if (!res.ok) {
      showMsg({ status: res.status, data }, 'error');
      return [];
    }

    // Adjust this based on your actual response shape
    const residents = data.residents || data.options || data || [];
    

    const opts = renderResidentOptions(residents);
console.log(opts)
    // Create modal selects
    document.getElementById('c_complainant_resident_id').insertAdjacentHTML('beforeend', opts);
    document.getElementById('c_respondent_resident_id').insertAdjacentHTML('beforeend', opts);

    // Edit modal selects (if you have edit modal equivalents)
    const eC = document.getElementById('e_complainant_resident_id');
    const eR = document.getElementById('e_respondent_resident_id');
    if (eC && eR) {
      eC.insertAdjacentHTML('beforeend', opts);
      eR.insertAdjacentHTML('beforeend', opts);
    }

    return residents;
  }

  function setupResidentToggle(selectId, inputId) {
    const sel = document.getElementById(selectId);
    const inp = document.getElementById(inputId);

    const apply = () => {
      const v = sel.value;
      if (v === '__outsider__') {
        inp.disabled = false;
        inp.focus();
      } else {
        // if they selected a resident (or blank), disable typing
        inp.value = '';
        inp.disabled = true;
      }
    };

    sel.addEventListener('change', apply);
    apply();
  }

  // Update your payload builders so IDs are only sent if a resident was chosen:
  function getResidentSelection(selectId, inputId) {
    const selVal = document.getElementById(selectId).value;

    if (!selVal) {
      // nothing chosen: treat as outsider manual name (if typed)
      return { resident_id: null, name: document.getElementById(inputId).value.trim() || null };
    }

    if (selVal === '__outsider__') {
      return { resident_id: null, name: document.getElementById(inputId).value.trim() || null };
    }

    return { resident_id: Number(selVal), name: null };
  }

    function dtLocalValue(dateStr) {
      if (!dateStr) return '';
      const d = new Date(dateStr);
      if (isNaN(d)) return '';
      const pad = (n) => String(n).padStart(2, '0');
      return `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`;
    }

    function createPayload(prefix) {
    const v = (id) => document.getElementById(prefix + id).value;

    const comp = getResidentSelection(prefix + '_complainant_resident_id', prefix + '_complainant_name');
    const resp = getResidentSelection(prefix + '_respondent_resident_id', prefix + '_respondent_name');

    return {
      incident_date: v('_incident_date'),
      incident_type: v('_incident_type').trim(),
      incident_location: v('_incident_location').trim(),
      narrative: v('_narrative').trim(),
      remarks: v('_remarks').trim() || null,

      // complainant
      complainant_resident_id: comp.resident_id,
      complainant_name: comp.name,
      complainant_contact: v('_complainant_contact').trim() || null,

      // respondent
      respondent_resident_id: resp.resident_id,
      respondent_name: resp.name,
      respondent_contact: v('_respondent_contact').trim() || null,
    };
  }

    function clearCreate() {
      ['incident_date','incident_type','incident_location','complainant_name','respondent_name','complainant_contact','respondent_contact','narrative','remarks']
        .forEach(k => document.getElementById('c_' + k).value = '');
    }

    async function createBlotter() {
      hideMsg();
      const payload = createPayload('c');

      const { res, data } = await apiSend('/api/v1/blotters', 'POST', payload);
      if (!res.ok) {
        showMsg({ status: res.status, data }, 'error');
        return;
      }

      closeModal('createModal');
      clearCreate();
      await loadBlotters();
      showMsg('Blotter created successfully.', 'success');
    }

    async function openEdit(id) {
      hideMsg();
      const { res, data } = await apiGet(`/api/v1/blotters/${id}`);
      if (!res.ok) {
        showMsg({ status: res.status, data }, 'error');
        return;
      }

      const b = data;
      editId = b.id;
      editBlotterNo = b.blotter_no ?? '';

      document.getElementById('e_meta').innerHTML =
        `Editing: <strong>${escapeHtml(editBlotterNo)}</strong> <span class="small">(#${editId})</span>`;

      document.getElementById('e_status').value = (b.status ?? 'filed').toLowerCase();

      document.getElementById('e_incident_date').value = dtLocalValue(b.incident_date);
      document.getElementById('e_incident_type').value = b.incident_type ?? '';
      document.getElementById('e_incident_location').value = b.incident_location ?? '';

      document.getElementById('e_complainant_name').value = b.complainant_name ?? '';
      document.getElementById('e_respondent_name').value = b.respondent_name ?? '';
      document.getElementById('e_complainant_contact').value = b.complainant_contact ?? '';
      document.getElementById('e_respondent_contact').value = b.respondent_contact ?? '';

      document.getElementById('e_narrative').value = b.narrative ?? '';
      document.getElementById('e_remarks').value = b.remarks ?? '';

      openModal('editModal');
    }

    async function saveEdit() {
      hideMsg();
      if (!editId) return;

      const payload = createPayload('e');
      const { res, data } = await apiSend(`/api/v1/blotters/${editId}`, 'PUT', payload);

      if (!res.ok) {
        showMsg({ status: res.status, data }, 'error');
        return;
      }

      closeModal('editModal');
      await loadBlotters();
      showMsg('Blotter updated successfully.', 'success');
    }

    async function updateStatusQuick(id, status) {
      hideMsg();
      const { res, data } = await apiSend(`/api/v1/blotters/${id}/status`, 'PATCH', { status });
      if (!res.ok) {
        showMsg({ status: res.status, data }, 'error');
        return;
      }
      await loadBlotters();
      showMsg(`Status updated to "${status}".`, 'success');
    }

    async function updateStatusFromEdit() {
      hideMsg();
      if (!editId) return;
      const status = document.getElementById('e_status').value;

      const { res, data } = await apiSend(`/api/v1/blotters/${editId}/status`, 'PATCH', { status });
      if (!res.ok) {
        showMsg({ status: res.status, data }, 'error');
        return;
      }
      await loadBlotters();
      showMsg(`Status updated to "${status}".`, 'success');
    }

    // table actions (edit + quick status)
    tbody.addEventListener('click', async (e) => {
      const btn = e.target.closest('button');
      if (!btn) return;

      const action = btn.getAttribute('data-action');
      const id = btn.getAttribute('data-id');

      if (action === 'edit') return openEdit(id);
      if (action === 'status') {
        const st = btn.getAttribute('data-status');
        return updateStatusQuick(id, st);
      }
    });

    // controls
    document.getElementById('btnReload').addEventListener('click', loadBlotters);
    document.getElementById('search').addEventListener('keydown', (e) => {
      if (e.key === 'Enter') loadBlotters();
    });

    document.getElementById('btnCreateSave').addEventListener('click', createBlotter);
    document.getElementById('btnEditSave').addEventListener('click', saveEdit);
    document.getElementById('btnSaveStatus').addEventListener('click', updateStatusFromEdit);

    loadBlotters();

     // Call these once after your helpers exist:
  (async function initBlotterResidentSelects() {
    await loadResidentOptions();

    // Create modal toggles
    setupResidentToggle('c_complainant_resident_id', 'c_complainant_name');
    setupResidentToggle('c_respondent_resident_id', 'c_respondent_name');

    // If you add edit modal selects, also toggle them:
    if (document.getElementById('e_complainant_resident_id')) {
      setupResidentToggle('e_complainant_resident_id', 'e_complainant_name');
      setupResidentToggle('e_respondent_resident_id', 'e_respondent_name');
    }
  })();
  </script>

</body>
</html>
