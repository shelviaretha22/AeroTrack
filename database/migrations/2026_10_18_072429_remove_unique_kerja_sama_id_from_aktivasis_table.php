<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('aktivasis', function (Blueprint $table) {
            // Lepaskan foreign key sementara.
            $table->dropForeign(['kerja_sama_id']);

            // Hapus batasan unik agar satu kerja sama
            // dapat memiliki beberapa riwayat pengajuan.
            $table->dropUnique('aktivasis_kerja_sama_id_unique');

            // Pasang kembali foreign key tanpa batasan unik.
            $table->foreign('kerja_sama_id')
                ->references('id')
                ->on('kerja_samas')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('aktivasis', function (Blueprint $table) {
            $table->dropForeign(['kerja_sama_id']);

            $table->unique('kerja_sama_id');

            $table->foreign('kerja_sama_id')
                ->references('id')
                ->on('kerja_samas')
                ->cascadeOnDelete();
        });
    }
};