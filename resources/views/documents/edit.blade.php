@extends('layouts.app')

@section('content')
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h2 class="fw-bold mb-0">Edit Dokumen</h2>
    </div>
  </div>

  <div class="card-soft p-4">
    <form method="POST" action="{{ route('documents.update', $document) }}" enctype="multipart/form-data">
      @csrf
      @method('PATCH')
      <div class="row g-3">
        <div class="col-md-4">
          <label class="form-label fw-semibold">Nomor Dokumen</label>
          <input type="text" name="number" class="form-control search" value="{{ old('number', $document->number) }}">
          @error('number') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-8">
          <label class="form-label fw-semibold">Judul Dokumen</label>
          <input type="text" name="title" class="form-control search" value="{{ old('title', $document->title) }}">
          @error('title') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-6">
          <label class="form-label fw-semibold">Pengirim</label>
          <input type="text" name="sender" class="form-control search" value="{{ old('sender', $document->sender) }}">
          @error('sender') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-6">
          <label class="form-label fw-semibold">Penerima</label>
          <input type="text" name="receiver" class="form-control search" value="{{ old('receiver', $document->receiver) }}">
          @error('receiver') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-6">
          <label class="form-label fw-semibold">Tujuan</label>
          <input type="text" name="destination" class="form-control search" value="{{ old('destination', $document->destination) }}">
          @error('destination') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-6">
          <label class="form-label fw-semibold">Nominal (Rp)</label>
          <input type="number" name="amount_idr" class="form-control search" value="{{ old('amount_idr', $document->amount_idr) }}">
          @error('amount_idr') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-6">
          <label class="form-label fw-semibold">Tanggal</label>
          <input type="date" name="date" class="form-control search" value="{{ old('date', $document->date?->format('Y-m-d')) }}">
          @error('date') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-6">
          <label class="form-label fw-semibold">Lampiran (Opsional)</label>
          <input type="file" name="file" class="form-control @error('file') is-invalid @enderror">
          @error('file') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-12">
          <label class="form-label fw-semibold">Catatan</label>
          <textarea name="description" rows="3" class="form-control search">{{ old('description', $document->description) }}</textarea>
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
