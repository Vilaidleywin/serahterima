<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title>{{ $title ?? 'Serah Terima' }}</title>

  {{-- Vendor --}}
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/tabler-icons@3.6.0/iconfont/tabler-icons.min.css" rel="stylesheet">

  {{-- Build CSS (opsional) --}}
  <link rel="stylesheet" href="{{ asset('css/app.build.css') }}">
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">

  @stack('styles')

  <style>
    :root {
      --aside-w: 260px;
      --aside-bg: #1f3350;
      --aside-text: #e7efff;
      --aside-active: #274368;
      --topbar-h: 60px;
    }

    html,
    body {
      height: 100%
    }


    body {
      background: #f6f7fb;
      overflow-x: hidden
    }

    /* ===== SIDEBAR ===== */
    .sidebar {
      position: fixed;
      inset: 0 auto 0 0;
      width: var(--aside-w);
      background: var(--aside-bg);
      color: var(--aside-text);
      display: flex;
      flex-direction: column;
      gap: 12px;
      padding: 18px 16px;
      z-index: 1031;
      border-right: 1px solid rgba(255, 255, 255, .08);
    }

    .sidebar .brand {
      display: flex;
      align-items: center;
      gap: 10px;
      margin-bottom: 6px
    }

    .brand-icon {
      width: 36px;
      height: 36px;
      display: grid;
      place-items: center;
      background: #2c4770;
      border-radius: 10px
    }

    .brand-text {
      font-weight: 800;
      letter-spacing: .2px
    }

    .menu .menu-item {
      display: flex;
      align-items: center;
      gap: 10px;
      color: var(--aside-text);
      text-decoration: none;
      border-radius: 10px;
      padding: 10px 12px;
      font-weight: 600;
      opacity: .92;
      transition: background .15s ease, opacity .15s ease
    }

    .menu .menu-item:hover {
      background: var(--aside-active);
      opacity: 1
    }

    .menu .menu-item.active {
      background: var(--aside-active);
      color: #fff
    }

    .menu .menu-item .ti {
      font-size: 18px;
      opacity: .95
    }

    /* 🔑 CSS Tambahan untuk Logout Hover & Gaya Dasar */
    .sidebar #logout-form button.menu-item {
      /* Pastikan gaya dasar tombol sama dengan a.menu-item */
      background-color: transparent;
      color: var(--aside-text);
      cursor: pointer;
      /* Menambahkan cursor pointer */
    }

    .sidebar #logout-form button.menu-item:hover {
      background: var(--aside-active);
      opacity: 1;
      color: #fff;
      /* Tambahkan agar teks tetap putih saat hover */
    }

    /* ===== TOPBAR ===== */
    .topbar {
      position: fixed;
      top: 0;
      right: 0;
      left: 0;
      height: var(--topbar-h);
      display: flex;
      align-items: center;
      justify-content: space-between;
      background: #f8fafc;
      border-bottom: 1px solid #e5e7eb;
      padding: 0 .8rem;
      z-index: 1032;
      transition: left .3s ease;
    }

    @media (min-width:1024px) {
      body.has-aside .topbar {
        left: var(--aside-w);
      }
    }

    /* ===== CONTENT ===== */
    .content {
      min-height: 100vh;
      margin-left: 0;
      padding: 20px;
      padding-top: calc(var(--topbar-h) + 20px);
      transition: margin-left .3s ease;
    }

    @media (min-width:1024px) {
      body.has-aside .content {
        margin-left: var(--aside-w);
      }
    }

    /* ===== MOBILE: sidebar jadi drawer ===== */
    @media (max-width:1024px) {
      .sidebar {
        inset: var(--topbar-h) auto 0 0;
        width: 84%;
        max-width: 320px;
        transform: translateX(-100%);
        transition: transform .28s ease;
        display: block;
      }



      .topbar {
        left: 0;
      }

      .content {
        margin-left: 0;
      }


    }
  </style>
</head>

