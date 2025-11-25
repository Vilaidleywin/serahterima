@extends('layouts.app')

@section('content')
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h2 class="fw-bold mb-0">Data Dokumen</h2>
      <div class="text-muted small">Rekap semua bukti serah terima</div>

      {{-- Keterangan filter aktif --}}
      @if(request()->filled('search') || request()->filled('status') || request()->filled('date_from') || request()->filled('date_to'))
        <div class="mt-1 small text-primary">
          <strong>Filter aktif:</strong>
          @if(request('search')) cari: <span class="text-dark">{{ request('search') }}</span>; @endif
          @if(request('status')) status: <span class="text-dark">{{ request('status') }}</span>; @endif
          @if(request('date_from') || request('date_to'))
            tanggal:
            <span class="text-dark">
              {{ request('date_from') ?? '–' }} s/d {{ request('date_to') ?? '–' }}
            </span>;
          @endif
        </div>
      @endif
    </div>

    <div class="d-flex gap-2">
      <a href="{{ route('documents.create') }}" class="btn btn-primary">
        <i class="ti ti-plus"></i> Tambah
      </a>
      <a href="{{ route('documents.index') }}" class="btn outline-secondary">
        <i class="ti ti-refresh"></i> Refresh
      </a>
    </div>
  </div>

  {{-- FILTER BAR --}}
  <form id="filterForm" method="get" class="mb-3">
    <div class="row g-3 align-items-end">

      {{-- KOLOM 1: Pencarian + tombol Filter & Reset di bawahnya --}}
      <div class="col-md-4">
        <label class="form-label mb-1 small text-muted">Pencarian</label>
        <input name="search" value="{{ request('search') }}" class="form-control search" placeholder="Cari dokumen...">

        {{-- Tombol-tombol di bawah pencarian --}}
        <div class="d-flex flex-wrap gap-2 mt-2">
          <button type="submit" class="btn btn-primary">
            <i class="ti ti-filter me-1"></i> Filter
          </button>

          {{-- Reset pakai AJAX --}}
          <a href="#" id="btnFilterReset" class="btn btn-outline-secondary">
            <i class="ti ti-filter-off me-1"></i> Reset
          </a>
        </div>
      </div>

      {{-- KOLOM 2: Status --}}
      <div class="col-md-2">
        <label class="form-label mb-1 small text-muted">Status</label>
        <select name="status" class="form-select search">
          <option value="">Semua Status</option>
          @foreach(['DRAFT', 'SUBMITTED', 'REJECTED'] as $s)
            <option value="{{ $s }}" @selected(request('status') === $s)>{{ $s }}</option>
          @endforeach
        </select>
      </div>

      {{-- KOLOM 3: Periode cepat --}}
      <div class="col-md-2">
        <label class="form-label mb-1 small text-muted">Periode cepat</label>
        <select name="period" id="datePreset" class="form-select">
          <option value="">Semua Periode</option>
          <option value="yesterday" @selected(request('period') === 'yesterday')>Kemarin</option>
          <option value="last_week" @selected(request('period') === 'last_week')>Minggu lalu</option>
          <option value="last_month" @selected(request('period') === 'last_month')>Bulan lalu</option>
        </select>
      </div>

      {{-- KOLOM 4: Tanggal dari --}}
      <div class="col-md-2">
        <label class="form-label mb-1 small text-muted">Tanggal dari</label>
        <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control">
      </div>

      {{-- KOLOM 5: Tanggal sampai --}}
      <div class="col-md-2">
        <label class="form-label mb-1 small text-muted">Tanggal sampai</label>
        <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control">
      </div>

    </div>
  </form>

  {{-- WRAPPER: bagian ini saja yang di-refresh via AJAX --}}
  <div id="table-wrapper">

    {{-- TABLE --}}
    <div class="card-soft p-2" style="border:1px solid #d9dee3; border-radius:10px; overflow:auto; max-height:600px;">
      <table class="table table-striped table-hover align-middle mb-0">
        <thead>
          <tr>
            <th style="width:60px">No</th>
            <th>No Dokumen</th>
            <th>Nama Dokumen</th>
            <th>Pengirim</th>
            <th>Penerima</th>
            <th>Divisi</th>
            <th>Tujuan</th>
            <th style="width:140px">Nominal (Rp)</th>
            <th style="width:120px">Tanggal</th>
            <th style="width:120px">Status</th>
            <th style="width:80px" class="text-end">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($documents as $i => $d)
            @php
              $rowNumber   = ($documents->firstItem() ?? 1) + $i;
              $statusUpper = strtoupper($d->status ?? '');
              $isRejected  = $statusUpper === 'REJECTED';
              $isSubmitted = $statusUpper === 'SUBMITTED';

              $hasSigned   = !empty($d->signed_at);
              $hasPhoto    = !empty($d->photo_path);

              // === HANYA BAGIAN INI YANG DIUBAH ===
              $canEdit     = !$isRejected;
              $canSign     = !$isSubmitted && !$isRejected && !$hasSigned;
              $canPhoto    = !$isRejected;
              $canDelete   = !$isRejected;
              // === END PERUBAHAN ===
            @endphp

            <tr>
              <td class="fw-semibold">{{ $rowNumber }}</td>

              <td>{{ $d->number ?? '-' }}</td>
              <td>{{ $d->title ?? '-' }}</td>
              <td>{{ $d->sender ?? '-' }}</td>
              <td>{{ $d->receiver ?? '-' }}</td>
              <td>{{ $d->division ?? '-' }}</td>
              <td>{{ $d->destination ?? '-' }}</td>
              <td>{{ $d->amount_idr_formatted ?? 'Rp. ' . number_format((int) $d->amount_idr, 0, ',', '.') }}</td>
              <td>{{ $d->date?->format('Y-m-d') }}</td>
              <td>@include('shared.status-badge', ['status' => $d->status])</td>

              <td class="text-end">
                <div class="dropdown">
                  <button type="button" class="btn btn-kebab" data-bs-toggle="dropdown" aria-expanded="false">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="#111827"
                      class="bi bi-three-dots-vertical">
                      <path
                        d="M9.5 13a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zm0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zm0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z" />
                    </svg>
                  </button>

                  <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                    {{-- Always allow Detail --}}
                    <li>
                      <a class="dropdown-item" href="{{ route('documents.show', $d) }}">
                        <i class="ti ti-eye me-2"></i> Detail
                      </a>
                    </li>

                    {{-- Edit: boleh selama tidak REJECTED --}}
                    @if($canEdit)
                      <li>
                        <a class="dropdown-item" href="{{ route('documents.edit', $d) }}">
                          <i class="ti ti-edit me-2"></i> Edit
                        </a>
                      </li>
                    @endif

                    {{-- Tanda Tangan: hanya kalau masih DRAFT & belum tanda tangan --}}
                    @if($canSign)
                      <li>
                        <a class="dropdown-item" href="{{ route('documents.sign', $d) }}">
                          <i class="ti ti-signature me-2"></i> Tanda Tangan
                        </a>
                      </li>
                    @endif


                    {{-- Ambil Foto: tidak boleh kalau REJECTED, dan hilang kalau sudah ada foto --}}
                    @if(!$hasPhoto)
                      @if($canPhoto)
                        <li>
                          <a class="dropdown-item" href="{{ route('documents.photo', $d) }}">
                            <i class="ti ti-camera me-2"></i> Ambil Foto
                          </a>
                        </li>
                      @else
                        <li>
                          <span class="dropdown-item text-muted" style="cursor: default;">
                            <i class="ti ti-camera me-2"></i> Ambil Foto (tidak tersedia)
                          </span>
                        </li>
                      @endif
                    @endif
                    
                    <li><hr class="dropdown-divider"></li>

                    {{-- Delete: boleh selama tidak REJECTED (DRAFT & SUBMITTED) --}}
                    @if($canDelete)
                      <li>
                        <button
                          type="button"
                          class="dropdown-item text-danger"
                          onclick="confirmDelete({{ $d->id }}, @js(($d->number ?? '-') . ' - ' . ($d->title ?? '-')))"
                        >
                          <i class="ti ti-trash me-2"></i> Hapus
                        </button>
                      </li>
                    @endif

                  </ul>
                </div>

                {{-- FORM DELETE --}}
                <form id="delete-form-{{ $d->id }}" action="{{ route('documents.destroy', $d) }}" method="POST"
                  class="d-none">
                  @csrf
                  @method('DELETE')
                </form>
              </td>

            </tr>
          @empty
            <tr>
              <td colspan="11" class="text-center py-4">Tidak ada data</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{-- FOOTER PAGINATION --}}
    <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2 py-2 px-1"
      style="border-top:1px solid #e3e8ef; font-size:14px; color:#6b7280;">

      {{-- Rows per page --}}
      <form method="get" class="d-inline-flex align-items-center gap-2 mb-0">
        <input type="hidden" name="search" value="{{ request('search') }}">
        <input type="hidden" name="status" value="{{ request('status') }}">
        <input type="hidden" name="date_from" value="{{ request('date_from') }}">
        <input type="hidden" name="date_to" value="{{ request('date_to') }}">

        <select name="per_page" class="form-select form-select-sm" style="width:90px" onchange="this.form.submit()">
          @foreach([10, 15, 25, 50] as $n)
            <option value="{{ $n }}" @selected(($per_page ?? 15) == $n)>{{ $n }}</option>
          @endforeach
        </select>
        <span>rows / page</span>
      </form>

      {{-- Showing info --}}
      <div class="text-muted small flex-grow-1 text-center mb-0">
        Showing {{ $documents->firstItem() ?? 0 }} to {{ $documents->lastItem() ?? 0 }} of {{ $documents->total() }} rows
      </div>

      {{-- Pagination --}}
      <div class="mb-0">
        <nav>
          <ul class="pagination mb-0">
            <li class="page-item {{ $documents->onFirstPage() ? 'disabled' : '' }}">
              <a class="page-link" href="{{ $documents->previousPageUrl() ?? '#' }}">&laquo;</a>
            </li>

            @foreach ($documents->getUrlRange(1, $documents->lastPage()) as $page => $url)
              <li class="page-item {{ $page == $documents->currentPage() ? 'active' : '' }}">
                <a class="page-link" href="{{ $url }}">{{ $page }}</a>
              </li>
            @endforeach

            <li class="page-item {{ !$documents->hasMorePages() ? 'disabled' : '' }}">
              <a class="page-link" href="{{ $documents->nextPageUrl() ?? '#' }}">&raquo;</a>
            </li>
          </ul>
        </nav>
      </div>
    </div>
  </div> {{-- /#table-wrapper --}}
@endsection

@push('scripts')
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      // ====== AJAX FILTER & RESET (refresh cuma bagian table-wrapper) ======
      const filterForm     = document.getElementById('filterForm');
      const btnFilterReset = document.getElementById('btnFilterReset');
      const tableWrapper   = document.getElementById('table-wrapper');

      if (!filterForm || !btnFilterReset || !tableWrapper) {
        return;
      }

      function loadTable(url) {
        fetch(url, {
          headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
          .then(res => res.text())
          .then(html => {
            const parser    = new DOMParser();
            const doc       = parser.parseFromString(html, 'text/html');
            const newWrapper = doc.querySelector('#table-wrapper');
            if (newWrapper) {
              tableWrapper.innerHTML = newWrapper.innerHTML;
            }
          })
          .catch(err => console.error(err));
      }

      // submit filter tanpa reload full page
      filterForm.addEventListener('submit', function (e) {
        e.preventDefault();
        const params = new URLSearchParams(new FormData(filterForm)).toString();
        const url = "{{ route('documents.index') }}" + (params ? ('?' + params) : '');
        loadTable(url);
      });

      // reset filter tanpa reload full page
      btnFilterReset.addEventListener('click', function (e) {
        e.preventDefault();
        filterForm.reset();
        loadTable("{{ route('documents.index') }}");
      });

      // pagination di dalam #table-wrapper pakai AJAX juga
      document.addEventListener('click', function (e) {
        const link = e.target.closest('#table-wrapper .pagination a');
        if (!link) return;

        if (link.getAttribute('href') === '#' || link.parentElement.classList.contains('disabled')) {
          e.preventDefault();
          return;
        }

        e.preventDefault();
        loadTable(link.href);
      });
    });
  </script>

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

    .filter-buttons {
      margin-top: 12px;
    }

    @media (min-width: 768px) {
      .filter-buttons {
        margin-top: 18px;
      }
    }

    /* Kebab button ala screenshot: putih, rounded, border halus */
    .btn-kebab {
      width: 40px;
      height: 40px;
      border-radius: 999px;
      border: 1px solid #e2e8f0;
      background-color: #ffffff;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      padding: 0;
      box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
    }

    .btn-kebab:hover {
      background-color: #eef2ff;
      border-color: #c7d2fe;
      box-shadow: 0 2px 6px rgba(15, 23, 42, 0.12);
    }
  </style>
@endpush
