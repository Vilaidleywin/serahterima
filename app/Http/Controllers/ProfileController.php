<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        // cek role admin
        $isAdmin = in_array($user->role ?? '', ['admin', 'admin_internal', 'admin_komersial'], true);

        $rules = [
            // email
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'avatar' => ['nullable', 'image', 'max:5120'], // max 5MB
        ];

        // kalau admin, izinkan ubah username
        if ($isAdmin) {
            $rules['username'] = [
                'required',
                'alpha_dash',
                'max:50',
                Rule::unique('users', 'username')->ignore($user->id),
            ];
        }

        // jika user mau ganti password, tambahkan aturan validasi
        if (
            $request->filled('password')
            || $request->filled('password_confirmation')
            || $request->filled('current_password')
        ) {
            $rules['current_password'] = ['required', 'current_password'];
            $rules['password'] = ['required', 'confirmed', Password::min(8)];
            // bisa ditambah ->mixedCase()->numbers() kalau mau lebih strict
        }

        $data = $request->validate($rules);

        // 1) Username (hanya admin)
        if ($isAdmin && array_key_exists('username', $data) && $data['username'] !== $user->username) {
            $user->username = $data['username'];
        }

        // 2) Email: jika berubah, reset email_verified_at
        if ($request->input('email') !== $user->email) {
            $user->email = $request->input('email');

            // jika pakai email verification, batal verifikasi lama
            if (property_exists($user, 'email_verified_at')) {
                $user->email_verified_at = null;
            }
        }

        // 3) Avatar: upload & hapus file lama
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $path = $file->store('avatars', 'public');

            if (!empty($user->avatar) && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            $user->avatar = $path;
        }

        // 4) Password: verifikasi sudah dilakukan oleh rule 'current_password'
        if ($request->filled('password')) {
            $user->password = Hash::make($request->input('password'));
        }

        // simpan
        $user->save();

        // pakai 'success' biar keluar SweetAlert toast di layouts.app
        return redirect()
            ->route('profile.edit')
            ->with('success', 'Profil berhasil diperbarui!');
    }
}
