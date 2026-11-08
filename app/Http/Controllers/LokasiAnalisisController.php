<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Lokasi;
use App\Services\EtlLokasiAktivasi;
use App\Services\XlsxSederhana;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Analisis tambahan modul Lokasi dan Aktivasi:
 *  - Analisis what-if okupansi (dengan drilldown Terminal → Lantai → Jenis Ruangan)
 *  - Laporan Lokasi dan Aktivasi yang dibaca dari tabel hasil ETL
 *    (tampil di web, PDF, dan Excel)
 *  - Tombol menjalankan ETL
 */
class LokasiAnalisisController extends Controller
{
    private const URUTAN_STATUS = [
        'Belum Diajukan', 'Menunggu Keputusan', 'Disetujui', 'Ditolak',
    ];

    // ==================================================================
    // WHAT-IF
    // ==================================================================

    public function whatif(Request $request)
    {
        $input = $request->validate([
            'mode'  => ['nullable', 'in:naik,turun,target'],
            'nilai' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        $mode = $input['mode'] ?? 'naik';
        $nilai = (float) ($input['nilai'] ?? 10);

        // Nilai kosong/NULL di database diperlakukan sebagai "-" (sama seperti drilldown).
        $kosong = '-';
        $ekspresi = fn (string $kolom) => "COALESCE(NULLIF({$kolom}, ''), '{$kosong}')";

        $filter = [
            'terminal'      => trim((string) $request->query('terminal', '')),
            'lantai'        => trim((string) $request->query('lantai', '')),
            'jenis_ruangan' => trim((string) $request->query('jenis_ruangan', '')),
        ];

        $dasar = Lokasi::query();

        foreach ($filter as $kolom => $isi) {
            if ($isi !== '') {
                $dasar->whereRaw($ekspresi($kolom) . ' = ?', [$isi]);
            }
        }

        // Level kelompok mengikuti filter yang sudah dipilih (drilldown).
        $level = match (true) {
            $filter['terminal'] === '' => ['kunci' => 'terminal', 'judul' => 'Terminal'],
            $filter['lantai'] === ''   => ['kunci' => 'lantai', 'judul' => 'Lantai'],
            default                    => ['kunci' => 'jenis_ruangan', 'judul' => 'Jenis Ruangan'],
        };

        $kolomGrup = $ekspresi($level['kunci']);

        $baris = (clone $dasar)
            ->select(
                DB::raw("{$kolomGrup} as nilai"),
                DB::raw('COUNT(*) as total'),
                DB::raw("SUM(CASE WHEN status = 'Terisi' THEN 1 ELSE 0 END) as terisi"),
                DB::raw('COALESCE(SUM(luas_area), 0) as luas')
            )
            ->groupBy(DB::raw($kolomGrup))
            ->get()
            ->sortBy(fn ($r) => (string) $r->nilai, SORT_NATURAL)
            ->values()
            ->map(function ($r) use ($mode, $nilai, $kosong) {
                $total = (int) $r->total;
                $terisi = (int) $r->terisi;
                $okSekarang = $total > 0 ? $terisi / $total * 100 : 0;

                $okSkenario = match ($mode) {
                    'naik'  => min(100, $okSekarang + $nilai),
                    'turun' => max(0, $okSekarang - $nilai),
                    default => $nilai, // target
                };

                $terisiSkenario = (int) round($total * $okSkenario / 100);
                $luas = (float) $r->luas;

                return [
                    'nilai'            => (string) $r->nilai,
                    'nama'             => (string) $r->nilai === $kosong ? 'Belum diisi' : (string) $r->nilai,
                    'total'            => $total,
                    'terisi'           => $terisi,
                    'ok_sekarang'      => round($okSekarang, 1),
                    'terisi_skenario'  => $terisiSkenario,
                    'ok_skenario'      => $total > 0 ? round($terisiSkenario / $total * 100, 1) : 0,
                    'selisih'          => $terisiSkenario - $terisi,
                    'luas'             => $luas,
                    // Estimasi: luas terisi dianggap sebanding dengan jumlah ruangan terisi.
                    'luas_terisi_skenario' => $total > 0 ? round($luas * $terisiSkenario / $total, 2) : 0,
                ];
            });

        $total = $baris->sum('total');
        $terisi = $baris->sum('terisi');
        $terisiSkenario = $baris->sum('terisi_skenario');

        $ringkasan = [
            'total'           => $total,
            'terisi'          => $terisi,
            'ok_sekarang'     => $total > 0 ? round($terisi / $total * 100, 1) : 0,
            'terisi_skenario' => $terisiSkenario,
            'ok_skenario'     => $total > 0 ? round($terisiSkenario / $total * 100, 1) : 0,
            'selisih'         => $terisiSkenario - $terisi,
            'luas'            => round($baris->sum('luas'), 2),
            'luas_skenario'   => round($baris->sum('luas_terisi_skenario'), 2),
        ];

        return view('lokasi.whatif', [
            'mode'       => $mode,
            'nilai'      => $nilai,
            'filter'     => array_filter($filter, fn ($v) => $v !== ''),
            'level'      => $level,
            'baris'      => $baris,
            'ringkasan'  => $ringkasan,
            'dalamSendiri' => $level['kunci'] === 'jenis_ruangan',
        ]);
    }

    // ==================================================================
    // LAPORAN (dibaca dari tabel hasil ETL)
    // ==================================================================

    public function laporan(Request $request)
    {
        $terminal = $this->terminalDipilih($request);

        return view('lokasi.laporan', [
            'terminal' => $terminal,
            'data'     => $this->dataLaporan($terminal),
        ]);
    }

    public function laporanPdf(Request $request)
    {
        $terminal = $this->terminalDipilih($request);
        $data = $this->dataLaporan($terminal);

        if ($data['tanggal_okupansi'] === null && $data['tanggal_aktivasi'] === null) {
            return back()->with('error', 'Belum ada data laporan. Jalankan ETL terlebih dahulu.');
        }

        AuditLog::catat('Lokasi', 'Ekspor laporan PDF', 'Laporan Lokasi dan Aktivasi.');

        return Pdf::loadView('lokasi.laporan-pdf', [
            'terminal' => $terminal,
            'data'     => $data,
            'dicetak'  => now(),
        ])->setPaper('a4', 'portrait')
            ->download('laporan-lokasi-aktivasi-' . now()->format('Ymd') . '.pdf');
    }

    public function laporanExcel(Request $request)
    {
        $terminal = $this->terminalDipilih($request);
        $data = $this->dataLaporan($terminal);

        if ($data['tanggal_okupansi'] === null && $data['tanggal_aktivasi'] === null) {
            return back()->with('error', 'Belum ada data laporan. Jalankan ETL terlebih dahulu.');
        }

        $sheetTerminal = [['Terminal', 'Total Ruangan', 'Terisi', 'Kosong', 'Okupansi (%)', 'Luas Total (m2)', 'Luas Terisi (m2)']];
        foreach ($data['per_terminal'] as $r) {
            $sheetTerminal[] = [
                $r['nama'], $r['total'], $r['terisi'], $r['kosong'],
                $r['okupansi'], $r['luas_total'], $r['luas_terisi'],
            ];
        }

        $sheetOkupansi = [['Terminal', 'Lantai', 'Jenis Ruangan', 'Total Ruangan', 'Terisi', 'Kosong', 'Okupansi (%)', 'Luas Total (m2)', 'Luas Terisi (m2)']];
        foreach ($data['okupansi'] as $r) {
            $sheetOkupansi[] = [
                $r->terminal, $r->lantai, $r->jenis_ruangan,
                (int) $r->total_ruangan, (int) $r->terisi, (int) $r->kosong,
                (float) $r->okupansi, (float) $r->luas_total, (float) $r->luas_terisi,
            ];
        }

        $sheetAktivasi = [['Periode Pengajuan', 'Status', 'Jumlah', 'Rata-rata Hari ke Keputusan']];
        foreach ($data['aktivasi'] as $r) {
            $sheetAktivasi[] = [
                $r->periode, $r->status, (int) $r->jumlah,
                $r->rata_hari_keputusan !== null ? (float) $r->rata_hari_keputusan : null,
            ];
        }

        try {
            $path = XlsxSederhana::buat([
                'Ringkasan Terminal' => $sheetTerminal,
                'Okupansi Detail'    => $sheetOkupansi,
                'Aktivasi'           => $sheetAktivasi,
            ]);
        } catch (Throwable $e) {
            report($e);

            return back()->with('error', 'Ekspor Excel gagal: ' . $e->getMessage());
        }

        AuditLog::catat('Lokasi', 'Ekspor laporan Excel', 'Laporan Lokasi dan Aktivasi.');

        return response()->download(
            $path,
            'laporan-lokasi-aktivasi-' . now()->format('Ymd') . '.xlsx',
            ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
        )->deleteFileAfterSend(true);
    }

    public function jalankanEtl(EtlLokasiAktivasi $etl)
    {
        try {
            $hasil = $etl->jalankan('manual');
        } catch (Throwable $e) {
            report($e);
            AuditLog::catat('Lokasi', 'ETL gagal', mb_substr($e->getMessage(), 0, 500), null, 'peringatan');

            return back()->with('error', 'ETL gagal dijalankan. Periksa catatan ETL di bawah.');
        }

        AuditLog::catat(
            'Lokasi',
            'ETL dijalankan',
            'Okupansi ' . $hasil['okupansi'] . ' baris, aktivasi ' . $hasil['aktivasi'] . ' baris.'
        );

        return back()->with('success', 'ETL selesai. Ringkasan okupansi dan aktivasi sudah diperbarui.');
    }

    // ------------------------------------------------------------------

    private function terminalDipilih(Request $request): ?string
    {
        $terminal = trim((string) $request->query('terminal', ''));

        return in_array($terminal, ['T1', 'T2'], true) ? $terminal : null;
    }

    /**
     * Susun semua angka laporan dari tabel ringkasan (snapshot terbaru).
     * Dipakai bersama oleh tampilan web, PDF, dan Excel.
     */
    private function dataLaporan(?string $terminal): array
    {
        $tanggalOkupansi = DB::table('ringkasan_okupansi')->max('tanggal_snapshot');
        $tanggalAktivasi = DB::table('ringkasan_aktivasi')->max('tanggal_snapshot');

        $okupansi = collect();

        if ($tanggalOkupansi) {
            $okupansi = DB::table('ringkasan_okupansi')
                ->where('tanggal_snapshot', $tanggalOkupansi)
                ->when($terminal, fn ($q) => $q->where('terminal', $terminal))
                ->orderBy('terminal')->orderBy('lantai')->orderBy('jenis_ruangan')
                ->get();
        }

        $aktivasi = collect();

        if ($tanggalAktivasi) {
            $aktivasi = DB::table('ringkasan_aktivasi')
                ->where('tanggal_snapshot', $tanggalAktivasi)
                ->orderByDesc('periode')
                ->get()
                ->sortBy(function ($r) {
                    $i = array_search($r->status, self::URUTAN_STATUS, true);

                    return [-(int) str_replace('-', '', (string) $r->periode), $i === false ? 99 : $i];
                })
                ->values();
        }

        $perTerminal = $okupansi
            ->groupBy('terminal')
            ->map(fn (Collection $g, $nama) => $this->hitung((string) $nama, $g))
            ->values();

        $perLantai = $okupansi
            ->groupBy(fn ($r) => $r->terminal . ' / Lantai ' . $r->lantai)
            ->map(fn (Collection $g, $nama) => $this->hitung((string) $nama, $g))
            ->values();

        $perStatus = $aktivasi
            ->groupBy('status')
            ->map(fn (Collection $g, $nama) => [
                'nama'   => (string) $nama,
                'jumlah' => (int) $g->sum('jumlah'),
            ])
            ->sortBy(function ($r) {
                $i = array_search($r['nama'], self::URUTAN_STATUS, true);

                return $i === false ? 99 : $i;
            })
            ->values();

        $totalAktivasi = (int) $perStatus->sum('jumlah');
        $disetujui = (int) ($perStatus->firstWhere('nama', 'Disetujui')['jumlah'] ?? 0);
        $keputusan = $disetujui + (int) ($perStatus->firstWhere('nama', 'Ditolak')['jumlah'] ?? 0);

        $totalRuang = (int) $okupansi->sum('total_ruangan');
        $totalTerisi = (int) $okupansi->sum('terisi');

        $run = DB::table('etl_runs')
            ->where('nama', EtlLokasiAktivasi::NAMA)
            ->orderByDesc('id')
            ->first();

        return [
            'tanggal_okupansi' => $tanggalOkupansi,
            'tanggal_aktivasi' => $tanggalAktivasi,
            'okupansi'         => $okupansi,
            'aktivasi'         => $aktivasi,
            'per_terminal'     => $perTerminal,
            'per_lantai'       => $perLantai,
            'per_status'       => $perStatus,
            'kpi' => [
                'total_ruangan'  => $totalRuang,
                'terisi'         => $totalTerisi,
                'okupansi'       => $totalRuang > 0 ? round($totalTerisi / $totalRuang * 100, 1) : 0,
                'total_aktivasi' => $totalAktivasi,
                'disetujui'      => $disetujui,
                'persen_setuju'  => $keputusan > 0 ? round($disetujui / $keputusan * 100, 1) : 0,
            ],
            'etl_terakhir' => $run,
            'riwayat_etl'  => DB::table('etl_runs')
                ->where('nama', EtlLokasiAktivasi::NAMA)
                ->orderByDesc('id')
                ->limit(5)
                ->get(),
        ];
    }

    private function hitung(string $nama, Collection $g): array
    {
        $total = (int) $g->sum('total_ruangan');
        $terisi = (int) $g->sum('terisi');

        return [
            'nama'        => $nama,
            'total'       => $total,
            'terisi'      => $terisi,
            'kosong'      => $total - $terisi,
            'okupansi'    => $total > 0 ? round($terisi / $total * 100, 1) : 0,
            'luas_total'  => round((float) $g->sum('luas_total'), 2),
            'luas_terisi' => round((float) $g->sum('luas_terisi'), 2),
        ];
    }
}
