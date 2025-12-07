<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\DocumentController;
use Illuminate\Support\Facades\DB;


class UserController extends Controller
{
    public function index(Request $request)
    {
        $q = User::query();

        // ambil per_page dari query, default 15 & whitelist
        $per_page = (int) $request->integer('per_page', 15);
        $per_page = in_array($per_page, [10, 15, 25, 50], true) ? $per_page : 15;

        // pencarian: nama, username, email, role, division
        if ($s = (string) $request->query('search', '')) {
            $like = "%{$s}%";
            $q->where(function ($w) use ($like) {
                $w->where('name', 'like', $like)
                    ->orWhere('username', 'like', $like)
                    ->orWhere('email', 'like', $like)
                    ->orWhere('division', 'like', $like)
                    ->orWhere('role', 'like', $like);
            });
        }

        $users = $q->orderBy('name')
            ->paginate($per_page)
            ->withQueryString();

        return view('admin.users.index', [
            'users'    => $users,
            'per_page' => $per_page,
        ]);
    }

    public function create()
    {
        return view('admin.users.form', [
            'mode'      => 'create',
            // ambil daftar divisi statis dari DocumentController
            'divisions' => DocumentController::divisions(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:100'],
            'username' => ['required', 'alpha_dash', 'max:50', 'unique:users,username'],
            'email'    => ['required', 'email', 'max:150', 'unique:users,email'],
            'division' => ['required', 'string', 'max:100'],
            'password' => ['required', 'min:8'],
            // optional: kalau kamu kasih field status di form (aktif/nonaktif)
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['password']   = Hash::make($data['password']);
        $data['role']       = 'user';        // kunci role tetap user
        $data['created_by'] = auth()->id();  // tetap disimpan

        // default: aktif kalau field tidak dikirim
        $data['is_active'] = $request->boolean('is_active', true);

        User::create($data);

        // pakai 'success' biar keluar SweetAlert toast
        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User berhasil dibuat.');
    }

    public function edit(User $user)
    {
        return view('admin.users.form', [
            'mode'      => 'edit',
            'user'      => $user,
            'divisions' => DocumentController::divisions(),
        ]);
    }

    public function update(Request $request, User $user)
    {
        // Larang update untuk user dengan role admin/admin_internal/admin_komersial
        if (in_array($user->role, ['admin', 'admin_internal', 'admin_komersial'], true)) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'User dengan role admin tidak dapat diubah dari halaman ini.');
        }

        $data = $request->validate([
            'name'     => ['required', 'string', 'max:100'],
            'username' => [
                'required',
                'alpha_dash',
                'max:50',
                Rule::unique('users', 'username')->ignore($user->id),
            ],
            'email'    => [
                'required',
                'email',
                'max:150',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'division' => ['required', 'string', 'max:100'],
            'password' => ['nullable', 'min:8'],
            // optional: kalau kamu kasih field status di form edit
            'is_active' => ['nullable', 'boolean'],
        ]);

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        // tidak boleh ubah kepemilikan & role lewat form
        unset($data['created_by']);
        $data['role'] = 'user';

        // kalau form nggak kirim is_active, pakai nilai lama
        $data['is_active'] = $request->has('is_active')
            ? $request->boolean('is_active')
            : $user->is_active;

        $user->update($data);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        // Larang hapus user admin/admin_internal/admin_komersial
        if (in_array($user->role, ['admin', 'admin_internal', 'admin_komersial'], true)) {
            return back()->with('error', 'User dengan role admin tidak dapat dihapus.');
        }

        // tetap: tidak boleh hapus diri sendiri
        if (auth()->id() === $user->id) {
            return back()->with('error', 'Tidak bisa menghapus diri sendiri.');
        }

        DB::transaction(function () use ($user) {
            // hapus semua log login user ini dulu
            DB::table('user_logins')
                ->where('user_id', $user->id)
                ->delete();

            // baru hapus user
            $user->delete();
        });

        return back()->with('success', 'User berhasil dihapus.');
    }


    // ⬇️⬇️ METHOD BARU: toggle aktif/nonaktif
    public function toggleStatus(User $user)
    {
        // amankan: jangan ubah status admin*
        if (in_array($user->role, ['admin', 'admin_internal', 'admin_komersial'], true)) {
            return back()->with('error', 'User dengan role admin tidak dapat diubah statusnya.');
        }

        // opsional: jangan izinkan user mengubah status dirinya sendiri
        if (auth()->id() === $user->id) {
            return back()->with('error', 'Tidak bisa mengubah status akun diri sendiri.');
        }

        $user->is_active = ! $user->is_active;
        $user->save();

        return back()->with('success', 'Status user berhasil diperbarui.');
    }
}
