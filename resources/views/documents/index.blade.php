@extends('layouts.app')

@section('content')
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h2 class="fw-bold mb-0">Data Dokumen</h2>
      <div class="text-muted small">Rekap semua bukti serah terima</div>
    </div>
    <div class="d-flex gap-2">
      <a href="{{ route('documents.create') }}" class="btn btn-primary"><i class="ti ti-plus"></i> Tambah</a>
      <a href="{{ route('documents.index') }}" class="btn btn-ghost"><i class="ti ti-refresh"></i> Refresh</a>
    </div>
  </div>

  <form class="row g-2 mb-3" method="get">
    <div class="col-md-8">
      <input name="search" value="{{ request('search') }}" class="form-control search" placeholder="Cari dokumen...">
    </div>
    <div class="col-md-3">
      <select name="status" class="form-select search">
        <option value="">Semua Status</option>
        @foreach(['PENDING','DONE','FAILED'] as $s)
          <option value="{{ $s }}" {{ request('status')===$s?'selected':'' }}>{{ $s }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-md-1 d-grid">
      <button class="btn btn-primary">Filter</button>
    </div>
  </form>

  <div class="table-responsive card-soft p-2">
    <table class="table table-borderless align-middle mb-0">
      <thead>
        <tr>
          <th class="text-center" style="width:60px">No</th>
          <th style="width:140px">No Dokumen</th>
          <th>Judul</th>
          <th style="width:160px">Penerima</th>
          <th style="width:140px">Nominal (Rp)</th>
          <th style="width:120px">Tanggal</th>
          <th style="width:120px">Status</th>
          <th style="width:180px">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($documents as $i=>$d)
          <tr>
            <td class="text-center">{{ $documents->firstItem() + $i }}</td>
            <td>{{ $d->number }}</td>
            <td>{{ $d->title }}</td>
            <td>{{ $d->receiver }}</td>
            <td>{{ $d->amount_idr }}</td>
            <td>{{ $d->date->format('Y-m-d') }}</td>
            <td>@include('shared.status-badge',['status'=>$d->status])</td>
            <td class="d-flex gap-2">
              <a href="{{ route('documents.edit',$d) }}" class="btn btn-sm btn-ghost">Edit</a>
              <form action="{{ route('documents.destroy',$d) }}" method="post"
                    onsubmit="return confirm('Hapus dokumen ini?')">
                @csrf @method('delete')
                <button class="btn btn-sm btn-danger">Hapus</button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="8" class="text-center py-4">Tidak ada data</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-3">
    {{ $documents->links() }}
  </div>
@endsection
