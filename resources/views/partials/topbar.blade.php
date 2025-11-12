@php
  $user = auth()->user();
  $avatar = 'https://cdn-icons-png.flaticon.com/512/3177/3177440.png';
@endphp

<header class="topbar" role="banner" aria-label="Topbar">
  <div class="d-flex align-items-center gap-2">
    {{-- Overflow (mobile) → NAV PANEL DARK --}}
    <div class="dropdown d-lg-none">
      <button
        type="button"
        id="btnOverflow"
        class="btn btn-light"
        aria-label="Buka menu cepat"
        data-bs-toggle="dropdown"
        data-bs-auto-close="outside"
        aria-expanded="false"
        style="border-radius:10px"
      >
        <i class="ti ti-dots-vertical"></i>
      </button>

      <div class="dropdown-menu dropdown-menu-start dropdown-menu-sheet" aria-labelledby="btnOverflow">
        <div class="sheet-head">
          <strong class="brand">
            <img src="{{ asset('images/logo2.png') }}" alt="Logo" class="brand-logo"> Menu
          </strong>
          <button class="sheet-close" data-bs-toggle="dropdown" aria-label="Tutup">
            <i class="ti ti-x"></i>
          </button>
        </div>

        <div class="sheet-body">
          <a class="sheet-item" href="{{ route('dashboard') }}"><i class="ti ti-home me-2"></i>Dashboard</a>
          @if (Route::has('documents.index'))
            <a class="sheet-item" href="{{ route('documents.index') }}"><i class="ti ti-folder me-2"></i>Data Dokumen</a>
          @endif
          @if (Route::has('documents.create'))
            <a class="sheet-item" href="{{ route('documents.create') }}"><i class="ti ti-file-plus me-2"></i>Input Dokumen</a>
          @endif

          <div class="sheet-sep"></div>

          @if (Route::has('profile.edit'))
            <a class="sheet-item" href="{{ route('profile.edit') }}"><i class="ti ti-settings me-2"></i>Profil</a>
          @endif
          <form action="{{ route('logout') }}" method="POST" class="mt-1">
            @csrf
            <button type="submit" class="sheet-item text-danger"><i class="ti ti-logout me-2"></i>Logout</button>
          </form>
        </div>
      </div>
    </div>

    {{-- Brand / breadcrumb --}}
    <a href="{{ route('dashboard') }}" class="text-decoration-none d-flex align-items-center gap-2">
      <img src="{{ asset('images/logo2.png') }}" alt="Logo"
        style="width:32px;height:32px;border-radius:8px;object-fit:contain;background:#fff;padding:3px">
      <strong class="text-dark">Serah Terima</strong>
    </a>

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

  <div class="d-flex align-items-center gap-2">
    {{-- User dropdown --}}
    <div class="dropdown">
      <button
        class="btn btn-light btn-user d-flex align-items-center gap-2 px-2 py-1"
        type="button"
        data-bs-toggle="dropdown"
        data-bs-display="static"
        data-bs-offset="0,8"
        data-bs-auto-close="outside"
        aria-expanded="false"
        style="border-radius:999px"
      >
        <img src="{{ $avatar }}" alt="avatar" style="width:28px;height:28px;border-radius:50%;object-fit:cover">
        <span class="d-none d-md-inline text-truncate" style="max-width:160px">
          {{ \Illuminate\Support\Str::limit($user->name ?? 'Pengguna', 22) }}
        </span>
        <i class="ti ti-chevron-down d-none d-md-inline"></i>
      </button>

      <ul class="dropdown-menu dropdown-menu-end shadow menu-compact">
        @if (Route::has('profile.edit'))
          <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="ti ti-settings me-2"></i>Profil</a></li>
        @endif
        <li><hr class="dropdown-divider"></li>
        <li>
          <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button class="dropdown-item text-danger" type="submit"><i class="ti ti-logout me-2"></i>Logout</button>
          </form>
        </li>
      </ul>
    </div>
  </div>
</header>

{{-- Backdrop untuk panel (membuat panel “terpisah” dari halaman) --}}
<div id="menuBackdrop" class="menu-backdrop" hidden></div>

