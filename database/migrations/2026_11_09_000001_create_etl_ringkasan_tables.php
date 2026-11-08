<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Catatan setiap proses ETL dijalankan.
        Schema::create('etl_runs', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('pemicu')->default('artisan'); // artisan | jadwal | manual
            $table->string('status')->default('berjalan'); // berjalan | berhasil | gagal
            $table->unsignedInteger('baris_diekstrak')->default(0);
            $table->unsignedInteger('baris_dimuat')->default(0);
            $table->text('pesan')->nullable();
            $table->timestamp('mulai_at')->nullable();
            $table->timestamp('selesai_at')->nullable();
            $table->timestamps();
        });

        // Tabel ringkasan okupansi Lokasi (hasil Transform + Load).
        Schema::create('ringkasan_okupansi', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal_snapshot');
            $table->string('terminal');
            $table->string('lantai');
            $table->string('jenis_ruangan');
            $table->unsignedInteger('total_ruangan')->default(0);
            $table->unsignedInteger('terisi')->default(0);
            $table->unsignedInteger('kosong')->default(0);
            $table->decimal('okupansi', 5, 1)->default(0);
            $table->decimal('luas_total', 12, 2)->default(0);
            $table->decimal('luas_terisi', 12, 2)->default(0);
            $table->timestamps();

            $table->unique(
                ['tanggal_snapshot', 'terminal', 'lantai', 'jenis_ruangan'],
                'uniq_ringkasan_okupansi'
            );
        });

        // Tabel ringkasan Aktivasi per periode pengajuan dan status.
        Schema::create('ringkasan_aktivasi', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal_snapshot');
            $table->string('periode'); // YYYY-MM tanggal pengajuan, atau "-"
            $table->string('status');
            $table->unsignedInteger('jumlah')->default(0);
            $table->decimal('rata_hari_keputusan', 8, 1)->nullable();
            $table->timestamps();

            $table->unique(
                ['tanggal_snapshot', 'periode', 'status'],
                'uniq_ringkasan_aktivasi'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ringkasan_aktivasi');
        Schema::dropIfExists('ringkasan_okupansi');
        Schema::dropIfExists('etl_runs');
    }
};
