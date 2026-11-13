
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kerja_samas', function (Blueprint $table) {
            $table->string('berita_acara_path')->nullable();

            $table->string('status', 50)
                ->default('Draft')
                ->change();
        });
    }

    public function down(): void
    {
        // Kembalikan status ke nilai yang didukung enum lama.
        DB::table('kerja_samas')
            ->where('status', 'Pengajuan BA')
            ->update(['status' => 'Proses']);

        DB::table('kerja_samas')
            ->where('status', 'Aktivasi')
            ->update(['status' => 'Disetujui']);

        Schema::table('kerja_samas', function (Blueprint $table) {
            $table->enum('status', [
                'Draft',
                'Proses',
                'Disetujui',
                'Ditolak'
            ])->default('Draft')->change();

            $table->dropColumn('berita_acara_path');
        });
    }
};