<style>
  :root {
    --topbar-h: 72px;
    --nav-bg: #1f3555;
    --nav-bg-2: #192c49;
    --nav-text: #e8eef7;
    --nav-hover: #2a476f;
  }

  .topbar {
    position: fixed; inset: 0 0 auto 0; height: var(--topbar-h);
    z-index: 1200; background: #fff; border-bottom: 1px solid #e5e7eb; padding: 8px 16px;
    backdrop-filter: saturate(140%) blur(6px);
  }
  .app-main { padding-top: var(--topbar-h); }
  .topbar .btn-user { padding: 6px 10px; border-radius: 999px; }

  .dropdown-menu { border-radius: 14px; padding: 8px; min-width: 220px; }
  .dropdown-item { padding: .65rem .85rem; border-radius: 10px; }
  .dropdown-item:hover { background: #f3f4f6; }
  .menu-compact { min-width: 220px; }

  /* === NAV PANEL DARK (terpisah, ada border & shadow) === */
  .dropdown-menu-sheet{
    padding:0; border:1px solid rgba(255,255,255,.08);
    overflow:hidden; width:92vw; max-width:520px;
    background:var(--nav-bg); color:var(--nav-text);
    border-radius:22px;
    box-shadow:0 28px 60px rgba(0,0,0,.35);
    transform-origin:top left; transform:translateY(-10px) scale(.98);
    opacity:0; transition:transform .22s ease, opacity .22s ease;
  }
  .dropdown-menu-sheet.show{ transform:translateY(0) scale(1); opacity:1; }

  .dropdown-menu-sheet .sheet-head{
    display:flex; align-items:center; justify-content:space-between;
    background:var(--nav-bg-2); padding:.95rem 1rem;
    border-bottom:1px solid rgba(255,255,255,.08);
  }
  .dropdown-menu-sheet .brand{ color:#fff; display:inline-flex; align-items:center; gap:.6rem; font-weight:700; }
  .dropdown-menu-sheet .brand-logo{ width:22px; height:22px; object-fit:contain; filter:brightness(0) invert(1); }
  .dropdown-menu-sheet .sheet-close{ border:0; background:transparent; width:36px; height:36px; border-radius:10px; color:#fff; }
  .dropdown-menu-sheet .sheet-close:hover{ background:rgba(255,255,255,.08); }

  .dropdown-menu-sheet .sheet-body{ padding:.6rem; display:flex; flex-direction:column; }
  .dropdown-menu-sheet .sheet-item{
    display:flex; align-items:center; gap:.65rem;
    padding:.9rem 1.1rem; color:var(--nav-text);
    text-decoration:none; border-radius:12px; font-weight:600;
  }
  .dropdown-menu-sheet .sheet-item i{ width:20px; text-align:center; opacity:.9; }
  .dropdown-menu-sheet .sheet-item:hover{ background:var(--nav-hover); }
  .dropdown-menu-sheet .sheet-sep{ height:1px; margin:.4rem 0; background:rgba(255,255,255,.14); }

  /* posisi aman & ada jarak dari tepi/topbar */
  @media (max-width: 992px){
    .dropdown-menu-sheet{
      margin-top:10px;          /* jarak dari topbar */
      margin-left:12px;         /* jarak dari tepi kiri */
      left:0 !important; right:auto !important;
    }
  }

  /* Backdrop: bikin panel berasa melayang & terpisah */
  .menu-backdrop{
    position:fixed; inset:var(--topbar-h) 0 0 0;
    background:rgba(0,0,0,.35);
    backdrop-filter: blur(1px);
    z-index:1199;  /* di bawah panel (1200+ dropdown) */
  }
  .menu-backdrop[hidden]{ display:none !important; }

  /* Kill sidebar di mobile */
  @media (max-width: 992px){
    .sidebar, .sidebar.open, .sidebar-backdrop{ display:none !important; visibility:hidden !important; pointer-events:none !important; }
    .sidebar{ transform:translateX(-200%) !important; }
  }
</style>

<script>
  // Tampilkan backdrop ketika dropdown panel dibuka
  (function () {
    const backdrop = document.getElementById('menuBackdrop');
    const dd = document.getElementById('btnOverflow')?.closest('.dropdown');

    if (!dd || !backdrop) return;

    dd.addEventListener('show.bs.dropdown', () => {
      backdrop.removeAttribute('hidden');
    });
    dd.addEventListener('hide.bs.dropdown', () => {
      backdrop.setAttribute('hidden', '');
    });

    // klik backdrop menutup dropdown
    backdrop.addEventListener('click', () => {
      const trigger = document.getElementById('btnOverflow');
      if (trigger) trigger.click();
    });
  })();
</script>
