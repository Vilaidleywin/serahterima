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
          <input type="text" name="number" class="form-control search" value="{{ old('number') }}" required>
          @error('number') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-8">
          <label class="form-label fw-semibold">Judul Dokumen</label>
          <input type="text" name="title" class="form-control search" value="{{ old('title') }}" required>
          @error('title') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-6">
          <label class="form-label fw-semibold">Pengirim</label>
          <input type="text" name="sender" class="form-control search" value="{{ old('sender') }}" required>
          @error('sender') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-6">
          <label class="form-label fw-semibold">Penerima</label>
          <input type="text" name="receiver" class="form-control search" value="{{ old('receiver') }}" required>
          @error('receiver') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-6">
          <label class="form-label fw-semibold">Tujuan</label>
          <input type="text" name="destination_desc" class="form-control search" value="{{ old('destination_desc') }}"
            required>
          @error('destination_desc') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-6">
          <label class="form-label fw-semibold">Divisi Tujuan Dokumen</label>

          @php
            $selectedValue = old('division_tujuan', data_get($document ?? null, 'division_tujuan', ''));
            $hasOther = collect($divisions)->map(fn($d) => strtoupper((string) ($d ?? '')))->contains('OTHER');
          @endphp

          <select name="division_tujuan" id="division_tujuan" class="form-select search" required>
            <option value="">— Pilih Divisi Tujuan —</option>
            @foreach($divisions as $div)
              @php $val = (string) $div; @endphp
              <option value="{{ $val }}" {{ ($selectedValue == $val) ? 'selected' : '' }}>
                {{ $val }}
              </option>
            @endforeach
            @unless($hasOther)
              <option value="Other" {{ (strtoupper((string) $selectedValue) === 'OTHER') ? 'selected' : '' }}>Other</option>
            @endunless
          </select>

          @error('division_tujuan')
            <div class="text-danger small">{{ $message }}</div>
          @enderror

          @php $isOther = strtoupper((string) $selectedValue) === 'OTHER'; @endphp

          <input type="text" name="division_tujuan_other" id="division_tujuan_other" class="form-control mt-2"
            placeholder="Tulis nama divisi tujuan lainnya..."
            value="{{ $isOther ? old('division_tujuan_other', data_get($document ?? null, 'division_tujuan', '')) : old('division_tujuan_other') }}"
            style="{{ $isOther ? '' : 'display:none;' }}">

          @error('division_tujuan_other')
            <div class="text-danger small">{{ $message }}</div>
          @enderror
        </div>

        <div class="col-md-6">
          <label class="form-label fw-semibold">Nominal (Rp)</label>
          <input type="number" name="amount_idr" class="form-control search" value="{{ old('amount_idr') }}" required>
          @error('amount_idr') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-6">
          <label class="form-label fw-semibold">Tanggal</label>
          <input type="date" name="date" class="form-control search" value="{{ old('date') }}" required>
          @error('date') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-6">
          <label class="form-label fw-semibold">Lampiran (Wajib)</label>
          <input type="file" name="file" class="form-control @error('file') is-invalid @enderror" required>
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

@push('scripts')
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const sel = document.getElementById('division_tujuan');
      const other = document.getElementById('division_tujuan_other');
      if (!sel || !other) return;

      function toggleOther() {
        if ((sel.value || '').toString().trim().toUpperCase() === 'OTHER') {
          other.style.display = '';
          // kalau mau division_tujuan_other ikut wajib,
          // bisa tambahin required saat Other dipilih:
          other.setAttribute('required', 'required');
          other.focus();
        } else {
          other.style.display = 'none';
          other.value = '';
          other.removeAttribute('required');
        }
      }

      sel.addEventListener('change', toggleOther);
      toggleOther();
    });
  </script>
@endpush