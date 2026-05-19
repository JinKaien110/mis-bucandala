@extends('layouts.admin')

@section('title', 'Edit Payment - Barangay MIS')

@section('content')
<div class="page-surface">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 fw-bold text-primary">Edit Payment #{{ $payment->id }}</h4>
            <p class="text-muted mb-0">Update payment details</p>
        </div>
        <a class="btn btn-outline-secondary" href="{{ route('admin.payments.show', $payment) }}">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.payments.update', $payment) }}">
                @csrf
                @method('PUT')
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label">Resident (Optional)</label>
                        <select name="resident_id" class="form-select">
                            <option value="">Select Resident</option>
                            @foreach($residents as $resident)
                                <option value="{{ $resident->id }}" {{ $payment->resident_id == $resident->id ? 'selected' : '' }}>
                                    {{ $resident->first_name }} {{ $resident->middle_name ?? '' }} {{ $resident->last_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Document Request (Optional)</label>
                        <select name="document_request_id" class="form-select">
                            <option value="">Select Document Request</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Payment Type</label>
                        <select name="payment_type" class="form-select" required>
                            <option value="document_fee" {{ $payment->payment_type === 'document_fee' ? 'selected' : '' }}>Document Fee</option>
                            <option value="clearance" {{ $payment->payment_type === 'clearance' ? 'selected' : '' }}>Clearance</option>
                            <option value="certification" {{ $payment->payment_type === 'certification' ? 'selected' : '' }}>Certification</option>
                            <option value="other" {{ $payment->payment_type === 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Amount</label>
                        <div class="input-group">
                            <span class="input-group-text">₱</span>
                            <input type="number" name="amount" class="form-control" step="0.01" min="0.01" value="{{ $payment->amount }}" required>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Description</label>
                        <input type="text" name="description" class="form-control" value="{{ $payment->description }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">OR Number (Optional)</label>
                        <input type="text" name="or_number" class="form-control" value="{{ $payment->or_number }}">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Notes (Optional)</label>
                        <textarea name="notes" class="form-control" rows="3">{{ $payment->notes }}</textarea>
                    </div>
                </div>
                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i> Update Payment
                    </button>
                    <a href="{{ route('admin.payments.show', $payment) }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection