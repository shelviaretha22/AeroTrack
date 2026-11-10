<?php

namespace App\Http\Controllers;

use App\Models\Aktivasi;
use App\Models\AktivasiRiwayat;
use App\Models\KerjaSama;
use App\Models\Kontrak;
use App\Models\PendapatanTagihan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanExport;

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
            $statusLaporan = 'Belum Dibuat';
        } else {
            $adaPdfFinal = filled($kontrak->final_pdf_path)
                && \Illuminate\Support\Facades\Storage::disk('local')
                    ->exists($kontrak->final_pdf_path);

            if (!$adaPdfFinal) {
                $statusLaporan = 'Draft';
            } else {
                $tanggalAkhir = $ks->tanggal_berakhir;

                $isExpired = $tanggalAkhir
                    && Carbon::parse($tanggalAkhir)
                        ->startOfDay()
                        ->lt(now()->startOfDay());

                $statusLaporan = $isExpired ? 'Nonaktif' : 'Aktif';
            }
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
            'Belum Dibuat' => 1,
            'Draft' => 2,
            'Aktif' => 3,
            'Nonaktif' => 4,
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

    public function drilldown(Request $request)
{
    $tahun = (int) $request->query('tahun', now()->year);

    if ($tahun < 2000 || $tahun > 2100) {
        $tahun = now()->year;
    }

    $type = $request->query('type');
    $value = $request->query('value');

    $judul = '';
    $deskripsi = '';
    $data = collect();

    // Data kerja sama pada periode yang dipilih.
    $kerjaSamas = KerjaSama::with([
        'tenant',
        'lokasi',
        'kontrakTerbaru',
        'aktivasi',
    ])
        ->whereDate('tanggal_mulai', '<=', $tahun . '-12-31')
        ->whereDate('tanggal_berakhir', '>=', $tahun . '-01-01')
        ->get();

    // Data kontrak pada periode yang dipilih.
    $kontrakPeriode = Kontrak::with([
        'kerjaSama.tenant',
        'kerjaSama.lokasi',
    ])
        ->whereHas('kerjaSama', function ($query) use ($tahun) {
            $query
                ->whereDate('tanggal_mulai', '<=', $tahun . '-12-31')
                ->whereDate('tanggal_berakhir', '>=', $tahun . '-01-01');
        })
        ->get();

    /*
    |--------------------------------------------------------------------------
    | FORMAT DATA UNTUK TABEL
    |--------------------------------------------------------------------------
    */

    $buatBaris = function ($kerjaSama, $kontrak = null, $status = null, $keterangan = null) {
        $tenant = $kerjaSama->tenant;
        $lokasi = $kerjaSama->lokasi;

        return [
            'id' => $kerjaSama->id,
            'nomor_kerja_sama' => $kerjaSama->nomor_kerja_sama ?? '-',
            'mitra_usaha' => $kerjaSama->mitra_usaha ?? '-',
            'brand' => $tenant?->brand ?? '-',
            'nama_tenant' => $tenant?->nama_tenant ?? '-',
            'jenis_usaha' => $kerjaSama->jenis_usaha ?? '-',
            'terminal' => $lokasi?->terminal ?? '-',
            'kode_ruang' => $lokasi?->kode_ruang ?? '-',
            'nomor_kontrak' => $kontrak?->nomor_kontrak_mitra ?? '-',
            'status' => $status ?? $kontrak?->status ?? '-',
            'keterangan' => $keterangan ?? '-',
            'url' => route('laporan.detail', $kerjaSama->id),
        ];
    };

    /*
    |--------------------------------------------------------------------------
    | JENIS DRILL-DOWN
    |--------------------------------------------------------------------------
    */

    switch ($type) {

        case 'kontrak':
            $judul = 'Seluruh Kontrak';
            $deskripsi = 'Daftar kontrak kerja sama pada tahun ' . $tahun . '.';

            $data = $kontrakPeriode->map(function ($kontrak) use ($buatBaris) {
                return $buatBaris(
                    $kontrak->kerjaSama,
                    $kontrak,
                    $kontrak->status
                );
            });
            break;

        case 'status':
            $judul = 'Kontrak ' . $value;
            $deskripsi = 'Daftar kontrak berdasarkan status ' . $value . ' pada tahun ' . $tahun . '.';

            if (!in_array($value, ['Aktif', 'Nonaktif', 'Proses'], true)) {
                abort(404);
            }

            $filtered = $kontrakPeriode->filter(function ($kontrak) use ($value) {
                if ($value === 'Proses') {
                    return !in_array($kontrak->status, ['Aktif', 'Nonaktif'], true);
                }

                return $kontrak->status === $value;
            });

            $data = $filtered->map(function ($kontrak) use ($buatBaris) {
                return $buatBaris(
                    $kontrak->kerjaSama,
                    $kontrak,
                    $kontrak->status
                );
            });
            break;

        case 'terminal':
            if (!in_array($value, ['T1', 'T2'], true)) {
                abort(404);
            }

            $judul = 'Kontrak Terminal ' . $value;
            $deskripsi = 'Daftar kontrak yang berada di Terminal ' . $value . '.';

            $data = $kontrakPeriode
                ->filter(function ($kontrak) use ($value) {
                    return $kontrak->kerjaSama?->lokasi?->terminal === $value;
                })
                ->map(function ($kontrak) use ($buatBaris) {
                    return $buatBaris(
                        $kontrak->kerjaSama,
                        $kontrak,
                        $kontrak->status
                    );
                });
            break;

        case 'aktivasi':
            if (!in_array($value, ['aktif', 'belum_aktif'], true)) {
                abort(404);
            }

            $judul = $value === 'aktif'
                ? 'Kerja Sama Sudah Aktif'
                : 'Kerja Sama Belum Aktif';

            $deskripsi = 'Daftar kerja sama berdasarkan status aktivasi sistem pada tahun ' . $tahun . '.';

            $filtered = $kerjaSamas->filter(function ($ks) use ($value) {
                $sudahAktif = $ks->aktivasi?->status === 'Disetujui';

                return $value === 'aktif'
                    ? $sudahAktif
                    : !$sudahAktif;
            });

            $data = $filtered->map(function ($ks) use ($buatBaris) {
                return $buatBaris(
                    $ks,
                    $ks->kontrakTerbaru,
                    $ks->aktivasi?->status ?? 'Belum Aktif'
                );
            });
            break;

        case 'aktivasi_terminal':
            if (!in_array($value, ['T1', 'T2'], true)) {
                abort(404);
            }

            $judul = 'Aktivasi Terminal ' . $value;
            $deskripsi = 'Daftar kerja sama pada Terminal ' . $value . ' beserta status aktivasi sistemnya.';

            $data = $kerjaSamas
                ->filter(function ($ks) use ($value) {
                    return $ks->lokasi?->terminal === $value;
                })
                ->map(function ($ks) use ($buatBaris) {
                    return $buatBaris(
                        $ks,
                        $ks->kontrakTerbaru,
                        $ks->aktivasi?->status ?? 'Belum Aktif'
                    );
                });
            break;

        case 'kategori':
            $kategori = [
                'F&B' => ['F&B', 'Food & Beverage'],
                'Food & Beverage' => ['F&B', 'Food & Beverage'],

                'Services' => ['Service', 'Services'],
                'Service' => ['Service', 'Services'],

                'Retail' => ['Retail'],
                'Lounge' => ['Lounge'],
                'Duty Free' => ['Duty Free'],
                'UMKM' => ['UMKM'],
            ];

            if (!isset($kategori[$value])) {
                abort(404);
            }

            $judul = 'Kategori Tenant ' . $value;
            $deskripsi = 'Daftar kerja sama dalam kategori tenant ' . $value . '.';

            $data = $kerjaSamas
                ->filter(function ($ks) use ($kategori, $value) {
                    return in_array($ks->jenis_usaha, $kategori[$value], true);
                })
                ->map(function ($ks) use ($buatBaris) {
                    return $buatBaris(
                        $ks,
                        $ks->kontrakTerbaru,
                        $ks->kontrakTerbaru?->status
                    );
                });
            break;

        case 'perpanjangan':
            if (!in_array($value, ['lintas', 'perpanjangan'], true)) {
                abort(404);
            }

            $statusPerpanjangan = $value === 'lintas'
                ? 'Tidak Memperpanjang'
                : 'Ingin Memperpanjang';

            $judul = $value === 'lintas'
                ? 'Kontrak Tidak Memperpanjang'
                : 'Kontrak Ingin Memperpanjang';

            $deskripsi = 'Daftar kontrak berdasarkan keputusan perpanjangan.';

            $data = $kontrakPeriode
                ->where('status_perpanjangan', $statusPerpanjangan)
                ->map(function ($kontrak) use ($buatBaris) {
                    return $buatBaris(
                        $kontrak->kerjaSama,
                        $kontrak,
                        $kontrak->status,
                        $kontrak->status_perpanjangan
                    );
                });
            break;

        case 'dokumen_kontrak':
            if (!in_array($value, ['lengkap', 'belum_lengkap'], true)) {
                abort(404);
            }

            $judul = $value === 'lengkap'
                ? 'Dokumen Kontrak Lengkap'
                : 'Dokumen Kontrak Belum Lengkap';

            $deskripsi = 'Daftar kontrak berdasarkan kelengkapan nomor kontrak mitra.';

            $data = $kontrakPeriode
                ->filter(function ($kontrak) use ($value) {
                    $lengkap = filled($kontrak->nomor_kontrak_mitra);

                    return $value === 'lengkap'
                        ? $lengkap
                        : !$lengkap;
                })
                ->map(function ($kontrak) use ($buatBaris) {
                    return $buatBaris(
                        $kontrak->kerjaSama,
                        $kontrak,
                        $kontrak->status,
                        filled($kontrak->nomor_kontrak_mitra)
                            ? 'Lengkap'
                            : 'Belum Lengkap'
                    );
                });
            break;

        case 'berita_acara':
            if (!in_array($value, ['lengkap', 'belum_lengkap'], true)) {
                abort(404);
            }

            $judul = $value === 'lengkap'
                ? 'Berita Acara Final Lengkap'
                : 'Berita Acara Final Belum Lengkap';

            $deskripsi = 'Daftar kerja sama yang telah disetujui berdasarkan kelengkapan berita acara final.';

            $data = $kerjaSamas
                ->where('status', 'Disetujui')
                ->filter(function ($ks) use ($value) {
                    $lengkap = filled($ks->berita_acara_final_path);

                    return $value === 'lengkap'
                        ? $lengkap
                        : !$lengkap;
                })
                ->map(function ($ks) use ($buatBaris) {
                    return $buatBaris(
                        $ks,
                        $ks->kontrakTerbaru,
                        $ks->aktivasi?->status ?? '-',
                        filled($ks->berita_acara_final_path)
                            ? 'Lengkap'
                            : 'Belum Lengkap'
                    );
                });
            break;

        default:
            abort(404);
    }

    return view('laporan.drilldown', [
        'tahun' => $tahun,
        'judul' => $judul,
        'deskripsi' => $deskripsi,
        'data' => $data->values(),
    ]);
}

public function exportPdf(Request $request)
{
    // Gunakan perhitungan dan filter yang sama dengan halaman utama.
    $view = $this->index($request);
    $data = $view->getData();

    $pdf = Pdf::loadView('laporan.pdf', $data)
        ->setPaper('a4', 'landscape');

    return $pdf->download(
        'Laporan-AeroTrack-' . $data['tahun'] . '.pdf'
    );
}

public function exportExcel(Request $request)
{
    // Gunakan data laporan yang sama dengan halaman utama.
    $view = $this->index($request);
    $data = $view->getData();

    return Excel::download(
        new LaporanExport($data),
        'Laporan-AeroTrack-' . $data['tahun'] . '.xlsx'
    );
}
}

