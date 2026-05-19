@extends('layouts.admin')

@section('content')
@php
  // Use passed variable or fallback
  $role = $authRole ?? (auth()->user()->role ?? 'staff');

  $isSuper = $role === 'superadmin';
  $isAdmin = $role === 'admin';
  $canManageUsers = $isSuper || $isAdmin; // you can tighten if you want
@endphp

<div class="container py-3">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  {{-- Header --}}
  <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
    <div>
      <div class="text-muted small">SYSTEM</div>
      <h3 class="mb-1">User Management</h3>
      <div class="text-muted">
        Manage staff accounts. High-risk actions (Deactivate) are restricted to Superadmin.
      </div>
    </div>

    <div class="d-flex gap-2">
      <button class="btn btn-outline-secondary" id="btnReload" type="button">Reload</button>

      {{-- Create is allowed for Admin + Superadmin --}}
      <button class="btn btn-dark" id="btnOpenCreate" type="button" @disabled(!$canManageUsers)>
        + Create User
      </button>
    </div>
  </div>

  {{-- Flash --}}
  @if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  @if ($errors->any())
    <div class="alert alert-danger">
      <div class="fw-semibold">Validation error:</div>
      <ul class="mb-0">
        @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
      </ul>
    </div>
  @endif

  {{-- Toolbar --}}
  <div class="card shadow-sm mb-3">
    <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-2">
      <div class="d-flex gap-2 align-items-center flex-wrap">
        <div class="input-group" style="min-width: 280px;">
          <span class="input-group-text">Search</span>
          <input id="search" type="text" class="form-control" placeholder="Name or email...">
        </div>

        <span class="badge text-bg-light border">
          Role: <strong class="ms-1">{{ strtoupper($role) }}</strong>
        </span>
      </div>

      <div class="text-muted small">
        Tip: Press Enter to search.
      </div>
    </div>
  </div>

  {{-- JS message box --}}
  <div id="message" class="alert" style="display:none;"></div>

  {{-- Table --}}
  <div class="card shadow-sm">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table align-middle table-hover mb-0">
          <thead class="table-light">
            <tr>
              <th style="width:70px;">ID</th>
              <th>User</th>
              <th style="width:160px;">Role</th>
              <th style="width:140px;">Status</th>
              <th style="width:320px;">Actions</th>
            </tr>
          </thead>
          <tbody id="tbody">
            <tr><td colspan="5" class="text-muted">Loading...</td></tr>
          </tbody>
        </table>
      </div>

      <div class="text-muted small mt-2">
        Deactivate is <strong>Superadmin only</strong> to prevent lockouts/misuse.
      </div>
    </div>
  </div>

  {{-- Create User Modal --}}
  <div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content border-0 shadow">
        <div class="modal-header">
          <div>
            <div class="text-muted small">CREATE</div>
            <h5 class="modal-title mb-0">New User</h5>
          </div>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <form method="POST" action="/api/v1/users" id="createForm">
          @csrf

          <div class="modal-body">
            <div class="mb-2">
              <label class="form-label">Full name</label>
              <input name="name" class="form-control" placeholder="Juan Dela Cruz" value="{{ old('name') }}" @disabled(!$canManageUsers)>
            </div>

            <div class="mb-2">
              <label class="form-label">Email</label>
              <input name="email" class="form-control" placeholder="email@example.com" type="email" value="{{ old('email') }}" @disabled(!$canManageUsers)>
            </div>

            <div class="mb-2">
              <label class="form-label">Temporary password</label>
              <input name="password" class="form-control" placeholder="Min 8 chars" type="password" @disabled(!$canManageUsers)>
            </div>

            <div class="mb-2">
              <label class="form-label">Role</label>
              <select name="role" class="form-select" @disabled(!$canManageUsers)>
                <option value="staff" {{ old('role') === 'staff' ? 'selected' : '' }}>staff</option>

                {{-- Admin creation optionally superadmin only --}}
                @if($isSuper)
                  <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>admin</option>
                @endif
              </select>

              @if(!$isSuper)
                <div class="form-text">
                  Only Superadmin can create Admin accounts.
                </div>
              @endif
            </div>

            @if(!$canManageUsers)
              <div class="alert alert-secondary mb-0">
                You don’t have permission to create users.
              </div>
            @endif
          </div>

          <div class="modal-footer">
            <button class="btn btn-outline-secondary" type="button" data-bs-dismiss="modal">Cancel</button>
            <button class="btn btn-dark" type="submit" @disabled(!$canManageUsers)>Create</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  {{-- Reset Password Modal --}}
  <div class="modal fade" id="resetModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content border-0 shadow">
        <div class="modal-header">
          <div>
            <div class="text-muted small">SECURITY</div>
            <h5 class="modal-title mb-0">Reset Password</h5>
          </div>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <div class="text-muted small mb-2" id="resetTarget"></div>

          <label class="form-label">New password</label>
          <input id="r_password" class="form-control" placeholder="Min 8 chars" type="password" @disabled(!$canManageUsers)>

          @if(!$canManageUsers)
            <div class="alert alert-secondary mt-3 mb-0">
              You don’t have permission to reset passwords.
            </div>
          @endif
        </div>

        <div class="modal-footer">
          <button class="btn btn-outline-secondary" type="button" data-bs-dismiss="modal">Cancel</button>
          <button class="btn btn-danger" type="button" id="btnReset" @disabled(!$canManageUsers)>Reset</button>
        </div>
      </div>
    </div>
  </div>

