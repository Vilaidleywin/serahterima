@extends('layouts.app')

@section('content')
  <div class="card-soft p-3">
    <h5 class="mb-3">Edit Profil</h5>
    <form method="POST" action="{{ route('profile.update') }}">
      @csrf
      @method('PATCH')

      <div class="mb-3">
        <label class="form-label">Nama</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}">
        @error('name')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
      </div>

      <button class="btn btn-primary">Simpan</button>
    </form>
  </div>
@endsection
