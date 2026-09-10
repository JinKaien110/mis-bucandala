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

  <!-- Household Members -->
  <div class="glass-card mb-4">
    <div class="glass-card-header d-flex justify-content-between align-items-center">
      <h6 class="mb-0 fw-bold"><i class="bi bi-people-fill me-2"></i>Household Members</h6>
      @php
        $isHead = false;
        try {
          $isHead = $household && $resident
            ? $household->members()
                ->where('resident_id', $resident->id)
                ->where('relationship', 'Head')
                ->exists()
            : false;
        } catch (\Throwable $e) {
          $isHead = false;
        }
      @endphp
      @if($isHead)
        <div class="d-flex gap-2">
          <button type="button" class="btn btn-glass btn-sm" id="addMemberBtnModal">
            <i class="bi bi-plus-lg me-1"></i> Add Member
          </button>
          <button type="button" class="btn btn-glass-primary btn-sm" onclick="openBulkEditModal()">
            <i class="bi bi-pencil-square me-1"></i> Edit Members
          </button>
        </div>
      @endif
    </div>
    <div class="glass-card-body">
      <div class="table-responsive">
        <table class="glass-table">
          <thead>
            <tr>
              <th style="width: 20%;">Member Name</th>
              <th style="width: 15%;">Account No.</th>
              <th style="width: 15%;">Relationship</th>
              <th style="width: 20%;">Contact & Email</th>
              <th style="width: 10%;">Monthly Income</th>
              <th style="width: 20%; text-align: center;">Resident Indicators</th>
            </tr>
          </thead>
          <tbody>
            @forelse($household->members as $member)
            <tr>
              <td class="fw-bold align-middle">{{ $member->first_name }} {{ $member->last_name }}</td>
              <td class="align-middle">
                <code class="text-info">{{ $member->resident->account_no ?? 'N/A' }}</code>
              </td>
              <td>
                <span class="badge {{ $member->relationship === 'Head' ? 'bg-primary' : 'badge-glass-secondary' }} px-3 py-2">
                  {{ $member->relationship }}
                </span>
              </td>
              <td class="align-middle">
                <div class="small fw-medium">{{ $member->resident->user->email ?? $member->email ?? '-' }}</div>
                <small class="opacity-75"><i class="bi bi-telephone me-1"></i>+63 {{ $member->resident->contact_no ?? '-' }}</small>
              </td>
              <td class="align-middle">
                {{ is_numeric($member->resident->monthly_income ?? null) ? '₱' . number_format($member->resident->monthly_income, 2) : ($member->resident->monthly_income ?? '-') }}
              </td>
              <td class="text-center align-middle">
                <div class="d-flex flex-wrap justify-content-center gap-1">
                  @if($member->resident && $member->resident->solo_parent)
                    <span class="badge badge-glass-info" style="font-size:0.65rem; padding:3px 8px;">Solo Parent</span>
                  @endif
                  @if($member->resident && $member->resident->pwd)
                    <span class="badge badge-glass-warning" style="font-size:0.65rem; padding:3px 8px;">PWD</span>
                  @endif
                  @if(!($member->resident && ($member->resident->solo_parent || $member->resident->pwd)))
                    <span class="text-muted opacity-50" style="font-size:0.85rem;">None</span>
                  @endif
                </div>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="6" class="text-center py-4">
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

  <div class="row g-4" id="householdView">
    <!-- Left Bento Column -->
    <div class="col-lg-7 d-flex flex-column gap-4">
      <!-- Household Basic Info -->
      <div class="glass-card">
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
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label-glass">Phase</label>
              <div class="p-2 rounded" style="background: color-mix(in srgb, var(--mis-blue-dark) 5%, transparent 95%); border: 1px dashed rgba(255,215,0,0.3);">
                {{ !empty($household->phase) ? $household->phase : 'None' }}
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
            <div class="col-md-12">
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
                 {{ $household->members()->count() }}
               </div>
             </div>
             <div class="col-md-6">
               <label class="form-label-glass">Homeownership Type</label>
               <div class="p-2 rounded" style="background: color-mix(in srgb, var(--mis-blue-dark) 5%, transparent 95%); border: 1px dashed rgba(255,215,0,0.3);">
                 {{ $household->homeownership_type ?? 'Not set' }}
               </div>
             </div>
          </div>
        </div>
      </div>

      <!-- Socio-Economic -->
      <div class="glass-card">
        <div class="glass-card-header">
          <h6 class="mb-0 fw-bold"><i class="bi bi-currency-dollar me-2"></i>Socio-Economic Information</h6>
        </div>
        <div class="glass-card-body">
          <div class="row g-3">
            <div class="col-md-12">
              <label class="form-label-glass">Monthly Income Range</label>
              <div class="p-2 rounded" style="background: color-mix(in srgb, var(--mis-blue-dark) 5%, transparent 95%); border: 1px dashed rgba(255,215,0,0.3);">
                {{ $household->monthly_income_range ?? 'Not set' }}
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
               <span class="badge badge-glass-success w-100 text-center">4PS Beneficiary</span>
               @else
               <span class="badge badge-glass-secondary w-100 text-center">Not 4PS</span>
               @endif
             </div>
             <div class="col-md-6">
               @if($household->is_indigent ?? false)
               <span class="badge badge-glass-success w-100 text-center">Indigent</span>
               @else
               <span class="badge badge-glass-secondary w-100 text-center">Not Indigent</span>
               @endif
             </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Right Bento Column -->
    <div class="col-lg-5 d-flex flex-column gap-4">
      <!-- Housing & Utilities -->
      <div class="glass-card">
        <div class="glass-card-header">
          <h6 class="mb-0 fw-bold"><i class="bi bi-building me-2"></i>Housing & Utilities</h6>
        </div>
        <div class="glass-card-body">
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label-glass">Facilities Available</label>
              <div class="d-flex flex-wrap gap-2">
                 @if($household->has_toilet ?? false)
                 <span class="badge badge-glass-success">Toilet</span>
                 @endif
                 @if($household->has_bathroom ?? false)
                 <span class="badge badge-glass-success">Bathroom</span>
                 @endif
                 @if($household->has_kitchen ?? false)
                 <span class="badge badge-glass-success">Kitchen</span>
                 @endif
                 @if($household->has_garage ?? false)
                 <span class="badge badge-glass-success">Garage</span>
                 @endif
                 @if($household->has_electricity ?? false)
                 <span class="badge badge-glass-success">Electricity</span>
                 @endif
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Health & Community -->
      <div class="glass-card">
        <div class="glass-card-header">
          <h6 class="mb-0 fw-bold"><i class="bi bi-heart-pulse me-2"></i>Health & Community Indicators</h6>
        </div>
        <div class="glass-card-body">
          <div class="row g-2">
             <div class="col-12">
               @if($household->has_pregnant_member ?? false)
               <span class="badge badge-glass-success w-100 mb-1">Has Pregnant Member</span>
               @endif
               @if($household->has_senior_citizen ?? false)
               <span class="badge badge-glass-success w-100 mb-1">Has Senior Citizen</span>
               @endif
               @if($household->has_pwd ?? false)
               <span class="badge badge-glass-success w-100 mb-1">Has PWD Member</span>
               @endif
               @if($household->has_chronic_illness ?? false)
               <span class="badge badge-glass-warning w-100 mb-1">Has Chronic Illness</span>
               @endif

               @if(!($household->has_pregnant_member || $household->has_senior_citizen || $household->has_pwd || $household->has_chronic_illness))
               <span class="badge badge-glass-secondary w-100">No Special Indicators</span>
               @endif
             </div>
          </div>
          <hr style="border-color: rgba(255, 215, 0, 0.2);">
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label-glass">Program Participation</label>
              <div class="p-2 rounded" style="background: color-mix(in srgb, var(--mis-blue-dark) 5%, transparent 95%); border: 1px dashed rgba(255,215,0,0.3);">
                {{ $household->barangay_program_participation ?? 'None' }}
              </div>
            </div>
          </div>
        </div>
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
              <label class="form-label-glass">Phase</label>
              <select name="phase" class="glass-select form-select">
                <option value="" {{ empty($household->phase) ? 'selected' : '' }}>None</option>
                <option value="A" {{ ($household->phase ?? '') === 'A' ? 'selected' : '' }}>A</option>
                <option value="B" {{ ($household->phase ?? '') === 'B' ? 'selected' : '' }}>B</option>
                <option value="C" {{ ($household->phase ?? '') === 'C' ? 'selected' : '' }}>C</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label-glass">Contact No.</label>
              <div class="input-group">
                <span class="input-group-text">+63</span>
                <input type="text" name="contact_no" class="glass-input form-control" value="{{ $household->contact_no ?? '' }}" placeholder="917 123 4567" maxlength="12">
              </div>
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
              <h6 class="mb-3 mt-4 fs-6"><i class="bi bi-building me-2"></i>Housing Details</h6>
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

            <div class="col-md-12">
              <label class="form-label-glass">Primary Income Source</label>

              <input type="text" name="primary_income_source" class="glass-input form-control" value="{{ $household->primary_income_source ?? '' }}" placeholder="e.g., Salary, Business, Remittance">
            </div>

            <div class="col-12">
              <h6 class="mb-3 mt-4 fs-6"><i class="bi bi-people me-2"></i>Community Participation</h6>
            </div>
            <div class="col-md-12">
              <label class="form-label-glass">Barangay Program Participation</label>

              @php
                $existingProgramsRaw = $household->barangay_program_participation ?? '';
                $existingPrograms = [];
                if (is_array($existingProgramsRaw)) {
                  $existingPrograms = $existingProgramsRaw;
                } elseif (is_string($existingProgramsRaw)) {
                  // Support stored comma-separated or JSON string
                  $trim = trim($existingProgramsRaw);
                  $existingPrograms = str_starts_with($trim, '[') ? (json_decode($trim, true) ?: []) : array_filter(array_map('trim', explode(',', $existingProgramsRaw)));
                }
              @endphp

              <div class="mb-2 d-flex flex-wrap gap-2">
                @php
                  $suggestedPrograms = ['Clean-up drives', 'Health programs', 'Livelihood training', 'Scholarship assistance', 'Disaster preparedness'];
                  $selectedMap = array_flip($existingPrograms);
                @endphp

                @foreach($suggestedPrograms as $program)
                  <label class="btn btn-glass btn-sm" style="cursor:pointer; user-select:none;">
                    <input type="checkbox" class="form-check-input" name="barangay_program_participation[]" value="{{ $program }}" {{ isset($selectedMap[$program]) ? 'checked' : '' }} style="margin-right:6px;">
                    {{ $program }}
                  </label>
                @endforeach

                <div class="d-flex align-items-center gap-2" style="min-width: 260px; flex: 1;">
                  <input type="text" name="barangay_program_participation_other" class="glass-input form-control" value="{{ $household->barangay_program_participation_other ?? '' }}" placeholder="Other program (type here)..." oninput="this.dataset.value = this.value;">
                </div>
              </div>
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

