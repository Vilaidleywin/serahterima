<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_logins', function (Blueprint $table) {
            // Hapus foreign key lama
            $table->dropForeign(['user_id']); // ini sama dengan 'user_logins_user_id_foreign'

            // Bikin lagi dengan ON DELETE CASCADE
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('user_logins', function (Blueprint $table) {
            // Balik ke versi tanpa cascade (opsional, bebas mau gimana)
            $table->dropForeign(['user_id']);

            $table->foreign('user_id')
                ->references('id')
                ->on('users');
        });
    }
};
