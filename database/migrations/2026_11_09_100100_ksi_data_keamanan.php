<?php

use App\Services\IntegritasDokumen;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Penyesuaian data lama agar cocok dengan fitur keamanan baru.
 * Aman dijalankan ulang (idempoten).
 */
return new class extends Migration
{
    public function up(): void
    {
        // 1. Enkripsi IP login lama.
        foreach (DB::table('users')->select('id', 'last_login_ip', 'previous_login_ip')->get() as $u) {
            $ubah = [];

            foreach (['last_login_ip', 'previous_login_ip'] as $kolom) {
                if (filled($u->$kolom) && ! $this->terenkripsi($u->$kolom)) {
                    $ubah[$kolom] = Crypt::encryptString($u->$kolom);
                }
            }

            if ($ubah) {
                DB::table('users')->where('id', $u->id)->update($ubah);
            }
        }

        // 2. Enkripsi catatan keputusan Aktivasi lama, dan catat hash dokumen
        //    yang sudah ada (dipercaya apa adanya saat migrasi ini dijalankan).
        foreach (DB::table('aktivasis')
            ->select('id', 'catatan', 'surat_pengajuan_path', 'surat_pengajuan_sha256', 'dokumen_path', 'dokumen_sha256')
            ->get() as $a) {
            $ubah = [];

            if (filled($a->catatan) && ! $this->terenkripsi($a->catatan)) {
                $ubah['catatan'] = Crypt::encryptString($a->catatan);
            }

            if (blank($a->surat_pengajuan_sha256) && ($h = IntegritasDokumen::hash($a->surat_pengajuan_path))) {
                $ubah['surat_pengajuan_sha256'] = $h;
            }

            if (blank($a->dokumen_sha256) && ($h = IntegritasDokumen::hash($a->dokumen_path))) {
                $ubah['dokumen_sha256'] = $h;
            }

            if ($ubah) {
                DB::table('aktivasis')->where('id', $a->id)->update($ubah);
            }
        }

        // 3. Pindahkan foto profil dari disk publik ke disk privat.
        foreach (DB::table('users')->whereNotNull('avatar_path')->get(['id', 'avatar_path']) as $u) {
            $publik = Storage::disk('public');
            $privat = Storage::disk('local');

            if ($publik->exists($u->avatar_path)) {
                if (! $privat->exists($u->avatar_path)) {
                    $privat->put($u->avatar_path, $publik->get($u->avatar_path));
                }

                $publik->delete($u->avatar_path);
            }
        }
    }

    public function down(): void
    {
        // Sengaja kosong: enkripsi dan perpindahan file tidak dibalik otomatis.
    }

    private function terenkripsi(string $nilai): bool
    {
        try {
            Crypt::decryptString($nilai);

            return true;
        } catch (\Throwable) {
            return false;
        }
    }
};
