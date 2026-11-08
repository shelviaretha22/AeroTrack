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

            // Siapa (kosong untuk login gagal dari email yang tidak dikenal)
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            // Apa dan di mana
            $table->string('modul', 30);          // Autentikasi, Akun, Lokasi, Aktivasi
            $table->string('aksi', 60);           // mis. Login berhasil, Lokasi dihapus
            $table->string('level', 15)->default('info'); // info | peringatan
            $table->text('keterangan')->nullable();

            // Data yang terkena (opsional)
            $table->string('subjek_tipe', 60)->nullable();
            $table->unsignedBigInteger('subjek_id')->nullable();

            // Dari mana
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 255)->nullable();

            $table->timestamp('created_at')->useCurrent();

            $table->index(['modul', 'created_at']);
            $table->index(['user_id', 'created_at']);
            $table->index('level');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
