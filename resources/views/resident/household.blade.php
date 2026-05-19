@extends('layouts.resident', ['currentRoute' => 'resident.household', 'residentName' => ($resident->first_name ?? 'Resident')])

@section('content')
<div class="container">
  <!-- Page Header -->
  <div class="glass p-4 mb-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
      <div>
        <h2 class="mb-1"><i class="bi bi-house me-2"></i>Household Registry</h2>
        <p class="opacity-75 mb-0">Manage your household information and members.</p>
        @if($household)
        <div class="mt-2">
          <span class="badge badge-glass-info">Household Code: {{ $household->household_code }}</span>
        </div>
        @endif
      </div>
      <div class="d-flex gap-3">
         @if(!$resident->household_id)
         <button type="button" class="btn btn-glass-primary" id="btnCreateHouseholdModal">
           <i class="bi bi-plus-lg me-2"></i>Create Household
         </button>
         <button type="button" class="btn btn-glass-primary" id="btnJoinHousehold">
           <i class="bi bi-house-add me-2"></i>Join Household
         </button>
          @else
          <button type="button" class="btn btn-glass" id="btnEditHousehold">
            <i class="bi bi-pencil me-2"></i>Edit Household
          </button>
          @endif
      </div>
    </div>
  </div>

  <!-- Household Details View -->
  @if($household)
  <div class="row g-4" id="householdView">
    <!-- Household Basic Info -->
    <div class="col-md-6">
      <div class="glass-card h-100">
        <div class="glass-card-header">
          <h6 class="mb-0 fw-bold"><i class="bi bi-house-fill me-2"></i>Basic Household Information</h6>
        </div>
        <div class="glass-card-body">
          <div class="mb-3">
            <label class="form-label-glass">Address / Blk & Lot</label>
            <div class="p-2 rounded" style="background: color-mix(in srgb, var(--mis-blue-dark) 5%, transparent 95%); border: 1px dashed rgba(255,215,0,0.3);">
              {{ $household->address_line ?? 'Not set' }}
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label-glass">Street</label>
            <div class="p-2 rounded" style="background: color-mix(in srgb, var(--mis-blue-dark) 5%, transparent 95%); border: 1px dashed rgba(255,215,0,0.3);">
              {{ $household->street ?? 'Not set' }}
            </div>
          </div>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label-glass">Phase</label>
              <div class="p-2 rounded" style="background: color-mix(in srgb, var(--mis-blue-dark) 5%, transparent 95%); border: 1px dashed rgba(255,215,0,0.3);">
                {{ $household->phase ?? 'Not set' }}
              </div>
            </div>
            <div class="col-md-6">
              <label class="form-label-glass">Contact No.</label>
              <div class="p-2 rounded" style="background: color-mix(in srgb, var(--mis-blue-dark) 5%, transparent 95%); border: 1px dashed rgba(255,215,0,0.3);">
                +63{{ $household->contact_no ?? 'Not set' }}
              </div>
            </div>
          </div>
          <div class="row g-3 mt-2">
            <div class="col-md-6">
              <label class="form-label-glass">Household Type</label>
              <div class="p-2 rounded" style="background: color-mix(in srgb, var(--mis-blue-dark) 5%, transparent 95%); border: 1px dashed rgba(255,215,0,0.3);">
                {{ $household->household_type ?? 'Not set' }}
              </div>
            </div>
            <div class="col-md-6">
              <label class="form-label-glass">Homeownership Type</label>
              <div class="p-2 rounded" style="background: color-mix(in srgb, var(--mis-blue-dark) 5%, transparent 95%); border: 1px dashed rgba(255,215,0,0.3);">
                {{ $household->homeownership_type ?? 'Not set' }}
              </div>
            </div>
          </div>
          <div class="row g-3 mt-2">
             <div class="col-md-6">
               <label class="form-label-glass">Total Members</label>
               <div class="p-2 rounded" style="background: color-mix(in srgb, var(--mis-blue-dark) 5%, transparent 95%); border: 1px dashed rgba(255,215,0,0.3);">
                 {{ $household->total_members ?? 0 }}
               </div>
             </div>
             <div class="col-md-6">
               <label class="form-label-glass">Registered Pets</label>
               <div class="p-2 rounded" style="background: color-mix(in srgb, var(--mis-blue-dark) 5%, transparent 95%); border: 1px dashed rgba(255,215,0,0.3);">
                 {{ $household->registered_pets_count ?? 0 }}
               </div>
             </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Housing & Utilities -->
    <div class="col-md-6">
      <div class="glass-card h-100">
        <div class="glass-card-header">
          <h6 class="mb-0 fw-bold"><i class="bi bi-building me-2"></i>Housing & Utilities</h6>
        </div>
        <div class="glass-card-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label-glass">House Type</label>
              <div class="p-2 rounded" style="background: color-mix(in srgb, var(--mis-blue-dark) 5%, transparent 95%); border: 1px dashed rgba(255,215,0,0.3);">
                {{ $household->house_type ?? 'Not set' }}
              </div>
            </div>
            <div class="col-md-6">
              <label class="form-label-glass">Facilities Available</label>
              <div class="d-flex flex-wrap gap-2">
                 @if($household->has_toilet ?? false)
                 <span class="badge badge-glass-info">Toilet</span>
                 @endif
                 @if($household->has_bathroom ?? false)
                 <span class="badge badge-glass-info">Bathroom</span>
                 @endif
                 @if($household->has_kitchen ?? false)
                 <span class="badge badge-glass-info">Kitchen</span>
                 @endif
                 @if($household->has_garage ?? false)
                 <span class="badge badge-glass-info">Garage</span>
                 @endif
                 @if($household->has_electricity ?? false)
                 <span class="badge badge-glass-success">Has Electricity</span>
                 @endif
              </div>
            </div>
          </div>
          <div class="mt-3">
            <label class="form-label-glass">Disaster Risk Level</label>
            <div class="p-2 rounded" style="background: color-mix(in srgb, var(--mis-blue-dark) 5%, transparent 95%); border: 1px dashed rgba(255,215,0,0.3);">
              {{ $household->disaster_risk_level ?? 'Not set' }}
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Socio-Economic -->
    <div class="col-md-6">
      <div class="glass-card">
        <div class="glass-card-header">
          <h6 class="mb-0 fw-bold"><i class="bi bi-currency-dollar me-2"></i>Socio-Economic Information</h6>
        </div>
        <div class="glass-card-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label-glass">Monthly Income Range</label>
              <div class="p-2 rounded" style="background: color-mix(in srgb, var(--mis-blue-dark) 5%, transparent 95%); border: 1px dashed rgba(255,215,0,0.3);">
                {{ $household->monthly_income_range ?? 'Not set' }}
              </div>
            </div>
            <div class="col-md-6">
              <label class="form-label-glass">Employment Status</label>
              <div class="p-2 rounded" style="background: color-mix(in srgb, var(--mis-blue-dark) 5%, transparent 95%); border: 1px dashed rgba(255,215,0,0.3);">
                {{ $household->employment_status ?? 'Not set' }}
              </div>
            </div>
          </div>
          <div class="mt-3">
            <label class="form-label-glass">Primary Income Source</label>
            <div class="p-2 rounded" style="background: color-mix(in srgb, var(--mis-blue-dark) 5%, transparent 95%); border: 1px dashed rgba(255,215,0,0.3);">
              {{ $household->primary_income_source ?? 'Not set' }}
            </div>
          </div>
          <div class="row g-3 mt-2">
             <div class="col-md-6">
               @if($household->is_4ps_beneficiary ?? false)
               <span class="badge badge-glass-success">4PS Beneficiary</span>
               @else
               <span class="badge badge-glass-secondary">Not 4PS</span>
               @endif
             </div>
             <div class="col-md-6">
               @if($household->is_indigent ?? false)
               <span class="badge badge-glass-success">Indigent</span>
               @else
               <span class="badge badge-glass-secondary">Not Indigent</span>
               @endif
             </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Health & Community -->
    <div class="col-md-6">
      <div class="glass-card">
        <div class="glass-card-header">
          <h6 class="mb-0 fw-bold"><i class="bi bi-heart-pulse me-2"></i>Health & Community Indicators</h6>
        </div>
        <div class="glass-card-body">
          <div class="row g-3">
             <div class="col-md-6">
               @if($household->has_pregnant_member ?? false)
               <span class="badge badge-glass-success">Has Pregnant Member</span>
               @else
               <span class="badge badge-glass-secondary">No Pregnant Member</span>
               @endif
             </div>
             <div class="col-md-6">
               @if($household->has_senior_citizen ?? false)
               <span class="badge badge-glass-success">Has Senior Citizen</span>
               @else
               <span class="badge badge-glass-secondary">No Senior Citizen</span>
               @endif
             </div>
             <div class="col-md-6">
               @if($household->has_pwd ?? false)
               <span class="badge badge-glass-success">Has PWD Member</span>
               @else
               <span class="badge badge-glass-secondary">No PWD Member</span>
               @endif
             </div>
             <div class="col-md-6">
               @if($household->has_chronic_illness ?? false)
               <span class="badge badge-glass-warning">Has Chronic Illness</span>
               @else
               <span class="badge badge-glass-secondary">No Chronic Illness</span>
               @endif
             </div>
          </div>
          <hr style="border-color: rgba(255, 215, 0, 0.2);">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label-glass">Barangay Program Participation</label>
              <div class="p-2 rounded" style="background: color-mix(in srgb, var(--mis-blue-dark) 5%, transparent 95%); border: 1px dashed rgba(255,215,0,0.3);">
                {{ $household->barangay_program_participation ?? 'None' }}
              </div>
            </div>
            
          </div>
          
        </div>

        
      </div>

     
