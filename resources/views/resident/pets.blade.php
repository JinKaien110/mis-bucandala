@extends('layouts.resident', ['currentRoute' => 'resident.pets', 'residentName' => ($resident->first_name ?? 'Resident')])

@section('content')
<div class="container">
  <!-- Page Header -->
  <div class="glass p-4 mb-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
      <div>
        <h2 class="mb-1"><i class="bi bi-paw me-2"></i>Pet Registration</h2>
        <p class="opacity-75 mb-0">Register and manage your pets.</p>
      </div>
      <button class="btn btn-glass-primary" data-bs-toggle="modal" data-bs-target="#petModal">
        <i class="bi bi-plus-lg me-2"></i>Register New Pet
      </button>
    </div>
  </div>

  <!-- Pet Cards -->
  <div class="row g-4">
    @if(count($pets) > 0)
      @foreach($pets as $pet)
      <div class="col-md-6 col-lg-4">
        <div class="glass-card">
          <div class="d-flex align-items-start gap-3">
            @if($pet->photo)
              <img src="{{ asset('storage/' . $pet->photo) }}" alt="{{ $pet->name }}" style="width: 80px; height: 80px; border-radius: 16px; object-fit: cover;">
            @else
              <div style="width: 80px; height: 80px; border-radius: 16px; background: rgba(255,255,255,0.1); display: flex; align-items: center; justify-content: center;">
                <i class="bi bi-paw" style="font-size: 32px; opacity: 0.5;"></i>
              </div>
            @endif
            <div class="flex-grow-1">
              <h5 class="mb-1">{{ $pet->name }}</h5>
              <p class="small opacity-75 mb-2">{{ ucfirst($pet->type) }} {{ $pet->breed ? '- ' . $pet->breed : '' }}</p>
              <span class="badge-glass 
                @if($pet->vaccination_status === 'vaccinated') badge-glass-success
                @elseif($pet->vaccination_status === 'partial') badge-glass-warning
                @else badge-glass-secondary @endif">
                {{ ucfirst($pet->vaccination_status ?? 'Unknown') }}
              </span>
            </div>
          </div>
          
          <div class="mt-3 pt-3 border-top border-light d-flex gap-2">
            <button class="btn btn-sm btn-outline-light flex-grow-1 edit-pet-btn" 
              data-id="{{ $pet->id }}" 
              data-name="{{ $pet->name }}" 
              data-type="{{ $pet->type }}" 
              data-breed="{{ $pet->breed ?? '' }}" 
              data-color="{{ $pet->color ?? '' }}" 
              data-gender="{{ $pet->gender ?? '' }}" 
              data-vaccination="{{ $pet->vaccination_status ?? '' }}">
              <i class="bi bi-pencil me-1"></i>Edit
            </button>
            <button class="btn btn-sm btn-outline-danger delete-pet-btn" data-id="{{ $pet->id }}">
              <i class="bi bi-trash"></i>
            </button>
          </div>
        </div>
      </div>
      @endforeach
    @else
      <div class="col-12">
        <div class="glass p-5 text-center">
          <i class="bi bi-paw" style="font-size: 64px; opacity: 0.3;"></i>
          <h4 class="mt-3">No Pets Registered</h4>
          <p class="opacity-75">You haven't registered any pets yet. Click the button above to register your first pet.</p>
        </div>
      </div>
    @endif
  </div>
</div>

<!-- Pet Modal -->
<div class="modal fade" id="petModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content glass-modal">
      <div class="modal-header">
        <h5 class="modal-title" id="petModalTitle"><i class="bi bi-paw me-2"></i>Register New Pet</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="petForm">
          <input type="hidden" id="petId">
          
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label-glass">Pet Name <span class="text-danger">*</span></label>
              <input type="text" class="glass-input form-control" id="petName" name="name" required>
            </div>
            <div class="col-md-6">
              <label class="form-label-glass">Type <span class="text-danger">*</span></label>
              <select class="glass-select form-select" id="petType" name="type" required>
                <option value="">Select Type</option>
                <option value="dog">Dog</option>
                <option value="cat">Cat</option>
                <option value="bird">Bird</option>
                <option value="other">Other</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label-glass">Breed</label>
              <input type="text" class="glass-input form-control" id="petBreed" name="breed" placeholder="e.g., Labrador, Persian">
            </div>
            <div class="col-md-6">
              <label class="form-label-glass">Color</label>
              <input type="text" class="glass-input form-control" id="petColor" name="color" placeholder="e.g., Brown, Black">
            </div>
            <div class="col-md-6">
              <label class="form-label-glass">Gender</label>
              <select class="glass-select form-select" id="petGender" name="gender">
                <option value="">Select</option>
                <option value="male">Male</option>
                <option value="female">Female</option>
                <option value="unknown">Unknown</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label-glass">Vaccination Status</label>
              <select class="glass-select form-select" id="petVaccination" name="vaccination_status">
                <option value="">Select</option>
                <option value="vaccinated">Vaccinated</option>
                <option value="partial">Partial</option>
                <option value="unvaccinated">Unvaccinated</option>
              </select>
            </div>
            <div class="col-12">
              <label class="form-label-glass">Photo</label>
              <input type="file" class="glass-input form-control" id="petPhoto" name="photo" accept="image/*">
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-glass" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-glass-primary" id="savePetBtn" onclick="savePet()">Save Pet</button>
      </div>
    </div>
  </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content glass-modal">
      <div class="modal-body text-center py-4">
        <i class="bi bi-exclamation-triangle text-danger" style="font-size: 48px;"></i>
        <h4 class="mt-3">Delete Pet?</h4>
        <p class="opacity-75">Are you sure you want to remove this pet from your registration?</p>
        <input type="hidden" id="deletePetId">
        <div class="d-flex justify-content-center gap-3 mt-4">
          <button type="button" class="btn btn-glass" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-danger" onclick="confirmDelete()">Delete</button>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
