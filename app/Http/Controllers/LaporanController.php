<?php

namespace App\Http\Controllers;

use App\Models\Aktivasi;
use App\Models\AktivasiRiwayat;
use App\Models\KerjaSama;
use App\Models\Kontrak;
use App\Models\PendapatanTagihan;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $tahun = (int) $request->input('tahun', now()->year);

        if ($tahun < 2000 || $tahun > 2100) {
            $tahun = now()->year;
        }

        $jenisUsaha = $request->input('jenis_usaha');
        $terminal = $request->input('terminal');
        $statusFilter = $request->input('status');
        $dokumenFilter = $request->input('dokumen');
        $urutan = $request->input('urutan', 'status');

        /*
        |--------------------------------------------------------------------------
        | DATA KERJA SAMA
        |--------------------------------------------------------------------------
        */

        $kerjaSamas = KerjaSama::with([
    'tenant',
    'lokasi',
    'kontrakTerbaru',
    'aktivasis',
    'aktivasi',
    'progres',
])
->whereDate('tanggal_mulai', '<=', $tahun . '-12-31')
->whereDate('tanggal_berakhir', '>=', $tahun . '-01-01')
->get();

        /*
        |--------------------------------------------------------------------------
        | PEMBENTUKAN DATA LAPORAN
        |--------------------------------------------------------------------------
        */

        $laporan = $kerjaSamas->map(function ($ks) {

            $kontrak = $ks->kontrakTerbaru;

            if (!$kontrak) {
                $statusLaporan = 'Proses';
            } elseif ($kontrak->status === 'Aktif') {
                $statusLaporan = 'Aktif';
            } elseif ($kontrak->status === 'Nonaktif') {
                $statusLaporan = 'Nonaktif';
            } else {
                $statusLaporan = 'Proses';
            }

            $nomorKontrakMitra = $kontrak->nomor_kontrak_mitra ?? null;

            $baFinal = $ks->berita_acara_final_path ?? null;

            $baLengkap = filled($baFinal);

            $kontrakLengkap = filled($nomorKontrakMitra);

            $dokumenLengkap = $baLengkap && $kontrakLengkap;

            return [
                'kerja_sama' => $ks,
                'tenant' => $ks->tenant,
                'lokasi' => $ks->lokasi,
                'kontrak' => $kontrak,
                'status_laporan' => $statusLaporan,
                'dokumen_lengkap' => $dokumenLengkap,
            ];
        });

        /*
        |--------------------------------------------------------------------------
        | FILTER JENIS USAHA
        |--------------------------------------------------------------------------
        */

        if ($jenisUsaha) {
            $laporan = $laporan->filter(function ($item) use ($jenisUsaha) {
                return ($item['kerja_sama']->jenis_usaha ?? null) === $jenisUsaha;
            });
        }

        /*
        |--------------------------------------------------------------------------
        | FILTER TERMINAL
        |--------------------------------------------------------------------------
        */

        if ($terminal) {
            $laporan = $laporan->filter(function ($item) use ($terminal) {
                return ($item['lokasi']->terminal ?? null) === $terminal;
            });
        }

        /*
        |--------------------------------------------------------------------------
        | FILTER STATUS KONTRAK
        |--------------------------------------------------------------------------
        */

        if ($statusFilter) {
            $laporan = $laporan->filter(function ($item) use ($statusFilter) {
                return $item['status_laporan'] === $statusFilter;
            });
        }

        /*
        |--------------------------------------------------------------------------
        | FILTER KELENGKAPAN DOKUMEN
        |--------------------------------------------------------------------------
        */

        if ($dokumenFilter === 'Lengkap') {
            $laporan = $laporan->where('dokumen_lengkap', true);
        } elseif ($dokumenFilter === 'Belum Lengkap') {
            $laporan = $laporan->where('dokumen_lengkap', false);
        }

        /*
        |--------------------------------------------------------------------------
        | PENGURUTAN DATA
        |--------------------------------------------------------------------------
        */

        $prioritasStatus = [
            'Proses' => 1,
            'Aktif' => 2,
            'Nonaktif' => 3,
        ];

        if ($urutan === 'terbaru') {
            $laporan = $laporan->sortByDesc(function ($item) {
                return $item['kerja_sama']->tanggal_pengajuan;
            });
        } elseif ($urutan === 'terlama') {
            $laporan = $laporan->sortBy(function ($item) {
                return $item['kerja_sama']->tanggal_pengajuan;
            });
        } elseif ($urutan === 'az') {
            $laporan = $laporan->sortBy(function ($item) {
                return strtolower($item['kerja_sama']->mitra_usaha ?? '');
            });
        } elseif ($urutan === 'za') {
            $laporan = $laporan->sortByDesc(function ($item) {
                return strtolower($item['kerja_sama']->mitra_usaha ?? '');
            });
        } else {
            $laporan = $laporan->sortBy(function ($item) use ($prioritasStatus) {
                return $prioritasStatus[$item['status_laporan']] ?? 99;
            });
        }

        $laporan = $laporan->values();

        /*
|--------------------------------------------------------------------------
| RINGKASAN KONTRAK BERDASARKAN TAHUN
|--------------------------------------------------------------------------
*/

