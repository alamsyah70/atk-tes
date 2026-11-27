<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();

            // Identitas pelaku saat aktivitas dilakukan
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->string('actor_user', 100)->nullable();
            $table->string('actor_name', 150)->nullable();
            $table->json('actor_roles')->nullable();

            // Informasi aktivitas
            $table->string('action', 50);
            $table->string('module', 100);
            $table->text('description');

            // Data objek yang mengalami perubahan
            $table->string('subject_type', 150)->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();

            // Snapshot perubahan
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();

            // Informasi perangkat / akses
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();

            $table->timestamp('created_at')->useCurrent();

            $table->index(['branch_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
            $table->index(['module', 'created_at']);
            $table->index(['action', 'created_at']);
            $table->index(['subject_type', 'subject_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