</div>

{{-- Needs Bootstrap JS in your layout (footer) --}}
<script>
  const tbody = document.getElementById('tbody');
  const msgBox = document.getElementById('message');
  const csrf = () => document.querySelector('meta[name="csrf-token"]').content;

  const IS_SUPERADMIN = @json($isSuper);
  const CAN_MANAGE_USERS = @json($canManageUsers);

  function showMsg(obj, type='info') {
    msgBox.style.display = 'block';
    msgBox.className = 'alert ' + (type === 'success' ? 'alert-success' : (type === 'error' ? 'alert-danger' : 'alert-secondary'));
    msgBox.textContent = typeof obj === 'string' ? obj : JSON.stringify(obj, null, 2);
    msgBox.scrollIntoView({ behavior:'smooth', block:'start' });
  }
  function hideMsg(){ msgBox.style.display='none'; msgBox.textContent=''; }

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

    const newToken = res.headers.get('x-csrf-token');
    if (newToken) document.querySelector('meta[name="csrf-token"]').setAttribute('content', newToken);

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

  function badgeRole(role){
    const r = String(role || '').toLowerCase();
    const cls = r === 'superadmin' ? 'text-bg-dark'
              : r === 'admin' ? 'text-bg-warning'
              : 'text-bg-success';
    return `<span class="badge ${cls}">${escapeHtml(r.toUpperCase())}</span>`;
  }

  function badgeStatus(status){
    const s = String(status || '').toLowerCase();
    const cls = s === 'active' ? 'text-bg-success' : 'text-bg-secondary';
    return `<span class="badge ${cls}">${escapeHtml(s.toUpperCase())}</span>`;
  }

  let resetUserId = null;

  async function loadUsers() {
    hideMsg();
    tbody.innerHTML = `<tr><td colspan="5" class="text-muted">Loading...</td></tr>`;

    const q = document.getElementById('search').value.trim();
    const url = q ? `/api/v1/users?q=${encodeURIComponent(q)}` : `/api/v1/users`;

    const { res, data } = await api(url);
    if (!res.ok) {
      showMsg({ status: res.status, data }, 'error');
      tbody.innerHTML = `<tr><td colspan="5" class="text-muted">Failed to load users</td></tr>`;
      return;
    }

    const users = data.users || [];
    if (!users.length) {
      tbody.innerHTML = `<tr><td colspan="5" class="text-muted">No users found.</td></tr>`;
      return;
    }

    tbody.innerHTML = users.map(u => {
      const id = u.id;
      const name = escapeHtml(u.name || '');
      const email = escapeHtml(u.email || '');
      const role = String(u.role || '').toLowerCase();
      const status = String(u.status || '').toLowerCase();

      // RBAC: Deactivate only superadmin
      const canToggle = IS_SUPERADMIN;
      const toggleLabel = status === 'active' ? 'Deactivate' : 'Activate';

      // Optional: prevent self-deactivate (even for superadmin)
      const isSelf = !!u.is_self; // if your API returns this; else ignore

      const toggleDisabled = (!canToggle) || isSelf;

      const toggleBtn = `
        <button class="btn btn-outline-secondary btn-sm"
                data-action="toggle" data-id="${id}"
                ${toggleDisabled ? 'disabled' : ''}>
          ${toggleLabel}
        </button>`;

      const resetBtn = `
        <button class="btn btn-outline-danger btn-sm"
                data-action="reset" data-id="${id}"
                data-label="${name} (${email})"
                ${CAN_MANAGE_USERS ? '' : 'disabled'}>
          Reset Password
        </button>`;

      return `
        <tr>
          <td class="text-muted">${id}</td>
          <td>
            <div class="fw-semibold">${name}</div>
            <div class="text-muted small">${email}</div>
          </td>
          <td>${badgeRole(role)}</td>
          <td>${badgeStatus(status)}</td>
          <td>
            <div class="d-flex gap-2 flex-wrap">
              ${toggleBtn}
              ${resetBtn}
            </div>
            ${(!IS_SUPERADMIN) ? `<div class="text-muted small mt-1">Deactivate is Superadmin-only.</div>` : ``}
          </td>
        </tr>
      `;
    }).join('');
  }

  // Table actions
  tbody.addEventListener('click', async (e) => {
    const btn = e.target.closest('button');
    if (!btn) return;

    const action = btn.getAttribute('data-action');
    const id = btn.getAttribute('data-id');

    if (action === 'toggle') {
      if (!IS_SUPERADMIN) {
        return showMsg('Only Superadmin can activate/deactivate users.', 'error');
      }

      btn.disabled = true;
      const { res, data } = await api(`/api/v1/users/${id}/toggle-status`, { method: 'PATCH' });
      btn.disabled = false;

      if (!res.ok) return showMsg({ status: res.status, data }, 'error');

      showMsg(data, 'success');
      await loadUsers();
    }

    if (action === 'reset') {
      if (!CAN_MANAGE_USERS) {
        return showMsg('You don’t have permission to reset passwords.', 'error');
      }

      resetUserId = id;
      document.getElementById('resetTarget').textContent = btn.getAttribute('data-label') || '';
      document.getElementById('r_password').value = '';

      const m = new bootstrap.Modal(document.getElementById('resetModal'));
      m.show();
    }
  });

  // Open create modal
  document.getElementById('btnOpenCreate').addEventListener('click', () => {
    if (!CAN_MANAGE_USERS) return showMsg('You don’t have permission to create users.', 'error');
    const m = new bootstrap.Modal(document.getElementById('createModal'));
    m.show();
  });

  // Reset password submit
  document.getElementById('btnReset').addEventListener('click', async () => {
    const newPass = document.getElementById('r_password').value;

    const { res, data } = await api(`/api/v1/users/${resetUserId}/reset-password`, {
      method: 'PATCH',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ password: newPass })
    });

    if (!res.ok) return showMsg({ status: res.status, data }, 'error');

    bootstrap.Modal.getInstance(document.getElementById('resetModal')).hide();
    showMsg(data, 'success');
    await loadUsers();
  });

  document.getElementById('btnReload').addEventListener('click', loadUsers);
  document.getElementById('search').addEventListener('keydown', (e) => {
    if (e.key === 'Enter') loadUsers();
  });

  // auto-open create modal when validation errors
  @if ($errors->any())
    new bootstrap.Modal(document.getElementById('createModal')).show();
  @endif

  loadUsers();
</script>
@endsection
