<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('penyesuaian_stoks', function (Blueprint $table) {
            $table->foreignId('barang_id')
                ->nullable()
                ->after('branch_id')
                ->constrained('barangs')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('penyesuaian_stoks', function (Blueprint $table) {
            $table->dropForeign(['barang_id']);
            $table->dropColumn('barang_id');
        });
    }
};
