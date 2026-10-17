<?php

namespace App\Http\Controllers;

use App\Models\Kontrak;
use App\Models\KerjaSama;
use App\Models\Tenant;
use App\Models\Lokasi;
use Illuminate\Http\Request;

class KontrakController extends Controller
{
    
public function index(Request $request)
{
    $query = Kontrak::with([
        'kerjaSama',
        'tenant',
        'lokasi',
    ]);

    // Pencarian berdasarkan nomor kontrak, kerja sama,
    // brand, tenant, atau lokasi.
    if ($request->filled('search')) {
        $search = $request->input('search');

        $query->where(function ($q) use ($search) {
            $q->where('nomor_kontrak', 'like', "%{$search}%")
                ->orWhereHas('kerjaSama', function ($ks) use ($search) {
                    $ks->where('nomor_kerja_sama', 'like', "%{$search}%")
                        ->orWhere('mitra_usaha', 'like', "%{$search}%")
                        ->orWhere('brand', 'like', "%{$search}%");
                })
                ->orWhereHas('tenant', function ($tenant) use ($search) {
                    $tenant->where('nama_tenant', 'like', "%{$search}%");
                })
                ->orWhereHas('lokasi', function ($lokasi) use ($search) {
                    $lokasi->where('lokasi', 'like', "%{$search}%")
                        ->orWhere('kode_ruang', 'like', "%{$search}%");
                });
        });
    }

    // Pengurutan data.
    switch ($request->input('sort', 'newest')) {
        case 'oldest':
            $query->orderBy('created_at', 'asc');
            break;

        case 'az':
            $query->orderBy(
                KerjaSama::select('brand')
                    ->whereColumn(
                        'kerja_samas.id',
                        'kontraks.kerja_sama_id'
                    )
            );
            break;

        case 'za':
            $query->orderByDesc(
                KerjaSama::select('brand')
                    ->whereColumn(
                        'kerja_samas.id',
                        'kontraks.kerja_sama_id'
                    )
            );
            break;

        case 'active':
            $query->where('status', 'Selesai')
                ->orderByDesc('tanggal_acc');
            break;

        case 'inactive':
            $query->where('status', '!=', 'Selesai')
                ->orderByDesc('created_at');
            break;

        default:
            $query->orderByDesc('created_at');
            break;
    }

    $kontraks = $query->get();

    return view('kontrak.index', compact('kontraks'));
}


    public function store(Request $request, $kerjaSamaId)
    {
        $kerjaSama = KerjaSama::with(['tenant', 'lokasi'])
            ->findOrFail($kerjaSamaId);

        $tenant = $kerjaSama->tenant;

        if (!$tenant) {
            $tenant = Tenant::where(
                'kerja_sama_id',
                $kerjaSama->id
            )->first();
        }

        $lokasi = $tenant
            ? Lokasi::where('tenant_id', $tenant->id)->first()
            : null;

        if (!$tenant || !$lokasi) {
            return redirect()
                ->route('kontrak')
                ->with('error', 'Data Tenant atau Lokasi belum lengkap.');
        }

        return redirect()
            ->route('kontrak')
            ->with('error', 'Kontrak belum dapat dibuat sebelum proses BA dan Aktivasi diverifikasi.');
    }

    public function acc($id)
    {
        $kontrak = Kontrak::with([
            'kerjaSama', 'tenant', 'lokasi',
        ])->findOrFail($id);

        return view('kontrak.acc', compact('kontrak'));
    }

    public function storeAcc(Request $request, $id)
    {
        return redirect()
            ->route('kontrak')
            ->with('error', 'ACC belum dapat diproses sebelum BA dan Aktivasi diverifikasi.');
    }

    
    public function document($id)
    {
        $kontrak = Kontrak::with([
            'kerjaSama.tenant',
            'kerjaSama.lokasi',
            'tenant',
            'lokasi',
        ])->findOrFail($id);

        return view('kontrak.dokumen', compact('kontrak'));
    }

    public function previewDocument($kerjaSamaId)
{
    $kerjaSama = KerjaSama::with([
        'tenant',
        'lokasi',
    ])->findOrFail($kerjaSamaId);

    $tenant = $kerjaSama->tenant;
    $lokasi = $kerjaSama->lokasi;

    return view('kontrak.dokumen', [
        'kontrak' => null,
        'kerjaSama' => $kerjaSama,
        'tenant' => $tenant,
        'lokasi' => $lokasi,
        'isPreview' => true,
    ]);
}

    
    public function show($id)
    {
        $kontrak = Kontrak::with([
            'kerjaSama.tenant',
            'kerjaSama.lokasi',
            'tenant',
            'lokasi',
        ])->findOrFail($id);

        return view('kontrak.dokumen', compact('kontrak'));
    }

}