<!-- ================= CREATE HOUSEHOLD MODAL ================= -->
<div class="modal fade" id="addHouseholdModal" tabindex="-1" data-bs-backdrop="static">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">

      <form method="POST" action="{{ route('admin.households.store') }}">
        @csrf

        <div class="modal-header">
          <h5 class="modal-title">Create Household</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">

          <div class="mb-3">
            <label class="form-label">Address Line *</label>
            <input type="text" name="address_line" class="form-control" required>
          </div>

          <hr>

          <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="mb-0">Household Members *</h6>
            <button type="button" class="btn btn-sm btn-outline-primary" id="addMemberBtn">
              Add Member
            </button>
          </div>

          <div id="membersContainer"></div>

        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Save Household</button>
        </div>

      </form>
    </div>
  </div>
</div>


<!-- ================= EDIT MODALS ================= -->
@foreach($households as $household)
<div class="modal fade" id="editHouseholdModal{{ $household->id }}" tabindex="-1" data-bs-backdrop="static">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">

      <form method="POST" action="{{ route('admin.households.update', $household) }}">
        @csrf
        @method('PUT')

        <div class="modal-header">
          <h5 class="modal-title">
            Edit Household — {{ $household->household_code }}
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">

          <div class="mb-3">
            <label class="form-label">Address Line *</label>
            <input type="text"
                   name="address_line"
                   class="form-control"
                   value="{{ $household->address_line }}"
                   required>
          </div>

          <hr>

          <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="mb-0">Household Members *</h6>
            <button type="button"
                    class="btn btn-sm btn-outline-primary addMemberBtnEdit"
                    data-id="{{ $household->id }}">
              Add Member
            </button>
          </div>

          <div id="editMembersContainer{{ $household->id }}">
            @foreach($household->members as $index => $member)
              <div class="border rounded p-3 mb-3 member-row">

                <input type="hidden"
                       name="members[{{ $index }}][id]"
                       value="{{ $member->id }}">

                <div class="row g-2">

                  <div class="col-md-4">
                    <label class="form-label">Registered Resident</label>
                    <select name="members[{{ $index }}][resident_id]"
                            class="form-select resident-select">
                      <option value="">-- Select if registered --</option>

                      @foreach($residents as $resident)
                        <option value="{{ $resident->id }}"
                          data-first="{{ $resident->first_name }}"
                          data-last="{{ $resident->last_name }}"
                          data-email="{{ $resident->email }}"
                          {{ $member->resident_id == $resident->id ? 'selected' : '' }}>
                          {{ $resident->first_name }} {{ $resident->last_name }}
                        </option>
                      @endforeach
                    </select>
                  </div>

                  <div class="col-md-3">
                    <label class="form-label">First Name</label>
                    <input type="text"
                           name="members[{{ $index }}][first_name]"
                           class="form-control first-name"
                           value="{{ $member->first_name }}"
                           required>
                  </div>

                  <div class="col-md-3">
                    <label class="form-label">Last Name</label>
                    <input type="text"
                           name="members[{{ $index }}][last_name]"
                           class="form-control last-name"
                           value="{{ $member->last_name }}"
                           required>
                  </div>

                  <div class="col-md-4 mt-2">
                    <label class="form-label">Email *</label>
                    <input type="email"
                           name="members[{{ $index }}][email]"
                           class="form-control email-field"
                           value="{{ $member->email }}"
                           required>
                  </div>

                  <div class="col-md-4 mt-2">
                    <label class="form-label">Relationship *</label>
                    <input type="text"
                           name="members[{{ $index }}][relationship]"
                           class="form-control"
                           value="{{ $member->relationship }}"
                           required>
                  </div>

                  <div class="col-md-2 mt-4">
                    <button type="button"
                            class="btn btn-outline-danger w-100 removeMember">
                      Remove
                    </button>
                  </div>

                </div>
              </div>
            @endforeach
          </div>

        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">
            Update Household
          </button>
        </div>

      </form>
    </div>
  </div>
</div>
@endforeach
