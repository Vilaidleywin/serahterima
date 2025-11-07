<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $q = User::query();

        // admin hanya lihat user yang dia buat
        if (in_array(auth()->user()->role, ['admin_internal','admin_komersial'], true)) {
            $q->where('created_by', auth()->id());
        }

        if ($s = (string) $request->query('search', '')) {
            $like = "%{$s}%";
            $q->where(function($w) use($like){
                $w->where('name','like',$like)
                  ->orWhere('username','like',$like)
                  ->orWhere('email','like',$like)
                  ->orWhere('role','like',$like);
            });
        }

        $users = $q->orderBy('name')->paginate(15)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $roles = ['admin_internal','admin_komersial','user'];
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required','string','max:100'],
            'username' => ['required','alpha_dash','max:50','unique:users,username'],
            'email'    => ['required','email','max:150','unique:users,email'],
            'role'     => ['required', Rule::in(['admin_internal','admin_komersial','user'])],
            'password' => ['required','min:6'],
        ]);

        $data['created_by'] = auth()->id();

        User::create($data);

        return redirect()->route('admin.users.index')->with('ok','User dibuat');
    }

    public function edit(User $user)
    {
        // hanya pembuat yang boleh mengubah
        if (in_array(auth()->user()->role, ['admin_internal','admin_komersial'], true)) {
            abort_unless($user->created_by === auth()->id(), 403);
        }

        $roles = ['admin_internal','admin_komersial','user'];
        return view('admin.users.edit', compact('user','roles'));
    }

    public function update(Request $request, User $user)
    {
        if (in_array(auth()->user()->role, ['admin_internal','admin_komersial'], true)) {
            abort_unless($user->created_by === auth()->id(), 403);
        }

        $data = $request->validate([
            'name'     => ['required','string','max:100'],
            'username' => ['required','alpha_dash','max:50', Rule::unique('users','username')->ignore($user->id)],
            'email'    => ['required','email','max:150', Rule::unique('users','email')->ignore($user->id)],
            'role'     => ['required', Rule::in(['admin_internal','admin_komersial','user'])],
            'password' => ['nullable','min:6'],
        ]);

        if (empty($data['password'])) unset($data['password']);
        unset($data['created_by']); // jangan boleh ubah kepemilikan

        $user->update($data);

        return redirect()->route('admin.users.index')->with('ok','User diupdate');
    }

    public function destroy(User $user)
    {
        if (in_array(auth()->user()->role, ['admin_internal','admin_komersial'], true)) {
            abort_unless($user->created_by === auth()->id(), 403);
        }

        if (auth()->id() === $user->id) {
            return back()->with('err','Tidak bisa menghapus diri sendiri');
        }

        $user->delete();

        return back()->with('ok','User dihapus');
    }
}
