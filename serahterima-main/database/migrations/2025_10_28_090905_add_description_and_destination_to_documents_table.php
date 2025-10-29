<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            if (!Schema::hasColumn('documents', 'destination')) {
                $table->string('destination')->nullable()->after('receiver');
            }
            if (!Schema::hasColumn('documents', 'amount_idr')) {
                $table->decimal('amount_idr', 15, 2)->nullable()->after('destination');
            }
            if (!Schema::hasColumn('documents', 'description')) {
                $table->text('description')->nullable()->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn(['destination', 'amount_idr', 'description']);
        });
    }
};
