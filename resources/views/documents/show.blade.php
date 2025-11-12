@extends('layouts.app')

@section('content')
  {{-- ==== CSS ANTI NABRAK ==== --}}
  <style>
    /* Matikan semua efek sticky di kolom kanan pada layar ≥ lg */
    @media (min-width: 992px) {

      .col-lg-4 .position-sticky,
      .col-lg-4 .sticky-top,
      .col-lg-4 .card-soft.position-sticky,
      .col-lg-4 .card-soft.sticky-top,
      .sidebar-card.position-sticky,
      .sidebar-card.sticky-top {
        position: static !important;
        top: auto !important;
      }

      /* Pastikan kartu kanan ikut flow normal & tidak bikin stacking aneh */
      .col-lg-4>.card-soft,
      .sidebar-card {
        position: static !important;
        z-index: 1 !important;
        transform: none !important;
      }
    }

    /* Blok tanda tangan & foto mulai baris baru + di atas layer sidebar */
    #sign-photo-block {
      clear: both;
      margin-top: 2.5rem;
      position: relative;
      z-index: 2;
    }
  </style>

  <div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h3 class="fw-bold mb-0">{{ $document->title }}</h3>
        <div class="text-muted small">No. Dokumen: {{ $document->number }}</div>
      </div>

      @php
        $isRejected = strtoupper($document->status) === 'REJECTED';
      @endphp
      <div class="d-flex gap-2">
        <a href="{{ route('documents.index') }}" class="btn btn-outline-secondary">
          <i class="ti ti-arrow-left"></i> Kembali
        </a>

        <a href="{{ route('documents.edit', $document) }}" class="btn btn-primary {{ $isRejected ? 'disabled' : '' }}"
          @if($isRejected) aria-disabled="true" tabindex="-1" @endif>
          <i class="ti ti-edit"></i> Edit
        </a>
      </div>
    </div>

    {{-- Banner jika REJECTED --}}
    @if($isRejected)
      <div class="alert alert-warning" role="alert" style="border-radius:10px;">
        <strong>Dokumen Ditolak.</strong> Edit, tanda tangan, dan pengambilan foto dinonaktifkan.
      </div>
    @endif

    {{-- Ringkasan --}}
    <div class="row g-3 mb-4">
      <div class="col-md-3">
        <div class="card-soft text-center p-3 h-100">
          <div class="text-muted small mb-1">Status</div>
          @include('shared.status-badge', ['status' => $document->status])
        </div>
      </div>

      <div class="col-md-3">
        <div class="card-soft text-center p-3 h-100">
          <div class="text-muted small mb-1">Tanggal</div>
          <div class="fw-semibold">
            {{ optional($document->date)->translatedFormat('d M Y') ?? '-' }}
          </div>
          <div class="text-muted small">
            {{ optional($document->date)->diffForHumans() ?? '-' }}
          </div>
        </div>
      </div>

      <div class="col-md-3">
        <div class="card-soft text-center p-3 h-100">
          <div class="text-muted small mb-1">Pengirim</div>
          <div class="fw-semibold">{{ $document->sender ?? '-' }}</div>
        </div>
      </div>

      <div class="col-md-3">
        <div class="card-soft text-center p-3 h-100">
          <div class="text-muted small mb-1">Penerima</div>
          <div class="fw-semibold">{{ $document->receiver ?? '-' }}</div>
        </div>
      </div>
    </div>

    {{-- Detail & Info --}}
    <div class="row g-3">
      <div class="col-lg-8">
        <div class="card-soft p-4">
          <div class="mb-3">
            <h6 class="fw-semibold text-uppercase text-muted small mb-1">Perihal</h6>
            <div class="fs-5">{{ $document->title }}</div>
          </div>

          @if($document->destination)
            <div class="mb-3">
              <h6 class="fw-semibold text-uppercase text-muted small mb-1">Tujuan</h6>
              <div>{{ $document->destination }}</div>
            </div>
          @endif

          @if($document->division)
            <div class="mb-3">
              <h6 class="fw-semibold text-uppercase text-muted small mb-1">Divisi</h6>
              <div>{{ $document->division }}</div>
            </div>
          @endif

          <div class="mb-3">
            <h6 class="fw-semibold text-uppercase text-muted small mb-1">Nominal</h6>
            <div class="fw-semibold text-success">
              {{ $document->amount_idr ? 'Rp ' . number_format($document->amount_idr, 0, ',', '.') : 'Rp 0' }}
            </div>
          </div>

          <div class="mb-3">
            <h6 class="fw-semibold text-uppercase text-muted small mb-1">Catatan</h6>
            <div style="white-space: pre-line;">{{ $document->description ?: '-' }}</div>
          </div>

          @if($document->file_path)
            <div class="mb-2">
              <h6 class="fw-semibold text-uppercase text-muted small mb-1">Lampiran</h6>
              <a href="{{ Storage::url($document->file_path) }}" target="_blank" class="btn btn-outline-primary">
                <i class="ti ti-paperclip me-1"></i> Lihat Lampiran
              </a>
            </div>
          @endif
        </div>
      </div>

      {{-- === KARTU INFO & AKSI (fixed: no sticky) === --}}
      @php $isSigned = filled($document->signature_path); @endphp
      <div class="col-lg-4">
        <div class="card-soft p-4 sidebar-card"><!-- class khusus untuk override -->

          @if($isSigned)
            <div class="alert alert-success d-flex align-items-center gap-2 mb-3"
              style="background:#e6f6ee;border:1px solid #b4e0c3;color:#13693d; position: static !important; animation: none !important;">
              <i class="ti ti-badge-check"></i>
              <div class="small">
                Dokumen <strong>telah ditandatangani</strong>
                {{ optional($document->signed_at)->translatedFormat('d M Y H:i') ?? '' }}.
                Tidak dapat ditolak lagi.
              </div>
            </div>
          @endif

          <h6 class="fw-semibold text-uppercase text-muted small mb-2">Info Tambahan</h6>
          <ul class="list-unstyled mb-3">
            <li class="mb-2">
              <i class="ti ti-user text-muted me-2"></i> Dibuat oleh:
              <span class="fw-semibold">{{ $document->user->name ?? '-' }}</span>
            </li>
            <li class="mb-2">
              <i class="ti ti-clock text-muted me-2"></i> Diperbarui:
              <span class="text-muted small">{{ $document->updated_at->diffForHumans() }}</span>
            </li>
            <li class="mb-2">
              <i class="ti ti-folder text-muted me-2"></i> Nomor Dokumen:
              <span class="fw-semibold">{{ $document->number }}</span>
            </li>
          </ul>
          <hr>

          <a href="{{ route('documents.print-tandaterima', $document) }}" target="_blank"
            class="btn btn-outline-secondary w-100 mb-2">
            <i class="ti ti-printer"></i> Cetak Tanda Terima
          </a>

          <button class="btn btn-outline-danger w-100 mb-2" onclick="confirmDelete({{ $document->id }})">
            <i class="ti ti-trash me-1"></i> Hapus Dokumen
          </button>

          @if(!$isSigned && !$isRejected)
            <form action="{{ route('documents.reject', $document) }}" method="POST"
              onsubmit="return confirm('Tolak dokumen ini?');" class="mt-2">
              @csrf
              <button type="submit" class="btn btn-outline-warning w-100">
                <i class="ti ti-ban"></i> Tolak Dokumen
              </button>
            </form>
          @endif

          <form id="delete-form-{{ $document->id }}" action="{{ route('documents.destroy', $document) }}" method="POST"
            class="d-none">
            @csrf
            @method('DELETE')
          </form>

        </div>
      </div>
    </div>

    {{-- === BLOK TANDA TANGAN & FOTO (tidak nabrak) === --}}
    <div id="sign-photo-block" class="card-soft p-3 mt-5">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <div class="text-muted small">Tanda Tangan & Foto Dokumen</div>
        <div class="d-flex gap-2"></div>
      </div>

      <div class="row g-3">
        {{-- Kolom Tanda Tangan --}}
        <div class="col-md-6">
          <div class="border rounded p-3 text-center h-100">
            <div class="fw-semibold mb-2">Tanda Tangan</div>

            @if($document->signature_path)
              <img src="{{ Storage::url($document->signature_path) }}" alt="Signature"
                style="max-height:140px;object-fit:contain">
              <div class="text-muted small mt-2">
                Ditandatangani
                {{ optional($document->signed_at)->translatedFormat('d M Y H:i') ?? '-' }}
                @if($document->receiver)
                  oleh <strong>{{ $document->receiver }}</strong>
                @endif
              </div>
            @else
              <div class="text-muted small">Belum ada tanda tangan.</div>
            @endif
          </div>
        </div>

        {{-- Kolom Foto Dokumen --}}
        <div class="col-md-6">
          <div class="border rounded p-3 text-center h-100">
            <div class="fw-semibold mb-2">Foto Dokumen</div>

            @if($document->photo_path)
              <img src="{{ Storage::url($document->photo_path) }}" alt="Photo"
                style="max-height:180px;width:100%;object-fit:contain">
              <div class="text-muted small mt-2">
                Difoto terakhir
                {{ optional($document->updated_at)->translatedFormat('d M Y H:i') ?? '-' }}
              </div>
            @else
              <div class="text-muted small">Belum ada foto.</div>
            @endif
          </div>
        </div>
      </div>
    </div>

  </div>
@endsection