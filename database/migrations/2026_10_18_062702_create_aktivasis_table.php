
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aktivasis', function (Blueprint $table) {
            $table->id();

            // Relasi dengan data Kerja Sama
            $table->foreignId('kerja_sama_id')
                ->unique()
                ->constrained('kerja_samas')
                ->cascadeOnDelete();

            // Informasi pengajuan aktivasi
            $table->string('nomor_pengajuan')->nullable();
            $table->date('tanggal_pengajuan')->nullable();

            // Status proses aktivasi
            $table->string('status')
                ->default('Belum Diajukan');

            // Diisi ketika keputusan pusat sudah diterima
            $table->date('tanggal_keputusan')->nullable();
            $table->text('catatan')->nullable();
            $table->string('dokumen_path')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aktivasis');
    }
};
