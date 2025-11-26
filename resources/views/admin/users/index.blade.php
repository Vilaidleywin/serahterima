@extends('layouts.app')

@section('title', 'Pengguna')

@section('content')
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h2 class="fw-bold mb-0">Data Pengguna</h2>
      <div class="text-muted small">Kelola akun yang dapat mengakses aplikasi</div>

      @if(request()->filled('search'))
        <div class="mt-1 small text-primary">
          <strong>Filter aktif:</strong>
          @if(request('search'))
            cari: <span class="text-dark">{{ request('search') }}</span>;
          @endif
        </div>
      @endif
    </div>

    <div class="d-flex gap-2">
      <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
        <i class="ti ti-plus"></i> Tambah
      </a>
      <a href="{{ route('admin.users.index') }}" class="btn outline-secondary">
        <i class="ti ti-refresh"></i> Refresh
      </a>
    </div>
  </div>

  {{-- ALERT BOOTSTRAP DIHAPUS, BIAR PAKAI TOAST DARI app.blade --}}

  {{-- FILTER BAR --}}
  <form id="userFilterForm" method="GET" class="mb-3">
    <div class="row g-3 align-items-end">

      <div class="col-md-6">
        <label class="form-label mb-1 small text-muted">Pencarian</label>
        <div class="input-group">
          <input
            name="search"
            id="userSearchInput"
            value="{{ request('search') }}"
            class="form-control search"
            placeholder="Cari nama, username, email, atau divisi"
          >

          <button class="btn btn-primary" id="btnUserSearch" type="button">
            <i class="ti ti-search me-1"></i> Cari
          </button>

          <button class="btn btn-outline-secondary" id="btnUserReset" type="button">
            <i class="ti ti-filter-off me-1"></i> Reset
          </button>
        </div>
      </div>

      <div class="col-md-3"></div>
      <div class="col-md-3"></div>

    </div>
  </form>

  {{-- WRAPPER UNTUK AJAX --}}
  <div id="user-table-wrapper">
    @if($users->isEmpty())
      <div class="text-center text-muted py-5">
        <p class="mb-1">Anda belum mendaftarkan user sama sekali.</p>
        <a href="{{ route('admin.users.create') }}" class="btn btn-sm btn-primary mt-2">
          Tambah Pengguna Pertama
        </a>
      </div>
    @else
      <div class="card-soft p-2" style="border:1px solid #d9dee3; border-radius:10px; overflow:auto; max-height:600px;">
        <table class="table table-striped table-hover align-middle mb-0">
          <thead>
            <tr>
              <th style="width:60px">No</th>
              <th>Nama</th>
              <th>Username</th>
              <th>Email</th>
              <th>Divisi</th>
              <th>Status</th>
              <th>Role</th>
              <th style="width:220px" class="text-end">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @foreach($users as $i => $u)
              @php $rowNumber = ($users->firstItem() ?? 1) + $i; @endphp

              <tr>
                <td class="fw-semibold">{{ $rowNumber }}</td>
                <td>{{ $u->name }}</td>
                <td>{{ $u->username }}</td>
                <td>{{ $u->email }}</td>
                <td>{{ $u->division ?? '-' }}</td>

                <td>
                  @if($u->is_active)
                    <span class="badge-status active">Aktif</span>
                  @else
                    <span class="badge-status inactive">Nonaktif</span>
                  @endif
                </td>

                <td><span class="badge badge-role">{{ $u->role }}</span></td>

                <td class="text-end">
                  @if(!in_array($u->role, ['admin','admin_internal','admin_komersial']))
                    <form action="{{ route('admin.users.toggle-status', $u) }}" method="POST" class="d-inline">
                      @csrf @method('PATCH')
                      <button class="btn btn-sm btn-outline-warning mb-1">
                        {{ $u->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                      </button>
                    </form>

                    <a href="{{ route('admin.users.edit', $u) }}" class="btn btn-sm btn-outline-secondary mb-1">
                      Edit
                    </a>

                    <button class="btn btn-sm btn-outline-danger mb-1"
                      onclick="confirmDelete({{ $u->id }}, @js($u->name.' ('.$u->username.')'))">
                      Hapus
                    </button>

                    <form id="delete-form-{{ $u->id }}" action="{{ route('admin.users.destroy', $u) }}" method="POST" class="d-inline">
                      @csrf @method('DELETE')
                    </form>
                  @else
                    <span class="text-muted small">Admin</span>
                  @endif
                </td>
              </tr>

            @endforeach
          </tbody>
        </table>
      </div>

      <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2 py-2 px-1"
        style="border-top:1px solid #e3e8ef; font-size:14px; color:#6b7280;">

        <form method="get" class="d-inline-flex align-items-center gap-2 mb-0">
          <input type="hidden" name="search" value="{{ request('search') }}">
          <select name="per_page" class="form-select form-select-sm" style="width:90px" onchange="this.form.submit()">
            @foreach([10, 15, 25, 50] as $n)
              <option value="{{ $n }}" @selected(($per_page ?? 15) == $n)>{{ $n }}</option>
            @endforeach
          </select>
          <span>rows / page</span>
        </form>

        <div class="text-muted small flex-grow-1 text-center mb-0">
          Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} rows
        </div>

        <div class="mb-0">
          {{ $users->links() }}
        </div>
      </div>
    @endif
  </div>
@endsection

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", () => {
  const filterForm  = document.getElementById("userFilterForm");
  const tableWrap   = document.getElementById("user-table-wrapper");
  const btnSearch   = document.getElementById("btnUserSearch");
  const btnReset    = document.getElementById("btnUserReset");
  const inputSearch = document.getElementById("userSearchInput");

  if (!filterForm || !tableWrap) return;

  function buildQuery() {
    const params = new URLSearchParams(new FormData(filterForm)).toString();
    return "{{ route('admin.users.index') }}" + (params ? `?${params}` : "");
  }

  function renderSkeleton() {
    tableWrap.innerHTML = `
      <div class="card-soft p-3" style="border:1px solid #d9dee3; border-radius:10px;">
        <div class="skeleton skeleton-title"></div>
        <div class="skeleton skeleton-row"></div>
        <div class="skeleton skeleton-row"></div>
        <div class="skeleton skeleton-row"></div>
        <div class="skeleton skeleton-row"></div>
      </div>
    `;
  }

  function loadTable(url) {
    tableWrap.classList.add('is-loading');
    tableWrap.style.transition = 'opacity .2s ease, transform .2s ease';
    tableWrap.style.opacity = '0';
    tableWrap.style.transform = 'translateY(6px)';

    setTimeout(() => {
      renderSkeleton();

      fetch(url, { headers: { "X-Requested-With": "XMLHttpRequest" }})
        .then(res => res.text())
        .then(html => {
          const doc   = new DOMParser().parseFromString(html, "text/html");
          const fresh = doc.querySelector("#user-table-wrapper");
          if (fresh) tableWrap.innerHTML = fresh.innerHTML;

          tableWrap.classList.remove('is-loading');
          tableWrap.style.opacity   = '0';
          tableWrap.style.transform = 'translateY(6px)';

          requestAnimationFrame(() => {
            requestAnimationFrame(() => {
              tableWrap.style.opacity   = '1';
              tableWrap.style.transform = 'translateY(0)';
            });
          });
        })
        .catch(err => {
          console.error(err);
          tableWrap.classList.remove('is-loading');
        });
    }, 150);
  }

  btnSearch?.addEventListener("click", () => {
    loadTable(buildQuery());
  });

  btnReset?.addEventListener("click", () => {
    filterForm.reset();
    loadTable("{{ route('admin.users.index') }}");
  });

  if (inputSearch) {
    let debounceTimer = null;

    inputSearch.addEventListener("input", () => {
      clearTimeout(debounceTimer);
      debounceTimer = setTimeout(() => {
        loadTable(buildQuery());
      }, 400);
    });

    inputSearch.addEventListener("keydown", (e) => {
      if (e.key === "Enter") {
        e.preventDefault();
        loadTable(buildQuery());
      }
    });
  }

  document.addEventListener("click", (e) => {
    const link = e.target.closest("#user-table-wrapper .pagination a");
    if (!link) return;
    if (link.getAttribute("href") === "#" || link.parentElement.classList.contains("disabled")) {
      e.preventDefault();
      return;
    }
    e.preventDefault();
    loadTable(link.href);
  });
});
</script>
@endpush

