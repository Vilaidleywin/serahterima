@php
  $user = auth()->user();
  $avatar = $user->avatar
    ? asset('storage/' . $user->avatar)
    : 'https://cdn-icons-png.flaticon.com/512/3177/3177440.png';

  $isAdmin = in_array(($user->role ?? ''), ['admin_internal', 'admin_komersial'], true);
@endphp



<header class="topbar d-flex justify-content-between align-items-center px-3" role="banner" aria-label="Topbar">
  <div class="d-flex align-items-center gap-2">
    {{-- HAMBURGER – hanya muncul di mobile --}}
    <button type="button" id="btnMobileNav" class="btn btn-light d-lg-none me-1" aria-label="Buka menu"
      style="border-radius:10px; width:34px; height:34px; display:flex; align-items:center; justify-content:center; font-size:20px;">
      ☰
    </button>

    {{-- Brand / judul --}}
    <a href="{{ $isAdmin ? route('admin.dashboard') : route('dashboard') }}"
      class="text-decoration-none d-flex align-items-center gap-2">
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
            <button class="dropdown-item text-danger d-flex align-items-center" type="button" id="btnDropdownLogout">
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
      @if ($isAdmin)
        <a href="{{ route('admin.dashboard') }}"
          class="mobile-link {{ request()->routeIs('admin.dashboard') ? 'is-active' : '' }}">
          <i class="ti ti-home-2"></i>
          <span>Dashboard</span>
        </a>


        @if (Route::has('admin.users.index'))
          <a href="{{ route('admin.users.index') }}"
            class="mobile-link {{ request()->routeIs('admin.users.*') ? 'is-active' : '' }}">
            <i class="ti ti-users"></i>
            <span>Pengguna</span>
          </a>
        @endif
      @else
        {{-- MENU USER BIASA --}}
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
      {{-- (bagian logout mobile yang tadi kita bahas bisa taruh di sini) --}}
    </div>

  </div>
</nav>

<div id="mobileNavBackdrop" class="mobile-drawer-backdrop d-lg-none" hidden></div>



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
          confirmClass: 'btn btn-danger ms-2',
          cancelClass: 'btn btn-outline-secondary '
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