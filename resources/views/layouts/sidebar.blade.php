<aside class="sidebar bg-white border-end">
  <div class="sidebar-inner d-flex flex-column h-100">
    <div class="p-3 border-bottom d-none d-lg-block">
      <span class="fw-bold">Menu</span>
    </div>

    <nav class="nav flex-column p-2">
      <a class="nav-link sidebar-link" href="{{ route('dashboard') }}">
        <i class="bi bi-speedometer2 me-2"></i> Dashboard
      </a>
      <a class="nav-link sidebar-link" href="{{ route('documents.index') }}">
        <i class="bi bi-files me-2"></i> Data Dokumen
      </a>
      {{-- tambahkan menu lain di sini --}}
    </nav>

    <div class="mt-auto p-2 small text-muted d-none d-lg-block">
      v1.0
    </div>
  </div>

  {{-- Overlay untuk mobile --}}
  <div class="sidebar-backdrop"></div>
</aside>
