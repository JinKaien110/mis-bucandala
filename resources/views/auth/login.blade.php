<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Login - Barangay Bucandala 1 MIS</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <style>
    :root {
      --mis-blue: #1055C9;
      --mis-blue-light: #3b82f6;
      --mis-blue-dark: #0d47a1;
      --mis-yellow: #FEEE91;
    }
    
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    
    body {
      font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
      min-height: 100vh;
      background: linear-gradient(135deg, #1055C9 0%, #0d47a1 50%, #3b82f6 100%);
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
    }
    
    /* Navbar styles matching home page */
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
      text-decoration: none;
    }
    .navbar-glass .nav-link:hover {
      background: rgba(255, 215, 0, 0.15);
      color: #ffffff !important;
    }
    .navbar-glass .dropdown-menu {
      background: rgba(16, 85, 201, 0.98);
      backdrop-filter: blur(20px);
      border: 1px solid rgba(255, 215, 0, 0.3);
      border-radius: 12px;
      padding: 8px;
    }
    .navbar-glass .btn-outline-light {
      border-color: #ffffff;
      color: #ffffff;
      padding: 6px 14px;
      font-size: 0.875rem;
      border-radius: 10px;
      text-decoration: none;
    }
    .navbar-glass .btn-outline-light:hover {
      background: rgba(255, 255, 255, 0.2);
      color: #ffffff;
    }
    .navbar-glass .btn-warning {
      background: #FFD700;
      color: #1f2937;
      border: none;
      padding: 6px 14px;
      font-size: 0.875rem;
      font-weight: 600;
      border-radius: 10px;
      text-decoration: none;
    }
    .navbar-glass .btn-warning:hover {
      background: #FFEB3B;
    }
    
    .main-content {
      flex: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      width: 100%;
      padding: 80px 20px 20px;
    }
    
    /* Animated background with floating orbs */
    .bg-animation {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      overflow: hidden;
      z-index: 0;
    }
    
    .orb {
      position: absolute;
      border-radius: 50%;
      filter: blur(80px);
      animation: float 20s ease-in-out infinite;
    }
    
    .orb-1 {
      width: 400px;
      height: 400px;
      background: rgba(16, 85, 201, 0.4);
      top: -100px;
      left: -100px;
      animation-delay: 0s;
    }
    
    .orb-2 {
      width: 500px;
      height: 500px;
      background: rgba(254, 238, 145, 0.3);
      bottom: -150px;
      right: -150px;
      animation-delay: -7s;
    }
    
    .orb-3 {
      width: 300px;
      height: 300px;
      background: rgba(16, 85, 201, 0.25);
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      animation-delay: -14s;
    }
    
    .orb-4 {
      width: 250px;
      height: 250px;
      background: rgba(254, 238, 145, 0.2);
      top: 20%;
      right: 10%;
      animation-delay: -5s;
    }
    
    @keyframes float {
      0%, 100% { 
        transform: translate(0, 0) scale(1) rotate(0deg); 
      }
      25% { 
        transform: translate(30px, -30px) scale(1.05) rotate(5deg); 
      }
      50% { 
        transform: translate(-20px, 20px) scale(0.95) rotate(-5deg); 
      }
      75% { 
        transform: translate(20px, 30px) scale(1.02) rotate(3deg); 
      }
    }
    
    @keyframes pulse {
      0%, 100% { opacity: 0.4; transform: scale(1); }
      50% { opacity: 0.6; transform: scale(1.1); }
    }
    
    .login-container {
      position: relative;
      z-index: 1;
      width: 100%;
      max-width: 440px;
    }
    
    .login-card {
      background: rgba(255, 255, 255, 0.95);
      border-radius: 24px;
      box-shadow: 
        0 25px 50px -12px rgba(0, 0, 0, 0.25),
        0 0 0 1px rgba(255, 255, 255, 0.1);
      overflow: hidden;
      backdrop-filter: blur(20px);
    }
    
    .login-header {
      background: linear-gradient(135deg, var(--mis-blue) 0%, var(--mis-blue-dark) 100%);
      padding: 32px 32px 40px;
      text-align: center;
      position: relative;
      overflow: hidden;
    }
    
    .login-header::before {
      content: '';
      position: absolute;
      top: -50%;
      left: -50%;
      width: 200%;
      height: 200%;
      background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 60%);
      animation: shimmer 3s ease-in-out infinite;
    }
    
    @keyframes shimmer {
      0%, 100% { transform: translate(-30%, -30%); }
      50% { transform: translate(30%, 30%); }
    }
    
    .login-header img {
      width: 80px;
      height: 80px;
      border-radius: 20px;
      object-fit: cover;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
      border: 3px solid rgba(255, 255, 255, 0.3);
      margin-bottom: 16px;
    }
    
    .login-header h1 {
      color: white;
      font-size: 1.5rem;
      font-weight: 700;
      margin-bottom: 4px;
      position: relative;
    }
    
    .login-header p {
      color: rgba(255, 255, 255, 0.8);
      font-size: 0.9rem;
      position: relative;
    }
    
    .login-body {
      padding: 32px;
    }
    
    .form-group {
      margin-bottom: 20px;
    }
    
    .form-group label {
      display: block;
      font-weight: 600;
      color: #374151;
      margin-bottom: 8px;
      font-size: 0.9rem;
    }
    
    .input-group {
      position: relative;
    }
    
    .input-group-text {
      background: #f3f4f6;
      border: 2px solid #e5e7eb;
      border-right: none;
      border-radius: 12px 0 0 12px;
      color: #6b7280;
      padding: 12px 16px;
    }
    
    .form-control {
      border: 2px solid #e5e7eb;
      border-left: none;
      border-radius: 0 12px 12px 0;
      padding: 12px 16px;
      font-size: 1rem;
      transition: all 0.3s ease;
    }
    
    .form-control:focus {
      border-color: var(--mis-blue);
      box-shadow: 0 0 0 4px rgba(16, 85, 201, 0.1);
    }
    
    .btn-login {
      width: 100%;
      padding: 14px 24px;
      background: linear-gradient(135deg, var(--mis-blue) 0%, var(--mis-blue-light) 100%);
      border: none;
      border-radius: 12px;
      color: white;
      font-size: 1rem;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      box-shadow: 0 4px 14px rgba(16, 85, 201, 0.4);
    }
    
    .btn-login:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(16, 85, 201, 0.5);
    }
    
    .btn-login:active {
      transform: translateY(0);
    }
    
    .btn-login:disabled {
      opacity: 0.7;
      cursor: not-allowed;
      transform: none;
    }
    
    .alert {
      border-radius: 12px;
      padding: 12px 16px;
      margin-bottom: 20px;
      font-size: 0.9rem;
    }
    
    .alert-danger {
      background: #fef2f2;
      border: 1px solid #fecaca;
      color: #dc2626;
    }
    
    .alert-success {
      background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
      border: 1px solid #22c55e;
      color: #15803d;
      border-radius: 12px;
      padding: 16px 20px;
      font-weight: 500;
      display: flex;
      align-items: center;
    }
    .alert-success i {
      font-size: 1.25rem;
      margin-right: 8px;
    }
    
    .output-box {
      margin-top: 20px;
      background: #1f2937;
      border-radius: 12px;
      padding: 16px;
      display: none;
    }
    
    .output-box.show {
      display: block;
    }
    
    .output-box pre {
      color: #10b981;
      font-size: 0.85rem;
      margin: 0;
      white-space: pre-wrap;
      word-break: break-all;
    }
    
    .divider {
      display: flex;
      align-items: center;
      margin: 24px 0;
    }
    
    .divider::before,
    .divider::after {
      content: '';
      flex: 1;
      height: 1px;
      background: #e5e7eb;
    }
    
    .divider span {
      padding: 0 16px;
      color: #6b7280;
      font-size: 0.85rem;
    }
    
    .footer-links {
      text-align: center;
      margin-top: 20px;
    }
    
    .footer-links a {
      color: var(--mis-blue);
      text-decoration: none;
      font-size: 0.9rem;
      transition: all 0.3s ease;
    }
    
    .footer-links a:hover {
      color: var(--mis-blue-dark);
      text-decoration: underline;
    }
    
    /* Login button enhanced */
    .btn-login {
      position: relative;
      overflow: hidden;
    }
    
    .btn-login::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
      transition: left 0.5s ease;
    }
    
    .btn-login:hover::before {
      left: 100%;
    }
    
    .loading-spinner {
      display: inline-block;
      width: 20px;
      height: 20px;
      border: 3px solid rgba(255,255,255,0.3);
      border-radius: 50%;
      border-top-color: white;
      animation: spin 1s ease-in-out infinite;
      margin-right: 8px;
    }
    
    @keyframes spin {
      to { transform: rotate(360deg); }
    }
  </style>