<!-- Bulk Edit Members Modal -->
<div class="modal fade" id="bulkEditMembersModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content glass-modal">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-people-fill me-2"></i>Edit All Household Members</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="bulkEditMembersForm">
          <div class="table-responsive">
            <table class="glass-table">
              <thead>
                <tr>
                  <th style="width: 50%;">Name</th>
                  <th style="width: 50%;">Relationship</th>
                </tr>
              </thead>
              <tbody id="bulkEditTableBody">
                <!-- Populated via JS -->
              </tbody>
            </table>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-glass" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-glass-primary" id="saveBulkEditBtn" onclick="submitBulkEdit()">Update All Members</button>
      </div>
    </div>
  </div>
</div>

<!-- Edit Member Modal -->
<div class="modal fade" id="editMemberModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content glass-modal">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Edit Member Details</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="editMemberForm">
          <input type="hidden" id="editMemberId" name="member_id">
          <div class="mb-3">
            <label class="form-label-glass">Relationship <span class="text-danger">*</span></label>
            <select class="glass-select form-select" id="editRelationship" name="relationship" required>
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
        <button type="button" class="btn btn-glass-primary" onclick="updateMemberDetails()">Update Member</button>
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

/* Glass inputs + selects (same styling) */
.glass-input,
.glass-select {
  background: var(--input-bg) !important;
  background-color: var(--input-bg) !important;
  border: 1px solid var(--input-border) !important;
  color: var(--mis-blue-dark) !important;
  padding: 10px 14px !important;
  border-radius: 10px !important;
}

