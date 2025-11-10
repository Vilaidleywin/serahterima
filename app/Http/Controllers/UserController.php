<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\DocumentController;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $q = User::query();

        // admin hanya lihat user yang dia buat
        if (in_array(auth()->user()->role, ['admin_internal', 'admin_komersial'], true)) {
            $q->where('created_by', auth()->id());
        }

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

        $users = $q->orderBy('name')->paginate(15)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.form', [
            'mode'       => 'create',
            // ambil daftar divisi statis dari DocumentController (sudah kita tambahkan)
            'divisions'  => DocumentController::divisions(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:100'],
            'username' => ['required', 'alpha_dash', 'max:50', 'unique:users,username'],
            'email'    => ['required', 'email', 'max:150', 'unique:users,email'],
            'division' => ['required', 'string', 'max:100'],
            'password' => ['required', 'min:6'],
        ]);

        $data['password']   = Hash::make($data['password']);
        $data['role']       = 'user';        // kunci role
        $data['created_by'] = auth()->id();

        User::create($data);

        return redirect()->route('admin.users.index')->with('ok', 'User dibuat');
    }

    public function edit(User $user)
    {
        return view('admin.users.form', [
            'mode'       => 'edit',
            'user'       => $user,
            'divisions'  => DocumentController::divisions(),
        ]);
    }

    public function update(Request $request, User $user)
    {
        if (in_array(auth()->user()->role, ['admin_internal', 'admin_komersial'], true)) {
            abort_unless($user->created_by === auth()->id(), 403);
        }

        $data = $request->validate([
            'name'     => ['required', 'string', 'max:100'],
            'username' => ['required', 'alpha_dash', 'max:50', Rule::unique('users', 'username')->ignore($user->id)],
            'email'    => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user->id)],
            'division' => ['required', 'string', 'max:100'],
            'password' => ['nullable', 'min:6'],
        ]);

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        // tidak boleh ubah kepemilikan & role
        unset($data['created_by']);
        $data['role'] = 'user';

        $user->update($data);

        return redirect()->route('admin.users.index')->with('ok', 'User diupdate');
    }

    public function destroy(User $user)
    {
        if (in_array(auth()->user()->role, ['admin_internal', 'admin_komersial'], true)) {
            abort_unless($user->created_by === auth()->id(), 403);
        }

        if (auth()->id() === $user->id) {
            return back()->with('err', 'Tidak bisa menghapus diri sendiri');
        }

        $user->delete();

        return back()->with('ok', 'User dihapus');
    }
}
