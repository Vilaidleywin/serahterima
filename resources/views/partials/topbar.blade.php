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

  // Toggle buka/tutup mobile nav + animasi hamburger
  btnMobileNav?.addEventListener('click', () => {
    btnMobileNav.classList.toggle('active'); // <- buat animasi jadi "X"
    mobileNav.classList.toggle('open');
  });

  // Tombol close (X di header mobile nav)
  mobileClose?.addEventListener('click', () => {
    btnMobileNav.classList.remove('active');
    mobileNav.classList.remove('open');
  });

  // Klik area luar = tutup menu
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

/* Left section */
.topbar-left {
  display: flex;
  align-items: center;
  gap: 1rem;
}

/* ===== HAMBURGER BUTTON ===== */
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

/* Hover & Active */
.hamburger:hover {
  background: rgba(255,255,255,0.15);
  box-shadow: 0 0 6px rgba(255,255,255,0.25);
}
.hamburger:active span {
  transform: scaleX(0.9);
}

/* Animation: turn into X */
.hamburger.active span:nth-child(1) {
  transform: translateY(6px) rotate(45deg);
}
.hamburger.active span:nth-child(2) {
  opacity: 0;
}
.hamburger.active span:nth-child(3) {
  transform: translateY(-6px) rotate(-45deg);
}

/* ===== BRAND ===== */
.brand {
  display: flex;
  align-items: center;
  gap: 0.4rem;
  font-weight: 600;
  font-size: 1rem;
}
.brand-icon {
  font-size: 1.2rem;
}

/* ===== BREADCRUMB ===== */
.breadcrumb-mini {
  display: flex;
  align-items: center;
  gap: 0.3rem;
  font-size: 0.85rem;
  color: #cbd5e1;
  border-left: 1px solid #334155;
  padding-left: 0.8rem;
}
.breadcrumb-mini a {
  color: #f9fafb;
  text-decoration: none;
}
.breadcrumb-mini a:hover {
  text-decoration: underline;
}

/* ===== PROFIL KANAN ===== */
.topbar-right {
  display: flex;
  align-items: center;
  gap: 1rem;
}
.user-dropdown {
  position: relative;
}
.user-chip {
  background: none;
  border: none;
  color: #fff;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 6px;
}
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
.user-item {
  display: block;
  padding: 6px 12px;
  color: #fff;
  text-decoration: none;
}
.user-item:hover {
  background: #475569;
}
.user-sep {
  height: 1px;
  background: #475569;
  margin: 4px 0;
}

/* ===== MAIN CONTENT ===== */
.content {
  margin-left: 260px;
  padding: 20px;
  padding-top: 80px;
  transition: margin-left 0.3s ease;
}

/* ===== MOBILE RESPONSIVE ===== */
@media (max-width: 1024px) {
  .topbar {
    left: 0;
  }

  .hamburger {
    display: flex;
  }

  .content {
    margin-left: 0;
  }

  /* === Mobile nav overlay === */
  .mobile-nav {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.45);
    backdrop-filter: blur(4px);
    z-index: 1050;
    opacity: 0;
    pointer-events: none;
    transform: translateY(-10px);
    transition: all 0.35s ease;
  }

  .mobile-nav.open {
    opacity: 1;
    pointer-events: auto;
    transform: translateY(0);
  }

  .mobile-nav-header {
    background: #1e293b;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 16px;
    animation: fadeDown 0.3s ease;
  }

  .mobile-menu {
    background: #273244;
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
  .mobile-link.active {
    background: #334155;
  }

  /* === Animations === */
  @keyframes fadeDown {
    from {opacity: 0; transform: translateY(-10px);}
    to {opacity: 1; transform: translateY(0);}
  }
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
