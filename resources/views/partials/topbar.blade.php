@php
  $user   = auth()->user();
  $role   = $user->role ?? 'user';
  $avatar = 'https://cdn-icons-png.flaticon.com/512/3177/3177440.png';
@endphp

{{-- 1x di layout: global logout form --}}
<form id="logoutFormGlobal" action="{{ route('logout') }}" method="POST" class="d-none">
  @csrf
</form>

<header class="topbar-sea" role="banner">
  <div class="tb-left">
    {{-- Hamburger (mobile) --}}
    <button type="button" class="hamburger" id="btnMobileNav" aria-label="Buka menu" aria-controls="mobileNav" aria-expanded="false">
      <span></span><span></span><span></span>
    </button>

    {{-- Brand / Breadcrumb --}}
    <a href="{{ route('dashboard') }}" class="brand" aria-label="Beranda">
      <span class="brand-badge">🚢</span>
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

  <div class="tb-right">
    
    {{-- User dropdown --}}
    <div class="user-dropdown">
      <button type="button" class="user-avatar" id="userToggle" aria-haspopup="true" aria-expanded="false" aria-controls="userMenu">
        <img src="{{ $avatar }}" alt="avatar" class="avatar-img">
        <span class="user-name">{{ \Illuminate\Support\Str::limit($user->name ?? 'Pengguna', 20) }}</span>
        <i class="ti ti-chevron-down chevron"></i>
      </button>

      <div class="user-menu glass" id="userMenu" role="menu" aria-hidden="true">
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

        <a href="#" class="user-item danger btn-logout" role="menuitem" data-logout>
          <i class="ti ti-logout"></i> Logout
        </a>
      </div>
    </div>
  </div>
</header>

{{-- ===== MOBILE NAV (drawer) ===== --}}
<div class="mobile-nav-sea" id="mobileNav" aria-hidden="true">
  <div class="mobile-nav-header">
    <button class="mobile-close" type="button" data-close-mobile-nav aria-label="Tutup">
      <span></span><span></span>
    </button>
    <div class="mobile-brand">
      <span class="brand-badge">🚢</span>
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

    <a href="#" class="mobile-link danger" data-logout>
      <i class="ti ti-logout"></i> Logout
    </a>
  </nav>
</div>

<div class="nav-backdrop" id="navBackdrop" hidden></div>

{{-- ===== JS: dropdown + drawer + logout ===== --}}
<script>
(function(){
  // Logout via form global
  const form = document.getElementById('logoutFormGlobal');
  document.querySelectorAll('[data-logout]').forEach(el=>{
    el.addEventListener('click', e => { e.preventDefault(); form?.requestSubmit(); });
  });

  // User dropdown
  const toggle = document.getElementById('userToggle');
  const menu   = document.getElementById('userMenu');
  function closeMenu(){ menu?.classList.remove('open'); menu?.setAttribute('aria-hidden','true'); toggle?.setAttribute('aria-expanded','false'); }
  function openMenu(){  menu?.classList.add('open');    menu?.setAttribute('aria-hidden','false'); toggle?.setAttribute('aria-expanded','true'); }
  toggle?.addEventListener('click', e=>{ e.stopPropagation(); menu?.classList.contains('open')?closeMenu():openMenu(); });
  document.addEventListener('click', e=>{ if(menu && toggle && !menu.contains(e.target) && !toggle.contains(e.target)) closeMenu(); });
  document.addEventListener('keydown', e=>{ if(e.key==='Escape') closeMenu(); });

  // Mobile drawer
  const btn = document.getElementById('btnMobileNav');
  const mobileNav = document.getElementById('mobileNav');
  const closeBtn = document.querySelector('[data-close-mobile-nav]');
  const backdrop = document.getElementById('navBackdrop');

  function openNav(){
    document.body.classList.add('nav-open');
    btn?.classList.add('active'); btn?.setAttribute('aria-expanded','true');
    mobileNav?.classList.add('open'); mobileNav?.setAttribute('aria-hidden','false');
    backdrop.hidden = false;
    closeMenu();
  }
  function closeNav(){
    document.body.classList.remove('nav-open');
    btn?.classList.remove('active'); btn?.setAttribute('aria-expanded','false');
    mobileNav?.classList.remove('open'); mobileNav?.setAttribute('aria-hidden','true');
    backdrop.hidden = true;
  }

  closeNav(); // default tertutup
  btn?.addEventListener('click', ()=>document.body.classList.contains('nav-open')?closeNav():openNav());
  closeBtn?.addEventListener('click', closeNav);
  backdrop?.addEventListener('click', closeNav);
  window.addEventListener('resize', ()=>{ if (window.innerWidth >= 1024) closeNav(); });
})();
</script>

