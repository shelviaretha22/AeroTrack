<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kerja_samas', function (Blueprint $table) {
            $table->id();

            // Informasi Mitra
            $table->string('mitra_usaha');
            $table->string('brand')->nullable();
            $table->string('jenis_usaha');
            $table->string('bentuk_kerja_sama');

            // Informasi Proses Kerja Sama
            $table->date('tanggal_pengajuan');
            $table->enum('status', [
                'Draft',
                'Proses',
                'Disetujui',
                'Ditolak'
            ])->default('Draft');

            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_berakhir')->nullable();

            // Informasi Pengajuan
            $table->string('pic_commercial');
            $table->text('catatan')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kerja_samas');
    }
};