@php
  $user = auth()->user();
  $role = $user->role ?? 'user';
@endphp

<aside class="sidebar-sea" role="navigation" aria-label="Sidebar">
  {{-- Brand --}}
  <a class="brand" href="{{ route('dashboard') }}">
    <span class="brand-badge" aria-hidden="true">🚢</span>
    <span class="brand-text">SerahTerima</span>
  </a>

  {{-- Nav --}}
  <nav class="nav-list">
    <a href="{{ route('dashboard') }}"
       class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
      <span class="dot" aria-hidden="true"></span>
      <i class="ti ti-layout-dashboard"></i>
      <span>Dashboard</span>
    </a>

    @if (in_array($role, ['admin_internal','admin_komersial']))
      <a href="{{ route('admin.users.index') }}"
         class="nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
        <span class="dot" aria-hidden="true"></span>
        <i class="ti ti-users"></i>
        <span>Pengguna</span>
      </a>
    @else
      <a href="{{ route('documents.index') }}"
         class="nav-item {{ request()->routeIs('documents.index') ? 'active' : '' }}">
        <span class="dot" aria-hidden="true"></span>
        <i class="ti ti-folder"></i>
        <span>Data Dokumen</span>
      </a>
      <a href="{{ route('documents.create') }}"
         class="nav-item {{ request()->routeIs('documents.create') ? 'active' : '' }}">
        <span class="dot" aria-hidden="true"></span>
        <i class="ti ti-file-plus"></i>
        <span>Input Dokumen</span>
      </a>
    @endif
  </nav>

  <hr class="side-sep" role="separator">

  <div class="group-label">Akun</div>
  <a href="#" class="nav-item danger logout" data-logout>
    <span class="dot" aria-hidden="true"></span>
    <i class="ti ti-logout"></i>
    <span>Logout</span>
  </a>

  {{-- Account block --}}
  <div class="account">
    <div class="name">{{ $user->name ?? '-' }}</div>
    <div class="role">{{ strtoupper($role) }}</div>
  </div>
</aside>

{{-- ===== SIDEBAR THEME: PELNI BLUE-SEA ===== --}}
<style>
  :root{
    --aside-w: 260px;
    --blue-900:#062a5d;   /* navy */
    --blue-700:#0b3d91;   /* pelni navy */
    --blue-500:#1e88e5;   /* bright */
    --aqua:#35b8ff;
    --gold:#ffd54f;
    --text:#e6f2ff;
    --muted:#b9d4ff;
  }

  /* Layout offset untuk konten utama */
  .page-content{ margin-left:var(--aside-w); }
  @media (max-width: 1024px){
    .page-content{ margin-left:0 }
  }

  .sidebar-sea{
    position:fixed; inset:0 auto 0 0; width:var(--aside-w);
    background:
      radial-gradient(700px 400px at -20% 120%, rgba(53,184,255,.22), transparent 60%),
      linear-gradient(180deg, var(--blue-900), var(--blue-700));
    color:var(--text);
    border-right:1px solid rgba(255,255,255,.1);
    padding:14px;
    display:flex; flex-direction:column; gap:10px;
    z-index:1020;
  }

  /* Brand */
  .brand{ display:flex; align-items:center; gap:.65rem; text-decoration:none; color:#fff; }
  .brand-badge{
    width:42px; height:42px; display:grid; place-items:center;
    border-radius:12px; font-size:1.1rem; font-weight:700;
    background:linear-gradient(135deg, var(--gold), #ffe88a);
    color:var(--blue-900);
    box-shadow:0 10px 24px rgba(0,0,0,.2);
  }
  .brand-text{ font-weight:800; letter-spacing:.2px }

  /* Nav items */
  .nav-list{ display:flex; flex-direction:column; gap:6px; margin-top:4px }
  .nav-item{
    position:relative;
    display:flex; align-items:center; gap:.65rem;
    padding:.6rem .8rem .6rem .9rem;
    border-radius:14px;
    color:#d8e7ff; text-decoration:none;
    transition:background .2s, color .2s, transform .02s;
  }
  .nav-item i{ font-size:1.1rem; opacity:.95 }
  .nav-item .dot{
    width:6px; height:6px; border-radius:999px;
    background:transparent; margin-right:.1rem;
  }

  .nav-item:hover{
    background:rgba(255,255,255,.08);
    color:#fff;
  }
  .nav-item:active{ transform:translateY(1px) }

  /* Active state: glass highlight + indicator bar kiri */
  .nav-item.active{
    background:linear-gradient( to bottom right, rgba(255,255,255,.22), rgba(255,255,255,.12) );
    border:1px solid rgba(255,255,255,.18);
    box-shadow: inset 0 1px 0 rgba(255,255,255,.25);
    color:#fff;
  }
  .nav-item.active::before{
    content:""; position:absolute; left:-10px; top:10px; bottom:10px; width:4px;
    border-radius:8px;
    background:linear-gradient(180deg, var(--aqua), var(--blue-500));
    box-shadow:0 0 0 4px rgba(53,184,255,.18);
  }
  .nav-item.active .dot{ background:linear-gradient(135deg, var(--aqua), var(--blue-500)) }

  /* Separator & labels */
  .side-sep{ border-color:rgba(255,255,255,.16); margin:.8rem 0 }
  .group-label{ color:var(--muted); font-size:.8rem; text-transform:uppercase; letter-spacing:.08em; margin-bottom:.2rem }

  /* Danger/Logout */
  .nav-item.danger{ color:#ffd3d3 }
  .nav-item.danger:hover{ background:rgba(239,68,68,.14); color:#fff }

  /* Account block */
  .account{
    margin-top:auto; padding:.8rem; border-radius:12px;
    background:linear-gradient( to bottom right, rgba(255,255,255,.16), rgba(255,255,255,.08) );
    border:1px solid rgba(255,255,255,.16);
  }
  .account .name{ font-weight:700 }
  .account .role{
    display:inline-block; margin-top:4px; font-size:.72rem; letter-spacing:.06em;
    color:var(--blue-900); background:#ffe88a; border:1px solid #ffd861;
    padding:.12rem .4rem; border-radius:999px; font-weight:800;
  }

  /* Mobile: jadikan drawer (siapkan toggle di topbar kalau ada) */
  @media (max-width:1024px){
    .sidebar-sea{
      transform:translateX(-100%); transition:transform .25s ease;
    }
    body.sidebar-open .sidebar-sea{ transform:none }
  }
</style>
