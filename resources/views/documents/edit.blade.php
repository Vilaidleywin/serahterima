@extends('layouts.app')

@section('content')
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h2 class="fw-bold mb-0">Edit Dokumen</h2>
      <div class="text-muted small">Perbarui data dokumen</div>
    </div>
  </div>

  <div class="card-soft p-4">
    <form method="POST" action="{{ route('documents.update', $document) }}" enctype="multipart/form-data">
      @csrf
      @method('PATCH')

      <div class="row g-3">
        {{-- NOMOR --}}
        <div class="col-md-4">
          <label class="form-label fw-semibold">Nomor Dokumen</label>
          <input type="text"
                 name="number"
                 class="form-control search"
                 value="{{ old('number', $document->number) }}">
          @error('number') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>

        {{-- JUDUL --}}
        <div class="col-md-8">
          <label class="form-label fw-semibold">Judul Dokumen</label>
          <input type="text"
                 name="title"
                 class="form-control search"
                 value="{{ old('title', $document->title) }}">
          @error('title') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>

        {{-- PENGIRIM --}}
        <div class="col-md-6">
          <label class="form-label fw-semibold">Pengirim</label>
          <input type="text"
                 name="sender"
                 class="form-control search"
                 value="{{ old('sender', $document->sender) }}">
          @error('sender') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>

        {{-- PENERIMA --}}
        <div class="col-md-6">
          <label class="form-label fw-semibold">Penerima</label>
          <input type="text"
                 name="receiver"
                 class="form-control search"
                 value="{{ old('receiver', $document->receiver) }}">
          @error('receiver') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>

        {{-- DIVISI TUJUAN DOKUMEN --}}
        <div class="col-md-6">
          <label class="form-label fw-semibold">Divisi Tujuan Dokumen</label>
          <select name="division_tujuan"
                  class="form-select @error('division_tujuan') is-invalid @enderror"
                  id="division-tujuan-select">
            <option value="">-- Pilih Divisi --</option>
            @foreach($divisions as $d)
              <option value="{{ $d }}"
                {{ old('division_tujuan', $divisionValue ?? '') === $d ? 'selected' : '' }}>
                {{ $d }}
              </option>
            @endforeach
            <option value="Other"
              {{ old('division_tujuan', $divisionValue ?? '') === 'Other' ? 'selected' : '' }}>
              Other
            </option>
          </select>
          @error('division_tujuan') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- DIVISI TUJUAN (OTHER) --}}
        <div class="col-md-6"
             id="division-other-group"
             style="{{ old('division_tujuan', $divisionValue ?? '') === 'Other' ? '' : 'display:none;' }}">
          <label class="form-label fw-semibold">Divisi Tujuan (Other)</label>
          <input type="text"
                 name="division_tujuan_other"
                 id="division_tujuan_other"
                 class="form-control @error('division_tujuan_other') is-invalid @enderror"
                 placeholder="Isi bila memilih Other"
                 value="{{ old('division_tujuan_other', $divisionOther ?? '') }}">
          @error('division_tujuan_other') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- KETERANGAN TUJUAN / TEMPAT --}}
        <div class="col-md-6">
          <label class="form-label fw-semibold">Keterangan Tujuan / Tempat</label>
          <select name="destination_desc"
                  class="form-select @error('destination_desc') is-invalid @enderror"
                  id="destination-desc-select">
            <option value="">-- Pilih Keterangan --</option>
            @foreach($destinationOptions as $opt)
              <option value="{{ $opt }}"
                {{ old('destination_desc', $destinationDesc ?? '') === $opt ? 'selected' : '' }}>
                {{ $opt }}
              </option>
            @endforeach
            <option value="Other"
              {{ old('destination_desc', $destinationDesc ?? '') === 'Other' ? 'selected' : '' }}>
              Other
            </option>
          </select>
          @error('destination_desc') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- KETERANGAN TUJUAN (OTHER) --}}
        <div class="col-md-6"
             id="destination-desc-other-group"
             style="{{ old('destination_desc', $destinationDesc ?? '') === 'Other' ? '' : 'display:none;' }}">
          <label class="form-label fw-semibold">Keterangan Tujuan (Other)</label>
          <input type="text"
                 name="destination_desc_other"
                 id="destination_desc_other"
                 class="form-control @error('destination_desc_other') is-invalid @enderror"
                 placeholder="Isi bila memilih Other"
                 value="{{ old('destination_desc_other', $destinationDescOther ?? '') }}">
          @error('destination_desc_other') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- NOMINAL --}}
        <div class="col-md-6">
          <label class="form-label fw-semibold">Nominal (Rp)</label>
          <input type="number"
                 name="amount_idr"
                 class="form-control search"
                 value="{{ old('amount_idr', $document->amount_idr) }}">
          @error('amount_idr') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>

        {{-- TANGGAL --}}
        <div class="col-md-6">
          <label class="form-label fw-semibold">Tanggal</label>
          <input type="date"
                 name="date"
                 class="form-control search"
                 value="{{ old('date', $document->date?->format('Y-m-d')) }}">
          @error('date') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>

        {{-- FILE (OPSIONAL) --}}
        <div class="col-md-6">
          <label class="form-label fw-semibold">Lampiran (Wajib)</label>
          <input type="file"
                 name="file"
                 class="form-control @error('file') is-invalid @enderror">
          @error('file') <div class="invalid-feedback">{{ $message }}</div> @enderror

          @if($document->file_path)
            <div class="form-text mt-1">
              File sekarang:
              <a href="{{ asset('storage/'.$document->file_path) }}" target="_blank">Lihat lampiran</a>
            </div>
          @endif
        </div>

        {{-- CATATAN --}}
        <div class="col-12">
          <label class="form-label fw-semibold">Catatan</label>
          <textarea name="description"
                    rows="3"
                    class="form-control search">{{ old('description', $document->description) }}</textarea>
          @error('description') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>

        {{-- BUTTON --}}
        <div class="col-12 text-end">
          <a href="{{ route('documents.index') }}" class="btn btn-light me-2">Batal</a>
          <button class="btn btn-primary px-4">
            <i class="ti ti-device-floppy"></i> Simpan Perubahan
          </button>
        </div>
      </div>
    </form>
  </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  // division other toggle
  const divisionSelect = document.getElementById('division-tujuan-select');
  const divisionOtherGroup = document.getElementById('division-other-group');
  const divisionOtherInput = document.getElementById('division_tujuan_other');

  function toggleDivisionOther() {
    if (!divisionSelect || !divisionOtherGroup) return;
    if (divisionSelect.value === 'Other') {
      divisionOtherGroup.style.display = '';
      if (divisionOtherInput) divisionOtherInput.setAttribute('required', 'required');
    } else {
      divisionOtherGroup.style.display = 'none';
      if (divisionOtherInput) {
        divisionOtherInput.removeAttribute('required');
        divisionOtherInput.value = '';
      }
    }
  }
  divisionSelect?.addEventListener('change', toggleDivisionOther);
  toggleDivisionOther();

  // destination desc other toggle
  const destSelect = document.getElementById('destination-desc-select');
  const destOtherGroup = document.getElementById('destination-desc-other-group');
  const destOtherInput = document.getElementById('destination_desc_other');

  function toggleDestOther() {
    if (!destSelect || !destOtherGroup) return;
    if (destSelect.value === 'Other') {
      destOtherGroup.style.display = '';
      if (destOtherInput) destOtherInput.setAttribute('required', 'required');
    } else {
      destOtherGroup.style.display = 'none';
      if (destOtherInput) {
        destOtherInput.removeAttribute('required');
        destOtherInput.value = '';
      }
    }
  }
  destSelect?.addEventListener('change', toggleDestOther);
  toggleDestOther();
});
</script>
@endpush
