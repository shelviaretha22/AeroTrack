<?php

namespace App\Http\Controllers;

use App\Models\KerjaSama;
use App\Models\Lokasi;
use App\Models\PendapatanTagihan;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Dashboard monitoring kontrak tenant.
 *
 * Satuan "kontrak" di dashboard = satu data Kerja Sama (sama dengan menu
 * Aktivasi), sehingga angkanya selalu sejalan dengan menu tersebut.
 * Semua card, chart, dan tabel dihitung dari SATU kumpulan data yang
 * sudah difilter, jadi selalu konsisten satu sama lain.
 */
class DashboardController extends Controller
{
    /** Urutan dan nama tampilan status kontrak di dashboard. */
    public const STATUS = [
        'Aktif',
        'Proses Pusat',
        'Proses Cabang',
        'Negosiasi',
    ];

    /** Kunci jenis_usaha (tersimpan di database) => nama tampilan. */
    public const KATEGORI = [
        'F&B'       => 'Food & Beverage',
        'Service'   => 'Services',
        'Retail'    => 'Retail',
        'Lounge'    => 'Lounge',
        'Duty Free' => 'Duty Free',
        'UMKM'      => 'UMKM',
    ];

    public const PERIODE = ['Lintas', 'Perpanjangan'];

    private const PER_HALAMAN = 8;

    public function index(Request $request)
    {
        $filter = [
            'terminal' => (string) $request->query('terminal', ''),
            'lokasi'   => (string) $request->query('lokasi', ''),
            'kategori' => (string) $request->query('kategori', ''),
            'status'   => (string) $request->query('status', ''),
            'periode'  => (string) $request->query('periode', ''),
        ];

        // Tagihan yang invoicenya sudah diberikan = pendapatan yang sudah diterima
        // (sama dengan definisi di menu Pendapatan).
        $diterimaPerKs = PendapatanTagihan::query()
            ->where('status_invoice', true)
            ->selectRaw('kerja_sama_id, SUM(jumlah_tagihan) as total')
            ->groupBy('kerja_sama_id')
            ->pluck('total', 'kerja_sama_id');

        $semua = KerjaSama::with([
            'tenant',
            'lokasi',
            'aktivasi',
            'progres',
            'kontrakTerbaru',
        ])->get()->map(fn (KerjaSama $ks) => $this->susunBaris($ks, (float) ($diterimaPerKs[$ks->id] ?? 0)));

        // ---- Filter (berlaku untuk seluruh bagian dashboard) ----
        $data = $semua->filter(function (array $b) use ($filter) {
            return ($filter['terminal'] === '' || $b['terminal'] === $filter['terminal'])
                && ($filter['lokasi'] === '' || $b['lokasi'] === $filter['lokasi'])
                && ($filter['kategori'] === '' || $b['kategori'] === $filter['kategori'])
                && ($filter['status'] === '' || $b['status'] === $filter['status'])
                && ($filter['periode'] === '' || $b['periode'] === $filter['periode']);
        })->values();

        $total = $data->count();
        $aktif = $data->where('aktif', true)->count();

        // ---- 1. Ringkasan ----
        $opsiTerminal = Lokasi::query()
            ->whereNotNull('terminal')->where('terminal', '<>', '')
            ->distinct()->orderBy('terminal')->pluck('terminal');

        $perTerminal = $opsiTerminal->take(4)->mapWithKeys(function ($t) use ($data) {
            $g = $data->where('terminal', $t);

            return [$t => ['total' => $g->count(), 'aktif' => $g->where('aktif', true)->count()]];
        });

        // ---- 2. Distribusi lokasi (pie) ----
        $distribusiLokasi = $data
            ->groupBy(fn ($b) => $b['terminal'] ?: 'Belum ada lokasi')
            ->map->count()
            ->sortKeys();

        // ---- 3. Per kategori ----
        $perKategori = collect(self::KATEGORI)->map(function ($label, $kunci) use ($data) {
            $g = $data->where('kategori', $kunci);

            return [
                'label' => $label,
                'total' => $g->count(),
                'aktif' => $g->where('aktif', true)->count(),
            ];
        });

        // ---- 4. Informasi status ----
        $perStatus = collect(self::STATUS)->mapWithKeys(fn ($s) => [$s => $data->where('status', $s)->count()]);

        $info = [
            'belumAktif'     => $total - $aktif,
            'pusat'          => $perStatus['Proses Pusat'],
            'prosesAktivasi' => $perStatus['Proses Pusat'] + $perStatus['Proses Cabang'],
            'noKontrak'      => $data->filter(fn ($b) => filled($b['nomor_kontrak']))->count(),
            'noKonsumen'     => $data->filter(fn ($b) => filled($b['no_konsumen']))->count(),
        ];

        // ---- 6. Tabel status: nomor kontrak per status ----
        $tabelStatus = collect(self::STATUS)->map(function ($s) use ($data) {
            $nomor = $data->where('status', $s)->pluck('nomor_kontrak')->filter()->values();

            return ['status' => $s, 'nomor' => $nomor, 'jumlah' => $data->where('status', $s)->count()];
        });

        // ---- 7. Periode ----
        $perPeriode = collect(self::PERIODE)->mapWithKeys(fn ($p) => [$p => $data->where('periode', $p)->count()]);

        // ---- 5. Potensi pendapatan per bulan (GTO x revenue sharing) ----
        $potensi = $data
            ->filter(fn ($b) => $b['potensi'] !== null)
            ->sortByDesc('potensi')
            ->values();

        $grandTotalPotensi = $potensi->sum('potensi');
        $grandTotalDiterima = $data->sum('diterima');

        return view('dashboard', [
            'filter'            => $filter,
            'adaFilter'         => collect($filter)->contains(fn ($v) => $v !== ''),
            'opsiTerminal'      => $opsiTerminal,
            'opsiLokasi'        => Lokasi::query()->whereNotNull('lokasi')->where('lokasi', '<>', '')
                                        ->distinct()->orderBy('lokasi')->pluck('lokasi'),
            'opsiKategori'      => self::KATEGORI,
            'opsiStatus'        => self::STATUS,
            'opsiPeriode'       => self::PERIODE,

            'total'             => $total,
            'aktif'             => $aktif,
            'perTerminal'       => $perTerminal,
            'distribusiLokasi'  => $distribusiLokasi,
            'perKategori'       => $perKategori,
            'info'              => $info,
            'perStatus'         => $perStatus,
            'tabelStatus'       => $tabelStatus,
            'perPeriode'        => $perPeriode,

            'potensi'           => $this->paginasi($potensi, $request, 'hal_potensi', 'tbl-potensi'),
            'grandTotalPotensi' => $grandTotalPotensi,
            'grandTotalDiterima' => $grandTotalDiterima,
            'jumlahPotensi'     => $potensi->count(),
            'detail'            => $this->paginasi($data, $request, 'hal_detail', 'tbl-detail'),
        ]);
    }

