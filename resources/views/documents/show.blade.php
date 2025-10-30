@extends('layouts.app')

@section('content')
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="fw-bold mb-0">Detail Dokumen</h2>
    <a href="{{ route('documents.index') }}" class="btn btn-ghost">
      <i class="ti ti-arrow-left"></i> Kembali
    </a>
  </div>

  <div class="card-soft p-4">
    <dl class="row mb-0">
      <dt class="col-sm-3">No. Dokumen</dt>
      <dd class="col-sm-9">{{ $document->number }}</dd>

      <dt class="col-sm-3">Judul</dt>
      <dd class="col-sm-9">{{ $document->title }}</dd>

      <dt class="col-sm-3">Penerima</dt>
      <dd class="col-sm-9">{{ $document->receiver }}</dd>

      <dt class="col-sm-3">Tujuan</dt>
      <dd class="col-sm-9">{{ $document->destination ?? '-' }}</dd>

      <dt class="col-sm-3">Nominal</dt>
      <dd class="col-sm-9">Rp {{ number_format($document->amount_idr, 0, ',', '.') }}</dd>

      <dt class="col-sm-3">Tanggal</dt>
      <dd class="col-sm-9">{{ $document->date->format('d M Y') }}</dd>

      <dt class="col-sm-3">Status</dt>
      <dd class="col-sm-9">@include('shared.status-badge', ['status' => $document->status])</dd>

      <dt class="col-sm-3">Deskripsi</dt>
      <dd class="col-sm-9">{{ $document->description ?? '-' }}</dd>
    </dl>
  </div>
@endsection
