<header class="topbar">
  <div class="topbar-left">
    {{-- Tombol hamburger (muncul di layar kecil & medium) --}}
    <button type="button" class="hamburger" id="btnMobileNav" aria-label="Buka menu">
      <span></span><span></span><span></span>
    </button>

    {{-- Logo / Brand --}}
    <div class="brand">
      <div class="brand-icon">📁</div>
      <strong>Serah Terima</strong>
    </div>

    {{-- Breadcrumb opsional --}}
    @if(isset($breadcrumb))
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
    @endif
  </div>

  {{-- Profil user --}}
  <div class="topbar-right">
    <div class="user-dropdown">
      <button type="button" class="user-chip" id="userToggle">
        <i class="ti ti-user"></i>
        <span>Profil</span>
        <i class="ti ti-chevron-down chev"></i>
      </button>
      <div class="user-menu" id="userMenu">
        <a href="#" class="user-item"><i class="ti ti-settings"></i> Settings</a>
        <div class="user-sep"></div>
        <a href="#" class="user-item"><i class="ti ti-logout"></i> Logout</a>
      </div>
    </div>
  </div>
</header>

<!-- ===== MOBILE NAV (untuk layar kecil) ===== -->
<div class="mobile-nav" id="mobileNav" aria-hidden="true">
  <div class="mobile-nav-header">
    <button class="mobile-close" type="button" data-close-mobile-nav aria-label="Close">
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
    <a href="{{ route('documents.index') }}" class="mobile-link {{ request()->routeIs('documents.*')?'active':'' }}">
      <i class="ti ti-file-description"></i> Data Dokumen
    </a>
    <a class="mobile-link disabled"><i class="ti ti-file-plus"></i> Input Dokumen</a>
    <a class="mobile-link disabled"><i class="ti ti-report"></i> Laporan</a>
    <a class="mobile-link disabled"><i class="ti ti-users"></i> Pengguna</a>
    <a class="mobile-link disabled"><i class="ti ti-logout"></i> Logout</a>
  </nav>
</div>

<script>
  // === Dropdown profil ===
  const toggle = document.getElementById('userToggle');
  const menu = document.getElementById('userMenu');
  toggle?.addEventListener('click', () => {
    menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
  });
  window.addEventListener('click', (e) => {
    if (!toggle.contains(e.target) && !menu.contains(e.target)) {
      menu.style.display = 'none';
    }
  });

  // === Buka / Tutup Mobile Nav ===
  const btnMobileNav = document.getElementById('btnMobileNav');
  const mobileNav = document.getElementById('mobileNav');
  const mobileClose = document.querySelector('[data-close-mobile-nav]');

  btnMobileNav?.addEventListener('click', () => {
    btnMobileNav.classList.toggle('active');
    mobileNav.classList.toggle('open');
  });

  mobileClose?.addEventListener('click', () => {
    btnMobileNav.classList.remove('active');
    mobileNav.classList.remove('open');
  });

  mobileNav?.addEventListener('click', (e) => {
    if (e.target === mobileNav) {
      btnMobileNav.classList.remove('active');
      mobileNav.classList.remove('open');
    }
  });
</script>

<style>
/* ===== TOPBAR ===== */
.topbar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  background: #1e293b;
  color: #fff;
  padding: 0.6rem 1.2rem;
  box-shadow: 0 2px 6px rgba(0,0,0,0.1);
  position: fixed;
  top: 0;
  left: 260px;
  right: 0;
  z-index: 1030;
  height: 56px;
  transition: left 0.3s ease;
}
.topbar-left { display: flex; align-items: center; gap: 1rem; }

/* ===== HAMBURGER ===== */
.hamburger {
  background: rgba(255,255,255,0.08);
  border: 1px solid rgba(255,255,255,0.15);
  display: none;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  gap: 4px;
  cursor: pointer;
  border-radius: 6px;
  width: 38px;
  height: 34px;
  transition: all 0.3s ease;
  position: relative;
}
.hamburger span {
  display: block;
  width: 20px;
  height: 2px;
  background: #fff;
  border-radius: 2px;
  transition: all 0.3s ease;
}
.hamburger:hover { background: rgba(255,255,255,0.15); }
.hamburger.active span:nth-child(1) { transform: translateY(6px) rotate(45deg); }
.hamburger.active span:nth-child(2) { opacity: 0; }
.hamburger.active span:nth-child(3) { transform: translateY(-6px) rotate(-45deg); }

/* ===== PROFIL ===== */
.topbar-right { display: flex; align-items: center; gap: 1rem; }
.user-dropdown { position: relative; }
.user-chip { background: none; border: none; color: #fff; cursor: pointer; display: flex; align-items: center; gap: 6px; }
.user-menu {
  display: none;
  position: absolute;
  right: 0;
  top: 110%;
  background: #334155;
  border-radius: 6px;
  padding: 0.5rem 0;
  box-shadow: 0 4px 10px rgba(0,0,0,0.3);
}
.user-item { display: block; padding: 6px 12px; color: #fff; text-decoration: none; }
.user-item:hover { background: #475569; }
.user-sep { height: 1px; background: #475569; margin: 4px 0; }

/* ===== MAIN ===== */
.content { margin-left: 260px; padding: 20px; padding-top: 80px; transition: margin-left 0.3s ease; }

/* ===== MOBILE ===== */
@media (max-width: 1024px) {
  .topbar { left: 0; }
  .hamburger { display: flex; }
  .content { margin-left: 0; }

  .mobile-nav {
    position: fixed !important;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(0,0,0,0.55);
    backdrop-filter: blur(6px);
    z-index: 99999 !important;
    opacity: 0;
    pointer-events: none;
    transform: translateY(-10px);
    transition: all 0.35s ease;
  }
  .mobile-nav.open { opacity: 1; pointer-events: auto; transform: translateY(0); }

  .mobile-nav-header {
    position: relative;
    background: #111827;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 14px 18px;
    border-bottom: 1px solid rgba(255,255,255,0.08);
    box-shadow: 0 2px 10px rgba(0,0,0,0.4);
  }

  /* Tombol close pindah ke kiri */
  .mobile-close {
    position: absolute;
    left: 14px;
    top: 10px;
    background: #1e293b;
    border: 1px solid rgba(255,255,255,0.15);
    border-radius: 8px;
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    z-index: 1000001;
    transition: transform 0.25s ease, background 0.25s ease;
    box-shadow: 0 0 10px rgba(0,0,0,0.4);
  }

  /* Garis X */
  .mobile-close span {
    position: absolute;
    width: 18px;
    height: 2px;
    background: #fff;
    border-radius: 2px;
  }
  .mobile-close span:nth-child(1) { transform: rotate(45deg); }
  .mobile-close span:nth-child(2) { transform: rotate(-45deg); }

  .mobile-close:hover { background: #273244; transform: scale(1.1); }

  .mobile-menu {
    background: #1e293b;
    padding: 12px 0;
    height: calc(100% - 56px);
    overflow-y: auto;
    animation: fadeUp 0.4s ease;
  }
  .mobile-link {
    display: block;
    color: #fff;
    text-decoration: none;
    padding: 12px 18px;
    opacity: 0;
    animation: fadeIn 0.4s forwards;
  }
  .mobile-link:hover,
  .mobile-link.active { background: #334155; }

  @keyframes fadeUp {
    from {opacity: 0; transform: translateY(10px);}
    to {opacity: 1; transform: translateY(0);}
  }
  @keyframes fadeIn {
    from {opacity: 0;}
    to {opacity: 1;}
  }
}
</style>
