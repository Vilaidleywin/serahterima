@extends('layouts.app')

@section('content')
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h2 class="fw-bold mb-0">Tanda Tangan Dokumen</h2>
      <div class="text-muted small">No. Dokumen: {{ $document->number }}</div>
    </div>
    <div class="d-flex gap-2">
      <a href="{{ route('documents.show', $document) }}" class="btn btn-ghost">
        <i class="ti ti-arrow-left"></i> Kembali
      </a>
    </div>
  </div>

  <div class="card-soft p-4">
    @if($errors->any())
      <div class="alert alert-danger mb-3">
        {{ $errors->first() }}
      </div>
    @endif

    {{-- Area canvas responsif --}}
    <div class="mb-3">
      <div class="text-muted small mb-2">Silakan tanda tangan di kotak berikut (gunakan mouse atau sentuh).</div>
      <div class="border rounded-3" style="background:#fff; position:relative;">
        <canvas id="sigCanvas" style="width:100%; height:280px; display:block;"></canvas>
        <div id="sigHint" class="position-absolute top-50 start-50 translate-middle text-muted small" style="pointer-events:none;">
          Tanda tangani di sini
        </div>
      </div>
    </div>

    <div class="d-flex gap-2 mb-3">
      <button id="btnClear" type="button" class="btn btn-outline-secondary">
        <i class="ti ti-eraser"></i> Bersihkan
      </button>
      <button id="btnUndo" type="button" class="btn btn-outline-secondary">
        <i class="ti ti-arrow-back-up"></i> Undo
      </button>
    </div>

    <form id="signForm" method="POST" action="{{ route('documents.sign.store', $document) }}">
      @csrf
      <input type="hidden" name="signature" id="signatureInput">
      <button id="btnSave" type="submit" class="btn btn-primary">
        <i class="ti ti-device-floppy"></i> Simpan Tanda Tangan
      </button>
    </form>
  </div>

  @if($document->signature_path)
    <div class="card-soft p-3 mt-3">
      <div class="text-muted small mb-1">Tanda tangan tersimpan:</div>
      <img src="{{ asset('storage/'.$document->signature_path) }}" alt="signature" style="max-height:120px">
      <div class="text-muted small mt-1">
        Ditandatangani {{ $document->signed_at?->diffForHumans() }} oleh {{ $document->receiver}}
      </div>
    </div>
  @endif
@endsection

@push('scripts')
  {{-- Signature Pad --}}
  <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const canvas   = document.getElementById('sigCanvas');
      const hint     = document.getElementById('sigHint');
      const btnClear = document.getElementById('btnClear');
      const btnUndo  = document.getElementById('btnUndo');
      const btnSave  = document.getElementById('btnSave');
      const input    = document.getElementById('signatureInput');

      // Resize canvas to device pixel ratio (tajam)
      function resizeCanvas() {
        const ratio = Math.max(window.devicePixelRatio || 1, 1);
        const rect = canvas.getBoundingClientRect();
        canvas.width  = rect.width * ratio;
        canvas.height = rect.height * ratio;
        const ctx = canvas.getContext('2d');
        ctx.scale(ratio, ratio);
        // kasih background putih biar PNG tidak transparan
        ctx.fillStyle = '#fff';
        ctx.fillRect(0, 0, rect.width, rect.height);
      }
      resizeCanvas();
      window.addEventListener('resize', resizeCanvas);

      const sigPad = new SignaturePad(canvas, {
        backgroundColor: 'rgba(255,255,255,1)',
        penColor: '#111827',
        minWidth: 0.8,
        maxWidth: 2.2,
        throttle: 16
      });

      function updateHint() {
        hint.style.display = sigPad.isEmpty() ? 'block' : 'none';
      }
      updateHint();

      btnClear.addEventListener('click', () => {
        sigPad.clear();
        resizeCanvas();
        updateHint();
      });

      btnUndo.addEventListener('click', () => {
        const data = sigPad.toData();
        if (data.length) {
          data.pop();
          sigPad.fromData(data);
        }
        updateHint();
      });

      // Submit -> isi hidden input dengan PNG dataURL
      btnSave.addEventListener('click', (e) => {
        if (sigPad.isEmpty()) {
          e.preventDefault();
          alert('Silakan buat tanda tangan terlebih dahulu.');
          return false;
        }
        // toDataURL sudah PNG (background putih sudah di-draw)
        input.value = sigPad.toDataURL('image/png');
      });
    });
  </script>
@endpush