</div>
 <!-- Household Members -->
       
      <div class="glass-card mt-4">
        <div class="glass-card-header d-flex justify-content-between align-items-center">
          <h6 class="mb-0 fw-bold"><i class="bi bi-people-fill me-2"></i>Household Members</h6>
          <button type="button" class="btn btn-glass btn-sm" id="addMemberBtnModal">
            <i class="bi bi-plus-lg me-1"></i> Add Member
          </button>
        </div>
        <div class="glass-card-body">
          <div class="table-responsive">
            <table class="glass-table">
              <thead>
                <tr>
                  <th style="width: 25%;">Name</th>
                  <th style="width: 18%;">Relationship</th>
                  <th style="width: 25%;">Email</th>
                  <th style="width: 17%;">Birth Date</th>
                  <th style="width: 15%; text-align: center;">PWD</th>
                </tr>
              </thead>
              <tbody>
                @forelse($household->members as $member)
                <tr>
                  <td class="fw-bold">{{ $member->first_name }} {{ $member->last_name }}</td>
                  <td>
                    <span class="badge {{ $member->relationship === 'Head' ? 'bg-primary' : 'badge-glass-secondary' }} px-3 py-2">
                      {{ $member->relationship }}
                    </span>
                  </td>
                  <td class="align-middle">{{ $member->email ?? '-' }}</td>
                  <td class="align-middle">{{ $member->birth_date ? \Carbon\Carbon::parse($member->birth_date)->format('M d, Y') : '-' }}</td>
                  <td class="text-center align-middle">
                    @if($member->is_pwd)
                      <span class="text-warning fs-5" title="Person with Disability"><i class="bi bi-check-circle-fill"></i></span>
                    @else
                      <span class="text-muted opacity-25 fs-5"><i class="bi bi-circle"></i></span>
                    @endif
                  </td>
                </tr>
                @empty
                <tr>
                  <td colspan="5" class="text-center py-4">
                    <i class="bi bi-people fs-1 opacity-50 mb-2"></i>
                    <p class="opacity-75">No household members yet.</p>
                  </td>
                </tr>
                @endforelse
              </tbody>
            </table>
           </div>
          </div>
       </div>
        @if(count($joinRequests) > 0)
      <div class="row g-4 mt-4">
        <div class="col-12">
          <div class="glass-card">
            <div class="glass-card-header">
              <h6 class="mb-0 fw-bold"><i class="bi bi-person-plus me-2"></i>Pending Join Requests</h6>
            </div>
            <div class="glass-card-body">
              <div class="table-responsive">
                <table class="glass-table">
                  <thead>
                    <tr>
                      <th style="width: 25%;">Name</th>
                      <th style="width: 45%;">Message</th>
                      <th style="width: 30%; text-align: right;">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse($joinRequests as $request)
                    <tr>
                      <td>
                        <div class="fw-bold">{{ $request->resident->first_name ?? 'Unknown' }} {{ $request->resident->last_name ?? '' }}</div>
                        <small class="opacity-50 d-block">{{ $request->resident->email ?? 'No email' }}</small>
                      </td>
                      <td class="small text-wrap" style="max-width: 300px;"><em class="text-muted">"{{ $request->message ?? 'No message' }}"</em></td>
                      <td class="text-end">
                        <button type="button" class="btn btn-glass-success btn-sm me-2" onclick="approveJoinRequest({{ $request->id }})">
                          <i class="bi bi-check-lg"></i> Approve
                        </button>
                        <button type="button" class="btn btn-glass-danger btn-sm" onclick="rejectJoinRequest({{ $request->id }})">
                          <i class="bi bi-x-lg"></i> Reject
                        </button>
                      </td>
                    </tr>
                    @empty
                    <tr>
                      <td colspan="3" class="text-center py-4">
                        <i class="bi bi-inbox fs-1 opacity-50 mb-2"></i>
                        <p class="opacity-75">No pending join requests.</p>
                      </td>
                    </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
        @endif
     </div>
   @else
   <div class="glass p-5 text-center">
     <i class="bi bi-house fs-1 text-muted mb-3"></i>
     <h4 class="mt-3">No Household Registered</h4>
     <p class="text-muted mb-4">Create a household to manage your residence information.</p>
     <button class="btn btn-glass-primary btn-lg px-4" id="btnCreateHouseholdNoHousehold">
       <i class="bi bi-plus-lg me-2"></i>Create Household
     </button>
   </div>
   @endif
