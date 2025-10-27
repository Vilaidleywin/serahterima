<header class="appbar">
  {{-- Kiri: tombol hamburger --}}
  <button type="button" class="appbar-btn hamburger" data-toggle-mobile-nav aria-label="Toggle sidebar">
    <span></span><span></span><span></span>
  </button>

  {{-- Tengah: logo/brand --}}
  <div class="appbar-brand">
    <div class="brand-icon">📁</div>
    <strong>Serah Terima</strong>
  </div>

  {{-- Kanan: profil user --}}
  <div class="user-dropdown">
    <button type="button" class="user-chip" id="userToggle" aria-haspopup="true" aria-expanded="false">
      <i class="ti ti-user"></i>
      <span>Profil</span>
      <i class="ti ti-chevron-down chev"></i>
    </button>

    <div class="user-menu" role="menu" aria-label="User menu">
      <a href="#" class="user-item" role="menuitem"><i class="ti ti-settings"></i> Settings</a>
      <div class="user-sep"></div>
      <a href="#" class="user-item" role="menuitem"><i class="ti ti-logout"></i> Logout</a>
    </div>
  </div>
</header>

{{-- Breadcrumb opsional --}}
@if(isset($breadcrumb))
  <div class="breadcrumb-wrap">
    <nav class="breadcrumb-mini">
      @foreach($breadcrumb as $i => $item)
        @if(!empty($item['url']) && $i < count($breadcrumb)-1)
          <a href="{{ $item['url'] }}">{{ $item['label'] }}</a>
          <span class="sep">/</span>
        @else
          <span class="current">{{ $item['label'] }}</span>
        @endif
      @endforeach
    </nav>
  </div>
@endif
