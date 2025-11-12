@extends('layouts.app')

@section('content')
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h2 class="fw-bold mb-0">Data Dokumen</h2>
      <div class="text-muted small">Rekap semua bukti serah terima</div>
    </div>
    <div class="d-flex gap-2">
      <a href="{{ route('documents.create') }}" class="btn btn-primary"><i class="ti ti-plus"></i> Tambah</a>
      <a href="{{ route('documents.index') }}" class="btn outline-secondary"><i class="ti ti-refresh"></i> Refresh</a>
    </div>
  </div>

  {{-- FILTER BAR --}}
  <form class="row g-2 mb-3" method="get">
    <div class="col-md-3">
      <input name="search" value="{{ request('search') }}" class="form-control search" placeholder="Cari dokumen...">
    </div>

    <div class="col-md-2">
      <select name="status" class="form-select search">
        <option value="">Semua Status</option>
        @foreach(['DRAFT', 'SUBMITTED', 'REJECTED'] as $s)
          <option value="{{ $s }}" @selected(request('status') === $s)>{{ $s }}</option>
        @endforeach
      </select>
    </div>

    {{-- Preset periode cepat berdasarkan created_at --}}
    <div class="col-md-2">
      <select name="period" class="form-select">
        <option value="">Semua Periode</option>
        <option value="today" @selected(request('period') === 'today')>Hari ini</option>
        <option value="week" @selected(request('period') === 'week')>Minggu ini</option>
        <option value="month" @selected(request('period') === 'month')>Bulan ini</option>
      </select>
    </div>

    {{-- Rentang manual untuk kolom DATE (tanggal serah terima) --}}
    <div class="col-md-2">
      <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control" placeholder="Date from">
    </div>
    <div class="col-md-2">
      <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control" placeholder="Date to">
    </div>

    {{-- (Opsional) Rentang created_at --}}
    {{--
    <div class="col-md-2">
      <input type="date" name="created_from" value="{{ request('created_from') }}" class="form-control"
        placeholder="Created from">
    </div>
    <div class="col-md-2">
      <input type="date" name="created_to" value="{{ request('created_to') }}" class="form-control"
        placeholder="Created to">
    </div>
    --}}

    <div class="col-md-1 d-grid">
      <button class="btn btn-primary">
        <i class="ti ti-filter me-1"></i> Filter
      </button>
    </div>
  </form>


  {{-- TABLE --}}
  <div class="card-soft p-2" style="border:1px solid #d9dee3; border-radius:10px; overflow:auto; max-height:600px;">
    <table class="table table-striped table-hover align-middle mb-0">
      <thead>
        <tr>
          <th class="text-center" style="width:60px">No</th>
          <th style="width:140px">No. Dokumen</th>
          <th>Perihal</th>
          <th style="width:160px">Pengirim</th>
          <th style="width:160px">Penerima</th>
          <th style="width:160px">Divisi</th>
          <th style="width:160px">Tujuan</th>
          <th style="width:140px">Nominal (Rp)</th>
          <th style="width:120px">Tanggal</th>
          <th style="width:120px">Status</th>
          <th style="width:180px">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($documents as $i => $d)
          <tr>
            <td class="text-center">{{ $documents->firstItem() + $i }}</td>
            <td>{{ $d->number }}</td>
            <td>{{ $d->title }}</td>
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
                  <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor"
                    class="bi bi-three-dots-vertical" viewBox="0 0 16 16">
                    <path
                      d="M9.5 13a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zM9.5 8a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zM9.5 3a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z" />
                  </svg>
                </button>

                <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                  <li>
                    <a class="dropdown-item" href="{{ route('documents.show', $d) }}">
                      <i class="ti ti-eye me-2"></i> Detail
                    </a>
                  </li>
                  <li>
                    <a class="dropdown-item" href="{{ route('documents.edit', $d) }}">
                      <i class="ti ti-edit me-2"></i> Edit
                    </a>
                  </li>
                  <li>
                    <a class="dropdown-item" href="{{ route('documents.sign', $d) }}">
                      <i class="ti ti-signature me-2"></i> Tanda Tangan
                    </a>
                  </li>
                  <li>
                    <hr class="dropdown-divider">
                  </li>
                  <li>
                    <a class="dropdown-item" href="{{ route('documents.photo', $d) }}">
                      <i class="ti ti-camera me-2"></i> Ambil Foto
                    </a>
                  </li>
                  <li>
                    <button type="button" class="dropdown-item text-danger" onclick="confirmDelete({{ $d->id }})">
                      <i class="ti ti-trash me-2"></i> Hapus
                    </button>
                  </li>
                </ul>
              </div>

              {{-- form hapus tersembunyi --}}
              <form id="delete-form-{{ $d->id }}" action="{{ route('documents.destroy', $d) }}" method="POST"
                class="d-none">
                @csrf @method('DELETE')
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

  {{-- Footer pagination bar --}}
  <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2 py-2 px-1"
    style="border-top:1px solid #e3e8ef; font-size:14px; color:#6b7280;">

    {{-- Rows per page selector --}}
    <form method="get" class="d-inline-flex align-items-center gap-2 mb-0">
      {{-- bawa seluruh filter agar saat ganti per_page tidak hilang --}}
      <input type="hidden" name="search" value="{{ request('search') }}">
      <input type="hidden" name="status" value="{{ request('status') }}">
      <input type="hidden" name="from_date" value="{{ request('from_date') }}">
      <input type="hidden" name="to_date" value="{{ request('to_date') }}">

      <select name="per_page" class="form-select form-select-sm" style="width:90px" onchange="this.form.submit()">
        @foreach([10, 15, 25, 50] as $n)
          <option value="{{ $n }}" @selected(($per_page ?? 15) == $n)>{{ $n }}</option>
        @endforeach
      </select>
      <span>rows / page</span>
    </form>

    {{-- Showing info --}}
    <div class="text-muted small flex-grow-1 text-center mb-0">
      Showing {{ $documents->firstItem() ?? 0 }} to {{ $documents->lastItem() ?? 0 }}
      of {{ $documents->total() }} rows
    </div>

    {{-- Pagination numbers --}}
    <div class="mb-0">
      <nav aria-label="Page navigation">
        <ul class="pagination mb-0">
          {{-- Previous --}}
          <li class="page-item {{ $documents->onFirstPage() ? 'disabled' : '' }}">
            <a class="page-link" href="{{ $documents->previousPageUrl() ?? '#' }}" aria-label="Previous">
              <span aria-hidden="true">&laquo;</span>
            </a>
          </li>

          {{-- Page numbers --}}
          @foreach ($documents->getUrlRange(1, $documents->lastPage()) as $page => $url)
            <li class="page-item {{ $page == $documents->currentPage() ? 'active' : '' }}">
              <a class="page-link" href="{{ $url }}">{{ $page }}</a>
            </li>
          @endforeach

          {{-- Next --}}
          <li class="page-item {{ !$documents->hasMorePages() ? 'disabled' : '' }}">
            <a class="page-link" href="{{ $documents->nextPageUrl() ?? '#' }}" aria-label="Next">
              <span aria-hidden="true">&raquo;</span>
            </a>
          </li>
        </ul>
      </nav>
    </div>
  </div>
