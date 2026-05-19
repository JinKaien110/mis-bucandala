<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="description" content="Barangay Bucandala 1 - City of Imus, Cavite. Official website for resident services, document requests, announcements, and community information.">
  <title>Barangay Bucandala 1 - Official Website</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

  <style>
    :root {
      --mis-blue: #1055C9;
      --mis-blue-light: #3b82f6;
      --mis-blue-dark: #0d47a1;
      --mis-yellow: #FFD700;
      --mis-yellow-dark: #FFC107;
      --mis-green: #28a745;
      --mis-gray: #f8f9fa;
      --glass-bg: rgba(255, 255, 255, 0.15);
      --glass-border: rgba(255, 255, 255, 0.25);
      --glass-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.3);
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    html {
      overflow-x: hidden;
    }

    body {
      font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
      background: linear-gradient(135deg, #0d47a1 0%, #1055C9 50%, #1976D2 100%);
      min-height: 100vh;
      overflow-x: hidden;
      color: #ffffff;
      position: relative;
      z-index: 2;
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
    }

    a { color: var(--mis-yellow); }
    a:hover { color: #ffffff; }

    h1, h2, h3, h4, h5, h6 { color: #ffffff; }
    p, span, li, td, th { color: rgba(255,255,255,0.9); }
    .text-white { color: #ffffff !important; }
    .text-muted { color: rgba(255,255,255,0.7) !important; }

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

    .orb {
      position: absolute;
      border-radius: 50%;
      filter: blur(100px);
      animation: float 25s ease-in-out infinite;
    }

    .orb-1 {
      width: 600px;
      height: 600px;
      background: rgba(255, 215, 0, 0.08);
      top: -200px;
      left: -200px;
    }

    .orb-2 {
      width: 500px;
      height: 500px;
      background: rgba(255, 255, 255, 0.05);
      bottom: -100px;
      right: -100px;
      animation-delay: -8s;
    }

    .orb-3 {
      width: 400px;
      height: 400px;
      background: rgba(255, 193, 7, 0.06);
      top: 40%;
      left: 50%;
      animation-delay: -16s;
    }

    .orb-4 {
      width: 300px;
      height: 300px;
      background: rgba(255, 228, 77, 0.08);
      top: 10%;
      right: 20%;
      animation-delay: -12s;
    }

    @keyframes float {
      0%, 100% { transform: translate(0, 0) scale(1) rotate(0deg); }
      25% { transform: translate(30px, -30px) scale(1.05) rotate(5deg); }
      50% { transform: translate(-20px, 20px) scale(0.95) rotate(-5deg); }
      75% { transform: translate(20px, 30px) scale(1.02) rotate(3deg); }
    }

    /* Floating Icons Animation */
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

    .floating-icon:nth-child(1) { top: 10%; left: 5%; animation-delay: 0s; --tx: 100px; --ty: 50px; --rot: 15deg; }
    .floating-icon:nth-child(2) { top: 20%; left: 85%; animation-delay: -3s; --tx: -80px; --ty: 80px; --rot: -10deg; }
    .floating-icon:nth-child(3) { top: 50%; left: 3%; animation-delay: -5s; --tx: 120px; --ty: -60px; --rot: 20deg; }
    .floating-icon:nth-child(4) { top: 70%; left: 90%; animation-delay: -8s; --tx: -100px; --ty: -40px; --rot: -15deg; }
    .floating-icon:nth-child(5) { top: 35%; left: 50%; animation-delay: -12s; --tx: 60px; --ty: 100px; --rot: 10deg; }
    .floating-icon:nth-child(6) { top: 80%; left: 20%; animation-delay: -2s; --tx: 80px; --ty: -80px; --rot: -20deg; }
    .floating-icon:nth-child(7) { top: 15%; left: 35%; animation-delay: -6s; --tx: -60px; --ty: 40px; --rot: 25deg; }
    .floating-icon:nth-child(8) { top: 60%; left: 75%; animation-delay: -10s; --tx: -90px; --ty: 60px; --rot: -5deg; }
    .floating-icon:nth-child(9) { top: 45%; left: 15%; animation-delay: -15s; --tx: 50px; --ty: -90px; --rot: 12deg; }
    .floating-icon:nth-child(10) { top: 25%; left: 65%; animation-delay: -18s; --tx: -70px; --ty: -70px; --rot: -18deg; }

    /* Glassmorphism Base */
    .glass {
      background: var(--glass-bg);
      backdrop-filter: blur(16px);
      -webkit-backdrop-filter: blur(16px);
      border: 1px solid var(--glass-border);
      border-radius: 20px;
      box-shadow: var(--glass-shadow);
    }

    .glass-light {
      background: rgba(255, 255, 255, 0.15);
      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);
      border: 1px solid rgba(255, 255, 255, 0.25);
      border-radius: 16px;
    }

    /* Navigation */
    .navbar-glass {
      background: rgba(255, 255, 255, 0.15);
      backdrop-filter: blur(20px);
      -webkit-backdrop-filter: blur(20px);
      border-bottom: 1px solid rgba(255, 215, 0, 0.15);
      z-index: 1000;
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

    .navbar-glass .user-avatar {
      width: 36px;
      height: 36px;
      border-radius: 50%;
      background: linear-gradient(135deg, #FFD700 0%, #FFC107 100%);
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 600;
    }

    .navbar-glass .dropdown-menu-glass {
      background: linear-gradient(180deg, rgba(255, 255, 255, 0.98) 0%, rgba(255, 255, 255, 0.95) 100%);
      backdrop-filter: blur(20px);
      border: 1px solid rgba(255, 215, 0, 0.2);
      border-radius: 16px;
      padding: 8px;
      box-shadow: 0 16px 48px rgba(0, 0, 0, 0.1);
    }

    .navbar-glass .dropdown-item-glass {
      color: rgba(255, 255, 255, 0.9);
      padding: 12px 16px;
      border-radius: 10px;
      transition: all 0.25s ease;
      display: flex;
      align-items: center;
      gap: 12px;
      text-decoration: none !important;
    }

    .navbar-glass .dropdown-item-glass:hover {
      background: linear-gradient(90deg, rgba(255, 215, 0, 0.25) 0%, rgba(255, 215, 0, 0.1) 100%);
      color: #ffffff;
      transform: translateX(4px);
    }

    .navbar-glass .dropdown-icon {
      width: 28px;
      height: 28px;
      display: flex;
      align-items: center;
      justify-content: center;
      background: rgba(255, 215, 0, 0.15);
      border-radius: 8px;
      font-size: 14px;
    }

    .navbar-glass .btn-primary-glass {
      background: linear-gradient(135deg, #FFD700 0%, #FFC107 100%);
      border: none;
      color: #ffffff;
      padding: 8px 16px;
      border-radius: 10px;
      font-weight: 600;
      text-decoration: none;
    }

    .navbar-glass .btn-primary-glass:hover {
      transform: translateY(-2px);
      color: #ffffff;
    }

    .navbar-glass .dropdown-divider {
      border-color: rgba(255, 215, 0, 0.2);
      margin: 4px 0;
    }

    .navbar-glass.fixed-top {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
    }

    .nav-link {
      color: rgba(255, 255, 255, 0.9) !important;
      font-weight: 500;
      transition: all 0.3s ease;
      position: relative;
      padding: 8px 14px;
      border-radius: 10px;
    }

    .nav-link:hover {
      background: rgba(255, 215, 0, 0.15);
      color: #ffffff !important;
    }

    .dropdown-menu {
      background: rgba(255, 255, 255, 0.98);
      border: 1px solid rgba(255, 215, 0, 0.2);
      border-radius: 12px;
      padding: 8px;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    }

    .dropdown-item {
      color: rgba(255, 255, 255, 0.9);
      padding: 10px 16px;
      border-radius: 8px;
      transition: all 0.2s;
    }

    .dropdown-item:hover {
      background: rgba(255, 215, 0, 0.15);
      color: #ffffff;
    }

    /* Hero Section */
    .hero-section {
      position: relative;
      padding: 120px 0 80px;
      text-align: center;
    }

    .hero-logo {
      width: 120px;
      height: 120px;
      border-radius: 24px;
      object-fit: cover;
      border: 4px solid rgba(255, 215, 0, 0.5);
      box-shadow: 0 12px 40px rgba(255, 215, 0, 0.3);
      margin-bottom: 24px;
      animation: pulse-glow 3s ease-in-out infinite;
    }

    @keyframes pulse-glow {
      0%, 100% { box-shadow: 0 12px 40px rgba(255, 215, 0, 0.3), 0 0 0 0 rgba(255, 215, 0, 0.4); }
      50% { box-shadow: 0 12px 40px rgba(255, 215, 0, 0.3), 0 0 0 15px rgba(255, 215, 0, 0); }
    }

    .hero-title {
      font-size: 3rem;
      font-weight: 800;
      margin-bottom: 8px;
      text-shadow: 0 2px 10px rgba(0,0,0,0.2);
      color: #ffffff;
    }

    .hero-subtitle {
      font-size: 1.3rem;
      color: rgba(255, 255, 255, 0.9);
      margin-bottom: 8px;
    }
    
    .hero-description {
      max-width: 600px;
      margin: 16px auto 32px;
      font-size: 1rem;
      color: rgba(255, 255, 255, 0.8);
      line-height: 1.6;
    }

    .hero-location {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 8px 20px;
      background: rgba(255, 215, 0, 0.2);
      border: 1px solid rgba(255, 215, 0, 0.3);
      border-radius: 50px;
      font-size: 0.95rem;
      color: #ffffff;
    }

    /* Quick Actions */
    .quick-actions {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
      gap: 16px;
      margin-top: 40px;
    }

    .action-card {
      padding: 24px 16px;
      text-align: center;
      transition: all 0.3s ease;
      cursor: pointer;
      text-decoration: none;
      background: rgba(255, 255, 255, 0.12);
      border: 1px solid rgba(255, 215, 0, 0.25);
      border-radius: 16px;
      color: #ffffff;
    }

    .action-card:hover {
      transform: translateY(-8px);
      background: rgba(255, 215, 0, 0.2);
      border-color: rgba(255, 215, 0, 0.5);
      box-shadow: 0 20px 40px rgba(255, 215, 0, 0.2);
    }

    .action-icon {
      width: 64px;
      height: 64px;
      border-radius: 16px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 16px;
      font-size: 28px;
      background: rgba(255, 215, 0, 0.2);
      border: 1px solid rgba(255, 215, 0, 0.3);
    }

    .action-title {
      font-weight: 600;
      font-size: 1rem;
      margin-bottom: 4px;
      color: #ffffff;
    }

    .action-desc {
      font-size: 0.8rem;
      color: rgba(255, 255, 255, 0.7);
    }

    /* Section Styles */
    .section {
      padding: 60px 0;
      position: relative;
      z-index: 1;
    }

    .section-title {
      font-size: 1.8rem;
      font-weight: 700;
      margin-bottom: 8px;
      display: flex;
      align-items: center;
      gap: 12px;
      color: #ffffff;
    }

    .section-subtitle {
      color: rgba(255, 255, 255, 0.8);
      margin-bottom: 32px;
    }

    /* Announcements */
    .announcement-card {
      padding: 20px;
      margin-bottom: 16px;
      transition: all 0.3s ease;
      background: rgba(255, 255, 255, 0.15);
      border: 1px solid rgba(255, 215, 0, 0.25);
      border-radius: 12px;
      color: #ffffff;
    }

    .announcement-card:hover {
      background: rgba(255, 215, 0, 0.2);
      transform: translateX(8px);
      border-color: rgba(255, 215, 0, 0.5);
    }

    .announcement-badge {
      display: inline-block;
      padding: 4px 12px;
      border-radius: 50px;
      font-size: 0.7rem;
      font-weight: 600;
      text-transform: uppercase;
      margin-bottom: 8px;
    }

    .badge-important {
      background: rgba(220, 38, 38, 0.25);
      color: #fca5a5;
    }

    .badge-event {
      background: rgba(255, 215, 0, 0.25);
      color: #fef08a;
    }

    .badge-general {
      background: rgba(34, 197, 94, 0.25);
      color: #86efac;
    }

    .badge-news {
      background: rgba(59, 130, 246, 0.25);
      color: #93c5fd;
    }

    /* Event Card */
    .event-card {
      padding: 16px;
      margin-bottom: 12px;
      display: flex;
      gap: 12px;
      align-items: flex-start;
      background: rgba(255, 255, 255, 0.15);
      border: 1px solid rgba(255, 215, 0, 0.25);
      border-radius: 12px;
      color: #ffffff;
    }

    .event-card:hover {
      background: rgba(255, 215, 0, 0.2);
      border-color: rgba(255, 215, 0, 0.5);
    }

    .event-date-box {
      min-width: 50px;
      text-align: center;
      padding: 8px;
      border-radius: 10px;
      background: rgba(255, 215, 0, 0.2);
    }

    .event-month {
      font-size: 0.7rem;
      text-transform: uppercase;
      color: #FFD700;
      font-weight: 600;
    }

    .event-day {
      font-size: 1.2rem;
      font-weight: 700;
      color: #ffffff;
    }

    .event-day {
      font-size: 1.3rem;
      font-weight: 700;
      color: #ffffff;
    }

    .badge-important {
      background: rgba(239, 68, 68, 0.2);
      color: #dc2626;
    }

    .badge-event {
      background: rgba(255, 215, 0, 0.2);
      color: #b45309;
    }

    .badge-general {
      background: rgba(40, 167, 69, 0.2);
      color: #059669;
    }

    /* Services Grid */
    .services-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 20px;
    }

    .service-card {
      padding: 28px;
      transition: all 0.3s ease;
      background: rgba(255, 255, 255, 0.15);
      border: 1px solid rgba(255, 215, 0, 0.15);
      border-radius: 12px;
      color: #ffffff;
    }

    .service-card:hover {
      transform: translateY(-5px);
      background: rgba(255, 215, 0, 0.15);
    }

    .service-icon {
      width: 56px;
      height: 56px;
      border-radius: 14px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 24px;
      margin-bottom: 16px;
      background: rgba(255, 215, 0, 0.2);
    }

    .service-title {
      font-weight: 600;
      font-size: 1.1rem;
      margin-bottom: 8px;
    }

    .service-desc {
      font-size: 0.85rem;
      color: rgba(255, 255, 255, 0.75);
      line-height: 1.5;
    }

    /* Events */
    .event-card {
      padding: 20px;
      margin-bottom: 16px;
      display: flex;
      gap: 16px;
      align-items: flex-start;
      transition: all 0.3s ease;
    }

    .event-card:hover {
      background: rgba(255, 255, 255, 0.15);
    }

    .event-date-box {
      min-width: 60px;
      text-align: center;
      padding: 12px 8px;
      border-radius: 12px;
      background: rgba(255, 255, 255, 0.1);
      border: 1px solid rgba(255, 255, 255, 0.15);
    }

    .event-month {
      font-size: 0.7rem;
      text-transform: uppercase;
      color: var(--mis-yellow);
    }

    .event-day {
      font-size: 1.5rem;
      font-weight: 700;
    }

    .event-details {
      flex: 1;
    }

    .event-title {
      font-weight: 600;
      margin-bottom: 6px;
      color: #ffffff;
    }

    .event-meta {
      font-size: 0.8rem;
      color: rgba(255, 255, 255, 0.7);
      display: flex;
      gap: 16px;
      flex-wrap: wrap;
    }

    .event-meta i {
      margin-right: 4px;
    }

    /* Statistics */
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
      gap: 20px;
    }

    .stat-card {
      padding: 24px;
      text-align: center;
      background: rgba(255, 255, 255, 0.15);
      border: 1px solid rgba(255, 215, 0, 0.15);
      border-radius: 12px;
    }

    .stat-number {
      font-size: 2.5rem;
      font-weight: 800;
      color: #FFC107;
    }

    .stat-label {
      font-size: 0.85rem;
      color: rgba(255, 255, 255, 0.75);
      margin-top: 4px;
    }

    /* Contact Section */
    .contact-card {
      padding: 24px;
      display: flex;
      align-items: flex-start;
      gap: 16px;
      background: rgba(255, 255, 255, 0.15);
      border: 1px solid rgba(255, 215, 0, 0.15);
      border-radius: 12px;
      color: #ffffff;
    }

    .contact-icon {
      width: 48px;
      height: 48px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 20px;
      background: rgba(255, 215, 0, 0.2);
      border: 1px solid rgba(255, 215, 0, 0.3);
      flex-shrink: 0;
    }

    .contact-info h5 {
      font-weight: 600;
      margin-bottom: 4px;
    }

    .contact-info p {
      font-size: 0.9rem;
      color: rgba(255, 255, 255, 0.75);
      margin: 0;
    }

    /* Footer */
    .footer-glass {
      background: rgba(255, 215, 0, 0.1);
      backdrop-filter: blur(20px);
      -webkit-backdrop-filter: blur(20px);
      border-top: 1px solid rgba(255, 215, 0, 0.2);
      padding: 40px 0 20px;
      color: #ffffff;
    }

    .footer-links h5 {
      font-weight: 600;
      margin-bottom: 16px;
      color: #ffffff;
    }

    .footer-links ul {
      list-style: none;
      padding: 0;
      margin: 0;
    }

    .footer-links li {
      margin-bottom: 8px;
    }

    .footer-links a {
      color: rgba(255, 255, 255, 0.7);
      text-decoration: none;
      transition: color 0.3s;
      font-size: 0.9rem;
    }

    .footer-links a:hover {
      color: #FFD700;
    }

    .footer-bottom {
      border-top: 1px solid rgba(255, 215, 0, 0.2);
      margin-top: 30px;
      padding-top: 20px;
      text-align: center;
      font-size: 0.85rem;
      color: rgba(255, 255, 255, 0.6);
    }

    /* Buttons */
    .btn-glass {
      background: rgba(255, 215, 0, 0.2);
      border: 1px solid rgba(255, 215, 0, 0.35);
      color: #ffffff;
      padding: 12px 28px;
      border-radius: 12px;
      font-weight: 600;
      transition: all 0.3s ease;
    }

    .btn-glass:hover {
      background: rgba(255, 215, 0, 0.35);
      color: #ffffff;
      transform: translateY(-2px);
    }

    .btn-primary-glass {
      background: linear-gradient(135deg, #FFD700 0%, #FFC107 100%);
      border: none;
      color: #ffffff;
      padding: 14px 32px;
      border-radius: 14px;
      font-weight: 600;
      box-shadow: 0 8px 25px rgba(255, 215, 0, 0.4);
      transition: all 0.3s ease;
    }

    .btn-primary-glass:hover {
      transform: translateY(-3px);
      box-shadow: 0 12px 35px rgba(16, 85, 201, 0.5);
      color: #fff;
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

    /* Swiper */
    .swiper {
      padding-bottom: 40px !important;
    }

    .swiper-pagination-bullet {
      background: rgba(255, 255, 255, 0.5) !important;
    }

    .swiper-pagination-bullet-active {
      background: #fff !important;
    }

    /* Responsive */
    @media (max-width: 768px) {
      .hero-title {
        font-size: 2rem;
      }
      
      .hero-subtitle {
        font-size: 1rem;
      }

      .section {
        padding: 40px 0;
      }

      .section-title {
        font-size: 1.4rem;
      }

      .quick-actions {
        grid-template-columns: repeat(2, 1fr);
      }

      .action-card {
        padding: 16px 12px;
      }

      .action-icon {
        width: 48px;
        height: 48px;
        font-size: 20px;
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

    /* Stagger Animation */
    .fade-up {
      opacity: 0;
      transform: translateY(30px);
      animation: fadeUp 0.6s ease forwards;
    }

    @keyframes fadeUp {
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .delay-1 { animation-delay: 0.1s; }
    .delay-2 { animation-delay: 0.2s; }
    .delay-3 { animation-delay: 0.3s; }
    .delay-4 { animation-delay: 0.4s; }
    .delay-5 { animation-delay: 0.5s; }

    /* FAQ Toggle */
    .faq-item {
      background: rgba(255, 255, 255, 0.08);
      border: 1px solid rgba(255, 215, 0, 0.15);
      border-radius: 12px;
      overflow: hidden;
    }

    .faq-question {
      cursor: pointer;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .faq-question i {
      transition: transform 0.3s;
    }

    .faq-item.open .faq-question i {
      transform: rotate(180deg);
    }

    .faq-item.open .faq-answer {
      display: block !important;
      max-height: 500px !important;
      padding: 0 20px 20px !important;
    }

    html {
      scroll-behavior: smooth;
    }

    /* Form Elements */
    .form-label {
      color: #ffffff;
      font-weight: 500;
      margin-bottom: 8px;
    }

    .form-control, .form-select {
      background: rgba(255, 255, 255, 0.12);
      border: 1px solid rgba(255, 255, 255, 0.25);
      color: #ffffff;
      border-radius: 12px;
      padding: 12px 16px;
      transition: all 0.3s ease;
    }

    .form-control:focus, .form-select:focus {
      background: rgba(255, 255, 255, 0.18);
      border-color: #FFD700;
      box-shadow: 0 0 0 3px rgba(255, 215, 0, 0.15);
      color: #ffffff;
      outline: none;
    }

    .form-control::placeholder {
      color: rgba(255, 255, 255, 0.5);
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
      background: #1055C9;
      color: #ffffff;
    }

    /* Buttons */
    .btn {
      border-radius: 12px;
      font-weight: 600;
      padding: 12px 24px;
      transition: all 0.3s ease;
    }

    .btn-warning {
      background: #FFD700;
      border: none;
      color: #1f2937;
    }

    .btn-warning:hover {
      background: #FFC107;
      color: #1f2937;
      transform: translateY(-2px);
    }

    .btn-outline-primary {
      background: transparent;
      border: 2px solid #FFD700;
      color: #FFD700;
    }

    .btn-outline-primary:hover {
      background: #FFD700;
      color: #1f2937;
    }

    .btn-primary {
      background: #FFD700;
      border: none;
      color: #1f2937;
    }

    .btn-primary:hover {
      background: #FFC107;
      color: #1f2937;
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

    /* Badges */
    .badge {
      padding: 6px 12px;
      border-radius: 50px;
      font-weight: 500;
      font-size: 0.8rem;
    }

    .badge.bg-primary {
      background: #FFD700 !important;
      color: #1f2937;
    }

    .badge.bg-secondary {
      background: rgba(255, 255, 255, 0.2);
      color: #ffffff;
    }

    /* Pagination */
    .pagination {
      gap: 8px;
    }

    .page-link {
      background: rgba(255, 255, 255, 0.1);
      border: 1px solid rgba(255, 255, 255, 0.2);
      color: #ffffff;
      border-radius: 8px;
      padding: 10px 16px;
      transition: all 0.3s ease;
    }

    .page-link:hover {
      background: rgba(255, 215, 0, 0.2);
      border-color: #FFD700;
      color: #ffffff;
    }

    .page-item.active .page-link {
      background: #FFD700;
      border-color: #FFD700;
      color: #1f2937;
    }

    .page-item.disabled .page-link {
      background: rgba(255, 255, 255, 0.05);
      color: rgba(255, 255, 255, 0.4);
    }

    /* Modal */
    .modal-content {
      background: rgba(16, 85, 201, 0.95);
      border: 1px solid rgba(255, 215, 0, 0.3);
      border-radius: 20px;
      color: #ffffff;
    }

    .modal-header {
      border-bottom: 1px solid rgba(255, 215, 0, 0.2);
      color: #ffffff;
    }

    .modal-footer {
      border-top: 1px solid rgba(255, 215, 0, 0.2);
    }

    .btn-close {
      filter: invert(1);
    }

    /* Alerts */
    .alert {
      border-radius: 12px;
      padding: 16px 20px;
      border: none;
    }

    .alert-success {
      background: rgba(34, 197, 94, 0.2);
      color: #86efac;
      border: 1px solid rgba(34, 197, 94, 0.3);
    }

    .alert-danger {
      background: rgba(220, 38, 38, 0.2);
      color: #fca5a5;
      border: 1px solid rgba(220, 38, 38, 0.3);
    }

    .alert-warning {
      background: rgba(255, 215, 0, 0.2);
      color: #fef08a;
      border: 1px solid rgba(255, 215, 0, 0.3);
    }

    .alert-info {
      background: rgba(59, 130, 246, 0.2);
      color: #93c5fd;
      border: 1px solid rgba(59, 130, 246, 0.3);
    }

    /* Dropdown */
    .dropdown-menu {
      background: rgba(16, 85, 201, 0.98);
      border: 1px solid rgba(255, 215, 0, 0.3);
      border-radius: 12px;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
    }

    .dropdown-item {
      color: #ffffff;
      padding: 10px 16px;
      transition: all 0.2s;
    }

    .dropdown-item:hover {
      background: rgba(255, 215, 0, 0.2);
      color: #FFD700;
    }

    /* Official Cards */
    .official-card {
      padding: 24px;
      text-align: center;
      background: rgba(255, 255, 255, 0.1);
      border: 1px solid rgba(255, 215, 0, 0.2);
      border-radius: 16px;
      transition: all 0.3s ease;
    }

    .official-card:hover {
      transform: translateY(-8px);
      background: rgba(255, 255, 255, 0.15);
      border-color: rgba(255, 215, 0, 0.4);
    }

    .official-card .captain-badge {
      display: inline-block;
      background: linear-gradient(135deg, #FFD700 0%, #FFC107 100%);
      color: #1f2937;
      padding: 6px 16px;
      border-radius: 50px;
      font-size: 0.75rem;
      font-weight: 600;
      margin-bottom: 16px;
    }

    .official-card .official-avatar {
      width: 120px;
      height: 120px;
      border-radius: 50%;
      object-fit: cover;
      border: 4px solid rgba(255, 215, 0, 0.3);
      margin-bottom: 16px;
    }

    .official-card .official-name {
      font-size: 1.1rem;
      font-weight: 600;
      color: #ffffff;
      margin-bottom: 4px;
    }

    .official-card .official-position {
      font-size: 0.9rem;
      color: rgba(255, 255, 255, 0.7);
      margin-bottom: 8px;
    }

    .official-card .official-contact {
      font-size: 0.85rem;
      color: rgba(255, 255, 255, 0.6);
    }

    /* Search Box */
    .search-box {
      background: rgba(255, 255, 255, 0.12);
      border: 1px solid rgba(255, 255, 255, 0.25);
      color: #ffffff;
      padding: 12px 20px;
      border-radius: 12px;
      min-width: 250px;
    }

    .search-box:focus {
      outline: none;
      border-color: #FFD700;
      background: rgba(255, 255, 255, 0.18);
    }

    .search-box::placeholder {
      color: rgba(255, 255, 255, 0.5);
    }

    /* Filter Button */
    .filter-btn {
      background: rgba(255, 255, 255, 0.12);
      border: 1px solid rgba(255, 215, 0, 0.25);
      color: #ffffff;
      padding: 10px 20px;
      border-radius: 25px;
      font-weight: 500;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .filter-btn:hover, .filter-btn.active {
      background: #FFD700;
      color: #1f2937;
      border-color: #FFD700;
    }

    /* FAQ Category Buttons */
    .category-btn {
      background: rgba(255, 255, 255, 0.12);
      border: 1px solid rgba(255, 215, 0, 0.25);
      color: #ffffff;
      padding: 10px 20px;
      border-radius: 25px;
      font-weight: 500;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .category-btn:hover, .category-btn.active {
      background: #FFD700;
      color: #1f2937;
      border-color: #FFD700;
    }

    /* News Section Styles */
    .filter-tabs {
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
    }
    .filter-tab {
      padding: 12px 20px;
      border-radius: 12px;
      background: linear-gradient(135deg, rgba(255,255,255,0.2) 0%, rgba(255,255,255,0.1) 100%);
      border: 2px solid rgba(255, 215, 0, 0.4);
      color: #ffffff;
      text-decoration: none;
      font-weight: 500;
      transition: all 0.3s;
    }
    .filter-tab:hover {
      background: linear-gradient(135deg, rgba(255,215,0,0.3) 0%, rgba(255,215,0,0.15) 100%);
      transform: translateY(-2px);
    }

    .view-toggle {
      display: flex;
      gap: 10px;
    }
    .view-btn {
      padding: 12px 20px;
      border-radius: 12px;
      background: linear-gradient(135deg, rgba(255,255,255,0.2) 0%, rgba(255,255,255,0.1) 100%);
      border: 2px solid rgba(255, 215, 0, 0.4);
      color: #ffffff;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s;
    }
    .view-btn:hover {
      background: linear-gradient(135deg, rgba(255,215,0,0.3) 0%, rgba(255,215,0,0.15) 100%);
      transform: translateY(-2px);
    }
    .view-btn.active {
      background: linear-gradient(135deg, #FFD700 0%, #FFC107 100%);
      border-color: #FFD700;
    }

    .announcement-card {
      padding: 20px;
      margin-bottom: 16px;
      transition: all 0.3s;
      background: rgba(255, 255, 255, 0.15);
      border: 1px solid rgba(255, 215, 0, 0.15);
      border-radius: 12px;
    }
    .announcement-card:hover {
      transform: translateY(-3px);
      background: rgba(255, 255, 255, 0.25);
    }
    .announcement-badge {
      display: inline-block;
      padding: 3px 10px;
      border-radius: 12px;
      font-size: 0.7rem;
      font-weight: 600;
      text-transform: uppercase;
      margin-bottom: 8px;
    }
    .badge-important {
      background: rgba(239,68,68,0.2);
      color: #fca5a5;
    }
    .badge-event {
      background: rgba(59,130,246,0.2);
      color: #93c5fd;
    }
    .badge-news {
      background: rgba(168,85,247,0.2);
      color: #d8b4fe;
    }
    .badge-general {
      background: rgba(40,167,69,0.2);
      color: #86efac;
    }
    .announcement-date {
      font-size: 0.75rem;
      color: rgba(255, 255, 255, 0.75);
      margin-bottom: 8px;
    }
    .announcement-title {
      font-weight: 600;
      font-size: 1.1rem;
      margin-bottom: 8px;
    }
    .announcement-excerpt {
      font-size: 0.9rem;
      color: rgba(255, 255, 255, 0.75);
      line-height: 1.5;
    }

    .event-card {
      padding: 16px;
      margin-bottom: 12px;
      display: flex;
      gap: 14px;
      align-items: flex-start;
      transition: all 0.3s;
      background: rgba(255, 255, 255, 0.15);
      border: 1px solid rgba(255, 215, 0, 0.15);
      border-radius: 12px;
    }
    .event-card:hover {
      transform: translateY(-3px);
      background: rgba(255, 255, 255, 0.25);
    }
    .event-date-box {
      min-width: 50px;
      text-align: center;
      padding: 8px;
      border-radius: 10px;
      background: rgba(255,255,255,0.1);
    }
    .event-month {
      font-size: 0.65rem;
      text-transform: uppercase;
      color: #FFD700;
    }
    .event-day {
      font-size: 1.3rem;
      font-weight: 700;
    }
    .event-details {
      flex: 1;
    }
    .event-title {
      font-weight: 600;
      margin-bottom: 6px;
    }
    .event-meta {
      font-size: 0.8rem;
      color: rgba(255, 255, 255, 0.75);
      display: flex;
      gap: 16px;
      flex-wrap: wrap;
    }
    .event-status {
      display: inline-block;
      padding: 3px 8px;
      border-radius: 10px;
      font-size: 0.65rem;
      font-weight: 600;
    }
    .status-upcoming {
      background: rgba(59,130,246,0.2);
      color: #93c5fd;
    }
    .status-ongoing {
      background: rgba(34,197,94,0.2);
      color: #4ade80;
    }
    .status-past {
      background: rgba(255,255,255,0.1);
      color: rgba(255,255,255,0.5);
    }

    .page-header {
      text-align: center;
      margin-bottom: 30px;
    }
    .page-title {
      font-size: 2rem;
      font-weight: 700;
      margin-bottom: 8px;
      color: #ffffff;
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
    .pagination .page-link {
      background: linear-gradient(135deg, rgba(255,255,255,0.3) 0%, rgba(255,255,255,0.15) 100%);
      border: 2px solid rgba(255, 215, 0, 0.5);
      color: #ffffff;
      padding: 14px 20px;
      border-radius: 14px;
      font-weight: 600;
      font-size: 15px;
      transition: all 0.3s;
      min-width: 55px;
      text-align: center;
    }
    .pagination .page-link:hover {
      background: linear-gradient(135deg, #FFD700 0%, #FFC107 100%);
      border-color: #FFD700;
      transform: translateY(-4px);
      color: #ffffff;
      text-decoration: none;
    }
    .pagination .page-item.active .page-link {
      background: linear-gradient(135deg, #FFD700 0%, #FFC107 100%);
      border-color: #FFD700;
      color: #ffffff;
      font-weight: 700;
    }

    /* Officials Styles */
    .official-card {
      padding: 32px;
      text-align: center;
      transition: all 0.3s;
      background: rgba(255, 255, 255, 0.15);
      border: 1px solid rgba(255, 215, 0, 0.15);
      border-radius: 16px;
    }
    .official-card:hover {
      transform: translateY(-8px);
      background: rgba(255, 255, 255, 0.25);
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
      color: #ffffff;
      padding: 8px 20px;
      border-radius: 50px;
      font-size: 0.85rem;
      font-weight: 700;
      display: inline-block;
      margin-bottom: 16px;
    }
    .search-box {
      background: rgba(255, 255, 255, 0.15);
      border: 1px solid rgba(255, 215, 0, 0.4);
      color: #ffffff;
      padding: 12px 20px;
      border-radius: 25px;
      width: 100%;
      max-width: 300px;
    }
    .search-box:focus {
      outline: none;
      border-color: #FFD700;
      background: rgba(255, 255, 255, 0.25);
    }
    .search-box::placeholder { color: #6b7280; }
    .filter-btn {
      background: rgba(255, 255, 255, 0.15);
      border: 1px solid rgba(255, 215, 0, 0.4);
      color: #ffffff;
      padding: 10px 20px;
      border-radius: 25px;
      font-weight: 500;
      transition: all 0.3s;
      cursor: pointer;
    }
    .filter-btn:hover, .filter-btn.active {
      background: linear-gradient(135deg, #FFD700 0%, #FFC107 100%);
      color: #ffffff;
      border-color: #FFD700;
    }
  </style>
</head>
<body>
  <!-- Animated Background -->
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

  <!-- Navigation Bar -->
  @include('components.navigation-bar', ['currentRoute' => 'public.home'])

  <!-- Hero Section -->
  <section id="home" class="hero-section">
    <div class="container">
      <img src="{{ asset('storage/branding/barangay-logo.jpg') }}" alt="Barangay Logo" class="hero-logo" onerror="this.src='https://via.placeholder.com/120?text=BRGY'">
      
      <h1 class="hero-title fade-up">Barangay Bucandala 1</h1>
      <p class="hero-subtitle fade-up delay-1">Your Trusted Local Government Unit in City of Imus, Cavite</p>
      <div class="hero-location fade-up delay-2">
        <i class="bi bi-geo-alt"></i>
        <span>City of Imus, Province of Cavite, Philippines</span>
      </div>

      <!-- Quick Description -->
      <p class="hero-description fade-up delay-3">
        Serving our community with dedication since 1994. Experience convenient online services, transparent governance, and a thriving community.
      </p>

      <!-- Quick Actions -->
      <div class="quick-actions fade-up delay-4">
        <a href="{{ route('public.residents.register') }}" class="action-card glass">
          <div class="action-icon">
            <i class="bi bi-person-plus"></i>
          </div>
          <div class="action-title">Resident Registration</div>
          <div class="action-desc">Register as a resident</div>
        </a>

        <a href="#services" class="action-card glass">
          <div class="action-icon">
            <i class="bi bi-file-earmark-text"></i>
          </div>
          <div class="action-title">Request Document</div>
          <div class="action-desc">Clearance, certification</div>
        </a>

        <a href="{{ route('public.services.blotter') }}" class="action-card glass">
          <div class="action-icon">
            <i class="bi bi-exclamation-triangle"></i>
          </div>
          <div class="action-title">File Blotter</div>
          <div class="action-desc">Report an incident</div>
        </a>

        <a href="#contact" class="action-card glass">
          <div class="action-icon">
            <i class="bi bi-envelope"></i>
          </div>
          <div class="action-title">Contact Us</div>
          <div class="action-desc">Send a message</div>
        </a>
      </div>
    </div>
  </section>

  <!-- About Section -->
  <section id="about" class="section">
    <div class="container">
      <div class="page-header text-center mb-5">
        <h1 class="page-title">About Barangay Bucandala 1</h1>
        <p style="color: rgba(255,255,255,0.9);">Serving our community with dedication and integrity since 1994</p>
      </div>

      <!-- Mission/Vision + Cards -->
      <div class="row g-4 align-items-center">
        <div class="col-lg-6">
          <h2 class="section-title">
            <i class="bi bi-building"></i>
            About Our Barangay
          </h2>
          <p class="mb-4" style="color: rgba(255,255,255,0.9);">
            Barangay Bucandala 1 is a thriving community located in the City of Imus, Province of Cavite. 
            With a population of over 5,000 residents across 1,200 households, we take pride in providing 
            efficient public services and fostering a sense of community among our residents.
          </p>
          <p class="mb-4" style="color: rgba(255,255,255,0.9);">
            Our barangay was established in 1994 and has since grown into a well-organized community with 
            modern facilities, active programs, and dedicated officials working towards the welfare of every resident.
          </p>
        </div>
        <div class="col-lg-6">
          <div class="glass p-4">
            <div class="row g-3 text-center">
              <div class="col-6">
                <div class="text-center p-3">
                  <div class="fs-1 mb-2">👥</div>
                  <div class="fw-bold" style="color: #FFD700; font-size: 1.8rem;">5,200+</div>
                  <div class="small" style="color: rgba(255,255,255,0.8);">Registered Residents</div>
                </div>
              </div>
              <div class="col-6">
                <div class="text-center p-3">
                  <div class="fs-1 mb-2">🏠</div>
                  <div class="fw-bold" style="color: #FFD700; font-size: 1.8rem;">1,200+</div>
                  <div class="small" style="color: rgba(255,255,255,0.8);">Households</div>
                </div>
              </div>
              <div class="col-6">
                <div class="text-center p-3">
                  <div class="fs-1 mb-2">👨‍💼</div>
                  <div class="fw-bold" style="color: #FFD700; font-size: 1.8rem;">15+</div>
                  <div class="small" style="color: rgba(255,255,255,0.8);">Barangay Officials</div>
                </div>
              </div>
              <div class="col-6">
                <div class="text-center p-3">
                  <div class="fs-1 mb-2">📄</div>
                  <div class="fw-bold" style="color: #FFD700; font-size: 1.8rem;">850+</div>
                  <div class="small" style="color: rgba(255,255,255,0.8);">Documents Issued</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- History -->
      <div class="mb-5">
        <h2 class="section-title mb-4"><i class="bi bi-clock-history"></i> Our History</h2>
        <div class="row g-3">
          <div class="col-md-4">
            <div class="glass p-4" style="height: 100%;">
              <div style="font-size: 2rem; font-weight: 700; color: #FFD700; margin-bottom: 8px;">1994</div>
              <p style="color: rgba(255,255,255,0.85); font-size: 0.95rem;">Barangay Bucandala 1 was officially established as a separate barangay from the larger Bucandala district.</p>
            </div>
          </div>
          <div class="col-md-4">
            <div class="glass p-4" style="height: 100%;">
              <div style="font-size: 2rem; font-weight: 700; color: #FFD700; margin-bottom: 8px;">2001</div>
              <p style="color: rgba(255,255,255,0.85); font-size: 0.95rem;">Construction of the new Barangay Hall, providing better facilities for residents and officials.</p>
            </div>
          </div>
          <div class="col-md-4">
            <div class="glass p-4" style="height: 100%;">
              <div style="font-size: 2rem; font-weight: 700; color: #FFD700; margin-bottom: 8px;">2010</div>
              <p style="color: rgba(255,255,255,0.85); font-size: 0.95rem;">Launch of computerization programs and digital record-keeping for efficient service.</p>
            </div>
          </div>
          <div class="col-md-4">
            <div class="glass p-4" style="height: 100%;">
              <div style="font-size: 2rem; font-weight: 700; color: #FFD700; margin-bottom: 8px;">2018</div>
              <p style="color: rgba(255,255,255,0.85); font-size: 0.95rem;">Implementation of the first online services for document requests and resident registration.</p>
            </div>
          </div>
          <div class="col-md-4">
            <div class="glass p-4" style="height: 100%;">
              <div style="font-size: 2rem; font-weight: 700; color: #FFD700; margin-bottom: 8px;">2023</div>
              <p style="color: rgba(255,255,255,0.85); font-size: 0.95rem;">Launch of the new Management Information System with enhanced features and security.</p>
            </div>
          </div>
          <div class="col-md-4">
            <div class="glass p-4" style="height: 100%;">
              <div style="font-size: 2rem; font-weight: 700; color: #FFD700; margin-bottom: 8px;">Present</div>
              <p style="color: rgba(255,255,255,0.85); font-size: 0.95rem;">Continuing our commitment to serve the community with modern technology and dedicated personnel.</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Community Programs -->
      <div class="mb-5">
        <h2 class="section-title mb-4"><i class="bi bi-people"></i> Community Programs</h2>
        <div class="row g-4">
          <div class="col-md-4">
            <div class="glass p-4" style="text-align: center; height: 100%;">
              <div style="width: 60px; height: 60px; margin: 0 auto 16px; background: rgba(255,215,0,0.2); border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 28px; color: #FFD700;">
                <i class="bi bi-heart-pulse"></i>
              </div>
              <h5 style="color: #ffffff; margin-bottom: 8px;">Health Programs</h5>
              <p class="small" style="color: rgba(255,255,255,0.8);">Monthly medical missions, vaccination drives, health seminars, and free check-ups for residents.</p>
            </div>
          </div>
          <div class="col-md-4">
            <div class="glass p-4" style="text-align: center; height: 100%;">
              <div style="width: 60px; height: 60px; margin: 0 auto 16px; background: rgba(255,215,0,0.2); border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 28px; color: #FFD700;">
                <i class="bi bi-book"></i>
              </div>
              <h5 style="color: #ffffff; margin-bottom: 8px;">Education Support</h5>
              <p class="small" style="color: rgba(255,255,255,0.8);">Scholar programs, educational assistance, and learning materials distribution for students.</p>
            </div>
          </div>
          <div class="col-md-4">
            <div class="glass p-4" style="text-align: center; height: 100%;">
              <div style="width: 60px; height: 60px; margin: 0 auto 16px; background: rgba(255,215,0,0.2); border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 28px; color: #FFD700;">
                <i class="bi bi-shield-check"></i>
              </div>
              <h5 style="color: #ffffff; margin-bottom: 8px;">Peace & Order</h5>
              <p class="small" style="color: rgba(255,255,255,0.8);">Tanod watch program, community patrol, and disaster preparedness training.</p>
            </div>
          </div>
          <div class="col-md-4">
            <div class="glass p-4" style="text-align: center; height: 100%;">
              <div style="width: 60px; height: 60px; margin: 0 auto 16px; background: rgba(255,215,0,0.2); border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 28px; color: #FFD700;">
                <i class="bi bi-tree"></i>
              </div>
              <h5 style="color: #ffffff; margin-bottom: 8px;">Environment</h5>
              <p class="small" style="color: rgba(255,255,255,0.8);">Clean and green programs, tree planting, and waste management initiatives.</p>
            </div>
          </div>
          <div class="col-md-4">
            <div class="glass p-4" style="text-align: center; height: 100%;">
              <div style="width: 60px; height: 60px; margin: 0 auto 16px; background: rgba(255,215,0,0.2); border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 28px; color: #FFD700;">
                <i class="bi bi-person-heart"></i>
              </div>
              <h5 style="color: #ffffff; margin-bottom: 8px;">Senior Citizens</h5>
              <p class="small" style="color: rgba(255,255,255,0.8);">Monthly benefits, medical assistance, and social activities for senior residents.</p>
            </div>
          </div>
          <div class="col-md-4">
            <div class="glass p-4" style="text-align: center; height: 100%;">
              <div style="width: 60px; height: 60px; margin: 0 auto 16px; background: rgba(255,215,0,0.2); border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 28px; color: #FFD700;">
                <i class="bi bi-person-hearts"></i>
              </div>
              <h5 style="color: #ffffff; margin-bottom: 8px;">Youth Programs</h5>
              <p class="small" style="color: rgba(255,255,255,0.8);">Sports activities, skills training, and leadership development for the youth.</p>
            </div>
          </div>
        </div>
      </div>
</div>
      </div>

      <!-- Services Section -->
  <section id="services" class="section">
    <div class="container">
      <div class="text-center mb-5">
        <h2 class="section-title" style="justify-content: center;"><i class="bi bi-briefcase"></i> Our Services</h2>
        <p style="color: rgba(255,255,255,0.8);">Choose between onsite or online services</p>
      </div>

      <!-- Onsite Services -->
      <div class="mb-5">
        <div class="section-badge" style="display: inline-block; padding: 8px 20px; border-radius: 50px; font-size: 0.85rem; font-weight: 600; margin-bottom: 24px; background: rgba(239, 68, 68, 0.2); color: #fca5a5;">
          <i class="bi bi-building me-2"></i>Walk-in Services (Visit Barangay Hall)
        </div>
        <div class="row g-4">
          <div class="col-md-4">
            <a href="#faqs" onclick="scrollToFaq('blotter'); return false;" class="service-card glass" style="padding: 32px; display: block; text-decoration: none; color: #ffffff; transition: all 0.3s;">
              <div style="width: 72px; height: 72px; border-radius: 18px; display: flex; align-items: center; justify-content: center; font-size: 32px; margin-bottom: 20px; background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.2); color: #fca5a5;">
                <i class="bi bi-exclamation-triangle"></i>
              </div>
              <h4 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 8px; color: #ffffff;">File a Blotter</h4>
              <p style="font-size: 0.9rem; margin-bottom: 16px; color: rgba(255,255,255,0.9);">Report incidents like theft, disputes, noise complaints, and other disturbances.</p>
              <span style="font-weight: 600; color: #FEEE91; display: flex; align-items: center; gap: 8px;">Learn more <i class="bi bi-arrow-right"></i></span>
            </a>
          </div>
          <div class="col-md-4">
            <a href="#faqs" onclick="scrollToFaq('blotter'); return false;" class="service-card glass" style="padding: 32px; display: block; text-decoration: none; color: #ffffff; transition: all 0.3s;">
              <div style="width: 72px; height: 72px; border-radius: 18px; display: flex; align-items: center; justify-content: center; font-size: 32px; margin-bottom: 20px; background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.2); color: #93c5fd;">
                <i class="bi bi-shield-check"></i>
              </div>
              <h4 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 8px; color: #ffffff;">Open a Case</h4>
              <p style="font-size: 0.9rem; margin-bottom: 16px; color: rgba(255,255,255,0.9);">Start formal legal cases for disputes, land issues, or family matters.</p>
              <span style="font-weight: 600; color: #FEEE91; display: flex; align-items: center; gap: 8px;">Learn more <i class="bi bi-arrow-right"></i></span>
            </a>
          </div>
          <div class="col-md-4">
            <a href="#faqs" onclick="scrollToFaq('documents'); return false;" class="service-card glass" style="padding: 32px; display: block; text-decoration: none; color: #ffffff; transition: all 0.3s;">
              <div style="width: 72px; height: 72px; border-radius: 18px; display: flex; align-items: center; justify-content: center; font-size: 32px; margin-bottom: 20px; background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.2); color: #FEEE91;">
                <i class="bi bi-file-earmark-text"></i>
              </div>
              <h4 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 8px; color: #ffffff;">Document Request</h4>
              <p style="font-size: 0.9rem; margin-bottom: 16px; color: rgba(255,255,255,0.9);">Request clearance, certificates, and permits. Pay online or at the hall.</p>
              <span style="font-weight: 600; color: #FEEE91; display: flex; align-items: center; gap: 8px;">Learn more <i class="bi bi-arrow-right"></i></span>
            </a>
          </div>
        </div>

        <!-- How to Access -->
        <div class="glass p-4 mt-4" style="background: rgba(16, 85, 201, 0.15); border: 1px solid rgba(255,255,255,0.25);">
          <h5 class="mb-3" style="color: #ffffff;"><i class="bi bi-signpost me-2"></i>How to Access Onsite Services</h5>
          <div class="row g-3">
            <div class="col-md-3">
              <div class="glass" style="padding: 20px; display: flex; gap: 16px; margin-bottom: 12px; background: rgba(255, 255, 255, 0.18); border: 1px solid rgba(255, 255, 255, 0.25); border-radius: 16px;">
                <div style="width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; background: #1055C9; flex-shrink: 0;">1</div>
                <div>
                  <strong style="color: #ffffff;">Visit the Hall</strong>
                  <p class="small mb-0" style="font-size: 0.875rem; color: rgba(255,255,255,0.85);">Go to Barangay Bucandala 1 Hall</p>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="glass" style="padding: 20px; display: flex; gap: 16px; margin-bottom: 12px; background: rgba(255, 255, 255, 0.18); border: 1px solid rgba(255, 255, 255, 0.25); border-radius: 16px;">
                <div style="width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; background: #1055C9; flex-shrink: 0;">2</div>
                <div>
                  <strong style="color: #ffffff;">Get a Number</strong>
                  <p class="small mb-0" style="font-size: 0.875rem; color: rgba(255,255,255,0.85);">Take a queue number at the entrance</p>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="glass" style="padding: 20px; display: flex; gap: 16px; margin-bottom: 12px; background: rgba(255, 255, 255, 0.18); border: 1px solid rgba(255, 255, 255, 0.25); border-radius: 16px;">
                <div style="width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; background: #1055C9; flex-shrink: 0;">3</div>
                <div>
                  <strong style="color: #ffffff;">Submit Requirements</strong>
                  <p class="small mb-0" style="font-size: 0.875rem; color: rgba(255,255,255,0.85);">Provide necessary documents</p>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="glass" style="padding: 20px; display: flex; gap: 16px; margin-bottom: 12px; background: rgba(255, 255, 255, 0.18); border: 1px solid rgba(255, 255, 255, 0.25); border-radius: 16px;">
                <div style="width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; background: #1055C9; flex-shrink: 0;">4</div>
                <div>
                  <strong style="color: #ffffff;">Pay & Receive</strong>
                  <p class="small mb-0" style="font-size: 0.875rem; color: rgba(255,255,255,0.85);">Pay fees and receive your document</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Online Services -->
      <div class="mb-5">
        <div class="section-badge" style="display: inline-block; padding: 8px 20px; border-radius: 50px; font-size: 0.85rem; font-weight: 600; margin-bottom: 24px; background: rgba(40, 167, 69, 0.2); color: #86efac;">
          <i class="bi bi-laptop me-2"></i>Online Services (From Home)
        </div>
        <div class="row g-4">
          <div class="col-md-4">
            <a href="{{ Auth::check() && Auth::user()->role === 'resident' ? route('resident.dashboard') : route('public.residents.register') }}" class="service-card glass" style="padding: 32px; display: block; text-decoration: none; color: inherit; transition: all 0.3s;">
              <div style="width: 72px; height: 72px; border-radius: 18px; display: flex; align-items: center; justify-content: center; font-size: 32px; margin-bottom: 20px; background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.2); color: #86efac;">
                <i class="bi bi-person-plus"></i>
              </div>
              <h4 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 8px;">Register as Resident</h4>
              <p style="font-size: 0.9rem; opacity: 0.75; margin-bottom: 16px;">Sign up as a new resident with OTP verification and ID upload.</p>
              <span style="font-weight: 600; color: #FEEE91; display: flex; align-items: center; gap: 8px;">{{ Auth::check() && Auth::user()->role === 'resident' ? 'Go to Dashboard' : 'Register now' }} <i class="bi bi-arrow-right"></i></span>
            </a>
          </div>
          <div class="col-md-4">
            <a href="{{ Auth::check() && Auth::user()->role === 'resident' ? route('resident.pets') : route('login') }}" class="service-card glass" style="padding: 32px; display: block; text-decoration: none; color: #ffffff; transition: all 0.3s; opacity: {{ (Auth::check() && Auth::user()->role === 'resident') ? '1' : '0.7' }};">
              <div style="width: 72px; height: 72px; border-radius: 18px; display: flex; align-items: center; justify-content: center; font-size: 32px; margin-bottom: 20px; background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.2); color: #d8b4fe;">
                <i class="bi bi-heart-pulse"></i>
              </div>
              <h4 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 8px; color: #ffffff;">Pet Registration</h4>
              <p style="font-size: 0.9rem; margin-bottom: 16px; color: rgba(255,255,255,0.9);">Register your pets, upload vaccination records, and get QR codes.</p>
              <span style="font-weight: 600; color: #FEEE91; display: flex; align-items: center; gap: 8px;">{{ (Auth::check() && Auth::user()->role === 'resident') ? 'Manage Pets' : 'Login to Access' }} <i class="bi bi-arrow-right"></i></span>
            </a>
          </div>
          <div class="col-md-4">
            <a href="{{ Auth::check() && Auth::user()->role === 'resident' ? route('resident.household') : route('login') }}" class="service-card glass" style="padding: 32px; display: block; text-decoration: none; color: #ffffff; transition: all 0.3s; opacity: {{ (Auth::check() && Auth::user()->role === 'resident') ? '1' : '0.7' }};">
              <div style="width: 72px; height: 72px; border-radius: 18px; display: flex; align-items: center; justify-content: center; font-size: 32px; margin-bottom: 20px; background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.2); color: #fbbf24;">
                <i class="bi bi-people"></i>
              </div>
              <h4 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 8px; color: #ffffff;">Household Register</h4>
              <p style="font-size: 0.9rem; margin-bottom: 16px; color: rgba(255,255,255,0.9);">Create and manage household profiles and add members.</p>
              <span style="font-weight: 600; color: #FEEE91; display: flex; align-items: center; gap: 8px;">{{ (Auth::check() && Auth::user()->role === 'resident') ? 'Manage Household' : 'Login to Access' }} <i class="bi bi-arrow-right"></i></span>
            </a>
          </div>
        </div>
      </div>

      <!-- Info Cards -->
      <div class="row g-4">
        <div class="col-md-6">
          <div class="glass p-4">
            <h5 class="mb-3" style="color: #ffffff;"><i class="bi bi-clock me-2"></i>Office Hours</h5>
            <div class="d-flex justify-content-between mb-2" style="color: rgba(255,255,255,0.85);">
              <span>Monday - Friday</span>
              <span>8:00 AM - 5:00 PM</span>
            </div>
            <div class="d-flex justify-content-between mb-2" style="color: rgba(255,255,255,0.85);">
              <span>Saturday</span>
              <span>8:00 AM - 12:00 PM</span>
            </div>
            <div class="d-flex justify-content-between" style="color: rgba(255,255,255,0.85);">
              <span>Sunday</span>
              <span>Closed</span>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="glass p-4">
            <h5 class="mb-3" style="color: #ffffff;"><i class="bi bi-telephone me-2"></i>Contact for Appointments</h5>
            <p class="mb-2" style="color: rgba(255,255,255,0.85);"><i class="bi bi-phone me-2"></i>(046) 123-4567</p>
            <p class="mb-0" style="color: rgba(255,255,255,0.85);"><i class="bi bi-envelope me-2"></i>info@bucandala1.gov.ph</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- News & Announcements Section -->
  <section id="news" class="section" data-section="news" style="background: rgba(16, 85, 201, 0.08); border-radius: 20px; margin: 20px;">
    <div class="container">
      <div class="text-center mb-5">
        <h2 class="section-title mb-4" style="justify-content: center;"><i class="bi bi-newspaper"></i> News & Events</h2>
        <p style="color: rgba(255,255,255,0.8);">Stay updated with the latest announcements and events from Barangay Bucandala 1</p>
      </div>

      <div id="listView">
        <div class="row">
          <!-- Main Content -->
          <div class="col-lg-8">
            <!-- Announcements -->
            @if($announcements ?? collect([])->count() > 0)
            <div class="glass p-4 mb-4" style="background: rgba(255, 255, 255, 0.12);">
              <h5 class="mb-3" style="color: #ffffff;"><i class="bi bi-megaphone me-2"></i>Announcements</h5>
              @foreach($announcements ?? [] as $announcement)
                <div class="announcement-card glass" style="background: rgba(255, 255, 255, 0.15);">
                  <span class="announcement-badge 
                    @if($announcement->priority === 'high') badge-important
                    @elseif($announcement->type === 'event') badge-event
                    @elseif($announcement->type === 'news') badge-news
                    @else badge-general @endif">
                    {{ $announcement->type ?? 'announcement' }}
                  </span>
                  <div class="d-flex justify-content-between align-items-start">
                    <h6 class="mb-2" style="color: #ffffff;">{{ $announcement->title }}</h6>
                    <small style="color: rgba(255,255,255,0.6);">{{ $announcement->created_at->format('M d, Y') }}</small>
                  </div>
                  <p class="mb-2 small" style="color: rgba(255,255,255,0.8);">{{ Str::limit($announcement->content, 120) }}</p>
                  <button class="btn btn-warning btn-sm" data-title="{{ $announcement->title }}" data-content="{{ $announcement->content }}" data-date="{{ $announcement->created_at->format('M d, Y') }}" onclick="showAnnouncementModal(this)">
                    <i class="bi bi-eye me-1"></i>View Details
                  </button>
                </div>
              @endforeach
              
              <!-- Announcements Pagination -->
              @if($announcements->hasPages())
              <div class="mt-4" style="display: flex; justify-content: center; gap: 8px; flex-wrap: wrap;">
                @php
                  $currentPage = $announcements->currentPage();
                  $lastPage = $announcements->lastPage();
                  $nextPageUrl = $announcements->nextPageUrl();
                  $prevPageUrl = $announcements->previousPageUrl();
                @endphp
                
                @if($prevPageUrl)
                  <a href="{{ $prevPageUrl }}#news" style="
                      background: rgba(255,255,255,0.12);
                      border: 1px solid rgba(255, 215, 0, 0.35);
                      color: #ffffff;
                      padding: 10px 16px;
                      border-radius: 10px;
                      font-weight: 500;
                      text-decoration: none;
                      display: inline-block;
                  ">« Previous</a>
                @else
                  <span style="
                      background: rgba(255, 255, 255, 0.1);
                      border: 2px solid rgba(255, 255, 255, 0.2);
                      color: rgba(255, 255, 255, 0.35);
                      padding: 12px 18px;
                      border-radius: 12px;
                      font-weight: 600;
                      display: inline-block;
                      cursor: not-allowed;
                  ">« Previous</span>
                @endif

                @for($i = 1; $i <= $lastPage; $i++)
                  @if($i == $currentPage)
                    <span style="
                        background: #FFD700;
                        border: 1px solid #FFD700;
                        color: #1f2937;
                        padding: 10px 16px;
                        border-radius: 10px;
                        font-weight: 600;
                        display: inline-block;
                    ">{{ $i }}</span>
                  @else
                    <a href="{{ $announcements->url($i) }}#news" style="
                        background: rgba(255,255,255,0.12);
                        border: 1px solid rgba(255, 215, 0, 0.35);
                        color: #ffffff;
                        padding: 10px 16px;
                        border-radius: 10px;
                        font-weight: 500;
                        text-decoration: none;
                        display: inline-block;
                    ">{{ $i }}</a>
                  @endif
                @endfor

                @if($nextPageUrl)
                  <a href="{{ $nextPageUrl }}#news" style="
                      background: rgba(255,255,255,0.12);
                      border: 1px solid rgba(255, 215, 0, 0.35);
                      color: #ffffff;
                      padding: 10px 16px;
                      border-radius: 10px;
                      font-weight: 500;
                      text-decoration: none;
                      display: inline-block;
                  ">Next »</a>
                @else
                  <span style="
                      background: rgba(255, 255, 255, 0.1);
                      border: 2px solid rgba(255, 255, 255, 0.2);
                      color: rgba(255, 255, 255, 0.35);
                      padding: 12px 18px;
                      border-radius: 12px;
                      font-weight: 600;
                      display: inline-block;
                      cursor: not-allowed;
                  ">Next »</span>
                @endif
              </div>
              @endif
            </div>
            @endif

            
          </div>

          <!-- Sidebar -->
          <div class="col-lg-4">
            <!-- Upcoming Events Sidebar -->
            @if($events ?? collect([])->count() > 0)
            <div class="glass p-4 mb-4" style="background: rgba(255, 255, 255, 0.12);">
              <h6 class="mb-3" style="color: #ffffff;"><i class="bi bi-calendar-event me-2"></i>Upcoming Events</h6>
              @foreach($events ?? [] as $event)
                <?php
                  $isPast = $event->end_datetime && $event->end_datetime->isPast();
                $isOngoing = $event->start_datetime->isPast() && $event->end_datetime && $event->end_datetime->isFuture();
                ?>
                <div class="event-card glass" style="background: rgba(255, 255, 255, 0.15);">
                  <div class="event-date-box">
                    <div class="event-month">{{ $event->start_datetime->format('M') }}</div>
                    <div class="event-day">{{ $event->start_datetime->format('d') }}</div>
                  </div>
                  <div class="flex-grow-1">
                    <h6 class="mb-0" style="color: #ffffff;">{{ $event->title }}</h6>
                    <small style="color: rgba(255,255,255,0.7);"><i class="bi bi-clock me-1"></i>{{ $event->start_datetime->format('g:i A') }}</small>
                  </div>
                </div>
              @endforeach
              
              <!-- Events Pagination -->
              @if($events->hasPages())
              <div class="mt-3" style="display: flex; justify-content: center; gap: 6px; flex-wrap: wrap;">
                @php
                  $eventCurrent = $events->currentPage();
                  $eventLast = $events->lastPage();
                  $eventPrev = $events->previousPageUrl();
                  $eventNext = $events->nextPageUrl();
                @endphp
                
                @if($eventPrev)
                  <a href="{{ $eventPrev }}#news" style="padding: 8px 12px; background: rgba(255,255,255,0.12); border: 1px solid rgba(255,215,0,0.35); border-radius: 8px; color: #ffffff; text-decoration: none; font-size: 13px;">« Prev</a>
                @endif
                
                @for($i = 1; $i <= $eventLast; $i++)
                  @if($i == $eventCurrent)
                    <span style="padding: 8px 12px; background: #FFD700; border: 1px solid #FFD700; border-radius: 8px; color: #1f2937; font-weight: 600; font-size: 13px;">{{ $i }}</span>
                  @else
                    <a href="{{ $events->url($i) }}#news" style="padding: 8px 12px; background: rgba(255,255,255,0.12); border: 1px solid rgba(255,215,0,0.35); border-radius: 8px; color: #ffffff; text-decoration: none; font-size: 13px;">{{ $i }}</a>
                  @endif
                @endfor
                
                @if($eventNext)
                  <a href="{{ $eventNext }}#news" style="padding: 8px 12px; background: rgba(255,255,255,0.12); border: 1px solid rgba(255,215,0,0.35); border-radius: 8px; color: #ffffff; text-decoration: none; font-size: 13px;">Next »</a>
                @endif
              </div>
              @endif
            </div>
            @endif

            <!-- Calendar -->
            <div class="glass p-4 mb-4" style="background: rgba(255, 255, 255, 0.12);">
              <h6 class="mb-3" style="color: #ffffff;"><i class="bi bi-calendar3 me-2"></i>Calendar</h6>
              <div id="calendarContainer">
                <div class="d-flex justify-content-between align-items-center mb-3">
                  <button type="button" onclick="changeCalendarMonth(-1)" style="background:none;border:none;cursor:pointer;font-size:20px;color:#FFD700;padding:4px 12px;border-radius:8px;">‹</button>
                  <span id="calendarMonthYear" style="font-weight:700;font-size:16px;color:#ffffff;"></span>
                  <button type="button" onclick="changeCalendarMonth(1)" style="background:none;border:none;cursor:pointer;font-size:20px;color:#FFD700;padding:4px 12px;border-radius:8px;">›</button>
                </div>
                <div style="display:grid;grid-template-columns:repeat(7,1fr);gap:4px;text-align:center;">
                  @foreach(['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $day)
                    <div style="font-size:11px;font-weight:600;opacity:0.7;color:rgba(255,255,255,0.7);padding:4px;">{{ $day }}</div>
                  @endforeach
                  <div id="calendarDays" style="grid-column: 1 / span 7; display: grid; grid-template-columns: repeat(7, 1fr); gap: 4px;"></div>
                </div>
              </div>
            </div>

            <!-- Office Hours -->
            <div class="glass p-4" style="background: rgba(255, 255, 255, 0.12);">
              <h6 class="mb-3" style="color: #ffffff;"><i class="bi bi-clock me-2"></i>Office Hours</h6>
              <div class="d-flex justify-content-between py-1" style="color: rgba(255,255,255,0.85);"><span>Mon-Fri</span><span class="fw-medium">8AM-5PM</span></div>
              <div class="d-flex justify-content-between py-1" style="color: rgba(255,255,255,0.85);"><span>Saturday</span><span class="fw-medium">8AM-12PM</span></div>
              <div class="d-flex justify-content-between py-1" style="color: rgba(255,255,255,0.85);"><span>Sunday</span><span style="color: rgba(255,255,255,0.5);">Closed</span></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Calendar View -->
      <div id="calendarView" style="display: none;">
        <div class="glass p-4">
          <div id="calendar"></div>
        </div>
      </div>
    </div>
  </section>

  <!-- Officials Section -->
  <section id="officials" class="section">
    <div class="container">
      <h2 class="section-title mb-4"><i class="bi bi-award"></i>Barangay Officials</h2>
      <p style="margin-bottom: 30px; color: rgba(255,255,255,0.8);">Meet our dedicated team serving the community</p>

      <!-- Search & Filter -->
      <div class="glass p-4 mb-4">
        <div class="d-flex flex-wrap gap-3 align-items-center justify-content-center">
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
    </div>
  </section>

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

  <!-- Why Our Online System -->
  <section class="section">
    <div class="container">
      <h2 class="section-title mb-4">
        <i class="bi bi-laptop"></i>
        Why Our Online System?
      </h2>
      <p style="margin-bottom: 24px; color: rgba(255,255,255,0.8);">Experience convenient, secure, and transparent services</p>

      <div class="glass p-4">
        <div class="row g-4">
          <div class="col-md-4">
            <h5 style="color: #ffffff;"><i class="bi bi-clock me-2" style="color: #FFD700;"></i>Save Time</h5>
            <p class="small" style="color: rgba(255,255,255,0.8);">Skip the long queues. Register, request documents, and file reports from anywhere, anytime.</p>
          </div>
          <div class="col-md-4">
            <h5 style="color: #ffffff;"><i class="bi bi-shield-lock me-2" style="color: #86efac;"></i>Secure & Private</h5>
            <p class="small" style="color: rgba(255,255,255,0.8);">Your data is protected under the Data Privacy Act. We use secure encryption for all transactions.</p>
          </div>
          <div class="col-md-4">
            <h5 style="color: #ffffff;"><i class="bi bi-graph-up me-2" style="color: #93c5fd;"></i>Transparent</h5>
            <p class="small" style="color: rgba(255,255,255,0.8);">Track your requests in real-time. Know the status of your documents, blotters, and applications.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- FAQs Section -->
  <section id="faqs" class="section">
    <div class="container">
      <h2 class="section-title mb-4">
        <i class="bi bi-question-circle"></i>
        Frequently Asked Questions
      </h2>
      <p style="margin-bottom: 24px; color: rgba(255,255,255,0.8);">Find answers to common questions about our services</p>

      <!-- Search -->
      <div class="glass p-3 mb-4">
        <input type="text" class="form-control" id="faqSearch" placeholder="Search for a question..." style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.3); color: #fff; padding: 12px 20px; border-radius: 12px;">
      </div>

      <!-- Categories -->
      <div class="d-flex flex-wrap gap-2 mb-4">
        <button class="category-btn active" data-category="all" style="background: rgba(255,255,255,0.15); border: 1px solid rgba(255,215,0,0.4); color: #ffffff; padding: 10px 20px; border-radius: 25px; font-weight: 500; cursor: pointer;">All</button>
        <button class="category-btn" data-category="registration" style="background: rgba(255,255,255,0.15); border: 1px solid rgba(255,215,0,0.4); color: #ffffff; padding: 10px 20px; border-radius: 25px; font-weight: 500; cursor: pointer;">Registration</button>
        <button class="category-btn" data-category="documents" style="background: rgba(255,255,255,0.15); border: 1px solid rgba(255,215,0,0.4); color: #ffffff; padding: 10px 20px; border-radius: 25px; font-weight: 500; cursor: pointer;">Documents</button>
        <button class="category-btn" data-category="blotter" style="background: rgba(255,255,255,0.15); border: 1px solid rgba(255,215,0,0.4); color: #ffffff; padding: 10px 20px; border-radius: 25px; font-weight: 500; cursor: pointer;">Blotter</button>
        <button class="category-btn" data-category="services" style="background: rgba(255,255,255,0.15); border: 1px solid rgba(255,215,0,0.4); color: #ffffff; padding: 10px 20px; border-radius: 25px; font-weight: 500; cursor: pointer;">Services</button>
        <button class="category-btn" data-category="account" style="background: rgba(255,255,255,0.15); border: 1px solid rgba(255,215,0,0.4); color: #ffffff; padding: 10px 20px; border-radius: 25px; font-weight: 500; cursor: pointer;">Account</button>
      </div>

      <div class="row">
        <div class="col-lg-8">
          <!-- Registration FAQs -->
          <div class="faq-item" data-category="registration" style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); border-radius: 16px; margin-bottom: 12px; overflow: hidden;">
            <div class="faq-question" style="padding: 20px; cursor: pointer; display: flex; justify-content: space-between; align-items: center; font-weight: 600;" >
              <span>How do I register as a new resident?</span>
              <i class="bi bi-chevron-down"></i>
            </div>
            <div class="faq-answer" style="display: none; padding: 0 20px; max-height: 0; overflow: hidden; transition: all 0.3s ease;">
              <p style="margin-bottom: 10px; opacity: 0.75;">Click the "Register" button on the homepage or navigate to the registration page. Fill out the form with your personal details, upload a valid ID and a selfie holding your ID, then verify your email with the OTP sent to your inbox.</p>
              <span class="tag" style="display: inline-block; padding: 4px 12px; border-radius: 50px; font-size: 0.75rem; background: rgba(255,255,255,0.1); margin-bottom: 15px;">Registration</span>
            </div>
          </div>

          <div class="faq-item" data-category="account" style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); border-radius: 16px; margin-bottom: 12px; overflow: hidden;">
            <div class="faq-question" style="padding: 20px; cursor: pointer; display: flex; justify-content: space-between; align-items: center; font-weight: 600;" >
              <span>How do I reset my password?</span>
              <i class="bi bi-chevron-down"></i>
            </div>
            <div class="faq-answer" style="display: none; padding: 0 20px; max-height: 0; overflow: hidden; transition: all 0.3s ease;">
              <p style="margin-bottom: 10px; opacity: 0.75;">Currently, password reset is handled by barangay staff. Please visit the barangay hall with a valid ID to reset your password. For security purposes, we verify your identity in person.</p>
              <span class="tag" style="display: inline-block; padding: 4px 12px; border-radius: 50px; font-size: 0.75rem; background: rgba(255,255,255,0.1); margin-bottom: 15px;">Account</span>
            </div>
          </div>

          <!-- Document FAQs -->
          <div class="faq-item" data-category="documents" style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); border-radius: 16px; margin-bottom: 12px; overflow: hidden;">
            <div class="faq-question" style="padding: 20px; cursor: pointer; display: flex; justify-content: space-between; align-items: center; font-weight: 600;" >
              <span>What documents do I need for barangay clearance?</span>
              <i class="bi bi-chevron-down"></i>
            </div>
            <div class="faq-answer" style="display: none; padding: 0 20px; max-height: 0; overflow: hidden; transition: all 0.3s ease;">
              <p style="margin-bottom: 10px; opacity: 0.75;">You need: (1) Valid government-issued ID, (2) Recent 1x1 photo, (3) Proof of residency (if not a registered resident). The fee is ₱50.00.</p>
              <span class="tag" style="display: inline-block; padding: 4px 12px; border-radius: 50px; font-size: 0.75rem; background: rgba(255,255,255,0.1); margin-bottom: 15px;">Documents</span>
            </div>
          </div>

          <div class="faq-item" data-category="documents" style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); border-radius: 16px; margin-bottom: 12px; overflow: hidden;">
            <div class="faq-question" style="padding: 20px; cursor: pointer; display: flex; justify-content: space-between; align-items: center; font-weight: 600;" >
              <span>How long does it take to process a document request?</span>
              <i class="bi bi-chevron-down"></i>
            </div>
            <div class="faq-answer" style="display: none; padding: 0 20px; max-height: 0; overflow: hidden; transition: all 0.3s ease;">
              <p style="margin-bottom: 10px; opacity: 0.75;">Processing times vary: Barangay Clearance (1-2 business days), Certificate of Indigency (1-2 business days), Business Permit (2-3 business days), Certificate of Residency (1-2 business days).</p>
              <span class="tag" style="display: inline-block; padding: 4px 12px; border-radius: 50px; font-size: 0.75rem; background: rgba(255,255,255,0.1); margin-bottom: 15px;">Documents</span>
            </div>
          </div>

          <div class="faq-item" data-category="documents" style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); border-radius: 16px; margin-bottom: 12px; overflow: hidden;">
            <div class="faq-question" style="padding: 20px; cursor: pointer; display: flex; justify-content: space-between; align-items: center; font-weight: 600;" >
              <span>Can I pay for documents online?</span>
              <i class="bi bi-chevron-down"></i>
            </div>
            <div class="faq-answer" style="display: none; padding: 0 20px; max-height: 0; overflow: hidden; transition: all 0.3s ease;">
              <p style="margin-bottom: 10px; opacity: 0.75;">Yes! We accept online payment via GCash. You can also pay in cash at the barangay hall when you claim your document.</p>
              <span class="tag" style="display: inline-block; padding: 4px 12px; border-radius: 50px; font-size: 0.75rem; background: rgba(255,255,255,0.1); margin-bottom: 15px;">Documents</span>
            </div>
          </div>

          <!-- Blotter FAQs -->
          <div class="faq-item" data-category="blotter" style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); border-radius: 16px; margin-bottom: 12px; overflow: hidden;">
            <div class="faq-question" style="padding: 20px; cursor: pointer; display: flex; justify-content: space-between; align-items: center; font-weight: 600;" >
              <span>How do I file a blotter/report an incident?</span>
              <i class="bi bi-chevron-down"></i>
            </div>
            <div class="faq-answer" style="display: none; padding: 0 20px; max-height: 0; overflow: hidden; transition: all 0.3s ease;">
              <p style="margin-bottom: 10px; opacity: 0.75;">You can file a blotter online through our website or visit the barangay hall in person. Provide details about the incident including date, time, location, and parties involved. Your identity will be kept confidential.</p>
              <span class="tag" style="display: inline-block; padding: 4px 12px; border-radius: 50px; font-size: 0.75rem; background: rgba(255,255,255,0.1); margin-bottom: 15px;">Blotter</span>
            </div>
          </div>

          <div class="faq-item" data-category="blotter" style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); border-radius: 16px; margin-bottom: 12px; overflow: hidden;">
            <div class="faq-question" style="padding: 20px; cursor: pointer; display: flex; justify-content: space-between; align-items: center; font-weight: 600;" >
              <span>What happens after I file a blotter?</span>
              <i class="bi bi-chevron-down"></i>
            </div>
            <div class="faq-answer" style="display: none; padding: 0 20px; max-height: 0; overflow: hidden; transition: all 0.3s ease;">
              <p style="margin-bottom: 10px; opacity: 0.75;">Once filed, a barangay official will contact you within 2-3 business days to verify your report. A hearing may be scheduled if necessary. Both parties will be invited to a mediation session.</p>
              <span class="tag" style="display: inline-block; padding: 4px 12px; border-radius: 50px; font-size: 0.75rem; background: rgba(255,255,255,0.1); margin-bottom: 15px;">Blotter</span>
            </div>
          </div>

          <!-- Services FAQs -->
          <div class="faq-item" data-category="services" style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); border-radius: 16px; margin-bottom: 12px; overflow: hidden;">
            <div class="faq-question" style="padding: 20px; cursor: pointer; display: flex; justify-content: space-between; align-items: center; font-weight: 600;" >
              <span>What are the barangay office hours?</span>
              <i class="bi bi-chevron-down"></i>
            </div>
            <div class="faq-answer" style="display: none; padding: 0 20px; max-height: 0; overflow: hidden; transition: all 0.3s ease;">
              <p style="margin-bottom: 10px; opacity: 0.75;">Monday - Friday: 8:00 AM - 5:00 PM<br>Saturday: 8:00 AM - 12:00 PM<br>Sunday: Closed</p>
              <span class="tag" style="display: inline-block; padding: 4px 12px; border-radius: 50px; font-size: 0.75rem; background: rgba(255,255,255,0.1); margin-bottom: 15px;">Services</span>
            </div>
          </div>

          <div class="faq-item" data-category="services" style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); border-radius: 16px; margin-bottom: 12px; overflow: hidden;">
            <div class="faq-question" style="padding: 20px; cursor: pointer; display: flex; justify-content: space-between; align-items: center; font-weight: 600;" >
              <span>Where is the barangay hall located?</span>
              <i class="bi bi-chevron-down"></i>
            </div>
            <div class="faq-answer" style="display: none; padding: 0 20px; max-height: 0; overflow: hidden; transition: all 0.3s ease;">
              <p style="margin-bottom: 10px; opacity: 0.75;">Barangay Bucandala 1 is located in the City of Imus, Cavite. The main hall is near the Bucandala Plaza. You can find directions on our Contact page.</p>
              <span class="tag" style="display: inline-block; padding: 4px 12px; border-radius: 50px; font-size: 0.75rem; background: rgba(255,255,255,0.1); margin-bottom: 15px;">Services</span>
            </div>
          </div>

          <div class="faq-item" data-category="services" style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); border-radius: 16px; margin-bottom: 12px; overflow: hidden;">
            <div class="faq-question" style="padding: 20px; cursor: pointer; display: flex; justify-content: space-between; align-items: center; font-weight: 600;" >
              <span>How do I schedule an appointment?</span>
              <i class="bi bi-chevron-down"></i>
            </div>
            <div class="faq-answer" style="display: none; padding: 0 20px; max-height: 0; overflow: hidden; transition: all 0.3s ease;">
              <p style="margin-bottom: 10px; opacity: 0.75;">Call (046) 123-4567 or email info@bucandala1.gov.ph to schedule an appointment. Walk-ins are also accepted but appointments are given priority.</p>
              <span class="tag" style="display: inline-block; padding: 4px 12px; border-radius: 50px; font-size: 0.75rem; background: rgba(255,255,255,0.1); margin-bottom: 15px;">Services</span>
            </div>
          </div>
        </div>

        <div class="col-lg-4">
          <div class="glass p-4">
            <h5 class="mb-3">Can't find what you're looking for?</h5>
            <p class="small mb-3" style="opacity: 0.75;">Contact us directly and we'll help you with your question.</p>
            <a href="{{ route('public.contact') }}" class="btn btn-glass w-100" style="background: rgba(255,255,255,0.15); border: 1px solid rgba(255,215,0,0.4); color: #ffffff; padding: 12px 20px; border-radius: 12px; display: inline-block; text-decoration: none; text-align: center;">
              <i class="bi bi-envelope me-2"></i>Contact Us
            </a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <script>
    // FAQ Toggle
    function toggleFaq(element) {
      const item = element.parentElement;
      const answer = element.nextElementSibling;
      item.classList.toggle('open');
      if (item.classList.contains('open')) {
        answer.style.display = 'block';
        answer.style.maxHeight = '500px';
        answer.style.padding = '0 20px 20px';
      } else {
        answer.style.display = 'none';
        answer.style.maxHeight = '0';
        answer.style.padding = '0 20px';
      }
    }

    document.querySelectorAll('.faq-question').forEach(q => {
      q.addEventListener('click', function() {
        toggleFaq(this);
      });
    });

    // Category Filter
    document.querySelectorAll('.category-btn').forEach(btn => {
      btn.addEventListener('click', () => {
        document.querySelectorAll('.category-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        
        const cat = btn.dataset.category;
        document.querySelectorAll('.faq-item').forEach(item => {
          if (cat === 'all' || item.dataset.category === cat) {
            item.style.display = 'block';
          } else {
            item.style.display = 'none';
          }
        });
      });
    });

    // Search
    document.getElementById('faqSearch').addEventListener('input', (e) => {
      const search = e.target.value.toLowerCase();
      document.querySelectorAll('.faq-item').forEach(item => {
        const text = item.textContent.toLowerCase();
        item.style.display = text.includes(search) ? 'block' : 'none';
      });
    });

    // Scroll to FAQ and open related accordion
    function scrollToFaq(category) {
      const faqSection = document.getElementById('faqs');
      if (faqSection) {
        // Filter to show only the relevant category
        document.querySelectorAll('.category-btn').forEach(b => b.classList.remove('active'));
        const categoryBtn = document.querySelector(`.category-btn[data-category="${category}"]`);
        if (categoryBtn) {
          categoryBtn.classList.add('active');
          document.querySelectorAll('.faq-item').forEach(item => {
            if (category === 'all' || item.dataset.category === category) {
              item.style.display = 'block';
            } else {
              item.style.display = 'none';
            }
          });
        }
        
        // Scroll to FAQs section
        faqSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
        
        // Open the first FAQ in the category after a short delay
        setTimeout(() => {
          const firstFaqInCategory = document.querySelector(`.faq-item[data-category="${category}"]`);
          if (firstFaqInCategory) {
            const question = firstFaqInCategory.querySelector('.faq-question');
            if (question && !firstFaqInCategory.classList.contains('open')) {
              toggleFaq(question);
            }
          }
        }, 500);
      }
    }
  </script>

  <!-- Contact Section -->
  <section id="contact" class="section">
    <div class="container">
      <h2 class="section-title mb-4">
        <i class="bi bi-telephone"></i>
        Contact Us
      </h2>
      <p style="margin-bottom: 24px; color: rgba(255,255,255,0.8);">Get in touch with the barangay office</p>

      <div class="row g-4">
        <div class="col-lg-7">
          <!-- Contact Form -->
          <div class="glass p-4 mb-4">
            <h5 class="mb-3" style="color: #ffffff;"><i class="bi bi-envelope me-2"></i>Send us a Message</h5>
            <p class="small mb-4" style="color: rgba(255,255,255,0.8);">Have a question or feedback? Fill out the form below.</p>
            <form id="homeContactForm">
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label">Full Name <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" id="fullName" placeholder="Your full name" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Email <span class="text-danger">*</span></label>
                  <input type="email" class="form-control" id="email" placeholder="your@email.com" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Phone</label>
                  <input type="tel" class="form-control" id="phone" placeholder="09xxxxxxxxx">
                </div>
                <div class="col-md-6">
                  <label class="form-label">Subject <span class="text-danger">*</span></label>
                  <select class="form-select" id="subject" required>
                    <option value="">Select a subject</option>
                    <option value="general">General Inquiry</option>
                    <option value="document">Document Request</option>
                    <option value="complaint">File a Complaint</option>
                    <option value="suggestion">Suggestion</option>
                    <option value="other">Other</option>
                  </select>
                </div>
                <div class="col-12">
                  <label class="form-label">Message <span class="text-danger">*</span></label>
                  <textarea class="form-control" id="message" rows="4" placeholder="Your message here..." required></textarea>
                </div>
                <div class="col-12">
                  <button type="submit" class="btn btn-glass px-4">
                    <i class="bi bi-send me-2"></i>Send Message
                  </button>
                </div>
              </div>
            </form>
          </div>

          <div class="row g-3">
            <div class="col-md-6">
              <div class="glass p-4">
                <h6 class="mb-3"><i class="bi bi-clock me-2"></i>Office Hours</h6>
                <div class="d-flex justify-content-between py-2 border-bottom">
                  <span>Monday - Friday</span>
                  <span class="fw-medium">8:00 AM - 5:00 PM</span>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                  <span>Saturday</span>
                  <span class="fw-medium">8:00 AM - 12:00 PM</span>
                </div>
                <div class="d-flex justify-content-between py-2">
                  <span>Sunday</span>
                  <span class="opacity-75">Closed</span>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="glass p-4">
                <h6 class="mb-3"><i class="bi bi-geo-alt me-2"></i>Location</h6>
                <p class="small mb-2 opacity-75">Barangay Hall, Bucandala 1<br>City of Imus, Cavite</p>
                <a href="https://www.google.com/maps/dir//Barangay+Bucandala+1,+Imus,+Cavite" target="_blank" class="btn btn-glass btn-sm">
                  <i class="bi bi-sign-turn-right me-1"></i>Get Directions
                </a>
              </div>
            </div>
          </div>
        </div>

        <div class="col-lg-5">
          <!-- Contact Info -->
          <div class="glass p-4 mb-4">
            <h5 class="mb-4"><i class="bi bi-building me-2"></i>Barangay Hall</h5>
            <div class="contact-card" style="padding: 16px; display: flex; align-items: flex-start; gap: 14px; margin-bottom: 12px; border-radius: 12px; background: rgba(255,215,0,0.08); border: 1px solid rgba(255,215,0,0.15);">
              <div style="width: 44px; height: 44px; border-radius: 10px; display: flex; align-items: center; justify-content: center; background: rgba(255,215,0,0.2);">
                <i class="bi bi-geo-alt"></i>
              </div>
              <div>
                <h6 class="mb-1">Address</h6>
                <p class="small mb-0 opacity-75">Barangay Bucandala 1, City of Imus, Cavite</p>
              </div>
            </div>
            <div class="contact-card" style="padding: 16px; display: flex; align-items: flex-start; gap: 14px; margin-bottom: 12px; border-radius: 12px; background: rgba(255,215,0,0.08); border: 1px solid rgba(255,215,0,0.15);">
              <div style="width: 44px; height: 44px; border-radius: 10px; display: flex; align-items: center; justify-content: center; background: rgba(255,215,0,0.2);">
                <i class="bi bi-telephone"></i>
              </div>
              <div>
                <h6 class="mb-1">Phone</h6>
                <p class="small mb-0 opacity-75">(046) 123-4567</p>
              </div>
            </div>
            <div class="contact-card" style="padding: 16px; display: flex; align-items: flex-start; gap: 14px; margin-bottom: 12px; border-radius: 12px; background: rgba(255,215,0,0.08); border: 1px solid rgba(255,215,0,0.15);">
              <div style="width: 44px; height: 44px; border-radius: 10px; display: flex; align-items: center; justify-content: center; background: rgba(255,215,0,0.2);">
                <i class="bi bi-envelope"></i>
              </div>
              <div>
                <h6 class="mb-1">Email</h6>
                <p class="small mb-0 opacity-75">info@bucandala1.gov.ph</p>
              </div>
            </div>
          </div>

          <!-- Map -->
          <div class="glass p-4">
            <h5 class="mb-4"><i class="bi bi-map me-2"></i>Map</h5>
            <div class="rounded-4 overflow-hidden" style="height: 200px; background: rgba(255,215,0,0.1);">
              <iframe src="https://www.google.com/maps/embed?pb=!4v1775935144663!6m8!1m7!1s5ELMSmwBEJaGsFpnyHf-OQ!2m2!1d14.40819065824641!2d120.9306048943416!3f296.27!4f0.9500000000000028!5f0.7820865974627469" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="footer-glass">
    <div class="container">
      <div class="row g-4">
        <div class="col-lg-4">
          <div class="d-flex align-items-center gap-3 mb-3">
            <img src="{{ asset('storage/branding/barangay-logo.jpg') }}" alt="Logo" width="50" height="50" class="rounded-circle" onerror="this.src='https://via.placeholder.com/50?text=BRGY'">
            <div>
              <h5 class="mb-0">Barangay Bucandala 1</h5>
              <small class="text-white-50">City of Imus, Cavite</small>
            </div>
          </div>
          <p class="small text-white-50">Your partner in building a safer, more connected community. Serving the residents of Bucandala 1 with dedication and integrity.</p>
        </div>

        <div class="col-lg-2 col-md-4">
          <div class="footer-links">
            <h5>Services</h5>
            <ul>
              <li><a href="{{ route('public.services.documents') }}">Clearance</a></li>
              <li><a href="{{ route('public.services.documents') }}">Certification</a></li>
              <li><a href="#services">Permits</a></li>
              <li><a href="{{ route('public.services.blotter') }}">Blotter</a></li>
            </ul>
          </div>
        </div>

        <div class="col-lg-2 col-md-4">
          <div class="footer-links">
            <h5>Quick Links</h5>
            <ul>
              <li><a href="#home">Home</a></li>
              <li><a href="#news">News</a></li>
              <li><a href="#events">Events</a></li>
              <li><a href="#contact">Contact</a></li>
            </ul>
          </div>
        </div>

        <div class="col-lg-2 col-md-4">
          <div class="footer-links">
            <h5>Account</h5>
            <ul>
              <li><a href="#contact">Login</a></li>
              <li><a href="{{ route('public.residents.register') }}">Register</a></li>
              <li><a href="#faqs">Help</a></li>
            </ul>
          </div>
        </div>

        <div class="col-lg-2">
          <div class="footer-links">
            <h5>Follow Us</h5>
            <div class="d-flex gap-3">
              <a href="#" class="fs-5"><i class="bi bi-facebook"></i></a>
              <a href="#" class="fs-5"><i class="bi bi-twitter-x"></i></a>
              <a href="#" class="fs-5"><i class="bi bi-instagram"></i></a>
              <a href="#" class="fs-5"><i class="bi bi-youtube"></i></a>
            </div>
          </div>
        </div>
      </div>

      <div class="footer-bottom">
        <p>&copy; {{ date('Y') }} Barangay Bucandala 1. All rights reserved. | Developed with love for our community</p>
      </div>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
  <script>
    // Initialize Swiper
    const swiper = new Swiper('.swiper', {
      pagination: {
        el: '.swiper-pagination',
        clickable: true,
      },
      autoplay: {
        delay: 5000,
      },
    });

    // Contact Form Handler
    document.getElementById('homeContactForm').addEventListener('submit', function(e) {
      e.preventDefault();
      
      const fullName = document.getElementById('fullName').value.trim();
      const email = document.getElementById('email').value.trim();
      const subject = document.getElementById('subject').value;
      const message = document.getElementById('message').value.trim();
      
      let isValid = true;
      
      if (!fullName) {
        document.getElementById('fullName').classList.add('is-invalid');
        isValid = false;
      } else {
        document.getElementById('fullName').classList.remove('is-invalid');
      }
      
      if (!email || !email.includes('@')) {
        document.getElementById('email').classList.add('is-invalid');
        isValid = false;
      } else {
        document.getElementById('email').classList.remove('is-invalid');
      }
      
      if (!subject) {
        document.getElementById('subject').classList.add('is-invalid');
        isValid = false;
      } else {
        document.getElementById('subject').classList.remove('is-invalid');
      }
      
      if (!message) {
        document.getElementById('message').classList.add('is-invalid');
        isValid = false;
      } else {
        document.getElementById('message').classList.remove('is-invalid');
      }
      
      if (isValid) {
        alert('Thank you for your message! We will respond within 24-48 hours.');
        this.reset();
      }
    });

    // Smooth scroll
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
      anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
          target.scrollIntoView({
            behavior: 'smooth'
          });
        }
      });
    });

    // Navbar scroll effect
    window.addEventListener('scroll', function() {
      const navbar = document.querySelector('.navbar-glass');
      if (navbar) {
        if (window.scrollY > 50) {
          navbar.style.background = 'linear-gradient(135deg, #1055C9 0%, #0d47a1 100%)';
        } else {
          navbar.style.background = 'rgba(255, 255, 255, 0.15)';
        }
      }
    });

    // Auto-scroll to news section on pagination
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('page_announcements') || urlParams.has('page_events')) {
      const newsSection = document.getElementById('news');
      if (newsSection) {
        setTimeout(() => {
          newsSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }, 100);
      }
    }

    // Calendar functionality
    let calendarDate = new Date();
    const calendarEvents = @json($calendarEvents ?? []);

    function renderCalendar() {
      const year = calendarDate.getFullYear();
      const month = calendarDate.getMonth();
      const today = new Date();
      
      document.getElementById('calendarMonthYear').textContent = 
        new Date(year, month).toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
      
      const firstDay = new Date(year, month, 1).getDay();
      const daysInMonth = new Date(year, month + 1, 0).getDate();
      
      let html = '';
      for (let i = 0; i < firstDay; i++) {
        html += '<div style="height:40px;"></div>';
      }
      
      for (let day = 1; day <= daysInMonth; day++) {
        const dateStr = year + '-' + String(month + 1).padStart(2, '0') + '-' + String(day).padStart(2, '0');
        const eventsOnDay = calendarEvents.filter(e => e.start === dateStr);
        const isToday = today.getDate() === day && today.getMonth() === month && today.getFullYear() === year;
        const hasEvents = eventsOnDay.length > 0;
        
        let bgColor = 'transparent';
        let fontWeight = '500';
        let cursor = 'default';
        let textColor = 'rgba(255,255,255,0.9)';
        
        if (isToday) {
          bgColor = '#FFD700';
          textColor = '#1f2937';
          fontWeight = '700';
        } else if (hasEvents) {
          bgColor = 'rgba(16, 85, 201, 0.4)';
          textColor = '#ffffff';
          cursor = 'pointer';
        }
        
        html += '<div onclick="showDayEvents(\'' + dateStr + '\')" style="height:40px;display:flex;align-items:center;justify-content:center;border-radius:8px;font-size:14px;background:' + bgColor + ';font-weight:' + fontWeight + ';color:' + textColor + ';cursor:' + cursor + ';">' + day + '</div>';
      }
      
      document.getElementById('calendarDays').innerHTML = html;
    }
    
    function showDayEvents(dateStr) {
      var modalEl = document.getElementById('detailModal');
      if (!modalEl) return;
      
      var modal = bootstrap.Modal.getOrCreateInstance(modalEl);
      var eventsOnDay = calendarEvents.filter(function(e) { return e.start === dateStr; });
      if (eventsOnDay.length > 0) {
        var event = eventsOnDay[0];
        modalEl.querySelector('#detailTitle').textContent = event.title;
        modalEl.querySelector('#detailContent').textContent = event.description || event.title;
        modalEl.querySelector('#detailDate').textContent = 'Event Date: ' + new Date(dateStr).toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
        modal.show();
      }
    }

    function changeCalendarMonth(delta) {
      calendarDate.setMonth(calendarDate.getMonth() + delta);
      renderCalendar();
    }

    function switchView(view) {
      document.querySelectorAll('.view-btn').forEach(btn => btn.classList.remove('active'));
      event.target.classList.add('active');
      
      if (view === 'list') {
        document.getElementById('listView').style.display = 'block';
        document.getElementById('calendarView').style.display = 'none';
      } else {
        document.getElementById('listView').style.display = 'none';
        document.getElementById('calendarView').style.display = 'block';
      }
    }

    // Initialize calendar
    if (document.getElementById('calendarContainer')) {
      renderCalendar();
    }
  </script>

  <!-- Detail Modal -->
  <div class="modal" id="detailModal" tabindex="-1" onclick="if(event.target === this) closeDetailModal()">
    <div class="modal-dialog modal-dialog-centered modal-xl">
      <div class="modal-content" style="background: linear-gradient(135deg, #1055C9 0%, #0d47a1 100%); border: 1px solid rgba(255,215,0,0.3);">
        <div class="modal-header" style="border-bottom: 1px solid rgba(255,215,0,0.2);">
          <h5 class="modal-title text-white" id="detailTitle">Details</h5>
          <button type="button" class="btn-close btn-close-white" onclick="closeDetailModal()"></button>
        </div>
        <div class="modal-body text-white">
          <p id="detailContent" style="line-height: 1.7;"></p>
          <small class="text-white-50" id="detailDate"></small>
        </div>
      </div>
    </div>
  </div>

  <script>
    function closeDetailModal() {
      var modal = document.getElementById('detailModal');
      if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
      }
    }
    window.closeDetailModal = closeDetailModal;

    // Show announcement modal
    window.showAnnouncementModal = function(btn) {
      var modal = document.getElementById('detailModal');
      modal.querySelector('#detailTitle').textContent = btn.getAttribute('data-title') || '';
      modal.querySelector('#detailContent').textContent = btn.getAttribute('data-content') || '';
      modal.querySelector('#detailDate').textContent = 'Posted on: ' + (btn.getAttribute('data-date') || '');
      modal.style.display = 'flex';
      document.body.style.overflow = 'hidden';
    };

    // Show calendar event modal
    window.showDayEvents = function(dateStr) {
      var eventsOnDay = calendarEvents.filter(function(e) { return e.start === dateStr; });
      if (eventsOnDay.length > 0) {
        var event = eventsOnDay[0];
        var modal = document.getElementById('detailModal');
        modal.querySelector('#detailTitle').textContent = event.title;
        modal.querySelector('#detailContent').textContent = event.description || event.title;
        modal.querySelector('#detailDate').textContent = 'Event Date: ' + new Date(dateStr).toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
      }
    };
  </script>
</body>
</html>
