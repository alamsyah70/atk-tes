<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_stok_opnames', function (Blueprint $table) {
            $table->id();

            $table->foreignId('stok_opname_id')
                ->constrained('stok_opnames')
                ->cascadeOnDelete();

            $table->foreignId('barang_id')
                ->constrained('barangs')
                ->restrictOnDelete();

            $table->string('kode_barang');
            $table->string('nama_barang');

            // Stok sistem saat sesi opname dibuat
            $table->integer('stok_sistem')->default(0);

            // Diisi petugas saat menghitung fisik
            $table->integer('stok_fisik')->nullable();

            // Stok fisik dikurangi stok sistem
            $table->integer('selisih')->nullable();

            $table->timestamps();

            $table->unique(
                ['stok_opname_id', 'barang_id'],
                'stok_opname_barang_unique'
            );

            $table->index('kode_barang');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_stok_opnames');
    }
};
