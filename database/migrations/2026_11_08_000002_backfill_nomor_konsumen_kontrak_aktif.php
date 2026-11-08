<?php

use App\Models\AktivasiProgres;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Memberi nomor konsumen otomatis (NK-00001, ...) untuk kontrak yang
     * sudah aktif sebelum fitur ini ada. Urut menurut tanggal persetujuan.
     * Kontrak yang sudah punya nomor tidak diubah.
     */
    public function up(): void
    {
        $ids = DB::table('aktivasis')
            ->where('status', 'Disetujui')
            ->orderBy('tanggal_keputusan')
            ->orderBy('id')
            ->pluck('kerja_sama_id')
            ->unique();

        foreach ($ids as $id) {
            AktivasiProgres::terbitkanNomorKonsumen((int) $id);
        }
    }

    public function down(): void
    {
        // Tidak ada yang dikembalikan; nomor yang sudah terbit dibiarkan.
    }
};
