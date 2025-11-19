<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login | PT. PID</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    :root {
      --pid-primary: #2563eb;
      --pid-primary-dark: #1e40af;
      --pid-light: #f9fafb;
    }

    * {
      box-sizing: border-box;
    }

    body {
      font-family: 'Segoe UI', system-ui, -apple-system, BlinkMacSystemFont, sans-serif;
      background: url('{{ asset('images/backgroundlogin.jpg') }}') no-repeat center center/cover;
      min-height: 100vh;
      margin: 0;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 1.5rem 1rem; /* HP: agak rapat */
      color: #fff;
      position: relative;
    }

    .overlay {
      position: fixed;
      inset: 0;
      background: linear-gradient(to bottom right, rgba(15,23,42,0.9), rgba(37,99,235,0.7));
      mix-blend-mode: multiply;
      z-index: 0;
    }

    .login-wrapper {
      position: relative;
      z-index: 1;
      width: 100%;
      max-width: 420px; /* default HP & small tablet */
    }

    .app-title {
      text-align: center;
      margin-bottom: 1.5rem;
    }

    .app-title h1 {
      font-weight: 700;
      letter-spacing: 0.08em;
      font-size: 1.5rem;
      margin-bottom: 0.25rem;
    }

    .app-title h6 {
      font-weight: 400;
      font-size: 0.85rem;
      letter-spacing: 0.15em;
      text-transform: uppercase;
      opacity: 0.9;
    }

    .login-card {
      background: rgba(15,23,42,0.7);
      backdrop-filter: blur(18px);
      border-radius: 18px;
      padding: 1.5rem 1.25rem;
      border: 1px solid rgba(148,163,184,0.35);
      box-shadow: 0 18px 45px rgba(15,23,42,0.7);
    }

    .login-header {
      margin-bottom: 1.5rem;
    }

    .login-header h2 {
      font-size: 1.2rem;
      font-weight: 600;
      margin-bottom: 0.25rem;
    }

    .login-header p {
      font-size: 0.85rem;
      color: #e5e7eb;
      margin: 0;
      opacity: 0.9;
    }

    .form-label {
      font-size: 0.85rem;
      color: #e5e7eb;
      margin-bottom: 0.35rem;
    }

    .form-control {
      border-radius: 12px;
      border: 1px solid rgba(148,163,184,0.6);
      background: rgba(15,23,42,0.85);
      color: #f9fafb;
      padding: 0.6rem 0.85rem;
      font-size: 0.9rem;
    }

    .form-control:focus {
      border-color: var(--pid-primary);
      box-shadow: 0 0 0 1px rgba(37,99,235,0.45);
      background: rgba(15,23,42,0.95);
      color: #f9fafb;
    }

    .form-control::placeholder {
      color: #9ca3af;
      font-size: 0.85rem;
    }

    .btn-login {
      width: 100%;
      border-radius: 999px;
      background: linear-gradient(to right, var(--pid-primary), #38bdf8);
      border: none;
      font-weight: 600;
      font-size: 0.95rem;
      padding: 0.65rem 1rem;
      margin-top: 0.35rem;
      transition: transform 0.15s ease, box-shadow 0.15s ease, opacity 0.15s ease;
      box-shadow: 0 12px 30px rgba(37,99,235,0.55);
    }

    .btn-login:hover {
      opacity: 0.95;
      transform: translateY(-1px);
      box-shadow: 0 14px 36px rgba(37,99,235,0.6);
      background: linear-gradient(to right, var(--pid-primary-dark), #0ea5e9);
    }

    .btn-login:active {
      transform: translateY(0);
      box-shadow: 0 8px 18px rgba(15,23,42,0.8);
    }

    .remember-label {
      color: #e5e7eb;
      font-size: 0.78rem;
      cursor: pointer;
      user-select: none;
    }

    .form-check-input {
      cursor: pointer;
      width: 0.95rem;
      height: 0.95rem;
    }

    .helper-row {
      font-size: 0.78rem;
      color: #cbd5f5;
    }

    .alert {
      font-size: 0.78rem;
      border-radius: 10px;
    }

    .footer-text {
      margin-top: 0.85rem;
      text-align: center;
      font-size: 0.75rem;
      color: #cbd5f5;
      opacity: 0.85;
    }

    .footer-text span {
      opacity: 1;
      font-weight: 500;
    }

    /* =========================
       RESPONSIVE: HP & TABLET
       ========================= */

    /* HP gede / small tablet (>= 576px) */
    @media (min-width: 576px) {
      body {
        padding: 2rem 1.5rem;
      }
      .login-wrapper {
        max-width: 440px;
      }
      .login-card {
        padding: 1.75rem 1.6rem;
      }
      .login-header h2 {
        font-size: 1.3rem;
      }
    }

    /* Tablet portrait (>= 768px) */
    @media (min-width: 768px) {
      body {
        padding: 2.5rem 2rem;
      }
      .login-wrapper {
        max-width: 480px;
      }
      .login-card {
        padding: 2rem 1.9rem;
      }
      .app-title h1 {
        font-size: 1.8rem;
      }
      .app-title h6 {
        font-size: 0.9rem;
      }
      .login-header h2 {
        font-size: 1.35rem;
      }
      .form-control {
        font-size: 0.95rem;
        padding: 0.7rem 0.9rem;
      }
    }

    /* Tablet landscape / small laptop (>= 992px) */
    @media (min-width: 992px) {
      body {
        padding: 3rem 2.5rem;
      }
      .login-wrapper {
        max-width: 520px;
      }
      .login-card {
        padding: 2.25rem 2.1rem;
      }
    }
  </style>
</head>
<body>
  <div class="overlay"></div>

  <div class="login-wrapper">
    <!-- Title di atas card -->
    <div class="app-title">
      <h1>PT. PID</h1>
      <h6>Serah Terima Dokumen</h6>
    </div>

    <!-- Card Login -->
    <form method="POST" action="{{ route('login.attempt') }}" class="login-card">
      @csrf

      
      @if ($errors->any())
        <div class="alert alert-danger py-2 small mb-3">
          {{ $errors->first() }}
        </div>
      @endif

      {{-- USERNAME / EMAIL --}}
      <div class="mb-3">
        <label for="login" class="form-label">Email atau Username</label>
        <input
          type="text"
          class="form-control"
          id="login"
          name="login"
          value="{{ old('login') }}"
          required
          autofocus
          autocomplete="username">
      </div>

      {{-- PASSWORD --}}
      <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input
          type="password"
          class="form-control"
          id="password"
          name="password"
          required
          autocomplete="current-password">
      </div>

      {{-- REMEMBER + LUPA PASSWORD (opsional link) --}}
      <div class="d-flex justify-content-between align-items-center mb-2">
        <div class="form-check">
          <input
            class="form-check-input"
            type="checkbox"
            value="1"
            id="remember"
            name="remember"
            {{ old('remember') ? 'checked' : '' }}>
          <label class="form-check-label remember-label" for="remember">
            Ingat saya
          </label>
        </div>

        {{-- Kalau nanti ada route lupa password tinggal aktifkan --}}
        {{-- <a href="{{ route('password.request') }}" class="helper-row text-decoration-none">
          Lupa password?
        </a> --}}
      </div>

      <button type="submit" class="btn btn-login">
        MASUK
      </button>

      <div class="footer-text mt-3">
        <span>PT. PID</span> &mdash; Sistem Serah Terima Dokumen
      </div>
    </form>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
