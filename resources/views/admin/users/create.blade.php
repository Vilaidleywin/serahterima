@extends('layouts.app')

@section('title','Tambah Pengguna')

@section('content')
<div class="container py-3" style="max-width:720px">
  <h3 class="mb-3">Tambah Pengguna</h3>
  @if ($errors->any()) <div class="alert alert-danger">{{ $errors->first() }}</div> @endif

  <form method="POST" action="{{ route('admin.users.store') }}">
    @csrf
    @include('admin.users.form', ['mode' => 'create'])
  </form>
</div>
@endsection
