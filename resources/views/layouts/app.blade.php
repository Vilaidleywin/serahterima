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
          {{-- Role user biasa: isi menu user di sini --}}
          <a class="menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
            <i class="ti ti-layout-dashboard"></i> <span>Dashboard</span>
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



  {{-- MAIN --}}
  <main class="content">
    @include('partials.topbar')

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
</body>

</html>