{{-- ===== CSS: PELNI Blue-Sea, nyatu dengan sidebar ===== --}}
<style>
  :root{
    --aside-w:260px;              /* sinkron dg sidebar */
    --topbar-h:60px;

    --blue-900:#062a5d;
    --blue-700:#0b3d91;
    --blue-500:#1e88e5;
    --aqua:#35b8ff;
    --gold:#ffd54f;

    --fg:#e6f2ff;
    --fg-dim:#cfe0ff;
    --border:rgba(255,255,255,.14);
    --glass:rgba(255,255,255,.10);
  }

  /* TOPBAR */
  .topbar-sea{
    position:fixed; top:0; left:var(--aside-w); right:0; height:var(--topbar-h);
    display:flex; align-items:center; justify-content:space-between; gap:12px;
    padding:0 12px;
    color:var(--fg);
    background:
      radial-gradient(600px 300px at 110% -60%, rgba(53,184,255,.20), transparent 60%),
      linear-gradient(180deg, var(--blue-900), var(--blue-700));
    border-bottom:1px solid var(--border);
    z-index:1030;
  }
  @media (max-width:1024px){ .topbar-sea{ left:0 } }

  .tb-left{ display:flex; align-items:center; gap:.75rem; min-width:0 }
  .tb-right{ display:flex; align-items:center; gap:.5rem }

  .brand{ display:flex; align-items:center; gap:.5rem; text-decoration:none; color:#fff }
  .brand-badge{
    width:36px; height:36px; display:grid; place-items:center; border-radius:10px;
    background:linear-gradient(135deg, var(--gold), #ffe88a);
    color:var(--blue-900); font-weight:800; box-shadow:0 6px 18px rgba(0,0,0,.2);
  }

  .breadcrumb-mini{ color:var(--fg-dim); display:flex; gap:.5rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis }
  .breadcrumb-mini a{ color:#fff; text-decoration:none; border-bottom:1px dashed transparent }
  .breadcrumb-mini a:hover{ border-bottom-color:#fff }

  /* Hamburger */
  .hamburger{ display:none; width:40px; height:36px; border-radius:9px; border:1px solid var(--border); background:transparent; cursor:pointer }
  .hamburger span{ display:block; width:20px; height:2px; margin:4px auto; background:#fff; border-radius:2px }
  @media (max-width:1024px){ .hamburger{ display:block } }

  /* User dropdown */
  .user-dropdown{ position:relative }
  .user-avatar{
    display:flex; align-items:center; gap:.5rem; height:38px; padding:0 .6rem; cursor:pointer;
    border:1px solid var(--border); border-radius:999px;
    background:linear-gradient(to bottom right, rgba(255,255,255,.14), rgba(255,255,255,.06));
    color:#fff;
    backdrop-filter: blur(6px);
  }
  .avatar-img{ width:28px; height:28px; border-radius:50%; object-fit:cover; border:1px solid rgba(255,255,255,.25) }
  .user-name{ max-width:150px; font-size:.9rem; overflow:hidden; text-overflow:ellipsis; white-space:nowrap }
  .chevron{ font-size:14px; opacity:.85 }

  .glass{
    background:linear-gradient(to bottom right, rgba(15,23,42,.96), rgba(15,23,42,.92));
    border:1px solid rgba(255,255,255,.12);
    border-radius:14px;
    box-shadow:0 12px 26px rgba(0,0,0,.35), inset 0 1px 0 rgba(255,255,255,.08);
    backdrop-filter: blur(10px);
  }

  .user-menu{ display:none; position:absolute; right:0; top:120%; min-width:240px; overflow:hidden }
  .user-menu.open{ display:block }
  .user-info{ display:flex; gap:.6rem; align-items:center; padding:.75rem; border-bottom:1px solid var(--border) }
  .avatar-img-sm{ width:36px; height:36px; border-radius:50%; border:1px solid rgba(255,255,255,.18); object-fit:cover }
  .user-meta .nm{ color:#fff; font-weight:700; line-height:1.1 }
  .user-meta .em{ color:#93a4bf; font-size:.85rem }
  .user-item{ display:flex; align-items:center; gap:.6rem; padding:.6rem .8rem; color:#e5e7eb; text-decoration:none }
  .user-item:hover{ background:rgba(59,130,246,.14); color:#fff }
  .user-item.danger, .mobile-link.danger{ color:#ffbcbc }
  .user-item.danger:hover{ background:rgba(239,68,68,.14); color:#ffe1e1 }

  .user-sep{ height:1px; background:var(--border); margin:.25rem 0 }

  /* ===== Mobile drawer ===== */
  .mobile-nav-sea{
    position:fixed; top:0; left:0; bottom:0; width:min(86vw,320px);
    background:
      radial-gradient(700px 400px at -20% 120%, rgba(53,184,255,.22), transparent 60%),
      linear-gradient(180deg, var(--blue-900), var(--blue-700));
    color:#e6f2ff;
    border-right:1px solid var(--border);
    transform:translateX(-100%); transition:transform .3s ease; z-index:1040; overflow:auto;
  }
  .mobile-nav-sea.open{ transform:translateX(0) }

  .mobile-nav-header{ display:flex; align-items:center; justify-content:space-between; padding:.8rem .9rem; border-bottom:1px solid var(--border) }
  .mobile-brand{ display:flex; align-items:center; gap:.5rem }
  .mobile-brand .brand-badge{ width:36px; height:36px; border-radius:10px; display:grid; place-items:center;
    background:linear-gradient(135deg, var(--gold), #ffe88a); color:var(--blue-900); font-weight:800 }

  .mobile-close{ background:transparent; border:1px solid var(--border); width:36px; height:32px; border-radius:8px; cursor:pointer }
  .mobile-close span{ display:block; width:18px; height:2px; background:#fff; margin:4px auto }

  .mobile-menu{ padding:.6rem; display:flex; flex-direction:column; gap:.2rem }
  .mobile-link{ display:block; padding:.55rem .7rem; border-radius:10px; color:#e5e7eb; text-decoration:none }
  .mobile-link:hover,.mobile-link.active{ background:rgba(59,130,246,.18); color:#fff }
  .mobile-sep{ height:1px; background:var(--border); margin:.6rem 0 }

  /* Backdrop */
  .nav-backdrop{ position:fixed; inset:0; background:rgba(0,0,0,.35); z-index:1035; opacity:0; pointer-events:none; transition:opacity .25s }
  body.nav-open .nav-backdrop{ opacity:1; pointer-events:auto }
  .nav-backdrop[hidden]{ display:none !important }
</style>