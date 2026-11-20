<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::table('barangs', function (Blueprint $table) {
        $table->decimal('average_perolehan', 18, 6)
            ->nullable()
            ->default(0)
            ->change();
    });
}

public function down(): void
{
    Schema::table('barangs', function (Blueprint $table) {
        $table->decimal('average_perolehan', 15, 2)
            ->nullable()
            ->default(null)
            ->change();
    });
}
};
