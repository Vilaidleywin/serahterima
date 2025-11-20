@extends('layouts.app')

@section('title', $mode === 'edit' ? 'Edit Pengguna' : 'Tambah Pengguna')

@section('content')
@php
  $user   = $user ?? null;
  $isEdit = $mode === 'edit';
@endphp

<div class="container py-4">
  {{-- Header --}}
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h4 class="mb-1">{{ $isEdit ? 'Edit Pengguna' : 'Tambah Pengguna' }}</h4>
      <small class="text-muted">{{ $isEdit ? 'Perbarui data akun' : 'Buat akun baru' }}</small>
    </div>

    {{-- Action bar (desktop) --}}
    <div class="d-none d-md-flex gap-2">
      <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Batal</a>
      <button form="userForm" class="btn btn-primary">
        {{ $isEdit ? 'Simpan Perubahan' : 'Simpan' }}
      </button>
    </div>
  </div>

  {{-- Error summary --}}
  @if($errors->any())
    @php
      $allErrors = $errors->all();
      $maxShown  = 3;
    @endphp

    <div class="alert alert-danger small d-flex align-items-start gap-2 mb-3 error-summary">
      <div class="pt-1 flex-shrink-0">
        <i class="ti ti-alert-triangle" style="font-size:1.25rem;"></i>
      </div>
      <div>
        <div class="fw-semibold mb-1">
          Form belum lengkap, silakan periksa kembali.
        </div>
        <ul class="mb-0 ps-3">
          @foreach($allErrors as $i => $e)
            @break($i >= $maxShown)
            <li>{{ $e }}</li>
          @endforeach

          @if(count($allErrors) > $maxShown)
            <li>dan {{ count($allErrors) - $maxShown }} error lainnya…</li>
          @endif
        </ul>
      </div>
    </div>
  @endif

  <div class="card border-0 shadow-sm">
    <div class="card-body">
      <form id="userForm" method="POST"
            action="{{ $isEdit ? route('admin.users.update', $user) : route('admin.users.store') }}"
            autocomplete="off">
        @csrf
        @if($isEdit) @method('PUT') @endif

        <div class="row g-3">
          {{-- Nama --}}
          <div class="col-md-6">
            <label for="name" class="form-label">Nama <span class="text-danger">*</span></label>
            <input id="name" name="name" type="text"
                   class="form-control @error('name') is-invalid @enderror"
                   value="{{ old('name', $user->name ?? '') }}"
                   placeholder="Nama lengkap"
                   required>
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          {{-- Username --}}
          <div class="col-md-6">
            <div class="d-flex justify-content-between align-items-center">
              <label for="username" class="form-label mb-0">
                Username <span class="text-danger">*</span>
              </label>
              <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none" id="genUsername">
                Buat dari nama
              </button>
            </div>
            <input id="username" name="username" type="text"
                   class="form-control @error('username') is-invalid @enderror"
                   value="{{ old('username', $user->username ?? '') }}"
                   placeholder="username"
                   autocomplete="off"
                   required>
            @error('username')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          {{-- Email --}}
          <div class="col-md-6">
            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
            <input id="email" name="email" type="email"
                   class="form-control @error('email') is-invalid @enderror"
                   value="{{ old('email', $user->email ?? '') }}"
                   placeholder="nama@contoh.com"
                   autocomplete="email"
                   required>
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          {{-- Divisi --}}
          <div class="col-md-6">
            <label for="division" class="form-label">Divisi <span class="text-danger">*</span></label>
            <select id="division" name="division"
                    class="form-select @error('division') is-invalid @enderror"
                    required>
              <option value="" disabled {{ old('division', $user->division ?? '') ? '' : 'selected' }}>
                Pilih divisi
              </option>
              @foreach($divisions as $d)
                <option value="{{ $d }}" {{ old('division', $user->division ?? '') === $d ? 'selected' : '' }}>
                  {{ $d }}
                </option>
              @endforeach
            </select>
            @error('division')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          {{-- Password --}}
          <div class="col-md-6">
            <label for="password" class="form-label">
              Password {{ $isEdit ? '(opsional)' : '' }}
            </label>

            <div class="position-relative">
              <input id="password" name="password" type="password"
                     class="form-control pw-input @error('password') is-invalid @enderror"
                     placeholder="{{ $isEdit ? 'Kosongkan jika tidak diubah' : 'Minimal 8 karakter' }}"
                     {{ $isEdit ? '' : 'required' }}
                     minlength="8"
                     autocomplete="new-password">
              <button type="button" class="pw-toggle" id="togglePw"
                      aria-label="Tampilkan password" aria-pressed="false">
                <i class="bi bi-eye"></i>
              </button>
              @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            @if($isEdit)
              <small class="text-muted">Biarkan kosong jika tidak ingin mengubah password.</small>
            @endif
          </div>

          {{-- Role (info saja) --}}
          <div class="col-md-6">
            <label class="form-label">Role</label>
            <input type="text" class="form-control" value="user" disabled>
            <small class="text-muted">Role saat ini dikunci sebagai <strong>user</strong>.</small>
          </div>
        </div>
      </form>
    </div>

    {{-- Action bar bawah (mobile & desktop) --}}
    <div class="card-footer bg-white">
      <div class="d-flex flex-column flex-sm-row justify-content-end gap-2">
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary w-100 w-sm-auto">
          Batal
        </a>
        <button form="userForm" class="btn btn-primary w-100 w-sm-auto">
          {{ $isEdit ? 'Simpan Perubahan' : 'Simpan' }}
        </button>
      </div>
    </div>
  </div>
