<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login</title>
  @vite(['resources/css/app.css','resources/js/app.js'])
  <style>
    body{font-family:system-ui,sans-serif;display:grid;place-items:center;min-height:100vh;margin:0;background:#f6f7f9}
    .card{max-width:380px;width:100%;background:#fff;border:1px solid #e5e7eb;border-radius:14px;padding:22px}
    .row{display:flex;flex-direction:column;gap:6px;margin-bottom:12px}
    .btn{padding:10px 14px;border-radius:10px;border:1px solid #e5e7eb;background:#111827;color:#fff;width:100%}
    .muted{color:#6b7280;font-size:13px}
    input{padding:10px;border-radius:10px;border:1px solid #e5e7eb}
    .err{color:#ef4444;font-size:13px}
  </style>
</head>
<body>
  <form class="card" method="POST" action="{{ route('login.attempt') }}">
    @csrf
    <h2 style="margin:0 0 6px">Masuk</h2>
    <p class="muted" style="margin:0 0 16px">Gunakan akun yang terdaftar.</p>

    @if ($errors->any())
      <div class="err" style="margin-bottom:10px">
        {{ $errors->first() }}
      </div>
    @endif

    <div class="row">
      <label for="email">Email</label>
      <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus>
    </div>

    <div class="row">
      <label for="password">Password</label>
      <input id="password" name="password" type="password" required>
    </div>

    <div class="row" style="flex-direction:row;align-items:center;gap:10px">
      <input id="remember" name="remember" type="checkbox" value="1" style="width:16px;height:16px">
      <label for="remember" class="muted">Ingat saya</label>
    </div>

    <button class="btn" type="submit">Login</button>
  </form>
</body>
</html>
