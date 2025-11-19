@extends('layouts.app')

@section('title', 'Pengguna')

@section('content')
  <div class="container py-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h3 class="mb-0">Pengguna</h3>
      <a href="{{ route('admin.users.create') }}" class="btn btn-primary">+ Tambah</a>
    </div>

    @if(session('ok'))
      <div class="alert alert-success">{{ session('ok') }}</div>
    @endif

    @if(session('err'))
      <div class="alert alert-danger">{{ session('err') }}</div>
    @endif

    <form method="GET" class="mb-3">
      <input name="search" class="form-control" placeholder="Cari nama/username/email/divisi"
        value="{{ request('search') }}">
    </form>

    @if($users->isEmpty())
      <p class="text-muted">Anda belum mendaftarkan user sama sekali.</p>
    @else
      <div class="table-responsive">
        <table class="table table-striped align-middle">
          <thead>
            <tr>
              <th>Nama</th>
              <th>Username</th>
              <th>Email</th>
              <th>Divisi</th>
              <th>Role</th>
              <th style="width:140px"></th>
            </tr>
          </thead>
          <tbody>
            @foreach($users as $u)
              <tr>
                <td>{{ $u->name }}</td>
                <td>{{ $u->username }}</td>
                <td>{{ $u->email }}</td>
                <td>{{ $u->division ?? '-' }}</td>
                <td><span class="badge bg-secondary">{{ $u->role }}</span></td>
                <td class="text-end">
                  @if(in_array($u->role, ['admin', 'admin_internal', 'admin_komersial'], true))
                  @else
                    <a href="{{ route('admin.users.edit', $u) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                    <form action="{{ route('admin.users.destroy', $u) }}" method="POST" class="d-inline"
                      onsubmit="return confirm('Hapus user ini?')">
                      @csrf @method('DELETE')
                      <button class="btn btn-sm btn-outline-danger">Hapus</button>
                    </form>
                  @endif
                </td>

              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      {{-- FOOTER PAGINATION --}}
      <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2 py-2 px-1"
        style="border-top:1px solid #e3e8ef; font-size:14px; color:#6b7280;">

        {{-- Rows per page --}}
        <form method="get" class="d-inline-flex align-items-center gap-2 mb-0">
          {{-- keep search query saat ganti per_page --}}
          <input type="hidden" name="search" value="{{ request('search') }}">

          <select name="per_page" class="form-select form-select-sm" style="width:90px" onchange="this.form.submit()">
            @foreach([10, 15, 25, 50] as $n)
              <option value="{{ $n }}" @selected(($per_page ?? 15) == $n)>{{ $n }}</option>
            @endforeach
          </select>
          <span>rows / page</span>
        </form>

        {{-- Showing info --}}
        <div class="text-muted small flex-grow-1 text-center mb-0">
          Showing {{ $users->firstItem() ?? 0 }} to {{ $users->lastItem() ?? 0 }} of {{ $users->total() }} rows
        </div>

        {{-- Pagination --}}
        <div class="mb-0">
          <nav>
            <ul class="pagination mb-0">
              <li class="page-item {{ $users->onFirstPage() ? 'disabled' : '' }}">
                <a class="page-link" href="{{ $users->previousPageUrl() ?? '#' }}">&laquo;</a>
              </li>

              @foreach ($users->getUrlRange(1, $users->lastPage()) as $page => $url)
                <li class="page-item {{ $page == $users->currentPage() ? 'active' : '' }}">
                  <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                </li>
              @endforeach

              <li class="page-item {{ !$users->hasMorePages() ? 'disabled' : '' }}">
                <a class="page-link" href="{{ $users->nextPageUrl() ?? '#' }}">&raquo;</a>
              </li>
            </ul>
          </nav>
        </div>
      </div>
    @endif
  </div>
@endsection