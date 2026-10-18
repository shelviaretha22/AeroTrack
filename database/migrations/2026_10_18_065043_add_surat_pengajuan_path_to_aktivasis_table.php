<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('aktivasis', function (Blueprint $table) {
            $table->string('surat_pengajuan_path')
                ->nullable()
                ->after('tanggal_pengajuan');

            $table->unique(
                'nomor_pengajuan',
                'aktivasis_nomor_pengajuan_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('aktivasis', function (Blueprint $table) {
            $table->dropUnique('aktivasis_nomor_pengajuan_unique');
            $table->dropColumn('surat_pengajuan_path');
        });
    }
};