@push('styles')
<style>
  .outline-secondary {
    border: 1px solid #6c757d;
    color: #6c757d;
    background: transparent;
  }
  .outline-secondary:hover {
    background-color: #6c757d !important;
    color: white !important;
  }

  .badge-status {
    border-radius: 999px;
    padding: 0.25rem 0.7rem;
    font-size: .75rem;
    font-weight: 600;
  }
  .badge-status.active {
    background-color: #dcfce7;
    color: #166534;
  }
  .badge-status.inactive {
    background-color: #fee2e2;
    color: #b91c1c;
  }

  .badge-role {
    border-radius: 999px;
    font-size: .75rem;
    font-weight: 500;
    background: #e5e7eb;
    color: #374151;
  }

  /* Skeleton */
  .skeleton {
    background: linear-gradient(90deg, #e5e7eb 0%, #f3f4f6 50%, #e5e7eb 100%);
    background-size: 200% 100%;
    animation: shimmer 1.2s infinite;
    border-radius: 8px;
    margin-bottom: 10px;
  }
  .skeleton-title {
    height: 22px;
    width: 35%;
    margin-bottom: 16px;
  }
  .skeleton-row {
    height: 14px;
    width: 100%;
  }
  @keyframes shimmer {
    0% { background-position: 200% 0; }
    100% { background-position: -200% 0; }
  }

  /* Spinner premium di pojok kanan atas wrapper user */
  #user-table-wrapper {
    position: relative;
  }
  #user-table-wrapper::after {
    content: "";
    position: absolute;
    top: 6px;
    right: 10px;
    width: 22px;
    height: 22px;
    border-radius: 999px;
    background: rgba(255,255,255,0.9);
    box-shadow: 0 2px 6px rgba(15,23,42,0.15);
    border: 3px solid #e5e7eb;
    border-top-color: #4f46e5;
    border-right-color: #4f46e5;
    opacity: 0;
    animation: spinUser 0.7s linear infinite;
    transition: opacity .15s ease;
    pointer-events: none;
  }
  #user-table-wrapper.is-loading::after {
    opacity: 1;
  }

  @keyframes spinUser {
    to { transform: rotate(360deg); }
  }
</style>
@endpush
