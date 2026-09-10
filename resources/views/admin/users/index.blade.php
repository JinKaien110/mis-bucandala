@extends('layouts.admin')

@section('title', 'Users & Roles - Barangay MIS')

@section('content')
<div class="page-surface">
    @php
        $currentUserRole = auth()->user()->role ?? '';
        $canCreateUsers = $currentUserRole === 'admin';
    @endphp

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 fw-bold text-primary">Users & Roles</h4>
            <p class="text-muted mb-0">Manage user accounts</p>
        </div>
        @if($canCreateUsers)
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createUserModal">
                <i class="bi bi-plus-lg me-1"></i> New User
            </button>
        @endif
    </div>

    @if(session('success'))
        <div class="alert alert-success d-flex align-items-center shadow-sm border-0 mb-4">
            <i class="bi bi-check-circle-fill fs-4 me-2"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    <div class="row g-4 mb-4">
        <div class="col-12 col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-muted small text-uppercase">Total Users</div>
                    <div class="fs-3 fw-bold text-primary">{{ $stats['total_users'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-muted small text-uppercase">Admins</div>
                    <div class="fs-3 fw-bold text-danger">{{ $stats['admin_count'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-muted small text-uppercase">Clerks</div>
                    <div class="fs-3 fw-bold text-info">{{ $stats['clerk_count'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-muted small text-uppercase">Lupon Members</div>
                    <div class="fs-3 fw-bold text-warning">{{ $stats['lupon_count'] ?? 0 }}</div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-muted small text-uppercase">Active</div>
                    <div class="fs-3 fw-bold text-success">{{ $stats['active_count'] }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <form method="GET" action="{{ route('admin.users.index') }}" class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Search name or email..." value="{{ request('search') }}">
                </div>

                <div class="col-md-3">
                    <select name="role" class="form-select">
                        <option value="">All Roles</option>
                        <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin (Captain)</option>
                        <option value="secretary" {{ request('role') === 'secretary' ? 'selected' : '' }}>Staff (Secretary)</option>
                        <option value="clerk" {{ request('role') === 'clerk' ? 'selected' : '' }}>Clerk</option>
                        <option value="lupon" {{ request('role') === 'lupon' ? 'selected' : '' }}>Lupon Member</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <button type="submit" class="btn btn-outline-primary w-100">Filter</button>
                </div>
            </form>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive w-100" style="overflow-x:auto;">
                <table class="table table-hover align-middle mb-0 w-100" style="table-layout:fixed;">
                    <thead class="bg-light text-uppercase small text-muted">
                        <tr>
                            <th class="px-4 py-3">Name</th>
                            <th class="py-3">Email</th>
                            <th class="py-3">Role</th>
                            <th class="py-3">Status</th>
                            <th class="py-3">Registered</th>
                            <th class="py-3 text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td class="px-4">
                                    <span class="fw-bold">
                                        {{ trim(($user->admin->first_name ?? '') . ' ' . ($user->admin->last_name ?? '')) }}
                                    </span>
                                </td>

                                <td>{{ $user->email }}</td>

                                <td>
                                    @php
                                        // If the user is a staff-role account that is assigned as a Lupon Member
                                        // (based on admins.position), show that position in the Role column.
                                        $adminPosition = $user->admin->position ?? null;

                                        $roleLabel = match($user->role) {
                                            'admin' => 'Admin (Captain)',
                                            'secretary' => 'Staff',
                                            'clerk' => 'Clerk',
                                            'lupon' => 'Lupon Member',
                                            'readonly' => 'Read Only',
                                            default => $user->role
                                        };

                                        if (($user->role ?? null) === 'staff' && $adminPosition) {
                                            // For staff users, trust admins.position as the source of truth.
                                            // Example: Barangay Treasurer, Lupon Member, etc.
                                            $roleLabel = $adminPosition;
                                        }

                                        $roleColor = match($user->role) {
                                            'admin' => 'danger',
                                            'secretary' => 'info',
                                            'clerk' => 'info',
                                            'lupon' => 'warning',
                                            'readonly' => 'secondary',
                                            default => 'secondary'
                                        };

                                        // Ensure specific roles/positions get their own badge color (per request)
                                        if (($user->role ?? null) === 'staff' && $adminPosition === 'Barangay Treasurer') {
                                            $roleColor = 'success';
                                        };

                                        if (($user->role ?? null) === 'staff' && $adminPosition === 'Lupon Member') {
                                            $roleColor = 'warning';
                                        };

                                        if (($user->role ?? null) === 'resident') {
                                            $roleColor = 'primary';
                                        };

                                        if (($user->role ?? null) === 'admin' && ($user->admin->position ?? null) === 'Barangay Captain') {
                                            $roleColor = 'danger';
                                        };
                                    @endphp

                                    <span class="badge bg-{{ $roleColor }}-subtle text-{{ $roleColor }} border rounded-pill">
                                        {{ $roleLabel }}
                                    </span>
                                </td>

                                <td>
                                    @php
                                        $statusColor = $user->status === 'active' ? 'success' : 'secondary';
                                    @endphp
                                    <span class="badge bg-{{ $statusColor }}-subtle text-{{ $statusColor }} border rounded-pill">
                                        {{ ucfirst($user->status) }}
                                    </span>
                                </td>

                                <td>{{ $user->created_at->format('M d, Y') }}</td>

                                <td class="text-end pe-4">
                                    <a class="btn btn-sm btn-info rounded-circle" style="width: 32px; height: 32px;" href="{{ route('admin.users.show', $user) }}">
                                        <i class="bi bi-eye-fill"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="text-muted opacity-50">
                                        <i class="bi bi-people fs-1 d-block mb-3"></i>
                                        <h5 class="fw-normal">No users found</h5>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($users->hasPages())
            <div class="card-footer bg-white border-top-0 py-3 overflow-hidden">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div class="text-muted small">
                        Showing {{ $users->firstItem() ?? 0 }} to {{ $users->lastItem() ?? 0 }} of {{ $users->total() }} results
                    </div>
                    <div class="w-100" style="min-width:0; display:block;">
                        <x-admin-pagination :paginator="$users" />
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

{{-- Create User Modal --}}
@if($canCreateUsers)
    <div class="modal fade" id="createUserModal" tabindex="-1" aria-labelledby="createUserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold text-primary" id="createUserModalLabel">Create New User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <form method="POST" action="{{ route('admin.users.store') }}">
                        @csrf
                        <input
    type="hidden"
    name="barangay_official_id"
    id="barangay_official_id">
                        <div class="row g-4">
                            <div class="col-md-12">
                                <label class="form-label">Barangay Official (optional)</label>
                                <select id="official_selector" class="form-select" aria-label="Select Barangay Official">
                                    <option value="">Select an official to prefill</option>
                                </select>
                                <div class="form-text">Selecting an official will auto-fill first name, last name, email, and position.</div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">First Name <span class="text-danger">*</span></label>
                                <input id="official_first_name" type="text" name="first_name" class="form-control" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Last Name <span class="text-danger">*</span></label>
                                <input id="official_last_name" type="text" name="last_name" class="form-control" required>
                            </div>


                            <div class="col-md-6">
                                <label class="form-label">Email Address <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Contact Number</label>
                                <input type="text" name="contact_no" class="form-control" placeholder="e.g., 09123456789">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Password <span class="text-danger">*</span></label>
                                <input type="password" name="password" class="form-control" minlength="8" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                <input type="password" name="password_confirmation" class="form-control" minlength="8" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Role <span class="text-danger">*</span></label>
                                <select name="role" class="form-select" required>
                                    <option value="">Select Role</option>
                                    <option value="admin">Admin (Barangay Captain)</option>
                                    <option value="staff" default selected>Staff</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Position <span class="text-danger">*</span></label>
                                <select id="official_position" name="position" class="form-select" required>
                                    <option value="">Select Position</option>
                                    <option value="Barangay Captain">Barangay Captain</option>
                                    <option value="Barangay Kagawad">Barangay Kagawad</option>
                                    <option value="Barangay Secretary">Barangay Secretary</option>
                                    <option value="Barangay Treasurer">Barangay Treasurer</option>
                                    <option value="SK Chairman">SK Chairman</option>
                                    <option value="Clerk">Barangay Clerk</option>
                                    <option value="Lupon Member">Barangay Lupon</option>
                                </select>
                            </div>

                        </div>

                        <div class="mt-4 d-flex gap-2 justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i> Create User
                            </button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        </div>

                        <script>
(function () {

    const officialSelector = document.getElementById('official_selector');

    if (!officialSelector) return;

    const firstName = document.getElementById('official_first_name');
    const lastName = document.getElementById('official_last_name');
    const position = document.getElementById('official_position');
    const officialId = document.getElementById('barangay_official_id');

    const emailInput = document.querySelector('input[name="email"]');

    let officials = [];

    // Load active officials once
    fetch('/admin/officials/api/active')
        .then(response => response.json())
        .then(payload => {

            officials = payload.officials || [];

            officials.forEach(o => {

                const option = document.createElement('option');

                option.value = o.id;

                option.textContent =
                    `${o.position} - ${o.first_name} ${o.last_name}`;

                officialSelector.appendChild(option);

            });

        })
        .catch(error => {
            console.error(error);
        });

    officialSelector.addEventListener('change', function () {

        const id = this.value;

        if (!id) {

            officialId.value = '';

            firstName.value = '';

            lastName.value = '';

            emailInput.value = '';

            position.value = '';

            return;
        }

        const official = officials.find(o => o.id == id);

        if (!official) return;

        officialId.value = official.id;

        firstName.value = official.first_name ?? '';

        lastName.value = official.last_name ?? '';

        emailInput.value = official.email ?? '';

        position.value = official.position ?? '';

    });

})();
</script>

                    </form>
                </div>
            </div>
        </div>
    </div>
@endif
@endsection

