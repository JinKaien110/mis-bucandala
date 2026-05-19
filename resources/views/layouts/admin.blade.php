<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Barangay MIS Admin')</title>

<link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">


  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap5.min.css" rel="stylesheet" />

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

  @yield('styles')

  <style>
    html, body {
      margin: 0;
      padding: 0;
      min-height: 100vh;
      height: 100%;
    }
    
    *, *::before, *::after {
      box-sizing: border-box;
    }
    :root{
      --mis-blue: #1055C9;
      --mis-blue-2: #0d47a1;
      --mis-yellow: #FEEE91;
      --mis-black: #111;
      --mis-white: #fff;
    }
    /* Bootstrap Modal Fix */
    .modal { z-index: 1060 !important; }
    .modal-backdrop { z-index: 1055 !important; }
    .modal.show { z-index: 1061 !important; }
    /* ===========================
   Fancy app background (SAFE)
   =========================== */

/* Body background layer */
.admin-body {
      min-height: 100vh;
      background:
        radial-gradient(900px 420px at 18% 8%, rgba(30,64,175,.16), transparent 60%),
        radial-gradient(700px 380px at 92% 18%, rgba(255,210,77,.22), transparent 55%),
        linear-gradient(135deg, rgb(239, 246, 255) 0%, rgb(224, 231, 255) 100%);
    }

/* Ensure sidebar remains solid and readable */
.sidebar{
  position: sticky;
  z-index: 10;
}

/* Main content area */
.content {
      flex: 1;
      padding: 18px;
      background: transparent;
    }



/* Extra subtle texture on content */
.content::before{
  content:"";
  position: fixed;
  z-index: -1;
  pointer-events:none;
  opacity:.40;
  background-image: radial-gradient(rgba(30,64,175,.14) 1px, transparent 1px);
  background-size: 26px 26px;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
}

/* Soft blobs on content */
.content::after{
  content:"";
  position: fixed;
  z-index: -1;
  pointer-events:none;
  background:
    radial-gradient(circle at 20% 80%, rgba(30,64,175,.22), transparent 55%),
    radial-gradient(circle at 85% 15%, rgba(255,210,77,.26), transparent 55%);
  filter: blur(14px);
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
}



/* Optional: if your pages already use container, this makes it feel premium */
/* ✅ One clean page wrapper only */
.content .page-surface {
      background: rgba(255,255,255,.62);
      border: 1px solid rgba(255,255,255,.55);
      box-shadow: 0 18px 40px rgba(0,0,0,.06);
      border-radius: 18px;
      backdrop-filter: blur(8px);
      -webkit-backdrop-filter: blur(8px);
      padding: 16px;
      box-sizing: border-box;
    }



    .app { 
      min-height: 100vh; 
      display: flex; 
      flex-direction: row; 
    }

    /* Sidebar */
    .sidebar {
      width: 295px;
      background: linear-gradient(180deg, var(--mis-blue), var(--mis-blue-2));
      color: var(--mis-white);
      position: sticky;
      top: 0;
      height: 100vh;
      flex-shrink: 0;
      padding: 16px 14px;
      overflow-y: auto;
    }

    /* ===============================
   Fancy Sidebar Scrollbar
   =============================== */

/* Chrome, Edge, Safari */
.sidebar::-webkit-scrollbar {
  width: 6px;
}

.sidebar::-webkit-scrollbar-track {
  background: transparent;
}

.sidebar::-webkit-scrollbar-thumb {
  background: rgba(255, 255, 255, 0.25);
  border-radius: 10px;
}

.sidebar::-webkit-scrollbar-thumb:hover {
  background: rgba(255, 210, 77, 0.9); /* yellow accent */
}

