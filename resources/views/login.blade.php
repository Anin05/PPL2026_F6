<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Masuk - ANAHEIM ERP</title>
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
  <style>
    body { display:flex; min-height:100vh; align-items:center; justify-content:center; background:#f4f6fb; }
    .login { width:360px; background:#fff; padding:28px; border-radius:12px; box-shadow:0 2px 12px rgba(0,0,0,.08); }
    .login h1 { font-size:20px; margin:0 0 4px; }
    .login p { margin:0 0 18px; color:#6b7280; font-size:13px; }
    .login label { display:block; font-size:13px; margin:12px 0 4px; }
    .login input { width:100%; padding:10px; border:1px solid #d1d5db; border-radius:8px; box-sizing:border-box; }
    .login button { width:100%; margin-top:18px; }
    #pesan { color:#b91c1c; font-size:13px; margin-top:12px; min-height:18px; }
  </style>
</head>
<body>
  <form class="login" id="form-login">
    <h1>Masuk</h1>
    <p>ANAHEIM ERP · F-6 CRM Klien &amp; Kontak</p>
    <label>Email atau username</label>
    <input name="emailOrUsername" autocomplete="username" required>
    <label>Password</label>
    <input name="password" type="password" autocomplete="current-password" required>
    <button class="btn btn-primary" type="submit">Masuk</button>
    <div id="pesan"></div>
  </form>

  <script src="{{ asset('js/api.js') }}"></script>
  <script>
    document.getElementById('form-login').addEventListener('submit', async (e) => {
      e.preventDefault();
      const btn = e.target.querySelector('button');
      const pesan = document.getElementById('pesan');
      pesan.textContent = '';
      btn.disabled = true;
      btn.textContent = 'Memeriksa...';
      const f = Object.fromEntries(new FormData(e.target));
      try {
        const res = await api('POST', '/session/login', f);
        const token = res.accessToken;
        if (!token) throw { error: { message: 'Respons login tidak berisi accessToken.' } };
        const boleh = ['CEO', 'COO', 'ADMIN', 'AM'];
        if (res.account && !boleh.includes(res.account.roleCode)) {
          throw { error: { message: 'Peran Anda tidak memiliki akses ke modul CRM Klien.' } };
        }
        sessionStorage.setItem('token', token);
        if (res.account) sessionStorage.setItem('account', JSON.stringify(res.account));
        location.href = '/klien';
      } catch (err) {
        pesan.textContent = (err.error && err.error.message)
  || ('Login gagal (status ' + (err.status || 'tidak ada respons') + ').');
        btn.textContent = 'Masuk';
      }
    });
  </script>
</body>
</html>