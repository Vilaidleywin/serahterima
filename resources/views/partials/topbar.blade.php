@php
  $user = auth()->user();
  $avatar = $user->avatar
    ? asset('storage/' . $user->avatar)
    : 'https://cdn-icons-png.flaticon.com/512/3177/3177440.png';
@endphp


<header class="topbar d-flex justify-content-between align-items-center px-3" role="banner" aria-label="Topbar">
  <div class="d-flex align-items-center gap-2">
    {{-- HAMBURGER – hanya muncul di mobile --}}
    <button type="button" id="btnMobileNav" class="btn btn-light d-lg-none me-1" aria-label="Buka menu"
      style="border-radius:10px; width:34px; height:34px; display:flex; align-items:center; justify-content:center; font-size:20px;">
      ☰
    </button>

    {{-- Brand / judul --}}
    <a href="{{ route('dashboard') }}" class="text-decoration-none d-flex align-items-center gap-2">
      <img src="{{ asset('images/logo2.png') }}" alt="Logo"
        style="width:32px;height:32px;border-radius:8px;object-fit:contain;background:#fff;padding:3px">
      <strong class="text-dark">Serah Terima</strong>
    </a>

    {{-- Breadcrumb (desktop saja, opsional) --}}
    @isset($breadcrumb)
      <nav class="breadcrumb-mini ms-2 d-none d-lg-flex" aria-label="Breadcrumb">
        @foreach($breadcrumb as $i => $item)
          @if(!empty($item['url']) && $i < count($breadcrumb) - 1)
            <a href="{{ $item['url'] }}" class="text-secondary text-decoration-none">{{ $item['label'] }}</a>
            <span class="mx-1 text-muted">/</span>
          @else
            <span class="text-muted">{{ $item['label'] }}</span>
          @endif
        @endforeach
      </nav>
    @endisset
  </div>

  {{-- User dropdown --}}
  <div class="d-flex align-items-center gap-2">
    <div class="dropdown">
      <button class="btn btn-light btn-user d-flex align-items-center gap-2 px-2 py-1" type="button"
        data-bs-toggle="dropdown" data-bs-display="static" data-bs-offset="0,8" data-bs-auto-close="outside"
        aria-expanded="false" style="border-radius:999px">

        <img src="{{ $avatar }}" alt="avatar" style="width:28px;height:28px;border-radius:50%;object-fit:cover">

        <span class="d-none d-md-inline text-truncate" style="max-width:160px">
          {{ \Illuminate\Support\Str::limit($user->name ?? 'Pengguna', 22) }}
        </span>

        <i class="ti ti-chevron-down d-none d-md-inline"></i>
      </button>

      <ul class="dropdown-menu dropdown-menu-end shadow menu-compact">

        {{-- HEADER DROPDOWN --}}
        <li class="px-3 py-2">
          <div class="d-flex align-items-center gap-2">
            <img src="{{ $avatar }}" alt="avatar"
              style="width:45px;height:45px;border-radius:12px;object-fit:cover;border:1px solid #eee">
            <div>
              <div class="fw-semibold">{{ $user->name }}</div>
              <div class="text-muted small">{{ $user->division ?? 'Divisi tidak ada' }}</div>
            </div>
          </div>
        </li>

        <li>
          <hr class="dropdown-divider">
        </li>

        {{-- MENU PROFIL --}}
        @if (Route::has('profile.edit'))
          <li>
            <a class="dropdown-item d-flex align-items-center" href="{{ route('profile.edit') }}">
              <i class="ti ti-settings me-2"></i> Pengaturan Profil
            </a>
          </li>
        @endif

        {{-- LOGOUT (DROPDOWN) --}}
        <li>
          <form action="{{ route('logout') }}" method="POST" id="dropdownLogoutForm">
            @csrf
            <button class="dropdown-item text-danger d-flex align-items-center"
                    type="button" id="btnDropdownLogout">
              <i class="ti ti-logout me-2"></i> Logout
            </button>
          </form>
        </li>

      </ul>
    </div>

  </div>
</header>

{{-- === MOBILE DRAWER NAV (FULLSCREEN) === --}}
<nav id="mobileNav" class="mobile-drawer d-lg-none" aria-label="Menu utama">
  <div class="mobile-drawer-inner">
    <div class="mobile-drawer-header">
      <div class="d-flex align-items-center gap-2">
        <img src="{{ asset('images/logo2.png') }}" alt="Logo" class="mobile-brand-logo">
        <div class="fw-semibold text-white">Menu</div>
      </div>
      <button type="button" class="btn btn-link text-white p-0" id="btnMobileNavClose" aria-label="Tutup menu"
        style="width:34px; height:34px; display:flex; align-items:center; justify-content:center; font-size:22px;">
        ✕
      </button>
    </div>

    <div class="mobile-drawer-body">
      <a href="{{ route('dashboard') }}" class="mobile-link {{ request()->routeIs('dashboard') ? 'is-active' : '' }}">
        <i class="ti ti-home-2"></i>
        <span>Dashboard</span>
      </a>

      @if (Route::has('documents.index'))
        <a href="{{ route('documents.index') }}"
          class="mobile-link {{ request()->routeIs('documents.index') ? 'is-active' : '' }}">
          <i class="ti ti-folder"></i>
          <span>Data Dokumen</span>
        </a>
      @endif

      @if (Route::has('documents.create'))
        <a href="{{ route('documents.create') }}"
          class="mobile-link {{ request()->routeIs('documents.create') ? 'is-active' : '' }}">
          <i class="ti ti-file-plus"></i>
          <span>Input Dokumen</span>
        </a>
      @endif

      {{-- <div class="mobile-separator"></div> --}}

      @if (Route::has('profile.edit'))
        <a href="{{ route('profile.edit') }}"
          class="mobile-link {{ request()->routeIs('profile.edit') ? 'is-active' : '' }}">
          <i class="ti ti-settings"></i>
          <span>Profil</span>
        </a>
      @endif

      {{-- LOGOUT (MOBILE) --}}
      
    </div>
  </div>
