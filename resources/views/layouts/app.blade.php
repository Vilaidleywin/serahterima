<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title>{{ $title ?? 'SerahTerima' }}</title>

  {{-- Vendor --}}
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/tabler-icons@3.6.0/iconfont/tabler-icons.min.css" rel="stylesheet">

  {{-- CSS hasil build (Tailwind v4/CLI) + custom --}}
  <link rel="stylesheet" href="{{ asset('css/app.build.css') }}">
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">


  <style>
    /* ========= VARIABEL & RESET ========= */
    :root{
      --aside-w: 260px;
      --aside-bg: #1f3350;
      --aside-text: #e7efff;
      --aside-active: #274368;
      --topbar-h: 56px;
    }
    html,body{ height:100%; }
    body{ background:#f6f7fb; overflow-x:hidden; } /* <-- cegah geser kanan */

    /* ========= SIDEBAR ========= */
    .sidebar{
      position: fixed; inset: 0 auto 0 0;
      width: var(--aside-w);
      background: var(--aside-bg); color: var(--aside-text);
      display:flex; flex-direction:column; gap:12px;
      padding:18px 16px; z-index:1031; /* di bawah topbar */
      border-right: 1px solid rgba(255,255,255,.08);
    }
    .sidebar .brand{ display:flex; align-items:center; gap:10px; margin-bottom:6px; }
    .sidebar .brand-icon{ width:36px; height:36px; display:grid; place-items:center; background:#2c4770; border-radius:10px; }
    .sidebar .brand-text{ font-weight:800; letter-spacing:.2px; }

    .menu .menu-item{
      display:flex; align-items:center; gap:10px;
      color: var(--aside-text); text-decoration:none;
      border-radius:10px; padding:10px 12px; font-weight:600;
      opacity:.92; transition: background .15s ease, opacity .15s ease;
    }
    .menu .menu-item:hover{ background: var(--aside-active); opacity:1; }
    .menu .menu-item.active{ background: var(--aside-active); color:#fff; }
    .menu .menu-item .ti{ font-size:18px; opacity:.95; }

    /* ========= TOPBAR OVERRIDE (ambil alih style dari partial) ========= */
    .topbar{
      position: fixed;
      top:0; right:0; left: var(--aside-w);  /* <-- offset ikut aside */
      height: var(--topbar-h);
      display:flex; align-items:center; justify-content:space-between;
      background:#f8fafc; border-bottom:1px solid #e5e7eb;
      padding: 0 .8rem; z-index:1032;
      transition: left .3s ease;
    }
    /* Sembunyikan breadcrumb di topbar saat layar kecil supaya tidak dobel */
    @media (max-width: 1024px){ .topbar .breadcrumb-mini{ display:none !important; } }

    /* ========= CONTENT ========= */
    .content{
      min-height:100vh;
      margin-left: var(--aside-w);
      padding: 20px;
      padding-top: calc(var(--topbar-h) + 20px); /* <-- tidak ketiban judul */
      transition: margin-left .3s ease;
    }

    /* ========= MOBILE ========= */
    @media (max-width: 1024px){
      .sidebar{ display:none; }
      body.nav-open .sidebar{
        display:block; position: fixed; inset: var(--topbar-h) auto 0 0;
        width: 84%; max-width: 320px; z-index:1040;
      }
      .topbar{ left: 0; }       /* <-- di HP, topbar full width */
      .content{ margin-left: 0; } /* <-- konten full, ga geser kanan */

      .nav-backdrop{
        position: fixed; inset: var(--topbar-h) 0 0 0;
        background: rgba(0,0,0,.45); display:none; z-index:1035;
      }
      body.nav-open .nav-backdrop{ display:block; }
    }
  </style>
</head>

<body>
  {{-- SIDEBAR (desktop + drawer mobile) --}}
  <aside class="sidebar" id="appSidebar">
    <div class="brand brand-desktop">
      <span class="brand-icon">📁</span>
      <span class="brand-text">Serah Terima</span>
    </div>

    <nav class="menu">
      @auth
        @php $role = auth()->user()->role ?? 'user'; @endphp

        @if (in_array($role, ['admin_internal', 'admin_komersial']))
          <a class="menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
            <i class="ti ti-layout-dashboard"></i> <span>Dashboard</span>
          </a>
          <a class="menu-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"
             href="{{ route('admin.users.index') }}">
            <i class="ti ti-users"></i> <span>Pengguna</span>
          </a>
          <form class="menu-item" action="{{ route('logout') }}" method="POST" onsubmit="return confirm('Keluar?')">
            @csrf
            <button type="submit" style="all:unset;display:flex;gap:8px;align-items:center;cursor:pointer">
              <i class="ti ti-logout"></i> <span>Logout</span>
            </button>
          </form>
        @else
          <a class="menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
            <i class="ti ti-layout-dashboard"></i> <span>Dashboard</span>
          </a>
          <a class="menu-item {{ request()->routeIs('documents.index') ? 'active' : '' }}"
             href="{{ route('documents.index') }}">
            <i class="ti ti-folder"></i> <span>Data Dokumen</span>
          </a>
          <a class="menu-item {{ request()->routeIs('documents.create') ? 'active' : '' }}"
             href="{{ route('documents.create') }}">
            <i class="ti ti-file-plus"></i> <span>Input Dokumen</span>
          </a>
          <form class="menu-item" action="{{ route('logout') }}" method="POST" onsubmit="return confirm('Keluar?')">
            @csrf
            <button type="submit" style="all:unset;display:flex;gap:8px;align-items:center;cursor:pointer">
              <i class="ti ti-logout"></i> <span>Logout</span>
            </button>
          </form>
        @endif
      @endauth
    </nav>
  </aside>

  {{-- BACKDROP buat drawer mobile --}}
  <div class="nav-backdrop" id="navBackdrop"></div>

  {{-- MAIN --}}
  <main class="content">
    @include('partials.topbar')
    @yield('content')
  </main>

  {{-- Scripts --}}
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
    // === Drawer mobile (klik hamburger di topbar) ===
    (function(){
      const body = document.body;
      const btn  = document.getElementById('btnMobileNav'); // tombol ada di topbar partial
      const bd   = document.getElementById('navBackdrop');

      function openNav(){ body.classList.add('nav-open'); btn?.classList.add('active'); }
      function closeNav(){ body.classList.remove('nav-open'); btn?.classList.remove('active'); }
      function toggleNav(){ body.classList.toggle('nav-open'); btn?.classList.toggle('active'); }

      btn && btn.addEventListener('click', toggleNav);
      bd && bd.addEventListener('click', closeNav);
      document.addEventListener('keydown', e => { if(e.key === 'Escape') closeNav(); });
    })();

    // === Fallback confirmDelete (global) ===
    window.confirmDelete ??= function (id) {
      Swal.fire({
        title: 'Hapus dokumen ini?',
        text: 'Data akan hilang secara permanen!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
      }).then((r) => {
        if (r.isConfirmed) {
          const f = document.getElementById(`delete-form-${id}`);
          if (f) f.submit();
        }
      });
    };

    // === Context menu baris (tetap) ===
    (function () {
      const menu = document.createElement('div');
      menu.id = 'rowActionMenu';
      Object.assign(menu.style, {
        position: 'fixed', minWidth: '180px', borderRadius: '12px',
        background: '#fff', boxShadow: '0 12px 24px rgba(0,0,0,.12)',
        padding: '6px', zIndex: '3000', display: 'none'
      });
      menu.innerHTML = `
        <a id="am-detail" class="dropdown-item" style="display:block;padding:8px 12px;border-radius:8px;color:#111827;text-decoration:none">👁️ Detail</a>
        <a id="am-edit"   class="dropdown-item" style="display:block;padding:8px 12px;border-radius:8px;color:#111827;text-decoration:none">✏️ Edit</a>
        <a id="am-sign"   class="dropdown-item" style="display:block;padding:8px 12px;border-radius:8px;color:#111827;text-decoration:none">✍️ Tanda Tangan</a>
        <hr style="margin:6px 0">
        <button id="am-del" class="dropdown-item" style="width:100%;text-align:left;padding:8px 12px;border-radius:8px;color:#dc2626;background:transparent;border:0">🗑️ Hapus</button>
      `;
      document.body.appendChild(menu);

      function closeMenu() { menu.style.display = 'none'; }
      document.addEventListener('click', (e) => { if (menu.style.display !== 'none' && !menu.contains(e.target)) closeMenu(); });
      window.addEventListener('resize', closeMenu);
      window.addEventListener('scroll', closeMenu, true);

      window.openActionMenu = function (ev, urlShow, urlEdit, urlSign, id) {
        ev.stopPropagation();
        const rect = ev.currentTarget.getBoundingClientRect();
        const x = Math.min(window.innerWidth - menu.offsetWidth - 8, rect.right - 180);
        const y = rect.bottom + 6;
        menu.style.left = `${Math.max(8, x)}px`;
        menu.style.top = `${Math.min(window.innerHeight - 8, y)}px`;
        document.getElementById('am-detail').href = urlShow;
        document.getElementById('am-edit').href = urlEdit;
        document.getElementById('am-sign').href = urlSign;
        document.getElementById('am-del').onclick = function () {
          closeMenu();
          confirmDelete(id);
        };
        menu.style.display = 'block';
      };
    })();
  </script>

  {{-- Toast flash --}}
  @if (session('success'))
    <script>
      window.addEventListener('DOMContentLoaded', () => {
        Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: @json(session('success')), showConfirmButton: false, timer: 2200, timerProgressBar: true });
      });
    </script>
  @endif
  @if (session('error'))
    <script>
      window.addEventListener('DOMContentLoaded', () => {
        Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: @json(session('error')), showConfirmButton: false, timer: 2600, timerProgressBar: true });
      });
    </script>
  @endif

  @stack('scripts')
  @stack('styles')
</body>
</html>
