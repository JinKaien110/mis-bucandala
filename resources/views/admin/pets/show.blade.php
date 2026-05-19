@extends('layouts.admin')

@section('title', 'Pet Details - Barangay MIS')

@section('content')
<div class="page-surface">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h4 class="mb-1">{{ $pet->nickname }}</h4>
      <p class="text-muted mb-0">{{ ucfirst($pet->species) }} {{ $pet->breed ? '- ' . $pet->breed : '' }}</p>
    </div>
    <div class="d-flex gap-2">
      <a href="{{ route('admin.pets.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back
      </a>
      <a href="{{ route('admin.pets.edit', $pet) }}" class="btn btn-primary">
        <i class="bi bi-pencil"></i> Edit
      </a>
    </div>
  </div>

  <div class="row">
    <div class="col-lg-8">
      <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-light">
          <h5 class="mb-0">Pet Information</h5>
        </div>
        <div class="card-body">
          <table class="table table-borderless">
            <tr>
              <td class="text-muted" style="width: 180px;">Name</td>
              <td class="fw-semibold">{{ $pet->nickname }}</td>
            </tr>
            <tr>
              <td class="text-muted">Species</td>
              <td>{{ ucfirst($pet->species) }}</td>
            </tr>
            <tr>
              <td class="text-muted">Breed</td>
              <td>{{ $pet->breed ?? '-' }}</td>
            </tr>
            <tr>
              <td class="text-muted">Sex</td>
              <td><span class="badge bg-{{ $pet->sex === 'male' ? 'info' : 'pink' }}">{{ ucfirst($pet->sex) }}</span></td>
            </tr>
            <tr>
              <td class="text-muted">Age</td>
              <td>{{ $pet->age ? $pet->age . ' years old' : 'Unknown' }}</td>
            </tr>
            <tr>
              <td class="text-muted">Birth Date</td>
              <td>{{ $pet->birth_date?->format('F d, Y') ?? '-' }}</td>
            </tr>
            <tr>
              <td class="text-muted">Color/Markings</td>
              <td>{{ $pet->color ?? '-' }}</td>
            </tr>
            <tr>
              <td class="text-muted">Vaccination Status</td>
              <td>
                @switch($pet->vaccination_status)
                  @case('up-to-date')
                    <span class="badge bg-success">Up to Date</span>
                    @break
                  @case('overdue')
                    <span class="badge bg-danger">Overdue</span>
                    @break
                  @default
                    <span class="badge bg-secondary">Not Vaccinated</span>
                @endswitch
              </td>
            </tr>
            @if($pet->notes)
            <tr>
              <td class="text-muted">Notes</td>
              <td>{{ $pet->notes }}</td>
            </tr>
            @endif
          </table>
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-light">
          <h5 class="mb-0">Owner Information</h5>
        </div>
        <div class="card-body">
          @if($pet->resident)
            <p class="mb-1"><strong>{{ $pet->resident->full_name }}</strong></p>
            <p class="text-muted mb-1">{{ $pet->resident->email }}</p>
            <p class="text-muted mb-0">{{ $pet->resident->contact_no }}</p>
          @else
            <p class="text-muted mb-0">No owner information</p>
          @endif
        </div>
      </div>

      <div class="card border-0 shadow-sm">
        <div class="card-header bg-light">
          <h5 class="mb-0">Actions</h5>
        </div>
        <div class="card-body">
          <form method="POST" action="{{ route('admin.pets.destroy', $pet) }}" onsubmit="return confirm('Archive this pet record?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger w-100">
              <i class="bi bi-trash"></i> Delete Record
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
