@extends('layouts.public', ['currentRoute' => 'public.officials'])

@push('styles')
<style>
.official-card {
  padding: 32px;
  text-align: center;
  transition: all 0.3s;
}

.official-card:hover {
  transform: translateY(-8px);
  background: rgba(255, 255, 255, 0.18);
}

.official-avatar {
  width: 140px;
  height: 140px;
  border-radius: 50%;
  object-fit: cover;
  border: 5px solid rgba(255, 255, 255, 0.3);
  margin-bottom: 16px;
}

.official-name {
  font-weight: 600;
  font-size: 1.1rem;
  margin-bottom: 4px;
}

.official-position {
  font-size: 0.85rem;
  opacity: 0.75;
  margin-bottom: 8px;
}

.official-contact {
  font-size: 0.8rem;
  opacity: 0.6;
}

.captain-badge {
  background: linear-gradient(135deg, #FEEE91 0%, #f59e0b 100%);
  color: #1f2937;
  padding: 8px 20px;
  border-radius: 50px;
  font-size: 0.85rem;
  font-weight: 700;
  display: inline-block;
  margin-bottom: 16px;
}

.search-box {
  background: rgba(255, 255, 255, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.2);
  color: #fff;
  padding: 14px 20px;
  border-radius: 12px;
  width: 100%;
  max-width: 400px;
}

.search-box:focus {
  outline: none;
  border-color: var(--mis-yellow);
  background: rgba(255, 255, 255, 0.15);
}

.search-box::placeholder { color: rgba(255, 255, 255, 0.5); }

.filter-btn {
  background: rgba(255, 255, 255, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.2);
  color: #fff;
  padding: 8px 16px;
  border-radius: 50px;
  transition: all 0.3s;
  cursor: pointer;
}

.filter-btn:hover, .filter-btn.active {
  background: var(--mis-yellow);
  color: #1f2937;
  border-color: var(--mis-yellow);
}
</style>
@endpush

@section('content')
<div class="page-header text-center mb-5">
  <h1 class="page-title">Barangay Officials</h1>
  <p class="opacity-75">Meet our dedicated team serving the community</p>
</div>

<!-- Search & Filter -->
<div class="glass p-4 mb-4">
  <div class="d-flex flex-wrap gap-3 align-items-center">
    <input type="text" class="search-box" id="searchOfficial" placeholder="Search officials...">
    <button class="filter-btn active" data-filter="all">All</button>
    <button class="filter-btn" data-filter="captain">Captain</button>
    <button class="filter-btn" data-filter="councilor">Councilor</button>
    <button class="filter-btn" data-filter="secretary">Secretary</button>
    <button class="filter-btn" data-filter="tanod">Tanod</button>
  </div>
</div>

<!-- Officials Grid -->
<div class="row g-4" id="officialsList">
  <div class="col-md-4 col-lg-3" data-position="captain">
    <div class="official-card glass">
      <div class="captain-badge">Barangay Captain</div>
      <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3E%3Cpath d='M8 8a3 3 0 100-6 3 3 0 000 6zm0 1c-3.315 0-6 1.79-6 4v1h12v-1c0-2.21-2.685-4-6-4z' fill='%23ffffff'/%3E%3C/svg%3E" alt="Barangay Captain" class="official-avatar">
      <h4 class="official-name">Hon. Juan Dela Cruz</h4>
      <p class="official-position">Barangay Captain</p>
      <p class="official-contact"><i class="bi bi-telephone me-2"></i>(046) 123-4567</p>
    </div>
  </div>

  <div class="col-md-4 col-lg-3" data-position="councilor">
    <div class="official-card glass">
      <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3E%3Cpath d='M8 8a3 3 0 100-6 3 3 0 000 6zm0 1c-3.315 0-6 1.79-6 4v1h12v-1c0-2.21-2.685-4-6-4z' fill='%23ffffff'/%3E%3C/svg%3E" alt="Councilor" class="official-avatar">
      <h4 class="official-name">Hon. Maria Santos</h4>
      <p class="official-position">Councilor</p>
      <p class="official-contact"><i class="bi bi-telephone me-2"></i>(046) 123-4568</p>
    </div>
  </div>

  <div class="col-md-4 col-lg-3" data-position="councilor">
    <div class="official-card glass">
      <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3E%3Cpath d='M8 8a3 3 0 100-6 3 3 0 000 6zm0 1c-3.315 0-6 1.79-6 4v1h12v-1c0-2.21-2.685-4-6-4z' fill='%23ffffff'/%3E%3C/svg%3E" alt="Councilor" class="official-avatar">
      <h4 class="official-name">Hon. Pedro Reyes</h4>
      <p class="official-position">Councilor</p>
      <p class="official-contact"><i class="bi bi-telephone me-2"></i>(046) 123-4569</p>
    </div>
  </div>

  <div class="col-md-4 col-lg-3" data-position="councilor">
    <div class="official-card glass">
      <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3E%3Cpath d='M8 8a3 3 0 100-6 3 3 0 000 6zm0 1c-3.315 0-6 1.79-6 4v1h12v-1c0-2.21-2.685-4-6-4z' fill='%23ffffff'/%3E%3C/svg%3E" alt="Councilor" class="official-avatar">
      <h4 class="official-name">Hon. Ana Garcia</h4>
      <p class="official-position">Councilor</p>
      <p class="official-contact"><i class="bi bi-telephone me-2"></i>(046) 123-4570</p>
    </div>
  </div>

  <div class="col-md-4 col-lg-3" data-position="secretary">
    <div class="official-card glass">
      <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3E%3Cpath d='M8 8a3 3 0 100-6 3 3 0 000 6zm0 1c-3.315 0-6 1.79-6 4v1h12v-1c0-2.21-2.685-4-6-4z' fill='%23ffffff'/%3E%3C/svg%3E" alt="Secretary" class="official-avatar">
      <h4 class="official-name">Mrs. Carmen Lim</h4>
      <p class="official-position">Barangay Secretary</p>
      <p class="official-contact"><i class="bi bi-envelope me-2"></i>secretary@bucandala1.gov.ph</p>
    </div>
  </div>

  <div class="col-md-4 col-lg-3" data-position="tanod">
    <div class="official-card glass">
      <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3E%3Cpath d='M8 8a3 3 0 100-6 3 3 0 000 6zm0 1c-3.315 0-6 1.79-6 4v1h12v-1c0-2.21-2.685-4-6-4z' fill='%23ffffff'/%3E%3C/svg%3E" alt="Tanod" class="official-avatar">
      <h4 class="official-name">Mr. Jose Mangubat</h4>
      <p class="official-position">Tanod Chief</p>
      <p class="official-contact"><i class="bi bi-telephone me-2"></i>(046) 123-4571</p>
    </div>
  </div>

  <div class="col-md-4 col-lg-3" data-position="tanod">
    <div class="official-card glass">
      <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3E%3Cpath d='M8 8a3 3 0 100-6 3 3 0 000 6zm0 1c-3.315 0-6 1.79-6 4v1h12v-1c0-2.21-2.685-4-6-4z' fill='%23ffffff'/%3E%3C/svg%3E" alt="Tanod" class="official-avatar">
      <h4 class="official-name">Mr. Mario Basco</h4>
      <p class="official-position">Tanod</p>
      <p class="official-contact"><i class="bi bi-telephone me-2"></i>(046) 123-4572</p>
    </div>
  </div>

  <div class="col-md-4 col-lg-3" data-position="tanod">
    <div class="official-card glass">
      <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3E%3Cpath d='M8 8a3 3 0 100-6 3 3 0 000 6zm0 1c-3.315 0-6 1.79-6 4v1h12v-1c0-2.21-2.685-4-6-4z' fill='%23ffffff'/%3E%3C/svg%3E" alt="Tanod" class="official-avatar">
      <h4 class="official-name">Mr. Rico Dimagiba</h4>
      <p class="official-position">Tanod</p>
      <p class="official-contact"><i class="bi bi-telephone me-2"></i>(046) 123-4573</p>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
// Search
document.getElementById('searchOfficial').addEventListener('input', function(e) {
  const search = e.target.value.toLowerCase();
  document.querySelectorAll('.official-card').forEach(card => {
    const text = card.textContent.toLowerCase();
    card.style.display = text.includes(search) ? 'block' : 'none';
  });
});

// Filter
document.querySelectorAll('.filter-btn').forEach(btn => {
  btn.addEventListener('click', function() {
    document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
    this.classList.add('active');
    
    const filter = this.dataset.filter;
    document.querySelectorAll('.col-md-4').forEach(card => {
      if (filter === 'all' || card.dataset.position === filter) {
        card.style.display = 'block';
      } else {
        card.style.display = 'none';
      }
    });
  });
});
</script>
@endpush