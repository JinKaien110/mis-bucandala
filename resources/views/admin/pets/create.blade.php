@extends('layouts.admin')

@section('title', 'Register Pet - Barangay MIS')

@section('content')
<div class="page-surface">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h4 class="mb-1">Register New Pet</h4>
      <p class="text-muted mb-0">Only residents with email can register pets</p>
    </div>
  </div>

  @if($residents->count() === 0)
    <div class="alert alert-warning">
      <i class="bi bi-exclamation-triangle me-2"></i>
      No residents with email found. Residents must have an email address to register pets.
    </div>
  @endif

  <div class="row">
    <div class="col-lg-8">
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <form method="POST" action="{{ route('admin.pets.store') }}">
            @csrf

            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Pet Owner (Resident)</label>
                <select name="resident_id" class="form-select" required {{ $residents->count() === 0 ? 'disabled' : '' }}>
                  <option value="">-- Select Resident --</option>
                  @foreach($residents as $resident)
                    <option value="{{ $resident->id }}">{{ $resident->full_name }} ({{ $resident->email }})</option>
                  @endforeach
                </select>
                <small class="text-muted">Resident must have an email address</small>
              </div>

              <div class="col-md-6">
                <label class="form-label">Pet Name</label>
                <input type="text" name="nickname" class="form-control" placeholder="e.g., Buddy" required>
              </div>

              <div class="col-md-6">
                <label class="form-label">Species</label>
                <select name="species" class="form-select" required>
                  <option value="">-- Select Species --</option>
                  <option value="dog">Dog</option>
                  <option value="cat">Cat</option>
                  <option value="bird">Bird</option>
                  <option value="other">Other</option>
                </select>
              </div>

              <div class="col-md-6">
                <label class="form-label">Breed</label>
                <input type="text" name="breed" class="form-control" placeholder="e.g., Labrador">
              </div>

              <div class="col-md-6">
                <label class="form-label">Sex</label>
                <select name="sex" class="form-select" required>
                  <option value="">-- Select Sex --</option>
                  <option value="male">Male</option>
                  <option value="female">Female</option>
                </select>
              </div>

              <div class="col-md-6">
                <label class="form-label">Birth Date</label>
                <input type="date" name="birth_date" class="form-control">
              </div>

              <div class="col-md-6">
                <label class="form-label">Color/Markings</label>
                <input type="text" name="color" class="form-control" placeholder="e.g., Brown with white patches">
              </div>

              <div class="col-md-6">
                <label class="form-label">Vaccination Status</label>
                <select name="vaccination_status" class="form-select">
                  <option value="">-- Select Status --</option>
                  <option value="up-to-date">Up to Date</option>
                  <option value="overdue">Overdue</option>
                  <option value="not-vaccinated">Not Vaccinated</option>
                </select>
              </div>

              <div class="col-12">
                <label class="form-label">Notes</label>
                <textarea name="notes" class="form-control" rows="2" placeholder="Any additional notes..."></textarea>
              </div>
            </div>

            <div class="d-flex gap-2 mt-4">
              <button type="submit" class="btn btn-primary" {{ $residents->count() === 0 ? 'disabled' : '' }}>Register Pet</button>
              <a href="{{ route('admin.pets.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
