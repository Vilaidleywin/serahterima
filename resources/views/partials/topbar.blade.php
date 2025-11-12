@php
  $user = auth()->user();
  $avatar = 'https://cdn-icons-png.flaticon.com/512/3177/3177440.png';
@endphp

<header class="topbar" role="banner" aria-label="Topbar">
  <div class="d-flex align-items-center gap-2">
    {{-- Hamburger (mobile) --}}
    <button type="button" id="btnMobileNav" class="btn btn-light d-lg-none" aria-label="Buka menu samping"
      aria-controls="appSidebar" aria-expanded="false" style="border-radius:10px">
      <i class="ti ti-menu-2"></i>
    </button>

    {{-- Overflow (mobile) --}}
    <button type="button" id="btnOverflow" class="btn btn-light d-lg-none" aria-label="Buka menu cepat"
      aria-controls="topbarMenu" aria-expanded="false" style="border-radius:10px">
      <i class="ti ti-dots-vertical"></i>
    </button>

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
      <button class="btn btn-light btn-user d-flex align-items-center gap-2 px-2 py-1" type="button"
        data-bs-toggle="dropdown" data-bs-display="static" data-bs-offset="0,8" aria-expanded="false"
        style="border-radius:999px">
        <img src="{{ $avatar }}" alt="avatar" style="width:28px;height:28px;border-radius:50%;object-fit:cover">
        <span class="d-none d-md-inline text-truncate" style="max-width:160px">
          {{ \Illuminate\Support\Str::limit($user->name ?? 'Pengguna', 22) }}
        </span>
        <i class="ti ti-chevron-down d-none d-md-inline"></i>
      </button>

      <ul class="dropdown-menu dropdown-menu-end shadow">
        @if (Route::has('profile.edit'))
          <li>
            <a class="dropdown-item" href="{{ route('profile.edit') }}">
              <i class="ti ti-settings me-2"></i>Profil
            </a>
          </li>
        @endif
        <li>
          <hr class="dropdown-divider">
        </li>
        <li>
          <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button class="dropdown-item text-danger" type="submit">
              <i class="ti ti-logout me-2"></i>Logout
            </button>
          </form>
        </li>
      </ul>
    </div>
  </div>
</header>

{{-- === OVERFLOW SHEET (mobile) === --}}
<div id="topbarMenuBackdrop" class="tbmenu-backdrop" aria-hidden="true" hidden></div>

<div id="topbarMenu" class="topbar-sheet" role="menu" aria-hidden="true" aria-labelledby="btnOverflow" hidden>
  <div class="sheet-head">
    <strong>Menu</strong>
    <button type="button" class="sheet-close" aria-label="Tutup menu" data-close-topbar-menu>
      <i class="ti ti-x"></i>
    </button>
  </div>
  <div class="sheet-body">
    <a href="{{ route('dashboard') }}" class="sheet-item"><i class="ti ti-home me-2"></i>Dashboard</a>
    @if (Route::has('documents.index'))
      <a href="{{ route('documents.index') }}" class="sheet-item"><i class="ti ti-folder me-2"></i>Data Dokumen</a>
    @endif
    @if (Route::has('documents.create'))
      <a href="{{ route('documents.create') }}" class="sheet-item"><i class="ti ti-file-plus me-2"></i>Input Dokumen</a>
    @endif
    <div class="sheet-sep"></div>
    @if (Route::has('profile.edit'))
      <a href="{{ route('profile.edit') }}" class="sheet-item"><i class="ti ti-settings me-2"></i>Profil</a>
    @endif
    <form action="{{ route('logout') }}" method="POST" class="mt-1">
      @csrf
      <button type="submit" class="sheet-item text-danger"><i class="ti ti-logout me-2"></i>Logout</button>
    </form>
  </div>
</div>

