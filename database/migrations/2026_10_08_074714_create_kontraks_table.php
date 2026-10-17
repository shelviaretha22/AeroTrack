<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('kontraks', function (Blueprint $table) {
            $table->id();

            // Nomor kontrak dibuat otomatis oleh sistem
            $table->string('nomor_kontrak')->unique();

            // Relasi data yang sudah diinput sebelumnya
            $table->foreignId('kerja_sama_id')
                ->constrained('kerja_samas')
                ->cascadeOnDelete();

            $table->foreignId('tenant_id')
                ->constrained('tenants')
                ->cascadeOnDelete();

            $table->foreignId('lokasi_id')
                ->constrained('lokasis')
                ->cascadeOnDelete();

            // Tanggal proses kontrak
            $table->date('tanggal_pengajuan');
            $table->date('tanggal_acc')->nullable();

            // File Berita Acara yang sudah dibuat sebelumnya
            $table->string('ba_path')->nullable();

            // Tanda tangan PIC Commercial
            $table->string('ttd_commercial_path')->nullable();

            // Cara memasukkan tanda tangan:
            // draw = gambar langsung di layar
            // upload = upload file JPG
            $table->enum('ttd_type', ['draw', 'upload'])->nullable();

            // Status kontrak
            $table->string('status')->default('Draft');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kontraks');
    }
};