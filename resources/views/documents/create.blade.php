@extends('layouts.app')

@section('content')
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h2 class="fw-bold mb-0">Tambah Dokumen</h2>
      <div class="text-muted small">Isi data dokumen baru</div>
    </div>
    <a href="{{ route('documents.index') }}" class="btn btn-outline-secondary">
      <i class="ti ti-arrow-left"></i> Kembali
    </a>
  </div>

  <div class="card-soft p-4">
    <form method="POST" action="{{ route('documents.store') }}" enctype="multipart/form-data">
      @csrf
      @method(isset($document) ? 'PUT' : 'POST')
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
          <input type="text" name="sender" class="form-control search" value="{{ old('sender') }}"
            placeholder="Nama pengirim dokumen">
          @error('sender') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
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
          <select name="division" class="form-select search">
            <option value="">— Pilih Divisi —</option>
            @foreach($divisions as $div)
              <option value="{{ $div }}" @selected(old('division') === $div)>{{ $div }}</option>
            @endforeach
          </select>
          @error('division') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>

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
          <label class="form-label fw-semibold">Status</label>
          <select name="status" class="form-select search">
            <option value="SUBMITTED" {{ old('status') == 'SUBMITTED' ? 'selected' : '' }}>SUBMITTED</option>
            <option value="REJECTED" {{ old('status') == 'REJECTED' ? 'selected' : '' }}>REJECTED</option>
          </select>
          @error('status') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-6">
          <label class="form-label fw-semibold">Lampiran (Opsional)</label>
          <input type="file" name="file" class="form-control @error('file') is-invalid @enderror"
            accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
          @error('file') <div class="invalid-feedback">{{ $message }}</div> @enderror

          @isset($document->file_path)
            @if($document->file_path)
              <div class="small mt-2">
                File saat ini: <a href="{{ Storage::url($document->file_path) }}" target="_blank">Lihat</a>
              </div>
            @endif
          @endisset
        </div>

        {{-- 📝 Catatan / Deskripsi --}}
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