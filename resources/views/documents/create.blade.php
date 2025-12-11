@extends('layouts.app')

@section('content')
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h2 class="fw-bold mb-0">Tambah Dokumen</h2>
      <div class="text-muted small">Input dokumen baru ke sistem</div>
    </div>
  </div>

  <div class="card-soft p-4">
    <form id="docForm" method="POST" action="{{ route('documents.store') }}" enctype="multipart/form-data">
      @csrf

      <div class="row g-3">
        {{-- NOMOR --}}
        <div class="col-md-4">
          <label class="form-label fw-semibold">Nomor Dokumen</label>
          <input type="text"
                 name="number"
                 class="form-control search"
                 value="{{ old('number') }}">
          @error('number') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>

        {{-- JUDUL --}}
        <div class="col-md-8">
          <label class="form-label fw-semibold">Judul Dokumen</label>
          <input type="text"
                 name="title"
                 class="form-control search"
                 value="{{ old('title') }}">
          @error('title') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>

        {{-- PENGIRIM --}}
        <div class="col-md-6">
          <label class="form-label fw-semibold">Pengirim</label>
          <input type="text"
                 name="sender"
                 class="form-control search"
                 value="{{ old('sender') }}">
          @error('sender') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>

        {{-- PENERIMA --}}
        <div class="col-md-6">
          <label class="form-label fw-semibold">Penerima</label>
          <input type="text"
                 name="receiver"
                 class="form-control search"
                 value="{{ old('receiver') }}">
          @error('receiver') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>

        {{-- DIVISI TUJUAN DOKUMEN --}}
        <div class="col-md-6">
          <label class="form-label fw-semibold">Divisi Tujuan Dokumen</label>
          <select name="division_tujuan"
                  class="form-select @error('division_tujuan') is-invalid @enderror">
            <option value="">-- Pilih Divisi --</option>
            @foreach($divisions as $d)
              <option value="{{ $d }}" {{ old('division_tujuan') === $d ? 'selected' : '' }}>
                {{ $d }}
              </option>
            @endforeach
            <option value="Other" {{ old('division_tujuan') === 'Other' ? 'selected' : '' }}>
              Other
            </option>
          </select>
          @error('division_tujuan') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- DIVISI TUJUAN (OTHER) --}}
        <div class="col-md-6"
             id="division-other-group"
             style="{{ old('division_tujuan') === 'Other' ? '' : 'display:none;' }}">
          <label class="form-label fw-semibold">Divisi Tujuan (Other)</label>
          <input type="text"
                 name="division_tujuan_other"
                 id="division_tujuan_other"
                 class="form-control @error('division_tujuan_other') is-invalid @enderror"
                 placeholder="Isi bila memilih Other"
                 value="{{ old('division_tujuan_other') }}">
          @error('division_tujuan_other') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- KETERANGAN TUJUAN / TEMPAT --}}
        <div class="col-md-12">
          <label class="form-label fw-semibold">Keterangan Tujuan / Tempat</label>
          <select name="destination_desc"
                  id="destination-select"
                  class="form-select @error('destination_desc') is-invalid @enderror">
            <option value="">-- Pilih Tujuan --</option>
            <option value="Cyber 1" {{ old('destination_desc') === 'Cyber 1' ? 'selected' : '' }}>
              Cyber 1
            </option>
            <option value="Gudang Cakung" {{ old('destination_desc') === 'Gudang Cakung' ? 'selected' : '' }}>
              Gudang Cakung
            </option>
            <option value="PID Kemayoran" {{ old('destination_desc') === 'PID Kemayoran' ? 'selected' : '' }}>
              PID Kemayoran
            </option>
            <option value="Other" {{ old('destination_desc') === 'Other' ? 'selected' : '' }}>
              Other
            </option>
          </select>
          @error('destination_desc') <div class="invalid-feedback">{{ $message }}</div> @enderror

          <div class="mt-3"
               id="destination-other-group"
               style="{{ old('destination_desc') === 'Other' ? '' : 'display:none;' }}">
            <label class="form-label fw-semibold">Tujuan (Other)</label>
            <input type="text"
                   name="destination_desc_other"
                   id="destination_desc_other"
                   class="form-control @error('destination_desc_other') is-invalid @enderror"
                   placeholder="Isi bila memilih Other"
                   value="{{ old('destination_desc_other') }}">
            <div class="invalid-feedback">Kolom tujuan wajib diisi jika memilih "Other".</div>
            @error('destination_desc_other') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
        </div>

        {{-- NOMINAL --}}
        <div class="col-md-6">
          <label class="form-label fw-semibold">Nominal (Rp)</label>
          <input type="number"
                 name="amount_idr"
                 class="form-control search"
                 value="{{ old('amount_idr') }}">
          @error('amount_idr') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>

        {{-- TANGGAL --}}
        <div class="col-md-6">
          <label class="form-label fw-semibold">Tanggal</label>
          <input type="date"
                 name="date"
                 class="form-control search"
                 value="{{ old('date') }}">
          @error('date') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>

        {{-- FILE --}}
        <div class="col-md-6">
          <label class="form-label fw-semibold">Lampiran (Wajib)</label>
          <input type="file"
                 name="file"
                 class="form-control @error('file') is-invalid @enderror">
          @error('file') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- CATATAN --}}
        <div class="col-12">
          <label class="form-label fw-semibold">Catatan (Optional)</label>
          <textarea name="description"
                    rows="3"
                    class="form-control search">{{ old('description') }}</textarea>
          @error('description') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>

        {{-- BUTTON --}}
        <div class="col-12 text-end">
          <a href="{{ route('documents.index') }}" class="btn btn-light me-2">Batal</a>
          <button type="submit" class="btn btn-primary px-4">
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
  const select = document.querySelector('select[name="division_tujuan"]');
  const group  = document.getElementById('division-other-group');

  // Destination select + Other
  const destSelect = document.getElementById('destination-select');
  const destGroup  = document.getElementById('destination-other-group');
  const destInput  = document.getElementById('destination_desc_other');

  // Form
  const form = document.getElementById('docForm');

  function toggleDestinationOther() {
    if (!destSelect || !destGroup || !destInput) return;

    if (destSelect.value === 'Other') {
      destGroup.style.display = '';
      destInput.setAttribute('required', 'required');
    } else {
      destGroup.style.display = 'none';
      destInput.removeAttribute('required');
      destInput.classList.remove('is-invalid');
      // kosongkan hanya jika ingin meng-clear; komentar jika mau pertahankan
      // destInput.value = '';
    }
  }

  if (destSelect && destGroup && destInput) {
    destSelect.addEventListener('change', toggleDestinationOther);
    toggleDestinationOther();
  }

  if (!select || !group) {
    // masih daftar destination handling di atas
  } else {
    function toggleOther() {
      if (select.value === 'Other') {
        group.style.display = '';
        const input = group.querySelector('input[name="division_tujuan_other"]');
        if (input) input.setAttribute('required', 'required');
      } else {
        group.style.display = 'none';
        const input = group.querySelector('input[name="division_tujuan_other"]');
        if (input) {
          input.removeAttribute('required');
          input.classList.remove('is-invalid');
          // input.value = '';
        }
      }
    }

    select.addEventListener('change', toggleOther);
    toggleOther();
  }

  // client-side submit validation: pastikan destination_other terisi bila Other dipilih
  if (form) {
    form.addEventListener('submit', function (ev) {
      // reset any previous invalid state
      if (destInput) destInput.classList.remove('is-invalid');

      if (destSelect && destSelect.value === 'Other') {
        const v = destInput && destInput.value ? destInput.value.trim() : '';
        if (!v) {
          ev.preventDefault();
          ev.stopPropagation();
          if (destInput) {
            destInput.classList.add('is-invalid');
            destInput.focus();
          }
          // optional: scroll to input
          const top = destInput.getBoundingClientRect().top + window.scrollY - 120;
          window.scrollTo({ top, behavior: 'smooth' });
          return false;
        }
      }

      // juga validasi division other kalau ada
      if (select && select.value === 'Other') {
        const divInput = document.querySelector('input[name="division_tujuan_other"]');
        if (divInput) {
          divInput.classList.remove('is-invalid');
          const vv = divInput.value ? divInput.value.trim() : '';
          if (!vv) {
            ev.preventDefault();
            ev.stopPropagation();
            divInput.classList.add('is-invalid');
            divInput.focus();
            const top2 = divInput.getBoundingClientRect().top + window.scrollY - 120;
            window.scrollTo({ top: top2, behavior: 'smooth' });
            return false;
          }
        }
      }

      return true;
    });
  }
});
</script>
@endpush
