<?php

namespace App\Http\Controllers;

use App\Models\Aktivasi;
use App\Models\Kontrak;
use App\Models\KerjaSama;
use App\Models\Tenant;
use App\Models\Lokasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class KontrakController extends Controller
{
    /**
     * Menampilkan data kontrak dan kandidat kontrak
     * dari aktivasi yang telah disetujui.
     */
    public function index(Request $request)
    {
        // Ambil kontrak yang sudah dibuat.
        $query = Kontrak::with([
            'kerjaSama',
            'tenant',
            'lokasi',
        ]);

        if ($request->filled('search')) {
            $search = trim($request->input('search'));

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

        // ID kerja sama yang sudah memiliki kontrak.
        $kerjaSamaSudahAdaKontrak = Kontrak::pluck('kerja_sama_id');

        // Ambil kerja sama yang aktivasi TERBARUNYA disetujui,
        // BA tersedia, dan belum mempunyai kontrak.
        $kandidatKontrak = KerjaSama::with([
            'tenant',
            'lokasi',
            'aktivasi',
        ])
            ->whereNotNull('berita_acara_path')
            ->latest()
            ->get()
            ->filter(function ($kerjaSama) use ($kerjaSamaSudahAdaKontrak) {
                $baTersedia = Storage::disk('local')->exists(
                    $kerjaSama->berita_acara_path
                );

                return $baTersedia
                    && $kerjaSama->aktivasi?->status === 'Disetujui'
                    && $kerjaSama->tenant !== null
                    && $kerjaSama->lokasi !== null
                    && !$kerjaSamaSudahAdaKontrak->contains($kerjaSama->id);
            })
            ->values();

        // Pencarian juga berlaku untuk kandidat kontrak.
        if ($request->filled('search')) {
            $keyword = strtolower(trim($request->input('search')));

            $kandidatKontrak = $kandidatKontrak
                ->filter(function ($kerjaSama) use ($keyword) {
                    $nilaiPencarian = [
                        $kerjaSama->nomor_kerja_sama,
                        $kerjaSama->mitra_usaha,
                        $kerjaSama->brand,
                        $kerjaSama->tenant?->nama_tenant,
                        $kerjaSama->lokasi?->lokasi,
                        $kerjaSama->lokasi?->kode_ruang,
                    ];

                    foreach ($nilaiPencarian as $nilai) {
                        if (
                            $nilai !== null
                            && str_contains(
                                strtolower((string) $nilai),
                                $keyword
                            )
                        ) {
                            return true;
                        }
                    }

                    return false;
                })
                ->values();
        }

        return view('kontrak.index', compact(
            'kontraks',
            'kandidatKontrak'
        ));
    }

    /**
     * Membuat kontrak dari kerja sama yang aktivasi terbarunya disetujui.
     */
    public function store(Request $request, $kerjaSamaId)
    {
        try {
            $kontrak = DB::transaction(function () use ($kerjaSamaId) {
                $kerjaSama = KerjaSama::with([
                    'tenant',
                    'lokasi',
                    'aktivasi',
                ])
                    ->lockForUpdate()
                    ->findOrFail($kerjaSamaId);

                // Pastikan pengajuan aktivasi terbaru sudah disetujui.
                if ($kerjaSama->aktivasi?->status !== 'Disetujui') {
                    throw new \RuntimeException(
                        'Kontrak hanya dapat dibuat jika aktivasi terbaru telah disetujui.'
                    );
                }

                // Pastikan BA masih tersedia.
                if (
                    !$kerjaSama->berita_acara_path
                    || !Storage::disk('local')->exists(
                        $kerjaSama->berita_acara_path
                    )
                ) {
                    throw new \RuntimeException(
                        'Kontrak belum dapat dibuat karena dokumen BA tidak tersedia.'
                    );
                }

                $tenant = $kerjaSama->tenant
                    ?? Tenant::where('kerja_sama_id', $kerjaSama->id)->first();

                $lokasi = $kerjaSama->lokasi;

                if (!$tenant || !$lokasi) {
                    throw new \RuntimeException(
                        'Data tenant atau lokasi belum lengkap.'
                    );
                }

                // Cegah kontrak ganda untuk kerja sama yang sama.
                $sudahAda = Kontrak::where(
                    'kerja_sama_id',
                    $kerjaSama->id
                )->exists();

                if ($sudahAda) {
                    throw new \RuntimeException(
                        'Kontrak untuk kerja sama ini sudah pernah dibuat.'
                    );
                }

                return Kontrak::create([
                    'nomor_kontrak' => sprintf(
                        'KTR/JUANDA/%s/%04d',
                        now()->format('Y'),
                        $kerjaSama->id
                    ),
                    'kerja_sama_id' => $kerjaSama->id,
                    'tenant_id' => $tenant->id,
                    'lokasi_id' => $lokasi->id,
                    'tanggal_pengajuan' => now()->toDateString(),
                    'status' => 'Draft',
                ]);
            });

            return redirect()
                ->route('kontrak')
                ->with(
                    'success',
                    'Kontrak ' . $kontrak->nomor_kontrak
                    . ' berhasil dibuat dengan status Draft.'
                );
        } catch (\RuntimeException $e) {
            return redirect()
                ->route('kontrak')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Menampilkan halaman ACC kontrak.
     */
    public function acc($id)
    {
        $kontrak = Kontrak::with([
            'kerjaSama',
            'tenant',
            'lokasi',
        ])->findOrFail($id);

        return view('kontrak.acc', compact('kontrak'));
    }

    /**
     * Menyimpan ACC kontrak.
     * Proses ini tetap belum diaktifkan karena alur ACC perlu ditentukan.
     */
    public function storeAcc(Request $request, $id)
{
    $request->validate([
        'nama_commercial' => ['required', 'string', 'max:255'],
        'jabatan_commercial' => ['required', 'string', 'max:255'],
        'signature' => ['required', 'image', 'mimes:jpg,jpeg', 'max:2048'],
    ], [
        'nama_commercial.required' => 'Nama Commercial wajib diisi.',
        'jabatan_commercial.required' => 'Jabatan Commercial wajib diisi.',
        'signature.required' => 'File tanda tangan wajib diunggah.',
        'signature.image' => 'File tanda tangan harus berupa gambar.',
        'signature.mimes' => 'Tanda tangan harus berformat JPG atau JPEG.',
        'signature.max' => 'Ukuran tanda tangan maksimal 2 MB.',
    ]);

    $kontrak = Kontrak::findOrFail($id);

    if ($kontrak->tanggal_acc !== null || $kontrak->status === 'Selesai') {
        return redirect()
            ->route('kontrak')
            ->with('error', 'Kontrak ini sudah diberikan ACC.');
    }

    $path = $request->file('signature')->store(
        'ttd-commercial',
        'local'
    );

    $kontrak->update([
        'nama_commercial' => $request->nama_commercial,
        'jabatan_commercial' => $request->jabatan_commercial,
        'ttd_commercial_path' => $path,
        'ttd_type' => 'upload',
        'tanggal_acc' => now()->toDateString(),
        'status' => 'Selesai',
    ]);

    return redirect()
        ->route('kontrak')
        ->with('success', 'ACC kontrak berhasil disimpan.');
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

        return view('kontrak.dokumen', [
            'kontrak' => null,
            'kerjaSama' => $kerjaSama,
            'tenant' => $kerjaSama->tenant,
            'lokasi' => $kerjaSama->lokasi,
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