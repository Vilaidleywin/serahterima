<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Tambah created_by_division kalau belum ada
        if (!Schema::hasColumn('documents', 'created_by_division')) {
            Schema::table('documents', function (Blueprint $table) {
                $table->string('created_by_division')->nullable()->after('division');
            });
        }

        // Tambah target_division kalau belum ada
        if (!Schema::hasColumn('documents', 'target_division')) {
            Schema::table('documents', function (Blueprint $table) {
                $table->string('target_division')->nullable()->after('created_by_division');
            });
        }
    }

    public function down()
    {
        Schema::table('documents', function (Blueprint $table) {
            if (Schema::hasColumn('documents', 'created_by_division')) {
                $table->dropColumn('created_by_division');
            }
            if (Schema::hasColumn('documents', 'target_division')) {
                $table->dropColumn('target_division');
            }
        });
    }
};
