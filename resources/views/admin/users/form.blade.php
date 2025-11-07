<div class="mb-3">
  <label class="form-label">Nama</label>
  <input name="name" class="form-control" required value="{{ old('name', $user->name ?? '') }}">
</div>

<div class="mb-3">
  <label class="form-label">Username</label>
  <input name="username" class="form-control" required value="{{ old('username', $user->username ?? '') }}">
</div>

<div class="mb-3">
  <label class="form-label">Email</label>
  <input type="email" name="email" class="form-control" required value="{{ old('email', $user->email ?? '') }}">
</div>

<div class="mb-3">
  <label class="form-label">Role</label>
  <select name="role" class="form-select" required>
    @foreach($roles as $r)
      <option value="{{ $r }}" @selected(old('role', $user->role ?? 'user') === $r)>{{ $r }}</option>
    @endforeach
  </select>
</div>

<div class="mb-3">
  <label class="form-label">Password {{ ($mode ?? '') === 'edit' ? '(kosongkan jika tidak diubah)' : '' }}</label>
  <input type="password" name="password" class="form-control" {{ ($mode ?? '') === 'create' ? 'required' : '' }}>
</div>

<div class="d-flex gap-2">
  <button class="btn btn-primary" type="submit">{{ ($mode ?? '') === 'edit' ? 'Update' : 'Simpan' }}</button>
  <a class="btn btn-light" href="{{ route('admin.users.index') }}">Batal</a>
</div>