</div>

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>
  .pw-input{
    padding-right: 2.5rem; /* ruang untuk ikon mata */
  }
  .pw-toggle{
    position:absolute;
    right:.75rem;
    top:50%;
    transform:translateY(-50%);
    border:0;
    background:transparent;
    padding:0;
    color:#6c757d;
    line-height:1;
    cursor:pointer;
  }
  .pw-toggle:focus{
    outline:0;
  }
  #genUsername{
    font-size: .8rem;
  }

  .error-summary {
    border-left: 4px solid #dc3545;
    background-color: #fff5f5;
  }

  @media (max-width: 575.98px) {
    .w-sm-auto {
      width: 100% !important;
    }
  }
</style>
@endpush

@push('scripts')
<script>
  // generate username dari nama
  document.getElementById('genUsername')?.addEventListener('click', function(){
    const nameInput = document.getElementById('name');
    const usernameInput = document.getElementById('username');
    if (!nameInput || !usernameInput) return;

    const name = nameInput.value || '';
    const base = name.toLowerCase()
      .normalize('NFD').replace(/[\u0300-\u036f]/g,'') // hilangkan aksen
      .replace(/[^a-z0-9]+/g,'-')                      // selain huruf/angka jadi '-'
      .replace(/^-+|-+$/g,'')                          // trim '-'
      .slice(0, 20);

    if (!base) return;

    const candidate = base.length < 8
      ? base + Math.floor(Math.random() * 900 + 100)
      : base;

    usernameInput.value = candidate;
    usernameInput.focus();
  });

  // toggle lihat password (icon mata)
  document.getElementById('togglePw')?.addEventListener('click', function(){
    const pw   = document.getElementById('password');
    const icon = this.querySelector('i');
    if (!pw || !icon) return;

    if (pw.type === 'password') {
      pw.type = 'text';
      icon.classList.replace('bi-eye', 'bi-eye-slash');
      this.setAttribute('aria-pressed', 'true');
    } else {
      pw.type = 'password';
      icon.classList.replace('bi-eye-slash', 'bi-eye');
      this.setAttribute('aria-pressed', 'false');
    }
  });
</script>
@endpush
@endsection
