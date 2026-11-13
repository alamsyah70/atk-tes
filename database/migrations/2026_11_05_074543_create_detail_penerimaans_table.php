
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_penerimaans', function (Blueprint $table) {
            $table->id();

            $table->foreignId('penerimaan_id')
                ->constrained('penerimaans')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->string('kode_barang');
            $table->string('nama_barang');
            $table->string('kategori');
            $table->string('satuan');
            $table->string('lokasi');

            $table->integer('qty');
            $table->decimal('harga', 15, 2);
            $table->decimal('jumlah', 15, 2);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_penerimaans');
    }
};
