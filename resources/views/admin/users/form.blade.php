<div class="mb-3">
  <label class="form-label">Nama</label>
  <input type="text" name="name" class="form-control" value="{{ old('name', $user->name ?? '') }}" required>
</div>

<div class="mb-3">
  <label class="form-label">Username</label>
  <input type="text" name="username" class="form-control" value="{{ old('username', $user->username ?? '') }}" required>
</div>

<div class="mb-3">
  <label class="form-label">Email</label>
  <input type="email" name="email" class="form-control" value="{{ old('email', $user->email ?? '') }}" required>
</div>

<div class="mb-3">
  <label class="form-label">Role</label>
  <input type="text" name="role" class="form-control" value="user" readonly>
</div>

<div class="mb-3">
  <label class="form-label">Divisi</label>
  <select name="division" class="form-select" required>
    <option value="">-- Pilih Divisi --</option>
    <option value="HRD" {{ old('division', $user->division ?? '') == 'HRD' ? 'selected' : '' }}>HRD</option>
    <option value="Keuangan" {{ old('division', $user->division ?? '') == 'Keuangan' ? 'selected' : '' }}>Keuangan</option>
    <option value="Marketing" {{ old('division', $user->division ?? '') == 'Marketing' ? 'selected' : '' }}>Marketing</option>
    <option value="IT" {{ old('division', $user->division ?? '') == 'IT' ? 'selected' : '' }}>IT</option>
    <option value="Produksi" {{ old('division', $user->division ?? '') == 'Produksi' ? 'selected' : '' }}>Produksi</option>
  </select>
</div>

@if($mode === 'create')
  <div class="mb-3">
    <label class="form-label">Password</label>
    <input type="password" name="password" class="form-control" required>
  </div>
@endif

<div class="d-flex justify-content-between mt-4">
  <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Kembali</a>
  <button type="submit" class="btn btn-primary">
    {{ $mode === 'create' ? 'Simpan' : 'Perbarui' }}
  </button>
</div>
