<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SerahTerimaDokumen</title>

  {{-- Vendor --}}
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/tabler-icons@3.6.0/iconfont/tabler-icons.min.css" rel="stylesheet">

  {{-- Custom CSS --}}
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
  
  {{-- ===== SIDEBAR (desktop) ===== --}}
  <aside class="sidebar" id="appSidebar">
    {{-- Brand desktop --}}
    <div class="brand brand-desktop">
      <span class="brand-icon">📁</span>
      <span class="brand-text">Serah Terima</span>
    </div>

    <nav class="nav flex-column gap-1">
      <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
         href="{{ route('dashboard') }}">Dashboard</a>

      <a class="nav-link {{ request()->routeIs('documents.*') ? 'active' : '' }}"
         href="{{ route('documents.index') }}">Data Dokumen</a>

      <a class="nav-link disabled">Input Dokumen</a>
      <a class="nav-link disabled">Laporan</a>
      <a class="nav-link disabled">Pengguna</a>
      <a class="nav-link disabled">Logout</a>
    </nav>
  </aside>

  {{-- ===== MOBILE NAV (GLPI-like full sheet) ===== --}}
  <div class="mobile-nav" id="mobileNav" aria-hidden="true">
    <div class="mobile-nav-header">
      <button class="mobile-close" type="button" data-close-mobile-nav aria-label="Close">
        <i class="ti ti-x"></i>
      </button>
      <div class="mobile-brand">
        <span class="brand-icon">📁</span>
        <strong>Serah Terima</strong>
      </div>
    </div>

    <nav class="mobile-menu">
      <a href="{{ route('dashboard') }}" class="mobile-link {{ request()->routeIs('dashboard')?'active':'' }}">
        <i class="ti ti-home"></i> Dashboard
      </a>
      <a href="{{ route('documents.index') }}" class="mobile-link {{ request()->routeIs('documents.*')?'active':'' }}">
        <i class="ti ti-file-description"></i> Data Dokumen
      </a>
      <a class="mobile-link disabled"><i class="ti ti-file-plus"></i> Input Dokumen</a>
      <a class="mobile-link disabled"><i class="ti ti-report"></i> Laporan</a>
      <a class="mobile-link disabled"><i class="ti ti-users"></i> Pengguna</a>
      <a class="mobile-link disabled"><i class="ti ti-logout"></i> Logout</a>
    </nav>
  </div>

  {{-- ===== MAIN ===== --}}
  <main class="content">
    @include('partials.topbar')

    @if (session('ok'))
      <div class="alert-success" id="flashAlert">
        <i class="ti ti-check"></i> {{ session('ok') }}
      </div>
    @endif

    @yield('content')
  </main>

  {{-- ===== SCRIPTS ===== --}}
  <script src="{{ asset('js/app.js') }}?v={{ filemtime(public_path('js/app.js')) }}" defer></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