/* Force override for Bootstrap form-control defaults (prevents white backgrounds) */
.glass-modal input.form-control,
.glass-modal textarea.form-control {
  background: var(--input-bg) !important;
  background-color: var(--input-bg) !important;
  color: var(--mis-blue-dark) !important;
  border-color: var(--input-border) !important;
}

.glass-input::placeholder {
  color: color-mix(in srgb, var(--mis-blue-dark) 60%, transparent 40%);
}

.glass-select,
.glass-select.form-select {
  background: var(--input-bg) !important;
  color: var(--mis-blue-dark) !important;
  border-color: var(--input-border) !important;
}

.glass-select option {
  background: var(--mis-blue-dark) !important;
  color: var(--mis-white) !important;
}

.glass-select option:checked,
.glass-select option:focus,
.glass-select option:hover {
  background: color-mix(in srgb, var(--mis-blue-dark) 85%, var(--mis-yellow) 15%) !important;
  color: var(--mis-white) !important;
}

.glass-select:focus {
  color: var(--mis-blue-dark) !important;
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

/* Toast Animations */
@keyframes toastSlideIn {
  from { transform: translateX(100%); opacity: 0; }
  to { transform: translateX(0); opacity: 1; }
}

.glass-toast {
  animation: toastSlideIn 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55) forwards;
  border: 1px solid rgba(255, 255, 255, 0.2) !important;
  backdrop-filter: blur(20px) !important;
  -webkit-backdrop-filter: blur(20px) !important;
  box-shadow: 0 15px 35px rgba(0,0,0,0.3) !important;
}
</style>
@endpush