<style>
  
  :root {
    --topbar-h: 72px;
  }

  .topbar {
    position: fixed;
    inset: 0 0 auto 0;
    height: var(--topbar-h);
    z-index: 1200;
    background: #fff;
    border-bottom: 1px solid #e5e7eb;
    padding: 8px 16px;
  }

  .app-main {
    padding-top: var(--topbar-h);
  }

  .topbar .btn-user {
    padding: 6px 10px;
    border-radius: 999px;
  }

  .dropdown-menu {
    min-width: 220px;
    border-radius: 14px;
    padding: 8px;
  }

  /* default hidden */
  .topbar-sheet,
  .tbmenu-backdrop {
    display: none;
  }

  /* mobile sheet */
  @media (max-width:992px) {
    .topbar-sheet {
      position: fixed;
      left: 0;
      right: 0;
      top: var(--topbar-h);
      background: #fff;
      border-bottom: 1px solid #e5e7eb;
      transform: translateY(-110%);
      transition: transform .28s ease;
      z-index: 1045;
      box-shadow: 0 12px 24px rgba(0, 0, 0, .08);
      border-bottom-left-radius: 14px;
      border-bottom-right-radius: 14px;
      overflow: hidden;
      display: block;
    }

    body.tbmenu-open .topbar-sheet {
      transform: translateY(0);
    }

    .tbmenu-backdrop {
      position: fixed;
      inset: var(--topbar-h) 0 0 0;
      background: rgba(0, 0, 0, .35);
      z-index: 1040;
    }

    body.tbmenu-open .tbmenu-backdrop {
      display: block;
    }

    .sheet-head {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: .8rem 1rem;
      background: #f8fafc;
      border-bottom: 1px solid #e5e7eb;
    }

    .sheet-close {
      border: 0;
      background: transparent;
      width: 36px;
      height: 36px;
      border-radius: 8px;
    }

    .sheet-close:hover {
      background: #eef2f7;
    }

    .sheet-body {
      padding: .4rem;
      display: flex;
      flex-direction: column;
    }

    .sheet-item {
      display: flex;
      align-items: center;
      padding: .65rem .85rem;
      border-radius: 10px;
      text-decoration: none;
      color: #111827;
    }

    .sheet-item:hover {
      background: #f3f4f6;
    }

    .sheet-sep {
      height: 1px;
      background: #e5e7eb;
      margin: .4rem 0;
    }
  }

  .topbar-sheet[hidden],
  .tbmenu-backdrop[hidden] {
    display: none !important;
  }
</style>

<script>
  (function () {
    const body = document.body;
    const btnOverflow = document.getElementById('btnOverflow');
    const sheet = document.getElementById('topbarMenu');
    const backdrop = document.getElementById('topbarMenuBackdrop');
    const closeBtns = document.querySelectorAll('[data-close-topbar-menu]');
    const btnMobile = document.getElementById('btnMobileNav');

    function openSheet() {
      body.classList.add('tbmenu-open'); btnOverflow?.setAttribute('aria-expanded', 'true');
      sheet?.setAttribute('aria-hidden', 'false'); sheet?.removeAttribute('hidden'); backdrop?.removeAttribute('hidden');
    }
    function closeSheet() {
      body.classList.remove('tbmenu-open'); btnOverflow?.setAttribute('aria-expanded', 'false');
      sheet?.setAttribute('aria-hidden', 'true'); sheet?.setAttribute('hidden', ''); backdrop?.setAttribute('hidden', '');
    }
    function toggleSheet() { body.classList.contains('tbmenu-open') ? closeSheet() : openSheet(); }

    btnOverflow?.addEventListener('click', toggleSheet);
    closeBtns.forEach(btn => btn.addEventListener('click', closeSheet));
    backdrop?.addEventListener('click', closeSheet);
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeSheet(); });
    sheet?.querySelectorAll('a').forEach(a => a.addEventListener('click', closeSheet));
    window.addEventListener('resize', () => { if (window.innerWidth >= 1024) closeSheet(); });

    /* Toggle sidebar (mobile) → butuh .sidebar di layout */
    btnMobile?.addEventListener('click', () => {
      const sb = document.querySelector('.sidebar');
      if (!sb) return;
      sb.classList.toggle('open');
      // kalau ada backdrop sidebar, atur tampil/hidden-nya
      const sbb = document.querySelector('.sidebar-backdrop');
      if (sbb) {
        if (sb.classList.contains('open')) sbb.classList.add('show');
        else sbb.classList.remove('show');
      }
      closeSheet(); // tutup sheet kalau kebuka
    });

    // Tutup saat navigasi
    ['pageshow', 'turbo:visit', 'turbo:load', 'inertia:visit', 'inertia:navigate', 'livewire:navigated']
      .forEach(ev => window.addEventListener(ev, closeSheet));
  })();
</script>