$kontrakPeriode = Kontrak::with([
    'kerjaSama.lokasi',
])
->whereHas('kerjaSama', function ($query) use ($tahun) {
    $query
        ->whereDate('tanggal_mulai', '<=', $tahun . '-12-31')
        ->whereDate('tanggal_berakhir', '>=', $tahun . '-01-01');
})
->get();

$totalKontrak = $kontrakPeriode->count();

$kontrakAktif = $kontrakPeriode
    ->where('status', 'Aktif')
    ->count();

$kontrakNonaktif = $kontrakPeriode
    ->where('status', 'Nonaktif')
    ->count();

/*
|--------------------------------------------------------------------------
| REKAPITULASI PERIODE KONTRAK
|--------------------------------------------------------------------------
*/

// Kontrak dengan keputusan tidak memperpanjang (Lintas).
$kontrakLintas = $kontrakPeriode
    ->where('status_perpanjangan', 'Tidak Memperpanjang')
    ->count();

// Kontrak dengan keputusan ingin memperpanjang.
$kontrakPerpanjangan = $kontrakPeriode
    ->where('status_perpanjangan', 'Ingin Memperpanjang')
    ->count();

        /*
        |--------------------------------------------------------------------------
        | RINGKASAN TAGIHAN
        |--------------------------------------------------------------------------
        */

        $tagihan = PendapatanTagihan::where('tahun', $tahun);

        $totalTagihan = (clone $tagihan)->sum('jumlah_tagihan');

        $invoiceDiberikan = (clone $tagihan)
            ->where('status_invoice', true)
            ->sum('jumlah_tagihan');

        $pendingInvoice = (clone $tagihan)
            ->where('status_invoice', false)
            ->sum('jumlah_tagihan');

        /*
        |--------------------------------------------------------------------------
        | PROGRESS AKTIVASI
        |--------------------------------------------------------------------------
        */

        $semuaKerjaSama = $kerjaSamas;

        $totalKerjaSama = $semuaKerjaSama->count();

        $totalSistemAktif = $semuaKerjaSama->filter(function ($ks) {
            return $ks->aktivasi?->status === 'Disetujui';
        })->count();

        $progressAktivasi = $totalKerjaSama > 0
            ? round(($totalSistemAktif / $totalKerjaSama) * 100, 1)
            : 0;

        /*
        |--------------------------------------------------------------------------
        | PROGRESS AKTIVASI PER TERMINAL
        |--------------------------------------------------------------------------
        */

        $progressTerminal = [];

        foreach (['T1', 'T2'] as $namaTerminal) {
            $dataTerminal = $semuaKerjaSama->filter(function ($ks) use ($namaTerminal) {
                return $ks->lokasi?->terminal === $namaTerminal;
            });

            $totalTerminal = $dataTerminal->count();

            $aktifTerminal = $dataTerminal->filter(function ($ks) {
                return $ks->aktivasi?->status === 'Disetujui';
            })->count();

            $progressTerminal[$namaTerminal] = [
                'total' => $totalTerminal,
                'aktif' => $aktifTerminal,
                'persentase' => $totalTerminal > 0
                    ? round(($aktifTerminal / $totalTerminal) * 100, 1)
                    : 0,
            ];
        }

        $totalKontrak = $kontrakPeriode->count();

$kontrakAktif = $kontrakPeriode
    ->where('status', 'Aktif')
    ->count();

