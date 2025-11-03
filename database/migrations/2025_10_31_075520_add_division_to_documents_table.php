<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('documents', function (Blueprint $table) {
            if (!Schema::hasColumn('documents', 'division')) {
                $table->string('division', 100)->nullable()->after('destination');
            }
        });
    }

    public function down(): void {
        Schema::table('documents', function (Blueprint $table) {
            if (Schema::hasColumn('documents', 'division')) {
                $table->dropColumn('division');
            }
        });
    }
};
