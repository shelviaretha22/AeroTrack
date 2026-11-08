<?php

namespace App\Http\Controllers;

use App\Models\KerjaSama;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * Analisis Aktivasi untuk eksekutif (SIE): drilldown dan what-if.
 * Semua angka dihitung langsung dari data kerja sama, aktivasi, dan tenant;
 * tidak ada data yang diubah di sini.
 */
class AktivasiAnalisisController extends Controller
{
    private const TANPA_LOKASI = 'Belum ada lokasi';

    // ==================================================================
    // DRILLDOWN: Status -> Terminal -> Kerja sama -> Riwayat
    // ==================================================================

    public function drilldown(Request $request): JsonResponse
    {
        $request->validate([
            'status'      => ['nullable', 'string', 'in:' . implode(',', KerjaSama::STATUS_AKTIVASI)],
            'terminal'    => ['nullable', 'string', 'max:100'],
            'kerja_sama'  => ['nullable', 'integer', 'min:1'],
        ]);

        $status   = trim((string) $request->query('status', ''));
        $terminal = trim((string) $request->query('terminal', ''));
        $idKs     = $request->filled('kerja_sama') ? (int) $request->query('kerja_sama') : null;

        $semua = $this->semuaKerjaSama();

        // Level paling bawah: riwayat satu kerja sama.
        if ($idKs !== null) {
            return $this->levelRiwayat($semua, $idKs);
        }

        $data = $status === ''
            ? $semua
            : $semua->filter(fn (KerjaSama $ks) => $ks->statusAktivasi() === $status);

        if ($status !== '' && $terminal !== '') {
            $data = $data->filter(fn (KerjaSama $ks) => $this->terminalDari($ks) === $terminal);

            return $this->levelDaftar($data);
        }

        if ($status === '') {
            // Semua status tampil (termasuk yang 0) agar urutannya selalu sama.
            $kelompok = collect(KerjaSama::STATUS_AKTIVASI)->mapWithKeys(
                fn ($s) => [$s => $semua->filter(fn ($ks) => $ks->statusAktivasi() === $s)]
            );

            return $this->levelKelompok('status', 'Status', $kelompok, $semua);
        }

        $kelompok = $data
            ->groupBy(fn (KerjaSama $ks) => $this->terminalDari($ks))
            ->sortKeys();

        return $this->levelKelompok('terminal', 'Terminal', $kelompok, $data);
    }

    private function levelKelompok(string $kunci, string $judul, Collection $kelompok, Collection $induk): JsonResponse
    {
        $items = $kelompok->map(function (Collection $g, string $nama) use ($induk) {
            $umur = $g->map(fn ($ks) => $this->umurHari($ks))->filter(fn ($u) => $u !== null);

            return [
                'nilai'     => $nama,
                'nama'      => $nama,
                'total'     => $g->count(),
                'persen'    => $induk->count() > 0 ? round($g->count() / $induk->count() * 100, 1) : 0,
                'aktif'     => $g->filter(fn ($ks) => $ks->statusKontrakSistem() === 'Aktif')->count(),
                'rata_hari' => $umur->isNotEmpty() ? round($umur->avg(), 1) : null,
            ];
        })->values();

        $umurInduk = $induk->map(fn ($ks) => $this->umurHari($ks))->filter(fn ($u) => $u !== null);

        return response()->json([
            'kunci'     => $kunci,
            'judul'     => $judul,
            'items'     => $items,
            'daftar'    => [],
            'riwayat'   => [],
            'ks'        => null,
            'ringkasan' => [
                'total'     => $induk->count(),
                'aktif'     => $induk->filter(fn ($ks) => $ks->statusKontrakSistem() === 'Aktif')->count(),
                'rata_hari' => $umurInduk->isNotEmpty() ? round($umurInduk->avg(), 1) : null,
            ],
        ]);
    }

