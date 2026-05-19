@extends('layouts.admin')

@section('title', 'Create Household - Barangay MIS')

@section('content')
<div class="page-surface">
  
  <!-- Header -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h4 class="mb-1 fw-bold text-primary">Create Household</h4>
      <p class="text-muted mb-0">Register a new household and its members</p>
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

  <form method="POST" action="{{ route('admin.households.store') }}">
    @csrf

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
                       <input type="text" name="address_line" class="form-control" placeholder="e.g. Blk 1 Lot 2" required>
                     </div>
                     <div class="row g-3">
                         <div class="col-md-4">
                             <label class="form-label small text-muted fw-bold">Phase</label>
                             <input type="number" name="phase" class="form-control" placeholder="1" min="1">
                         </div>
                         <div class="col-md-8">
                             <label class="form-label small text-muted fw-bold">Contact No.</label>
                             <div class="input-group">
                                 <span class="input-group-text">+63</span>
                                 <input type="text" name="contact_no" class="form-control" placeholder="917 123 4567" maxlength="12">
                             </div>
                         </div>
                     </div>
                    <div class="row g-3 mt-2">
                        <div class="col-md-6">
                            <label class="form-label small text-muted fw-bold">Household Type</label>
                            <select name="household_type" class="form-select">
                                <option value="">-- Select --</option>
                                <option value="Family">Family</option>
                                <option value="Extended Family">Extended Family</option>
                                <option value="Boarding / Rental">Boarding / Rental</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted fw-bold">Homeownership Type</label>
                            <select name="homeownership_type" class="form-select">
                                <option value="">-- Select --</option>
                                <option value="Owned">Owned</option>
                                <option value="Rented">Rented</option>
                                <option value="Informal Settler">Informal Settler</option>
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
                                <option value="Concrete">Concrete</option>
                                <option value="Semi-concrete">Semi-concrete</option>
                                <option value="Light materials">Light materials</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted fw-bold">Facilities Available</label>
                            <div class="row g-2">
                                <div class="col-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="has_toilet" id="has_toilet" value="1">
                                        <label class="form-check-label" for="has_toilet">Toilet</label>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="has_bathroom" id="has_bathroom" value="1">
                                        <label class="form-check-label" for="has_bathroom">Bathroom</label>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="has_kitchen" id="has_kitchen" value="1">
                                        <label class="form-check-label" for="has_kitchen">Kitchen</label>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="has_garage" id="has_garage" value="1">
                                        <label class="form-check-label" for="has_garage">Garage</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row g-3 mt-2">
                        <div class="col-md-6 d-flex align-items-center mt-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="has_electricity" id="has_electricity" value="1" checked>
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
                            <input type="number" name="total_members" class="form-control" id="total_members" value="0" min="0" readonly>
                        </div>
                        <div class="col-md-2 col-6">
                            <label class="form-label small text-muted fw-bold">Adults (18+)</label>
                            <input type="number" name="total_adults" class="form-control" id="total_adults" value="0" min="0" readonly>
                        </div>
                        <div class="col-md-2 col-6">
                            <label class="form-label small text-muted fw-bold">Minors (under 18)</label>
                            <input type="number" name="total_minors" class="form-control" id="total_minors" value="0" min="0" readonly>
                        </div>
                        <div class="col-md-2 col-6">
                            <label class="form-label small text-muted fw-bold">Senior Citizens</label>
                            <input type="number" name="total_senior_citizens" class="form-control" id="total_senior_citizens" value="0" min="0" readonly>
                        </div>
                        <div class="col-md-2 col-6">
                            <label class="form-label small text-muted fw-bold">PWD</label>
                            <input type="number" name="total_pwd" class="form-control" id="total_pwd" value="0" min="0" readonly>
                        </div>
                        <div class="col-md-2 col-6">
                            <label class="form-label small text-muted fw-bold">Registered Pets</label>
                            <input type="number" name="registered_pets_count" class="form-control" value="0" min="0">
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
                                <option value="No Income">No Income</option>
                                <option value="Below 5,000">Below 5,000</option>
                                <option value="5,001 - 10,000">5,001 - 10,000</option>
                                <option value="10,001 - 20,000">10,001 - 20,000</option>
                                <option value="20,001 - 30,000">20,001 - 30,000</option>
                                <option value="30,001 - 50,000">30,001 - 50,000</option>
                                <option value="50,001 - 100,000">50,001 - 100,000</option>
                                <option value="Above 100,000">Above 100,000</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted fw-bold">Employment Status</label>
                            <select name="employment_status" class="form-select">
                                <option value="">-- Select --</option>
                                <option value="Employed">Employed</option>
                                <option value="Unemployed">Unemployed</option>
                                <option value="Self-employed">Self-employed</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-3">
                        <label class="form-label small text-muted fw-bold">Primary Income Source</label>
                        <input type="text" name="primary_income_source" class="form-control" placeholder="e.g. Farming, Business, Government Employee">
                    </div>
                    <div class="row g-3 mt-2">
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_4ps_beneficiary" id="is_4ps_beneficiary" value="1">
                                <label class="form-check-label" for="is_4ps_beneficiary">4PS Beneficiary</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_indigent" id="is_indigent" value="1">
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
                                <input class="form-check-input" type="checkbox" name="has_pregnant_member" id="has_pregnant_member" value="1">
                                <label class="form-check-label" for="has_pregnant_member">Has Pregnant Member</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="has_senior_citizen" id="has_senior_citizen" value="1">
                                <label class="form-check-label" for="has_senior_citizen">Has Senior Citizen</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="has_pwd" id="has_pwd" value="1">
                                <label class="form-check-label" for="has_pwd">Has PWD Member</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="has_chronic_illness" id="has_chronic_illness" value="1">
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
                                <option value="Low">Low</option>
                                <option value="Flood-prone">Flood-prone</option>
                                <option value="Fire-prone">Fire-prone</option>
                                <option value="High">High</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted fw-bold">Barangay Program Participation</label>
                            <input type="text" name="barangay_program_participation" class="form-control" placeholder="e.g. Medical, Feeding Program">
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
                    <div id="membersContainer"></div>
                    
                    <div class="text-center text-muted mt-4 empty-state" style="display: none;">
                        <i class="bi bi-person-plus fs-1 opacity-25"></i>
                        <p class="small">Click "Add Member" to add people to this household.</p>
                    </div>
                </div>
                <div class="card-footer bg-white py-3 text-end">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-save me-1"></i> Save Household
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
  const residentsData = @json($residents ?? []);
  let memberIndex = 0;
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

  function bindRow(row) {
    const nameInput = row.querySelector('.name-input');
    const residentId = row.querySelector('.resident-id');
    const emailInput = row.querySelector('.email');
    const birthInput = row.querySelector('.birth-date');
    const badge = row.querySelector('.resident-badge');
    const relationSelect = row.querySelector('.relation');
    
    let suggestionList = document.createElement('div');
    suggestionList.className = 'autocomplete-list';
    suggestionList.style.display = 'none';
    nameInput.closest('.input-group').style.position = 'relative';
    nameInput.closest('.input-group').appendChild(suggestionList);

    function renderSuggestions(matches) {
        if (!matches.length) {
            suggestionList.innerHTML = '';
            suggestionList.style.display = 'none';
            return;
        }
        suggestionList.innerHTML = matches.map(r => {
            const label = residentLabel(r).replace(/</g, '&lt;').replace(/>/g, '&gt;');
            const emailValue = (r.user && r.user.email) ? r.user.email : (r.email || '');
            return `<div class="autocomplete-item" 
                        data-id="${r.id}" 
                        data-email="${emailValue.replace(/"/g, '&quot;')}" 
                        data-birth="${r.birth_date || ''}">${label}</div>`;
        }).join('');
        suggestionList.style.display = 'block';
    }

    nameInput.addEventListener('input', function () {
        const query = this.value.toLowerCase().trim();
        if (!query) {
            renderSuggestions([]);
            residentId.value = '';
            if(badge) badge.classList.add('d-none');
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

    suggestionList.addEventListener('mousedown', (event) => {
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

        if(badge) badge.classList.remove('d-none');
        suggestionList.style.display = 'none';
    });

    relationSelect.addEventListener('change', updateRelationOptions);

    row.querySelector('.remove-member').addEventListener('click', () => {
        row.remove();
        updateRelationOptions();
        checkEmptyState();
    });
  }

  function createMemberRow(index) {
    const row = document.createElement('div');
    row.classList.add('card', 'mb-3', 'border-0', 'shadow-sm', 'member-row');
    
    const listId = `residentsList_${index}`;

    row.innerHTML = `
      <div class="card-body position-relative">
        <button type="button" class="btn-close position-absolute top-0 end-0 m-3 remove-member" aria-label="Remove"></button>
        
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label small text-muted fw-bold">Full Name <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                    <input type="text" 
                        name="members[${index}][full_name]" 
                        class="form-control name-input" 
                        placeholder="Search resident or type name" 
                        required 
                        autocomplete="off">
                </div>
                <input type="hidden" name="members[${index}][resident_id]" class="resident-id">
                <div class="form-text small text-primary resident-badge d-none"><i class="bi bi-check-circle-fill me-1"></i> Registered Resident</div>
            </div>

            <div class="col-md-6">
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

            <div class="col-md-6">
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
        adults++; // Default to adult if no birth date
      }

      if (isPwd) pwd++;
    });

    document.getElementById('total_members').value = total;
    document.getElementById('total_adults').value = adults;
    document.getElementById('total_minors').value = minors;
    document.getElementById('total_senior_citizens').value = seniors;
    document.getElementById('total_pwd').value = pwd;

    // Auto-check indicators
    if (seniors > 0) document.getElementById('has_senior_citizen').checked = true;
    if (pwd > 0) document.getElementById('has_pwd').checked = true;
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
      calculateTotals();
      updateRelationOptions();
  }

  addBtn.addEventListener('click', () => {
    const newRow = createMemberRow(memberIndex);
    container.appendChild(newRow);
    bindRow(newRow);
    updateRelationOptions();
    
    // Add event listeners for auto-calculation
    newRow.querySelector('.birth-date').addEventListener('change', calculateTotals);
    newRow.querySelector('.member-pwd').addEventListener('change', calculateTotals);
    
    memberIndex++;
    checkEmptyState();
  });

  // Add first row by default
  addBtn.click();
});
</script>
@endsection