/* Firefox */
.sidebar {
  scrollbar-width: thin;
  scrollbar-color: rgba(255,255,255,.35) transparent;
}

    .brand{
      display:flex; align-items:center; gap:10px;
      padding: 10px 10px 14px;
      border-bottom: 1px solid rgba(255,255,255,.14);
      margin-bottom: 14px;
    }
    .brand-badge{
      width:40px; height:40px; border-radius: 12px;
      background: var(--mis-yellow);
      color: var(--mis-black);
      display:grid; place-items:center;
      font-weight:900;
      box-shadow: 0 8px 20px rgba(0,0,0,.18);
    }
    .brand-title{ line-height:1.1; }
    .brand-title .name{ font-weight:900; letter-spacing:.3px; }
    .brand-title .sub{ font-size:.85rem; opacity:.85; }

    /* Section button */
    .section-btn{
      width:100%;
      display:flex;
      align-items:center;
      justify-content:space-between;
      padding: 10px 10px;
      border-radius: 12px;
      background: rgba(255,255,255,.08);
      border: 1px solid rgba(255,255,255,.10);
      color: rgba(255,255,255,.92);
      font-weight:800;
      text-decoration:none;
      transition: .15s ease;
    }
    .section-btn:hover{
      background: rgba(255,255,255,.12);
      color: #fff;
    }
    .chev{
      transition: transform .15s ease;
      opacity:.9;
    }
    .section-btn[aria-expanded="true"] .chev{
      transform: rotate(180deg);
    }

    .section-label{
      font-size:.72rem;
      text-transform:uppercase;
      letter-spacing:.08em;
      opacity:.85;
      padding: 12px 10px 6px;
    }

    /* Links */
    .side-link{
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:10px;
      padding: 10px 10px;
      border-radius: 12px;
      color: rgba(255,255,255,.92);
      text-decoration:none;
      transition: .15s ease;
      font-weight:650;
    }
    .side-link:hover{
      background: rgba(255,255,255,.12);
      color: #fff;
    }
    .side-link.active{
      background: var(--mis-yellow);
      color: var(--mis-black);
      box-shadow: 0 10px 20px rgba(0,0,0,.12);
    }
    .side-link .meta{
      font-size:.78rem;
      opacity:.85;
      font-weight:800;
    }

    .topbar{
      background:#fff;
      border: 1px solid rgba(0,0,0,.06);
      border-radius: 14px;
      padding: 12px 14px;
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:12px;
      box-shadow: 0 8px 20px rgba(0,0,0,.04);
      margin-bottom: 14px;
    }
    .page-title{ font-weight:900; margin:0; font-size: 1.05rem; }
    .crumb{ font-size:.85rem; color:#6c757d; }

    /* Mobile */
    @media (max-width: 991px){
      .sidebar{
        position: fixed;
        left: 0; top: 0;
        transform: translateX(-100%);
        transition: transform .2s ease;
        z-index: 999;
        width: 295px;
      }
      .sidebar.show{ transform: translateX(0); }
      .sidebar-backdrop{
        display:none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,.35);
        z-index: 998;
      }
      .sidebar-backdrop.show{ display:block; }
    }

    /* ===============================
    Modern Button Styles
    =============================== */
    
    /* Primary Button - Blue */
    .btn-primary-custom {
      background: linear-gradient(135deg, #1055C9 0%, #0d47a1 100%);
      color: #fff;
      border: none;
      padding: 10px 20px;
      border-radius: 10px;
      font-weight: 600;
      transition: all 0.3s ease;
      box-shadow: 0 4px 15px rgba(16, 85, 201, 0.3);
    }
    
    .btn-primary-custom:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(16, 85, 201, 0.4);
      background: linear-gradient(135deg, #0d47a1 0%, #0a3d8f 100%);
      color: #fff;
    }
    
    .btn-primary-custom:active {
      transform: translateY(0);
    }

    /* Secondary Button - Yellow/Gold */
    .btn-secondary-custom {
      background: linear-gradient(135deg, #FEEE91 0%, #f5d85c 100%);
      color: #1a1a1a;
      border: none;
      padding: 10px 20px;
      border-radius: 10px;
      font-weight: 600;
      transition: all 0.3s ease;
      box-shadow: 0 4px 15px rgba(254, 238, 145, 0.3);
    }
    
    .btn-secondary-custom:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(254, 238, 145, 0.5);
      background: linear-gradient(135deg, #f5d85c 0%, #ebc830 100%);
      color: #1a1a1a;
    }

    /* Outline Primary */
    .btn-outline-primary-custom {
      background: transparent;
      color: #1055C9;
      border: 2px solid #1055C9;
      padding: 8px 18px;
      border-radius: 10px;
      font-weight: 600;
      transition: all 0.3s ease;
    }
    
    .btn-outline-primary-custom:hover {
      background: #1055C9;
      color: #fff;
      box-shadow: 0 4px 15px rgba(16, 85, 201, 0.3);
    }

    /* Outline Secondary */
    .btn-outline-secondary-custom {
      background: transparent;
      color: #b8860b;
      border: 2px solid #d4a520;
      padding: 8px 18px;
      border-radius: 10px;
      font-weight: 600;
      transition: all 0.3s ease;
    }
    
    .btn-outline-secondary-custom:hover {
      background: #FEEE91;
      color: #1a1a1a;
      border-color: #FEEE91;
    }

    /* Success Button */
    .btn-success-custom {
      background: linear-gradient(135deg, #10b981 0%, #059669 100%);
      color: #fff;
      border: none;
      padding: 10px 20px;
      border-radius: 10px;
      font-weight: 600;
      transition: all 0.3s ease;
      box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
    }
    
    .btn-success-custom:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
      background: linear-gradient(135deg, #059669 0%, #047857 100%);
      color: #fff;
    }

    /* Danger Button */
    .btn-danger-custom {
      background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
      color: #fff;
      border: none;
      padding: 10px 20px;
      border-radius: 10px;
      font-weight: 600;
      transition: all 0.3s ease;
      box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3);
    }
    
    .btn-danger-custom:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(239, 68, 68, 0.4);
      background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
      color: #fff;
    }

    /* Warning Button */
    .btn-warning-custom {
      background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
      color: #fff;
      border: none;
      padding: 10px 20px;
      border-radius: 10px;
      font-weight: 600;
      transition: all 0.3s ease;
      box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);
    }
    
    .btn-warning-custom:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(245, 158, 11, 0.4);
      background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
      color: #fff;
    }

    /* Info Button */
    .btn-info-custom {
      background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
      color: #fff;
      border: none;
      padding: 10px 20px;
      border-radius: 10px;
      font-weight: 600;
      transition: all 0.3s ease;
      box-shadow: 0 4px 15px rgba(6, 182, 212, 0.3);
    }
    
    .btn-info-custom:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(6, 182, 212, 0.4);
      background: linear-gradient(135deg, #0891b2 0%, #0e7490 100%);
      color: #fff;
    }

    /* Dark Button */
    .btn-dark-custom {
      background: linear-gradient(135deg, #374151 0%, #1f2937 100%);
      color: #fff;
      border: none;
      padding: 10px 20px;
      border-radius: 10px;
      font-weight: 600;
      transition: all 0.3s ease;
      box-shadow: 0 4px 15px rgba(55, 65, 81, 0.3);
    }
    
    .btn-dark-custom:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(55, 65, 81, 0.4);
      background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
      color: #fff;
    }

    /* Small button variants */
    .btn-sm-custom {
      padding: 6px 14px;
      font-size: 0.85rem;
      border-radius: 8px;
    }

    /* Large button variants */
    .btn-lg-custom {
      padding: 14px 28px;
      font-size: 1.1rem;
      border-radius: 12px;
    }

    /* Icon button */
    .btn-icon-custom {
      width: 40px;
      height: 40px;
      padding: 0;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      border-radius: 10px;
    }
    
    .btn-icon-custom.btn-sm-custom {
      width: 32px;
      height: 32px;
      border-radius: 8px;
    }

    /* ===========================
   Background Animation
   =========================== */
   
    /* Animated floating orbs for admin body */
    .admin-body::before,
    .admin-body::after {
      content: '';
      position: fixed;
      border-radius: 50%;
      filter: blur(100px);
      pointer-events: none;
    }

    .admin-body::before {
      width: 500px;
      height: 500px;
      background: rgba(16, 85, 201, 0.08);
      top: -150px;
      left: -150px;
    }

    .admin-body::after {
      width: 600px;
      height: 600px;
      background: rgba(254, 238, 145, 0.1);
      bottom: -200px;
      right: -200px;
    }

    @keyframes floatAdmin {
      0%, 100% { 
        transform: translate(0, 0) scale(1); 
      }
      33% { 
        transform: translate(40px, -40px) scale(1.05); 
      }
      66% { 
        transform: translate(-30px, 30px) scale(0.95); 
      }
    }

    /* ===============================
    Enhanced Button Styles
    =============================== */
    
    /* Base button enhancements */
    .btn {
      border-radius: 14px;
      padding: 10px 20px;
      font-weight: 600;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      border: none;
    }
    
    /* Round circle buttons - override base btn styles */
    .btn.rounded-circle {
      border-radius: 50% !important;
      padding: 0 !important;
      width: 32px !important;
      height: 32px !important;
      display: inline-flex !important;
      align-items: center !important;
      justify-content: center !important;
      transform: none !important;
    }
    .btn.rounded-circle i {
      margin-right: 0 !important;
      line-height: 1 !important;
    }
    
    .btn:hover {
      transform: translateY(-3px);
    }
    
    .btn:active {
      transform: scale(0.98);
    }
    
    /* Primary Button - Blue #1055C9 */
    .btn-primary {
      background: linear-gradient(135deg, #1055C9 0%, #0d47a1 100%) !important;
      box-shadow: 0 4px 15px rgba(16, 85, 201, 0.4);
    }
    
    .btn-primary:hover {
      background: linear-gradient(135deg, #0d47a1 0%, #0a3d8f 100%) !important;
      box-shadow: 0 8px 25px rgba(16, 85, 201, 0.5);
    }

    /* Secondary Button */
    .btn-secondary {
      background: linear-gradient(135deg, #FEEE91 0%, #f5d85c 100%) !important;
      color: #1a1a1a !important;
      box-shadow: 0 4px 15px rgba(254, 238, 145, 0.4);
    }
    
    .btn-secondary:hover {
      background: linear-gradient(135deg, #f5d85c 0%, #ebc830 100%) !important;
      box-shadow: 0 8px 25px rgba(254, 238, 145, 0.5);
    }

    /* Success Button */
    .btn-success {
      background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
      box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4);
    }
    
    .btn-success:hover {
      box-shadow: 0 8px 25px rgba(16, 185, 129, 0.5);
    }

    /* Danger Button */
    .btn-danger {
      background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%) !important;
      box-shadow: 0 4px 15px rgba(239, 68, 68, 0.4);
    }
    
    .btn-danger:hover {
      box-shadow: 0 8px 25px rgba(239, 68, 68, 0.5);
    }

    /* Warning Button */
    .btn-warning {
      background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important;
      box-shadow: 0 4px 15px rgba(245, 158, 11, 0.4);
    }
    
    .btn-warning:hover {
      box-shadow: 0 8px 25px rgba(245, 158, 11, 0.5);
    }

    /* Dark Button */
    .btn-dark {
      background: linear-gradient(135deg, #374151 0%, #1f2937 100%) !important;
      box-shadow: 0 4px 15px rgba(55, 65, 81, 0.4);
    }
    
    .btn-dark:hover {
      box-shadow: 0 8px 25px rgba(55, 65, 81, 0.5);
    }

    /* Outline buttons */
    .btn-outline-primary {
      border: 2px solid #1055C9 !important;
      color: #1055C9 !important;
    }
    
    .btn-outline-primary:hover {
      background: #1055C9 !important;
      color: #fff !important;
    }

    .btn-outline-secondary {
      border: 2px solid #d4a520 !important;
    }
    
    .btn-outline-secondary:hover {
      background: #FEEE91 !important;
      border-color: #FEEE91 !important;
      color: #1a1a1a !important;
    }

    .btn-outline-danger {
      border: 2px solid #ef4444 !important;
    }

    .btn-outline-success {
      border: 2px solid #10b981 !important;
    }

    /* Small button */
    .btn-sm {
      border-radius: 10px;
      padding: 6px 14px;
    }

    /* Large button */
    .btn-lg {
      border-radius: 16px;
      padding: 14px 28px;
    }

    /* Icon spacing */
    .btn i, .btn [class^="bi-"] {
      margin-right: 5px;
    }

/* Pagination */
    .pagination {
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 8px;
      margin: 30px 0;
      flex-wrap: wrap;
    }

    .pagination .page-item .page-link {
      background: linear-gradient(135deg, rgba(255,255,255,0.2) 0%, rgba(255,255,255,0.1) 100%) !important;
      border: 1px solid rgba(16, 85, 201, 0.3) !important;
      color: #1055C9 !important;
      padding: 10px 16px;
      border-radius: 12px !important;
      font-weight: 500;
      transition: all 0.3s ease;
      min-width: 44px;
      text-align: center;
    }

    .pagination .page-item .page-link:hover {
      background: linear-gradient(135deg, #1055C9 0%, #0d47a1 100%) !important;
      border-color: #1055C9 !important;
      transform: translateY(-2px);
      box-shadow: 0 6px 16px rgba(16, 85, 201, 0.4);
      color: #fff !important;
    }

    .pagination .page-item.active .page-link {
      background: linear-gradient(135deg, #1055C9 0%, #0d47a1 100%) !important;
      border-color: #1055C9 !important;
      color: #fff !important;
      font-weight: 700;
      box-shadow: 0 6px 16px rgba(16, 85, 201, 0.4);
    }

    .pagination .page-item.disabled .page-link {
      background: rgba(255, 255, 255, 0.08) !important;
      border-color: rgba(0, 0, 0, 0.1) !important;
      color: rgba(0, 0, 0, 0.35) !important;
      cursor: not-allowed;
      pointer-events: none;
    }

    .pagination .page-item:first-child .page-link,
    .pagination .page-item:last-child .page-link {
      border-radius: 12px !important;
      min-width: 50px;
    }
  </style>

</head>

<body class="admin-body">
@php
  // active helper - you can refine per route names later
  $isActive = fn($pattern) => request()->routeIs($pattern) ? 'active' : '';

  // open the right collapsible section if any link inside is active
  $openRecords = request()->routeIs('admin.residents') || request()->routeIs('admin.households.*');
  $openServices = request()->routeIs('admin.document-requests.*') || request()->routeIs('admin.document-types.index');
  $openPeace = request()->routeIs('admin.blotters.*') || request()->routeIs('admin.cases.*');
  $openCommunity = request()->routeIs('admin.announcements.*') || request()->routeIs('admin.pets.*') || request()->routeIs('admin.officials.*') || request()->routeIs('admin.events.*');
  $openAdmin = request()->routeIs('admin.logs.*') || request()->routeIs('admin.users.*') || request()->routeIs('admin.reports.*') || request()->routeIs('admin.settings.*');

// Get current user for RBAC
  $currentUser = auth()->user();
  $userRole = $currentUser?->role ?? '';
  $isCaptain = ($userRole === 'admin');
  $isStaff = ($userRole === 'staff');
  $isSecretary = ($userRole === 'secretary');
  $canManageUsers = $currentUser && $isCaptain;
  $canViewUsers = $currentUser && ($isCaptain || $isStaff || $isSecretary);
  $canViewReports = $currentUser && ($isCaptain || $isStaff || $isSecretary);
@endphp

<div class="app">

  <div id="sbBackdrop" class="sidebar-backdrop"></div>

  <aside id="sidebar" class="sidebar">
    <div class="brand">
  <img
    src="{{ asset('storage/branding/barangay-logo.jpg') }}"
    alt="Barangay Logo"
    style="width:42px;height:42px;object-fit:contain;"
    class="rounded-circle"
  >
  <div class="brand-title">
    <div class="name">Barangay Bucandala 1</div>
    <div class="sub">Management Information System</div>
  </div>
</div>

@auth
<div class="px-3 mb-3">
  <div class="d-flex align-items-center justify-content-between">
    <div class="small text-white">
      <div>Welcome, <span class="fw-semibold">{{ $currentUser->admin?->first_name ?? $currentUser->name }}</span>!</div>
      <div class="opacity-75" style="font-size:0.7rem;">{{ ucfirst($currentUser->role) }}</div>
    </div>
    <form method="GET" action="{{ route('logout') }}">
      @csrf
      <button type="submit" class="btn btn-outline-light btn-sm">
        <i class="bi bi-box-arrow-left"></i>
      </button>
    </form>
  </div>
</div>
@endauth

    {{-- DASHBOARD --}}
    <div class="mt-2">
      <a class="side-link {{ $isActive('admin.analytics') }}" href="{{ route('admin.analytics') }}">
        <span><i class="bi bi-speedometer2 me-2"></i>Dashboard</span>
      </a>
    </div>

    {{-- CORE RECORDS --}}
    <div class="mt-3">
      <a class="section-btn" data-bs-toggle="collapse" href="#secRecords" role="button"
         aria-expanded="{{ $openRecords ? 'true' : 'false' }}" aria-controls="secRecords">
        <span>Core Records</span>
        <span class="chev">▾</span>
      </a>
      <div class="collapse {{ $openRecords ? 'show' : '' }} mt-2" id="secRecords">
        <a class="side-link {{ $isActive('admin.residents') }}" href="{{ route('admin.residents') }}">
          <span>Residents Registry</span>
        </a>
        <a class="side-link {{ $isActive('admin.households.*') }}" href="{{ route('admin.households.index') }}">
          <span>Households</span>
        </a>
      </div>
    </div>

    {{-- SERVICES --}}
    <div class="mt-3">
      <a class="section-btn" data-bs-toggle="collapse" href="#secServices" role="button"
         aria-expanded="{{ $openServices ? 'true' : 'false' }}" aria-controls="secServices">
        <span>Services</span>
        <span class="chev">▾</span>
      </a>
      <div class="collapse {{ $openServices ? 'show' : '' }} mt-2" id="secServices">
        <a class="side-link {{ $isActive('admin.document-requests.*') }}" href="{{ route('admin.document-requests.index') }}">
          <span>Document Requests</span>
        </a>
        <a class="side-link {{ $isActive('admin.document-types.index') }}" href="{{ route('admin.document-types.index')}}">
          <span>Document Templates</span>
        </a>
        <a class="side-link {{ $isActive('admin.payments.*') }}" href="{{ route('admin.payments.index') }}">
          <span>Fees / Payments</span>
        </a>
      </div>
    </div>

    {{-- PEACE & ORDER --}}
    <div class="mt-3">
      <a class="section-btn" data-bs-toggle="collapse" href="#secPeace" role="button"
         aria-expanded="{{ $openPeace ? 'true' : 'false' }}" aria-controls="secPeace">
        <span>Peace & Order</span>
        <span class="chev">▾</span>
      </a>
      <div class="collapse {{ $openPeace ? 'show' : '' }} mt-2" id="secPeace">
        <a class="side-link {{ $isActive('admin.blotters.*') }}" href="{{ route('admin.blotters.index') }}">
          <span>Blotters</span>
        </a>
        <a class="side-link {{ $isActive('admin.cases.*') }}" href="{{ route('admin.cases.index') }}">
          <span>Cases</span>
        </a>
      </div>
    </div>

    {{-- COMMUNITY --}}
    <div class="mt-3">
      <a class="section-btn" data-bs-toggle="collapse" href="#secCommunity" role="button"
         aria-expanded="{{ $openCommunity ? 'true' : 'false' }}" aria-controls="secCommunity">
        <span>Community</span>
        <span class="chev">▾</span>
      </a>
      <div class="collapse {{ $openCommunity ? 'show' : '' }} mt-2" id="secCommunity">
        <a class="side-link {{ $isActive('admin.announcements.*') }}" href="{{ route('admin.announcements.index') }}">
          <span>Announcements</span>
        </a>
        <a class="side-link {{ $isActive('admin.officials.*') }}" href="{{ route('admin.officials.index') }}">
          <span>Barangay Officials</span>
        </a>
        <a class="side-link {{ $isActive('admin.events.*') }}" href="{{ route('admin.events.index') }}">
          <span>Events / Calendar</span>
        </a>
        <a class="side-link {{ $isActive('admin.pets.*') }}" href="{{ route('admin.pets.index') }}">
          <span>Pet Registration</span>
        </a>
      </div>
    </div>

    {{-- ADMIN --}}
    <div class="mt-3">
      <a class="section-btn" data-bs-toggle="collapse" href="#secAdmin" role="button"
         aria-expanded="{{ $openAdmin ? 'true' : 'false' }}" aria-controls="secAdmin">
        <span>Administration</span>
        <span class="chev">▾</span>
      </a>
      <div class="collapse {{ $openAdmin ? 'show' : '' }} mt-2" id="secAdmin">
        <a class="side-link {{ $isActive('admin.logs.*') }}" href="{{ route('admin.logs.index') }}">
          <span>Activity Logs</span>
        </a>
        @if($canViewUsers)
        <a class="side-link {{ $isActive('admin.users.*') }}" href="{{ route('admin.users.index') }}">
          <span>Users & Roles</span>
        </a>
        @endif
         @if($canViewReports)
         <a class="side-link {{ $isActive('admin.reports.*') }}" href="{{ route('admin.reports.index') }}">
           <span>Reports</span>
         </a>
         @endif
         <a class="side-link {{ $isActive('admin.archive.*') }}" href="{{ route('admin.archive.index') }}">
           <span>Archive</span>
         </a>
       </div>
    </div>

    <div class="mt-4 pt-3 border-top border-light border-opacity-25">
      <div class="sidebar-footer">
        <div class="small opacity-75">
          © {{ date('Y') }} Barangay Bucandala 1<br>
          Management Information System
        </div>
      </div>
    </div>
  </aside>

<main class="content">


    @yield('content')
  </main>

  @stack('modals')
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

@stack('scripts')

<script>
  // mobile sidebar toggle
  (function () {
    const sb = document.getElementById('sidebar');
    const btn = document.getElementById('btnSidebar');
    const bd = document.getElementById('sbBackdrop');

    if (!sb || !btn || !bd) return;

    function open() { sb.classList.add('show'); bd.classList.add('show'); }
    function close() { sb.classList.remove('show'); bd.classList.remove('show'); }

    btn.addEventListener('click', open);
    bd.addEventListener('click', close);
  })();
</script>
</body>
</html>

