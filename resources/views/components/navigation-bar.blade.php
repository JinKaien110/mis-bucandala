 @php
$isAuthenticated = auth()->check();
$user = $isAuthenticated ? auth()->user() : null;
$isAdmin = $isAuthenticated && ($user->role === 'admin');
$isResident = $isAuthenticated && ($user->role === 'resident');
@endphp

<nav class="navbar navbar-expand-lg navbar-glass fixed-top">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center gap-2" href="/">
      <img src="{{ asset('storage/branding/barangay-logo.jpg') }}" alt="Logo" width="40" height="40" class="rounded-circle" onerror="this.src='https://via.placeholder.com/40?text=BRGY'">
      <span class="fw-bold" style="color: #ffffff;">Barangay Bucandala 1</span>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" style="background: rgba(255,255,255,0.15);">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto align-items-center">
        {{-- All Public Pages (shown for everyone) --}}
        <li class="nav-item">
          <a class="nav-link" href="/" style="color: rgba(255,255,255,0.9);">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="/#about" style="color: rgba(255,255,255,0.9);">About</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="/#services" style="color: rgba(255,255,255,0.9);">Services</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="/#news" style="color: rgba(255,255,255,0.9);">News</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="/#officials" style="color: rgba(255,255,255,0.9);"> Officials</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="/#faqs" style="color: rgba(255,255,255,0.9);">FAQs</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="/#contact" style="color: rgba(255,255,255,0.9);">Contact</a>
        </li>

        @if($isAuthenticated && ($isAdmin || $isResident))
          {{-- Logged in user: Show My Portal dropdown --}}
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="color: rgba(255,255,255,0.9);">
              <i class="bi bi-person-fill me-1"></i>My Portal
            </a>
            <ul class="dropdown-menu dropdown-menu-end" style="background: rgba(16, 85, 201, 0.98); border: 1px solid rgba(255,255,255,0.15);">
              @if($isAdmin)
                <li><a class="dropdown-item" href="{{ route('admin.residents') }}" style="color: #fff;">Admin Dashboard</a></li>
              @endif
              <li><a class="dropdown-item" href="{{ route('resident.dashboard') }}" style="color: #fff;">Dashboard</a></li>
              <li><a class="dropdown-item" href="{{ route('resident.profile') }}" style="color: #fff;">My Profile</a></li>
              <li><a class="dropdown-item" href="#" style="color: #fff;">My Requests</a></li>
              <li><a class="dropdown-item" href="{{ route('resident.pets') }}" style="color: #fff;">Pet Registration</a></li>
              <li><a class="dropdown-item" href="{{ route('resident.household') }}" style="color: #fff;">Household Registry</a></li>
              <li><hr class="dropdown-divider" style="border-color: rgba(255,255,255,0.2);"></li>
              <li><a class="dropdown-item" href="{{ route('logout') }}" style="color: #fca5a5;"><i class="bi bi-box-arrow-right me-1"></i>Logout</a></li>
            </ul>
          </li>
        @else
          {{-- Guest: Show Login/Register buttons --}}
          <li class="nav-item ms-lg-2">
            <a class="btn btn-outline-light btn-sm" href="{{ route('login') }}" style="border-color: rgba(255,255,255,0.5); color: #fff; padding: 6px 14px; font-size: 0.875rem;">
              <i class="bi bi-box-arrow-in-right me-1"></i>Login
            </a>
          </li>
          <li class="nav-item ms-lg-2">
            <a class="btn btn-sm" href="{{ route('public.residents.register') }}" style="background: #ffffff; color: #1055C9; border: none; padding: 6px 14px; font-size: 0.875rem; font-weight: 600;">
              <i class="bi bi-person-plus me-1"></i>Register
            </a>
          </li>
        @endif
      </ul>
    </div>
  </div>
</nav>