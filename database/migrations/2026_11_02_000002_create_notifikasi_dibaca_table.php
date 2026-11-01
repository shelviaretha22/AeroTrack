<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Notifikasi dihitung langsung dari data (kontrak, aktivasi, lokasi).
        // Tabel ini hanya mencatat notifikasi mana yang sudah dibaca user.
        Schema::create('notifikasi_dibaca', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('kunci', 191);
            $table->timestamp('dibaca_at')->useCurrent();

            $table->unique(['user_id', 'kunci']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifikasi_dibaca');
    }
};
