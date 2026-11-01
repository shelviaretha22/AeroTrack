<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Progres Kontrak Fisik dan Kontrak Sistem per Kerja Sama.
        // Status pengajuan Aktivasi tetap disimpan di tabel aktivasis.
        Schema::create('aktivasi_progres', function (Blueprint $table) {
            $table->id();

            $table->foreignId('kerja_sama_id')
                ->unique()
                ->constrained('kerja_samas')
                ->cascadeOnDelete();

            // A. Kontrak Fisik (diproses di luar AeroTrack)
            $table->string('kontrak_fisik_status')->default('Belum Selesai');
            $table->text('kontrak_fisik_catatan')->nullable();
            $table->timestamp('kontrak_fisik_updated_at')->nullable();

            // B. Kontrak Sistem (sistem operasional bandara)
            $table->string('kontrak_sistem_status')->default('Belum Tercatat');
            $table->date('tanggal_aktivasi_sistem')->nullable();
            $table->text('kontrak_sistem_catatan')->nullable();
            $table->string('kontrak_sistem_bukti_path')->nullable();
            $table->timestamp('kontrak_sistem_updated_at')->nullable();

            $table->timestamps();
        });

        // Riwayat perubahan: waktu dan pengguna.
        Schema::create('aktivasi_riwayats', function (Blueprint $table) {
            $table->id();

            $table->foreignId('kerja_sama_id')
                ->constrained('kerja_samas')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('komponen'); // Kontrak Fisik | Kontrak Sistem | Aktivasi
            $table->text('keterangan');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aktivasi_riwayats');
        Schema::dropIfExists('aktivasi_progres');
    }
};
