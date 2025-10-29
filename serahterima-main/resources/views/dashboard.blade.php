@extends('layouts.app')

@section('content')
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h2 class="fw-bold mb-0">Dashboard</h2>
      <div class="text-muted">Ringkasan dokumen</div>
    </div>
  </div>

  {{-- Ringkasan statistik --}}
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

  {{-- Tabel daftar dokumen terbaru --}}
  <h5 class="mb-2">Daftar Dokumen Terbaru</h5>
  <div class="table-responsive card-soft p-2">
    <table class="table table-borderless align-middle mb-0 tbl-relaxed">
      <thead>
        <tr>
          <th class="text-center" style="width:60px">No</th>
          <th style="width:140px">No. Dokumen</th>
          <th>Perihal</th>
          <th style="width:160px">Penerima</th>
          <th style="width:160px">Tujuan</th>
          <th style="width:140px">Nominal (Rp)</th>
          <th style="width:120px">Tanggal</th>
          <th style="width:120px" class="text-end">Status</th>
        </tr>
      </thead>
      <tbody>
        @forelse($latest as $i => $d)
          <tr>
            <td class="text-center">{{ $i + 1 }}</td>
            <td>{{ $d->number }}</td>
            <td>{{ $d->title }}</td>
            <td>{{ $d->receiver }}</td>
            <td>{{ $d->destination ?? '-' }}</td>
            <td>{{ $d->amount_idr_formatted }}</td>
            <td>{{ $d->date?->format('Y-m-d') }}</td>
            <td class="text-end">
              @include('shared.status-badge', ['status' => $d->status])
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="8" class="text-center py-4">Belum ada dokumen</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
@endsection