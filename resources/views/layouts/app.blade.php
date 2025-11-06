<!DOCTYPE html>
<html lang="id">
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title>SerahTerima</title>

  {{-- Vendor --}}
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/tabler-icons@3.6.0/iconfont/tabler-icons.min.css" rel="stylesheet">

  {{-- CSS hasil build (Tailwind v4/CLI) + custom --}}
  <link rel="stylesheet" href="{{ asset('css/app.build.css') }}">
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
  {{-- SIDEBAR (desktop) --}}
  <aside class="sidebar" id="appSidebar">
    <div class="brand brand-desktop">
      <span class="brand-icon">📁</span>
      <span class="brand-text">Serah Terima</span>
    </div>
    <nav class="nav flex-column gap-1">
      <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">Dashboard</a>
      <a class="nav-link {{ request()->routeIs('documents.index') ? 'active' : '' }}" href="{{ route('documents.index') }}">Data Dokumen</a>
      <a class="nav-link {{ request()->routeIs('documents.create') ? 'active' : '' }}" href="{{ route('documents.create') }}">Input Dokumen</a>
      <a class="nav-link disabled">Pengguna</a>
      <a class="nav-link disabled">Logout</a>
    </nav>
  </aside>

  {{-- MOBILE NAV --}}
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
      <a href="{{ route('dashboard') }}" class="mobile-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"><i class="ti ti-home"></i> Dashboard</a>
      <a href="{{ route('documents.index') }}" class="mobile-link {{ request()->routeIs('documents.index') ? 'active' : '' }}"><i class="ti ti-file-description"></i> Data Dokumen</a>
      <a href="{{ route('documents.create') }}" class="mobile-link {{ request()->routeIs('documents.create') ? 'active' : '' }}"><i class="ti ti-file-plus"></i> Input Dokumen</a>
      <a class="mobile-link disabled"><i class="ti ti-report"></i> Laporan</a>
      <a class="mobile-link disabled"><i class="ti ti-users"></i> Pengguna</a>
      <a class="mobile-link disabled"><i class="ti ti-logout"></i> Logout</a>
    </nav>
  </div>

  {{-- MAIN --}}
  <main class="content">
    {{-- @include('partials.topbar') --}}

    {{-- Flash message (opsional, kalau masih mau versi tekstual) --}}
    {{-- @if (session('success'))
      <div class="alert-success" id="flashAlert">
        <i class="ti ti-check"></i> {{ session('success') }}
      </div>
    @endif --}}

    @yield('content')
  </main>

  {{-- Scripts --}}
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  {{-- Fallback confirmDelete (global) --}}
  <script>
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
  </script>

  {{-- Action menu (jangan di dalam @if) --}}
  <script>
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

      function closeMenu(){ menu.style.display = 'none'; }
      document.addEventListener('click', (e)=>{ if (menu.style.display!=='none' && !menu.contains(e.target)) closeMenu(); });
      window.addEventListener('resize', closeMenu);
      window.addEventListener('scroll', closeMenu, true);

      window.openActionMenu = function(ev, urlShow, urlEdit, urlSign, id){
        ev.stopPropagation();
        const rect = ev.currentTarget.getBoundingClientRect();
        const x = Math.min(window.innerWidth - menu.offsetWidth - 8, rect.right - 180);
        const y = rect.bottom + 6;
        menu.style.left = `${Math.max(8, x)}px`;
        menu.style.top  = `${Math.min(window.innerHeight - 8, y)}px`;
        document.getElementById('am-detail').href = urlShow;
        document.getElementById('am-edit').href   = urlEdit;
        document.getElementById('am-sign').href   = urlSign;
        document.getElementById('am-del').onclick = function(){
          closeMenu();
          confirmDelete(id);
        };
        menu.style.display='block';
      };
    })();
  </script>

  {{-- Toast flash --}}
  @if (session('success'))
    <script>
      window.addEventListener('DOMContentLoaded', () => {
        Swal.fire({ toast:true, position:'top-end', icon:'success', title:@json(session('success')), showConfirmButton:false, timer:2200, timerProgressBar:true });
      });
    </script>
  @endif
  @if (session('error'))
    <script>
      window.addEventListener('DOMContentLoaded', () => {
        Swal.fire({ toast:true, position:'top-end', icon:'error', title:@json(session('error')), showConfirmButton:false, timer:2600, timerProgressBar:true });
      });
    </script>
  @endif

  @stack('scripts')
</body>
</html>
