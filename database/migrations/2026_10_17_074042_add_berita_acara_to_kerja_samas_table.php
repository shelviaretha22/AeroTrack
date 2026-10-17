
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
        });

        DB::statement("
            ALTER TABLE kerja_samas
            MODIFY status VARCHAR(50) NOT NULL DEFAULT 'Draft'
        ");
    }

    public function down(): void
    {
        // Kembalikan status baru ke nilai yang didukung enum lama.
        DB::table('kerja_samas')
            ->where('status', 'Pengajuan BA')
            ->update(['status' => 'Proses']);

        DB::table('kerja_samas')
            ->where('status', 'Aktivasi')
            ->update(['status' => 'Disetujui']);

        DB::statement("
            ALTER TABLE kerja_samas
            MODIFY status ENUM(
                'Draft',
                'Proses',
                'Disetujui',
                'Ditolak'
            ) NOT NULL DEFAULT 'Draft'
        ");

        Schema::table('kerja_samas', function (Blueprint $table) {
            $table->dropColumn('berita_acara_path');
        });
    }
};
