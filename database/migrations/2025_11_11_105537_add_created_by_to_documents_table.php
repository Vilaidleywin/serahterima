<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi.
     */
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            // Tambah kolom created_by kalau belum ada
            if (!Schema::hasColumn('documents', 'created_by')) {
                $table->foreignId('created_by')
                      ->nullable()
                      ->constrained('users')
                      ->nullOnDelete()
                      ->after('id'); // taruh setelah kolom id
            }
        });
    }

    /**
     * Rollback migrasi.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            if (Schema::hasColumn('documents', 'created_by')) {
                $table->dropConstrainedForeignId('created_by');
            }
        });
    }
};

