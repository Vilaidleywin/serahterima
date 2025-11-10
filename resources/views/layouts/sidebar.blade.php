@php
  $user = auth()->user();
  $role = $user->role ?? 'user';
@endphp

<aside class="sidebar" role="navigation" aria-label="Sidebar">
  <div class="brand side">
    <div class="brand-icon">📄dasd</div>
    <div class="brand-text">SerahTerima</div>
  </div>

  <nav class="nav flex-column gap-1">
    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
      <i class="ti ti-layout-dashboard me-2"></i> Dashboard
    </a>

    @if (in_array($role, ['admin_internal','admin_komersial']))
      <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
        <i class="ti ti-users me-2"></i> Pengguna
      </a>
    @else
      <a href="{{ route('documents.index') }}" class="nav-link {{ request()->routeIs('documents.index') ? 'active' : '' }}">
        <i class="ti ti-folder me-2"></i> Data Dokumen
      </a>
      <a href="{{ route('documents.create') }}" class="nav-link {{ request()->routeIs('documents.create') ? 'active' : '' }}">
        <i class="ti ti-file-plus me-2"></i> Input Dokumen
      </a>
    @endif
  </nav>

  <hr class="side-sep">

  <div class="small text-white-50 mb-2">Akun</div>

  <a href="#" class="nav-link logout-link" data-logout>
    <i class="ti ti-logout me-2"></i> Logout
  </a>

  <div class="mt-auto small text-white-50" style="opacity:.7">
    <div>{{ $user->name ?? '-' }}</div>
    <div class="xsmall">{{ strtoupper($role) }}</div>
  </div>
</aside>

{{-- CSS khusus SIDEBAR --}}
<style>
  :root{ --aside-w:260px; --side-bg:#0b1220; --topbar-h:60px; }

  .sidebar{
    position:fixed; top:0; left:0; bottom:0; width:var(--aside-w);
    padding:12px; background:var(--side-bg); color:#e5e7eb;
    border-right:1px solid rgba(255,255,255,.1);
    display:flex; flex-direction:column; gap:.5rem; z-index:1020;
  }

  /* offset konten (tambahkan class page-content pada wrapper utama) */
  .page-content{ margin-left:var(--aside-w); padding-top:calc(var(--topbar-h) + 14px); }

  .brand.side{ display:flex; align-items:center; gap:.5rem; margin-bottom:.5rem; }
  .brand-icon{ width:32px; height:32px; display:grid; place-items:center; background:#111827; border:1px solid rgba(255,255,255,.08); border-radius:8px; }

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

  .side-sep{ border-color: rgba(255,255,255,.14); margin:.75rem 0; }

  @media (max-width:1024px){
    .page-content{ margin-left:0; }
    .sidebar{ transform:translateX(-100%); } /* sidebar tetap tersembunyi di mobile; gunakan mobile drawer dari topbar */
  }
</style>