    private function levelDaftar(Collection $data): JsonResponse
    {
        $daftar = $data->map(fn (KerjaSama $ks) => [
            'id'        => $ks->id,
            'mitra'     => $ks->mitra_usaha,
            'brand'     => $ks->brand,
            'nomor'     => $ks->aktivasi?->nomor_pengajuan,
            'tanggal'   => $ks->aktivasi?->tanggal_pengajuan?->format('d M Y'),
            'umur_hari' => $this->umurHari($ks),
            'sistem'    => $ks->statusKontrakSistem(),
            'fisik'     => $ks->posisiKontrakFisik(),
        ])->sortByDesc(fn ($r) => $r['umur_hari'] ?? -1)->values();

        $umur = $daftar->pluck('umur_hari')->filter(fn ($u) => $u !== null);

        return response()->json([
            'kunci'     => 'kerja_sama',
            'judul'     => 'Kerja Sama',
            'items'     => [],
            'daftar'    => $daftar,
            'riwayat'   => [],
            'ks'        => null,
            'ringkasan' => [
                'total'     => $daftar->count(),
                'aktif'     => $daftar->where('sistem', 'Aktif')->count(),
                'rata_hari' => $umur->isNotEmpty() ? round($umur->avg(), 1) : null,
            ],
        ]);
    }

    private function levelRiwayat(Collection $semua, int $idKs): JsonResponse
    {
        $ks = $semua->firstWhere('id', $idKs);

        abort_if($ks === null, 404);

        $riwayat = $ks->aktivasiRiwayats()->with('user:id,name')->limit(50)->get()
            ->map(fn ($r) => [
                'waktu'      => $r->created_at->format('d M Y H:i'),
                'komponen'   => $r->komponen,
                'keterangan' => $r->keterangan,
                'oleh'       => $r->user?->name ?? '-',
            ])->values();

        return response()->json([
            'kunci'     => 'riwayat',
            'judul'     => 'Riwayat',
            'items'     => [],
            'daftar'    => [],
            'riwayat'   => $riwayat,
            'ks'        => [
                'id'     => $ks->id,
                'mitra'  => $ks->mitra_usaha,
                'brand'  => $ks->brand,
                'status' => $ks->statusAktivasi(),
                'sistem' => $ks->statusKontrakSistem(),
                'url'    => route('aktivasi.show', $ks->id),
            ],
            'ringkasan' => [
                'total'     => 1,
                'aktif'     => $ks->statusKontrakSistem() === 'Aktif' ? 1 : 0,
                'rata_hari' => $this->umurHari($ks),
            ],
        ]);
    }

    // ==================================================================
    // WHAT-IF: kalau pengajuan disetujui / diajukan, berapa kontrak aktif
    // ==================================================================

