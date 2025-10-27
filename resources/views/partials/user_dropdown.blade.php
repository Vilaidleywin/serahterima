<div class="user-dropdown" data-user-dropdown>
  <button class="user-chip" type="button" aria-haspopup="true" aria-expanded="false">
    <i class="ti ti-user"></i>
    <span>Admin</span>
    <i class="ti ti-chevron-down chev"></i>
  </button>

  <div class="user-menu" role="menu" aria-label="User menu">
    <a href="{{ route('dashboard') }}" class="user-item" role="menuitem">
      <i class="ti ti-layout-grid"></i> Dashboard
    </a>
    <a href="{{ route('documents.index') }}" class="user-item" role="menuitem">
      <i class="ti ti-files"></i> Data Dokumen
    </a>
    <div class="user-sep"></div>
    <a href="#" class="user-item" role="menuitem">
      <i class="ti ti-settings"></i> Settings
    </a>
    {{-- Tambah logout nanti di sini --}}
  </div>
</div>
