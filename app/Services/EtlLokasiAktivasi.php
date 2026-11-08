<?php

namespace App\Services;

use App\Models\Aktivasi;
use App\Models\Lokasi;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * ETL untuk modul Lokasi dan Aktivasi.
 *
 *  Extract   : ambil data mentah dari tabel lokasis dan aktivasis.
 *  Transform : rapikan nilai kosong menjadi "-", hitung okupansi,
 *              kelompokkan aktivasi per periode dan status.
 *  Load      : simpan ke ringkasan_okupansi dan ringkasan_aktivasi
 *              (snapshot per hari; dijalankan ulang di hari yang sama
 *              akan menggantikan snapshot hari itu).
 */
class EtlLokasiAktivasi
{
    public const NAMA = 'lokasi-aktivasi';

    /**
     * @return array{okupansi:int, aktivasi:int, diekstrak:int, run_id:int}
     */
    public function jalankan(string $pemicu = 'artisan'): array
    {
        $runId = DB::table('etl_runs')->insertGetId([
            'nama'       => self::NAMA,
            'pemicu'     => $pemicu,
            'status'     => 'berjalan',
            'mulai_at'   => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        try {
            // ---- EXTRACT ----
            $lokasi = $this->ekstrakLokasi();
            $aktivasi = $this->ekstrakAktivasi();

            // ---- TRANSFORM ----
            $okupansi = $this->transformOkupansi($lokasi);
            $ringkasAktivasi = $this->transformAktivasi($aktivasi);

            // ---- LOAD ----
            $this->muat($okupansi, $ringkasAktivasi);

            $diekstrak = $lokasi->count() + $aktivasi->count();
            $dimuat = $okupansi->count() + $ringkasAktivasi->count();

            DB::table('etl_runs')->where('id', $runId)->update([
                'status'          => 'berhasil',
                'baris_diekstrak' => $diekstrak,
                'baris_dimuat'    => $dimuat,
                'pesan'           => 'Okupansi: ' . $okupansi->count()
                    . ' baris, Aktivasi: ' . $ringkasAktivasi->count() . ' baris.',
                'selesai_at'      => now(),
                'updated_at'      => now(),
            ]);

            return [
                'okupansi'  => $okupansi->count(),
                'aktivasi'  => $ringkasAktivasi->count(),
                'diekstrak' => $diekstrak,
                'run_id'    => $runId,
            ];
        } catch (Throwable $e) {
            DB::table('etl_runs')->where('id', $runId)->update([
                'status'     => 'gagal',
                'pesan'      => mb_substr($e->getMessage(), 0, 1000),
                'selesai_at' => now(),
                'updated_at' => now(),
            ]);

            throw $e;
        }
    }

    // ------------------------------------------------------------------
    // EXTRACT
    // ------------------------------------------------------------------

    private function ekstrakLokasi(): Collection
    {
        return Lokasi::query()->get([
            'terminal', 'lantai', 'jenis_ruangan', 'status', 'luas_area',
        ]);
    }

    private function ekstrakAktivasi(): Collection
    {
        return Aktivasi::query()->get([
            'status', 'tanggal_pengajuan', 'tanggal_keputusan',
        ]);
    }

    // ------------------------------------------------------------------
    // TRANSFORM
    // ------------------------------------------------------------------

    private function bersihkan(mixed $nilai): string
    {
        $nilai = trim((string) $nilai);

        return $nilai === '' ? '-' : $nilai;
    }

    private function transformOkupansi(Collection $lokasi): Collection
    {
        return $lokasi
            ->groupBy(fn (Lokasi $l) => implode('|', [
                $this->bersihkan($l->terminal),
                $this->bersihkan($l->lantai),
                $this->bersihkan($l->jenis_ruangan),
            ]))
            ->map(function (Collection $grup, string $kunci) {
                [$terminal, $lantai, $jenis] = explode('|', $kunci);

                $total = $grup->count();
                $terisi = $grup->where('status', 'Terisi')->count();

                return [
                    'terminal'      => $terminal,
                    'lantai'        => $lantai,
                    'jenis_ruangan' => $jenis,
                    'total_ruangan' => $total,
                    'terisi'        => $terisi,
                    'kosong'        => $total - $terisi,
                    'okupansi'      => $total > 0 ? round($terisi / $total * 100, 1) : 0,
                    'luas_total'    => round((float) $grup->sum('luas_area'), 2),
                    'luas_terisi'   => round(
                        (float) $grup->where('status', 'Terisi')->sum('luas_area'),
                        2
                    ),
                ];
            })
            ->values();
    }

    private function transformAktivasi(Collection $aktivasi): Collection
    {
        return $aktivasi
            ->groupBy(fn (Aktivasi $a) => implode('|', [
                $a->tanggal_pengajuan ? $a->tanggal_pengajuan->format('Y-m') : '-',
                $this->bersihkan($a->status),
            ]))
            ->map(function (Collection $grup, string $kunci) {
                [$periode, $status] = explode('|', $kunci);

                // Lama proses: hari dari pengajuan sampai keputusan.
                $lama = $grup
                    ->filter(fn (Aktivasi $a) => $a->tanggal_pengajuan && $a->tanggal_keputusan)
                    ->map(fn (Aktivasi $a) => (float) $a->tanggal_pengajuan
                        ->diffInDays($a->tanggal_keputusan, true));

                return [
                    'periode'             => $periode,
                    'status'              => $status,
                    'jumlah'              => $grup->count(),
                    'rata_hari_keputusan' => $lama->isNotEmpty() ? round($lama->avg(), 1) : null,
                ];
            })
            ->values();
    }

    // ------------------------------------------------------------------
    // LOAD
    // ------------------------------------------------------------------

    private function muat(Collection $okupansi, Collection $aktivasi): void
    {
        $tanggal = now()->toDateString();
        $waktu = now();

        DB::transaction(function () use ($okupansi, $aktivasi, $tanggal, $waktu) {
            DB::table('ringkasan_okupansi')->where('tanggal_snapshot', $tanggal)->delete();
            DB::table('ringkasan_aktivasi')->where('tanggal_snapshot', $tanggal)->delete();

            foreach ($okupansi->chunk(200) as $potongan) {
                DB::table('ringkasan_okupansi')->insert(
                    $potongan->map(fn ($r) => $r + [
                        'tanggal_snapshot' => $tanggal,
                        'created_at'       => $waktu,
                        'updated_at'       => $waktu,
                    ])->all()
                );
            }

            foreach ($aktivasi->chunk(200) as $potongan) {
                DB::table('ringkasan_aktivasi')->insert(
                    $potongan->map(fn ($r) => $r + [
                        'tanggal_snapshot' => $tanggal,
                        'created_at'       => $waktu,
                        'updated_at'       => $waktu,
                    ])->all()
                );
            }
        });
    }
}
