<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Residents</title>

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
    .pill.verified { border-color:#0a0; }
    .pill.pending { border-color:#b58900; }
    .pill.rejected { border-color:#c00; }
    .pill.unverified { border-color:#666; }

    .alert { margin-top: 14px; padding: 10px; border:1px solid #eee; border-radius:8px; background:#fafafa; white-space: pre-wrap; }
    .alert.success { border-color:#bde5c8; background:#f0fff4; }
    .alert.error { border-color:#f5b5b5; background:#fff5f5; }
    ul { margin: 8px 0 0; padding-left: 18px; }

    .backdrop { position:fixed; inset:0; background: rgba(0,0,0,.35); display:none; align-items:center; justify-content:center; padding:16px; }
    .modal { width:100%; max-width:720px; background:#fff; border-radius:12px; border:1px solid #eee; padding:16px; }
    .modal h3 { margin:0 0 10px; }
    .grid { display:grid; gap:10px; grid-template-columns: 1fr 1fr; }
    .grid .full { grid-column: 1 / -1; }
    .actions { display:flex; gap:10px; justify-content:flex-end; margin-top:12px; }
    .muted { color:#666; font-size: 13px; }
    .small { font-size: 12px; color: #666; }
    .nowrap { white-space: nowrap; }
  </style>
</head>
<body>

  <h2>Residents</h2>
  <p class="muted">Encode residents here. Verification fields are optional for now.</p>

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
    <input id="search" type="text" placeholder="Search name/address/contact/email..." style="max-width: 340px;" />
    <button class="btn" id="btnReload" type="button">Reload</button>
    <button class="btn primary" id="btnOpenCreate" type="button">+ Add Resident</button>
  </div>

  <div id="message" class="alert" style="display:none;"></div>

  <table>
    <thead>
      <tr>
        <th style="width:60px;">ID</th>
        <th>Resident</th>
        <th>Address</th>
        <th>Contact</th>
        <th style="width:140px;">Verification</th>
        <th style="width:110px;">Status</th>
        <th style="width:90px;">Photo</th>

        <th style="width:220px;">Actions</th>
      </tr>
    </thead>
    <tbody id="tbody">
      <tr><td colspan="7" class="muted">Loading...</td></tr>
    </tbody>
  </table>

  <!-- Create Modal (Blade submit) -->
  <div class="backdrop" id="createModal">
    <div class="modal">
      <h3>Add Resident</h3>

      <form method="POST" action="{{ route('admin.residents.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="grid">
          <input name="first_name" placeholder="First name" value="{{ old('first_name') }}" />
          <input name="last_name" placeholder="Last name" value="{{ old('last_name') }}" />

          <input name="middle_name" placeholder="Middle name (optional)" value="{{ old('middle_name') }}" />
          <select name="sex">
            <option value="">Sex (optional)</option>
            <option value="male" {{ old('sex')==='male' ? 'selected' : '' }}>male</option>
            <option value="female" {{ old('sex')==='female' ? 'selected' : '' }}>female</option>
          </select>

          <input class="full" name="birth_date" type="date" value="{{ old('birth_date') }}" />

          <input class="full" name="address_line" placeholder="Address line (Block/Lot/Street/Purok)" value="{{ old('address_line') }}" />

          <input name="contact_no" placeholder="Contact no (optional)" value="{{ old('contact_no') }}" />
          <input name="email" placeholder="Email (optional)" value="{{ old('email') }}" />

          <select name="civil_status">
            <option value="" >Civil status (optional)</option>
            <option value="single">Single</option>
            <option value="married">Married</option>
            <option value="widowed">Widowed</option>
            <option value="separated">Separated</option>
            <option value="divorced">Divorced</option>
            </select>

          <input name="occupation" placeholder="Occupation (optional)" value="{{ old('occupation') }}" />

          <select name="verification_status" class="full">
            <option value="unverified" selected>Verification_status: unverified</option>
            <option value="pending">Pending</option>
            <option value="verified">Verified</option>
            <option value="rejected">Rejected</option>
          </select>

          <input class="full" type="file" name="photo" accept="image/*" />

         <input name="verification_id" placeholder="Verification ID No. (optional)" value="{{ old('verification_id') }}" />

          

          <select name="verification_type">
            <option value="">Verification type (optional)</option>
            <option value="barangay_id">Barangay ID</option>
            <option value="national_id">National ID</option>
            <option value="passport">Passport</option>
            <option value="drivers_license">Drivers License</option>
          </select>
        </div>

        <div class="actions">
          <button class="btn" type="button" id="btnCloseCreate">Cancel</button>
          <button class="btn primary" type="submit">Save</button>
        </div>
      </form>

      <p class="small" style="margin-top:10px;">
        This uses Blade submit so you return back with success message.
      </p>
    </div>
  </div>

  <!-- Edit Modal (Blade submit, set action dynamically) -->
  <div class="backdrop" id="editModal">
    <div class="modal">
      <h3>Edit Resident</h3>

      <form method="POST" id="editForm" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="grid">
          <input id="e_first_name" name="first_name" placeholder="First name" />
          <input id="e_last_name" name="last_name" placeholder="Last name" />

          <input id="e_middle_name" name="middle_name" placeholder="Middle name (optional)" />
          <select id="e_sex" name="sex">
            <option value="">Sex (optional)</option>
            <option value="male">Male</option>
            <option value="female">Female</option>
          </select>

          <input class="full" id="e_birth_date" name="birth_date" type="date" />

          <input class="full" id="e_address_line" name="address_line" placeholder="Address line" />

          <input id="e_contact_no" name="contact_no" placeholder="Contact no" />
          <input id="e_email" name="email" placeholder="Email" />

          <select id="e_civil_status" name="civil_status" >
  <<option value="single">Single</option>
            <option value="married">Married</option>
            <option value="widowed">Widowed</option>
            <option value="separated">Separated</option>
            <option value="divorced">Divorced</option>
</select>
          <input id="e_occupation" name="occupation" placeholder="Occupation" />

          <select id="e_verification_status" name="verification_status" class="full">
            <option value="unverified">Unverified</option>
            <option value="pending">Pending</option>
            <option value="verified">Verified</option>
            <option value="rejected">Rejected</option>
          </select>

        

          <input id="e_verification_id" name="verification_id" placeholder="Verification ID no" />
          

          <select id="e_verification_type" name="verification_type">
            <option value="">Verification type (optional)</option>
            <option value="barangay_id">Barangay ID</option>
            <option value="national_id">National ID</option>
            <option value="passport">Passport</option>
            <option value="drivers_license">Drivers License</option>
          </select>

          <select id="e_status" name="status" class="full">
            <option value="active">Status: Active</option>
            <option value="inactive">Status: Inactive</option>
          </select>
            <input class="full" type="file" name="photo" accept="image/*" />
<p class="small">If you upload a new photo, it will replace the current one.</p>
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
    const csrf = () => document.querySelector('meta[name="csrf-token"]').content;

    function showMsg(obj) {
      msgBox.style.display = 'block';
      msgBox.textContent = typeof obj === 'string' ? obj : JSON.stringify(obj, null, 2);
    }
    function hideMsg() {
      msgBox.style.display = 'none';
      msgBox.textContent = '';
    }

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

      const data = await res.json().catch(() => ({}));
      return { res, data };
    }

    function escapeHtml(s) {
      return String(s)
        .replaceAll('&','&amp;')
        .replaceAll('<','&lt;')
        .replaceAll('>','&gt;')
        .replaceAll('"','&quot;')
        .replaceAll("'","&#039;");
    }

    function pillStatus(status) {
      const cls = status === 'active' ? 'pill active' : 'pill inactive';
      return `<span class="${cls}">${status}</span>`;
    }

    function pillVerify(v) {
      const map = { verified:'verified', pending:'pending', rejected:'rejected', unverified:'unverified' };
      const cls = `pill ${map[v] || 'unverified'}`;
      return `<span class="${cls}">${v || 'unverified'}</span>`;
    }

    function openModal(id) { document.getElementById(id).style.display = 'flex'; }
    function closeModal(id) { document.getElementById(id).style.display = 'none'; }

    document.getElementById('btnOpenCreate').addEventListener('click', () => openModal('createModal'));
    document.getElementById('btnCloseCreate').addEventListener('click', () => closeModal('createModal'));
    document.getElementById('btnCloseEdit').addEventListener('click', () => closeModal('editModal'));

    async function loadResidents() {
      hideMsg();
      tbody.innerHTML = `<tr><td colspan="7" class="muted">Loading...</td></tr>`;

      const q = document.getElementById('search').value.trim();
      const url = q ? `/api/v1/residents?q=${encodeURIComponent(q)}` : `/api/v1/residents`;

      const { res, data } = await api(url);

      if (!res.ok) {
        showMsg({ status: res.status, data });
        tbody.innerHTML = `<tr><td colspan="7" class="muted">Failed to load residents</td></tr>`;
        return;
      }

      const residents = data.residents || [];
      if (!residents.length) {
        tbody.innerHTML = `<tr><td colspan="7" class="muted">No residents found.</td></tr>`;
        return;
      }

      tbody.innerHTML = residents.map(r => {
        const id = r.id;
        const name = `${escapeHtml(r.last_name)}, ${escapeHtml(r.first_name)} ${r.middle_name ? escapeHtml(r.middle_name) : ''}`.trim();
        const address = escapeHtml(r.address_line || '');
        const contact = `${escapeHtml(r.contact_no || '')}${r.email ? `<div class="small">${escapeHtml(r.email)}</div>` : ''}`;
        const ver = pillVerify(r.verification_status);
        const st = pillStatus(r.status);
        const photo = r.photo_path
  ? `<img src="/storage/${escapeHtml(r.photo_path)}" style="width:48px;height:48px;object-fit:cover;border-radius:8px;border:1px solid #eee;" />`
  : `<span class="small">None</span>`;


        const toggleLabel = r.status === 'active' ? 'Deactivate' : 'Activate';

        return `
          <tr>
            <td class="nowrap">${id}</td>
            <td>${name}</td>
            <td>${address}</td>
            <td>${contact}</td>
            <td>${ver}</td>
            
            <td>${st}</td>
            <td>${photo}</td>
            <td class="row" style="gap:8px;">
              <button class="btn" data-action="edit" data-id="${id}">Edit</button>
              <button class="btn" data-action="toggle" data-id="${id}">${toggleLabel}</button>
            </td>
          </tr>
        `;
      }).join('');
    }

    tbody.addEventListener('click', async (e) => {
      const btn = e.target.closest('button');
      if (!btn) return;

      const action = btn.getAttribute('data-action');
      const id = btn.getAttribute('data-id');

      if (action === 'toggle') {
        btn.disabled = true;
        const { res, data } = await api(`/api/v1/residents/${id}/toggle-status`, { method: 'PATCH' });
        btn.disabled = false;

        if (!res.ok) return showMsg({ status: res.status, data });

        showMsg(data);
        await loadResidents();
      }

      if (action === 'edit') {
        
        const { res, data } = await api(`/api/v1/residents/${id}`);
        if (!res.ok) return showMsg({ status: res.status, data });

        const r = data.resident;

        
        const form = document.getElementById('editForm');
        form.action = `/admin/residents/${id}`;

        document.getElementById('e_first_name').value = r.first_name ?? '';
        document.getElementById('e_middle_name').value = r.middle_name ?? '';
        document.getElementById('e_last_name').value = r.last_name ?? '';
        document.getElementById('e_sex').value = r.sex ?? '';

        document.getElementById('e_birth_date').value = r.birth_date ? String(r.birth_date).slice(0,10) : '';
        document.getElementById('e_address_line').value = r.address_line ?? '';

        document.getElementById('e_contact_no').value = r.contact_no ?? '';
        document.getElementById('e_email').value = r.email ?? '';
        document.getElementById('e_civil_status').value = r.civil_status ?? '';
        document.getElementById('e_occupation').value = r.occupation ?? '';


        document.getElementById('e_verification_status').value = r.verification_status ?? 'unverified';
        document.getElementById('e_verification_id').value = r.verification_id ?? '';
        document.getElementById('e_verification_type').value = r.verification_type ?? '';

        document.getElementById('e_status').value = r.status ?? 'active';

        openModal('editModal');
      }
    });

    document.getElementById('btnReload').addEventListener('click', loadResidents);
    document.getElementById('search').addEventListener('keydown', (e) => {
      if (e.key === 'Enter') loadResidents();
    });

    document.getElementById('createModal').addEventListener('click', (e) => {
      if (e.target.id === 'createModal') closeModal('createModal');
    });
    document.getElementById('editModal').addEventListener('click', (e) => {
      if (e.target.id === 'editModal') closeModal('editModal');
    });

    // if validation errors, re-open create modal automatically
    @if ($errors->any())
      openModal('createModal');
    @endif

    loadResidents();
  </script>

</body>
</html>
