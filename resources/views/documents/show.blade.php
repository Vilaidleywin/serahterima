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
        $isPhotoTaken = filled($document->photo_path);

        // Delete hanya ketika masih DRAFT (tidak submitted dan tidak rejected)
        $canDelete = !$isRejected && !$isSubmitted;
      @endphp

      <div class="d-flex gap-2">
        <a href="{{ route('documents.index') }}" class="btn btn-outline-secondary">
          <i class="ti ti-arrow-left"></i> Kembali
        </a>

        {{-- Edit dikunci bila SUBMITTED (REJECTED tetap bisa edit) --}}
        <a href="{{ route('documents.edit', $document) }}" class="btn btn-primary {{ $isSubmitted ? 'disabled' : '' }}"
          @if($isSubmitted) aria-disabled="true" tabindex="-1" @endif>
          <i class="ti ti-edit"></i> Edit
        </a>
      </div>
    </div>

    {{-- Banner jika REJECTED --}}
    @if($isRejected)
      <div class="alert alert-danger mb-4 reject-banner" role="alert">
        <strong>Dokumen Ditolak.</strong> Tanda tangan dan pengambilan foto dinonaktifkan.
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

      {{-- Kartu info & aksi --}}
      <div class="col-lg-4">
        <div class="card-soft p-4">

          {{-- Notifikasi hijau kalau sudah ditandatangani --}}
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

          {{-- Keterangan Penolakan (box merah di card kanan) --}}
          @if($isRejected && $document->reject_reason)
            @php
              // fallback ke updated_at kalau rejected_at null (data lama)
              $rejectedAt = $document->rejected_at ?? $document->updated_at;

              $rejectedAtCarbon = null;
              if ($rejectedAt) {
                try {
                  $rejectedAtCarbon = \Illuminate\Support\Carbon::parse($rejectedAt);
                } catch (\Exception $e) {
                  $rejectedAtCarbon = null;
                }
              }
            @endphp

            <div class="reject-info-box mb-3">
              <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="fw-semibold small text-uppercase">Keterangan Penolakan</span>
                <span class="badge bg-light text-danger border border-danger small">
                  REJECTED
                </span>
              </div>

              <p class="mb-1">{{ $document->reject_reason }}</p>

              @if($rejectedAtCarbon)
                <div class="text-muted small">
                  Ditolak pada
                  <span class="fw-semibold">
                    {{ $rejectedAtCarbon->timezone('Asia/Jakarta')->translatedFormat('d M Y, H:i') }}
                  </span>
                </div>
              @endif
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

          {{-- Cetak --}}
          <a href="{{ route('documents.print-tandaterima', $document) }}" target="_blank"
            class="btn btn-outline-secondary w-100 mb-2 mt-3">
            <i class="ti ti-printer"></i> Cetak Tanda Terima
          </a>

          {{-- Hapus Dokumen: hanya jika tidak REJECTED dan tidak SUBMITTED (DRAFT saja) --}}
          @if($canDelete)
            <button class="btn btn-outline-danger w-100 mb-2" onclick="confirmDelete({{ $document->id }})">
              <i class="ti ti-trash me-1"></i> Hapus Dokumen
            </button>
          @endif

          {{-- Tolak (hanya kalau belum signed dan belum rejected) --}}
          @if(!$isSigned && !$isRejected)
            <button type="button" class="btn btn-outline-warning w-100 mt-2" data-bs-toggle="modal"
              data-bs-target="#rejectModal">
              <i class="ti ti-ban"></i> Tolak Dokumen
            </button>
          @endif

          {{-- Form hapus --}}
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
          {{-- Tanda Tangan: disable jika REJECTED atau sudah ditandatangani --}}
          <a href="{{ route('documents.sign', $document) }}"
            class="btn btn-primary btn-sm {{ ($isRejected || $isSigned) ? 'disabled' : '' }}" @if($isRejected || $isSigned) aria-disabled="true" tabindex="-1" @endif>
            <i class="ti ti-signature me-1"></i> Tanda Tangan
          </a>

          {{-- Ambil Foto: disable jika REJECTED atau SUDAH ADA FOTO --}}
          <a href="{{ route('documents.photo', $document) }}"
            class="btn btn-outline-primary btn-sm {{ ($isRejected || $isPhotoTaken) ? 'disabled' : '' }}" @if($isRejected || $isPhotoTaken) aria-disabled="true" tabindex="-1" @endif>
            <i class="ti ti-camera me-1"></i> Ambil Foto
          </a>
        </div>
      </div>

      <div class="row g-3">
        <div class="col-md-6">
          <div class="extra-box text-center h-100">
            <div class="fw-semibold mb-2">Tanda Tangan</div>
            @if($document->signature_path)
              <button type="button" class="border-0 bg-transparent p-0 preview-trigger" data-bs-toggle="modal"
                data-bs-target="#imagePreviewModal" data-image="{{ Storage::url($document->signature_path) }}">
                <img src="{{ Storage::url($document->signature_path) }}" alt="Signature"
                  style="max-height:140px;object-fit:contain">
              </button>
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
              <button type="button" class="border-0 bg-transparent p-0 preview-trigger w-100" data-bs-toggle="modal"
                data-bs-target="#imagePreviewModal" data-image="{{ Storage::url($document->photo_path) }}">
                <img src="{{ Storage::url($document->photo_path) }}" alt="Photo"
                  style="max-height:180px;width:100%;object-fit:contain">
              </button>
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

  {{-- Modal Preview Gambar --}}
  <div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content bg-dark">
        <div class="modal-body p-0 position-relative">
          <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3"
            data-bs-dismiss="modal" aria-label="Close"></button>
          <img id="previewImage" src="" alt="Preview" class="w-100" style="max-height:80vh;object-fit:contain;">
        </div>
      </div>
    </div>
  </div>

  {{-- Modal Tolak Dokumen --}}
  <div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <form action="{{ route('documents.reject', $document) }}" method="POST">
        @csrf
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="rejectModalLabel">Tolak Dokumen</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>

          <div class="modal-body">
            <div class="mb-3">
              <label for="rejectReason" class="form-label">
                Keterangan Penolakan <span class="text-danger">*</span>
              </label>
              <textarea name="reject_reason" id="rejectReason" rows="3"
                class="form-control @error('reject_reason') is-invalid @enderror"
                required>{{ old('reject_reason') }}</textarea>

              @error('reject_reason')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-danger">Tolak Dokumen</button>
          </div>
        </div>
      </form>
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

    .preview-trigger {
      cursor: zoom-in;
    }

    /* Banner merah REJECTED */
    .reject-banner {
      background: #fee2e2 !important;
      color: #991b1b !important;
      border: 1px solid #fecaca !important;
      border-radius: 10px;
    }

    /* Box merah untuk keterangan penolakan */
    .reject-info-box {
      border-radius: 12px;
      background: #fee2e2;
      /* merah muda */
      border: 1px solid #fecaca;
      /* border merah lembut */
      padding: 0.85rem 1rem;
      color: #991b1b;
      /* teks merah tua */
    }

    .reject-info-box p {
      margin-bottom: 0.25rem;
      white-space: pre-line;
    }
  </style>
@endpush

@push('scripts')
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      var previewModal = document.getElementById('imagePreviewModal');
      var previewImage = document.getElementById('previewImage');

      if (previewModal) {
        previewModal.addEventListener('show.bs.modal', function (event) {
          var button = event.relatedTarget;
          var imageSrc = button.getAttribute('data-image');
          previewImage.src = imageSrc || '';
        });

        previewModal.addEventListener('hidden.bs.modal', function () {
          previewImage.src = '';
        });
      }

      // Kalau validasi alasan reject gagal, buka lagi modalnya
      @if($errors->has('reject_reason'))
        var rejectModalEl = document.getElementById('rejectModal');
        if (rejectModalEl) {
          var rejectModal = new bootstrap.Modal(rejectModalEl);
          rejectModal.show();
        }
      @endif
      });
  </script>
@endpush