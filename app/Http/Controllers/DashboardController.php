<?php

namespace App\Http\Controllers;

use App\Models\Aktivasi;
use App\Models\KerjaSama;
use App\Models\Kontrak;
use App\Models\PendapatanTagihan;

class DashboardController extends Controller
{
    public function index()
    {
        $kontraks = Kontrak::with(['kerjaSama', 'lokasi', 'tenant'])->get();
        $aktif = $kontraks->where('status', 'Aktif');
        $belumAktif = $kontraks->where('status', '!=', 'Aktif');

        $pusat = $kontraks->where('status', 'Proses Pusat')->count();

        $group = fn ($items, $fn) => $items->groupBy($fn)->map(fn ($g) => [
            'aktif' => $g->where('status', 'Aktif')->count(),
            'total' => $g->count(),
        ]);

        return view('dashboard', [
            'total'          => $kontraks->count(),
            'aktif'          => $aktif->count(),
            'belumAktif'     => $belumAktif->count(),
            'pusat'          => $pusat,
            'cabang'         => $belumAktif->count() - $pusat,
            'prosesAktivasi' => Aktivasi::where('status', 'Proses')->count(),

            // NO KON SYSTEM: kontrak aktif yang belum punya nomor sistem / BA final
            'noKonKontrak'   => $aktif->filter(fn ($k) => blank($k->nomor_kontrak_mitra))->count(),
            'noKonBA'        => KerjaSama::whereNull('berita_acara_final_path')->where('status', 'Disetujui')->count(),

            'perTerminal'    => $group($kontraks, fn ($k) => $k->lokasi->terminal ?? 'Lainnya')->take(3),
            'perJenis'       => $group($kontraks, fn ($k) => $k->kerjaSama->jenis_usaha ?? 'Lainnya'),
            'perStatus'      => $kontraks->groupBy(fn ($k) => $k->status ?: 'null')->map->count()->sortDesc(),
            'periode'        => [
                'Lintas'       => $kontraks->filter(fn ($k) => blank($k->status_perpanjangan))->count(),
                'Perpanjangan' => $kontraks->filter(fn ($k) => filled($k->status_perpanjangan))->count(),
            ],
            'daftarKontrak'  => $belumAktif->sortByDesc('tanggal_pengajuan')->values(),
            'pending'        => PendapatanTagihan::with('tenant')->where('status_invoice', false)->get()
                                    ->groupBy(fn ($p) => $p->tenant->nama_tenant ?? '-')
                                    ->map->sum('jumlah_tagihan')->sortDesc(),
        ]);
    }
}