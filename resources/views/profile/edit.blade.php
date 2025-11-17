@extends('layouts.app')

@section('content')
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h2 class="fw-bold mb-0">Edit Profil</h2>
      <div class="text-muted small">Ubah email, foto profil, atau password</div>
    </div>
  </div>

  <div class="card-soft p-4">
    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
      @csrf
      @method('PATCH')

      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label fw-semibold">Username (tidak bisa diubah)</label>
          <input type="text" class="form-control" value="{{ $user->name }}" readonly>
        </div>

        <div class="col-md-6">
          <label class="form-label fw-semibold">Email</label>
          <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
          @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-4">
          <label class="form-label fw-semibold">Foto Profil</label>
          <div class="mb-2">
            @php
              $avatarUrl = $user->avatar ? asset('storage/' . $user->avatar) : 'https://cdn-icons-png.flaticon.com/512/3177/3177440.png';
            @endphp
            <img src="{{ $avatarUrl }}" alt="avatar" style="width:84px;height:84px;border-radius:8px;object-fit:cover;border:1px solid #e5e7eb">
          </div>
          <input type="file" name="avatar" class="form-control @error('avatar') is-invalid @enderror">
          @error('avatar') <div class="text-danger small">{{ $message }}</div> @enderror
          <div class="form-text">Format gambar: jpg/png. Maks 5MB.</div>
        </div>

        <div class="col-12"><hr></div>

        <div class="col-md-6">
          <label class="form-label fw-semibold">Ubah Password (opsional)</label>
          <input type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" placeholder="Password sekarang (wajib jika ganti password)">
          @error('current_password') <div class="invalid-feedback">{{ $message }}</div> @enderror
          <div class="form-text">Masukkan password lama sebelum mengganti password baru.</div>
        </div>

        <div class="col-md-6">
          <label class="form-label fw-semibold">Password Baru</label>
          <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Minimal 8 karakter">
          @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-6">
          <label class="form-label fw-semibold">Konfirmasi Password Baru</label>
          <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi password baru">
        </div>

        <div class="col-12 text-end">
          <a href="{{ route('dashboard') }}" class="btn btn-light me-2">Batal</a>
          <button class="btn btn-primary">Simpan Perubahan</button>
        </div>
      </div>
    </form>
  </div>
@endsection
