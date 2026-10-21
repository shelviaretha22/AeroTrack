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
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class KontrakController extends Controller
{
    /**
     * Menampilkan dashboard, statistik, grafik,
     * dan tabel gabungan kontrak serta kandidat kontrak.
     */
    public function index(Request $request)
    {
        $hariIni = now()->startOfDay();

        /*
        |--------------------------------------------------------------------------
        | DATA SEMUA KONTRAK UNTUK SUMMARY DAN GRAFIK
        |--------------------------------------------------------------------------
        */

        $semuaKontrak = Kontrak::with([
            'kerjaSama',
            'tenant',
            'lokasi',
        ])->get();

        $totalKontrak = $semuaKontrak->count();

        $kontrakAktif = $semuaKontrak->filter(function ($kontrak) use ($hariIni) {
            $tanggalAkhir = $kontrak->kerjaSama?->tanggal_berakhir;

            return $kontrak->status === 'Selesai'
                && $tanggalAkhir
                && Carbon::parse($tanggalAkhir)
                    ->startOfDay()
                    ->greaterThanOrEqualTo($hariIni);
        })->count();

        $kontrakNonAktif = $semuaKontrak->filter(function ($kontrak) use ($hariIni) {
            $tanggalAkhir = $kontrak->kerjaSama?->tanggal_berakhir;

            return $tanggalAkhir
                && Carbon::parse($tanggalAkhir)
                    ->startOfDay()
                    ->lt($hariIni);
        })->count();

        $kontrakDalamProses = $semuaKontrak
            ->filter(fn ($kontrak) => $kontrak->status !== 'Selesai')
            ->count();

        /*
        |--------------------------------------------------------------------------
        | DATA GRAFIK DONAT: KOMPOSISI STATUS KONTRAK
        |--------------------------------------------------------------------------
        */

        $dataStatus = [
            'Aktif' => $kontrakAktif,
            'Non-Aktif' => $kontrakNonAktif,
            'Dalam Proses' => $kontrakDalamProses,
        ];

        /*
        |--------------------------------------------------------------------------
        | DATA GRAFIK BATANG: KONTRAK PER TAHUN
        |--------------------------------------------------------------------------
        */

        $dataTahunan = $semuaKontrak
            ->filter(fn ($kontrak) => $kontrak->tanggal_pengajuan !== null)
            ->groupBy(function ($kontrak) {
                return Carbon::parse($kontrak->tanggal_pengajuan)
                    ->format('Y');
            })
            ->map(fn ($items) => $items->count())
            ->sortKeys();

        $tahunTersedia = $dataTahunan->keys()->values();

        /*
        |--------------------------------------------------------------------------
        | QUERY TABEL KONTRAK
        |--------------------------------------------------------------------------
        */

        $query = Kontrak::with([
            'kerjaSama',
            'tenant',
            'lokasi',
        ]);

        // Pencarian.
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
                        $tenant->where('nama_tenant', 'like', "%{$search}%")
                            ->orWhere('mitra_usaha', 'like', "%{$search}%");
                    })
                    ->orWhereHas('lokasi', function ($lokasi) use ($search) {
                        $lokasi->where('lokasi', 'like', "%{$search}%")
                            ->orWhere('kode_ruang', 'like', "%{$search}%");
                    });
            });
        }

        // Filter status kontrak.
        if ($request->filled('status')) {
            $status = $request->input('status');

            if ($status === 'Aktif') {
                $query->where('status', 'Selesai')
                    ->whereHas('kerjaSama', function ($ks) use ($hariIni) {
                        $ks->whereDate('tanggal_berakhir', '>=', $hariIni);
                    });
            } elseif ($status === 'Non-Aktif') {
                $query->whereHas('kerjaSama', function ($ks) use ($hariIni) {
                    $ks->whereDate('tanggal_berakhir', '<', $hariIni);
                });
            } elseif ($status === 'Dalam Proses') {
                $query->where('status', '!=', 'Selesai');
            }
        }

        // Filter tahun pengajuan.
        if ($request->filled('tahun')) {
            $query->whereYear(
                'tanggal_pengajuan',
                $request->input('tahun')
            );
        }

        // Pengurutan.
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

            default:
                $query->orderByDesc('created_at');
                break;
        }

        $kontraks = $query->get();

        /*
        |--------------------------------------------------------------------------
        | KANDIDAT KONTRAK
        |--------------------------------------------------------------------------
        */

        $kerjaSamaSudahAdaKontrak = Kontrak::pluck('kerja_sama_id');

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
            });

        // Pencarian kandidat.
        if ($request->filled('search')) {
            $keyword = strtolower(trim($request->input('search')));

            $kandidatKontrak = $kandidatKontrak->filter(
                function ($kerjaSama) use ($keyword) {
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
                }
            );
        }

        // Filter status untuk kandidat.
        if ($request->filled('status')) {
            if ($request->input('status') !== 'Belum Kontrak') {
                $kandidatKontrak = collect();
            }
        }

        // Filter tahun kandidat berdasarkan tanggal pengajuan kerja sama.
        if ($request->filled('tahun')) {
            $tahun = $request->input('tahun');

            $kandidatKontrak = $kandidatKontrak->filter(
                function ($kerjaSama) use ($tahun) {
                    return $kerjaSama->tanggal_pengajuan
                        && Carbon::parse($kerjaSama->tanggal_pengajuan)
                            ->format('Y') === $tahun;
                }
            );
        }

        // Urutkan kandidat.
        switch ($request->input('sort', 'newest')) {
            case 'oldest':
                $kandidatKontrak = $kandidatKontrak
                    ->sortBy('created_at')
                    ->values();
                break;

            case 'az':
                $kandidatKontrak = $kandidatKontrak
                    ->sortBy('brand')
                    ->values();
                break;

            case 'za':
                $kandidatKontrak = $kandidatKontrak
                    ->sortByDesc('brand')
                    ->values();
                break;

            default:
                $kandidatKontrak = $kandidatKontrak
                    ->sortByDesc('created_at')
                    ->values();
                break;
        }

        return view('kontrak.index', compact(
            'kontraks',
            'kandidatKontrak',
            'totalKontrak',
            'kontrakAktif',
            'kontrakNonAktif',
            'kontrakDalamProses',
            'dataStatus',
            'dataTahunan',
            'tahunTersedia'
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

            // Pastikan aktivasi terbaru sudah disetujui.
            if ($kerjaSama->aktivasi?->status !== 'Disetujui') {
                throw new \RuntimeException(
                    'Kontrak hanya dapat dibuat jika aktivasi terbaru telah disetujui.'
                );
            }

            // Pastikan dokumen Berita Acara tersedia.
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

            // Ambil data tenant dan lokasi.
            $tenant = $kerjaSama->tenant
                ?? Tenant::where(
                    'kerja_sama_id',
                    $kerjaSama->id
                )->first();

            $lokasi = $kerjaSama->lokasi;

            if (!$tenant || !$lokasi) {
                throw new \RuntimeException(
                    'Data tenant atau lokasi belum lengkap.'
                );
            }

            // Cegah pembuatan kontrak ganda.
            $sudahAda = Kontrak::where(
                'kerja_sama_id',
                $kerjaSama->id
            )->exists();

            if ($sudahAda) {
                throw new \RuntimeException(
                    'Kontrak untuk kerja sama ini sudah pernah dibuat.'
                );
            }

            // Buat data kontrak dengan status Draft.
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

        // Jika berhasil, buka halaman Dokumen Kontrak.
        return redirect()
            ->route('kontrak.show', $kontrak->id)
            ->with(
                'success',
                'Kontrak berhasil dibuat. Silakan periksa Dokumen Kontrak.'
            );

    } catch (\RuntimeException $e) {
        // Jika gagal, kembali ke tabel kontrak dengan pesan error.
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

public function previewPdf(Request $request, $id)
{
    $kontrak = Kontrak::with([
        'kerjaSama.tenant',
        'kerjaSama.lokasi',
        'tenant',
        'lokasi',
    ])->findOrFail($id);

    $request->validate([
        'ttd_method' => ['required', 'in:online,offline'],
        'signature_data' => ['nullable', 'string', 'max:4000000'],
        'signature' => [
            'nullable',
            'image',
            'mimes:jpg,jpeg',
            'max:2048',
        ],
    ]);

    $signatureData = null;

    if ($request->input('ttd_method') === 'online') {
        
if ($request->hasFile('signature')) {
    $path = $request->file('signature')->store(
        'ttd-commercial',
        'local'
    );

    $data['ttd_commercial_path'] = $path;
    $data['ttd_type'] = 'upload';
} else {
    // Tanda tangan tidak diunggah sebagai file.
    // Gunakan nilai yang diizinkan oleh ENUM database.
    $data['ttd_type'] = 'draw';
}

    }

    $pdf = Pdf::loadView('kontrak.dokumen', [
        'kontrak' => $kontrak,
        'isPreview' => false,
        'isPdf' => true,
        'signatureData' => $signatureData,
    ])->setPaper('a4', 'portrait');

    return $pdf->stream(
        'pratinjau-kontrak-' . $kontrak->id . '.pdf'
    );
}


public function storeAcc(Request $request, $id)
{
    $kontrak = Kontrak::with('kerjaSama')->findOrFail($id);

    if ($kontrak->tanggal_acc !== null || $kontrak->status === 'Selesai') {
        return redirect()
            ->route('kontrak')
            ->with('error', 'Kontrak ini sudah diberikan ACC.');
    }

    $namaCommercial = $kontrak->kerjaSama?->pic_commercial;
    $jabatanCommercial = $kontrak->kerjaSama?->jabatan_pic_commercial;

    if (blank($namaCommercial) || blank($jabatanCommercial)) {
        return back()
            ->withInput()
            ->with('error', 'Nama dan jabatan PIC Commercial harus tersedia pada data Kerja Sama.');
    }

    $request->validate([
        'ttd_method' => ['required', 'in:online,offline'],
        'signature' => ['nullable', 'image', 'mimes:jpg,jpeg', 'max:2048'],
        'signature_data' => ['nullable', 'string', 'max:4000000'],
    ], [
        'ttd_method.required' => 'Silakan pilih metode tanda tangan.',
        'signature.image' => 'File tanda tangan harus berupa gambar.',
        'signature.mimes' => 'Tanda tangan yang diunggah harus berformat JPG/JPEG.',
        'signature.max' => 'Ukuran tanda tangan maksimal 2 MB.',
    ]);

    $data = [
        'nama_commercial' => $namaCommercial,
        'jabatan_commercial' => $jabatanCommercial,
    ];

    if ($request->input('ttd_method') === 'online') {
        if ($request->hasFile('signature')) {
            // Metode upload JPG/JPEG.
            $path = $request->file('signature')->store(
                'ttd-commercial',
                'local'
            );

            $data['ttd_commercial_path'] = $path;
            $data['ttd_type'] = 'upload';
        } else {
            // Metode menggambar tanda tangan pada canvas.
            $dataUrl = $request->input('signature_data', '');

            if (!preg_match(
                '/^data:image\/png;base64,([A-Za-z0-9+\/=\r\n]+)$/',
                $dataUrl,
                $matches
            )) {
                return back()
                    ->withInput()
                    ->with('error', 'Silakan gambar tanda tangan atau unggah file JPG/JPEG terlebih dahulu.');
            }

            $imageData = base64_decode($matches[1], true);

            if (
                $imageData === false
                || !str_starts_with($imageData, "\x89PNG\r\n\x1a\n")
            ) {
                return back()
                    ->withInput()
                    ->with('error', 'Data gambar tanda tangan tidak valid. Silakan gambar ulang.');
            }

            $path = 'ttd-commercial/ttd-' . $kontrak->id . '-' . now()->format('YmdHis') . '.png';

            Storage::disk('local')->put($path, $imageData);

            $data['ttd_commercial_path'] = $path;
            $data['ttd_type'] = 'draw';
        }
    } else {
        // Kolom database hanya menerima draw/upload dan boleh NULL.
        // Metode offline tidak menyimpan tipe tanda tangan digital.
        $data['ttd_type'] = null;
        $data['ttd_commercial_path'] = null;
    }

    $kontrak->update($data);

    return redirect()
        ->route('kontrak.uploadFinal', $kontrak->id)
        ->with(
            'success',
            'Informasi Commercial tersimpan. Silakan lanjutkan ke Upload Dokumen Kontrak Final.'
        );
}

public function formUploadFinal($id)
{
    $kontrak = Kontrak::with([
        'kerjaSama',
        'tenant',
        'lokasi',
    ])->findOrFail($id);

    // Halaman upload final hanya bisa dibuka setelah proses ACC.
    if (blank($kontrak->nama_commercial) || blank($kontrak->jabatan_commercial)) {
        return redirect()
            ->route('kontrak.acc', $kontrak->id)
            ->with('error', 'Silakan selesaikan tahap ACC terlebih dahulu.');
    }

    return view('kontrak.upload-final', compact('kontrak'));
}


public function storeUploadFinal(Request $request, $id)
{
    $kontrak = Kontrak::with('kerjaSama')->findOrFail($id);

    // Pastikan tahap ACC sudah dilakukan.
    if (blank($kontrak->nama_commercial) || blank($kontrak->jabatan_commercial)) {
        return redirect()
            ->route('kontrak.acc', $kontrak->id)
            ->with('error', 'Silakan selesaikan tahap ACC terlebih dahulu.');
    }

    // Validasi dokumen final harus berupa PDF.
    $request->validate([
        'final_pdf' => [
            'required',
            'file',
            'mimes:pdf',
            'max:10240',
        ],
        'final_signature_confirmed' => [
            'required',
            'accepted',
        ],
    ], [
        'final_pdf.required' => 'Dokumen kontrak final wajib diunggah.',
        'final_pdf.mimes' => 'Dokumen kontrak final harus berformat PDF.',
        'final_pdf.max' => 'Ukuran dokumen maksimal 10 MB.',
        'final_signature_confirmed.required' => 'Konfirmasi tanda tangan kedua pihak wajib dilakukan.',
        'final_signature_confirmed.accepted' => 'Kamu harus mengonfirmasi bahwa dokumen telah ditandatangani kedua pihak.',
    ]);

    // Simpan file PDF ke storage lokal.
    $path = $request->file('final_pdf')->store(
        'kontrak-final',
        'local'
    );

    // Dokumen final berhasil disimpan, kontrak dinyatakan selesai.
    $kontrak->update([
        'final_pdf_path' => $path,
        'final_uploaded_at' => now(),
        'final_signature_confirmed' => true,
        'status' => 'Selesai',
    ]);

    return redirect()
    ->route('kontrak')
    ->with(
        'success',
        'Dokumen kontrak final berhasil diunggah. Kontrak telah selesai.'
    );
}

    /**
     * Menyimpan keputusan konfirmasi perpanjangan kontrak.
     */
    
public function storeKonfirmasiPerpanjangan(Request $request, $id)
{
    $request->validate([
        'status_perpanjangan' => [
            'required',
            'in:Ingin Memperpanjang,Tidak Memperpanjang',
        ],
    ], [
        'status_perpanjangan.required' =>
            'Silakan pilih keputusan perpanjangan terlebih dahulu.',
        'status_perpanjangan.in' =>
            'Pilihan konfirmasi perpanjangan tidak valid.',
    ]);

    $kontrak = Kontrak::with('kerjaSama')->findOrFail($id);

    if ($kontrak->status !== 'Selesai') {
    return redirect()
        ->route('kontrak')
        ->with('error', 'Konfirmasi hanya dapat dilakukan untuk kontrak yang sudah selesai.');
}

    $tanggalAkhir = $kontrak->kerjaSama?->tanggal_berakhir;

    if (!$tanggalAkhir) {
        return redirect()
            ->route('kontrak')
            ->with('error', 'Tanggal akhir masa sewa belum tersedia.');
    }

    $batasKonfirmasi = Carbon::parse($tanggalAkhir)
        ->subMonthNoOverflow()
        ->startOfDay();

    if (now()->startOfDay()->lt($batasKonfirmasi)) {
        return redirect()
            ->route('kontrak')
            ->with('error', 'Konfirmasi baru dapat dilakukan mulai satu bulan sebelum masa sewa berakhir.');
    }

    $kontrak->update([
        'status_perpanjangan' => $request->input('status_perpanjangan'),
        'tanggal_konfirmasi' => now()->toDateString(),
    ]);

    if ($request->input('status_perpanjangan') === 'Ingin Memperpanjang') {
    return redirect()
        ->route('kerja-sama.create', [
            'perpanjangan_dari' => $kontrak->kerja_sama_id,
        ])
        ->with(
            'success',
            'Konfirmasi perpanjangan berhasil disimpan. Silakan lanjutkan membuat kerja sama baru.'
        );
}

    return redirect()
        ->route('kontrak')
        ->with('success', 'Konfirmasi tidak memperpanjang berhasil disimpan.');
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

public function finalPdf($id)
{
    $kontrak = Kontrak::findOrFail($id);

    if (
        $kontrak->status !== 'Selesai' ||
        blank($kontrak->final_pdf_path)
    ) {
        return redirect()
            ->route('kontrak')
            ->with('error', 'Dokumen kontrak final belum tersedia.');
    }

    $disk = \Illuminate\Support\Facades\Storage::disk('local');

    if (!$disk->exists($kontrak->final_pdf_path)) {
        return redirect()
            ->route('kontrak')
            ->with('error', 'File PDF kontrak final tidak ditemukan.');
    }

    return response()->file(
        $disk->path($kontrak->final_pdf_path),
        [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="kontrak-final.pdf"',
        ]
    );
}

    public function updateDokumen(Request $request, $id)
{
    $request->validate([
        'klausul_tambahan' => ['nullable', 'string', 'max:10000'],
    ]);

    $kontrak = Kontrak::findOrFail($id);

    $kontrak->klausul_tambahan = $request->input(
        'klausul_tambahan',
        ''
    );

    $kontrak->save();

    return redirect()
        ->route('kontrak.show', $kontrak->id)
        ->with('success', 'Penyesuaian dokumen berhasil disimpan.');
}
}