</head>
<body>
  <div class="bg-animation">
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>
    <div class="orb orb-4"></div>
  </div>
  
  <!-- Navbar -->
  @include('components.navigation-bar', ['currentRoute' => 'login'])

  <div class="login-container" style="margin-top: 100px;">
    <div class="login-card">
      <div class="login-header">
        <img src="{{ asset('storage/branding/barangay-logo.jpg') }}" alt="Barangay Logo" onerror="this.src='https://via.placeholder.com/80?text=BRGY'">
        <h1>Barangay Bucandala 1</h1>
        <p>Management Information System</p>
      </div>
      
      <div class="login-body">
        <div id="alertBox"></div>
        
        <form id="loginForm">
          <div class="form-group">
            <label>Email Address</label>
            <div class="input-group">
              <span class="input-group-text"><i class="bi bi-envelope"></i></span>
              <input type="email" class="form-control" id="email" value="admin@barangay.test" required placeholder="Enter your email">
            </div>
          </div>
          
          <div class="form-group">
            <label>Password</label>
            <div class="input-group">
              <span class="input-group-text"><i class="bi bi-lock"></i></span>
              <input type="password" class="form-control" id="password" value="password123" required placeholder="Enter your password">
            </div>
          </div>
          
          <button type="submit" class="btn-login" id="btnLogin">
            <span id="btnText">Sign In</span>
          </button>
        </form>
        
        <div class="footer-links">
          <a href="{{ route('public.residents.register') }}"><i class="bi bi-person-plus"></i> Resident Registration</a>
        </div>
      </div>
    </div>
  </div>

  <script>
    const email = document.getElementById('email');
    const password = document.getElementById('password');
    const btnLogin = document.getElementById('btnLogin');
    const alertBox = document.getElementById('alertBox');
    const btnText = document.getElementById('btnText');
    
    function showAlert(message, type = 'danger') {
      alertBox.innerHTML = `<div class="alert alert-${type}">${message}</div>`;
      setTimeout(() => alertBox.innerHTML = '', 5000);
    }
    
    // Check for registration success parameter
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('registered') === 'true') {
      showAlert('<i class="bi bi-check-circle-fill me-2"></i>Registration successful! Please log in with your credentials.', 'success');
      // Clean the URL
      window.history.replaceState({}, document.title, '/auth/login');
    }
    
    document.getElementById('loginForm').addEventListener('submit', async (e) => {
      e.preventDefault();
      
      // Debug: Log email and password
      console.log('=== Login Debug ===');
      console.log('Email:', email.value);
      console.log('Password:', password.value ? 'Password provided (length: ' + password.value.length + ')' : 'No password');
      
      btnLogin.disabled = true;
      btnText.innerHTML = '<span class="loading-spinner"></span>Signing in...';
      
      try {
        const res = await fetch('/api/v1/auth/login', {
          method: 'POST',
          credentials: 'same-origin',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
          },
          body: JSON.stringify({
            email: email.value,
            password: password.value
          })
        });
        
        const newToken = res.headers.get('x-csrf-token');
        if (newToken) {
          document.querySelector('meta[name="csrf-token"]').setAttribute('content', newToken);
        }
        
        const data = await res.json().catch(() => ({}));
        
        if (res.ok) {
          const userRole = data.user?.role || data.role;
          if (userRole === 'admin' || userRole === 'staff') {
            showAlert('Login successful! Redirecting to admin...', 'success');
            setTimeout(() => window.location.href = '/admin/analytics', 1000);
          } else {
            showAlert('Login successful! Redirecting...', 'success');
            setTimeout(() => window.location.href = '/resident/dashboard', 1000);
          }
} else {
            showAlert(data.message || 'Login failed. Please check your credentials.', 'danger');
          }
        } catch (e) {
          showAlert('An error occurred. Please try again.', 'danger');
        } finally {
          btnLogin.disabled = false;
          btnText.textContent = 'Sign In';
        }
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
  </script>
</body>
</html>
