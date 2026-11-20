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
        Schema::table('detail_penerimaans', function (Blueprint $table) {
            $table->unsignedBigInteger('barang_id')
                ->nullable()
                ->after('id');

            $table->foreign('barang_id')
                ->references('id')
                ->on('barangs')
                ->nullOnDelete();
        });

        Schema::table('detail_pengeluarans', function (Blueprint $table) {
            $table->unsignedBigInteger('barang_id')
                ->nullable()
                ->after('id');

            $table->foreign('barang_id')
                ->references('id')
                ->on('barangs')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('detail_pengeluarans', function (Blueprint $table) {
            $table->dropForeign(['barang_id']);
            $table->dropColumn('barang_id');
        });

        Schema::table('detail_penerimaans', function (Blueprint $table) {
            $table->dropForeign(['barang_id']);
            $table->dropColumn('barang_id');
        });
    }
};