</nav>

<div id="mobileNavBackdrop" class="mobile-drawer-backdrop d-lg-none" hidden></div>

<style>
  :root {
    --topbar-h: 72px;
    --nav-bg: #1f3555;
    --nav-bg-2: #192c49;
    --nav-text: #e8eef7;
    --nav-hover: #2a476f;
  }

  /* TOPBAR */
  .topbar {
    position: fixed;
    inset: 0 0 auto 0;
    height: var(--topbar-h);
    z-index: 1200;
    background: #fff;
    border-bottom: 1px solid #e5e7eb;
  }

  /* Pastikan di layout utama, konten dibungkus dengan class "app-main" */
  .app-main {
    padding-top: var(--topbar-h);
  }

  .topbar .btn-user {
    padding: 6px 10px;
    border-radius: 999px;
  }

  .dropdown-menu {
    border-radius: 14px;
    padding: 8px;
    min-width: 220px;
  }

  .dropdown-item {
    padding: .65rem .85rem;
    border-radius: 10px;
  }

  .dropdown-item:hover {
    background: #f3f4f6;
  }

  .menu-compact {
    min-width: 220px;
  }

  .menu-backdrop {
    display: none !important;
  }

  /* === MOBILE DRAWER FULL-SCREEN ala GLPI === */
  .mobile-drawer {
    position: fixed;
    inset: 0;
    z-index: 1200;
    pointer-events: none;
  }

  .mobile-drawer-inner {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    width: 100%;
    max-width: 100vw;
    background: var(--nav-bg);
    color: var(--nav-text);
    display: flex;
    flex-direction: column;
    transform: translateY(-100%);
    transition: transform .22s ease;
    box-shadow: none;
  }

  .mobile-drawer-header {
    padding: .9rem 1rem;
    background: var(--nav-bg-2);
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid rgba(255, 255, 255, .12);
  }

  .mobile-brand-logo {
    width: 28px;
    height: 28px;
    object-fit: contain;
    filter: brightness(0) invert(1);
  }

  .mobile-drawer-body {
    padding: .5rem .5rem 1rem;
    overflow-y: auto;
    flex: 1;
  }

  .mobile-link {
    display: flex;
    align-items: center;
    gap: .6rem;
    padding: .75rem 1rem;
    border-radius: 12px;
    color: inherit;
    text-decoration: none;
    font-weight: 500;
    font-size: .95rem;
  }

  .mobile-link i {
    width: 20px;
    text-align: center;
    opacity: .9;
  }

  .mobile-link:hover {
    background: var(--nav-hover);
  }

  .mobile-link.is-active {
    background: #163157;
  }

  .mobile-separator {
    height: 1px;
    background: rgba(255, 255, 255, .15);
    margin: .6rem 0;
  }

  .mobile-drawer-backdrop {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, .25);
    backdrop-filter: blur(1px);
    z-index: 1199;
  }

  .mobile-drawer-backdrop[hidden] {
    display: none !important;
  }

  .mobile-drawer.open {
    pointer-events: auto;
  }

  .mobile-drawer.open .mobile-drawer-inner {
    transform: translateY(0);
  }

  /* Hide sidebar di mobile – konten full */
  @media (max-width: 992px) {

    .sidebar,
    .sidebar.open,
    .sidebar-backdrop {
      display: none !important;
      visibility: hidden !important;
      pointer-events: none !important;
    }
  }
</style>

<script>
  (function () {
    const btnOpen = document.getElementById('btnMobileNav');
    const btnClose = document.getElementById('btnMobileNavClose');
    const drawer = document.getElementById('mobileNav');
    const backdrop = document.getElementById('mobileNavBackdrop');

    if (!btnOpen || !drawer || !backdrop) return;

    const openNav = () => {
      drawer.classList.add('open');
      backdrop.hidden = false;
      document.body.style.overflow = 'hidden';
    };

    const closeNav = () => {
      drawer.classList.remove('open');
      backdrop.hidden = true;
      document.body.style.overflow = '';
    };

    btnOpen.addEventListener('click', openNav);
    btnClose?.addEventListener('click', closeNav);
    backdrop.addEventListener('click', closeNav);

    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') closeNav();
    });
  })();

  // === SAMAIN KONFIRMASI LOGOUT DENGAN app.blade ===
  (function () {
    const dropdownBtn = document.getElementById('btnDropdownLogout');
    const mobileBtn = document.getElementById('btnMobileLogout');

    function handleLogoutClick(e) {
      e.preventDefault();
      const form = e.currentTarget.closest('form');
      if (!form) return;

      // pakai helper global dari app.blade
      if (typeof confirmWithSwal === 'function') {
        confirmWithSwal({
          icon: 'question',
          title: 'Keluar dari aplikasi?',
          text: 'Sesi kamu akan diakhiri dan kamu perlu login kembali untuk mengakses sistem.',
          confirmText: 'Ya, logout',
          cancelText: 'Batal',
          // jarak antar tombol pakai util Bootstrap (ms/me)
          confirmClass: 'btn btn-danger ms-3',
          cancelClass: 'btn btn-outline-secondary me-3'
        }).then((r) => {
          if (r.isConfirmed) form.submit();
        });
      } else {
        // fallback kalau SweetAlert belum tersedia
        if (confirm('Keluar dari aplikasi?')) form.submit();
      }
    }

    dropdownBtn?.addEventListener('click', handleLogoutClick);
    mobileBtn?.addEventListener('click', handleLogoutClick);
  })();
</script>
