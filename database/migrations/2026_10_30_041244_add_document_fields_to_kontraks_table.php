
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kontraks', function (Blueprint $table) {

            // =====================================
            // INFORMASI DOKUMEN KONTRAK
            // =====================================

            $table->string('nomor_kontrak_mitra')->nullable();
            $table->string('logo_mitra_path')->nullable();
            $table->date('tanggal_dokumen')->nullable();

            $table->unsignedInteger('periode_evaluasi_tarif')->nullable();
            $table->string('satuan_evaluasi_tarif', 20)->nullable();

            // Pilihan klausul kepemilikan fasilitas
            $table->string('klausul_kepemilikan_fasilitas')->nullable();

            // =====================================
            // SNAPSHOT IDENTITAS DAN KONTAK MITRA
            // =====================================

            $table->string('nama_mitra_snapshot')->nullable();
            $table->string('nama_pic_mitra_snapshot')->nullable();
            $table->string('jabatan_pic_mitra_snapshot')->nullable();

            $table->text('alamat_mitra_snapshot')->nullable();
            $table->string('telepon_mitra_snapshot')->nullable();
            $table->string('fax_mitra_snapshot')->nullable();
            $table->string('email_mitra_snapshot')->nullable();

            // =====================================
            // SNAPSHOT PENANDATANGAN MITRA
            // =====================================

            $table->string('nama_penandatangan_mitra')->nullable();
            $table->string('jabatan_penandatangan_mitra')->nullable();

            // =====================================
            // SNAPSHOT KERJA SAMA DAN LOKASI
            // =====================================

            $table->json('data_kerja_sama_snapshot')->nullable();
            $table->json('data_lokasi_snapshot')->nullable();

            // =====================================
            // PILIHAN LAMPIRAN PERJANJIAN
            // =====================================

            $table->json('lampiran_perjanjian')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('kontraks', function (Blueprint $table) {

            $table->dropColumn([
                'nomor_kontrak_mitra',
                'logo_mitra_path',
                'tanggal_dokumen',
                'periode_evaluasi_tarif',
                'satuan_evaluasi_tarif',
                'klausul_kepemilikan_fasilitas',

                'nama_mitra_snapshot',
                'nama_pic_mitra_snapshot',
                'jabatan_pic_mitra_snapshot',
                'alamat_mitra_snapshot',
                'telepon_mitra_snapshot',
                'fax_mitra_snapshot',
                'email_mitra_snapshot',

                'nama_penandatangan_mitra',
                'jabatan_penandatangan_mitra',

                'data_kerja_sama_snapshot',
                'data_lokasi_snapshot',
                'lampiran_perjanjian',
            ]);
        });
    }
};
