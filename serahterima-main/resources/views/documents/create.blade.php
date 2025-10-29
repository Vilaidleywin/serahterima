@extends('layouts.app')

@section('content')
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h2 class="fw-bold mb-0">Tambah Dokumen</h2>
      <div class="text-muted small">Isi data dokumen baru</div>
    </div>
    <a href="{{ route('documents.index') }}" class="btn btn-ghost">
      <i class="ti ti-arrow-left"></i> Kembali
    </a>
  </div>

  <div class="card-soft p-4">
    <form method="POST" action="{{ route('documents.store') }}" enctype="multipart/form-data">
      @csrf
      <div class="row g-3">
        <div class="col-md-4">
          <label class="form-label fw-semibold">Nomor Dokumen</label>
          <input type="text" name="number" class="form-control search" value="{{ old('number') }}"
            placeholder="Misal: ST-005">
          @error('number') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-8">
          <label class="form-label fw-semibold">Judul Dokumen</label>
          <input type="text" name="title" class="form-control search" value="{{ old('title') }}"
            placeholder="Judul dokumen">
          @error('title') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-6">
          <label class="form-label fw-semibold">Penerima</label>
          <input type="text" name="receiver" class="form-control search" value="{{ old('receiver') }}"
            placeholder="Nama penerima">
          @error('receiver') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-6">
          <label class="form-label fw-semibold">Tujuan</label>
          <input type="text" name="destination" class="form-control search" value="{{ old('destination') }}"
            placeholder="Masukkan tujuan dokumen">
          @error('destination') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-6">
          <label class="form-label fw-semibold">Nominal (Rp)</label>
          <input type="number" name="amount_idr" class="form-control search" value="{{ old('amount_idr') }}"
            placeholder="cth: 2000000">
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
            <option value="PENDING" {{ old('status') == 'PENDING' ? 'selected' : '' }}>PENDING</option>
            <option value="DONE" {{ old('status') == 'DONE' ? 'selected' : '' }}>DONE</option>
            <option value="FAILED" {{ old('status') == 'FAILED' ? 'selected' : '' }}>FAILED</option>
          </select>
          @error('status') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>

        <div class="col-12 text-end">
          <button class="btn btn-primary px-4"><i class="ti ti-device-floppy"></i> Simpan</button>
        </div>
      </div>
    </form>
  </div>
@endsection