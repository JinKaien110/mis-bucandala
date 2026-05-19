@extends('layouts.admin')

@section('title', 'Blotters - Barangay MIS')

@section('content')
<div class="page-surface">
  
  <!-- Header -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h4 class="mb-1 fw-bold text-primary">Blotters</h4>
      <p class="text-muted mb-0">Manage incident reports and complaints</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createBlotterModal">
        <i class="bi bi-plus-lg me-1"></i> New Blotter
    </button>
  </div>

  @if(session('success'))
    <div class="alert alert-success d-flex align-items-center shadow-sm border-0 mb-4">
        <i class="bi bi-check-circle-fill fs-4 me-2"></i>
        <div>{{ session('success') }}</div>
    </div>
  @endif
  @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
  @if(session('error'))
    <div class="alert alert-danger d-flex align-items-center shadow-sm border-0 mb-4">
        <i class="bi bi-exclamation-triangle-fill fs-4 me-2"></i>
        <div>{{ session('error') }}</div>
    </div>
  @endif

  <div class="card border-0 shadow-sm">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="bg-light text-uppercase small text-muted">
            <tr>
              <th class="px-4 py-3 border-0">Blotter No</th>
              <th class="py-3 border-0">Date</th>
              <th class="py-3 border-0">Type</th>
              <th class="py-3 border-0">Location</th>
              <th class="py-3 border-0">Case Status</th>
              <th class="py-3 border-0 text-end pe-4">Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($blotters as $b)
              <tr>
                <td class="px-4">
                    <span class="fw-bold text-primary">{{ $b->blotter_no }}</span>
                </td>
                <td>
                    <div class="d-flex align-items-center">
                        <i class="bi bi-calendar-event text-muted me-2"></i>
                        {{ $b->incident_date->format('M d, Y') }}
                        <small class="text-muted ms-1">{{ $b->incident_date->format('h:i A') }}</small>
                    </div>
                </td>
                <td>
                    <span class="badge bg-light text-dark border">{{ $b->incident_type }}</span>
                </td>
                <td><span class="text-muted small">{{ $b->incident_location }}</span></td>
                <td>
                  @if($b->case)
                    @php
                        $statusColor = match(strtolower($b->case->status)) {
                            'ongoing' => 'warning',
                            'settled' => 'success',
                            'dismissed' => 'secondary',
                            'certified_to_file_action' => 'danger',
                            default => 'info'
                        };
                    @endphp
                    <span class="badge bg-{{ $statusColor }}-subtle text-{{ $statusColor }} border border-{{ $statusColor }}-subtle rounded-pill">
                        {{ ucfirst($b->case->status) }}
                    </span>
                  @else
                    <span class="badge bg-light text-muted border rounded-pill">No Case</span>
                  @endif
                </td>
                <td class="text-end pe-4">
                  <a class="btn btn-sm btn-info rounded-circle" style="width: 32px; height: 32px;" href="{{ route('admin.blotters.show', $b) }}" title="View Details">
                    <i class="bi bi-eye-fill"></i>
                  </a>
                </td>
              </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center py-5">
                        <div class="text-muted opacity-50">
                            <i class="bi bi-journal-x fs-1 d-block mb-3"></i>
                            <h5 class="fw-normal">No blotters found</h5>
                            <p class="small">Create a new blotter to get started.</p>
                        </div>
                    </td>
                </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
    @if($blotters->hasPages())
        <div class="card-footer bg-white border-top-0 py-3">
            {{ $blotters->links() }}
        </div>
    @endif
  </div>
