@extends('layouts.app')

@section('content')
  <div class="container-fluid">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h3 class="fw-bold mb-0">{{ $document->title }}</h3>
        <div class="text-muted small">No. Dokumen: {{ $document->number }}</div>
      </div>
      <div class="d-flex gap-2">
        <a href="{{ route('documents.index') }}" class="btn btn-outline-secondary">
          <i class="ti ti-arrow-left"></i> Kembali
        </a>
        <a href="{{ route('documents.edit', $document) }}" class="btn btn-primary">
          <i class="ti ti-edit"></i> Edit
        </a>
      </div>
    </div>

    {{-- Ringkasan --}}
    <div class="row g-3 mb-4">
      {{-- Status --}}
      <div class="col-md-3">
        <div class="card-soft text-center p-3 h-100">
          <div class="text-muted small mb-1">Status</div>
          @include('shared.status-badge', ['status' => $document->status])
        </div>
      </div>

      {{-- Tanggal --}}
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

      {{-- Pengirim --}}
      <div class="col-md-3">
        <div class="card-soft text-center p-3 h-100">
          <div class="text-muted small mb-1">Pengirim</div>
          <div class="fw-semibold">{{ $document->sender ?? '-' }}</div>
        </div>
      </div>

      {{-- Penerima --}}
      <div class="col-md-3">
        <div class="card-soft text-center p-3 h-100">
          <div class="text-muted small mb-1">Penerima</div>
          <div class="fw-semibold">{{ $document->receiver ?? '-' }}</div>
        </div>
      </div>
    </div>

    {{-- Detail Dokumen --}}
    <div class="row g-3">
      <div class="col-lg-8">
        <div class="card-soft p-4">
          {{-- Perihal --}}
          <div class="mb-3">
            <h6 class="fw-semibold text-uppercase text-muted small mb-1">Perihal</h6>
            <div class="fs-5">{{ $document->title }}</div>
          </div>

          {{-- Tujuan --}}
          @if($document->destination)
            <div class="mb-3">
              <h6 class="fw-semibold text-uppercase text-muted small mb-1">Tujuan</h6>
              <div>{{ $document->destination }}</div>
            </div>
          @endif

          {{-- Divisi --}}
          @if($document->division)
            <div class="mb-3">
              <h6 class="fw-semibold text-uppercase text-muted small mb-1">Divisi</h6>
              <div>{{ $document->division }}</div>
            </div>
          @endif

          {{-- Nominal --}}
          <div class="mb-3">
            <h6 class="fw-semibold text-uppercase text-muted small mb-1">Nominal</h6>
            <div class="fw-semibold text-success">
              {{ $document->amount_idr ? 'Rp ' . number_format($document->amount_idr, 0, ',', '.') : 'Rp 0' }}
            </div>
          </div>

          {{-- Catatan / Deskripsi --}}
          <div class="mb-3">
            <h6 class="fw-semibold text-uppercase text-muted small mb-1">Catatan</h6>
            <div style="white-space: pre-line;">{{ $document->description ?: '-' }}</div>
          </div>

          {{-- Lampiran --}}
          @if($document->file)
            <div class="mb-2">
              <h6 class="fw-semibold text-uppercase text-muted small mb-1">Lampiran</h6>
              <a href="{{ asset('storage/' . $document->file) }}" target="_blank" class="btn btn-outline-primary">
                <i class="ti ti-paperclip me-1"></i> Lihat Lampiran
              </a>
            </div>
          @endif
        </div>
      </div>

      {{-- Info Tambahan --}}
      <div class="col-lg-4">
        <div class="card-soft p-4">
          <h6 class="fw-semibold text-uppercase text-muted small mb-2">Info Tambahan</h6>
          <ul class="list-unstyled mb-3">
            <li class="mb-2">
              <i class="ti ti-user text-muted me-2"></i> Dibuat oleh:
              <span class="fw-semibold">{{ $document->created_by->name ?? '-' }}</span>
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
          <button onclick="window.print()" class="btn btn-outline-secondary w-100 mb-2">
            <i class="ti ti-printer me-1"></i> Cetak Dokumen
          </button>
          <button class="btn btn-outline-danger w-100" onclick="confirmDelete({{ $document->id }})">
            <i class="ti ti-trash me-1"></i> Hapus Dokumen
          </button>

          <form id="delete-form-{{ $document->id }}" action="{{ route('documents.destroy', $document) }}" method="POST"
            class="d-none">
            @csrf
            @method('DELETE')
          </form>

        </div>
      </div>


      @if($document->signature_path)
        <div class="card-soft p-3 mt-3">
          <div class="text-muted small mb-1">Tanda Tangan</div>
          <img src="{{ asset('storage/' . $document->signature_path) }}" alt="signature" style="max-height:120px">
          <div class="text-muted small mt-1">
            Ditandatangani {{ $document->signed_at?->translatedFormat('d M Y H:i') }}
            @if($document->receiver)
              oleh <strong>{{ $document->receiver }}</strong>
            @endif
          </div>
        </div>
      @else
        <a href="{{ route('documents.sign', $document) }}" class="btn btn-success mt-2">
          <i class="ti ti-signature"></i> Tanda Tangani Dokumen
        </a>
      @endif


    </div>
  </div>
@endsection