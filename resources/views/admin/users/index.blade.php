@extends('layouts.app')

@section('title', 'Pengguna')

@section('content')
<style>
    /* Flash message fade */
    .flash-message {
        position: relative;
        border-left: 4px solid #198754;
        padding-left: 15px;
    }
    .flash-message.error {
        border-left-color: #dc3545;
    }
</style>

<div class="container py-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Pengguna</h3>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">+ Tambah</a>
    </div>

    {{-- FLASH MESSAGE --}}
    @if(session('ok'))
      <div class="alert alert-success flash-message d-flex align-items-center gap-2">
        <i class="ti ti-check"></i>
        <span>{{ session('ok') }}</span>
      </div>
    @endif

    @if(session('err'))
      <div class="alert alert-danger flash-message error d-flex align-items-center gap-2">
        <i class="ti ti-alert-circle"></i>
        <span>{{ session('err') }}</span>
      </div>
    @endif

    <form method="GET" class="mb-3">
      <div class="input-group">
        <input 
          name="search" 
          class="form-control" 
          placeholder="Cari nama/username/email/divisi"
          value="{{ request('search') }}"
        >
        <button class="btn btn-primary" type="submit">
          <i class="ti ti-search"></i> Cari
        </button>
      </div>
    </form>

    @if($users->isEmpty())
      <p class="text-muted">Anda belum mendaftarkan user sama sekali.</p>
    @else
      <div class="table-responsive">
        <table class="table table-striped align-middle">
          <thead>
            <tr>
              <th style="width:60px">No</th>
              <th>Nama</th>
              <th>Username</th>
              <th>Email</th>
              <th>Divisi</th>
              <th>Role</th>
              <th style="width:150px"></th>
            </tr>
          </thead>

          <tbody>
            @foreach($users as $i => $u)
              @php
                $rowNumber = ($users->firstItem() ?? 1) + $i;
              @endphp

              <tr>
                <td>{{ $rowNumber }}</td>
                <td>{{ $u->name }}</td>
                <td>{{ $u->username }}</td>
                <td>{{ $u->email }}</td>
                <td>{{ $u->division ?? '-' }}</td>
                <td><span class="badge bg-secondary">{{ $u->role }}</span></td>
                <td class="text-end">
                  @if(!in_array($u->role, ['admin','admin_internal','admin_komersial']))
                    <a href="{{ route('admin.users.edit', $u) }}" class="btn btn-sm btn-outline-secondary">
                      Edit
                    </a>

                    {{-- Tombol hapus pakai global confirmDelete (SweetAlert) --}}
                    <button
                        type="button"
                        class="btn btn-sm btn-outline-danger"
                        onclick="confirmDelete({{ $u->id }}, @js(($u->name ?? '-') . ' (' . ($u->username ?? '-') . ')'))"
                    >
                      Hapus
                    </button>

                    {{-- Form delete yang akan disubmit oleh confirmDelete --}}
                    <form id="delete-form-{{ $u->id }}" action="{{ route('admin.users.destroy', $u) }}" method="POST" class="d-inline">
                      @csrf
                      @method('DELETE')
                    </form>
                  @endif
                </td>
              </tr>

            @endforeach
          </tbody>
        </table>
      </div>

      {{-- FOOTER PAGINATION --}}
      <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2 py-2 px-1"
        style="border-top:1px solid #e3e8ef; font-size:14px; color:#6b7280;">

        <form method="get" class="d-inline-flex align-items-center gap-2 mb-0">
          <input type="hidden" name="search" value="{{ request('search') }}">
          <select name="per_page" class="form-select form-select-sm" style="width:90px" onchange="this.form.submit()">
            @foreach([10, 15, 25, 50] as $n)
              <option value="{{ $n }}" @selected(($per_page ?? 15) == $n)>{{ $n }}</option>
            @endforeach
          </select>
          <span>rows / page</span>
        </form>

        <div class="text-muted small flex-grow-1 text-center mb-0">
          Showing {{ $users->firstItem() ?? 0 }} to {{ $users->lastItem() ?? 0 }} of {{ $users->total() }} rows
        </div>

        <div class="mb-0">
          {{ $users->links() }}
        </div>
      </div>

    @endif
</div>

{{-- SCRIPT --}}
<script>
    // FLASH MESSAGE AUTO HIDE
    document.addEventListener('DOMContentLoaded', () => {
        const flashes = document.querySelectorAll('.flash-message');
        if (!flashes.length) return;

        setTimeout(() => {
            flashes.forEach(el => {
                el.style.transition = 'opacity .6s';
                el.style.opacity = '0';
                setTimeout(() => el.remove(), 600);
            });
        }, 3000);
    });
</script>

@endsection
