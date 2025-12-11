<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();   
            $table->string('title');             
            $table->string('receiver');           
            $table->bigInteger('amount')->default(0); 
            $table->date('date');                 
            $table->enum('status', ['SUBMITTED', 'REJECTED'])->default('SUBMITTED');
            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
