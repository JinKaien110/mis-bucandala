<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="description" content="{{ $pageDescription ?? 'Barangay Bucandala 1 Resident Portal' }}">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ $pageTitle ?? 'Barangay Bucandala 1' }}</title>
  <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

   <style>
     :root {
       --mis-white: #FFFFFF;
       --mis-yellow: #FFD700;
       --mis-yellow-light: #FFE44D;
       --mis-yellow-dark: #FFC107;
       --mis-blue: #1055C9;
       --mis-blue-light: #3b82f6;
       --mis-blue-dark: #0d47a1;

        /* Adaptive Glass System - richer blue tones */
        --glass-bg: color-mix(in srgb, var(--mis-blue) 25%, transparent 75%);
        --glass-border: color-mix(in srgb, var(--mis-yellow) 40%, transparent 60%);
        --glass-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.3);
        --glass-blur: blur(16px);

        /* Adaptive text colors */
        --text-primary: #ffffff;
        --text-secondary: color-mix(in srgb, var(--mis-white) 92%, var(--mis-blue-dark) 8%);
        --text-muted: color-mix(in srgb, var(--mis-white) 75%, var(--mis-blue-dark) 25%);

        /* Dynamic form field colors */
        --input-bg: color-mix(in srgb, var(--mis-white) 18%, var(--mis-blue-dark) 82%);
        --input-border: color-mix(in srgb, var(--mis-yellow) 45%, transparent 55%);
        --input-focus-glow: color-mix(in srgb, var(--mis-yellow) 30%, transparent 70%);

        /* Card elevation layers - more vibrant */
        --card-elevation-1: color-mix(in srgb, var(--mis-blue) 20%, transparent 80%);
        --card-elevation-2: color-mix(in srgb, var(--mis-blue) 28%, transparent 72%);
        --card-elevation-3: color-mix(in srgb, var(--mis-blue) 15%, transparent 85%);

        /* Gradient overlays for depth - more transparent to show blue */
        --gradient-overlay-light: linear-gradient(135deg, rgba(255,255,255,0.03) 0%, rgba(255,255,255,0.005) 100%);
        --gradient-overlay-medium: linear-gradient(135deg, rgba(255,255,255,0.015) 0%, rgba(255,255,255,0.002) 100%);
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
       text-shadow: 0 2px 4px rgba(0,0,0,0.15);
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

    .floating-icon.paw { color: #FFC107; }
    .floating-icon.house { color: #FFD700; }
    .floating-icon.user { color: #1055C9; }
    .floating-icon.document { color: #1055C9; }
    .floating-icon.calendar { color: #FFD700; }

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
        transform: translateY(-4px);
        box-shadow: 0 16px 48px color-mix(in srgb, var(--mis-blue) 35%, transparent 65%);
        border-color: color-mix(in srgb, var(--mis-yellow) 45%, transparent 55%);
        background: var(--card-elevation-2);
      }

      .glass-light {
        background: var(--card-elevation-3);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid color-mix(in srgb, var(--mis-white) 15%, transparent 85%);
        border-radius: 16px;
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
        transform: translateY(-4px);
        box-shadow: 0 16px 48px color-mix(in srgb, var(--mis-blue) 35%, transparent 65%);
        border-color: color-mix(in srgb, var(--mis-yellow) 45%, transparent 55%);
        background: var(--card-elevation-2);
      }

      .glass-light {
        background: var(--card-elevation-3);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid color-mix(in srgb, var(--mis-white) 15%, transparent 85%);
        border-radius: 16px;
      }

     .glass:hover {
      transform: translateY(-3px);
      box-shadow: 0 12px 40px rgba(31, 38, 135, 0.2);
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
       overflow: hidden;
     }

      .glass-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 16px 48px color-mix(in srgb, var(--mis-blue) 35%, transparent 65%);
        border-color: color-mix(in srgb, var(--mis-yellow) 45%, transparent 55%);
      }

       .glass-light {
         background: var(--card-elevation-3);
         backdrop-filter: blur(12px);
         -webkit-backdrop-filter: blur(12px);
         border: 1px solid color-mix(in srgb, var(--mis-yellow) 20%, transparent 80%);
         border-radius: 16px;
       }

      /* Modal overrides for glass style */
      .modal-content.glass-modal {
        background: linear-gradient(135deg, rgba(255,255,255,0.15) 0%, rgba(255,255,255,0.05) 100%) !important;
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 215, 0, 0.2) !important;
        border-radius: 20px;
        box-shadow: var(--glass-shadow);
        color: #ffffff !important;
        max-width: 95vw !important;
        margin: 1.75rem auto;
      }

      .modal-content.glass-modal .modal-body {
        padding: 1.5rem;
      }

      @media (max-width: 768px) {
        .modal-content.glass-modal {
          margin: 0.5rem auto;
          border-radius: 16px;
          max-width: 98vw !important;
        }
        
        .modal-content.glass-modal .modal-body {
          padding: 1rem;
        }
        
        .modal-dialog {
          margin: 0;
          min-height: calc(100vh - 1rem);
          display: flex;
          align-items: flex-start;
          padding-top: 1rem;
        }
      }

     .modal-content.glass-modal .modal-header,
     .modal-content.glass-modal .modal-footer {
       background: transparent !important;
       border-color: rgba(255, 215, 0, 0.2) !important;
     }

     .modal-content.glass-modal .modal-title {
       color: #ffffff !important;
       font-weight: 600;
     }

     .modal-content.glass-modal .btn-close-white {
       filter: invert(1) brightness(100%);
       opacity: 0.8;
     }

     .modal-content.glass-modal .btn-close-white:hover {
       opacity: 1;
     }

     /* Force all text in modal body to be white/light */
     .modal-content.glass-modal .modal-body,
     .modal-content.glass-modal .modal-body * {
       color: rgba(255, 255, 255, 0.95) !important;
     }

     .modal-content.glass-modal .modal-body small,
     .modal-content.glass-modal .modal-body .text-muted,
     .modal-content.glass-modal .modal-body .opacity-50,
     .modal-content.glass-modal .modal-body .opacity-75 {
       color: rgba(255, 255, 255, 0.7) !important;
     }

     .modal-content.glass-modal .modal-body .text-primary {
       color: #60a5fa !important;
     }

     /* Form labels in modals */
     .form-label-glass {
       color: #ffffff !important;
       font-weight: 500;
       margin-bottom: 8px;
       font-size: 0.875rem;
     }

      /* Enhanced glass inputs for modals */
      .glass-input, .glass-select {
        background: var(--input-bg);
        border: 1px solid var(--input-border);
        color: var(--mis-blue-dark) !important;
        padding: 10px 14px;
        border-radius: 10px;
      }

      .glass-input:focus, .glass-select:focus {
        background: color-mix(in srgb, var(--mis-white) 30%, var(--mis-blue-dark) 70%);
        border-color: var(--mis-yellow) !important;
        box-shadow: 0 0 0 3px var(--input-focus-glow);
        color: var(--mis-blue-dark) !important;
      }

      .glass-input::placeholder {
        color: color-mix(in srgb, var(--mis-blue-dark) 60%, transparent 40%) !important;
      }

     .glass-select option {
       background: #1055C9;
       color: #ffffff;
     }

     /* Glass table inside modals */
     .glass-table {
       width: 100%;
       border-collapse: collapse;
       color: rgba(255, 255, 255, 0.95) !important;
     }

     .glass-table thead th {
       background: rgba(255, 215, 0, 0.2) !important;
       color: #ffffff !important;
       font-weight: 600;
       border-bottom: 2px solid rgba(255, 215, 0, 0.4);
       padding: 12px 16px;
       text-align: left;
     }

     .glass-table tbody td {
       background: rgba(255, 255, 255, 0.06);
       border-bottom: 1px solid rgba(255, 255, 255, 0.15);
       padding: 12px 16px;
       color: rgba(255, 255, 255, 0.95) !important;
     }

     .glass-table tbody tr:hover td {
       background: rgba(255, 255, 255, 0.12) !important;
     }

     /* Badge styles in modal */
     .badge-glass {
       font-weight: 600;
       padding: 6px 12px;
       border-radius: 50px;
       font-size: 0.75rem;
       text-transform: uppercase;
       letter-spacing: 0.5px;
     }

     .badge-glass-info {
       background: rgba(59, 130, 246, 0.3) !important;
       color: #93c5fd !important;
       border: 1px solid rgba(59, 130, 246, 0.5);
     }

     .badge-glass-success {
       background: rgba(34, 197, 94, 0.3) !important;
       color: #4ade80 !important;
       border: 1px solid rgba(34, 197, 94, 0.5);
     }

     .badge-glass-warning {
       background: rgba(255, 215, 0, 0.3) !important;
       color: #FFD700 !important;
       border: 1px solid rgba(255, 215, 0, 0.5);
     }

     .badge-glass-danger {
       background: rgba(239, 68, 68, 0.3) !important;
       color: #f87171 !important;
       border: 1px solid rgba(239, 68, 68, 0.5);
     }

     .badge-glass-secondary {
       background: rgba(156, 163, 175, 0.3) !important;
       color: #d1d5db !important;
       border: 1px solid rgba(156, 163, 175, 0.5);
     }

     .modal-content.glass-modal .modal-header,
     .modal-content.glass-modal .modal-footer {
       background: transparent;
       border-color: rgba(255, 215, 0, 0.2);
     }

     .modal-content.glass-modal .modal-title {
       color: #ffffff !important;
       font-weight: 600;
     }

     .modal-content.glass-modal .btn-close-white {
       filter: invert(1) brightness(100%);
       opacity: 0.8;
     }

     .modal-content.glass-modal .btn-close-white:hover {
       opacity: 1;
     }

     /* Force all text in modal body to be white/light */
     .modal-content.glass-modal .modal-body,
     .modal-content.glass-modal .modal-body * {
       color: rgba(255, 255, 255, 0.95) !important;
     }

     /* Form labels in modals */
     .form-label-glass {
       color: #ffffff !important;
       font-weight: 500;
       margin-bottom: 8px;
       font-size: 0.875rem;
     }

      /* Enhanced glass inputs for modals */
      .glass-input, .glass-select {
        background: var(--input-bg);
        border: 1px solid var(--input-border);
        color: var(--mis-blue-dark) !important;
        padding: 10px 14px;
        border-radius: 10px;
      }

      .glass-input:focus, .glass-select:focus {
        background: color-mix(in srgb, var(--mis-white) 30%, var(--mis-blue-dark) 70%);
        border-color: var(--mis-yellow) !important;
        box-shadow: 0 0 0 3px var(--input-focus-glow);
        color: var(--mis-blue-dark) !important;
      }

      .glass-input::placeholder {
        color: color-mix(in srgb, var(--mis-blue-dark) 60%, transparent 40%) !important;
      }

     .glass-input:focus, .glass-select:focus {
       background: rgba(255, 255, 255, 0.35);
       border-color: var(--mis-yellow);
       box-shadow: 0 0 0 3px rgba(255, 215, 0, 0.2);
       color: #1f2937 !important;
     }

     .glass-input::placeholder {
       color: rgba(31, 41, 55, 0.6) !important;
     }

     .glass-select option {
       background: #1055C9;
       color: #ffffff;
     }

     /* Glass table inside modals */
     .glass-table {
       width: 100%;
       border-collapse: collapse;
       color: rgba(255, 255, 255, 0.95) !important;
     }

     .glass-table thead th {
       background: rgba(255, 215, 0, 0.2) !important;
       color: #ffffff !important;
       font-weight: 600;
       border-bottom: 2px solid rgba(255, 215, 0, 0.4);
       padding: 12px 16px;
       text-align: left;
     }

     .glass-table tbody td {
       background: rgba(255, 255, 255, 0.06);
       border-bottom: 1px solid rgba(255, 255, 255, 0.15);
       padding: 12px 16px;
       color: rgba(255, 255, 255, 0.95) !important;
     }

     .glass-table tbody tr:hover td {
       background: rgba(255, 255, 255, 0.12) !important;
     }

     .modal-content.glass-modal .modal-header,
     .modal-content.glass-modal .modal-footer {
       background: transparent;
       border-color: rgba(255, 215, 0, 0.2);
     }

     .modal-content.glass-modal .modal-title {
       color: #ffffff;
       font-weight: 600;
     }

     .modal-content.glass-modal .btn-close-white {
       filter: invert(1) brightness(100%);
       opacity: 0.8;
     }

     .modal-content.glass-modal .btn-close-white:hover {
       opacity: 1;
     }

     /* Form labels in modals */
     .form-label-glass {
       color: #ffffff !important;
       font-weight: 500;
       margin-bottom: 8px;
       font-size: 0.875rem;
     }

     /* Enhanced glass inputs for modals */
     .glass-input, .glass-select {
       background: rgba(255, 255, 255, 0.25);
       border: 1px solid rgba(255, 215, 0, 0.4);
       color: #1f2937;
       padding: 10px 14px;
       border-radius: 10px;
     }

     .glass-input:focus, .glass-select:focus {
       background: rgba(255, 255, 255, 0.35);
       border-color: var(--mis-yellow);
       box-shadow: 0 0 0 3px rgba(255, 215, 0, 0.2);
       color: #1f2937;
     }

     .glass-input::placeholder {
       color: rgba(31, 41, 55, 0.6);
     }

     /* Glass table inside modals */
     .glass-table {
       width: 100%;
       border-collapse: collapse;
     }

     .glass-table thead th {
       background: rgba(255, 215, 0, 0.2);
       color: #ffffff;
       font-weight: 600;
       border-bottom: 2px solid rgba(255, 215, 0, 0.4);
       padding: 12px 16px;
       text-align: left;
     }

     .glass-table tbody td {
       background: rgba(255, 255, 255, 0.08);
       border-bottom: 1px solid rgba(255, 255, 255, 0.15);
       padding: 12px 16px;
       color: rgba(255, 255, 255, 0.95);
     }

     .glass-table tbody tr:hover td {
       background: rgba(255, 255, 255, 0.15);
     }

     /* Ensure all modal body text is white */
     .modal-content.glass-modal .modal-body {
       color: #ffffff;
     }

     .modal-content.glass-modal .modal-body p,
     .modal-content.glass-modal .modal-body small,
     .modal-content.glass-modal .modal-body span {
       color: rgba(255, 255, 255, 0.9);
     }

     /* Badge styles in modal */
     .badge-glass {
       font-weight: 600;
       padding: 6px 12px;
       border-radius: 50px;
       font-size: 0.75rem;
       text-transform: uppercase;
       letter-spacing: 0.5px;
     }

     .badge-glass-info {
       background: rgba(59, 130, 246, 0.3);
       color: #93c5fd;
       border: 1px solid rgba(59, 130, 246, 0.5);
     }

     .badge-glass-success {
       background: rgba(34, 197, 94, 0.3);
       color: #4ade80;
       border: 1px solid rgba(34, 197, 94, 0.5);
     }

     .badge-glass-warning {
       background: rgba(255, 215, 0, 0.3);
       color: #FFD700;
       border: 1px solid rgba(255, 215, 0, 0.5);
     }

     .badge-glass-danger {
       background: rgba(239, 68, 68, 0.3);
       color: #f87171;
       border: 1px solid rgba(239, 68, 68, 0.5);
     }

     .badge-glass-secondary {
       background: rgba(156, 163, 175, 0.3);
       color: #d1d5db;
       border: 1px solid rgba(156, 163, 175, 0.5);
     }

    .btn-primary-custom, .btn-glass-primary {
      background: linear-gradient(135deg, #FFD700 0%, #FFC107 100%);
      border: none;
      color: #1f2937;
      padding: 12px 24px;
      border-radius: 10px;
      font-weight: 600;
      box-shadow: 0 4px 15px rgba(255, 215, 0, 0.3);
      transition: all 0.3s ease;
    }

    .btn-primary-custom:hover, .btn-glass-primary:hover {
      background: linear-gradient(135deg, #FFC107 0%, #FFB300 100%);
      transform: translateY(-2px) scale(1.02);
      box-shadow: 0 8px 25px rgba(255, 215, 0, 0.5);
      color: #1f2937;
    }

    .btn-primary-custom:active, .btn-glass-primary:active {
      transform: scale(0.98);
    }

    .btn-glass {
      background: linear-gradient(135deg, rgba(255,255,255,0.15) 0%, rgba(255,255,255,0.05) 100%);
      border: 1px solid rgba(255, 255, 255, 0.25);
      color: #fff;
      padding: 12px 28px;
      border-radius: 12px;
      font-weight: 600;
      transition: all 0.3s ease;
    }

    .btn-glass:hover {
      background: linear-gradient(135deg, rgba(255,215,0,0.25) 0%, rgba(255,215,0,0.1) 100%);
      transform: translateY(-2px);
      box-shadow: 0 8px 24px rgba(255, 215, 0, 0.3);
      color: #1f2937;
    }

    .form-label {
      color: #ffffff;
      font-weight: 500;
      margin-bottom: 8px;
    }

    .form-control, .form-select, textarea {
      background: rgba(255, 255, 255, 0.2);
      border: 1px solid rgba(255, 215, 0, 0.3);
      color: #1f2937;
      padding: 14px 18px;
      border-radius: 12px;
    }

    .form-control:focus, .form-select:focus, textarea:focus {
      background: rgba(255, 255, 255, 0.3);
      border-color: var(--mis-yellow-dark);
      box-shadow: none;
      outline: none;
      color: #1f2937;
    }

    .form-control::placeholder, textarea::placeholder {
      color: rgba(31, 41, 55, 0.5);
    }

    .form-select option {
      background: #1055C9;
      color: #ffffff;
    }

     .table, .glass-table {
       color: #ffffff;
     }

     .table thead th, .glass-table thead th {
       background: rgba(255, 215, 0, 0.2) !important;
       color: #ffffff !important;
       font-weight: 600;
       border-bottom: 2px solid rgba(255, 215, 0, 0.4);
       padding: 12px 16px;
     }

     .table tbody td, .glass-table tbody td {
       background: rgba(255, 255, 255, 0.06);
       border-bottom: 1px solid rgba(255, 255, 255, 0.15);
       padding: 12px 16px;
       color: rgba(255, 255, 255, 0.95);
     }

     .table tbody tr:hover td, .glass-table tbody tr:hover td {
       background: rgba(255, 255, 255, 0.12) !important;
     }

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

    .badge {
      font-weight: 600;
    }

    .alert {
      border: 1px solid rgba(255, 215, 0, 0.2);
      color: #ffffff;
    }

    /* Navbar */
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
      box-shadow: 0 8px 24px rgba(16, 85, 201, 0.5);
      color: #fff;
    }

    /* Main content */
    .main-content {
      padding: 120px 0 60px;
      position: relative;
      z-index: 1;
      min-height: calc(100vh - 200px);
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

    /* Stat cards */
    .stat-card {
      padding: 24px;
      border-radius: 16px;
      background: rgba(255, 255, 255, 0.1);
      border: 1px solid rgba(255, 255, 255, 0.15);
      transition: all 0.3s;
    }

    .stat-card:hover {
      transform: translateY(-4px);
      background: rgba(255, 255, 255, 0.15);
    }

    .stat-icon {
      width: 56px;
      height: 56px;
      border-radius: 14px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 24px;
      margin-bottom: 16px;
    }

    .stat-value {
      font-size: 2rem;
      font-weight: 700;
    }

    .stat-label {
      font-size: 0.875rem;
      opacity: 0.75;
    }

    /* Quick links */
    .quick-link {
      display: flex;
      align-items: center;
      gap: 16px;
      padding: 16px;
      border-radius: 12px;
      background: rgba(255, 255, 255, 0.08);
      border: 1px solid rgba(255, 255, 255, 0.1);
      text-decoration: none;
      color: #fff;
      transition: all 0.3s;
    }

    .quick-link:hover {
      background: rgba(255, 255, 255, 0.15);
      transform: translateX(8px);
      color: #fff;
    }

    .quick-link-icon {
      width: 44px;
      height: 44px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 20px;
    }

    /* Footer */
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
      transition: color 0.3s ease;
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
      background: rgba(255,255,255,0.05) !important;
      border-color: rgba(255,255,255,0.15) !important;
      color: rgba(255,255,255,0.35) !important;
      cursor: not-allowed !important;
      pointer-events: none !important;
      box-shadow: none !important;
    }

    /* Modal backdrop */
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

    /* Force modals above everything */
    .modal-overlay {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      z-index: 9999;
    }

    /* Responsive */
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
    }
  </style>

  @stack('styles')
</head>
<body>
  <!-- Background Animation -->
  <div class="bg-animation">
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>
    <div class="orb orb-4"></div>
  </div>

  <!-- Floating Icons -->
  <div class="floating-icons">
    <i class="bi bi-house floating-icon house"></i>
    <i class="bi bi-house floating-icon house"></i>
    <i class="bi bi-paw floating-icon paw"></i>
    <i class="bi bi-paw floating-icon paw"></i>
    <i class="bi bi-file-earmark-text floating-icon document"></i>
    <i class="bi bi-file-earmark-text floating-icon document"></i>
    <i class="bi bi-person floating-icon user"></i>
    <i class="bi bi-person floating-icon user"></i>
  </div>

  <!-- Navigation -->
  @include('components.navigation-bar', ['currentRoute' => $currentRoute ?? ''])

  <!-- Main Content -->
  <div class="main-content">
    <div class="container-fluid px-4">
      @yield('content')
    </div>
  </div>

  <!-- Modals (rendered outside main content to fix z-index) -->
  @stack('modals')

  <!-- Footer -->
  @include('components.resident.footer')

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    window.addEventListener('scroll', function() {
      const navbar = document.querySelector('.navbar-glass');
      const navLinks = document.querySelectorAll('.nav-link');
      if (navbar) {
        if (window.scrollY > 50) {
          navbar.style.background = 'linear-gradient(135deg, #0d47a1 0%, #1055C9 100%)';
          navLinks.forEach(link => link.style.color = '#ffffff !important');
        } else {
          navbar.style.background = 'rgba(255, 255, 255, 0.15)';
          navLinks.forEach(link => link.style.color = 'rgba(255, 255, 255, 0.9) !important');
        }
      }
    });
  </script>
  @stack('scripts')
</body>
</html>