$kontrakNonaktif = $kontrakPeriode
    ->where('status', 'Nonaktif')
    ->count();

$kontrakProses = max(
    0,
    $totalKontrak - $kontrakAktif - $kontrakNonaktif
);
        /*
        |--------------------------------------------------------------------------
        | KOMPOSISI STATUS KONTRAK
        |--------------------------------------------------------------------------
        */

        $komposisiStatus = [
            'Aktif' => $kontrakAktif,
            'Nonaktif' => $kontrakNonaktif,
            'Proses' => $kontrakProses,
        ];

        /*
|--------------------------------------------------------------------------
| ANALISIS JENIS USAHA BERDASARKAN KONTRAK
|--------------------------------------------------------------------------
*/

$komposisiJenisUsaha = $kontrakPeriode
    ->groupBy(function ($kontrak) {
        return $kontrak->kerjaSama?->jenis_usaha ?: 'Lainnya';
    })
    ->map(function ($items) {
        return $items->count();
    })
    ->sortDesc();

        /*
|--------------------------------------------------------------------------
| ANALISIS KONTRAK PER TERMINAL
|--------------------------------------------------------------------------
*/

$kontrakPerTerminal = $kontrakPeriode
    ->groupBy(function ($kontrak) {
        return $kontrak->kerjaSama?->lokasi?->terminal
            ?: 'Tidak Diketahui';
    })
    ->map(function ($items) {
        return $items->count();
    })
    ->sortKeys();

        /*
        |--------------------------------------------------------------------------
        | KELENGKAPAN DOKUMEN
        |--------------------------------------------------------------------------
        */

        /*
|--------------------------------------------------------------------------
| KELENGKAPAN DOKUMEN KONTRAK
|--------------------------------------------------------------------------
*/

$totalDokumenKontrak = $kontrakPeriode->count();

$dokumenKontrakLengkap = $kontrakPeriode
    ->filter(function ($kontrak) {
        return filled($kontrak->nomor_kontrak_mitra);
    })
    ->count();

$dokumenKontrakBelumLengkap = max(
    0,
    $totalDokumenKontrak - $dokumenKontrakLengkap
);


/*
|--------------------------------------------------------------------------
| KELENGKAPAN BERITA ACARA FINAL
|--------------------------------------------------------------------------
*/

$kerjaSamaDisetujui = $kerjaSamas
    ->where('status', 'Disetujui');

$totalBA = $kerjaSamaDisetujui->count();

$baLengkap = $kerjaSamaDisetujui
    ->filter(function ($ks) {
        return filled($ks->berita_acara_final_path);
    })
    ->count();

$baBelumLengkap = max(
    0,
    $totalBA - $baLengkap
);
        /*
        |--------------------------------------------------------------------------
        | DATA UNTUK VIEW
        |--------------------------------------------------------------------------
        */

        return view('laporan.index', compact(
            'tahun',
            'laporan',
            'totalKontrak',
            'kontrakAktif',
            'kontrakNonaktif',
            'kontrakProses',
            'totalTagihan',
            'invoiceDiberikan',
            'pendingInvoice',
            'totalKerjaSama',
            'totalSistemAktif',
            'progressAktivasi',
            'progressTerminal',
            'komposisiStatus',
            'komposisiJenisUsaha',
            'kontrakPerTerminal',
            'kontrakLintas',
            'kontrakPerpanjangan',
            'totalDokumenKontrak',
            'dokumenKontrakLengkap',
            'dokumenKontrakBelumLengkap',
            'totalBA',
            'baLengkap',
            'baBelumLengkap',
            'jenisUsaha',
            'terminal',
            'statusFilter',
            'dokumenFilter',
            'urutan'
        ));
    }

    public function show(KerjaSama $kerjaSama)
    {
        $kerjaSama->load([
            'tenant',
            'lokasi',
            'kontrakTerbaru',
            'aktivasis',
            'progres',
            'aktivasiRiwayats.user',
        ]);

        $tagihan = PendapatanTagihan::with('tenant')
            ->where('kerja_sama_id', $kerjaSama->id)
            ->orderByDesc('tahun')
            ->orderByDesc('bulan')
            ->get();

        return view('laporan.detail', compact(
            'kerjaSama',
            'tagihan'
        ));
    }
}
