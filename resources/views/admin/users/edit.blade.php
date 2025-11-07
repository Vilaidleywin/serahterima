@extends('layouts.app')

@section('content')
<div class="container py-3" style="max-width:720px">
  <h3 class="mb-3">Edit User</h3>

  @if ($errors->any())
    <div class="alert alert-danger">{{ $errors->first() }}</div>
  @endif

  <form method="POST" action="{{ route('admin.users.update', $user) }}">
    @csrf @method('PUT')
    @include('admin.users.form', ['mode' => 'edit'])
  </form>
</div>
@endsection
