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
        Schema::create('detail_pengeluarans', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pengeluaran_id')
                ->constrained('pengeluarans')
                ->cascadeOnDelete();

            $table->string('kode_barang');
            $table->string('nama_barang');
            $table->string('kategori')->nullable();
            $table->string('satuan')->nullable();
            $table->string('lokasi')->nullable();

            $table->integer('qty');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_pengeluarans');
    }
};