@endsection

@push('scripts')
  <script>
    (function () {
      const preset = document.getElementById('datePreset');
      const fromInput = document.querySelector('input[name="from_date"]');
      const toInput = document.querySelector('input[name="to_date"]');

      function pad(n) { return String(n).padStart(2, '0'); }
      function fmt(d) { return d.getFullYear() + '-' + pad(d.getMonth() + 1) + '-' + pad(d.getDate()); }

      function startOfWeek(d) {
        const day = d.getDay(); // 0=Sun
        const diff = (day === 0 ? -6 : 1 - day); // Minggu -> mundur ke Senin
        const s = new Date(d); s.setDate(d.getDate() + diff);
        return s;
      }
      function endOfWeek(d) {
        const s = startOfWeek(d);
        const e = new Date(s); e.setDate(s.getDate() + 6);
        return e;
      }

      preset?.addEventListener('change', () => {
        const today = new Date();
        let from = '', to = '';
        switch (preset.value) {
          case 'today':
            from = to = fmt(today);
            break;
          case 'this_week':
            from = fmt(startOfWeek(today));
            to = fmt(endOfWeek(today));
            break;
          case 'this_month': {
            const f = new Date(today.getFullYear(), today.getMonth(), 1);
            const l = new Date(today.getFullYear(), today.getMonth() + 1, 0);
            from = fmt(f); to = fmt(l);
            break;
          }
          case 'last_month': {
            const f = new Date(today.getFullYear(), today.getMonth() - 1, 1);
            const l = new Date(today.getFullYear(), today.getMonth(), 0);
            from = fmt(f); to = fmt(l);
            break;
          }
          default:
            from = ''; to = '';
        }
        fromInput.value = from;
        toInput.value = to;
      });
    })();
  </script>
@endpush