@endsection

<!-- Create/Edit Household Modal -->
<div class="modal fade" id="householdFormModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content glass-modal">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-house me-2"></i>{{ $household ? 'Edit Household' : 'Create Household' }}</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="householdForm" method="POST" action="{{ $household ? route('resident.household.update', $household) : route('resident.household.store') }}">
          @csrf
          @if($household)
          @method('PUT')
          @endif
          <div class="row g-3">
            <div class="col-12">
              <h6 class="mb-3 mt-2 fs-6"><i class="bi bi-house me-2"></i>Basic Information</h6>
            </div>
            <div class="col-md-6">
              <label class="form-label-glass">Address / Blk & Lot <span class="text-danger">*</span></label>
              <input type="text" name="address_line" class="glass-input form-control" value="{{ $household->address_line ?? '' }}" placeholder="e.g., Blk 1 Lot 2" required>
            </div>
            <div class="col-md-6">
              <label class="form-label-glass">Street</label>
              <input type="text" name="street" class="glass-input form-control" value="{{ $household->street ?? '' }}" placeholder="e.g., Sampaguita St.">
            </div>
            <div class="col-md-6">
              <label class="form-label-glass">Phase</label>
              <input type="number" name="phase" class="glass-input form-control" value="{{ $household->phase ?? '' }}" placeholder="1" min="1">
            </div>
            <div class="col-md-6">
              <label class="form-label-glass">Contact No.</label>
              <div class="input-group">
                <span class="input-group-text">+63</span>
                <input type="text" name="contact_no" class="glass-input form-control" value="{{ $household->contact_no ?? '' }}" placeholder="917 123 4567" maxlength="12">
              </div>
            </div>
            <div class="col-md-6">
              <label class="form-label-glass">Household Type</label>
              <select name="household_type" class="glass-select form-select">
                <option value="">-- Select --</option>
                <option value="Family" {{ ($household->household_type ?? '') === 'Family' ? 'selected' : '' }}>Family</option>
                <option value="Extended Family" {{ ($household->household_type ?? '') === 'Extended Family' ? 'selected' : '' }}>Extended Family</option>
                <option value="Boarding / Rental" {{ ($household->household_type ?? '') === 'Boarding / Rental' ? 'selected' : '' }}>Boarding / Rental</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label-glass">Homeownership Type</label>
              <select name="homeownership_type" class="glass-select form-select">
                <option value="">-- Select --</option>
                <option value="Owned" {{ ($household->homeownership_type ?? '') === 'Owned' ? 'selected' : '' }}>Owned</option>
                <option value="Rented" {{ ($household->homeownership_type ?? '') === 'Rented' ? 'selected' : '' }}>Rented</option>
                <option value="Informal Settler" {{ ($household->homeownership_type ?? '') === 'Informal Settler' ? 'selected' : '' }}>Informal Settler</option>
              </select>
            </div>
            <div class="col-12">
              <h6 class="mb-3 mt-4 fs-6"><i class="bi bi-people me-2"></i>Member Statistics</h6>
            </div>
            <div class="col-md-6">
              <label class="form-label-glass">Total Members</label>
              <input type="number" name="total_members" class="glass-input form-control" value="{{ $household->total_members ?? 0 }}" min="0">
            </div>
            <div class="col-md-6">
              <div class="row g-2">
                <div class="col-6">
                  <label class="form-label-glass">Adults</label>
                  <input type="number" name="total_adults" class="glass-input form-control" value="{{ $household->total_adults ?? 0 }}" min="0">
                </div>
                <div class="col-6">
                  <label class="form-label-glass">Minors</label>
                  <input type="number" name="total_minors" class="glass-input form-control" value="{{ $household->total_minors ?? 0 }}" min="0">
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <label class="form-label-glass">Senior Citizens</label>
              <input type="number" name="total_senior_citizens" class="glass-input form-control" value="{{ $household->total_senior_citizens ?? 0 }}" min="0">
            </div>
            <div class="col-md-4">
              <label class="form-label-glass">Total PWD</label>
              <input type="number" name="total_pwd" class="glass-input form-control" value="{{ $household->total_pwd ?? 0 }}" min="0">
            </div>
            <div class="col-md-4">
              <label class="form-label-glass">Registered Pets</label>
              <input type="number" name="registered_pets_count" class="glass-input form-control" value="{{ $household->registered_pets_count ?? 0 }}" min="0">
            </div>
            <div class="col-12">
              <h6 class="mb-3 mt-4 fs-6"><i class="bi bi-building me-2"></i>Housing Details</h6>
            </div>
            <div class="col-md-6">
              <label class="form-label-glass">House Type</label>
              <select name="house_type" class="glass-select form-select">
                <option value="">-- Select --</option>
                <option value="Concrete" {{ ($household->house_type ?? '') === 'Concrete' ? 'selected' : '' }}>Concrete</option>
                <option value="Semi-concrete" {{ ($household->house_type ?? '') === 'Semi-concrete' ? 'selected' : '' }}>Semi-concrete</option>
                <option value="Light materials" {{ ($household->house_type ?? '') === 'Light materials' ? 'selected' : '' }}>Light materials</option>
              </select>
            </div>
            <div class="col-md-12">
              <label class="form-label-glass">Facilities Available</label>
              <div class="row g-2">
                <div class="col-6 col-lg-4">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="has_toilet" value="1" id="has_toilet" {{ ($household->has_toilet ?? false) ? 'checked' : '' }}>
                    <label class="form-check-label" for="has_toilet">Toilet</label>
                  </div>
                </div>
                <div class="col-6 col-lg-4">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="has_bathroom" value="1" id="has_bathroom" {{ ($household->has_bathroom ?? false) ? 'checked' : '' }}>
                    <label class="form-check-label" for="has_bathroom">Bathroom</label>
                  </div>
                </div>
                <div class="col-6 col-lg-4">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="has_kitchen" value="1" id="has_kitchen" {{ ($household->has_kitchen ?? false) ? 'checked' : '' }}>
                    <label class="form-check-label" for="has_kitchen">Kitchen</label>
                  </div>
                </div>
                <div class="col-6 col-lg-4">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="has_garage" value="1" id="has_garage" {{ ($household->has_garage ?? false) ? 'checked' : '' }}>
                    <label class="form-check-label" for="has_garage">Garage</label>
                  </div>
                </div>
                <div class="col-6 col-lg-4">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="has_electricity" value="1" id="has_electricity" {{ ($household->has_electricity ?? true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="has_electricity">Has Electricity</label>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <label class="form-label-glass">Disaster Risk Level</label>
              <select name="disaster_risk_level" class="glass-select form-select">
                <option value="">-- Select --</option>
                <option value="Low" {{ ($household->disaster_risk_level ?? '') === 'Low' ? 'selected' : '' }}>Low</option>
                <option value="Medium" {{ ($household->disaster_risk_level ?? '') === 'Medium' ? 'selected' : '' }}>Medium</option>
                <option value="High" {{ ($household->disaster_risk_level ?? '') === 'High' ? 'selected' : '' }}>High</option>
              </select>
            </div>
            <div class="col-12">
              <h6 class="mb-3 mt-4 fs-6"><i class="bi bi-currency-dollar me-2"></i>Socio-Economic Information</h6>
            </div>
            <div class="col-md-6">
              <label class="form-label-glass">Monthly Income Range</label>
              <select name="monthly_income_range" class="glass-select form-select">
                <option value="">-- Select --</option>
                <option value="Below 10,000" {{ ($household->monthly_income_range ?? '') === 'Below 10,000' ? 'selected' : '' }}>Below ₱10,000</option>
                <option value="10,000 - 30,000" {{ ($household->monthly_income_range ?? '') === '10,000 - 30,000' ? 'selected' : '' }}>₱10,000 - ₱30,000</option>
                <option value="30,000 - 50,000" {{ ($household->monthly_income_range ?? '') === '30,000 - 50,000' ? 'selected' : '' }}>₱30,000 - ₱50,000</option>
                <option value="Above 50,000" {{ ($household->monthly_income_range ?? '') === 'Above 50,000' ? 'selected' : '' }}>Above ₱50,000</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label-glass">Employment Status</label>
              <select name="employment_status" class="glass-select form-select">
                <option value="">-- Select --</option>
                <option value="Employed" {{ ($household->employment_status ?? '') === 'Employed' ? 'selected' : '' }}>Employed</option>
                <option value="Self-employed" {{ ($household->employment_status ?? '') === 'Self-employed' ? 'selected' : '' }}>Self-employed</option>
                <option value="Unemployed" {{ ($household->employment_status ?? '') === 'Unemployed' ? 'selected' : '' }}>Unemployed</option>
                <option value="Student" {{ ($household->employment_status ?? '') === 'Student' ? 'selected' : '' }}>Student</option>
                <option value="Retired" {{ ($household->employment_status ?? '') === 'Retired' ? 'selected' : '' }}>Retired</option>
              </select>
            </div>
            <div class="col-md-12">
              <label class="form-label-glass">Primary Income Source</label>
              <input type="text" name="primary_income_source" class="glass-input form-control" value="{{ $household->primary_income_source ?? '' }}" placeholder="e.g., Salary, Business, Remittance">
            </div>
            <div class="col-md-6">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="is_4ps_beneficiary" value="1" id="is_4ps_beneficiary" {{ ($household->is_4ps_beneficiary ?? false) ? 'checked' : '' }}>
                <label class="form-check-label" for="is_4ps_beneficiary">4PS Beneficiary</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="is_indigent" value="1" id="is_indigent" {{ ($household->is_indigent ?? false) ? 'checked' : '' }}>
                <label class="form-check-label" for="is_indigent">Indigent</label>
              </div>
            </div>
            <div class="col-12">
              <h6 class="mb-3 mt-4 fs-6"><i class="bi bi-heart-pulse me-2"></i>Health & Community Indicators</h6>
            </div>
            <div class="col-md-6">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="has_pregnant_member" value="1" id="has_pregnant_member" {{ ($household->has_pregnant_member ?? false) ? 'checked' : '' }}>
                <label class="form-check-label" for="has_pregnant_member">Pregnant Member</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="has_senior_citizen" value="1" id="has_senior_citizen" {{ ($household->has_senior_citizen ?? false) ? 'checked' : '' }}>
                <label class="form-check-label" for="has_senior_citizen">Senior Citizen</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="has_pwd" value="1" id="has_pwd" {{ ($household->has_pwd ?? false) ? 'checked' : '' }}>
                <label class="form-check-label" for="has_pwd">PWD Member</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="has_chronic_illness" value="1" id="has_chronic_illness" {{ ($household->has_chronic_illness ?? false) ? 'checked' : '' }}>
                <label class="form-check-label" for="has_chronic_illness">Chronic Illness</label>
              </div>
            </div>
            <div class="col-12">
              <h6 class="mb-3 mt-4 fs-6"><i class="bi bi-people me-2"></i>Community Participation</h6>
            </div>
            <div class="col-md-12">
              <label class="form-label-glass">Barangay Program Participation</label>
              <textarea name="barangay_program_participation" class="glass-input form-control" rows="3" placeholder="e.g., Clean-up drives, Health programs">{{ $household->barangay_program_participation ?? '' }}</textarea>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-glass" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-glass-primary" form="householdForm">Save Household</button>
      </div>
    </div>
  </div>
</div>

<!-- Add Member Modal -->
<div class="modal fade" id="addMemberModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content glass-modal">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-person-plus me-2"></i>Add Member by Account</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="addMemberForm">
          <div class="mb-3">
            <label class="form-label-glass">Resident Account No <span class="text-danger">*</span></label>
            <input type="text" class="glass-input form-control" id="accountNo" name="account_no" placeholder="Enter resident account number" required>
            <small class="opacity-75">Enter the resident's account number to add them to your household.</small>
          </div>
          <div class="mb-3">
            <label class="form-label-glass">Relationship <span class="text-danger">*</span></label>
            <select class="glass-select form-select" id="relationship" name="relationship" required>
              <option value="">Select relationship</option>
              <option value="Head">Head of Family</option>
              <option value="Spouse">Spouse</option>
              <option value="Child">Child</option>
              <option value="Parent">Parent</option>
              <option value="Sibling">Sibling</option>
              <option value="Grandparent">Grandparent</option>
              <option value="Grandchild">Grandchild</option>
              <option value="Relative">Other Relative</option>
              <option value="Other">Other</option>
            </select>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-glass" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-glass-primary" onclick="addMemberByAccount()">Add Member</button>
      </div>
    </div>
  </div>
</div>

<!-- Join Household Modal -->
<div class="modal fade" id="joinHouseholdModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content glass-modal">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-house-add me-2"></i>Join Household</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="joinRequestForm">
          <div class="mb-3">
            <label class="form-label-glass">Enter Household Code <span class="text-danger">*</span></label>
            <input type="text" class="glass-input form-control" id="joinHouseholdCode" name="household_code" placeholder="e.g. HH-2024-000001" required>
            <small class="opacity-75">Enter the household code provided by the household head.</small>
          </div>
          <div class="mb-3">
            <label class="form-label-glass">Optional Message</label>
            <textarea class="glass-input form-control" id="joinMessage" name="message" rows="3" placeholder="Add a message to the household head (optional)"></textarea>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-glass" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-glass-primary" onclick="sendJoinRequest()">Send Request</button>
      </div>
    </div>
  </div>
</div>

@push('styles')
<style>
/* Glass card component */
.glass-card {
  background: var(--card-elevation-1);
  backdrop-filter: blur(16px);
  -webkit-backdrop-filter: blur(16px);
  border: 1px solid color-mix(in srgb, var(--mis-yellow) 25%, transparent 75%);
  border-radius: 16px;
  padding: 24px;
  transition: all 0.3s;
  position: relative;
  overflow: hidden;
}

.glass-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: var(--gradient-overlay-light);
  pointer-events: none;
  border-radius: inherit;
}

.glass-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 12px 36px color-mix(in srgb, var(--mis-blue) 30%, transparent 70%);
  border-color: color-mix(in srgb, var(--mis-yellow) 40%, transparent 60%);
  background: var(--card-elevation-2);
}

.glass-card-header {
  background: color-mix(in srgb, var(--mis-yellow) 15%, transparent 85%);
  border-bottom: 1px solid color-mix(in srgb, var(--mis-yellow) 30%, transparent 70%);
  color: var(--text-primary);
  font-weight: 600;
  padding: 16px 20px;
}

.glass-card-body {
  color: var(--text-primary);
  padding: 20px;
}

/* Glass inputs */
.glass-input, .glass-select {
  background: var(--input-bg);
  border: 1px solid var(--input-border);
  color: var(--mis-blue-dark);
  padding: 10px 14px;
  border-radius: 10px;
}

.glass-input:focus, .glass-select:focus {
  background: color-mix(in srgb, var(--mis-white) 30%, var(--mis-blue-dark) 70%);
  border-color: var(--mis-yellow);
  box-shadow: 0 0 0 3px var(--input-focus-glow);
}

.glass-input::placeholder {
  color: color-mix(in srgb, var(--mis-blue-dark) 60%, transparent 40%);
}

.glass-select option {
  background: var(--mis-blue-dark);
  color: var(--mis-white);
}

/* Form labels */
.form-label-glass {
  color: var(--text-primary) !important;
  font-weight: 500;
  margin-bottom: 8px;
  font-size: 0.875rem;
}

/* View mode display boxes */
.view-display {
  background: color-mix(in srgb, var(--mis-blue-dark) 8%, transparent 92%);
  border: 1px dashed color-mix(in srgb, var(--mis-yellow) 25%, transparent 75%);
  border-radius: 8px;
  padding: 10px 12px;
  color: var(--text-secondary);
}

/* Badge styles */
.badge-glass-info {
  background: color-mix(in srgb, #3b82f6 30%, transparent 70%);
  color: #93c5fd;
  border: 1px solid color-mix(in srgb, #3b82f6 40%, transparent 60%);
  padding: 6px 12px;
  border-radius: 50px;
  font-size: 0.75rem;
}

.badge-glass-success {
  background: color-mix(in srgb, #28a745 30%, transparent 70%);
  color: #4ade80;
  border: 1px solid color-mix(in srgb, #28a745 40%, transparent 60%);
  padding: 6px 12px;
  border-radius: 50px;
  font-size: 0.75rem;
}

.badge-glass-warning {
  background: color-mix(in srgb, var(--mis-yellow) 30%, transparent 70%);
  color: var(--mis-yellow);
  border: 1px solid color-mix(in srgb, var(--mis-yellow) 40%, transparent 60%);
  padding: 6px 12px;
  border-radius: 50px;
  font-size: 0.75rem;
}

.badge-glass-secondary {
  background: rgba(156, 163, 175, 0.2);
  color: #d1d5db;
  border: 1px solid rgba(156, 163, 175, 0.3);
  padding: 6px 12px;
  border-radius: 50px;
  font-size: 0.75rem;
}

/* Table styling */
.glass-table {
  width: 100%;
  border-collapse: collapse;
  color: var(--text-primary);
}

.glass-table thead th {
  background: color-mix(in srgb, var(--mis-yellow) 20%, transparent 80%);
  color: var(--mis-blue-dark);
  font-weight: 600;
  border-bottom: 2px solid color-mix(in srgb, var(--mis-yellow) 35%, transparent 65%);
  padding: 12px 16px;
  text-align: left;
}

.glass-table tbody td {
  background: color-mix(in srgb, var(--mis-blue-dark) 5%, transparent 95%);
  border-bottom: 1px solid color-mix(in srgb, var(--mis-blue) 15%, transparent 85%);
  padding: 12px 16px;
  color: var(--text-secondary);
}

.glass-table tbody tr:hover td {
  background: color-mix(in srgb, var(--mis-blue) 10%, transparent 90%) !important;
}

/* PWD checkbox styling */
.form-check-input:checked {
  background-color: var(--mis-yellow);
  border-color: var(--mis-yellow);
}

.form-check-input {
  background-color: color-mix(in srgb, var(--mis-blue-dark) 10%, transparent 90%);
  border: 1px solid color-mix(in srgb, var(--mis-yellow) 30%, transparent 70%);
}

.form-check-label {
  color: var(--text-secondary);
  cursor: pointer;
}
</style>
@endpush

@push('scripts')
<script>
// Open modals
document.getElementById('btnCreateHouseholdModal')?.addEventListener('click', () => {
  new bootstrap.Modal(document.getElementById('householdFormModal')).show();
});

document.getElementById('btnEditHousehold')?.addEventListener('click', () => {
  new bootstrap.Modal(document.getElementById('householdFormModal')).show();
});

document.getElementById('btnJoinHousehold')?.addEventListener('click', () => {
  new bootstrap.Modal(document.getElementById('joinHouseholdModal')).show();
});

document.getElementById('addMemberBtnModal')?.addEventListener('click', () => {
  new bootstrap.Modal(document.getElementById('addMemberModal')).show();
});

// PWD toggle
document.addEventListener('change', (e) => {
  if (e.target.classList.contains('member-pwd-toggle')) {
    const memberId = e.target.getAttribute('data-member-id');
    if (memberId) {
        fetch(`/resident/household/member/${memberId}/toggle-pwd`, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({ is_pwd: e.target.checked })
        }).then(response => {
          return response.json().catch(() => {
            return response.text().then(text => {
              console.error('Response is not JSON:', text);
              throw new Error('Invalid JSON response from server: ' + text.substring(0, 200));
            });
          }).then(data => ({response, data}));
        }).then(({response, data}) => {
          if (!response.ok) {
            alert(data.message || 'Failed to update PWD status');
            e.target.checked = !e.target.checked;
          }
        }).catch((error) => {
          console.error('Fetch error:', error);
          alert('Error updating PWD status: ' + error.message);
          e.target.checked = !e.target.checked;
        });
    }
  }

  // Handle relationship change
  if (e.target.classList.contains('member-relation')) {
    const memberId = e.target.getAttribute('data-member-id');
    if (memberId) {
        fetch(`/resident/household/member/${memberId}/update-relationship`, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({ relationship: e.target.value })
        }).then(response => {
          return response.json().catch(() => {
            return response.text().then(text => {
              console.error('Response is not JSON:', text);
              throw new Error('Invalid JSON response from server: ' + text.substring(0, 200));
            });
          }).then(data => ({response, data}));
        }).then(({response, data}) => {
          if (!response.ok) {
            alert(data.message || 'Failed to update relationship');
            location.reload();
          }
        }).catch((error) => {
          console.error('Fetch error:', error);
          alert('Error updating relationship: ' + error.message);
          location.reload();
        });
    }
  }
 });

// Add member by account
function addMemberByAccount() {
  const formData = new FormData(document.getElementById('addMemberForm'));
  fetch('{{ route("resident.household.add-member") }}', {
    method: 'POST',
    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
    body: formData
   }).then(response => {
     return response.json().catch(() => {
       return response.text().then(text => {
         console.error('Response is not JSON:', text);
         throw new Error('Invalid JSON response from server: ' + text.substring(0, 200));
       });
     }).then(data => ({response, data}));
   }).then(({response, data}) => {
     if (response.ok) {
       bootstrap.Modal.getInstance(document.getElementById('addMemberModal')).hide();
       document.getElementById('addMemberForm').reset();
       location.reload();
     } else {
       alert(data.message || 'Failed to add member');
     }
   }).catch((error) => {
     console.error('Fetch error:', error);
     alert('An error occurred: ' + error.message);
   });
}

// Join household request
function sendJoinRequest() {
  const formData = new FormData(document.getElementById('joinRequestForm'));
  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
  if (!csrfToken) { alert('CSRF token not found.'); return; }
   fetch('{{ route("resident.household.join") }}', {
     method: 'POST',
     headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
     body: formData
   }).then(response => {
     return response.json().catch(() => {
       return response.text().then(text => {
         console.error('Response is not JSON:', text);
         throw new Error('Invalid JSON response from server: ' + text.substring(0, 200));
       });
     }).then(data => ({response, data}));
   }).then(({response, data}) => {
     if (response.ok && data.success) {
       bootstrap.Modal.getInstance(document.getElementById('joinHouseholdModal')).hide();
       document.getElementById('joinRequestForm').reset();
       location.reload();
     } else {
       alert(data.message || 'Failed to send request');
     }
   }).catch((error) => {
     console.error('Fetch error:', error);
     alert('Error sending request: ' + error.message);
   });
}

// Submit household form via AJAX
document.getElementById('householdForm')?.addEventListener('submit', function(e) {
  e.preventDefault();
  const formData = new FormData(this);
  const url = this.action;
  const method = this.method;

    fetch(url, {
      method: method,
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        'Accept': 'application/json'
      },
      body: formData
    }).then(response => {
      return response.json().catch(() => {
        return response.text().then(text => {
          console.error('Response is not JSON:', text);
          throw new Error('Invalid JSON response from server: ' + text.substring(0, 200));
        });
      }).then(data => ({response, data}));
    }).then(({response, data}) => {
      if (response.ok) {
        bootstrap.Modal.getInstance(document.getElementById('householdFormModal')).hide();
        location.reload();
      } else {
        alert(data.message || 'Failed to save household');
      }
    }).catch((error) => {
      console.error('Fetch error:', error);
      alert('An error occurred. Please try again: ' + error.message);
    });
});

// Edit member function
function editMember(memberId) {
  // Find the member data
  const members = @json($household ? $household->members : []);
  const member = members.find(m => m.id == memberId);
  if (member) {
    // Pre-fill the add member modal with member data
    document.getElementById('accountNo').value = member.resident?.account_no || '';
    document.getElementById('relationship').value = member.relationship;
    // Store member ID for update
    document.getElementById('addMemberForm').setAttribute('data-edit-mode', 'true');
    document.getElementById('addMemberForm').setAttribute('data-member-id', memberId);
    new bootstrap.Modal(document.getElementById('addMemberModal')).show();
  }
}

// Approve or reject join request
function approveJoinRequest(requestId) {
  if (!confirm('Are you sure you want to approve this join request?')) {
    return;
  }

  fetch(`/resident/household/request/${requestId}/approve`, {
    method: 'POST',
    headers: {
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      'Accept': 'application/json'
    }
  }).then(response => {
    return response.json().catch(() => {
      return response.text().then(text => {
        console.error('Response is not JSON:', text);
        throw new Error('Invalid JSON response from server: ' + text.substring(0, 200));
      });
    }).then(data => ({response, data}));
  }).then(({response, data}) => {
    if (response.ok) {
      alert('Join request approved successfully!');
      location.reload();
    } else {
      alert(data.message || 'Failed to approve request');
    }
  }).catch((error) => {
    console.error('Fetch error:', error);
    alert('Error approving request: ' + error.message);
  });
}

function rejectJoinRequest(requestId) {
  if (!confirm('Are you sure you want to reject this join request?')) {
    return;
  }

  fetch(`/resident/household/request/${requestId}/reject`, {
    method: 'POST',
    headers: {
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      'Accept': 'application/json'
    }
  }).then(response => {
    return response.json().catch(() => {
      return response.text().then(text => {
        console.error('Response is not JSON:', text);
        throw new Error('Invalid JSON response from server: ' + text.substring(0, 200));
      });
    }).then(data => ({response, data}));
  }).then(({response, data}) => {
    if (response.ok) {
      alert('Join request rejected successfully!');
      location.reload();
    } else {
      alert(data.message || 'Failed to reject request');
    }
  }).catch((error) => {
    console.error('Fetch error:', error);
    alert('Error rejecting request: ' + error.message);
  });
}
</script>
@endpush
