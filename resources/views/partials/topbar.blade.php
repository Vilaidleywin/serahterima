<header class="glpi-header" role="banner" aria-label="Topbar">
  <button id="btnMobileNav" class="glpi-burger" aria-label="Buka menu" aria-controls="mobileNav" aria-expanded="false">
    <span></span><span></span><span></span>
  </button>

  <a href="{{ url('/') }}" class="glpi-brand" aria-label="Beranda">Serah Terima</a>

  <div class="glpi-right"><!-- kosong (slot avatar kalau perlu) --></div>
</header>

<aside id="mobileNav" class="glpi-drawer" aria-hidden="true" tabindex="-1">
  <div class="glpi-drawer-head">
    <button id="btnCloseDrawer" class="glpi-close" aria-label="Tutup menu">✕</button>
    <div class="glpi-drawer-title">Serah Terima</div>
  </div>

  <nav class="glpi-drawer-menu" role="navigation" aria-label="Menu">
    <a href="{{ route('dashboard') }}" class="glpi-item"><i class="ti ti-home"></i> Home</a>
    <a href="{{ route('documents.index') }}" class="glpi-item"><i class="ti ti-folders"></i> Data Dokumen</a>
    <a href="{{ route('documents.create') }}" class="glpi-item"><i class="ti ti-plus"></i> Buat Dokumen</a>
    <a href="#" class="glpi-item"><i class="ti ti-file-analytics"></i> Laporan</a>
    <a href="#" class="glpi-item"><i class="ti ti-users"></i> Pengguna</a>
    <form method="POST" action="{{ route('logout') }}" class="m-0">@csrf
      <button type="submit" class="glpi-item"><i class="ti ti-logout"></i> Logout</button>
    </form>
  </nav>
</aside>
<div id="navBackdrop" class="glpi-backdrop" hidden></div>