<body class="has-aside">
  {{-- SIDEBAR --}}
  <aside class="sidebar" id="appSidebar">
    <div class="brand d-flex align-items-center py-2">
      <img src="{{ asset('images/Logo3.png') }}" alt="Logo Pelni Services" class="brand-img"
        style="width:150px;object-fit:contain;display:block;">
    </div>

    <nav class="menu">
      @php
        $u = auth()->user();
        $isAdmin = false;
        if ($u) {
          if (method_exists($u, 'hasRole')) {
            try {
              $isAdmin = $u->hasRole(['admin_internal', 'admin_komersial']);
            } catch (\Throwable $e) {
            }
          }
          if (!$isAdmin)
            $isAdmin = in_array(($u->role ?? ''), ['admin_internal', 'admin_komersial'], true);
        }
      @endphp

      @if ($isAdmin)
        <a class="menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
          <i class="ti ti-layout-dashboard"></i> <span>Dashboard</span>
        </a>
        @if (Route::has('admin.users.index'))
          <a class="menu-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"
            href="{{ route('admin.users.index') }}">
            <i class="ti ti-users"></i> <span>Pengguna</span>
          </a>
        @endif
      @else
        <a class="menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
          <i class="ti ti-layout-dashboard"></i> <span>Dashboard</span>
        </a>
        @if (Route::has('documents.index'))
          <a class="menu-item {{ request()->routeIs('documents.index') ? 'active' : '' }}"
            href="{{ route('documents.index') }}">
            <i class="ti ti-folder"></i> <span>Data Dokumen</span>
          </a>
        @endif
        @if (Route::has('documents.create'))
          <a class="menu-item {{ request()->routeIs('documents.create') ? 'active' : '' }}"
            href="{{ route('documents.create') }}">
            <i class="ti ti-file-plus"></i> <span>Input Dokumen</span>
          </a>
        @endif
      @endif

      {{-- Logout --}}
      <form id="logout-form" action="{{ route('logout') }}" method="POST" class="mt-auto">
        @csrf
        <button type="button" id="btn-logout" class="menu-item w-100 border-0 bg-transparent text-start">
          <i class="ti ti-logout"></i> <span>Logout</span>
        </button>
      </form>
    </nav>
  </aside>

  {{-- MAIN --}}
  <main class="content">
    @include('partials.topbar')
    @yield('content')
  </main>

  {{-- Scripts --}}
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
    // Drawer mobile (sidebar)
    (function () {
      const body = document.body;
      const btn = document.getElementById('btnMobileNav');
      const bd = document.getElementById('navBackdrop'); // <- sudah didefinisikan lagi

      function openNav() {
        body.classList.add('nav-open');
        btn?.classList.add('active');
        btn?.setAttribute('aria-expanded', 'true');
      }

      function closeNav() {
        body.classList.remove('nav-open');
        btn?.classList.remove('active');
        btn?.setAttribute('aria-expanded', 'false');
      }

      function toggleNav() {
        body.classList.contains('nav-open') ? closeNav() : openNav();
      }

      btn && btn.addEventListener('click', toggleNav);
      bd && bd.addEventListener('click', closeNav);

      document.addEventListener('keydown', e => { if (e.key === 'Escape') closeNav(); });

      // Auto-close saat klik link di sidebar
      document.querySelectorAll('.menu a').forEach(a => a.addEventListener('click', closeNav));

      // Auto-close pada navigasi SPA / bfcache
      window.addEventListener('pageshow', closeNav);
      window.addEventListener('turbo:visit', closeNav);
      window.addEventListener('turbo:load', closeNav);
      document.addEventListener('inertia:visit', closeNav);
      document.addEventListener('inertia:navigate', closeNav);
      document.addEventListener('livewire:navigated', closeNav);

      // Tutup jika masuk desktop
      window.addEventListener('resize', () => { if (window.innerWidth >= 1024) closeNav(); });
    })();

    // Helper: konfirmasi aksi dengan SweetAlert + styling Bootstrap
    function confirmWithSwal(options) {
      return Swal.fire({
        icon: options.icon ?? 'warning',
        title: options.title ?? 'Yakin?',
        text: options.text ?? '',
        showCancelButton: true,
        confirmButtonText: options.confirmText ?? 'Ya',
        cancelButtonText: options.cancelText ?? 'Batal',
        reverseButtons: true,
        buttonsStyling: false,
        customClass: {
          confirmButton: options.confirmClass ?? 'btn btn-danger me-2',
          cancelButton: options.cancelClass ?? 'btn btn-secondary'
        }
      });
    }

    // Global confirmDelete
    window.confirmDelete ??= function (id) {
      confirmWithSwal({
        icon: 'warning',
        title: 'Hapus dokumen ini?',
        text: 'Data akan hilang secara permanen dan tidak bisa dikembalikan.',
        confirmText: 'Ya, hapus',
        cancelText: 'Batal',
        confirmClass: 'btn btn-danger me-2',
        cancelClass: 'btn btn-outline-secondary'
      }).then((r) => {
        if (r.isConfirmed) {
          const f = document.getElementById(`delete-form-${id}`);
          if (f) f.submit();
        }
      });
    };

    // LOGOUT
    document.getElementById('btn-logout')?.addEventListener('click', function () {
      confirmWithSwal({
        icon: 'question',
        title: 'Keluar dari aplikasi?',
        text: 'Sesi kamu akan diakhiri dan kamu perlu login kembali untuk mengakses sistem.',
        confirmText: 'Ya, logout',
        cancelText: 'Batal',
        confirmClass: 'btn btn-danger ms-2',
        cancelClass: 'btn btn-outline-secondary me-2'

      }).then((r) => {
        if (r.isConfirmed) {
          document.getElementById('logout-form')?.submit();
        }
      });
    });
  </script>

  {{-- Flash toast --}}
  @if (request('updated') == 1)
    <script>
      window.addEventListener('DOMContentLoaded', () => {
        Swal.fire({
          toast: true,
          position: 'top-end',
          icon: 'success',
          title: 'Profil berhasil diperbarui!',
          showConfirmButton: false,
          timer: 2200,
          timerProgressBar: true
        });
      });
    </script>
  @endif
  @if (session('error'))
    <script>
      window.addEventListener('DOMContentLoaded', () => {
        Swal.fire({
          toast: true,
          position: 'top-end',
          icon: 'error',
          title: @json(session('error')),
          showConfirmButton: false,
          timer: 2600,
          timerProgressBar: true
        });
      });
    </script>
  @endif

  @stack('scripts')
</body>

</html>