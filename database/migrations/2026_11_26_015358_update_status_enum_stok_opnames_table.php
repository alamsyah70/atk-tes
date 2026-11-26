<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE stok_opnames
            MODIFY status ENUM('draft', 'selesai', 'dibatalkan')
            NOT NULL DEFAULT 'draft'
        ");
    }

    public function down(): void
    {
        // Kembalikan status dibatalkan menjadi selesai
        // sebelum menghapus pilihan enum tersebut.
        DB::table('stok_opnames')
            ->where('status', 'dibatalkan')
            ->update(['status' => 'selesai']);

        DB::statement("
            ALTER TABLE stok_opnames
            MODIFY status ENUM('draft', 'selesai')
            NOT NULL DEFAULT 'draft'
        ");
    }
};
