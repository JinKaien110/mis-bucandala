@extends('layouts.admin')

@section('title', 'New Payment - Barangay MIS')

@section('content')
<div class="page-surface">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 fw-bold text-primary">New Payment</h4>
            <p class="text-muted mb-0">Create a new payment entry</p>
        </div>
        <a class="btn btn-outline-secondary" href="{{ route('admin.payments.index') }}">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.payments.store') }}">
                @csrf
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label">Resident (Optional)</label>
                        <select name="resident_id" class="form-select">
                            <option value="">Select Resident</option>
                            @foreach($residents as $resident)
                                <option value="{{ $resident->id }}">
                                    {{ $resident->first_name }} {{ $resident->middle_name ?? '' }} {{ $resident->last_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Document Request (Optional)</label>
                        <select name="document_request_id" class="form-select">
                            <option value="">Select Document Request</option>
                            @foreach($documentRequests as $doc)
                                <option value="{{ $doc->id }}">
                                    #{{ $doc->id }} - {{ $doc->documentType->name ?? 'N/A' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Payment Type</label>
                        <select name="payment_type" class="form-select" required>
                            <option value="">Select Type</option>
                            <option value="document_fee">Document Fee</option>
                            <option value="clearance">Clearance</option>
                            <option value="certification">Certification</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Amount</label>
                        <div class="input-group">
                            <span class="input-group-text">₱</span>
                            <input type="number" name="amount" class="form-control" step="0.01" min="0.01" required>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Description</label>
                        <input type="text" name="description" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">OR Number (Optional)</label>
                        <input type="text" name="or_number" class="form-control">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Notes (Optional)</label>
                        <textarea name="notes" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i> Create Payment
                    </button>
                    <a href="{{ route('admin.payments.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection