<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1) cari & drop UNIQUE index apa pun yang nempel ke kolom user_id
        $indexes = DB::select("SHOW INDEX FROM user_logins WHERE Column_name = 'user_id' AND Non_unique = 0");
        foreach ($indexes as $idx) {
            DB::statement("ALTER TABLE user_logins DROP INDEX `{$idx->Key_name}`");
        }

        // 2) pastikan ada index biasa (non-unique) untuk user_id
        $normalIdx = DB::select("SHOW INDEX FROM user_logins WHERE Column_name = 'user_id' AND Non_unique = 1");
        if (count($normalIdx) === 0) {
            DB::statement("ALTER TABLE user_logins ADD INDEX user_logins_user_id_index (user_id)");
        }

        // 3) (opsional) bersihin data yatim biar kalau mau pasang FK aman
        DB::statement("
            DELETE ul FROM user_logins ul
            LEFT JOIN users u ON u.id = ul.user_id
            WHERE u.id IS NULL
        ");

        // 4) (opsional) pasang FK dengan nama aman, tapi jangan bikin migrate gagal kalau udah ada
        try {
            DB::statement("
                ALTER TABLE user_logins
                ADD CONSTRAINT fk_user_logins_user_id
                FOREIGN KEY (user_id) REFERENCES users(id)
                ON DELETE CASCADE
            ");
        } catch (\Throwable $e) {
            // ignore
        }
    }

    public function down(): void
    {
        // rollback FK kalau ada
        try {
            DB::statement("ALTER TABLE user_logins DROP FOREIGN KEY fk_user_logins_user_id");
        } catch (\Throwable $e) {}

        // drop index biasa kalau ada
        try {
            DB::statement("ALTER TABLE user_logins DROP INDEX user_logins_user_id_index");
        } catch (\Throwable $e) {}

        // balikin unique (opsional)
        try {
            DB::statement("ALTER TABLE user_logins ADD UNIQUE user_logins_user_id_unique (user_id)");
        } catch (\Throwable $e) {}
    }
};
