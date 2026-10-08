<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Forgot Password - Barangay Bucandala 1 MIS</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <style>
    body {
      min-height: 100vh;
      display: grid;
      place-items: center;
      padding: 24px;
      background: linear-gradient(135deg, #1055C9 0%, #0d47a1 55%, #3b82f6 100%);
      font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
    }
    .reset-card {
      width: 100%;
      max-width: 480px;
      overflow: hidden;
      border: 0;
      border-radius: 20px;
      box-shadow: 0 25px 50px -12px rgba(0, 0, 0, .3);
    }
    .reset-header {
      padding: 28px;
      color: white;
      text-align: center;
      background: linear-gradient(135deg, #1055C9, #0d47a1);
    }
    .reset-body { padding: 30px; }
    .form-control { padding: 12px 14px; border-radius: 10px; }
    .btn-primary { padding: 12px; border-radius: 10px; background: #1055C9; }
    .step[hidden] { display: none; }
  </style>
</head>
<body>
  <main class="card reset-card">
    <div class="reset-header">
      <i class="bi bi-shield-lock fs-1" aria-hidden="true"></i>
      <h1 class="h4 mt-2 mb-1">Reset your password</h1>
      <p class="mb-0 text-white-50">Barangay Bucandala 1 Management Information System</p>
    </div>
    <div class="reset-body">
      <div id="alertBox" role="status" aria-live="polite"></div>

      <form id="emailStep" class="step">
        <p class="text-secondary">Enter the email address associated with your account. We will send you a verification code.</p>
        <label for="email" class="form-label fw-semibold">Email address</label>
        <input id="email" type="email" class="form-control mb-3" autocomplete="email" required maxlength="255">
        <button type="submit" class="btn btn-primary w-100">Send reset code</button>
      </form>

      <form id="otpStep" class="step" hidden>
        <p class="text-secondary">Enter the six-digit code sent to your email address.</p>
        <label for="otp" class="form-label fw-semibold">Verification code</label>
        <input id="otp" type="text" class="form-control mb-3" inputmode="numeric" autocomplete="one-time-code" pattern="[0-9]{6}" maxlength="6" required>
        <button type="submit" class="btn btn-primary w-100">Verify code</button>
      </form>

      <form id="passwordStep" class="step" hidden>
        <p class="text-secondary">Choose a new password with at least 8 characters, including uppercase and lowercase letters, a number, and a symbol.</p>
        <label for="password" class="form-label fw-semibold">New password</label>
        <input id="password" type="password" class="form-control mb-3" autocomplete="new-password" minlength="8" pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[^A-Za-z0-9]).{8,}" title="Use at least 8 characters with uppercase and lowercase letters, a number, and a symbol." required>
        <label for="passwordConfirmation" class="form-label fw-semibold">Confirm new password</label>
        <input id="passwordConfirmation" type="password" class="form-control mb-3" autocomplete="new-password" required>
        <button type="submit" class="btn btn-primary w-100">Reset password</button>
      </form>

      <div class="text-center mt-4">
        <a href="{{ route('login') }}" class="text-decoration-none"><i class="bi bi-arrow-left"></i> Back to sign in</a>
      </div>
    </div>
  </main>

  <script>
    const email = document.getElementById('email');
    const alertBox = document.getElementById('alertBox');
    let verificationToken = null;

    function showAlert(message, type = 'danger') {
      const alert = document.createElement('div');
      alert.className = `alert alert-${type}`;
      alert.textContent = message;
      alertBox.replaceChildren(alert);
    }

    async function postJson(url, payload) {
      const response = await fetch(url, {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(payload)
      });
      const data = await response.json().catch(() => ({}));
      if (!response.ok) {
        const validationMessage = data.errors ? Object.values(data.errors).flat()[0] : null;
        throw new Error(validationMessage || data.message || 'Unable to complete the request. Please try again.');
      }
      return data;
    }

    document.getElementById('emailStep').addEventListener('submit', async (event) => {
      event.preventDefault();
      const button = event.currentTarget.querySelector('button');
      button.disabled = true;
      try {
        const data = await postJson('/api/v1/auth/password/forgot', { email: email.value });
        document.getElementById('emailStep').hidden = true;
        document.getElementById('otpStep').hidden = false;
        showAlert(data.message, 'info');
        document.getElementById('otp').focus();
      } catch (error) {
        showAlert(error.message);
      } finally {
        button.disabled = false;
      }
    });

    document.getElementById('otpStep').addEventListener('submit', async (event) => {
      event.preventDefault();
      const button = event.currentTarget.querySelector('button');
      button.disabled = true;
      try {
        const data = await postJson('/api/v1/auth/password/verify', {
          email: email.value,
          otp: document.getElementById('otp').value
        });
        verificationToken = data.verification_token;
        document.getElementById('otpStep').hidden = true;
        document.getElementById('passwordStep').hidden = false;
        alertBox.replaceChildren();
        document.getElementById('password').focus();
      } catch (error) {
        showAlert(error.message);
      } finally {
        button.disabled = false;
      }
    });

    document.getElementById('passwordStep').addEventListener('submit', async (event) => {
      event.preventDefault();
      const button = event.currentTarget.querySelector('button');
      button.disabled = true;
      try {
        await postJson('/api/v1/auth/password/reset', {
          email: email.value,
          verification_token: verificationToken,
          password: document.getElementById('password').value,
          password_confirmation: document.getElementById('passwordConfirmation').value
        });
        window.location.href = '/auth/login?password_reset=true';
      } catch (error) {
        showAlert(error.message);
      } finally {
        button.disabled = false;
      }
    });
  </script>
</body>
</html>
