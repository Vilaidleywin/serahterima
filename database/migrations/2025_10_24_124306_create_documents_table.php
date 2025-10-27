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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();   // ST-001
            $table->string('title');              // Surat Kontrak
            $table->string('receiver');           // Budi
            $table->bigInteger('amount')->default(0); // nominal dalam rupiah
            $table->date('date');                 // 2025-10-01
            $table->enum('status', ['PENDING', 'DONE', 'FAILED'])->default('PENDING');
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
