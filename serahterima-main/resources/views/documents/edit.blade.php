@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="fw-bold mb-0">Edit Dokumen</h2>
            <div class="text-muted small">Ubah informasi dokumen serah terima</div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('documents.index') }}" class="btn btn-ghost">
                <i class="ti ti-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="card-soft p-4">
        {{-- alert error global (kalau ada banyak kesalahan) --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <div class="fw-semibold mb-1">Periksa input kamu:</div>
                <ul class="mb-0">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('documents.update', $document->id) }}" method="POST" enctype="multipart/form-data"
            novalidate>
            @csrf
            @method('PUT')

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Nomor Dokumen</label>
                    <input type="text" name="number" value="{{ old('number', $document->number) }}"
                        class="form-control @error('number') is-invalid @enderror" placeholder="Masukkan nomor dokumen"
                        required>
                    @error('number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Judul</label>
                    <input type="text" name="title" value="{{ old('title', $document->title) }}"
                        class="form-control @error('title') is-invalid @enderror" placeholder="Masukkan judul dokumen"
                        required>
                    @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Penerima</label>
                    <input type="text" name="receiver" value="{{ old('receiver', $document->receiver) }}"
                        class="form-control @error('receiver') is-invalid @enderror" placeholder="Nama penerima">
                    @error('receiver') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Tujuan</label>
                    <input type="text" name="destination" class="form-control search" value="{{ old('destination') }}"
                        placeholder="Masukkan tujuan dokumen">
                    @error('destination') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Nominal (Rp)</label>
                    <input type="number" name="amount_idr" step="1" min="0"
                        value="{{ old('amount_idr', $document->amount_idr) }}"
                        class="form-control @error('amount_idr') is-invalid @enderror" placeholder="Masukkan nominal">
                    @error('amount_idr') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Tanggal</label>
                    <input type="date" name="date" value="{{ old('date', optional($document->date)->format('Y-m-d')) }}"
                        class="form-control @error('date') is-invalid @enderror">
                    @error('date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Status</label>
                    <select name="status" class="form-select @error('status') is-invalid @enderror">
                        @foreach(['PENDING', 'DONE', 'FAILED'] as $s)
                            <option value="{{ $s }}" {{ old('status', $document->status) === $s ? 'selected' : '' }}>
                                {{ $s }}
                            </option>
                        @endforeach
                    </select>
                    @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Lampiran (Opsional)</label>
                    <input type="file" name="file" class="form-control @error('file') is-invalid @enderror">
                    @error('file') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold">Deskripsi</label>
                    <textarea name="description" rows="4" class="form-control @error('description') is-invalid @enderror"
                        placeholder="Tambahkan deskripsi">{{ old('description', $document->description) }}</textarea>
                    @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="d-flex justify-content-end mt-4 gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="ti ti-device-floppy"></i> Simpan Perubahan
                </button>
                <a href="{{ route('documents.index') }}" class="btn btn-ghost">Batal</a>
            </div>
        </form>
    </div>

@endsection