@push('scripts')
<script>
// Reusable Toast Notification System
function showToast(message, type = 'success') {
  const wrapper = document.getElementById('toastWrapper') || (() => {
    const el = document.createElement('div');
    el.id = 'toastWrapper';
    el.style.cssText = 'position: fixed; top: 25px; right: 25px; z-index: 999999; pointer-events: none;';
    document.body.appendChild(el);
    return el;
  })();

  const toastEl = document.createElement('div');
  toastEl.style.pointerEvents = 'auto';
  toastEl.className = `toast show align-items-center text-white border-0 mb-3 glass-toast`;
  
  // Colors matching the MIS brand and login glass style
  const bg = type === 'success' 
    ? 'linear-gradient(135deg, rgba(16, 85, 201, 0.95) 0%, rgba(5, 150, 105, 0.95) 100%)'
    : 'linear-gradient(135deg, rgba(220, 38, 38, 0.95) 0%, rgba(153, 27, 27, 0.95) 100%)';

  toastEl.style.cssText = `
    min-width: 320px;
    border-radius: 12px;
    background: ${bg};
    transition: all 0.4s ease;
  `;

  const icon = type === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill';

  toastEl.innerHTML = `
    <div class="d-flex p-3">
      <div class="bg-white bg-opacity-20 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 32px; height: 32px; min-width: 32px;">
        <i class="bi ${icon} fs-6"></i>
      </div>
      <div class="toast-body p-0 flex-grow-1 d-flex align-items-center">
        <div class="fw-medium">${message}</div>
      </div>
      <button type="button" class="btn-close btn-close-white ms-3" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
  `;

  wrapper.appendChild(toastEl);
  setTimeout(() => {
    toastEl.style.opacity = '0';
    toastEl.style.transform = 'translateX(20px)';
    setTimeout(() => toastEl.remove(), 300);
  }, 2500); // Display for 2.5s to ensure the 2s reload transition is smooth
}

// Open modals
const btnCreateHouseholdModal = document.getElementById('btnCreateHouseholdModal');
if (btnCreateHouseholdModal) {
  btnCreateHouseholdModal.addEventListener('click', () => {
    new bootstrap.Modal(document.getElementById('householdFormModal')).show();
  });
}

const btnEditHousehold = document.getElementById('btnEditHousehold');
if (btnEditHousehold) {
  btnEditHousehold.addEventListener('click', () => {
    new bootstrap.Modal(document.getElementById('householdFormModal')).show();
  });
}

const btnJoinHousehold = document.getElementById('btnJoinHousehold');
if (btnJoinHousehold) {
  btnJoinHousehold.addEventListener('click', () => {
    new bootstrap.Modal(document.getElementById('joinHouseholdModal')).show();
  });
}

const addMemberBtnModal = document.getElementById('addMemberBtnModal');
if (addMemberBtnModal) {
  addMemberBtnModal.addEventListener('click', () => {
    new bootstrap.Modal(document.getElementById('addMemberModal')).show();
  });
}

/* Resident household change handlers removed to resolve script syntax issues in the Blade file.
   PWD toggle and relationship update are handled elsewhere in this page. */

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
      showToast(data.message || 'Member added successfully!');
      setTimeout(() => { location.reload(); }, 2000);
    } else {
       showToast(data.message || 'Failed to add member', 'danger');
     }
   }).catch((error) => {
     console.error('Fetch error:', error);
     showToast('An error occurred: ' + error.message, 'danger');
   });
}

