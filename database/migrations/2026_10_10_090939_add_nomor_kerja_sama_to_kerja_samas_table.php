
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Menambahkan nomor kerja sama.
     */
    public function up(): void
    {
        Schema::table('kerja_samas', function (Blueprint $table) {
            $table->string('nomor_kerja_sama')
                ->nullable()
                ->unique()
                ->after('id');
        });

        // Mengisi nomor untuk data kerja sama yang sudah ada.
        $ids = DB::table('kerja_samas')
            ->orderBy('id')
            ->pluck('id');

        foreach ($ids as $index => $id) {
            DB::table('kerja_samas')
                ->where('id', $id)
                ->update([
                    'nomor_kerja_sama' => sprintf(
                        'KS-%03d',
                        $index + 1
                    ),
                ]);
        }
    }

    /**
     * Menghapus kolom jika migration di-rollback.
     */
    public function down(): void
    {
        Schema::table('kerja_samas', function (Blueprint $table) {
            $table->dropUnique(['nomor_kerja_sama']);
            $table->dropColumn('nomor_kerja_sama');
        });
    }
};
