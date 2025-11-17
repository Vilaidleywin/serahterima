@extends('layouts.app')

@section('content')
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h2 class="fw-bold mb-0">Tambah Dokumen</h2>
      
    </div>

  </div>

  <div class="card-soft p-4">
    <form method="POST" action="{{ route('documents.store') }}" enctype="multipart/form-data">
      @csrf
      <div class="row g-3">

        <div class="col-md-4">
          <label class="form-label fw-semibold">Nomor Dokumen</label>
          <input type="text" name="number" class="form-control search" value="{{ old('number') }}">
          @error('number') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-8">
          <label class="form-label fw-semibold">Judul Dokumen</label>
          <input type="text" name="title" class="form-control search" value="{{ old('title') }}">
          @error('title') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-6">
          <label class="form-label fw-semibold">Pengirim</label>
          <input type="text" name="sender" class="form-control search" value="{{ old('sender') }}">
          @error('sender') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-6">
          <label class="form-label fw-semibold">Penerima</label>
          <input type="text" name="receiver" class="form-control search" value="{{ old('receiver') }}">
          @error('receiver') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-6">
          <label class="form-label fw-semibold">Tujuan</label>
          <input type="text" name="destination" class="form-control search" value="{{ old('destination') }}">
          @error('destination') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-6">
  <label class="form-label fw-semibold">Divisi</label>
  <select name="division" id="division" class="form-select search">
    <option value="">— Pilih Divisi —</option>
    @foreach($divisions as $div)
      <option value="{{ $div }}" @selected(old('division', $document->division ?? '') === $div)>
        {{ $div }}
      </option>
    @endforeach
    @unless(collect($divisions)->contains('Other'))
      <option value="Other" @selected(strtoupper(old('division', $document->division ?? '')) === 'OTHER')>Other</option>
    @endunless
  </select>
  @error('division')
    <div class="text-danger small">{{ $message }}</div>
  @enderror

  {{-- Input teks tambahan kalau pilih Other --}}
  @php
    $isOther = strtoupper(old('division', $document->division ?? '')) === 'OTHER';
  @endphp
  <input type="text"
         name="division_other"
         id="division_other"
         class="form-control mt-2"
         placeholder="Tulis nama divisi lainnya..."
         value="{{ $isOther ? old('division_other', $document->division ?? '') : old('division_other') }}"
         style="{{ $isOther ? '' : 'display:none;' }}">
  @error('division_other')
    <div class="text-danger small">{{ $message }}</div>
  @enderror
</div>

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const sel = document.getElementById('division');
    const other = document.getElementById('division_other');
    if (!sel || !other) return;

    function toggleOther() {
      if ((sel.value || '').toUpperCase() === 'OTHER') {
        other.style.display = '';
        other.focus();
      } else {
        other.style.display = 'none';
        other.value = '';
      }
    }

    sel.addEventListener('change', toggleOther);
    toggleOther();
  });
</script>
@endpush


        <div class="col-md-6">
          <label class="form-label fw-semibold">Nominal (Rp)</label>
          <input type="number" name="amount_idr" class="form-control search" value="{{ old('amount_idr') }}">
          @error('amount_idr') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-6">
          <label class="form-label fw-semibold">Tanggal</label>
          <input type="date" name="date" class="form-control search" value="{{ old('date') }}">
          @error('date') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-6">
          <label class="form-label fw-semibold">Lampiran (Opsional)</label>
          <input type="file" name="file" class="form-control @error('file') is-invalid @enderror">
          @error('file') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-12">
          <label class="form-label fw-semibold">Catatan</label>
          <textarea name="description" rows="3" class="form-control search">{{ old('description') }}</textarea>
          @error('description') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>

        <div class="col-12 text-end">
          <button class="btn btn-primary px-4">
            <i class="ti ti-device-floppy"></i> Simpan
          </button>
        </div>
      </div>
    </form>
  </div>
@endsection
