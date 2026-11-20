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
        Schema::table('barangs', function (Blueprint $table) {
            // Cabang pemilik barang.
            // Nullable agar data barang lama tetap aman.
            $table->foreignId('branch_id')
                ->nullable()
                ->after('id')
                ->constrained('branches')
                ->restrictOnDelete();

            // Hapus unique global kode_barang.
            $table->dropUnique('barangs_kode_barang_unique');

            // Kode barang hanya wajib unik dalam cabang yang sama.
            $table->unique(
                ['branch_id', 'kode_barang'],
                'barangs_branch_kode_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('barangs', function (Blueprint $table) {
            $table->dropUnique('barangs_branch_kode_unique');

            $table->dropForeign(['branch_id']);

            $table->dropColumn('branch_id');

            // Kembalikan unique global jika rollback.
            $table->unique('kode_barang', 'barangs_kode_barang_unique');
        });
    }
};
