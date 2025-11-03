<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends \Illuminate\Database\Migrations\Migration {
    public function up(): void {
        Schema::table('documents', function (Blueprint $table) {
            if (!Schema::hasColumn('documents', 'created_by')) {
                $table->foreignId('created_by')
                      ->nullable()
                      ->constrained('users')
                      ->nullOnDelete()
                      ->after('id');
            }
        });
    }
    public function down(): void {
        Schema::table('documents', function (Blueprint $table) {
            if (Schema::hasColumn('documents', 'created_by')) {
                $table->dropConstrainedForeignId('created_by');
            }
        });
    }
};
