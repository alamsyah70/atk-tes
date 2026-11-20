<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            // Hapus UNIQUE global pada kode_supplier
            $table->dropUnique('suppliers_kode_supplier_unique');

            // Kode supplier hanya unik dalam cabang yang sama
            $table->unique(
                ['branch_id', 'kode_supplier'],
                'suppliers_branch_kode_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropUnique('suppliers_branch_kode_unique');

            // Mengembalikan aturan unik global
            $table->unique('kode_supplier');
        });
    }
};