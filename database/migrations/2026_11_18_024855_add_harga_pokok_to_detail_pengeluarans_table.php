<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('detail_pengeluarans', function (Blueprint $table) {
            $table->decimal('harga_pokok', 15, 2)
                ->nullable()
                ->after('qty');

            $table->decimal('jumlah_harga_pokok', 15, 2)
                ->nullable()
                ->after('harga_pokok');
        });
    }

    public function down(): void
    {
        Schema::table('detail_pengeluarans', function (Blueprint $table) {
            $table->dropColumn([
                'harga_pokok',
                'jumlah_harga_pokok',
            ]);
        });
    }
};
