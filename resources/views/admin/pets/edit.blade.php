@extends('layouts.admin')

@section('title', 'Edit Pet - Barangay MIS')

@section('content')
<div class="page-surface">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h4 class="mb-1">Edit Pet</h4>
      <p class="text-muted mb-0">Update pet information</p>
    </div>
  </div>

  <div class="row">
    <div class="col-lg-8">
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <form method="POST" action="{{ route('admin.pets.update', $pet) }}">
            @csrf
            @method('PUT')

            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Pet Owner (Resident)</label>
                <select name="resident_id" class="form-select" required>
                  <option value="">-- Select Resident --</option>
                  @foreach($residents as $resident)
                    <option value="{{ $resident->id }}" {{ $pet->resident_id === $resident->id ? 'selected' : '' }}>
                      {{ $resident->full_name }} ({{ $resident->email }})
                    </option>
                  @endforeach
                </select>
              </div>

              <div class="col-md-6">
                <label class="form-label">Pet Name</label>
                <input type="text" name="nickname" class="form-control" value="{{ old('nickname', $pet->nickname) }}" required>
              </div>

              <div class="col-md-6">
                <label class="form-label">Species</label>
                <select name="species" class="form-select" required>
                  <option value="dog" {{ $pet->species === 'dog' ? 'selected' : '' }}>Dog</option>
                  <option value="cat" {{ $pet->species === 'cat' ? 'selected' : '' }}>Cat</option>
                  <option value="bird" {{ $pet->species === 'bird' ? 'selected' : '' }}>Bird</option>
                  <option value="other" {{ $pet->species === 'other' ? 'selected' : '' }}>Other</option>
                </select>
              </div>

              <div class="col-md-6">
                <label class="form-label">Breed</label>
                <input type="text" name="breed" class="form-control" value="{{ old('breed', $pet->breed) }}">
              </div>

              <div class="col-md-6">
                <label class="form-label">Sex</label>
                <select name="sex" class="form-select" required>
                  <option value="male" {{ $pet->sex === 'male' ? 'selected' : '' }}>Male</option>
                  <option value="female" {{ $pet->sex === 'female' ? 'selected' : '' }}>Female</option>
                </select>
              </div>

              <div class="col-md-6">
                <label class="form-label">Birth Date</label>
                <input type="date" name="birth_date" class="form-control" value="{{ old('birth_date', $pet->birth_date?->format('Y-m-d')) }}">
              </div>

              <div class="col-md-6">
                <label class="form-label">Color/Markings</label>
                <input type="text" name="color" class="form-control" value="{{ old('color', $pet->color) }}">
              </div>

              <div class="col-md-6">
                <label class="form-label">Vaccination Status</label>
                <select name="vaccination_status" class="form-select">
                  <option value="">-- Select Status --</option>
                  <option value="up-to-date" {{ $pet->vaccination_status === 'up-to-date' ? 'selected' : '' }}>Up to Date</option>
                  <option value="overdue" {{ $pet->vaccination_status === 'overdue' ? 'selected' : '' }}>Overdue</option>
                  <option value="not-vaccinated" {{ $pet->vaccination_status === 'not-vaccinated' ? 'selected' : '' }}>Not Vaccinated</option>
                </select>
              </div>

              <div class="col-12">
                <label class="form-label">Notes</label>
                <textarea name="notes" class="form-control" rows="2">{{ old('notes', $pet->notes) }}</textarea>
              </div>
            </div>

            <div class="d-flex gap-2 mt-4">
              <button type="submit" class="btn btn-primary">Update Pet</button>
              <a href="{{ route('admin.pets.show', $pet) }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
