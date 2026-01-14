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
        {{-- NOMOR (MULTI) --}}
        <div class="col-md-4">
          <label class="form-label fw-semibold">Nomor Dokumen</label>

          <div id="number-wrapper" class="d-grid gap-2">
            @php
              $oldNumbers = old('number', ['']);
              if (!is_array($oldNumbers) || count($oldNumbers) === 0) $oldNumbers = [''];
            @endphp

            @foreach($oldNumbers as $i => $val)
              <div class="input-group number-row">
                <input type="text"
                       name="number[]"
                       class="form-control search @error("number.$i") is-invalid @enderror"
                       value="{{ $val }}"
                       placeholder="Masukkan nomor dokumen">

                <button type="button"
                        class="btn btn-outline-danger remove-number px-3"
                        title="Hapus"
                        {{ $i === 0 ? 'disabled' : '' }}>
                  Hapus
                </button>
              </div>

              @error("number.$i") <div class="text-danger small">{{ $message }}</div> @enderror
            @endforeach
          </div>

          <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="add-number">
            Tambah Nomor
          </button>
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
            <option value="Cyber 1" {{ old('destination_desc') === 'Cyber 1' ? 'selected' : '' }}>Cyber 1</option>
            <option value="Gudang Cakung" {{ old('destination_desc') === 'Gudang Cakung' ? 'selected' : '' }}>Gudang Cakung</option>
            <option value="PID Kemayoran" {{ old('destination_desc') === 'PID Kemayoran' ? 'selected' : '' }}>PID Kemayoran</option>
            <option value="Other" {{ old('destination_desc') === 'Other' ? 'selected' : '' }}>Other</option>
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
          <label class="form-label fw-semibold">Lampiran</label>
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

  // ===== MULTI NOMOR (TAMBAHAN) =====
  const addBtn = document.getElementById('add-number');
  const wrapper = document.getElementById('number-wrapper');

  function updateRemoveButtons() {
    if (!wrapper) return;
    const rows = wrapper.querySelectorAll('.number-row');
    rows.forEach((row, idx) => {
      const btn = row.querySelector('.remove-number');
      if (btn) btn.disabled = (idx === 0);
    });
  }

  function addNumberRow(focus = true) {
    if (!wrapper) return;
    const row = document.createElement('div');
    row.className = 'input-group number-row';
    row.innerHTML = `
      <input type="text" name="number[]" class="form-control search" placeholder="Masukkan nomor dokumen">
      <button type="button" class="btn btn-outline-danger remove-number px-3" title="Hapus">Hapus</button>
    `;
    wrapper.appendChild(row);
    updateRemoveButtons();
    if (focus) {
      const input = row.querySelector('input[name="number[]"]');
      if (input) input.focus();
    }
  }

  if (addBtn && wrapper) {
    addBtn.addEventListener('click', () => addNumberRow(true));

    wrapper.addEventListener('click', (e) => {
      const btn = e.target.closest('.remove-number');
      if (!btn) return;
      const row = btn.closest('.number-row');
      if (row) row.remove();
      updateRemoveButtons();
    });

    updateRemoveButtons();
  }

  // ✅ ENTER di input nomor => tambah nomor baru, bukan submit
  document.addEventListener('keydown', function (e) {
    if (e.key !== 'Enter') return;
    const target = e.target;
    if (target && target.matches('input[name="number[]"]')) {
      e.preventDefault();

      // optional: kalau mau Enter hanya jalan kalau input gak kosong, aktifin ini:
      // if (!target.value.trim()) return;

      addNumberRow(true);
    }
  });
  // ===== END MULTI NOMOR =====

  function toggleDestinationOther() {
    if (!destSelect || !destGroup || !destInput) return;

    if (destSelect.value === 'Other') {
      destGroup.style.display = '';
      destInput.setAttribute('required', 'required');
    } else {
      destGroup.style.display = 'none';
      destInput.removeAttribute('required');
      destInput.classList.remove('is-invalid');
    }
  }

  if (destSelect && destGroup && destInput) {
    destSelect.addEventListener('change', toggleDestinationOther);
    toggleDestinationOther();
  }

  if (select && group) {
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
        }
      }
    }

    select.addEventListener('change', toggleOther);
    toggleOther();
  }

  // client-side submit validation: pastikan destination_other terisi bila Other dipilih
  if (form) {
    form.addEventListener('submit', function (ev) {
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
          const top = destInput.getBoundingClientRect().top + window.scrollY - 120;
          window.scrollTo({ top, behavior: 'smooth' });
          return false;
        }
      }

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
