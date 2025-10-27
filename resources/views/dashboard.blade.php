@extends('layouts.app')

@section('content')
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h2 class="fw-bold mb-0">Dashboard</h2>
      <div class="text-muted">Ringkasan dokumen</div>
    </div>
  </div>

  <div class="row g-3 mb-3">
    <div class="col-md-4">
      <div class="card-soft p-3">
        <div class="text-muted small">Total Dokumen</div>
        <div class="fs-3 fw-semibold">{{ $total }}</div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card-soft p-3">
        <div class="text-muted small">Menunggu Persetujuan</div>
        <div class="fs-3 fw-semibold">{{ $pending }}</div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card-soft p-3">
        <div class="text-muted small">Selesai</div>
        <div class="fs-3 fw-semibold">{{ $done }}</div>
      </div>
    </div>
  </div>

  <h5 class="mb-2">Daftar Dokumen Terbaru</h5>
  <div class="table-responsive card-soft p-2">
    <table class="table table-borderless align-middle mb-0 tbl-relaxed">
      <thead>
        <tr>
          <th class="text-center" style="width:60px">No</th>
          <th>No Dokumen</th>
          <th>Judul</th>
          <th>Penerima</th>
          <th class="text-end" style="width:140px">Status</th>
        </tr>
      </thead>
      <tbody>
        @foreach($latest as $i => $d)
          <tr>
            <td class="text-center">{{ $i + 1 }}</td>
            <td>{{ $d->number }}</td>
            <td>{{ $d->title }}</td>
            <td>{{ $d->receiver }}</td>
            <td class="text-end">
              @include('shared.status-badge', ['status' => $d->status])
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
@endsection