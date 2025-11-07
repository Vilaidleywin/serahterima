@extends('layouts.app')

@section('title', 'Daftar Pengguna')

@section('content')
<div class="container py-3">
  <h3 class="mb-3">Daftar Pengguna</h3>

  @if(session('ok'))
    <div class="alert alert-success">{{ session('ok') }}</div>
  @endif

  <div class="mb-3 text-end">
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">+ Tambah Pengguna</a>
  </div>

  @if($users->isEmpty())
    <p class="text-muted">Belum ada pengguna.</p>
  @else
    <div class="table-responsive">
      <table class="table table-striped align-middle">
        <thead>
          <tr>
            <th>Nama</th>
            <th>Username</th>
            <th>Email</th>
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
              <td>{{ $u->role }}</td>
              <td class="text-end">
                <a href="{{ route('admin.users.edit', $u) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                <form action="{{ route('admin.users.destroy', $u) }}" method="POST" class="d-inline"
                      onsubmit="return confirm('Hapus user ini?')">
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
