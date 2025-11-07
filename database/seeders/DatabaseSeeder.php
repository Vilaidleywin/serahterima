<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // HAPUS/BLOCK kalau ada: User::factory(10)->create();

        // Pastikan semua user yang kamu buat selalu isi username & role:
        User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name'     => 'Test User',
                'username' => 'test',           // WAJIB
                'role'     => 'user',           // WAJIB (admin_internal/admin_komersial/user)
                'password' => 'password',       // Laravel 12 auto-hash via casts
            ]
        );

        // Contoh admin default (opsional)
        User::updateOrCreate(
            ['email' => 'admin.internal@example.com'],
            [
                'name'     => 'Admin Internal',
                'username' => 'admin_internal',
                'role'     => 'admin_internal',
                'password' => 'password',
            ]
        );

        User::updateOrCreate(
            ['email' => 'admin.komersial@example.com'],
            [
                'name'     => 'Admin Komersial',
                'username' => 'admin_komersial',
                'role'     => 'admin_komersial',
                'password' => 'password',
            ]
        );
        $adminInt = \App\Models\User::firstWhere('email', 'admin.internal@example.com');
        $adminKom = \App\Models\User::firstWhere('email', 'admin.komersial@example.com');

        if ($adminInt) {
            \App\Models\User::updateOrCreate(
                ['email' => 'user.a@example.com'],
                [
                    'name'       => 'User A',
                    'username'   => 'user_a',
                    'role'       => 'user',
                    'password'   => 'password',
                    'created_by' => $adminInt->id,   // <— milik admin internal
                ]
            );
        }

        if ($adminKom) {
            \App\Models\User::updateOrCreate(
                ['email' => 'user.b@example.com'],
                [
                    'name'       => 'User B',
                    'username'   => 'user_b',
                    'role'       => 'user',
                    'password'   => 'password',
                    'created_by' => $adminKom->id,   // <— milik admin komersial
                ]
            );
        }

        // JANGAN panggil User::factory() kalau factory belum dipatch (langkah 2)
        // User::factory(10)->create();  // <- pastikan ini dimatikan dulu jika factory belum diupdate
    }
}
