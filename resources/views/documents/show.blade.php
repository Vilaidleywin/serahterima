@extends('layouts.app')

@section('content')
  <div class="container-fluid document-show px-lg-4 px-md-3 px-2">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h3 class="fw-bold mb-0">{{ $document->title }}</h3>
        <div class="text-muted small">No. Dokumen: {{ $document->number }}</div>
      </div>

      @php
        $isRejected = strtoupper($document->status) === 'REJECTED';
        $isSigned = filled($document->signature_path);
        $isSubmitted = strtoupper($document->status) === 'SUBMITTED';
      @endphp

      <div class="d-flex gap-2">
        <a href="{{ route('documents.index') }}" class="btn btn-outline-secondary">
          <i class="ti ti-arrow-left"></i> Kembali
        </a>

        {{-- Tombol Edit dikunci bila REJECTED --}}
        <a href="{{ route('documents.edit', $document) }}" class="btn btn-primary {{ $isRejected ? 'disabled' : '' }}"
          @if($isRejected) aria-disabled="true" tabindex="-1" @endif>
          <i class="ti ti-edit"></i> Edit
        </a>
      </div>
    </div>

    {{-- Banner jika REJECTED --}}
    @if($isRejected)
      <div class="alert alert-warning mb-4" role="alert" style="border-radius:10px;">
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
    <div class="row g-4 mb-5">
      {{-- Detail dokumen --}}
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

      {{-- Info tambahan & aksi --}}
      <div class="col-lg-4">
        <div class="card-soft p-4 mb-4 mb-lg-0">
          @if($isSigned)
            <div class="alert alert-success d-flex align-items-center gap-2 mb-3"
              style="background:#e6f6ee;border:1px solid #b4e0c3;color:#13693d;">
              <i class="ti ti-badge-check"></i>
              <div class="small">
                Dokumen <strong>telah ditandatangani</strong>
                {{ optional($document->signed_at)->translatedFormat('d M Y H:i') ?? '' }}.
              </div>
            </div>
          @endif

          <h6 class="fw-semibold text-uppercase text-muted small mb-2">Info Tambahan</h6>
          <hr>

          <a href="{{ route('documents.print-tandaterima', $document) }}" target="_blank"
            class="btn btn-outline-secondary w-100 mb-2">
            <i class="ti ti-printer"></i> Cetak Tanda Terima
          </a>

          @if(!$isSigned && !$isSubmitted && !$isRejected)
            <button class="btn btn-outline-danger w-100 mb-2" onclick="confirmDelete({{ $document->id }})">
              <i class="ti ti-trash me-1"></i> Hapus Dokumen
            </button>
          @endif


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

    {{-- BLOK TANDA TANGAN & FOTO --}}
    <div class="card-soft p-4 mt-4">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="text-muted small">Tanda Tangan & Foto Dokumen</div>
        <div class="d-flex gap-2">
          <a href="{{ route('documents.sign', $document) }}"
            class="btn btn-primary btn-sm {{ ($isRejected || $isSigned) ? 'disabled' : '' }}" @if($isRejected || $isSigned) aria-disabled="true" tabindex="-1" @endif>
            <i class="ti ti-signature me-1"></i> Tanda Tangan
          </a>
          <a href="{{ route('documents.photo', $document) }}" class="btn btn-outline-primary btn-sm">
            <i class="ti ti-camera me-1"></i> Ambil Foto
          </a>
        </div>
      </div>

      <div class="row g-3">
        <div class="col-md-6">
          <div class="extra-box text-center h-100">
            <div class="fw-semibold mb-2">Tanda Tangan</div>
            @if($document->signature_path)
              <img src="{{ Storage::url($document->signature_path) }}" alt="Signature"
                style="max-height:140px;object-fit:contain">
              <div class="text-muted small mt-2">
                Ditandatangani {{ optional($document->signed_at)->translatedFormat('d M Y H:i') ?? '-' }}
                @if($document->receiver)
                  oleh <strong>{{ $document->receiver }}</strong>
                @endif
              </div>
            @else
              <div class="text-muted small">Belum ada tanda tangan.</div>
            @endif
          </div>
        </div>

        <div class="col-md-6">
          <div class="extra-box text-center h-100">
            <div class="fw-semibold mb-2">Foto Dokumen</div>
            @if($document->photo_path)
              <img src="{{ Storage::url($document->photo_path) }}" alt="Photo"
                style="max-height:180px;width:100%;object-fit:contain">
              <div class="text-muted small mt-2">
                Difoto terakhir {{ optional($document->updated_at)->translatedFormat('d M Y H:i') ?? '-' }}
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

@push('styles')
  <style>
    .document-show {
      background-color: #f6f7fb;
    }

    .card-soft {
      background: #ffffff;
      border-radius: 14px;
      border: 1px solid #e5e7eb;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
    }

    .extra-box {
      background: #ffffff;
      border-radius: 14px;
      border: 1px solid #e5e7eb;
      padding: 1.5rem;
    }
  </style>
@endpush