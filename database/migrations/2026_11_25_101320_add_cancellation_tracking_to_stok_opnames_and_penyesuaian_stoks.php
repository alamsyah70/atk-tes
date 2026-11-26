<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stok_opnames', function (Blueprint $table) {
            $table->timestamp('dibatalkan_at')->nullable();
            $table->foreignId('dibatalkan_oleh')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->text('alasan_pembatalan')->nullable();
        });

        Schema::table('penyesuaian_stoks', function (Blueprint $table) {
            $table->foreignId('stok_opname_id')
                ->nullable()
                ->constrained('stok_opnames')
                ->nullOnDelete();

            $table->foreignId('pembalikan_dari_id')
                ->nullable()
                ->constrained('penyesuaian_stoks')
                ->nullOnDelete();

            $table->unique(
                'pembalikan_dari_id',
                'penyesuaian_stoks_pembalikan_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('penyesuaian_stoks', function (Blueprint $table) {
            $table->dropUnique('penyesuaian_stoks_pembalikan_unique');
            $table->dropConstrainedForeignId('pembalikan_dari_id');
            $table->dropConstrainedForeignId('stok_opname_id');
        });

        Schema::table('stok_opnames', function (Blueprint $table) {
            $table->dropConstrainedForeignId('dibatalkan_oleh');
            $table->dropColumn([
                'dibatalkan_at',
                'alasan_pembatalan',
            ]);
        });
    }
};