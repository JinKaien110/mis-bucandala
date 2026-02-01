<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>Auth Test</title>
</head>
<body style="font-family: Arial; max-width: 520px; margin: 40px auto;">
  <h2>Auth Test (Sanctum Cookie)</h2>

  <div style="margin-bottom: 16px;">
    <label>Email</label><br />
    <input id="email" type="email" value="admin@barangay.test" style="width:100%; padding:8px;" />
  </div>

  <div style="margin-bottom: 16px;">
    <label>Password</label><br />
    <input id="password" type="password" value="password123" style="width:100%; padding:8px;" />
  </div>

  <button id="btnLogin" style="padding:10px 14px;">Login</button>
  <button id="btnMe" style="padding:10px 14px;">Me</button>
  <button id="btnLogout" style="padding:10px 14px;">Logout</button>

  <pre id="out" style="margin-top:20px; background:#f5f5f5; padding:12px; overflow:auto;"></pre>

  <script>
    const out = document.getElementById('out');
    const email = document.getElementById('email');
    const password = document.getElementById('password');

    function show(data) {
      out.textContent = typeof data === 'string' ? data : JSON.stringify(data, null, 2);
    }


    document.getElementById('btnLogin').addEventListener('click', async () => {
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
        document
            .querySelector('meta[name="csrf-token"]')
            .setAttribute('content', newToken);
        }

        const data = await res.json().catch(() => ({}));
        show({ status: res.status, data });

      } catch (e) {
        show(String(e));
      }
    });

    document.getElementById('btnMe').addEventListener('click', async () => {
      try {
        const res = await fetch('/api/v1/auth/me', {
          method: 'GET',
          credentials: 'same-origin',
          headers: { 'Accept': 'application/json' }
        });

        const data = await res.json().catch(() => ({}));
        show({ status: res.status, data });
      } catch (e) {
        show(String(e));
      }
    });

    document.getElementById('btnLogout').addEventListener('click', async () => {
      try {
        
        const res = await fetch('/api/v1/auth/logout', {
          method: 'POST',
          credentials: 'same-origin',
          headers: { 'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content

           }
        });
        const newToken = res.headers.get('x-csrf-token');

if (newToken) {
  document
    .querySelector('meta[name="csrf-token"]')
    .setAttribute('content', newToken);
}


        const data = await res.json().catch(() => ({}));
        show({ status: res.status, data });
      } catch (e) {
        show(String(e));
      }
    });
  </script>
</body>
</html>
