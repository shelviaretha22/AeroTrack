<?php

namespace App\Http\Controllers;

use App\Models\PendapatanTagihan;
use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PendapatanController extends Controller
{
    public function index(Request $request)
    {
        $tahun = (int) $request->input('tahun', now()->year);

        if ($tahun < 2000 || $tahun > 2100) {
            $tahun = now()->year;
        }

        // Filter bulan, default Januari sampai Desember.
        $bulanAwal = max(1, min(12, (int) $request->input('bulan_awal', 1)));
        $bulanAkhir = max(1, min(12, (int) $request->input('bulan_akhir', 12)));

        // Jika bulan awal lebih besar, tukar otomatis.
        if ($bulanAwal > $bulanAkhir) {
            [$bulanAwal, $bulanAkhir] = [$bulanAkhir, $bulanAwal];
        }

        $search = trim((string) $request->input('search', ''));

        $awalTahun = Carbon::create($tahun, 1, 1)->startOfDay();
        $akhirTahun = Carbon::create($tahun, 12, 31)->endOfDay();

        $query = Tenant::with('kerjaSama')
            ->whereHas('kerjaSama', function ($q) use ($awalTahun, $akhirTahun) {
                // Tagihan baru muncul setelah aktivasi disetujui pusat
                // (pengajuan aktivasi terbaru berstatus "Disetujui").
                $q->whereHas('aktivasi', function ($aktivasi) {
                    $aktivasi->where('status', 'Disetujui');
                })
                    ->whereNotNull('tanggal_mulai')
                    ->whereDate('tanggal_mulai', '<=', $akhirTahun)
                    ->where(function ($sub) use ($awalTahun) {
                        $sub->whereNull('tanggal_berakhir')
                            ->orWhereDate('tanggal_berakhir', '>=', $awalTahun);
                    });
            });

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('nama_tenant', 'like', "%{$search}%")
                    ->orWhere('mitra_usaha', 'like', "%{$search}%");
            });
        }

        $tenants = $query->get();

        $this->sinkronkanTagihan($tenants, $tahun, $awalTahun, $akhirTahun);

        // Ambil tagihan tahun terpilih sesuai rentang bulan.
        $daftarPendapatan = PendapatanTagihan::with(['tenant', 'kerjaSama'])
            ->where('tahun', $tahun)
            ->whereIn('tenant_id', $tenants->pluck('id'))
            ->whereBetween('bulan', [$bulanAwal, $bulanAkhir])
            ->orderBy('tenant_id')
            ->orderBy('bulan')
            ->get();

        $pendapatanPerTenant = $daftarPendapatan->groupBy('tenant_id');

        // Ringkasan nominal.
        $totalTagihan = $daftarPendapatan->sum('jumlah_tagihan');

        $totalInvoiceDiberikan = $daftarPendapatan
            ->where('status_invoice', true)
            ->sum('jumlah_tagihan');

        $totalInvoiceBelumDiberikan = $totalTagihan - $totalInvoiceDiberikan;

        // Ringkasan jumlah invoice.
        $jumlahInvoiceDiberikan = $daftarPendapatan
            ->where('status_invoice', true)
            ->count();

        $jumlahInvoiceBelumDiberikan = $daftarPendapatan
            ->where('status_invoice', false)
            ->count();

        return view('pendapatan.index', compact(
            'tahun',
            'bulanAwal',
            'bulanAkhir',
            'search',
            'tenants',
            'pendapatanPerTenant',
            'totalTagihan',
            'totalInvoiceDiberikan',
            'totalInvoiceBelumDiberikan',
            'jumlahInvoiceDiberikan',
            'jumlahInvoiceBelumDiberikan'
        ));
    }

    /**
     * Membuat/menyesuaikan tagihan bulanan berdasarkan periode kontrak.
     *
     * - Tagihan yang sudah berstatus "invoice diberikan" tidak diubah
     *   nominalnya (riwayat tidak ikut berubah saat GTO/RS diedit).
     * - Tagihan belum diberikan yang berada di luar periode kontrak
     *   (mis. tanggal berakhir dimajukan) dihapus.
     */
    private function sinkronkanTagihan($tenants, int $tahun, Carbon $awalTahun, Carbon $akhirTahun): void
    {
        if ($tenants->isEmpty()) {
            return;
        }

        $sudahAda = PendapatanTagihan::where('tahun', $tahun)
            ->whereIn('tenant_id', $tenants->pluck('id'))
            ->get()
            ->keyBy(fn ($t) => $t->tenant_id . '-' . $t->bulan);

        DB::transaction(function () use ($tenants, $tahun, $awalTahun, $akhirTahun, $sudahAda) {
            foreach ($tenants as $tenant) {
                $kerjaSama = $tenant->kerjaSama;

                if (!$kerjaSama || !$kerjaSama->tanggal_mulai) {
                    continue;
                }

                if ($tenant->gto === null || $tenant->revenue_sharing === null) {
                    continue;
                }

                $tanggalMulai = Carbon::parse($kerjaSama->tanggal_mulai);

                $tanggalBerakhir = $kerjaSama->tanggal_berakhir
                    ? Carbon::parse($kerjaSama->tanggal_berakhir)
                    : $akhirTahun->copy();

                $mulai = $tanggalMulai->greaterThan($awalTahun)
                    ? $tanggalMulai->copy()
                    : $awalTahun->copy();

                $berakhir = $tanggalBerakhir->lessThan($akhirTahun)
                    ? $tanggalBerakhir->copy()
                    : $akhirTahun->copy();

                if ($mulai->gt($berakhir)) {
                    continue;
                }

                $jumlahTagihan = round(
                    ((float) $tenant->gto * (float) $tenant->revenue_sharing) / 100,
                    2
                );

                $bulanAwalKontrak = $mulai->month;
                $bulanAkhirKontrak = $berakhir->month;

                for ($bulan = $bulanAwalKontrak; $bulan <= $bulanAkhirKontrak; $bulan++) {
                    $tagihan = $sudahAda->get($tenant->id . '-' . $bulan);

                    if (!$tagihan) {
                        PendapatanTagihan::create([
                            'tenant_id' => $tenant->id,
                            'kerja_sama_id' => $kerjaSama->id,
                            'tahun' => $tahun,
                            'bulan' => $bulan,
                            'jumlah_tagihan' => $jumlahTagihan,
                            'status_invoice' => false,
                        ]);

                        continue;
                    }

                    if (
                        !$tagihan->status_invoice
                        && (
                            abs((float) $tagihan->jumlah_tagihan - $jumlahTagihan) > 0.004
                            || (int) $tagihan->kerja_sama_id !== (int) $kerjaSama->id
                        )
                    ) {
                        $tagihan->update([
                            'kerja_sama_id' => $kerjaSama->id,
                            'jumlah_tagihan' => $jumlahTagihan,
                        ]);
                    }
                }

                PendapatanTagihan::where('tenant_id', $tenant->id)
                    ->where('tahun', $tahun)
                    ->where('status_invoice', false)
                    ->where(function ($q) use ($bulanAwalKontrak, $bulanAkhirKontrak) {
                        $q->where('bulan', '<', $bulanAwalKontrak)
                            ->orWhere('bulan', '>', $bulanAkhirKontrak);
                    })
                    ->delete();
            }
        });
    }

    public function updateStatusInvoice(Request $request, $id)
    {
        $request->validate([
            'status_invoice' => ['required', 'boolean'],
        ]);

        $tagihan = PendapatanTagihan::findOrFail($id);

        $status = $request->boolean('status_invoice');

        $tagihan->update([
            'status_invoice' => $status,
            'tanggal_diberikan' => $status ? now()->toDateString() : null,
        ]);

        return redirect()
            ->route('pendapatan', [
                'tahun' => $tagihan->tahun,
                'bulan_awal' => $request->input('bulan_awal', 1),
                'bulan_akhir' => $request->input('bulan_akhir', 12),
                'search' => $request->input('search'),
            ])
            ->with(
                'success',
                $status
                    ? 'Status invoice berhasil diperbarui menjadi sudah diberikan.'
                    : 'Status invoice berhasil diubah menjadi belum diberikan.'
            );
    }
}
