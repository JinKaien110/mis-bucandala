<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>User Management</title>

  <style>
    body { font-family: Arial, sans-serif; max-width: 980px; margin: 32px auto; padding: 0 16px; }
    .row { display:flex; gap:12px; align-items:center; flex-wrap:wrap; }
    .btn { padding:10px 12px; border:1px solid #ccc; background:#fff; cursor:pointer; border-radius:8px; }
    .btn.primary { border-color:#111; }
    .btn.danger { border-color:#c00; }
    .btn:disabled { opacity:.6; cursor:not-allowed; }
    input, select { padding:10px; border:1px solid #ccc; border-radius:8px; width:100%; box-sizing:border-box; }
    table { width:100%; border-collapse: collapse; margin-top: 14px; }
    th, td { border-bottom:1px solid #eee; padding:10px; text-align:left; }
    th { background:#fafafa; }
    .pill { padding:4px 8px; border-radius:999px; border:1px solid #ddd; font-size:12px; display:inline-block; }
    .pill.active { border-color:#0a0; }
    .pill.inactive { border-color:#c00; }

    .alert { margin-top: 14px; padding: 10px; border:1px solid #eee; border-radius:8px; background:#fafafa; white-space: pre-wrap; }
    .alert.success { border-color:#bde5c8; background:#f0fff4; }
    .alert.error { border-color:#f5b5b5; background:#fff5f5; }
    ul { margin: 8px 0 0; padding-left: 18px; }

    /* Modal */
    .backdrop { position:fixed; inset:0; background: rgba(0,0,0,.35); display:none; align-items:center; justify-content:center; padding:16px; }
    .modal { width:100%; max-width:520px; background:#fff; border-radius:12px; border:1px solid #eee; padding:16px; }
    .modal h3 { margin:0 0 10px; }
    .modal .grid { display:grid; gap:10px; }
    .modal .actions { display:flex; gap:10px; justify-content:flex-end; margin-top:12px; }
    .muted { color:#666; font-size: 13px; }
  </style>
</head>
<body>

  <h2>User Management</h2>
  <p class="muted">Admin creates Staff accounts here (this replaces public registration).</p>

  {{-- Laravel flash messages (create user result) --}}
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
    <input id="search" type="text" placeholder="Search name/email..." style="max-width: 260px;" />
    <button class="btn" id="btnReload" type="button">Reload</button>
    <button class="btn primary" id="btnOpenCreate" type="button">+ Create User</button>
  </div>

  {{-- JS message box (for toggle/reset responses) --}}
  <div id="message" class="alert" style="display:none;"></div>

  <table>
    <thead>
      <tr>
        <th style="width:60px;">ID</th>
        <th>Name</th>
        <th>Email</th>
        <th style="width:100px;">Role</th>
        <th style="width:120px;">Status</th>
        <th style="width:280px;">Actions</th>
      </tr>
    </thead>
    <tbody id="tbody">
      <tr><td colspan="6" class="muted">Loading...</td></tr>
    </tbody>
  </table>

  <!-- Create User Modal (Blade Form POST ✅) -->
  <div class="backdrop" id="createModal">
    <div class="modal">
      <h3>Create User</h3>

      <form method="POST" action="/api/v1/users" id="createForm">

        @csrf

        <div class="grid">
          <input name="name" placeholder="Full name" value="{{ old('name') }}" />
          <input name="email" placeholder="Email" type="email" value="{{ old('email') }}" />
          <input name="password" placeholder="Temporary password (min 8)" type="password" />
          <select name="role">
            <option value="staff" {{ old('role') === 'staff' ? 'selected' : '' }}>staff</option>
            <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>admin</option>
          </select>
        </div>

        <div class="actions">
          <button class="btn" type="button" id="btnCloseCreate">Cancel</button>
          <button class="btn primary" type="submit">Create</button>
        </div>
      </form>

      <p class="muted" style="margin-top:10px;">
        Note: This uses Laravel form submit (no fetch), so CSRF is automatic.
      </p>
    </div>
  </div>

  <!-- Reset Password Modal (fetch stays ✅) -->
  <div class="backdrop" id="resetModal">
    <div class="modal">
      <h3>Reset Password</h3>
      <p class="muted" id="resetTarget"></p>
      <div class="grid">
        <input id="r_password" placeholder="New password (min 8)" type="password" />
      </div>
      <div class="actions">
        <button class="btn" type="button" id="btnCloseReset">Cancel</button>
        <button class="btn danger" type="button" id="btnReset">Reset</button>
      </div>
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

      // keep this (you already set middleware to return token header)
      const newToken = res.headers.get('x-csrf-token');
      if (newToken) {
        document.querySelector('meta[name="csrf-token"]').setAttribute('content', newToken);
      }

      const data = await res.json().catch(() => ({}));
      return { res, data };
    }

    function pill(status) {
      const cls = status === 'active' ? 'pill active' : 'pill inactive';
      return `<span class="${cls}">${status}</span>`;
    }

    function escapeHtml(s) {
      return String(s)
        .replaceAll('&','&amp;')
        .replaceAll('<','&lt;')
        .replaceAll('>','&gt;')
        .replaceAll('"','&quot;')
        .replaceAll("'","&#039;");
    }

    let resetUserId = null;

    async function loadUsers() {
      hideMsg();
      tbody.innerHTML = `<tr><td colspan="6" class="muted">Loading...</td></tr>`;

      const q = document.getElementById('search').value.trim();
      const url = q ? `/api/v1/users?q=${encodeURIComponent(q)}` : `/api/v1/users`;

      const { res, data } = await api(url);

      if (!res.ok) {
        showMsg({ status: res.status, data });
        tbody.innerHTML = `<tr><td colspan="6" class="muted">Failed to load users</td></tr>`;
        return;
      }

      const users = data.users || [];
      if (!users.length) {
        tbody.innerHTML = `<tr><td colspan="6" class="muted">No users found.</td></tr>`;
        return;
      }

      tbody.innerHTML = users.map(u => {
        const id = u.id;
        const name = escapeHtml(u.name);
        const email = escapeHtml(u.email);
        const role = escapeHtml(u.role);
        const status = escapeHtml(u.status);
        const toggleLabel = status === 'active' ? 'Deactivate' : 'Activate';

        return `
          <tr>
            <td>${id}</td>
            <td>${name}</td>
            <td>${email}</td>
            <td>${role}</td>
            <td>${pill(status)}</td>
            <td class="row" style="gap:8px;">
              <button class="btn" data-action="toggle" data-id="${id}">${toggleLabel}</button>
              <button class="btn danger" data-action="reset" data-id="${id}" data-label="${name} (${email})">Reset Password</button>
            </td>
          </tr>
        `;
      }).join('');
    }

    // actions
    tbody.addEventListener('click', async (e) => {
      const btn = e.target.closest('button');
      if (!btn) return;

      const action = btn.getAttribute('data-action');
      const id = btn.getAttribute('data-id');

      if (action === 'toggle') {
        btn.disabled = true;
        const { res, data } = await api(`/api/v1/users/${id}/toggle-status`, { method: 'PATCH' });
        btn.disabled = false;

        if (!res.ok) return showMsg({ status: res.status, data });

        showMsg(data);
        await loadUsers();
      }

      if (action === 'reset') {
        resetUserId = id;
        document.getElementById('resetTarget').textContent = btn.getAttribute('data-label') || '';
        document.getElementById('r_password').value = '';
        openModal('resetModal');
      }
    });

    // modal helpers
    function openModal(id) { document.getElementById(id).style.display = 'flex'; }
    function closeModal(id) { document.getElementById(id).style.display = 'none'; }

    document.getElementById('btnOpenCreate').addEventListener('click', () => openModal('createModal'));
    document.getElementById('btnCloseCreate').addEventListener('click', () => closeModal('createModal'));

    document.getElementById('btnCloseReset').addEventListener('click', () => closeModal('resetModal'));

    document.getElementById('btnReset').addEventListener('click', async () => {
      const newPass = document.getElementById('r_password').value;

      const { res, data } = await api(`/api/v1/users/${resetUserId}/reset-password`, {
        method: 'PATCH',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ password: newPass })
      });

      if (!res.ok) return showMsg({ status: res.status, data });

      closeModal('resetModal');
      showMsg(data);
      await loadUsers();
    });

    document.getElementById('btnReload').addEventListener('click', loadUsers);
    document.getElementById('search').addEventListener('keydown', (e) => {
      if (e.key === 'Enter') loadUsers();
    });

    // close by backdrop click
    document.getElementById('createModal').addEventListener('click', (e) => {
      if (e.target.id === 'createModal') closeModal('createModal');
    });
    document.getElementById('resetModal').addEventListener('click', (e) => {
      if (e.target.id === 'resetModal') closeModal('resetModal');
    });

    // If validation errors happened, auto-open create modal
    @if ($errors->any())
      openModal('createModal');
    @endif

    loadUsers();
  </script>
</body>
</html>
