@extends('layouts.admin')

@section('title', 'Edit Household - Barangay MIS')

@section('content')

<div class="page-surface">

  <!-- Header -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h4 class="mb-1 fw-bold text-primary">Edit Household</h4>
      <p class="text-muted mb-0">Update household information and members</p>
    </div>

    <a href="{{ route('admin.households.index') }}" class="btn btn-outline-secondary">
      <i class="bi bi-arrow-left me-1"></i> Back to List
    </a>
  </div>

  {{-- Success message --}}
  @if(session('success'))
    <div class="alert alert-success d-flex align-items-center shadow-sm border-0 mb-4">
        <i class="bi bi-check-circle-fill fs-4 me-2"></i>
        <div>{{ session('success') }}</div>
    </div>
  @endif

  {{-- Error message --}}
  @if(session('error'))
    <div class="alert alert-danger d-flex align-items-center shadow-sm border-0 mb-4">
        <i class="bi bi-exclamation-triangle-fill fs-4 me-2"></i>
        <div>{{ session('error') }}</div>
    </div>
  @endif

  @if($errors->any())
    <div class="alert alert-danger d-flex align-items-center shadow-sm border-0 mb-4">
        <i class="bi bi-exclamation-triangle-fill fs-4 me-2"></i>
        <div>
            <div class="fw-bold mb-2">Please fix the following errors:</div>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
  @endif

  <form method="POST" action="{{ route('admin.households.update', $household) }}">
    @csrf
    @method('PUT')

    <div class="row g-4">
        <!-- Household Basic Info -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold text-primary"><i class="bi bi-house-fill me-2"></i>Basic Household Information</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                      <label class="form-label small text-muted text-uppercase fw-bold">Address / Blk & Lot <span class="text-danger">*</span></label>
                      <input type="text" name="address_line" class="form-control" value="{{ old('address_line', ucfirst(strtolower($household->address_line))) }}" required>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label small text-muted fw-bold">Street <span class="text-danger">*</span></label>
                            <input type="text" name="street" class="form-control" value="{{ old('street', $household->street) }}" placeholder="Street name" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small text-muted fw-bold">Phase</label>
                            <input type="text" name="phase" class="form-control" value="{{ old('phase', $household->phase) }}" placeholder="Phase 1, 2, 3...">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small text-muted fw-bold">Contact No.</label>
                            <div class="input-group">
                                <span class="input-group-text">+63</span>
                                <input type="text" name="contact_no" class="form-control" value="{{ old('contact_no', $household->contact_no) }}" placeholder="917 123 4567" maxlength="12">
                            </div>
                        </div>
                    </div>
                    <div class="row g-3 mt-2">
                        <div class="col-md-6">
                            <label class="form-label small text-muted fw-bold">Household Type</label>
                            <select name="household_type" class="form-select">
                                <option value="">-- Select --</option>
                                <option value="Family" {{ old('household_type', $household->household_type) == 'Family' ? 'selected' : '' }}>Family</option>
                                <option value="Extended Family" {{ old('household_type', $household->household_type) == 'Extended Family' ? 'selected' : '' }}>Extended Family</option>
                                <option value="Boarding / Rental" {{ old('household_type', $household->household_type) == 'Boarding / Rental' ? 'selected' : '' }}>Boarding / Rental</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted fw-bold">Homeownership Type</label>
                            <select name="homeownership_type" class="form-select">
                                <option value="">-- Select --</option>
                                <option value="Owned" {{ old('homeownership_type', $household->homeownership_type) == 'Owned' ? 'selected' : '' }}>Owned</option>
                                <option value="Rented" {{ old('homeownership_type', $household->homeownership_type) == 'Rented' ? 'selected' : '' }}>Rented</option>
                                <option value="Informal Settler" {{ old('homeownership_type', $household->homeownership_type) == 'Informal Settler' ? 'selected' : '' }}>Informal Settler</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Housing & Utilities -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold text-primary"><i class="bi bi-building me-2"></i>Housing & Utilities</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small text-muted fw-bold">House Type</label>
                            <select name="house_type" class="form-select">
                                <option value="">-- Select --</option>
                                <option value="Concrete" {{ old('house_type', $household->house_type) == 'Concrete' ? 'selected' : '' }}>Concrete</option>
                                <option value="Semi-concrete" {{ old('house_type', $household->house_type) == 'Semi-concrete' ? 'selected' : '' }}>Semi-concrete</option>
                                <option value="Light materials" {{ old('house_type', $household->house_type) == 'Light materials' ? 'selected' : '' }}>Light materials</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted fw-bold">Facilities Available</label>
                            <div class="row g-2">
                                <div class="col-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="has_toilet" id="has_toilet" value="1" {{ old('has_toilet', $household->has_toilet) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="has_toilet">Toilet</label>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="has_bathroom" id="has_bathroom" value="1" {{ old('has_bathroom', $household->has_bathroom) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="has_bathroom">Bathroom</label>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="has_kitchen" id="has_kitchen" value="1" {{ old('has_kitchen', $household->has_kitchen) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="has_kitchen">Kitchen</label>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="has_garage" id="has_garage" value="1" {{ old('has_garage', $household->has_garage) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="has_garage">Garage</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row g-3 mt-2">
                        <div class="col-md-6 d-flex align-items-center mt-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="has_electricity" id="has_electricity" value="1" {{ old('has_electricity', $household->has_electricity) ? 'checked' : '' }}>
                                <label class="form-check-label" for="has_electricity">Has Electricity</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Members Summary -->
        <div class="col-md-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold text-primary"><i class="bi bi-people-fill me-2"></i>Household Members Summary <small class="text-muted fw-normal">(Auto-calculated from members)</small></h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-2 col-6">
                            <label class="form-label small text-muted fw-bold">Total Members</label>
                            <input type="number" name="total_members" class="form-control" id="total_members" value="{{ old('total_members', $household->total_members) }}" min="0" readonly>
                        </div>
                        <div class="col-md-2 col-6">
                            <label class="form-label small text-muted fw-bold">Adults (18+)</label>
                            <input type="number" name="total_adults" class="form-control" id="total_adults" value="{{ old('total_adults', $household->total_adults) }}" min="0" readonly>
                        </div>
                        <div class="col-md-2 col-6">
                            <label class="form-label small text-muted fw-bold">Minors (under 18)</label>
                            <input type="number" name="total_minors" class="form-control" id="total_minors" value="{{ old('total_minors', $household->total_minors) }}" min="0" readonly>
                        </div>
                        <div class="col-md-2 col-6">
                            <label class="form-label small text-muted fw-bold">Senior Citizens</label>
                            <input type="number" name="total_senior_citizens" class="form-control" id="total_senior_citizens" value="{{ old('total_senior_citizens', $household->total_senior_citizens) }}" min="0" readonly>
                        </div>
                        <div class="col-md-2 col-6">
                            <label class="form-label small text-muted fw-bold">PWD</label>
                            <input type="number" name="total_pwd" class="form-control" id="total_pwd" value="{{ old('total_pwd', $household->total_pwd) }}" min="0" readonly>
                        </div>
                        <div class="col-md-2 col-6">
                            <label class="form-label small text-muted fw-bold">Registered Pets</label>
                            <input type="number" name="registered_pets_count" class="form-control" value="{{ old('registered_pets_count', $household->registered_pets_count) }}" min="0">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Socio-Economic -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold text-primary"><i class="bi bi-currency-dollar me-2"></i>Socio-Economic Information</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small text-muted fw-bold">Monthly Income Range</label>
                            <select name="monthly_income_range" class="form-select">
                                <option value="">-- Select --</option>
                                <option value="No Income" {{ old('monthly_income_range', $household->monthly_income_range) == 'No Income' ? 'selected' : '' }}>No Income</option>
                                <option value="Below 5,000" {{ old('monthly_income_range', $household->monthly_income_range) == 'Below 5,000' ? 'selected' : '' }}>Below 5,000</option>
                                <option value="5,001 - 10,000" {{ old('monthly_income_range', $household->monthly_income_range) == '5,001 - 10,000' ? 'selected' : '' }}>5,001 - 10,000</option>
                                <option value="10,001 - 20,000" {{ old('monthly_income_range', $household->monthly_income_range) == '10,001 - 20,000' ? 'selected' : '' }}>10,001 - 20,000</option>
                                <option value="20,001 - 30,000" {{ old('monthly_income_range', $household->monthly_income_range) == '20,001 - 30,000' ? 'selected' : '' }}>20,001 - 30,000</option>
                                <option value="30,001 - 50,000" {{ old('monthly_income_range', $household->monthly_income_range) == '30,001 - 50,000' ? 'selected' : '' }}>30,001 - 50,000</option>
                                <option value="50,001 - 100,000" {{ old('monthly_income_range', $household->monthly_income_range) == '50,001 - 100,000' ? 'selected' : '' }}>50,001 - 100,000</option>
                                <option value="Above 100,000" {{ old('monthly_income_range', $household->monthly_income_range) == 'Above 100,000' ? 'selected' : '' }}>Above 100,000</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted fw-bold">Employment Status</label>
                            <select name="employment_status" class="form-select">
                                <option value="">-- Select --</option>
                                <option value="Employed" {{ old('employment_status', $household->employment_status) == 'Employed' ? 'selected' : '' }}>Employed</option>
                                <option value="Unemployed" {{ old('employment_status', $household->employment_status) == 'Unemployed' ? 'selected' : '' }}>Unemployed</option>
                                <option value="Self-employed" {{ old('employment_status', $household->employment_status) == 'Self-employed' ? 'selected' : '' }}>Self-employed</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-3">
                        <label class="form-label small text-muted fw-bold">Primary Income Source</label>
                        <input type="text" name="primary_income_source" class="form-control" value="{{ old('primary_income_source', $household->primary_income_source) }}" placeholder="e.g. Farming, Business, Government Employee">
                    </div>
                    <div class="row g-3 mt-2">
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_4ps_beneficiary" id="is_4ps_beneficiary" value="1" {{ old('is_4ps_beneficiary', $household->is_4ps_beneficiary) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_4ps_beneficiary">4PS Beneficiary</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_indigent" id="is_indigent" value="1" {{ old('is_indigent', $household->is_indigent) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_indigent">Indigent</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Health & Community Indicators -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold text-primary"><i class="bi bi-heart-pulse me-2"></i>Health & Community Indicators</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="has_pregnant_member" id="has_pregnant_member" value="1" {{ old('has_pregnant_member', $household->has_pregnant_member) ? 'checked' : '' }}>
                                <label class="form-check-label" for="has_pregnant_member">Has Pregnant Member</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="has_senior_citizen" id="has_senior_citizen" value="1" {{ old('has_senior_citizen', $household->has_senior_citizen) ? 'checked' : '' }}>
                                <label class="form-check-label" for="has_senior_citizen">Has Senior Citizen</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="has_pwd" id="has_pwd" value="1" {{ old('has_pwd', $household->has_pwd) ? 'checked' : '' }}>
                                <label class="form-check-label" for="has_pwd">Has PWD Member</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="has_chronic_illness" id="has_chronic_illness" value="1" {{ old('has_chronic_illness', $household->has_chronic_illness) ? 'checked' : '' }}>
                                <label class="form-check-label" for="has_chronic_illness">Has Chronic Illness</label>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small text-muted fw-bold">Disaster Risk Level</label>
                            <select name="disaster_risk_level" class="form-select">
                                <option value="">-- Select --</option>
                                <option value="Low" {{ old('disaster_risk_level', $household->disaster_risk_level) == 'Low' ? 'selected' : '' }}>Low</option>
                                <option value="Flood-prone" {{ old('disaster_risk_level', $household->disaster_risk_level) == 'Flood-prone' ? 'selected' : '' }}>Flood-prone</option>
                                <option value="Fire-prone" {{ old('disaster_risk_level', $household->disaster_risk_level) == 'Fire-prone' ? 'selected' : '' }}>Fire-prone</option>
                                <option value="High" {{ old('disaster_risk_level', $household->disaster_risk_level) == 'High' ? 'selected' : '' }}>High</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted fw-bold">Barangay Program Participation</label>
                            <input type="text" name="barangay_program_participation" class="form-control" value="{{ old('barangay_program_participation', $household->barangay_program_participation) }}" placeholder="e.g. Medical, Feeding Program">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Household Members -->
        <div class="col-md-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold text-primary"><i class="bi bi-person-plus-fill me-2"></i>Household Members</h6>
                    <button type="button" class="btn btn-sm btn-primary" id="addMemberBtn">
                        <i class="bi bi-plus-lg me-1"></i> Add Member
                    </button>
                </div>
                <div class="card-body bg-light">
                    <div id="membersContainer">
                        @foreach($household->members as $index => $member)
                            <div class="card mb-3 border-0 shadow-sm member-row">
                                <div class="card-body position-relative">
                                    <button type="button" class="btn-close position-absolute top-0 end-0 m-3 remove-member" aria-label="Remove"></button>
                                    <input type="hidden" name="members[{{ $index }}][id]" value="{{ $member->id }}">
                                    
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="form-label small text-muted fw-bold">Full Name <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                                                @php
                                                    $residentName = $member->resident ? $member->resident->full_name : '';
                                                    $localName = trim($member->first_name . ' ' . $member->last_name);
                                                    $memberName = $residentName ?: $localName;
                                                @endphp
                                                <input type="text" 
                                                    name="members[{{ $index }}][full_name]" 
                                                    class="form-control name-input" 
                                                    value="{{ $memberName }}" 
                                                    placeholder="Search or type name" 
                                                    autocomplete="off"
                                                    spellcheck="false"
                                                    required>
                                            </div>
                                            <input type="hidden" name="members[{{ $index }}][resident_id]" class="resident-id" value="{{ $member->resident_id ?? '' }}">
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label small text-muted fw-bold">Relationship <span class="text-danger">*</span></label>
                                            <select name="members[{{ $index }}][relation]" class="form-select relation" required>
                                                <option value="">-- Select --</option>
                                                @foreach(['Head', 'Spouse', 'Child', 'Parent', 'Sibling', 'Grandparent', 'Grandchild', 'Relative', 'Other'] as $rel)
                                                    <option value="{{ $rel }}" {{ ($member->relationship == $rel || $member->relation == $rel) ? 'selected' : '' }}>{{ $rel }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label small text-muted fw-bold">Email</label>
                                            <input type="email" name="members[{{ $index }}][email]" class="form-control email" value="{{ $member->email }}">
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label small text-muted fw-bold">Birth Date <span class="text-danger">*</span></label>
                                            <input type="date" name="members[{{ $index }}][birth_date]" class="form-control birth-date" value="{{ $member->birth_date ? \Carbon\Carbon::parse($member->birth_date)->format('Y-m-d') : '' }}" required>
                                        </div>
                                        <div class="col-md-4 d-flex align-items-center mt-4">
                                            <div class="form-check">
                                                <input class="form-check-input member-pwd" type="checkbox" name="members[{{ $index }}][is_pwd]" id="member_pwd_{{ $index }}" value="1" {{ $member->is_pwd ? 'checked' : '' }}>
                                                <label class="form-check-label" for="member_pwd_{{ $index }}">PWD (Person With Disability)</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <div class="text-center text-muted mt-4 empty-state" style="display: none;">
                        <i class="bi bi-person-plus fs-1 opacity-25"></i>
                        <p class="small">Click "Add Member" to add people to this household.</p>
                    </div>
                </div>
                <div class="card-footer bg-white py-3 text-end">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-save me-1"></i> Update Household
                    </button>
                </div>
            </div>
        </div>
    </div>

  </form>
</div>

<style>
.autocomplete-list {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    z-index: 1050;
    background: #fff;
    border: 1px solid rgba(0,0,0,.15);
    border-radius: .35rem;
    max-height: 240px;
    overflow-y: auto;
    box-shadow: 0 0.75rem 1.5rem rgba(18,38,63,.03);
}
.autocomplete-item {
    cursor: pointer;
    padding: 0.5rem 0.75rem;
}
.autocomplete-item:hover {
    background: #f8f9fa;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const container = document.getElementById('membersContainer');
  const addBtn = document.getElementById('addMemberBtn');
  const residentsData = @json($availableResidents);
  let memberIndex = {{ $household->members->count() }};
  let hasHeadSelected = false;

  function residentLabel(resident) {
    return [resident.first_name, resident.last_name].filter(Boolean).join(' ').trim();
  }

  function updateRelationOptions() {
    hasHeadSelected = false;
    document.querySelectorAll('.member-row .relation').forEach(select => {
      const option = select.querySelector('option[value="Head"]');
      if (hasHeadSelected && option) {
        option.disabled = true;
        if (select.value === 'Head') {
          select.value = '';
        }
      }
      if (option) option.disabled = hasHeadSelected;
      hasHeadSelected = hasHeadSelected || select.value === 'Head';
    });
  }

  function calculateTotals() {
    let total = 0;
    let adults = 0;
    let minors = 0;
    let seniors = 0;
    let pwd = 0;

    const rows = container.querySelectorAll('.member-row');
    rows.forEach(row => {
      const birthDateInput = row.querySelector('.birth-date');
      const isPwd = row.querySelector('.member-pwd')?.checked;

      total++;

      if (birthDateInput && birthDateInput.value) {
        const birthDate = new Date(birthDateInput.value);
        const today = new Date();
        let age = today.getFullYear() - birthDate.getFullYear();
        const monthDiff = today.getMonth() - birthDate.getMonth();
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
          age--;
        }

        if (age >= 65) {
          seniors++;
        } else if (age >= 18) {
          adults++;
        } else {
          minors++;
        }
      } else {
        adults++;
      }

      if (isPwd) pwd++;
    });

    document.getElementById('total_members').value = total;
    document.getElementById('total_adults').value = adults;
    document.getElementById('total_minors').value = minors;
    document.getElementById('total_senior_citizens').value = seniors;
    document.getElementById('total_pwd').value = pwd;
  }

  function createSuggestionList(row, nameInput) {
    let list = row.querySelector('.autocomplete-list');
    if (!list) {
      list = document.createElement('div');
      list.className = 'autocomplete-list';
      const inputGroup = nameInput.closest('.input-group');
      if (inputGroup) {
        inputGroup.style.position = 'relative';
        inputGroup.appendChild(list);
      }
    }
    return list;
  }

  function bindRow(row) {
    const nameInput = row.querySelector('.name-input');
    const residentId = row.querySelector('.resident-id');
    const emailInput = row.querySelector('.email');
    const birthInput = row.querySelector('.birth-date');
    const relationSelect = row.querySelector('.relation');
    
    if (!nameInput) return;

    const suggestionList = createSuggestionList(row, nameInput);

    function renderSuggestions(matches) {
        if (!matches.length) {
            suggestionList.innerHTML = '';
            suggestionList.style.display = 'none';
            return;
        }
        suggestionList.innerHTML = matches.map(r => {
            const label = residentLabel(r).replace(/</g, '&lt;').replace(/>/g, '&gt;');
            const emailValue = (r.user && r.user.email) ? r.user.email : (r.email || '');
            return `<div class="autocomplete-item" data-id="${r.id}" data-email="${emailValue.replace(/"/g, '&quot;')}" data-birth="${r.birth_date || ''}">${label}</div>`;
        }).join('');
        suggestionList.style.display = 'block';
    }

    nameInput.addEventListener('input', function () {
        const query = this.value.toLowerCase().trim();
        if (!query) {
            renderSuggestions([]);
            return;
        }

        const matches = residentsData.filter(r => {
            return (r.household_id === null || r.household_id === undefined || r.household_id === '') && residentLabel(r).toLowerCase().includes(query);
        }).slice(0, 8);
        
        renderSuggestions(matches);
    });

    nameInput.addEventListener('blur', () => {
        setTimeout(() => suggestionList.style.display = 'none', 150);
    });

    suggestionList.addEventListener('mousedown', function (event) {
        const item = event.target.closest('.autocomplete-item');
        if (!item) return;
        event.preventDefault();

        nameInput.value = item.textContent;
        residentId.value = item.getAttribute('data-id');
        if (emailInput) emailInput.value = item.getAttribute('data-email');
        
        const bday = item.getAttribute('data-birth');
        if (birthInput && bday) {
            birthInput.value = new Date(bday).toISOString().split('T')[0];
        }
        suggestionList.style.display = 'none';
    });

    if (relationSelect) {
      relationSelect.addEventListener('change', updateRelationOptions);
    }

    const removeButton = row.querySelector('.remove-member');
    if (removeButton) {
        removeButton.addEventListener('click', function () {
            row.remove();
            updateRelationOptions();
            calculateTotals();
            checkEmptyState();
        });
    }

    // Add event listeners for auto-calculation
    if (birthInput) {
      birthInput.addEventListener('change', calculateTotals);
    }
    const pwdCheckbox = row.querySelector('.member-pwd');
    if (pwdCheckbox) {
      pwdCheckbox.addEventListener('change', calculateTotals);
    }
  }

  function createMemberRow(index) {
    const row = document.createElement('div');
    row.className = 'card mb-3 border-0 shadow-sm member-row';
    
    row.innerHTML = `
      <div class="card-body position-relative">
        <button type="button" class="btn-close position-absolute top-0 end-0 m-3 remove-member" aria-label="Remove"></button>
        
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label small text-muted fw-bold">Full Name <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                    <input type="text" 
                        name="members[${index}][full_name]" 
                        class="form-control name-input" 
                        placeholder="Search or type name" 
                        autocomplete="off" 
                        spellcheck="false"
                        required>
                </div>
                <input type="hidden" name="members[${index}][resident_id]" class="resident-id">
            </div>

            <div class="col-md-4">
                <label class="form-label small text-muted fw-bold">Relationship <span class="text-danger">*</span></label>
                <select name="members[${index}][relation]" class="form-select relation" required>
                    <option value="">-- Select --</option>
                    <option value="Head">Head of Family</option>
                    <option value="Spouse">Spouse</option>
                    <option value="Child">Child</option>
                    <option value="Parent">Parent</option>
                    <option value="Sibling">Sibling</option>
                    <option value="Grandparent">Grandparent</option>
                    <option value="Grandchild">Grandchild</option>
                    <option value="Relative">Relative</option>
                    <option value="Other">Other</option>
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label small text-muted fw-bold">Email</label>
                <input type="email" name="members[${index}][email]" class="form-control email" placeholder="Optional">
            </div>

            <div class="col-md-4">
                <label class="form-label small text-muted fw-bold">Birth Date <span class="text-danger">*</span></label>
                <input type="date" name="members[${index}][birth_date]" class="form-control birth-date" required>
            </div>
            <div class="col-md-4 d-flex align-items-center mt-4">
                <div class="form-check">
                    <input class="form-check-input member-pwd" type="checkbox" name="members[${index}][is_pwd]" id="member_pwd_${index}" value="1">
                    <label class="form-check-label" for="member_pwd_${index}">PWD (Person With Disability)</label>
                </div>
            </div>
        </div>
      </div>
    `;
    return row;
  }

  function checkEmptyState() {
      const count = container.children.length;
      const emptyState = document.querySelector('.empty-state');
      if(count === 0) {
          emptyState.style.display = 'block';
          hasHeadSelected = false;
      } else {
          emptyState.style.display = 'none';
      }
  }

  // Initialize existing rows
  document.querySelectorAll('.member-row').forEach(bindRow);
  
  // Calculate initial totals
  calculateTotals();
  updateRelationOptions();

  addBtn.addEventListener('click', () => {
    const newRow = createMemberRow(memberIndex);
    container.appendChild(newRow);
    bindRow(newRow);
    updateRelationOptions();
    calculateTotals();
    memberIndex++;
    checkEmptyState();
  });

  checkEmptyState();
});
</script>
@endsection