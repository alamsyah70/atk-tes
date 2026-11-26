<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stok_opnames', function (Blueprint $table) {
            $table->id();

            $table->string('nomor_opname')->unique();

            $table->foreignId('branch_id')
                ->constrained('branches')
                ->restrictOnDelete();

            $table->date('tanggal');

            $table->text('keterangan')->nullable();

            $table->enum('status', ['draft', 'selesai'])
                ->default('draft');

            // Waktu pengambilan stok sistem
            $table->timestamp('snapshot_at')->nullable();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('finalized_at')->nullable();

            $table->timestamps();

            $table->index(['branch_id', 'tanggal']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stok_opnames');
    }
};
