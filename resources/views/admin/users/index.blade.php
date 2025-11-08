@extends('layouts.app')

@section('title','Pengguna')

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
    <input name="search" class="form-control" placeholder="Cari nama/username/email/divisi/role" value="{{ request('search') }}">
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
                <a href="{{ route('admin.users.edit', $u) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                <form action="{{ route('admin.users.destroy', $u) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus user ini?')">
                  @csrf @method('DELETE')
                  <button class="btn btn-sm btn-outline-danger">Hapus</button>
                </form>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    {{ $users->links() }}
  @endif
</div>
@endsection
