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
        Schema::table('penerimaans', function (Blueprint $table) {
            $table->string('nomor_penerimaan')
                ->nullable()
                ->unique()
                ->after('id');

            $table->string('bukti_po')->nullable()->after('keterangan');
        });
    }

    public function down(): void
    {
        Schema::table('penerimaans', function (Blueprint $table) {
            $table->dropUnique(['nomor_penerimaan']);
            $table->dropColumn(['nomor_penerimaan', 'bukti_po']);
        });
    }
};
