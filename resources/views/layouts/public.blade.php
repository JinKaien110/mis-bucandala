<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="description" content="{{ $pageDescription ?? 'Barangay Bucandala 1 - City of Imus, Cavite' }}">
  <title>{{ $pageTitle ?? 'Barangay Bucandala 1' }}</title>
  <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

   <style>
     :root {
       /* Base MIS colors */
       --mis-white: #FFFFFF;
       --mis-yellow: #FFD700;
       --mis-yellow-light: #FFE44D;
       --mis-yellow-dark: #FFC107;
       --mis-blue: #1055C9;
       --mis-blue-light: #3b82f6;
       --mis-blue-dark: #0d47a1;
       --mis-green: #28a745;

       /* Adaptive Glass System - Auto-adjusts to background */
       --glass-bg: color-mix(in srgb, var(--mis-blue-dark) 15%, transparent 85%);
       --glass-border: color-mix(in srgb, var(--mis-yellow) 30%, transparent 70%);
       --glass-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.3);
       --glass-blur: blur(16px);

       /* Adaptive text colors with auto-contrast */
       --text-primary: #ffffff;
       --text-secondary: color-mix(in srgb, var(--mis-white) 90%, var(--mis-blue-dark) 10%);
       --text-muted: color-mix(in srgb, var(--mis-white) 70%, var(--mis-blue-dark) 30%);
       --text-accent: var(--mis-yellow);

       /* Auto-adapting accent colors */
       --accent-primary: var(--mis-yellow);
       --accent-secondary: color-mix(in srgb, var(--mis-yellow) 60%, var(--mis-blue-light) 40%);
       --accent-tertiary: color-mix(in srgb, var(--mis-white) 80%, var(--mis-blue) 20%);

       /* Dynamic form field colors */
       --input-bg: color-mix(in srgb, var(--mis-white) 25%, var(--mis-blue-dark) 75%);
       --input-border: color-mix(in srgb, var(--mis-yellow) 40%, transparent 60%);
       --input-focus-glow: color-mix(in srgb, var(--mis-yellow) 30%, transparent 70%);

        /* Harmonious card layers - subtle blue tint, minimal white */
        --card-elevation-1: color-mix(in srgb, var(--mis-blue-dark) 18%, transparent 82%);
        --card-elevation-2: color-mix(in srgb, var(--mis-blue-dark) 22%, transparent 78%);
        --card-elevation-3: color-mix(in srgb, var(--mis-blue-dark) 14%, transparent 86%);

        /* Subtle gradient overlays for depth */
        --gradient-overlay-light: linear-gradient(135deg, rgba(255,255,255,0.04) 0%, rgba(255,255,255,0.01) 100%);
        --gradient-overlay-medium: linear-gradient(135deg, rgba(255,255,255,0.02) 0%, rgba(255,255,255,0.005) 100%);
     }

     * { margin: 0; padding: 0; box-sizing: border-box; }

     html, body {
       overflow-x: hidden;
     }

     body {
       font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
       background: linear-gradient(135deg, #0d47a1 0%, #1055C9 50%, #1976D2 100%);
       min-height: 100vh;
       color: var(--text-primary);
     }

     body::before {
       content: '';
       position: fixed;
       top: 0;
       left: 0;
       right: 0;
       bottom: 0;
       background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
       z-index: 0;
       pointer-events: none;
       mix-blend-mode: overlay;
     }

     a { color: var(--mis-yellow); }
     a:hover { color: var(--mis-white); }

     h1, h2, h3, h4, h5, h6 {
       color: var(--text-primary);
       text-shadow: 0 2px 4px rgba(0,0,0,0.2);
     }

     p, span, li, td, th {
       color: var(--text-secondary);
     }

     .text-white {
       color: var(--text-primary) !important;
     }

     .text-muted {
       color: var(--text-muted) !important;
     }

     .section-title {
       color: var(--mis-yellow);
       text-shadow: 0 2px 8px rgba(255, 215, 0, 0.3);
     }

    .bg-animation {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      overflow: hidden;
      z-index: 0;
      pointer-events: none;
    }

    .orb {
      position: absolute;
      border-radius: 50%;
      filter: blur(100px);
      animation: float 25s ease-in-out infinite;
      max-width: 100vw;
    }

    .orb-1 { width: 50vw; height: 50vw; max-width: 600px; max-height: 600px; background: rgba(255, 215, 0, 0.08); top: -20vh; left: -20vw; }
    .orb-2 { width: 40vw; height: 40vw; max-width: 500px; max-height: 500px; background: rgba(255, 255, 255, 0.05); bottom: -10vh; right: -10vw; animation-delay: -8s; }
    .orb-3 { width: 35vw; height: 35vw; max-width: 400px; max-height: 400px; background: rgba(255, 193, 7, 0.06); top: 40%; left: 50%; animation-delay: -16s; }
    .orb-4 { width: 30vw; height: 30vw; max-width: 300px; max-height: 300px; background: rgba(255, 228, 77, 0.08); top: 10%; right: 20%; animation-delay: -12s; }

    @keyframes float {
      0%, 100% { transform: translate(0, 0) scale(1) rotate(0deg); }
      25% { transform: translate(30px, -30px) scale(1.05) rotate(5deg); }
      50% { transform: translate(-20px, 20px) scale(0.95) rotate(-5deg); }
      75% { transform: translate(20px, 30px) scale(1.02) rotate(3deg); }
    }

    .floating-icons {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      pointer-events: none;
      z-index: 1;
      overflow: hidden;
    }

    .floating-icon {
      position: absolute;
      font-size: 2rem;
      opacity: 0.15;
      animation: drift 20s ease-in-out infinite;
      filter: drop-shadow(0 4px 8px rgba(0,0,0,0.2));
      max-width: 100vw;
    }

    .floating-icon.house { color: #FFD700; }
    .floating-icon.paw { color: #FFC107; }
    .floating-icon.document { color: #1055C9; }
    .floating-icon.bell { color: #FFD700; }
    .floating-icon.badge { color: #FFC107; }
    .floating-icon.users { color: #1055C9; }
    .floating-icon.calendar { color: #FFD700; }
    .floating-icon.child { color: #FFD700; }

    @keyframes drift {
      0%, 100% { 
        transform: translate(0, 0) rotate(0deg) scale(1);
        opacity: 0;
      }
      10% { opacity: 0.15; }
      50% { 
        transform: translate(var(--tx), var(--ty)) rotate(var(--rot)) scale(1.1);
        opacity: 0.12;
      }
      90% { opacity: 0.15; }
    }

    .floating-icon:nth-child(1) { top: 10%; left: 5%; animation-delay: 0s; --tx: 5vw; --ty: 2vh; --rot: 15deg; }
    .floating-icon:nth-child(2) { top: 20%; left: 85%; animation-delay: -3s; --tx: -4vw; --ty: 4vh; --rot: -10deg; }
    .floating-icon:nth-child(3) { top: 50%; left: 3%; animation-delay: -5s; --tx: 6vh; --ty: -3vw; --rot: 20deg; }
    .floating-icon:nth-child(4) { top: 70%; left: 90%; animation-delay: -8s; --tx: -5vw; --ty: -2vh; --rot: -15deg; }
    .floating-icon:nth-child(5) { top: 35%; left: 50%; animation-delay: -12s; --tx: 3vw; --ty: 5vh; --rot: 10deg; }
    .floating-icon:nth-child(6) { top: 80%; left: 20%; animation-delay: -2s; --tx: 4vw; --ty: -4vh; --rot: -20deg; }
    .floating-icon:nth-child(7) { top: 15%; left: 35%; animation-delay: -6s; --tx: -3vw; --ty: 2vh; --rot: 25deg; }
    .floating-icon:nth-child(8) { top: 60%; left: 75%; animation-delay: -10s; --tx: -4.5vw; --ty: 3vh; --rot: -5deg; }
    .floating-icon:nth-child(9) { top: 45%; left: 15%; animation-delay: -15s; --tx: 2.5vw; --ty: -4.5vh; --rot: 12deg; }
    .floating-icon:nth-child(10) { top: 25%; left: 65%; animation-delay: -18s; --tx: -3.5vw; --ty: -3.5vh; --rot: -18deg; }

     .glass {
       background: var(--glass-bg);
       backdrop-filter: var(--glass-blur);
       -webkit-backdrop-filter: var(--glass-blur);
       border: 1px solid var(--glass-border);
       border-radius: 20px;
       box-shadow: var(--glass-shadow);
       transition: all 0.3s ease;
       position: relative;
       overflow: hidden;
     }

     /* Glass subtle gradient overlay for depth */
     .glass::before {
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

     .glass:hover {
       transform: translateY(-3px);
       box-shadow: 0 12px 40px color-mix(in srgb, var(--mis-blue) 30%, transparent 70%);
       border-color: color-mix(in srgb, var(--mis-yellow) 50%, transparent 50%);
     }

     .glass-card {
       background: var(--card-elevation-1);
       backdrop-filter: blur(16px);
       -webkit-backdrop-filter: blur(16px);
       border: 1px solid color-mix(in srgb, var(--mis-yellow) 25%, transparent 75%);
       border-radius: 16px;
       padding: 24px;
       transition: all 0.3s;
       position: relative;
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
        transform: translateY(-4px);
        box-shadow: 0 16px 48px color-mix(in srgb, var(--mis-blue) 35%, transparent 65%);
        border-color: color-mix(in srgb, var(--mis-yellow) 45%, transparent 55%);
        background: var(--card-elevation-2);
      }

     .glass-light {
       background: var(--card-elevation-3);
       backdrop-filter: blur(12px);
       -webkit-backdrop-filter: blur(12px);
       border: 1px solid color-mix(in srgb, var(--mis-white) 20%, transparent 80%);
       border-radius: 16px;
     }

     /* Enhanced Buttons with adaptive colors */
     .btn-primary, .btn-primary-custom {
       background: linear-gradient(135deg, var(--mis-yellow) 0%, var(--mis-yellow-dark) 100%);
       color: var(--mis-blue-dark);
       border: none;
       padding: 12px 24px;
       border-radius: 10px;
       font-weight: 600;
       box-shadow: 0 4px 15px color-mix(in srgb, var(--mis-yellow) 40%, transparent 60%);
       transition: all 0.3s ease;
     }

     .btn-primary:hover, .btn-primary-custom:hover {
       background: linear-gradient(135deg, var(--mis-yellow-light) 0%, var(--mis-yellow) 100%);
       transform: translateY(-2px) scale(1.02);
       box-shadow: 0 8px 25px color-mix(in srgb, var(--mis-yellow) 60%, transparent 40%);
       color: var(--mis-blue-dark);
     }

     .btn-primary:active, .btn-primary-custom:active {
       transform: scale(0.98);
     }

     .btn-secondary {
       background: var(--glass-bg);
       color: var(--text-primary);
       border: 1px solid color-mix(in srgb, var(--mis-yellow) 40%, transparent 60%);
       padding: 12px 24px;
       border-radius: 10px;
       font-weight: 500;
       transition: all 0.3s ease;
     }

     .btn-secondary:hover {
       background: color-mix(in srgb, var(--mis-yellow) 20%, var(--glass-bg) 80%);
       color: var(--mis-yellow);
       transform: translateY(-2px);
       border-color: color-mix(in srgb, var(--mis-yellow) 60%, transparent 40%);
     }

     .btn-glass {
       background: var(--glass-bg);
       backdrop-filter: blur(8px);
       border: 1px solid color-mix(in srgb, var(--mis-white) 30%, transparent 70%);
       color: var(--text-primary);
       padding: 12px 28px;
       border-radius: 12px;
       font-weight: 600;
       transition: all 0.3s ease;
     }

     .btn-glass:hover {
       background: color-mix(in srgb, var(--mis-yellow) 15%, var(--glass-bg) 85%);
       color: var(--mis-blue-dark);
       transform: translateY(-2px);
       box-shadow: 0 8px 24px color-mix(in srgb, var(--mis-yellow) 30%, transparent 70%);
     }

     .btn-glass-primary {
       background: linear-gradient(135deg, var(--mis-yellow) 0%, var(--mis-yellow-dark) 100%);
       border: none;
       color: var(--mis-blue-dark);
       padding: 12px 24px;
       border-radius: 12px;
       font-weight: 600;
       box-shadow: 0 4px 16px color-mix(in srgb, var(--mis-yellow) 50%, transparent 50%);
       transition: all 0.3s ease;
     }

     .btn-glass-primary:hover {
       transform: translateY(-2px);
       box-shadow: 0 8px 28px color-mix(in srgb, var(--mis-yellow) 70%, transparent 30%);
       color: var(--mis-blue-dark);
     }

    /* Pagination */
    .pagination {
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 10px;
      margin: 30px 0;
      flex-wrap: wrap;
    }

    .pagination .page-link {
      background: rgba(255, 255, 255, 0.12) !important;
      border: 2px solid rgba(255, 215, 0, 0.35) !important;
      color: #ffffff !important;
      padding: 14px 20px !important;
      border-radius: 14px !important;
      font-weight: 600 !important;
      font-size: 15px !important;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
      min-width: 55px;
      text-align: center;
      box-shadow: 0 2px 8px rgba(0,0,0,0.2);
    }

    .pagination .page-link:hover {
      background: rgba(255, 215, 0, 0.25) !important;
      border-color: #FFD700 !important;
      transform: translateY(-4px) scale(1.08) !important;
      box-shadow: 0 12px 28px rgba(255, 215, 0, 0.4) !important;
      color: #FFD700 !important;
    }

    .pagination .page-item.active .page-link {
      background: linear-gradient(135deg, #FFD700 0%, #FFC107 100%) !important;
      border-color: #FFD700 !important;
      color: #1f2937 !important;
      font-weight: 700 !important;
      box-shadow: 0 8px 24px rgba(255, 215, 0, 0.5) !important;
      transform: translateY(-2px);
    }

    .pagination .page-item.disabled .page-link {
      background: rgba(255, 255, 255, 0.05) !important;
      border-color: rgba(255, 255, 255, 0.15) !important;
      color: rgba(255, 255, 255, 0.35) !important;
      cursor: not-allowed !important;
      pointer-events: none !important;
      box-shadow: none !important;
    }

    .pagination .page-item:first-child .page-link,
    .pagination .page-item:last-child .page-link {
      border-radius: 14px !important;
      min-width: 70px;
    }

    /* Form Elements */
    .form-label {
      color: #ffffff;
      font-weight: 500;
      margin-bottom: 8px;
    }

     .form-control, .form-select {
       background: var(--input-bg);
       border: 1px solid var(--input-border);
       color: var(--mis-blue-dark);
       border-radius: 12px;
       padding: 12px 16px;
       transition: all 0.3s ease;
     }

     .form-control:focus, .form-select:focus {
       background: color-mix(in srgb, var(--mis-white) 35%, var(--mis-blue-dark) 65%);
       border-color: var(--mis-yellow);
       box-shadow: 0 0 0 3px var(--input-focus-glow);
       color: var(--mis-blue-dark);
       outline: none;
     }

     .form-control::placeholder {
       color: color-mix(in srgb, var(--mis-blue-dark) 60%, transparent 40%);
     }

     .form-control.is-invalid {
       border-color: #dc2626;
     }

     .invalid-feedback {
       color: #fca5a5;
       font-size: 0.85rem;
       margin-top: 4px;
     }

     .form-select option {
       background: var(--mis-blue-dark);
       color: var(--mis-white);
     }

    /* Tables */
    .table {
      color: #ffffff;
    }

    .table thead th {
      background: rgba(255, 215, 0, 0.15);
      color: #ffffff;
      font-weight: 600;
      border-bottom: 2px solid rgba(255, 215, 0, 0.3);
      padding: 14px 16px;
    }

    .table tbody td {
      background: rgba(255, 255, 255, 0.05);
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
      padding: 14px 16px;
      color: rgba(255, 255, 255, 0.9);
    }

    .table tbody tr:hover td {
      background: rgba(255, 255, 255, 0.1);
    }

    /* Cards */
    .card {
      background: rgba(255, 255, 255, 0.1);
      border: 1px solid rgba(255, 255, 255, 0.15);
      border-radius: 16px;
      color: #ffffff;
    }

    .card-header {
      background: rgba(255, 215, 0, 0.1);
      border-bottom: 1px solid rgba(255, 215, 0, 0.2);
      color: #ffffff;
      font-weight: 600;
      padding: 16px 20px;
    }

    .card-body {
      color: #ffffff;
      padding: 20px;
    }

    /* Modal fix for public pages */
    .modal-backdrop {
      z-index: 1050 !important;
      background-color: rgba(0, 0, 0, 0.7) !important;
    }
    
    .modal {
      z-index: 1060 !important;
    }
    
    .modal.show {
      display: flex !important;
      align-items: center;
      justify-content: center;
    }
    
    .modal-backdrop.show {
      opacity: 0.7 !important;
    }

    .navbar-glass {
      background: rgba(255, 255, 255, 0.15);
      backdrop-filter: blur(20px);
      -webkit-backdrop-filter: blur(20px);
      border-bottom: 1px solid rgba(255, 215, 0, 0.15);
      z-index: 1040;
      transition: background 0.3s ease;
    }

    .navbar-glass.fixed-top {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
    }

    .nav-link {
      color: rgba(31, 41, 55, 0.85) !important;
      font-weight: 500;
      transition: all 0.3s ease;
      position: relative;
      padding: 10px 16px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      font-size: 0.95rem;
    }

    .nav-link:hover { 
      color: #1f2937 !important; 
      background: linear-gradient(90deg, rgba(255,215,0,0.2) 0%, rgba(255,215,0,0.1) 100%);
      transform: translateX(4px);
    }

    .nav-link.active {
      background: linear-gradient(90deg, rgba(255,215,0,0.5) 0%, rgba(255,215,0,0.25) 100%);
      color: #1f2937 !important;
      box-shadow: 0 4px 16px rgba(255, 215, 0, 0.3);
      border: 1px solid rgba(255, 193, 7, 0.3);
    }

    .nav-link.active i {
      color: var(--mis-yellow-dark);
    }

    .dropdown-menu {
      background: rgba(255, 255, 255, 0.98);
      border: 1px solid rgba(255, 215, 0, 0.2);
      border-radius: 12px;
      padding: 8px;
    }

    .dropdown-item {
      color: rgba(31, 41, 55, 0.9);
      padding: 10px 16px;
      border-radius: 8px;
      transition: all 0.2s;
    }

    .dropdown-item:hover {
      background: rgba(255, 215, 0, 0.15);
      color: #1f2937;
    }

    .main-content {
      padding: 120px 0 60px;
      position: relative;
      z-index: 1;
      max-width: 100%;
      overflow-x: hidden;
      width: 100%;
    }

    .page-wrapper {
      max-width: 100%;
      overflow-x: hidden;
    }

    .page-title {
      font-size: 2.5rem;
      font-weight: 700;
      margin-bottom: 8px;
    }

    .page-header p {
      opacity: 0.75;
    }

    .section-title {
      font-size: 1.5rem;
      font-weight: 600;
      margin-bottom: 20px;
      color: var(--mis-yellow);
    }

    .contact-card {
      padding: 20px;
      display: flex;
      align-items: flex-start;
      gap: 16px;
      margin-bottom: 16px;
      border-radius: 12px;
      background: rgba(255, 255, 255, 0.05);
      border: 1px solid rgba(255, 255, 255, 0.1);
      transition: all 0.3s;
    }

    .contact-card:hover {
      background: rgba(255, 255, 255, 0.1);
      transform: translateY(-2px);
    }

    .contact-icon {
      width: 50px;
      height: 50px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 22px;
      background: rgba(255, 255, 255, 0.15);
      flex-shrink: 0;
    }

    .emergency-card {
      padding: 20px;
      border-radius: 16px;
      display: flex;
      align-items: center;
      gap: 16px;
      transition: all 0.3s;
      cursor: pointer;
      text-decoration: none;
      color: #fff;
    }

    .emergency-card:hover {
      transform: translateY(-4px);
    }

    .emergency-card.police {
      background: linear-gradient(135deg, #FFD700 0%, #FFC107 100%);
    }

    .emergency-card.fire {
      background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
    }

    .emergency-card.medical {
      background: linear-gradient(135deg, #059669 0%, #10b981 100%);
    }

    .emergency-card.disaster {
      background: linear-gradient(135deg, #ea580c 0%, #f97316 100%);
    }

    .emergency-icon {
      width: 50px;
      height: 50px;
      border-radius: 50%;
      background: rgba(255, 255, 255, 0.2);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 24px;
    }

    .form-control, .form-textarea {
      background: rgba(255, 255, 255, 0.2);
      border: 1px solid rgba(255, 215, 0, 0.3);
      color: #1f2937;
      padding: 14px 18px;
      border-radius: 12px;
    }

    .form-control:focus, .form-textarea:focus {
      background: rgba(255, 255, 255, 0.3);
      border-color: var(--mis-yellow-dark);
      color: #1f2937;
      box-shadow: none;
      outline: none;
    }

    .form-control::placeholder, .form-textarea::placeholder { color: rgba(31, 41, 55, 0.5); }
    .form-textarea { min-height: 150px; resize: vertical; }

    .form-label {
      color: rgba(31, 41, 55, 0.9);
      font-weight: 500;
      margin-bottom: 8px;
    }

    .form-control.is-invalid {
      border-color: #ef4444;
    }

    .invalid-feedback {
      color: #fca5a5;
      font-size: 0.85rem;
      margin-top: 4px;
    }

    .btn-primary-glass {
      background: linear-gradient(135deg, #FFD700 0%, #FFC107 100%);
      border: none;
      color: #1f2937;
      padding: 14px 32px;
      border-radius: 14px;
      font-weight: 600;
      box-shadow: 0 8px 25px rgba(255, 215, 0, 0.4);
      transition: all 0.3s ease;
    }

    .btn-primary-glass:hover {
      transform: translateY(-3px);
      box-shadow: 0 12px 35px rgba(255, 215, 0, 0.5);
      color: #1f2937;
    }

    .btn-primary-glass.btn-sm {
      padding: 8px 16px;
      font-size: 0.875rem;
      border-radius: 10px;
    }

    .btn-outline-light.btn-sm {
      padding: 8px 16px;
      font-size: 0.875rem;
      border-radius: 10px;
      border-width: 1.5px;
    }

    .btn-outline-light.btn-sm:hover {
      background: rgba(255, 255, 255, 0.2);
      border-color: #fff;
    }

    .btn-glass {
      background: rgba(255, 255, 255, 0.15);
      border: 1px solid rgba(255, 255, 255, 0.25);
      color: #fff;
      padding: 12px 28px;
      border-radius: 12px;
      font-weight: 600;
      transition: all 0.3s ease;
    }

    .btn-glass:hover {
      background: rgba(255, 255, 255, 0.25);
      color: #fff;
      transform: translateY(-2px);
    }

    .hours-card {
      padding: 20px;
    }

    .hours-row {
      display: flex;
      justify-content: space-between;
      padding: 12px 0;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .hours-row:last-child { border-bottom: none; }

    .status-badge {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 6px 12px;
      border-radius: 20px;
      font-size: 0.85rem;
      font-weight: 500;
    }

    .status-open {
      background: rgba(34, 197, 94, 0.2);
      color: #4ade80;
    }

    .status-closed {
      background: rgba(239, 68, 68, 0.2);
      color: #fca5a5;
    }

    .status-dot {
      width: 8px;
      height: 8px;
      border-radius: 50%;
      animation: pulse 2s infinite;
    }

    .status-open .status-dot { background: #4ade80; }
    .status-closed .status-dot { background: #fca5a5; }

    @keyframes pulse {
      0%, 100% { opacity: 1; }
      50% { opacity: 0.5; }
    }

    .social-links {
      display: flex;
      gap: 12px;
    }

    .social-link {
      width: 44px;
      height: 44px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 20px;
      background: rgba(255, 255, 255, 0.1);
      border: 1px solid rgba(255, 255, 255, 0.2);
      transition: all 0.3s;
      text-decoration: none;
      color: #fff;
    }

    .social-link:hover {
      background: var(--mis-yellow);
      color: #1f2937;
      transform: translateY(-3px);
    }

    .map-container {
      border-radius: 16px;
      overflow: hidden;
      height: 350px;
      background: rgba(255, 255, 255, 0.1);
    }

    .map-container iframe {
      width: 100%;
      height: 100%;
      border: none;
    }

    .whatsapp-btn {
      position: fixed;
      bottom: 30px;
      right: 30px;
      width: 60px;
      height: 60px;
      border-radius: 50%;
      background: #25D366;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 30px;
      color: white;
      box-shadow: 0 8px 25px rgba(37, 211, 102, 0.4);
      z-index: 1000;
      transition: all 0.3s;
      text-decoration: none;
    }

    .whatsapp-btn:hover {
      transform: translateY(-4px) scale(1.1);
      color: white;
    }

    .modal-content {
      background: rgba(255, 255, 255, 0.98);
      border: none;
      border-radius: 20px;
      color: #1f2937;
    }

    .modal-title {
      color: #1f2937;
      font-weight: 600;
    }

    .rating-stars {
      display: flex;
      gap: 8px;
      margin-bottom: 16px;
    }

    .rating-star {
      font-size: 28px;
      cursor: pointer;
      color: rgba(31, 41, 55, 0.3);
      transition: all 0.2s;
    }

    .rating-star:hover,
    .rating-star.active {
      color: var(--mis-yellow);
      transform: scale(1.1);
    }

.footer-glass {
      background: rgba(255, 215, 0, 0.1);
      backdrop-filter: blur(20px);
      -webkit-backdrop-filter: blur(20px);
      border-top: 1px solid rgba(255, 215, 0, 0.2);
      padding: 40px 0 20px;
      position: relative;
      z-index: 1;
      max-width: 100%;
      overflow-x: hidden;
    }

    .footer-links h5 {
      font-weight: 600;
      margin-bottom: 16px;
      color: #1f2937;
    }

    .footer-links a {
      color: rgba(31, 41, 55, 0.75);
      text-decoration: none;
      font-size: 0.9rem;
    }

    .footer-links a:hover { color: #1f2937; }

    .footer-bottom {
      border-top: 1px solid rgba(255, 215, 0, 0.2);
      margin-top: 30px;
      padding-top: 20px;
      text-align: center;
      font-size: 0.85rem;
      color: rgba(31, 41, 55, 0.6);
    }

    .social-link {
      width: 44px;
      height: 44px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 20px;
      background: rgba(255, 215, 0, 0.15);
      border: 1px solid rgba(255, 215, 0, 0.3);
      transition: all 0.3s;
      text-decoration: none;
      color: #1f2937;
    }

    .social-link:hover {
      background: var(--mis-yellow);
      color: #1f2937;
      transform: translateY(-3px);
    }

    .footer-links h5 {
      font-weight: 600;
      margin-bottom: 16px;
      color: var(--mis-yellow);
    }

    .footer-links ul {
      list-style: none;
      padding: 0;
      margin: 0;
    }

    .footer-links li { margin-bottom: 8px; }

    .footer-links a {
      color: rgba(255, 255, 255, 0.75);
      text-decoration: none;
      font-size: 0.9rem;
    }

    .footer-links a:hover { color: #fff; }

    .footer-bottom {
      border-top: 1px solid rgba(255, 255, 255, 0.1);
      margin-top: 30px;
      padding-top: 20px;
      text-align: center;
      font-size: 0.85rem;
      color: rgba(255, 255, 255, 0.6);
    }

    /* User Avatar */
    .user-avatar {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background: linear-gradient(135deg, #FFD700 0%, #FFC107 100%);
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 600;
      box-shadow: 0 4px 16px rgba(255, 215, 0, 0.5), inset 0 1px 0 rgba(255, 255, 255, 0.3);
      transition: all 0.3s ease;
    }

    .user-avatar:hover {
      transform: scale(1.08);
      box-shadow: 0 6px 24px rgba(255, 215, 0, 0.6), 0 0 0 3px rgba(255, 193, 7, 0.3);
    }

    /* Dropdown Glass */
    .dropdown-menu-glass {
      background: linear-gradient(180deg, rgba(255, 255, 255, 0.98) 0%, rgba(255, 255, 255, 0.95) 100%);
      backdrop-filter: blur(20px);
      border: 1px solid rgba(255, 215, 0, 0.2);
      border-radius: 16px;
      padding: 8px;
      box-shadow: 0 16px 48px rgba(0, 0, 0, 0.1);
    }

    .dropdown-item-glass {
      color: rgba(31, 41, 55, 0.9);
      padding: 14px 18px;
      border-radius: 12px;
      transition: all 0.25s ease;
      display: flex;
      align-items: center;
      gap: 14px;
      margin-bottom: 4px;
      text-decoration: none !important;
    }

    .dropdown-item-glass:hover {
      background: linear-gradient(90deg, rgba(255, 215, 0, 0.25) 0%, rgba(255, 215, 0, 0.1) 100%);
      color: #1f2937;
      transform: translateX(6px);
      box-shadow: 0 4px 16px rgba(255, 215, 0, 0.2);
    }

    .dropdown-icon {
      width: 32px;
      height: 32px;
      display: flex;
      align-items: center;
      justify-content: center;
      background: rgba(255, 215, 0, 0.15);
      border-radius: 10px;
      font-size: 16px;
      transition: all 0.25s ease;
    }

    .dropdown-item-glass:hover .dropdown-icon {
      background: rgba(255, 215, 0, 0.3);
      transform: scale(1.1);
    }

    .logout-link:hover {
      background: linear-gradient(90deg, rgba(239, 68, 68, 0.25) 0%, rgba(239, 68, 68, 0.1) 100%);
      box-shadow: 0 4px 16px rgba(239, 68, 68, 0.2);
    }

    .logout-link:hover .dropdown-icon {
      background: rgba(239, 68, 68, 0.3);
    }

    .dropdown-divider {
      border-color: rgba(255, 255, 255, 0.1);
      margin: 8px 0;
    }

    a { text-decoration: none !important; }
    a:hover { text-decoration: none !important; }

    @media (max-width: 768px) {
      .navbar-collapse {
        background: rgba(255, 255, 255, 0.98);
        padding: 15px;
        border-radius: 12px;
        margin-top: 10px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
      }
      .navbar-nav {
        gap: 8px;
      }
      .navbar-nav .btn {
        width: 100%;
        text-align: center;
        margin-top: 5px;
      }
      .btn-primary-glass.btn-sm, .btn-outline-light.btn-sm {
        padding: 10px 20px;
      }
    }

    .contact-card {
      padding: 20px;
      display: flex;
      align-items: flex-start;
      gap: 16px;
      margin-bottom: 16px;
      border-radius: 12px;
      background: rgba(255, 215, 0, 0.08);
      border: 1px solid rgba(255, 215, 0, 0.15);
      transition: all 0.3s;
    }

    .contact-card:hover {
      background: rgba(255, 215, 0, 0.15);
      transform: translateY(-2px);
    }

    .contact-icon {
      width: 50px;
      height: 50px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 22px;
      background: rgba(255, 215, 0, 0.2);
      flex-shrink: 0;
    }

    .btn-glass {
      background: rgba(255, 215, 0, 0.15);
      border: 1px solid rgba(255, 215, 0, 0.25);
      color: #1f2937;
      padding: 12px 28px;
      border-radius: 12px;
      font-weight: 600;
      transition: all 0.3s ease;
    }

    .btn-glass:hover {
      background: rgba(255, 215, 0, 0.25);
      color: #1f2937;
      transform: translateY(-2px);
    }

    .btn-outline-light.btn-sm {
      padding: 8px 16px;
      font-size: 0.875rem;
      border-radius: 10px;
      border-width: 1.5px;
      border-color: rgba(31, 41, 55, 0.3);
      color: #1f2937;
    }

    .btn-outline-light.btn-sm:hover {
      background: rgba(255, 215, 0, 0.2);
      border-color: #1f2937;
    }

    .bg-animation, .floating-icons {
      overflow: hidden;
      max-width: 100vw;
    }

    </style>

  @stack('styles')
</head>
<body>
  <div class="bg-animation">
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>
    <div class="orb orb-4"></div>
  </div>

  <div class="floating-icons">
    <i class="bi bi-house floating-icon house"></i>
    <i class="bi bi-house floating-icon house"></i>
    <i class="bi bi-paw floating-icon paw"></i>
    <i class="bi bi-paw floating-icon paw"></i>
    <i class="bi bi-file-earmark-text floating-icon document"></i>
    <i class="bi bi-file-earmark-text floating-icon document"></i>
    <i class="bi bi-bell floating-icon bell"></i>
    <i class="bi bi-bell floating-icon bell"></i>
    <i class="bi bi-award floating-icon badge"></i>
    <i class="bi bi-award floating-icon badge"></i>
    <i class="bi bi-people floating-icon users"></i>
    <i class="bi bi-people floating-icon users"></i>
    <i class="bi bi-calendar-event floating-icon calendar"></i>
    <i class="bi bi-calendar-event floating-icon calendar"></i>
    <i class="bi bi-child floating-icon child"></i>
    <i class="bi bi-child floating-icon child"></i>
  </div>

  @include('components.navigation-bar', ['currentRoute' => $currentRoute ?? ''])

  <style>
    .navbar-glass {
      background: rgba(255, 255, 255, 0.15);
      backdrop-filter: blur(20px);
      -webkit-backdrop-filter: blur(20px);
      border-bottom: 1px solid rgba(255, 215, 0, 0.15);
      z-index: 1000;
      transition: background 0.3s ease;
    }
    .navbar-glass.fixed-top {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
    }
    .navbar-glass .nav-link {
      color: rgba(255, 255, 255, 0.9) !important;
      font-weight: 500;
      transition: all 0.3s ease;
      position: relative;
      padding: 8px 14px;
      border-radius: 10px;
      display: flex;
      align-items: center;
    }
    .navbar-glass .nav-link:hover {
      background: rgba(255, 215, 0, 0.15);
      color: #ffffff !important;
    }
    .navbar-glass .nav-link.active {
      background: linear-gradient(90deg, rgba(255, 215, 0, 0.5) 0%, rgba(255, 215, 0, 0.25) 100%);
      color: #ffffff !important;
    }
    .navbar-glass .dropdown-menu {
      background: rgba(16, 85, 201, 0.98);
      backdrop-filter: blur(20px);
      border: 1px solid rgba(255, 215, 0, 0.3);
      border-radius: 12px;
      padding: 8px;
    }
    .navbar-glass .dropdown-item {
      color: #ffffff;
      padding: 10px 14px;
      border-radius: 8px;
      transition: all 0.25s ease;
      text-decoration: none;
    }
    .navbar-glass .dropdown-item:hover {
      background: rgba(255, 215, 0, 0.2);
      color: #ffffff;
    }
    .navbar-glass .dropdown-divider {
      border-color: rgba(255, 255, 255, 0.2);
    }
    .user-avatar {
      width: 36px;
      height: 36px;
      border-radius: 50%;
      background: linear-gradient(135deg, #1055C9 0%, #3b82f6 100%);
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 600;
      box-shadow: 0 4px 12px rgba(16, 85, 201, 0.4);
    }
    .dropdown-menu-glass {
      background: linear-gradient(180deg, rgba(13, 71, 161, 0.98) 0%, rgba(13, 71, 161, 0.95) 100%);
      backdrop-filter: blur(20px);
      border: 1px solid rgba(255, 255, 255, 0.15);
      border-radius: 16px;
      padding: 8px;
      box-shadow: 0 16px 48px rgba(0, 0, 0, 0.4);
    }
    .dropdown-item-glass {
      color: rgba(255, 255, 255, 0.9);
      padding: 12px 16px;
      border-radius: 10px;
      transition: all 0.25s ease;
      display: flex;
      align-items: center;
      gap: 12px;
      text-decoration: none !important;
    }
    .dropdown-item-glass:hover {
      background: linear-gradient(90deg, rgba(59, 130, 246, 0.25) 0%, rgba(59, 130, 246, 0.1) 100%);
      color: #fff;
      transform: translateX(4px);
    }
    .dropdown-icon {
      width: 28px;
      height: 28px;
      display: flex;
      align-items: center;
      justify-content: center;
      background: rgba(255, 255, 255, 0.1);
      border-radius: 8px;
      font-size: 14px;
    }
    .logout-link:hover {
      background: linear-gradient(90deg, rgba(239, 68, 68, 0.25) 0%, rgba(239, 68, 68, 0.1) 100%);
    }
    .logout-link:hover .dropdown-icon {
      background: rgba(239, 68, 68, 0.3);
    }
    .dropdown-divider {
      border-color: rgba(255, 255, 255, 0.1);
      margin: 4px 0;
    }
    .btn-primary-glass {
      background: linear-gradient(135deg, #1055C9 0%, #0d47a1 100%);
      border: none;
      color: #fff;
      padding: 8px 16px;
      border-radius: 10px;
      font-weight: 600;
      box-shadow: 0 4px 16px rgba(16, 85, 201, 0.4);
      transition: all 0.3s ease;
      text-decoration: none;
    }
    .btn-primary-glass:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 24px rgba(255, 215, 0, 0.5);
      color: #1f2937;
    }
  </style>

  <div class="main-content">
    <div class="page-wrapper">
      @yield('content')
    </div>
  </div>

  @include('components.public.footer')

  @stack('modals')

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    window.addEventListener('scroll', function() {
      const navbar = document.querySelector('.navbar-glass');
      const navLinks = document.querySelectorAll('.nav-link');
      if (navbar) {
        if (window.scrollY > 50) {
          navbar.style.background = 'linear-gradient(135deg, #1055C9 0%, #0d47a1 100%)';
          navLinks.forEach(link => link.style.color = '#ffffff !important');
        } else {
          navbar.style.background = 'rgba(255, 255, 255, 0.15)';
          navLinks.forEach(link => link.style.color = 'rgba(31, 41, 55, 0.85) !important');
        }
      }
    });
  </script>
  @stack('scripts')
</body>
</html>