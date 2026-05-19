@extends('layouts.admin')

@section('title', 'User Details - Barangay MIS')

@section('content')
<div class="page-surface">
    @php
        $currentUserRole = auth()->user()->role ?? '';
        $canManageUsers = $currentUserRole === 'admin';
    @endphp

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 fw-bold text-primary">{{ $user->name }}</h4>
            <p class="text-muted mb-0">User Account Details</p>
        </div>
        <div class="d-flex gap-2">
            <a class="btn btn-outline-secondary" href="{{ route('admin.users.index') }}">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
            @if($canManageUsers)
                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-outline-primary">
                    <i class="bi bi-pencil me-1"></i> Edit
                </a>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success d-flex align-items-center shadow-sm border-0 mb-4">
            <i class="bi bi-check-circle-fill fs-4 me-2"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold text-primary">Account Information</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <small class="text-muted text-uppercase fw-bold d-block">Name</small>
                            <span>{{ $user->name }}</span>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted text-uppercase fw-bold d-block">Email</small>
                            <span>{{ $user->email }}</span>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted text-uppercase fw-bold d-block">Role</small>
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
                                    default => 'secondary'
                                };
                            @endphp
                            <span class="badge bg-{{ $roleColor }}-subtle text-{{ $roleColor }} border rounded-pill">{{ $roleLabel }}</span>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted text-uppercase fw-bold d-block">Status</small>
                            @php $statusColor = $user->status === 'active' ? 'success' : 'secondary'; @endphp
                            <span class="badge bg-{{ $statusColor }}-subtle text-{{ $statusColor }} border rounded-pill">{{ ucfirst($user->status) }}</span>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted text-uppercase fw-bold d-block">Registered Via</small>
                            <span>{{ $user->registered_via ?? 'N/A' }}</span>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted text-uppercase fw-bold d-block">Last Updated</small>
                            <span>{{ $user->updated_at->format('M d, Y h:i A') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @if($canManageUsers)
            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0 fw-bold text-primary">Actions</h6>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.users.toggleStatus', $user) }}">
                            @csrf
                            <button type="submit" class="btn {{ $user->status === 'active' ? 'btn-warning' : 'btn-success' }} w-100 mb-2">
                                <i class="bi bi-power me-1"></i> {{ $user->status === 'active' ? 'Deactivate' : 'Activate' }}
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.users.resetPassword', $user) }}">
                            @csrf
                            <div class="mb-2">
                                <input type="password" name="password" class="form-control" placeholder="New password" minlength="8" required>
                            </div>
                            <input type="password" name="password_confirmation" class="form-control mb-2" placeholder="Confirm password" minlength="8" required>
                            <button type="submit" class="btn btn-outline-primary w-100">
                                <i class="bi bi-key me-1"></i> Reset Password
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection