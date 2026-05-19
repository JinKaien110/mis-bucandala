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
            <a class="btn btn-primary" href="{{ route('admin.users.create') }}">
                <i class="bi bi-plus-lg me-1"></i> New User
            </a>
        @endif
    </div>

    @if(session('success'))
        <div class="alert alert-success d-flex align-items-center shadow-sm border-0 mb-4">
            <i class="bi bi-check-circle-fill fs-4 me-2"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    <div class="row g-4 mb-4">
        <div class="col-md-3">
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
                        <option value="blotter" {{ request('role') === 'blotter' ? 'selected' : '' }}>Blotter</option>
                        <option value="readonly" {{ request('role') === 'readonly' ? 'selected' : '' }}>Read Only</option>
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
                <div class="col-md-2">
                    <select name="role" class="form-select">
                        <option value="">All Roles</option>
                        <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin (Captain)</option>
                        <option value="secretary" {{ request('role') === 'secretary' ? 'selected' : '' }}>Staff (Secretary)</option>
                        <option value="clerk" {{ request('role') === 'clerk' ? 'selected' : '' }}>Clerk</option>
                        <option value="blotter" {{ request('role') === 'blotter' ? 'selected' : '' }}>Blotter</option>
                        <option value="readonly" {{ request('role') === 'readonly' ? 'selected' : '' }}>Read Only</option>
                    </select>
                </div>
            </form>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
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
                                    <span class="fw-bold">{{ $user->name }}</span>
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @php
                                        $roleLabel = match($user->role) {
                                            'admin' => 'Admin (Captain)',
                                            'secretary' => 'Staff (Secretary)',
                                            'clerk' => 'Clerk',
                                            'blotter' => 'Blotter',
                                            'readonly' => 'Read Only',
                                            default => $user->role
                                        };
                                        $roleColor = match($user->role) {
                                            'admin' => 'danger',
                                            'secretary' => 'info',
                                            'clerk' => 'info',
                                            'blotter' => 'warning',
                                            'readonly' => 'secondary',
                                            default => 'secondary'
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
            <div class="card-footer bg-white border-top-0 py-3">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
@endsection