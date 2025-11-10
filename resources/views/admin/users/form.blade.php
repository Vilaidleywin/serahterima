@extends('layouts.app')

@section('title', $mode === 'edit' ? 'Edit Pengguna' : 'Tambah Pengguna')

@section('content')
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h4 class="mb-1">{{ $mode === 'edit' ? 'Edit Pengguna' : 'Tambah Pengguna' }}</h4>
      <small class="text-muted">{{ $mode === 'edit' ? 'Perbarui data akun' : 'Buat akun baru' }}</small>
    </div>
    <div class="d-none d-md-flex gap-2">
      <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Batal</a>
      <button form="userForm" class="btn btn-primary">{{ $mode === 'edit' ? 'Simpan Perubahan' : 'Simpan' }}</button>
    </div>
  </div>

  @if($errors->any())
    <div class="alert alert-danger small">
      <strong>Periksa kembali isian Anda.</strong>
      <ul class="mb-0 mt-1">
        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
      </ul>
    </div>
  @endif

  @php $user = $user ?? null; @endphp

  <div class="card border-0 shadow-sm">
    <div class="card-body">
      <form id="userForm" method="POST"
            action="{{ $mode === 'edit' ? route('admin.users.update', $user) : route('admin.users.store') }}">
        @csrf
        @if($mode === 'edit') @method('PUT') @endif

        <div class="row g-3">
          {{-- Nama --}}
          <div class="col-md-6">
            <label for="name" class="form-label">Nama</label>
            <input id="name" name="name" type="text"
                   class="form-control @error('name') is-invalid @enderror"
                   value="{{ old('name', $user->name ?? '') }}" required>
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          {{-- Username --}}
          <div class="col-md-6">
            <div class="d-flex justify-content-between">
              <label for="username" class="form-label mb-0">Username</label>
              <button type="button" class="btn btn-link btn-sm p-0" id="genUsername">Buat dari nama</button>
            </div>
            <input id="username" name="username" type="text"
                   class="form-control @error('username') is-invalid @enderror"
                   value="{{ old('username', $user->username ?? '') }}" required>
            @error('username')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          {{-- Email --}}
          <div class="col-md-6">
            <label for="email" class="form-label">Email</label>
            <input id="email" name="email" type="email"
                   class="form-control @error('email') is-invalid @enderror"
                   value="{{ old('email', $user->email ?? '') }}" required>
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          {{-- Divisi --}}
          <div class="col-md-6">
            <label for="division" class="form-label">Divisi</label>
            <select id="division" name="division"
                    class="form-select @error('division') is-invalid @enderror" required>
              <option value="" disabled {{ old('division', $user->division ?? '') ? '' : 'selected' }}>Pilih divisi</option>
              @foreach($divisions as $d)
                <option value="{{ $d }}" {{ old('division', $user->division ?? '') === $d ? 'selected' : '' }}>{{ $d }}</option>
              @endforeach
            </select>
            @error('division')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          {{-- Password --}}
<div class="col-md-6">
  <label for="password" class="form-label">
    Password {{ $mode === 'edit' ? '(opsional)' : '' }}
  </label>

  <div class="position-relative">
    <input id="password" name="password" type="password"
           class="form-control pw-input @error('password') is-invalid @enderror"
           placeholder="{{ $mode === 'edit' ? 'Kosongkan jika tidak diubah' : 'Minimal 6 karakter' }}"
           {{ $mode === 'edit' ? '' : 'required' }}>
    <button type="button" class="pw-toggle" id="togglePw"
            aria-label="Tampilkan password" aria-pressed="false">
      <i class="bi bi-eye"></i>
    </button>
    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
</div>


          {{-- Role (info) --}}
          <div class="col-md-6">
            <label class="form-label">Role</label>
            <input type="text" class="form-control" value="user" disabled>
          </div>
        </div>
      </form>
    </div>

    {{-- Action bar bawah --}}
    <div class="card-footer bg-white">
      <div class="d-flex flex-column flex-sm-row justify-content-end gap-2">
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Batal</a>
        <button form="userForm" class="btn btn-primary">{{ $mode === 'edit' ? 'Simpan Perubahan' : 'Simpan' }}</button>
      </div>
    </div>
  </div>
</div>

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>
  .pw-input{ padding-right: 2.5rem; }             /* ruang untuk ikon */
  .pw-toggle{
    position:absolute; right:.75rem; top:50%; transform:translateY(-50%);
    border:0; background:transparent; padding:0;
    color:#6c757d; line-height:1; cursor:pointer;
  }
  .pw-toggle:focus{ outline: 0; }
</style>
@endpush


@push('scripts')
<script>
  // generate username dari nama
  document.getElementById('genUsername')?.addEventListener('click', function(){
    const name = document.getElementById('name')?.value || '';
    const base = name.toLowerCase()
      .normalize('NFD').replace(/[\u0300-\u036f]/g,'')
      .replace(/[^a-z0-9]+/g,'-').replace(/^-+|-+$/g,'').slice(0,20);
    if (!base) return;
    const u = base.length < 6 ? base + Math.floor(Math.random()*900+100) : base;
    document.getElementById('username').value = u;
  });

  // toggle lihat password (icon mata)
  document.getElementById('togglePw')?.addEventListener('click', function(){
    const pw = document.getElementById('password');
    const icon = this.querySelector('i');
    if (pw.type === 'password') {
      pw.type = 'text';
      icon.classList.replace('bi-eye', 'bi-eye-slash');
    } else {
      pw.type = 'password';
      icon.classList.replace('bi-eye-slash', 'bi-eye');
    }
  });
</script>
@endpush
@endsection
