@extends('layouts.app')

@section('content')
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h2 class="fw-bold mb-0">Edit Dokumen</h2>
      <div class="text-muted small">Status dikelola otomatis. Jika ditolak, dokumen tidak dapat diubah.</div>
    </div>
    <a href="{{ route('documents.index') }}" class="btn btn-outline-secondary">
      <i class="ti ti-arrow-left"></i> Kembali
    </a>
  </div>

  @if($document->status === 'REJECTED')
    <div class="alert alert-warning" role="alert" style="border-radius:10px;">
      <strong>Dokumen REJECTED.</strong> Form dinonaktifkan.
    </div>
  @endif

  <div class="card-soft p-4">
    <form method="POST" action="{{ route('documents.update', $document) }}" enctype="multipart/form-data">
      @csrf @method('PUT')

      @php $locked = $document->status === 'REJECTED'; @endphp

      <div class="row g-3">
        <div class="col-12">
          <div class="mb-2">
            <span class="text-muted small">Status:</span>
            @include('shared.status-badge', ['status' => $document->status])
          </div>
        </div>

        <div class="col-md-4">
          <label class="form-label fw-semibold">Nomor Dokumen</label>
          <input type="text" name="number" class="form-control search"
                 value="{{ old('number', $document->number) }}" {{ $locked ? 'readonly' : '' }}>
          @error('number') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-8">
          <label class="form-label fw-semibold">Judul Dokumen</label>
          <input type="text" name="title" class="form-control search"
                 value="{{ old('title', $document->title) }}" {{ $locked ? 'readonly' : '' }}>
          @error('title') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-6">
          <label class="form-label fw-semibold">Pengirim</label>
          <input type="text" name="sender" class="form-control search"
                 value="{{ old('sender', $document->sender) }}" {{ $locked ? 'readonly' : '' }}>
          @error('sender') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-6">
          <label class="form-label fw-semibold">Penerima</label>
          <input type="text" name="receiver" class="form-control search"
                 value="{{ old('receiver', $document->receiver) }}" {{ $locked ? 'readonly' : '' }}>
          @error('receiver') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-6">
          <label class="form-label fw-semibold">Tujuan</label>
          <input type="text" name="destination" class="form-control search"
                 value="{{ old('destination', $document->destination) }}" {{ $locked ? 'readonly' : '' }}>
          @error('destination') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-6">
          <label class="form-label fw-semibold">Divisi</label>
          <select name="division" class="form-select search" {{ $locked ? 'disabled' : '' }}>
            <option value="">— Pilih Divisi —</option>
            @foreach($divisions as $div)
              <option value="{{ $div }}" @selected(old('division', $document->division) === $div)>{{ $div }}</option>
            @endforeach
          </select>
          @error('division') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-6">
          <label class="form-label fw-semibold">Nominal (Rp)</label>
          <input type="number" name="amount_idr" class="form-control search"
                 value="{{ old('amount_idr', $document->amount_idr) }}" {{ $locked ? 'readonly' : '' }}>
          @error('amount_idr') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-6">
          <label class="form-label fw-semibold">Tanggal</label>
          <input type="date" name="date" class="form-control search"
                 value="{{ old('date', optional($document->date)->format('Y-m-d')) }}" {{ $locked ? 'readonly' : '' }}>
          @error('date') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-6">
          <label class="form-label fw-semibold">Lampiran (Opsional)</label>
          <input type="file" name="file" class="form-control" {{ $locked ? 'disabled' : '' }}>
          @error('file') <div class="text-danger small">{{ $message }}</div> @enderror
          @if($document->file_path)
            <div class="mt-1">
              <a href="{{ Storage::url($document->file_path) }}" target="_blank" class="small">
                Lihat lampiran saat ini
              </a>
            </div>
          @endif
        </div>

        <div class="col-12">
          <label class="form-label fw-semibold">Catatan</label>
          <textarea name="description" rows="3" class="form-control search" {{ $locked ? 'readonly' : '' }}>{{ old('description', $document->description) }}</textarea>
          @error('description') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>

        @unless($locked)
          <div class="col-12 text-end">
            <button class="btn btn-primary px-4"><i class="ti ti-device-floppy"></i> Simpan</button>
          </div>
        @endunless
      </div>
    </form>
  </div>
@endsection
