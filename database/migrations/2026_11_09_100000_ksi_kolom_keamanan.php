<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // IP login disimpan terenkripsi, hasil enkripsi lebih panjang dari 45 karakter.
        Schema::table('users', function (Blueprint $table) {
            $table->text('last_login_ip')->nullable()->change();
            $table->text('previous_login_ip')->nullable()->change();
        });

        // Hash SHA-256 untuk keutuhan dokumen Aktivasi.
        Schema::table('aktivasis', function (Blueprint $table) {
            $table->string('surat_pengajuan_sha256', 64)->nullable();
            $table->string('dokumen_sha256', 64)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('aktivasis', function (Blueprint $table) {
            $table->dropColumn(['surat_pengajuan_sha256', 'dokumen_sha256']);
        });

    }
};
