<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login | PT. PID</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: url('{{ asset('images/backgroundlogin.jpg') }}') no-repeat center center/cover;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-direction: column;
      color: #fff;
    }

    .overlay {
      position: fixed;
      inset: 0;
      background: rgba(0,0,0,0.35);
      backdrop-filter: blur(2px);
      z-index: 0;
    }

    .login-container {
      position: relative;
      z-index: 1;
      width: 100%;
      max-width: 400px;
      background: rgba(255, 255, 255, 0.2);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.3);
      border-radius: 15px;
      padding: 2rem;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
    }

    .login-container h1 {
      font-weight: 700;
      letter-spacing: 1px;
      font-size: 2rem;
    }

    .login-container h5 {
      font-weight: 400;
      margin-bottom: 1.5rem;
      color: #f1f5f9;
    }

    .form-control {
      border-radius: 10px;
      border: 1px solid rgba(255, 255, 255, 0.4);
      background: rgba(255, 255, 255, 0.2);
      color: #fff;
    }

    .form-control::placeholder {
      color: #e2e8f0;
    }

    .btn-login {
      width: 100%;
      border-radius: 10px;
      background-color: #2563eb;
      border: none;
      font-weight: 600;
      transition: 0.3s;
    }

    .btn-login:hover {
      background-color: #1e40af;
    }

    .forgot {
      text-align: center;
      margin-top: 0.8rem;
      font-size: 0.9rem;
    }

    .forgot a {
      color: #e0e7ff;
      text-decoration: none;
    }

    .forgot a:hover {
      text-decoration: underline;
    }

    @media (max-width: 768px) {
      .login-container {
        margin: 0 1rem;
        padding: 1.5rem;
      }
      .login-container h1 {
        font-size: 1.7rem;
      }
    }
  </style>
</head>
<body>
  <div class="overlay"></div>

  <div class="text-center mb-4" style="z-index:1;">
    <h1 class="fw-bold">PT. PID</h1>
    <h5>SERAH TERIMA DOKUMEN</h5>
  </div>

  <form method="POST" action="{{ route('login.attempt') }}" class="login-container">
    @csrf

    @if ($errors->any())
      <div class="alert alert-danger py-2 small mb-3">
        {{ $errors->first() }}
      </div>
    @endif

    <div class="mb-3">
      <label for="username" class="form-label">Username</label>
      <input type="text" class="form-control" id="username" name="login" value="{{ old('email') || old('username') }}" required autofocus placeholder="Masukkan Email atau Username">
      

    </div>

    <div class="mb-3">
      <label for="password" class="form-label">Password</label>
      <input type="password" class="form-control" id="password" name="password" required placeholder="Masukkan Password">
    </div>

    <button type="submit" class="btn btn-login py-2">LOGIN</button>

    <div class="forgot">
      <a href="#">Forgot password?</a>
    </div>
  </form>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
