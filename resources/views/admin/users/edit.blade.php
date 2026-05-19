@extends('layouts.admin')

@section('title', 'Edit User - Barangay MIS')

@section('content')
<div class="page-surface">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 fw-bold text-primary">Edit User</h4>
            <p class="text-muted mb-0">Update user account</p>
        </div>
        <a class="btn btn-outline-secondary" href="{{ route('admin.users.show', $user) }}">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.users.update', $user) }}">
                @csrf
                @method('PUT')
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Role</label>
                        <select name="role" class="form-select" required>
                            <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin (Captain)</option>
                            <option value="secretary" {{ $user->role === 'secretary' ? 'selected' : '' }}>Staff (Secretary)</option>
                            <option value="clerk" {{ $user->role === 'clerk' ? 'selected' : '' }}>Clerk</option>
                            <option value="blotter" {{ $user->role === 'blotter' ? 'selected' : '' }}>Blotter</option>
                            <option value="readonly" {{ $user->role === 'readonly' ? 'selected' : '' }}>Read Only</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select" required>
                            <option value="active" {{ $user->status === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ $user->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i> Update User
                    </button>
                    <a href="{{ route('admin.users.show', $user) }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection