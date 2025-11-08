@php
  $user = auth()->user();
  $role = $user->role ?? 'user';
  $avatar = 'https://cdn-icons-png.flaticon.com/512/3177/3177440.png';
@endphp

<!-- ====== TOPBAR ====== -->
<header class="topbar" role="banner">
  <div class="topbar-left">
    <button type="button" class="hamburger" id="btnMobileNav" aria-label="Buka menu" aria-controls="mobileNav" aria-expanded="false">
      <span></span><span></span><span></span>
    </button>

    <a href="{{ route('dashboard') }}" class="brand" aria-label="Beranda">
      <div class="brand-icon">📁</div>
      <strong>Serah Terima</strong>
    </a>

    @isset($breadcrumb)
      <nav class="breadcrumb-mini" aria-label="Breadcrumb">
        @foreach($breadcrumb as $i => $item)
          @if(!empty($item['url']) && $i < count($breadcrumb)-1)
            <a href="{{ $item['url'] }}">{{ $item['label'] }}</a><span class="sep">/</span>
          @else
            <span class="current">{{ $item['label'] }}</span>
          @endif
        @endforeach
      </nav>
    @endisset
  </div>

  <div class="topbar-right">
    <div class="user-dropdown">
      <button type="button" class="user-avatar" id="userToggle" aria-haspopup="true" aria-expanded="false" aria-controls="userMenu">
        <img src="{{ $avatar }}" alt="avatar" class="avatar-img">
        <span class="user-name">{{ \Illuminate\Support\Str::limit($user->name ?? 'Pengguna', 20) }}</span>
        <i class="ti ti-chevron-down chevron"></i>
      </button>

      <div class="user-menu" id="userMenu" role="menu" aria-hidden="true">
        <div class="user-info">
          <img src="{{ $avatar }}" alt="avatar" class="avatar-img-sm">
          <div class="user-meta">
            <div class="nm">{{ $user->name ?? 'Pengguna' }}</div>
            <div class="em">{{ $user->email ?? '' }}</div>
          </div>
        </div>

        <a href="{{ route('profile.edit') }}" class="user-item" role="menuitem">
          <i class="ti ti-settings"></i> Profil
        </a>

        <div class="user-sep"></div>

        <a href="#" class="user-item btn-logout" role="menuitem"
           onclick="event.preventDefault(); document.getElementById('logoutFormTop').requestSubmit();">
          <i class="ti ti-logout"></i> Logout
        </a>
        <form id="logoutFormTop" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
      </div>
    </div>
  </div>
</header>

<!-- ====== SIDEBAR ====== -->
<aside class="sidebar" role="navigation" aria-label="Sidebar">
  <div class="brand side">
    <div class="brand-icon">📄</div>
    <div class="brand-text">SerahTerima</div>
  </div>

  <nav class="nav flex-column gap-1">
    <a href="{{ route('dashboard') }}"
       class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
      <i class="ti ti-layout-dashboard me-2"></i> Dashboard
    </a>

    @if (in_array($role, ['admin_internal','admin_komersial']))
      <a href="{{ route('admin.users.index') }}"
         class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
        <i class="ti ti-users me-2"></i> Pengguna
      </a>
    @else
      <a href="{{ route('documents.index') }}"
         class="nav-link {{ request()->routeIs('documents.index') ? 'active' : '' }}">
        <i class="ti ti-folder me-2"></i> Data Dokumen
      </a>
      <a href="{{ route('documents.create') }}"
         class="nav-link {{ request()->routeIs('documents.create') ? 'active' : '' }}">
        <i class="ti ti-file-plus me-2"></i> Input Dokumen
      </a>
    @endif
  </nav>

  <hr class="side-sep">

  <div class="small text-white-50 mb-2">Akun</div>

  <!-- Link logout + form tersembunyi (hindari style button putih) -->
  <a href="#" class="nav-link logout-link"
     onclick="event.preventDefault(); if(confirm('Keluar dari aplikasi?')) document.getElementById('logoutFormSide').requestSubmit();">
    <i class="ti ti-logout me-2"></i> Logout
  </a>
  <form id="logoutFormSide" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>

  <div class="mt-auto small text-white-50" style="opacity:.7">
    <div>{{ $user->name ?? '-' }}</div>
    <div class="xsmall">{{ strtoupper($role) }}</div>
  </div>
</aside>

<!-- ===== MOBILE NAV (drawer) ===== -->
<div class="mobile-nav" id="mobileNav" aria-hidden="true">
  <div class="mobile-nav-header">
    <button class="mobile-close" type="button" data-close-mobile-nav aria-label="Tutup">
      <span></span><span></span>
    </button>
    <div class="mobile-brand">
      <span class="brand-icon">📁</span>
      <strong>Serah Terima</strong>
    </div>
  </div>

  <nav class="mobile-menu">
    <a href="{{ route('dashboard') }}" class="mobile-link {{ request()->routeIs('dashboard')?'active':'' }}">
      <i class="ti ti-home"></i> Dashboard
    </a>

    @if (in_array($role, ['admin_internal','admin_komersial']))
      <a href="{{ route('admin.users.index') }}" class="mobile-link {{ request()->routeIs('admin.users.*')?'active':'' }}">
        <i class="ti ti-users"></i> Pengguna
      </a>
    @else
      <a href="{{ route('documents.index') }}" class="mobile-link {{ request()->routeIs('documents.index')?'active':'' }}">
        <i class="ti ti-file-description"></i> Data Dokumen
      </a>
      <a href="{{ route('documents.create') }}" class="mobile-link {{ request()->routeIs('documents.create')?'active':'' }}">
        <i class="ti ti-file-plus"></i> Input Dokumen
      </a>
    @endif

    <div class="mobile-sep"></div>

    <a href="#" class="mobile-link"
       onclick="event.preventDefault(); document.getElementById('logoutFormMobile').requestSubmit();">
      <i class="ti ti-logout"></i> Logout
    </a>
    <form id="logoutFormMobile" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
  </nav>
</div>

<div class="nav-backdrop" id="navBackdrop" hidden></div>

<!-- ===== SCRIPTS ===== -->
<script>
/* Profile dropdown */
(function(){
  const toggle = document.getElementById('userToggle');
  const menu   = document.getElementById('userMenu');
  const open = ()=>{ menu?.classList.add('open'); menu?.setAttribute('aria-hidden','false'); toggle?.setAttribute('aria-expanded','true'); };
  const close= ()=>{ menu?.classList.remove('open'); menu?.setAttribute('aria-hidden','true'); toggle?.setAttribute('aria-expanded','false'); };
  toggle?.addEventListener('click', e=>{ e.stopPropagation(); menu?.classList.contains('open')?close():open(); });
  document.addEventListener('click', e=>{ if(menu && toggle && !menu.contains(e.target) && !toggle.contains(e.target)) close(); });
  document.addEventListener('keydown', e=>{ if(e.key==='Escape') close(); });
})();

/* Drawer mobile */
(function(){
  const btn = document.getElementById('btnMobileNav');
  const mobileNav = document.getElementById('mobileNav');
  const closeBtn = document.querySelector('[data-close-mobile-nav]');
  const backdrop = document.getElementById('navBackdrop');

  function openNav(){
    document.body.classList.add('nav-open');
    btn?.classList.add('active'); btn?.setAttribute('aria-expanded','true');
    mobileNav?.classList.add('open'); mobileNav?.setAttribute('aria-hidden','false');
    backdrop.hidden = false;
  }
  function closeNav(){
    document.body.classList.remove('nav-open');
    btn?.classList.remove('active'); btn?.setAttribute('aria-expanded','false');
    mobileNav?.classList.remove('open'); mobileNav?.setAttribute('aria-hidden','true');
    backdrop.hidden = true;
  }

  btn?.addEventListener('click', ()=>document.body.classList.contains('nav-open')?closeNav():openNav());
  closeBtn?.addEventListener('click', closeNav);
  backdrop?.addEventListener('click', closeNav);
  document.addEventListener('keydown', e=>{ if(e.key==='Escape') closeNav(); });
  window.addEventListener('resize', ()=>{ if (window.innerWidth >= 1024) closeNav(); });
})();
</script>

<!-- ===== STYLES ===== -->
<style>
  :root{
    --aside-w:260px;
    --topbar-h:60px;
    --topbar-bg:#0f172a;
    --topbar-fg:#e5e7eb;
    --topbar-fg-dim:#cbd5e1;
    --accent:#3b82f6;
    --side-bg:#0b1220;
  }

  /* Layout offsets */
  .sidebar{
    position:fixed; top:0; left:0; bottom:0; width:var(--aside-w);
    padding:12px; background:var(--side-bg); color:#e5e7eb;
    border-right:1px solid rgba(255,255,255,.1); display:flex; flex-direction:column; gap:.5rem; z-index:1020;
  }
  .page-content{ margin-left:var(--aside-w); padding-top:calc(var(--topbar-h) + 14px); }

  /* TOPBAR */
  .topbar{
    position:fixed; top:0; left:var(--aside-w); right:0; z-index:1030;
    height:var(--topbar-h); display:flex; align-items:center; justify-content:space-between;
    background:var(--topbar-bg); color:var(--topbar-fg);
    padding:0 .75rem; border-bottom:1px solid rgba(255,255,255,.08);
    transition:left .3s ease;
  }
  .topbar-left{ display:flex; align-items:center; gap:.75rem; min-width:0; }
  .brand{ display:flex; align-items:center; gap:.5rem; text-decoration:none; color:inherit; }
  .brand.side{ margin-bottom:.5rem; }
  .brand-icon{ width:32px; height:32px; display:grid; place-items:center; background:#111827; border:1px solid rgba(255,255,255,.08); border-radius:8px; }
  .breadcrumb-mini{ margin-left:.5rem; display:flex; align-items:center; gap:.5rem; color:var(--topbar-fg-dim); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
  .breadcrumb-mini a{ color:var(--topbar-fg); text-decoration:none; border-bottom:1px dashed transparent; }
  .breadcrumb-mini a:hover{ border-bottom-color:var(--topbar-fg); }
  .breadcrumb-mini .current{ opacity:.85; }

  .hamburger{ display:none; background:transparent; border:1px solid rgba(255,255,255,.14); width:40px; height:36px; border-radius:8px; cursor:pointer; padding:0; }
  .hamburger span{ display:block; width:20px; height:2px; margin:4px auto; background:#fff; border-radius:2px; transition:.3s; }
  .hamburger.active span:nth-child(1){ transform: translateY(6px) rotate(45deg); }
  .hamburger.active span:nth-child(2){ opacity:0; }
  .hamburger.active span:nth-child(3){ transform: translateY(-6px) rotate(-45deg); }

  .topbar-right{ display:flex; align-items:center; gap:8px; }
  .user-dropdown{ position:relative; }
  .user-avatar{ display:flex; align-items:center; gap:.5rem; background:rgba(255,255,255,.06); color:#fff; border:1px solid rgba(255,255,255,.12); height:38px; border-radius:999px; padding:0 .6rem; cursor:pointer; }
  .avatar-img{ width:28px; height:28px; border-radius:50%; object-fit:cover; border:1px solid rgba(255,255,255,.2); }
  .user-name{ max-width:150px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; font-size:.9rem; color:#fff; opacity:.95; }
  .chevron{ font-size:14px; opacity:.8; }
  .user-menu{ display:none; position:absolute; right:0; top:120%; min-width:230px; background:#111827; border:1px solid rgba(255,255,255,.12); border-radius:12px; overflow:hidden; box-shadow:0 12px 24px rgba(0,0,0,.35); }
  .user-menu.open{ display:block; }
  .user-info{ display:flex; gap:.6rem; align-items:center; padding:.75rem; background:#0b1220; border-bottom:1px solid rgba(255,255,255,.08); }
  .avatar-img-sm{ width:36px; height:36px; border-radius:50%; border:1px solid rgba(255,255,255,.18); object-fit:cover; }
  .user-meta .nm{ color:#fff; font-weight:600; line-height:1.1; }
  .user-meta .em{ color:#9ca3af; font-size:.85rem; }
  .user-item{ width:100%; display:flex; align-items:center; gap:.5rem; padding:.6rem .8rem; color:#e5e7eb; text-decoration:none; background:none; outline:none; border:none; cursor:pointer; font:inherit; text-align:left; }
  .user-item:hover{ background:rgba(59,130,246,.12); color:#fff; }
  .btn-logout{ color:#fca5a5; }
  .btn-logout:hover{ background:rgba(239,68,68,.12); color:#fecaca; }
  .user-sep{ height:1px; background:rgba(255,255,255,.08); margin:.25rem 0; }

  /* SIDEBAR NAV */
  .sidebar .nav-link{
    display:flex; align-items:center; gap:.5rem;
    padding:.6rem .8rem; border-radius:12px;
    text-decoration:none; color:#cbd5e1; background:transparent;
    transition:background .2s, color .2s;
  }
  .sidebar .nav-link:hover{ background:rgba(255,255,255,.06); color:#fff; }
  .sidebar .nav-link.active{ background:rgba(59,130,246,.18); color:#fff; }
  .sidebar .logout-link{ color:#fca5a5; }
  .sidebar .logout-link:hover{ background:rgba(239,68,68,.14); color:#fecaca; }
  .side-sep{ border-color: rgba(255,255,255,.14); margin: .75rem 0; }

  /* MOBILE */
  @media (max-width:1024px){
    .topbar{ left:0; }
    .hamburger{ display:block; }
    .breadcrumb-mini{ display:none; }
    .page-content{ margin-left:0; }
    .sidebar{ transform:translateX(-100%); }
  }

  /* MOBILE DRAWER */
  .mobile-nav{
    position:fixed; top:0; left:0; bottom:0; width:min(86vw, 320px); background:#0b1220; color:#e5e7eb;
    transform:translateX(-100%); transition:transform .3s ease; z-index:1040; overflow:auto;
    border-right:1px solid rgba(255,255,255,.12);
  }
  .mobile-nav.open{ transform:translateX(0); }
  .mobile-nav-header{ display:flex; align-items:center; justify-content:space-between; padding:.8rem .9rem; border-bottom:1px solid rgba(255,255,255,.08); }
  .mobile-close{ background:transparent; border:1px solid rgba(255,255,255,.14); width:36px; height:32px; border-radius:8px; cursor:pointer; }
  .mobile-close span{ display:block; width:18px; height:2px; background:#fff; margin:4px auto; }
  .mobile-brand{ display:flex; align-items:center; gap:.5rem; }
  .mobile-menu{ padding:.6rem; display:flex; flex-direction:column; gap:.2rem; }
  .mobile-link{ display:block; padding:.55rem .7rem; border-radius:10px; color:#e5e7eb; text-decoration:none; }
  .mobile-link:hover,.mobile-link.active{ background:rgba(59,130,246,.14); color:#fff; }
  .mobile-sep{ height:1px; background:rgba(255,255,255,.08); margin:.5rem 0; }

  /* BACKDROP */
  .nav-backdrop{ position:fixed; inset:0; background:rgba(0,0,0,.35); z-index:1035; opacity:0; pointer-events:none; transition:opacity .25s; }
  body.nav-open .nav-backdrop{ opacity:1; pointer-events:auto; }
  .nav-backdrop[hidden]{ display:none !important; }
</style>