</div>
@include('admin.blotters.create')
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  // Initialize Select2 for modal dropdowns
  $('.resident-select-modal').select2({
    theme: 'bootstrap-5',
    placeholder: function() {
      return $(this).data('placeholder') || '-- Search Resident --';
    },
    allowClear: true,
    width: '100%',
    dropdownParent: $('#createBlotterModal')
  });

  // Auto-fill contact info when resident is selected - Complainant
  $('#c_resident_select').on('select2:select', function(e) {
    const option = e.target.options[e.target.selectedIndex];
    if (option) {
      const email = option.dataset.email || '';
      const contact = option.dataset.contact || '';
      
      const container = $(this).closest('#c_resident_wrap');
      container.find('input[name="complainant_contact"]').val(contact);
      container.find('input[name="complainant_email"]').val(email);
    }
  });

  // Auto-fill contact info when resident is selected - Respondent
  $('#r_resident_select').on('select2:select', function(e) {
    const option = e.target.options[e.target.selectedIndex];
    if (option) {
      const email = option.dataset.email || '';
      const contact = option.dataset.contact || '';
      
      const container = $(this).closest('#r_resident_wrap');
      container.find('input[name="respondent_contact"]').val(contact);
      container.find('input[name="respondent_email"]').val(email);
    }
  });

  // Complainant toggle
  const cRadios = document.getElementsByName('complainant_mode');
  const cResWrap = document.getElementById('c_resident_wrap');
  const cManWrap = document.getElementById('c_manual_wrap');

  function toggleC() {
    const val = document.querySelector('input[name="complainant_mode"]:checked').value;
    if(val === 'resident') {
      cResWrap.style.display = 'block';
      cManWrap.style.display = 'none';
      document.getElementById('c_resident_select').disabled = false;
      document.querySelectorAll('#c_manual_wrap input').forEach(i => i.disabled = true);
    } else {
      cResWrap.style.display = 'none';
      cManWrap.style.display = 'block';
      document.getElementById('c_resident_select').disabled = true;
      document.querySelectorAll('#c_manual_wrap input').forEach(i => i.disabled = false);
    }
  }
  cRadios.forEach(r => r.addEventListener('change', toggleC));
  toggleC();

  // Respondent toggle
  const rRadios = document.getElementsByName('respondent_mode');
  const rResWrap = document.getElementById('r_resident_wrap');
  const rManWrap = document.getElementById('r_manual_wrap');

  function toggleR() {
    const val = document.querySelector('input[name="respondent_mode"]:checked').value;
    if(val === 'resident') {
      rResWrap.style.display = 'block';
      rManWrap.style.display = 'none';
      document.getElementById('r_resident_select').disabled = false;
      document.querySelectorAll('#r_manual_wrap input').forEach(i => i.disabled = true);
    } else {
      rResWrap.style.display = 'none';
      rManWrap.style.display = 'block';
      document.getElementById('r_resident_select').disabled = true;
      document.querySelectorAll('#r_manual_wrap input').forEach(i => i.disabled = false);
    }
  }
  rRadios.forEach(r => r.addEventListener('change', toggleR));
  toggleR();
});
</script>
@endpush

<style>
/* Modal Select2 Custom Styling */
#createBlotterModal .select2-container--bootstrap-5 .select2-selection {
  border-radius: 12px;
  padding: 12px 16px;
  border: 2px solid #e5e7eb;
  min-height: 46px;
  width: 100%;
}

#createBlotterModal .select2-container {
  width: 100% !important;
}

#createBlotterModal .select2-container--bootstrap-5 .select2-selection:focus,
#createBlotterModal .select2-container--bootstrap-5 .select2-selection--single:focus {
  border-color: #1055C9;
  box-shadow: 0 0 0 4px rgba(16, 85, 201, 0.1);
}

#createBlotterModal .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
  padding: 0;
  line-height: 1.5;
}

#createBlotterModal .select2-container--bootstrap-5 .select2-dropdown {
  border-radius: 12px;
  border: 2px solid #e5e7eb;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
}

#createBlotterModal .select2-container--bootstrap-5 .select2-results__option {
  padding: 10px 16px;
  border-radius: 8px;
  margin: 2px 4px;
}

#createBlotterModal .select2-container--bootstrap-5 .select2-results__option--highlighted[aria-selected] {
  background-color: #1055C9;
}

#createBlotterModal .select2-container--bootstrap-5 .select2-search__field {
  border-radius: 8px;
  padding: 8px 12px;
  border: 2px solid #e5e7eb;
}

#createBlotterModal .select2-container--bootstrap-5 .select2-search__field:focus {
  border-color: #1055C9;
  outline: none;
}

/* Modal Styling */
#createBlotterModal .modal-content {
  border: none;
  border-radius: 20px;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
  max-height: 90vh;
  display: flex;
  flex-direction: column;
}

#createBlotterModal .modal-header {
  padding: 20px 24px;
  flex-shrink: 0;
  border-bottom: 1px solid #e5e7eb;
}

#createBlotterModal .modal-body {
  padding: 20px 24px;
  overflow-y: auto;
  flex-grow: 1;
}

#createBlotterModal .modal-footer {
  padding: 16px 24px;
  border-radius: 0 0 20px 20px;
  flex-shrink: 0;
  border-top: 1px solid #e5e7eb;
}

#createBlotterModal .form-control,
#createBlotterModal .form-select {
  border-radius: 12px;
  padding: 12px 16px;
  border: 2px solid #e5e7eb;
  transition: all 0.3s ease;
}

#createBlotterModal .form-control:focus,
#createBlotterModal .form-select:focus {
  border-color: #1055C9;
  box-shadow: 0 0 0 4px rgba(16, 85, 201, 0.1);
}

#createBlotterModal .btn {
  border-radius: 12px;
  padding: 10px 20px;
  font-weight: 600;
}

#createBlotterModal .btn-primary {
  background: linear-gradient(135deg, #1055C9 0%, #0d47a1 100%);
  border: none;
  box-shadow: 0 4px 15px rgba(16, 85, 201, 0.3);
}

#createBlotterModal .btn-primary:hover {
  background: linear-gradient(135deg, #0d47a1 0%, #0a3d8f 100%);
  box-shadow: 0 6px 20px rgba(16, 85, 201, 0.4);
}
</style>
@endsection