    public function whatif(Request $request): JsonResponse
    {
        $input = $request->validate([
            'setuju_menunggu' => ['nullable', 'integer', 'min:0', 'max:100'],
            'ajukan_belum'    => ['nullable', 'integer', 'min:0', 'max:100'],
            'sla'             => ['nullable', 'integer', 'min:1', 'max:365'],
        ]);

        $p1  = (int) ($input['setuju_menunggu'] ?? 100); // % "Menunggu Keputusan" yang disetujui
        $p2  = (int) ($input['ajukan_belum'] ?? 50);     // % "Belum Diajukan" yang diajukan lalu disetujui
        $sla = (int) ($input['sla'] ?? 14);              // batas hari keputusan

        $semua = $this->semuaKerjaSama();

        $menunggu = $semua->filter(fn ($ks) => $ks->statusAktivasi() === 'Menunggu Keputusan');
        $belum    = $semua->filter(fn ($ks) => $ks->statusAktivasi() === 'Belum Diajukan');
        $aktif    = $semua->filter(fn ($ks) => $ks->statusKontrakSistem() === 'Aktif');

        $total = $semua->count();

        // Potensi pendapatan per bulan = GTO x revenue sharing (sama dengan Dashboard).
        $potensi = fn (Collection $c) => (float) $c->sum(fn (KerjaSama $ks) => $ks->tenant
            && $ks->tenant->gto !== null && $ks->tenant->revenue_sharing !== null
                ? (float) $ks->tenant->gto * (float) $ks->tenant->revenue_sharing / 100
                : 0);

        $tambahMenunggu = (int) round($menunggu->count() * $p1 / 100);
        $tambahBelum    = (int) round($belum->count() * $p2 / 100);
        $aktifBaru      = $aktif->count() + $tambahMenunggu + $tambahBelum;

        $persen = fn (int $a) => $total > 0 ? round($a / $total * 100, 1) : 0;

        $potensiSekarang = round($potensi($aktif), 2);
        $potensiTambahan = round(
            $potensi($menunggu) * $p1 / 100 + $potensi($belum) * $p2 / 100,
            2
        );

        // Rata-rata hari keputusan historis (pengajuan -> keputusan).
        $historis = $semua->filter(fn ($ks) => in_array($ks->statusAktivasi(), ['Disetujui', 'Ditolak'], true))
            ->map(fn ($ks) => $this->umurHari($ks))
            ->filter(fn ($u) => $u !== null);

        // Pengajuan yang menunggu lebih lama dari batas SLA.
        $menungguUmur = $menunggu
            ->map(fn (KerjaSama $ks) => ['ks' => $ks, 'umur' => $this->umurHari($ks)])
            ->filter(fn ($r) => $r['umur'] !== null)
            ->sortByDesc('umur')
            ->values();

        $lewatSla = $menungguUmur->filter(fn ($r) => $r['umur'] > $sla);

        return response()->json([
            'parameter' => ['setuju_menunggu' => $p1, 'ajukan_belum' => $p2, 'sla' => $sla],
            'sekarang'  => [
                'total'               => $total,
                'aktif'               => $aktif->count(),
                'tingkat'             => $persen($aktif->count()),
                'menunggu_keputusan'  => $menunggu->count(),
                'belum_diajukan'      => $belum->count(),
                'menunggu_ba'         => $semua->filter(fn ($ks) => $ks->statusAktivasi() === 'Menunggu BA')->count(),
                'ditolak'             => $semua->filter(fn ($ks) => $ks->statusAktivasi() === 'Ditolak')->count(),
                'rata_hari_keputusan' => $historis->isNotEmpty() ? round($historis->avg(), 1) : null,
                'potensi_bulan'       => $potensiSekarang,
            ],
            'skenario'  => [
                'tambah_dari_menunggu' => $tambahMenunggu,
                'tambah_dari_belum'    => $tambahBelum,
                'aktif'                => $aktifBaru,
                'tingkat'              => $persen($aktifBaru),
                'selisih_tingkat'      => round($persen($aktifBaru) - $persen($aktif->count()), 1),
                'potensi_tambahan'     => $potensiTambahan,
                'potensi_bulan'        => round($potensiSekarang + $potensiTambahan, 2),
                'potensi_tahun'        => round(($potensiSekarang + $potensiTambahan) * 12, 2),
            ],
            'sla'       => [
                'batas_hari' => $sla,
                'melewati'   => $lewatSla->count(),
                'dari'       => $menungguUmur->count(),
                'terlama'    => $menungguUmur->take(5)->map(fn ($r) => [
                    'id'    => $r['ks']->id,
                    'nomor' => $r['ks']->aktivasi?->nomor_pengajuan,
                    'mitra' => $r['ks']->mitra_usaha,
                    'umur'  => $r['umur'],
                    'lewat' => $r['umur'] > $sla,
                    'url'   => route('aktivasi.show', $r['ks']->id),
                ])->values(),
            ],
        ]);
    }

    // ==================================================================
    // Pembantu
    // ==================================================================

    /**
     * Status aktivasi diturunkan di PHP (bukan kolom database), sama seperti
     * halaman Aktivasi, sehingga angkanya selalu cocok dengan tabel.
     */
    private function semuaKerjaSama(): Collection
    {
        return KerjaSama::with(['tenant', 'lokasi', 'aktivasi', 'kontrakTerbaru'])
            ->latest()
            ->get();
    }

    private function terminalDari(KerjaSama $ks): string
    {
        return $ks->lokasi?->terminal ?: self::TANPA_LOKASI;
    }

    /**
     * Umur pengajuan dalam hari: sampai tanggal keputusan bila sudah diputuskan,
     * selain itu sampai hari ini. Null bila belum pernah diajukan.
     */
    private function umurHari(KerjaSama $ks): ?int
    {
        $aktivasi = $ks->aktivasi;

        if (! $aktivasi || ! $aktivasi->tanggal_pengajuan) {
            return null;
        }

        $akhir = in_array($aktivasi->status, ['Disetujui', 'Ditolak'], true) && $aktivasi->tanggal_keputusan
            ? Carbon::parse($aktivasi->tanggal_keputusan)
            : Carbon::today();

        return max(0, (int) round(abs(
            Carbon::parse($aktivasi->tanggal_pengajuan)->startOfDay()
                ->diffInDays($akhir->startOfDay())
        )));
    }
}
