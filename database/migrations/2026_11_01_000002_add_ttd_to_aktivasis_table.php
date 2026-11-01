<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('aktivasis', function (Blueprint $table) {
            // Tanda tangan PIC Commercial pada surat pengajuan.
            $table->string('ttd_commercial_path')->nullable();
            $table->string('ttd_type')->nullable(); // draw | upload
            $table->string('nama_penandatangan')->nullable();
            $table->string('jabatan_penandatangan')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('aktivasis', function (Blueprint $table) {
            $table->dropColumn([
                'ttd_commercial_path',
                'ttd_type',
                'nama_penandatangan',
                'jabatan_penandatangan',
            ]);
        });
    }
};