// Join household request
function sendJoinRequest() {
  const formData = new FormData(document.getElementById('joinRequestForm'));
  const csrfEl = document.querySelector('meta[name="csrf-token"]');
  const csrfToken = csrfEl ? csrfEl.content : null;
  if (!csrfToken) { showToast('CSRF token not found.', 'danger'); return; }
   fetch('{{ route("resident.household.join") }}', {
     method: 'POST',
     headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
     body: formData
   }).then(response => response.json().catch(() => response.text().then(t => { throw new Error(t) })))
  .then(data => {
     if (data.success) {
       bootstrap.Modal.getInstance(document.getElementById('joinHouseholdModal')).hide();
       document.getElementById('joinRequestForm').reset();
       showToast(data.message || 'Join request sent successfully!');
       setTimeout(() => { location.reload(); }, 2000);
     } else {
       showToast(data.message || 'Failed to send request', 'danger');
     }
   }).catch((error) => {
     console.error('Fetch error:', error);
     showToast('Error sending request: ' + error.message, 'danger');
   });
}

// Submit household form via AJAX
const householdForm = document.getElementById('householdForm');
if (householdForm) householdForm.addEventListener('submit', function(e) {
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
        showToast('Household updated successfully!');
        setTimeout(() => { location.reload(); }, 2000);
      } else {
        showToast(data.message || 'Failed to save household', 'danger');
      }
    }).catch((error) => {
      console.error('Fetch error:', error);
      showToast('An error occurred: ' + error.message, 'danger');
    });
});

function openBulkEditModal() {
    const members = {!! json_encode($household ? $household->members : []) !!};
    const tbody = document.getElementById('bulkEditTableBody');
    tbody.innerHTML = '';

    members.forEach((member, index) => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td class="align-middle">
                <input type="hidden" name="members[${index}][id]" value="${member.id}">
                <span class="fw-bold">${member.first_name} ${member.last_name}</span>
            </td>
            <td class="align-middle">
                ${member.relationship === 'Head' ? `
                    <input type="hidden" name="members[${index}][relationship]" value="Head">
                    <span class="badge badge-glass-secondary" style="font-size:0.75rem; padding:6px 12px; border-radius:50px; background: rgba(13,110,253,0.25); border: 1px solid rgba(13,110,253,0.4); color: #cfe2ff;">Head</span>
                ` : `
                    <select class="glass-select form-select form-select-sm" style="padding-top:6px; padding-bottom:6px; height:auto;" name="members[${index}][relationship]" required>
                        <option value="Spouse" ${member.relationship === 'Spouse' ? 'selected' : ''}>Spouse</option>
                        <option value="Child" ${member.relationship === 'Child' ? 'selected' : ''}>Child</option>
                        <option value="Parent" ${member.relationship === 'Parent' ? 'selected' : ''}>Parent</option>
                        <option value="Sibling" ${member.relationship === 'Sibling' ? 'selected' : ''}>Sibling</option>
                        <option value="Relative" ${member.relationship === 'Relative' ? 'selected' : ''}>Relative</option>
                        <option value="Other" ${member.relationship === 'Other' ? 'selected' : ''}>Other</option>
                    </select>`}
            </td>
        `;
        tbody.appendChild(row);
    });

    new bootstrap.Modal(document.getElementById('bulkEditMembersModal')).show();
}

function submitBulkEdit() {
    const formData = new FormData(document.getElementById('bulkEditMembersForm'));
    const btn = document.getElementById('saveBulkEditBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Updating...';

    fetch('{{ route("resident.household.members.bulk-update") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: new URLSearchParams([...formData, ['_method', 'PUT']])
    }).then(response => response.json()).then(data => {
        if (data.success) {
            showToast(data.message);
            setTimeout(() => location.reload(), 2000);
        } else {
            showToast(data.message || 'Error updating members', 'danger');
            btn.disabled = false;
            btn.innerHTML = 'Update All Members';
        }
    }).catch(error => {
        console.error('Error:', error);
        showToast('An error occurred', 'danger');
        btn.disabled = false;
        btn.innerHTML = 'Update All Members';
    });
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
    if (data.success) {
      showToast(data.message || 'Join request approved successfully!');
      setTimeout(() => { location.reload(); }, 2000);
    } else {
      showToast(data.message || 'Failed to approve request.', 'danger');
    }
  }).catch((error) => {
    console.error('Fetch error:', error);
    showToast('Error approving request: ' + error.message, 'danger');
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
    if (data.success) {
      showToast(data.message || 'Join request rejected.');
      setTimeout(() => { location.reload(); }, 2000);
    } else {
      showToast(data.message || 'Failed to reject request.', 'danger');
    }
  }).catch((error) => {
    console.error('Fetch error:', error);
    showToast('Error rejecting request: ' + error.message, 'danger');
  });
}
</script>
@endpush
