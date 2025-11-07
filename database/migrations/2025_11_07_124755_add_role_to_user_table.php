<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Tambah username hanya kalau BELUM ada
            if (!Schema::hasColumn('users', 'username')) {
                // kalau mau keras, bisa ->unique(), tapi aman juga tanpa itu dulu
                $table->string('username')->after('name'); 
            }

            // Tambah role hanya kalau BELUM ada
            if (!Schema::hasColumn('users', 'role')) {
                $table->enum('role', ['admin_internal','admin_komersial','user'])
                      ->default('user')
                      ->after('email');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'role')) {
                $table->dropColumn('role');
            }
            // Jangan drop username kalau sudah dipakai luas; tapi kalau ingin:
            // if (Schema::hasColumn('users', 'username')) {
            //     $table->dropColumn('username');
            // }
        });
    }
};