    /**
     * Mengubah satu Kerja Sama menjadi baris data dashboard.
     */
    private function susunBaris(KerjaSama $ks, float $diterima = 0): array
    {
        $tenant = $ks->tenant;
        $lokasi = $ks->lokasi;

        $potensi = ($tenant && $tenant->gto !== null && $tenant->revenue_sharing !== null)
            ? round(((float) $tenant->gto * (float) $tenant->revenue_sharing) / 100, 2)
            : null;

        return [
            'id'            => $ks->id,
            'mitra'         => $ks->mitra_usaha ?: ($tenant->nama_tenant ?? '-'),
            'brand'         => $ks->brand,
            'terminal'      => (string) ($lokasi->terminal ?? ''),
            'lokasi'        => (string) ($lokasi->lokasi ?? ''),
            'kode_ruang'    => $lokasi->kode_ruang ?? null,
            'kategori'      => $ks->jenis_usaha,
            'status'        => $this->tentukanStatus($ks),
            'aktif'         => $ks->statusKontrakSistem() === 'Aktif',
            // Lintas = kontrak baru; Perpanjangan = tenant memilih memperpanjang.
            'periode'       => $ks->kontrakTerbaru?->status_perpanjangan === 'Ingin Memperpanjang'
                                ? 'Perpanjangan'
                                : 'Lintas',
            'nomor_kontrak' => $ks->kontrakTerbaru?->nomor_kontrak,
            'no_konsumen'   => $ks->progres?->nomor_konsumen,
            'potensi'       => $potensi,
            'diterima'      => $diterima,
        ];
    }

    /**
     * Status kontrak untuk dashboard, diturunkan dari data yang sudah ada
     * (tidak ada input manual terpisah):
     *
     *  Aktif          pengajuan aktivasi disetujui Commercial Pusat
     *  Proses Pusat   aktivasi diajukan, menunggu keputusan pusat
     *  Proses Cabang  BA sudah ada, pengajuan aktivasi belum dikirim
     *  Negosiasi      selain di atas (kerja sama/BA/kontrak masih disiapkan,
     *                 atau kerja sama/aktivasi ditolak dan perlu dinegosiasikan ulang)
     */
    private function tentukanStatus(KerjaSama $ks): string
    {
        if ($ks->status === 'Ditolak') {
            return 'Negosiasi';
        }

        return match ($ks->statusAktivasi()) {
            'Disetujui'          => 'Aktif',
            'Menunggu Keputusan' => 'Proses Pusat',
            'Belum Diajukan'     => 'Proses Cabang',
            default              => 'Negosiasi',
        };
    }

    private function paginasi($koleksi, Request $request, string $namaHalaman, string $jangkar): LengthAwarePaginator
    {
        $halaman = max(1, (int) $request->query($namaHalaman, 1));

        return (new LengthAwarePaginator(
            $koleksi->forPage($halaman, self::PER_HALAMAN)->values(),
            $koleksi->count(),
            self::PER_HALAMAN,
            $halaman,
            ['path' => $request->url(), 'pageName' => $namaHalaman]
        ))->appends($request->query())->fragment($jangkar);
    }
}