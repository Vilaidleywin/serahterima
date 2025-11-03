<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('documents', function (Blueprint $table) {
            if (!Schema::hasColumn('documents', 'signature_path')) {
                $table->string('signature_path')->nullable()->after('description');
            }
            if (!Schema::hasColumn('documents', 'signed_at')) {
                $table->timestamp('signed_at')->nullable()->after('signature_path');
            }
            if (!Schema::hasColumn('documents', 'signed_by')) {
                $table->foreignId('signed_by')->nullable()->constrained('users')->nullOnDelete()->after('signed_at');
            }
        });
    }
    public function down(): void {
        Schema::table('documents', function (Blueprint $table) {
            if (Schema::hasColumn('documents', 'signed_by')) {
                $table->dropConstrainedForeignId('signed_by');
            }
            if (Schema::hasColumn('documents', 'signed_at')) {
                $table->dropColumn('signed_at');
            }
            if (Schema::hasColumn('documents', 'signature_path')) {
                $table->dropColumn('signature_path');
            }
        });
    }
};
