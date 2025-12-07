<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Pastikan kolom role & division SUDAH ada (sudah migrate add_role_and_division_to_users_table)
        
        User::updateOrCreate(
            ['email' => 'fitogantengaja@gmail.com'],
            [
                'name' => 'Fito Ganteng',
                'username'   => 'fitoganteng',
                'password' => Hash::make('password123'),
                'role' => 'admin_internal',
                'division' => 'IT Internal',
                'created_by' => 1,
            ]
        );
    }
}
