<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // ubah kolom status biar bisa DRAFT juga
        DB::statement("ALTER TABLE documents MODIFY status ENUM('DRAFT','SUBMITTED','REJECTED') DEFAULT 'DRAFT'");
    }

    public function down(): void
    {
        // rollback ke enum lama
        DB::statement("ALTER TABLE documents MODIFY status ENUM('SUBMITTED','REJECTED') DEFAULT 'SUBMITTED'");
    }
};
