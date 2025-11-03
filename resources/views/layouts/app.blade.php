<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="utf-8">
  @if (session('success'))
    <meta name="flash-success" content="{{ session('success') }}">
  @endif
  <title>SerahTerima</title>

  {{-- ===== Vendor ===== --}}
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/tabler-icons@3.6.0/iconfont/tabler-icons.min.css" rel="stylesheet">

  {{-- ===== Custom CSS ===== --}}
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

      <a class="nav-link {{ request()->routeIs('documents.index') ? 'active' : '' }}"
        href="{{ route('documents.index') }}">Data Dokumen</a>

      <a class="nav-link {{ request()->routeIs('documents.create') ? 'active' : '' }}"
        href="{{ route('documents.create') }}">
        Input Dokumen
      </a>
      <a class="nav-link disabled">Pengguna</a>
      <a class="nav-link disabled">Logout</a>
    </nav>
  </aside>

  {{-- ===== MOBILE NAV ===== --}}
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
      <a href="{{ route('dashboard') }}" class="mobile-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <i class="ti ti-home"></i> Dashboard
      </a>
      <a href="{{ route('documents.index') }}"
        class="mobile-link {{ request()->routeIs('documents.index') ? 'active' : '' }}">
        <i class="ti ti-file-description"></i> Data Dokumen
      </a>
      <a href="{{ route('documents.create') }}"
        class="mobile-link {{ request()->routeIs('documents.create') ? 'active' : '' }}">
        <i class="ti ti-file-plus"></i> Input Dokumen
      </a>
      <a class="mobile-link disabled"><i class="ti ti-report"></i> Laporan</a>
      <a class="mobile-link disabled"><i class="ti ti-users"></i> Pengguna</a>
      <a class="mobile-link disabled"><i class="ti ti-logout"></i> Logout</a>
    </nav>
  </div>

  {{-- ===== MAIN CONTENT ===== --}}
  <main class="content">
    @include('partials.topbar')

    {{-- Flash message --}}
    @if (session('ok'))
      <div class="alert-success" id="flashAlert">
        <i class="ti ti-check"></i> {{ session('ok') }}
      </div>
    @endif

    @yield('content')
  </main>

  {{-- ===== SCRIPTS ===== --}}
  {{-- Fallback SweetAlert + confirmDelete (agar tetap jalan walau Vite bermasalah) --}}
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    // Kalau confirmDelete dari app.js belum terdefinisi, pakai fallback ini.
    if (typeof window.confirmDelete !== 'function') {
      window.confirmDelete = function (id) {
        Swal.fire({
          title: 'Hapus dokumen ini?',
          text: 'Data akan hilang secara permanen!',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#d33',
          cancelButtonColor: '#6c757d',
          confirmButtonText: 'Ya, hapus!',
          cancelButtonText: 'Batal'
        }).then((result) => {
          if (result.isConfirmed) {
            const f = document.getElementById(`delete-form-${id}`);
            if (f) f.submit();
          }
        });
      };
    }
  </script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>


  @if (session('success'))
      <script>
        Swal.fire({
          icon: 'success',
          title: 'Berhasil!',
          text: '{{ session('success') }}',
          timer: 2000,
          showConfirmButton: false
        });
        <script>
          (function(){
      // buat menu satu kali, tempel ke body
      const menu = document.createElement('div');
          menu.id = 'rowActionMenu';
          menu.style.position = 'fixed';
          menu.style.minWidth = '180px';
          menu.style.borderRadius = '12px';
          menu.style.background = '#fff';
          menu.style.boxShadow = '0 12px 24px rgba(0,0,0,.12)';
          menu.style.padding = '6px';
          menu.style.zIndex = '3000';
          menu.style.display = 'none';
          menu.innerHTML = `
          <a id="am-detail" class="dropdown-item" style="display:block;padding:8px 12px;border-radius:8px;color:#111827;text-decoration:none">👁️ Detail</a>
          <a id="am-edit" class="dropdown-item" style="display:block;padding:8px 12px;border-radius:8px;color:#111827;text-decoration:none">✏️ Edit</a>
          <a id="am-sign" class="dropdown-item" style="display:block;padding:8px 12px;border-radius:8px;color:#111827;text-decoration:none">✍️ Tanda Tangan</a>
          <hr style="margin:6px 0">
            <button id="am-del" class="dropdown-item" style="width:100%;text-align:left;padding:8px 12px;border-radius:8px;color:#dc2626;background:transparent;border:0">🗑️ Hapus</button>
            `;
            document.body.appendChild(menu);

            function closeMenu(){menu.style.display = 'none'; }
      document.addEventListener('click', (e)=>{
        if (menu.style.display==='none') return;
            if (!menu.contains(e.target)) closeMenu();
      });
            window.addEventListener('resize', closeMenu);
            window.addEventListener('scroll', closeMenu, true);

            window.openActionMenu = function(ev, urlShow, urlEdit, urlSign, id){
              ev.stopPropagation();
            const rect = ev.currentTarget.getBoundingClientRect();
            // posisikan di samping tombol
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

      </script>
  @endif
@if (session('success'))
  <script>
    window.addEventListener('DOMContentLoaded', () => {
      if (window.Swal) {
        Swal.fire({
          toast: true,
          position: 'top-end',
          icon: 'success',
          title: @json(session('success')),
          showConfirmButton: false,
          timer: 2200,
          timerProgressBar: true,
        });
      }
    });
  </script>
@endif
@if (session('error'))
  <script>
    window.addEventListener('DOMContentLoaded', () => {
      if (window.Swal) {
        Swal.fire({
          toast: true,
          position: 'top-end',
          icon: 'error',
          title: @json(session('error')),
          showConfirmButton: false,
          timer: 2600,
          timerProgressBar: true,
        });
      }
    });
  </script>
@endif
@stack('scripts')
</body>

</html>