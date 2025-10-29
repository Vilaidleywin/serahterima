@extends('layouts.app')

@section('content')
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="fw-bold mb-0">Tanda Tangan Dokumen</h2>
    <a href="{{ route('documents.index') }}" class="btn btn-ghost">
      <i class="ti ti-arrow-left"></i> Kembali
    </a>
  </div>

  <div class="card-soft p-4 text-center">
    <p class="mb-3">Silakan tanda tangani dokumen berikut:</p>
    <h5>{{ $document->title }}</h5>
    <p class="text-muted small mb-4">{{ $document->number }} — {{ $document->receiver }}</p>

    <button class="btn btn-success px-4 py-2">
      <i class="ti ti-check"></i> Tanda Tangani Sekarang
    </button>
  </div>
@endsection