let isEdit = false;

function resetForm() {
  document.getElementById('petForm').reset();
  document.getElementById('petId').value = '';
  document.getElementById('petModalTitle').innerHTML = '<i class="bi bi-paw me-2"></i>Register New Pet';
  document.getElementById('savePetBtn').innerHTML = 'Save Pet';
  isEdit = false;
}

function editPet(id, name, type, breed, color, gender, vaccination) {
  isEdit = true;
  document.getElementById('petId').value = id;
  document.getElementById('petName').value = name || '';
  document.getElementById('petType').value = type || '';
  document.getElementById('petBreed').value = breed || '';
  document.getElementById('petColor').value = color || '';
  document.getElementById('petGender').value = gender || '';
  document.getElementById('petVaccination').value = vaccination || '';
  document.getElementById('petModalTitle').innerHTML = '<i class="bi bi-pencil me-2"></i>Edit Pet';
  document.getElementById('savePetBtn').innerHTML = 'Update Pet';
  new bootstrap.Modal(document.getElementById('petModal')).show();
}

async function savePet() {
  const form = document.getElementById('petForm');
  const formData = new FormData(form);
  const photoFile = document.getElementById('petPhoto').files[0];
  if (photoFile) {
    formData.append('photo', photoFile);
  }
  
  const url = isEdit 
    ? '/resident/pets/' + document.getElementById('petId').value 
    : '{{ route("resident.pets.store") }}';
  const method = isEdit ? 'PUT' : 'POST';
  
  document.getElementById('savePetBtn').disabled = true;
  document.getElementById('savePetBtn').innerHTML = 'Saving...';
  
  try {
    const res = await fetch(url, {
      method: method,
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
      },
      body: formData
    });
    
    const data = await res.json();
    
    if (res.ok) {
      bootstrap.Modal.getInstance(document.getElementById('petModal')).hide();
      resetForm();
      location.reload();
    } else {
      alert(data.message || 'Failed to save pet');
    }
  } catch (e) {
    alert('An error occurred. Please try again.');
  } finally {
    document.getElementById('savePetBtn').disabled = false;
    document.getElementById('savePetBtn').innerHTML = isEdit ? 'Update Pet' : 'Save Pet';
  }
}

function deletePet(id) {
  document.getElementById('deletePetId').value = id;
  new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

document.addEventListener('DOMContentLoaded', function() {
  document.querySelectorAll('.edit-pet-btn').forEach(btn => {
    btn.addEventListener('click', function() {
      editPet(
        this.dataset.id,
        this.dataset.name,
        this.dataset.type,
        this.dataset.breed,
        this.dataset.color,
        this.dataset.gender,
        this.dataset.vaccination
      );
    });
  });
  
  document.querySelectorAll('.delete-pet-btn').forEach(btn => {
    btn.addEventListener('click', function() {
      deletePet(this.dataset.id);
    });
  });
});

async function confirmDelete() {
  const id = document.getElementById('deletePetId').value;
  
  try {
    const res = await fetch('/resident/pets/' + id, {
      method: 'DELETE',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
      }
    });
    
    const data = await res.json();
    
    if (res.ok) {
      bootstrap.Modal.getInstance(document.getElementById('deleteModal')).hide();
      location.reload();
    } else {
      alert(data.message || 'Failed to delete pet');
    }
  } catch (e) {
    alert('An error occurred. Please try again.');
  }
}

document.getElementById('petModal').addEventListener('hidden.bs.modal', resetForm);
</script>
@endpush