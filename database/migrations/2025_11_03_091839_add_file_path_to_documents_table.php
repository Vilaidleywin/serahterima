<?php

// database/migrations/xxxx_add_file_path_to_documents_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::table('documents', function (Blueprint $table) {
      if (!Schema::hasColumn('documents', 'file_path')) {
        $table->string('file_path')->nullable()->after('description');
      }
    });
  }
  public function down(): void {
    Schema::table('documents', function (Blueprint $table) {
      if (Schema::hasColumn('documents', 'file_path')) {
        $table->dropColumn('file_path');
      }
    });
  }
};
