@php
  $user = auth()->user();
  $role = $user->role ?? 'user';
@endphp

<aside class="sidebar">
  <div class="brand">
    <div class="brand-icon">📄</div>
    <div class="brand-text">SerahTerima</div>
  </div>

  {{-- NAV --}}
  <nav class="nav flex-column gap-1">
    {{-- Dashboard --}}
    <a href="{{ route('dashboard') }}"
       class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
      <i class="ti ti-layout-dashboard me-2"></i> Dashboard
    </a>

    @if (in_array($role, ['admin_internal', 'admin_komersial']))
      {{-- Admin: Users --}}
      <a href="{{ route('admin.users.index') }}"
         class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
        <i class="ti ti-users me-2"></i> Pengguna
      </a>
    @else
      {{-- User: Dokumen --}}
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

  {{-- Separator --}}
  <hr style="border-color: rgba(255,255,255,.14)" class="my-3">

  {{-- User quick actions --}}
  <div class="small text-white-50 mb-2">Akun</div>
  <form action="{{ route('logout') }}" method="POST"
        onsubmit="return confirm('Keluar dari aplikasi?')">
    @csrf
    <button type="submit" class="nav-link w-100 text-start" style="background:none;border:0;">
      <i class="ti ti-logout me-2"></i> Logout
    </button>
  </form>

  {{-- Footer info --}}
  <div class="mt-auto small text-white-50" style="opacity:.7">
    <div>{{ $user->name ?? '-' }}</div>
    <div class="xsmall">{{ strtoupper($role) }}</div>
  </div>
</aside>
