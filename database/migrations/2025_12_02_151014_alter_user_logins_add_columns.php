<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('user_logins', function (Blueprint $table) {
            if (!Schema::hasColumn('user_logins', 'user_id')) {
                $table->foreignId('user_id')->after('id')->constrained()->cascadeOnDelete();
            }

            if (!Schema::hasColumn('user_logins', 'ip')) {
                $table->string('ip')->nullable()->after('user_id');
            }

            if (!Schema::hasColumn('user_logins', 'user_agent')) {
                $table->string('user_agent')->nullable()->after('ip');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_logins', function (Blueprint $table) {
            if (Schema::hasColumn('user_logins', 'user_agent')) {
                $table->dropColumn('user_agent');
            }

            if (Schema::hasColumn('user_logins', 'ip')) {
                $table->dropColumn('ip');
            }

            if (Schema::hasColumn('user_logins', 'user_id')) {
                $table->dropConstrainedForeignId('user_id');
            }
        });
    }
};
