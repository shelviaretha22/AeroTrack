<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Riwayat login (untuk kartu keamanan di halaman Profile).
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip', 45)->nullable();
            $table->timestamp('previous_login_at')->nullable();
            $table->string('previous_login_ip', 45)->nullable();

            // Preferensi per jenis notifikasi, misalnya {"lokasi_kosong": false}.
            // Jenis yang tidak tercatat dianggap aktif.
            $table->json('notification_preferences')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'last_login_at',
                'last_login_ip',
                'previous_login_at',
                'previous_login_ip',
                'notification_preferences',
            ]);
        });
    }
};
