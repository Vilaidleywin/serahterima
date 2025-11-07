<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title>{{ $title ?? 'SerahTerima' }}</title>

  {{-- Vendor --}}
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/tabler-icons@3.6.0/iconfont/tabler-icons.min.css" rel="stylesheet">

  {{-- Build CSS + custom kamu (tetap pakai jika ada) --}}
  <link rel="stylesheet" href="{{ asset('css/app.build.css') }}">
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">

  <style>
    :root{
      --aside-w: 260px;
      --topbar-h: 56px;
      --aside-bg: #1f3350;
      --aside-text: #e7efff;
      --aside-active: #274368;
    }

    /* ========== ASIDE (fix, desktop) ========== */
    .sidebar{
      position: fixed; inset: 0 auto 0 0;
      width: var(--aside-w);
      background: var(--aside-bg); color: var(--aside-text);
      display: flex; flex-direction: column; gap: 12px;
      padding: 18px 16px; z-index: 1031; /* di bawah topbar (1032) */
      border-right: 1px solid rgba(255,255,255,.06);
    }
    .sidebar .brand{ display:flex; align-items:center; gap:10px; margin-bottom:4px; }
    .sidebar .brand-icon{ width:36px; height:36px; display:grid; place-items:center; background:#2c4770; border-radius:10px; }
    .sidebar .brand-text{ font-weight:800; letter-spacing:.2px; }

    .sidebar .nav-link{
      display:flex; align-items:center; gap:10px;
      color: var(--aside-text); text-decoration:none;
      border-radius:10px; padding:10px 12px; font-weight:600;
      opacity:.92; transition: background .15s ease, opacity .15s ease;
    }
    .sidebar .nav-link:hover{ background: var(--aside-active); opacity:1; }
    .sidebar .nav-link.active{ background: var(--aside-active); color:#fff; }
    .sidebar .nav-link .ti{ font-size:18px; opacity:.95; }

    /* ========== TOPBAR (fix, kanan dari aside) ========== */
    .topbar{
      position: fixed; top:0; right:0; left: var(--aside-w);
      height: var(--topbar-h);
      display:flex; align-items:center; justify-content:space-between;
      background:#f8fafc; border-bottom:1px solid #e5e7eb;
      padding: 0 .8rem; z-index:1032;
    }
    .topbar .topbar-left{ display:flex; align-items:center; gap:12px; }
    .topbar .hamburger{ width:34px; height:34px; display:grid; place-items:center; border:0; background:transparent; }
    .topbar .brand{ display:flex; align-items:center; gap:8px; }

    /* Sembunyikan breadcrumb di topbar supaya gak dobel dengan breadcrumb di halaman */
    .topbar .breadcrumb-mini{ display:none !important; }

    /* ========== CONTENT (beri offset top & kiri) ========== */
    main.app-content{
      min-height: 100vh;
      padding: calc(var(--topbar-h) + 16px) 20px 24px;
      margin-left: var(--aside-w);
      background: #f6f7fb;
    }

    /* ========== MOBILE ========== */
    @media (max-width: 991px){
      .sidebar{ display:none; }
      body.nav-open .sidebar{
        display:block; position: fixed; inset: var(--topbar-h) auto 0 0;
        width: 84%; max-width: 320px; z-index:1040;
      }
      .topbar{ left: 0; }
      main.app-content{ margin-left: 0; padding-top: calc(var(--topbar-h) + 12px); }

      .nav-backdrop{
        position: fixed; inset: var(--topbar-h) 0 0 0; background: rgba(0,0,0,.35); display:none; z-index:1039;
      }
      body.nav-open .nav-backdrop{ display:block; }
    }
  </style>
</head>
<body>

  {{-- ASIDE & TOPBAR --}}
  @includeIf('layouts.sidebar')
  @includeIf('partials.topbar')
  <div class="nav-backdrop"></div>

  {{-- CONTENT --}}
  <main class="app-content container-fluid">
    @yield('content')
  </main>

  {{-- Vendor JS --}}
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  {{-- Controller kecil: drawer & dropdown user --}}
  <script>
  (function(){
    const body = document.body;
    const btn = document.getElementById('btnMobileNav');
    const backdrop = document.querySelector('.nav-backdrop');
    function toggle(open){
      if(open===undefined){ body.classList.toggle('nav-open'); }
      else { body.classList.toggle('nav-open', !!open); }
    }
    btn && btn.addEventListener('click', () => toggle());
    backdrop && backdrop.addEventListener('click', () => toggle(false));
    document.addEventListener('keydown', e=>{ if(e.key==='Escape') toggle(false); });

    // dropdown user
    const userToggle = document.getElementById('userToggle');
    const userMenu = document.getElementById('userMenu');
    function closeUser(){ userMenu?.classList.remove('open'); userToggle?.setAttribute('aria-expanded','false'); }
    function toggleUser(){ userMenu?.classList.toggle('open'); userToggle?.setAttribute('aria-expanded', String(userMenu?.classList.contains('open'))); }
    userToggle && userToggle.addEventListener('click', (e)=>{ e.stopPropagation(); toggleUser();});
    document.addEventListener('click', (e)=> {
      if(!userMenu) return;
      const within = userMenu.contains(e.target) || userToggle.contains(e.target);
      if(!within) closeUser();
    });
  })();
  </script>

  {{-- Flash fallback (kalau pakai session) --}}
  @if (session('success'))
    <div class="position-fixed top-0 end-0 p-3" style="z-index:2000">
      <div class="alert alert-success shadow mb-0">{{ session('success') }}</div>
    </div>
  @endif
  @if (session('error'))
    <div class="position-fixed top-0 end-0 p-3" style="z-index:2000">
      <div class="alert alert-danger shadow mb-0">{{ session('error') }}</div>
    </div>
  @endif

  @stack('scripts')
</body>
</html>
