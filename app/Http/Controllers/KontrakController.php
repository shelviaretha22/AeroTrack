<?php

namespace App\Http\Controllers;

use App\Models\Aktivasi;
use App\Models\Kontrak;
use App\Models\KerjaSama;
use App\Models\Tenant;
use App\Models\Lokasi;
use App\Models\MasterPerusahaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;

class KontrakController extends Controller
{
/**
 * Mengambil identitas perusahaan dari master perusahaan.
 */
private function getDataPerusahaan()
{
    $perusahaan = MasterPerusahaan::first();

    return [
        'nama' => $perusahaan?->nama_perusahaan ?? '',
        'domisili' => $perusahaan?->domisili ?? '',
        'alamat' => $perusahaan?->alamat ?? '',
        'penandatangan' => $perusahaan?->nama_penandatangan ?? '',
        'jabatan' => $perusahaan?->jabatan_penandatangan ?? '',
        'telepon' => $perusahaan?->telepon ?? '',
        'fax' => '',
        'email' => $perusahaan?->email ?? '',
    ];
}

    /**
     * Menampilkan dashboard, statistik, grafik,
     * daftar kontrak, dan kandidat kontrak.
     */
    public function index(Request $request)
    {
        $hariIni = now()->startOfDay();

        /*
        |--------------------------------------------------------------------------
        | 1. DATA SELURUH KONTRAK
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
        | 2. DATA GRAFIK STATUS KONTRAK
        |--------------------------------------------------------------------------
        */

        $dataStatus = [
            'Aktif' => $kontrakAktif,
            'Non-Aktif' => $kontrakNonAktif,
            'Dalam Proses' => $kontrakDalamProses,
        ];

        /*
        |--------------------------------------------------------------------------
        | 3. DATA GRAFIK KONTRAK PER TAHUN
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
        | 4. QUERY DAFTAR KONTRAK
        |--------------------------------------------------------------------------
        */

        $query = Kontrak::with([
            'kerjaSama',
            'tenant',
            'lokasi',
        ]);

        // Pencarian berdasarkan nomor kontrak, kerja sama,
        // mitra, tenant, dan lokasi.
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

        // Pengurutan daftar kontrak.
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
        | 5. KANDIDAT KONTRAK
        |--------------------------------------------------------------------------
        |
        | Kerja sama dapat menjadi kandidat kontrak jika:
        | - Dokumen Berita Acara tersedia.
        | - Aktivasi berstatus Disetujui.
        | - Data tenant dan lokasi tersedia.
        | - Belum memiliki kontrak.
        |
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

        // Pencarian kandidat kontrak.
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

        // Filter status kandidat.
        if (
            $request->filled('status')
            && $request->input('status') !== 'Belum Kontrak'
        ) {
            $kandidatKontrak = collect();
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

        // Pengurutan kandidat kontrak.
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

        /*
        |--------------------------------------------------------------------------
        | 6. KIRIM DATA KE VIEW
        |--------------------------------------------------------------------------
        */

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

                // Ambil data kerja sama beserta relasinya.
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
                    !$kerjaSama->berita_acara_path ||
                    !Storage::disk('local')->exists(
                        $kerjaSama->berita_acara_path
                    )
                ) {
                    throw new \RuntimeException(
                        'Kontrak belum dapat dibuat karena dokumen Berita Acara tidak tersedia.'
                    );
                }

                // Ambil data tenant.
                $tenant = $kerjaSama->tenant
                    ?? Tenant::where(
                        'kerja_sama_id',
                        $kerjaSama->id
                    )->first();

                // Ambil data lokasi.
                $lokasi = $kerjaSama->lokasi;

                // Pastikan data utama tersedia.
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

                // Buat kontrak dengan status Draft.
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

            if ($request->expectsJson()) {
    return response()->json([
        'success' => true,
        'message' => 'Data dokumen kontrak berhasil disimpan.'
    ]);
}

if ($request->expectsJson()) {
    return response()->json([
        'success' => true,
        'message' => 'Data dokumen kontrak berhasil disimpan.'
    ]);
}

return redirect()
    ->route('kontrak.show', $kontrak->id)
    ->with(
        'success',
        'Data dokumen kontrak berhasil disimpan.'
    );
        } catch (\RuntimeException $e) {

            return redirect()
                ->route('kontrak')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Menampilkan detail kontrak dan halaman dokumen kontrak.
     */
    public function show($id)
    {
        $kontrak = Kontrak::with([
            'kerjaSama.tenant',
            'kerjaSama.lokasi',
            'tenant',
            'lokasi',
        ])->findOrFail($id);

        $apIndonesia = $this->getDataPerusahaan();

return view('kontrak.dokumen', compact('kontrak', 'apIndonesia'));
    }

    /**
     * Menampilkan halaman pengisian dokumen kontrak.
     */
    public function document($id)
    {
        $kontrak = Kontrak::with([
            'kerjaSama.tenant',
            'kerjaSama.lokasi',
            'tenant',
            'lokasi',
        ])->findOrFail($id);

        if ($kontrak->status === 'Selesai') {
            return redirect()
                ->route('kontrak.show', $kontrak->id)
                ->with(
                    'error',
                    'Dokumen kontrak yang sudah selesai tidak dapat diubah.'
                );
        }

        $apIndonesia = $this->getDataPerusahaan();

return view('kontrak.dokumen', compact('kontrak', 'apIndonesia'));
    }
    /**
     * Menyimpan data dokumen kontrak.
     */
    public function updateDokumen(Request $request, $id)
    {
        $kontrak = Kontrak::with([
            'kerjaSama.tenant',
            'kerjaSama.lokasi',
            'tenant',
            'lokasi',
        ])->findOrFail($id);

        // Kontrak yang sudah selesai tidak dapat diubah.
        if ($kontrak->status === 'Selesai') {
            return redirect()
                ->route('kontrak.show', $kontrak->id)
                ->with(
                    'error',
                    'Dokumen kontrak yang sudah selesai tidak dapat diubah.'
                );
        }

        // Validasi data formulir.
        $validated = $request->validate([
            'nomor_kontrak_mitra' => [
                'nullable',
                'string',
                'max:255',
            ],
            'logo_mitra' => [
                $kontrak->logo_mitra_path ? 'nullable' : 'required',
                'image',
                'mimes:jpg,jpeg,png',
                'max:2048',
            ],
            'periode_evaluasi_tarif' => [
                'required',
                'integer',
                'min:1',
            ],
            'satuan_evaluasi_tarif' => [
                'required',
                'in:bulan,tahun',
            ],
            'klausul_kepemilikan_fasilitas' => [
                'required',
                'in:mitra,ap_indonesia',
            ],
            'nama_pic_mitra_snapshot' => [
                'required',
                'string',
                'max:255',
            ],
            'jabatan_pic_mitra_snapshot' => [
                'required',
                'string',
                'max:255',
            ],
            'alamat_mitra_snapshot' => [
                'required',
                'string',
                'max:5000',
            ],
            'telepon_mitra_snapshot' => [
                'required',
                'string',
                'max:50',
            ],
            'fax_mitra_snapshot' => [
                'nullable',
                'string',
                'max:50',
            ],
            'email_mitra_snapshot' => [
                'required',
                'email',
                'max:255',
            ],
            'nama_penandatangan_mitra' => [
                'required',
                'string',
                'max:255',
            ],
            'jabatan_penandatangan_mitra' => [
                'required',
                'string',
                'max:255',
            ],
            'lampiran_perjanjian' => [
                'required',
                'array',
                'min:1',
            ],
            'lampiran_perjanjian.*' => [
                'required',
                'string',
                'distinct',
                'in:SUP,BAK,Denah Fasilitas Komersial,Perjanjian Tingkat Layanan',
            ],
        ], [
            'logo_mitra.required' => 'Logo Mitra wajib diunggah.',
            'logo_mitra.image' => 'Logo Mitra harus berupa gambar.',
            'logo_mitra.mimes' => 'Logo Mitra harus berformat JPG, JPEG, atau PNG.',
            'logo_mitra.max' => 'Ukuran logo Mitra maksimal 2 MB.',
            'periode_evaluasi_tarif.required' => 'Periode evaluasi tarif wajib diisi.',
            'periode_evaluasi_tarif.min' => 'Periode evaluasi tarif minimal 1.',
            'klausul_kepemilikan_fasilitas.required' => 'Silakan pilih klausul kepemilikan fasilitas.',
            'nama_pic_mitra_snapshot.required' => 'Nama PIC Mitra wajib diisi.',
            'jabatan_pic_mitra_snapshot.required' => 'Jabatan PIC Mitra wajib diisi.',
            'alamat_mitra_snapshot.required' => 'Alamat Mitra wajib diisi.',
            'telepon_mitra_snapshot.required' => 'Nomor telepon Mitra wajib diisi.',
            'email_mitra_snapshot.required' => 'Email Mitra wajib diisi.',
            'email_mitra_snapshot.email' => 'Format email Mitra tidak valid.',
            'nama_penandatangan_mitra.required' => 'Nama penandatangan Mitra wajib diisi.',
            'jabatan_penandatangan_mitra.required' => 'Jabatan penandatangan Mitra wajib diisi.',
            'lampiran_perjanjian.required' => 'Lampiran perjanjian wajib dipilih.',
        ]);

        // SUP dan BAK merupakan lampiran wajib.
        $lampiran = $validated['lampiran_perjanjian'];

        if (
            !in_array('SUP', $lampiran, true) ||
            !in_array('BAK', $lampiran, true)
        ) {
            return back()
                ->withInput()
                ->with(
                    'error',
                    'Lampiran SUP dan BAK wajib disertakan.'
                );
        }

        // Pastikan data utama tersedia.
        $kerjaSama = $kontrak->kerjaSama;
        $tenant = $kontrak->tenant;
        $lokasi = $kontrak->lokasi;

        if (!$kerjaSama || !$tenant || !$lokasi) {
            return back()
                ->withInput()
                ->with(
                    'error',
                    'Data Kerja Sama, Tenant, atau Lokasi tidak ditemukan.'
                );
        }

        // Simpan lokasi logo lama sebelum diperbarui.
        $logoPathLama = $kontrak->logo_mitra_path;

        // Simpan logo baru jika pengguna mengunggahnya.
        $logoPath = $logoPathLama;

        if ($request->hasFile('logo_mitra')) {
            $logoPathBaru = $request->file('logo_mitra')->store(
                'kontrak-logo-mitra',
                'local'
            );

            if (!$logoPathBaru) {
                return back()
                    ->withInput()
                    ->with('error', 'Logo Mitra gagal disimpan.');
            }

            $logoPath = $logoPathBaru;
        }

        // Simpan snapshot agar perubahan data master
        // tidak mengubah isi dokumen kontrak yang sudah dibuat.
        $dataKerjaSama = [
            'nomor_kerja_sama' => $kerjaSama->nomor_kerja_sama,
            'mitra_usaha' => $kerjaSama->mitra_usaha,
            'brand' => $kerjaSama->brand,
            'jenis_usaha' => $kerjaSama->jenis_usaha,
            'bentuk_kerja_sama' => $kerjaSama->bentuk_kerja_sama,
            'tanggal_mulai' => $kerjaSama->tanggal_mulai,
            'tanggal_berakhir' => $kerjaSama->tanggal_berakhir,
        ];

        $dataLokasi = [
            'terminal' => $lokasi->terminal,
            'lantai' => $lokasi->lantai,
            'jenis_ruangan' => $lokasi->jenis_ruangan,
            'nomor_ro' => $lokasi->nomor_ro,
            'kode_ruang' => $lokasi->kode_ruang,
            'lokasi' => $lokasi->lokasi,
            'luas_area' => $lokasi->luas_area,
        ];

        // Simpan seluruh data dokumen ke tabel kontraks.
        $kontrak->update([
            'nomor_kontrak_mitra' => $validated['nomor_kontrak_mitra'] ?? null,
            'logo_mitra_path' => $logoPath,
            'tanggal_dokumen' => now()->toDateString(),

            'periode_evaluasi_tarif' => $validated['periode_evaluasi_tarif'],
            'satuan_evaluasi_tarif' => $validated['satuan_evaluasi_tarif'],
            'klausul_kepemilikan_fasilitas' => $validated['klausul_kepemilikan_fasilitas'],

            'nama_mitra_snapshot' => $tenant->mitra_usaha
                ?: $kerjaSama->mitra_usaha,

            'nama_pic_mitra_snapshot' => $validated['nama_pic_mitra_snapshot'],
            'jabatan_pic_mitra_snapshot' => $validated['jabatan_pic_mitra_snapshot'],
            'alamat_mitra_snapshot' => $validated['alamat_mitra_snapshot'],
            'telepon_mitra_snapshot' => $validated['telepon_mitra_snapshot'],
            'fax_mitra_snapshot' => $validated['fax_mitra_snapshot'] ?? null,
            'email_mitra_snapshot' => $validated['email_mitra_snapshot'],

            'nama_penandatangan_mitra' => $validated['nama_penandatangan_mitra'],
            'jabatan_penandatangan_mitra' => $validated['jabatan_penandatangan_mitra'],

            'data_kerja_sama_snapshot' => $dataKerjaSama,
            'data_lokasi_snapshot' => $dataLokasi,

            'lampiran_perjanjian' => array_values(
                array_unique($lampiran)
            ),
        ]);

        // Hapus logo lama setelah data baru berhasil diperbarui.
        if (
            isset($logoPathBaru) &&
            $logoPathLama &&
            $logoPathLama !== $logoPathBaru &&
            Storage::disk('local')->exists($logoPathLama)
        ) {
            Storage::disk('local')->delete($logoPathLama);
        }

        return redirect()
            ->route('kontrak.show', $kontrak->id)
            ->with(
                'success',
                'Data dokumen kontrak berhasil disimpan.'
            );
    }

    
    /**
     * Menampilkan halaman upload dokumen kontrak final.
     */
    public function formUploadFinal($id)
    {
        $kontrak = Kontrak::findOrFail($id);

        return view('kontrak.upload-final', [
            'kontrak' => $kontrak,
        ]);
    }

    
/**
 * Memproses upload dokumen kontrak final.
 */
public function storeUploadFinal(Request $request, $id)
{
    $kontrak = Kontrak::findOrFail($id);
    // Validasi file dan konfirmasi tanda tangan.
    $validated = $request->validate([
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
        'final_pdf.file' => 'File yang diunggah tidak valid.',
        'final_pdf.mimes' => 'Dokumen harus menggunakan format PDF.',
        'final_pdf.max' => 'Ukuran PDF maksimal 10 MB.',
        'final_signature_confirmed.required' => 'Konfirmasi tanda tangan wajib dicentang.',
        'final_signature_confirmed.accepted' => 'Dokumen harus dikonfirmasi telah ditandatangani kedua pihak.',
    ]);

    // Simpan file PDF ke penyimpanan lokal Laravel.
    $file = $request->file('final_pdf');

    $namaFile = 'kontrak_final_' . $kontrak->id . '_' .
        now()->format('YmdHis') . '.pdf';

    $pathBaru = $file->storeAs(
        'kontrak/final',
        $namaFile,
        'local'
    );

    if (!$pathBaru) {
        return back()
            ->withInput()
            ->with('error', 'Dokumen kontrak final gagal disimpan.');
    }

    // Simpan lokasi file dan informasi upload ke database.
    $pathLama = $kontrak->final_pdf_path;

    $kontrak->final_pdf_path = $pathBaru;
$kontrak->final_uploaded_at = now();
$kontrak->final_signature_confirmed = true;

// Tandai kontrak telah selesai ditandatangani.
$kontrak->status = 'Selesai';

$kontrak->save();

    // Hapus file sebelumnya jika ada penggantian dokumen.
    if (
        $pathLama &&
        $pathLama !== $pathBaru &&
        Storage::disk('local')->exists($pathLama)
    ) {
        Storage::disk('local')->delete($pathLama);
    }

   return redirect()
    ->route('kontrak')
    ->with(
        'success',
        'Dokumen kontrak final berhasil diunggah dan disimpan.'
    );
}

public function finalPdf($id)
{
    $kontrak = Kontrak::findOrFail($id);

    if (
        !$kontrak->final_pdf_path ||
        !Storage::disk('local')->exists($kontrak->final_pdf_path)
    ) {
        abort(404, 'Dokumen kontrak final tidak ditemukan.');
    }

    return Storage::disk('local')->response(
        $kontrak->final_pdf_path,
        basename($kontrak->final_pdf_path),
        [
            'Content-Type' => 'application/pdf',
        ],
        'inline'
    );
}
    /**
     * Menampilkan halaman upload dokumen kontrak final.
     */
    public function previewDocument($kerjaSamaId)
    {
        $kerjaSama = KerjaSama::with([
            'tenant',
            'lokasi',
        ])->findOrFail($kerjaSamaId);

        // Pastikan data Tenant dan Lokasi tersedia.
        if (!$kerjaSama->tenant || !$kerjaSama->lokasi) {
            return redirect()
                ->route('kontrak')
                ->with(
                    'error',
                    'Data Tenant atau Lokasi pada Kerja Sama tidak ditemukan.'
                );
        }

        return view('kontrak.dokumen', [
            'kontrak' => null,
            'kerjaSama' => $kerjaSama,
            'tenant' => $kerjaSama->tenant,
            'lokasi' => $kerjaSama->lokasi,
            'isPreview' => true,
            'apIndonesia' => $this->getDataPerusahaan(),
        ]);
    }


    /**
     * Menghasilkan dokumen Perjanjian Pokok dalam format Word.
     */
    public function downloadWord($id)
    {
        $kontrak = Kontrak::with([
            'kerjaSama',
            'tenant',
            'lokasi',
        ])->findOrFail($id);

        // Pastikan data dokumen sudah dilengkapi.
        if (
            !$kontrak->tanggal_dokumen ||
            !$kontrak->logo_mitra_path ||
            !$kontrak->periode_evaluasi_tarif ||
            !$kontrak->klausul_kepemilikan_fasilitas ||
            !$kontrak->nama_mitra_snapshot ||
            !$kontrak->nama_pic_mitra_snapshot ||
            !$kontrak->nama_penandatangan_mitra ||
            !$kontrak->data_kerja_sama_snapshot ||
            !$kontrak->data_lokasi_snapshot
        ) {
            return redirect()
                ->route('kontrak.show', $kontrak->id)
                ->with(
                    'error',
                    'Lengkapi data dokumen kontrak terlebih dahulu.'
                );
        }

        // Ambil data snapshot kontrak.
        $ks = $kontrak->data_kerja_sama_snapshot ?? [];
        $dataLokasi = $kontrak->data_lokasi_snapshot ?? [];

        $tanggalDokumen = Carbon::parse(
            $kontrak->tanggal_dokumen
        );

        $tanggalMulai = $ks['tanggal_mulai'] ?? null;
        $tanggalBerakhir = $ks['tanggal_berakhir'] ?? null;

        // Menghitung durasi kerja sama.
        $durasi = '-';

        if ($tanggalMulai && $tanggalBerakhir) {
            $durasi = Carbon::parse($tanggalMulai)
                ->diffInMonths(Carbon::parse($tanggalBerakhir));
        }

        $namaUsaha = $ks['jenis_usaha'] ?? '-';
        $namaLokasi = $dataLokasi['lokasi'] ?? '-';
        $namaMitra = $kontrak->nama_mitra_snapshot ?? '-';

        // Inisialisasi PHPWord.
        $phpWord = new PhpWord();

        $phpWord->setDefaultFontName('Arial');
        $phpWord->setDefaultFontSize(10);

        // Pengaturan halaman A4.
        $section = $phpWord->addSection([
            'paperSize' => 'A4',
            'orientation' => 'portrait',
            'marginTop' => 1800,
            'marginBottom' => 1600,
            'marginLeft' => 1800,
            'marginRight' => 1800,
        ]);

        // Format teks.
        $center = [
            'alignment' => 'center',
            'spaceAfter' => 100,
        ];

        $boldCenter = [
            'bold' => true,
            'size' => 11,
        ];

        $normal = [
            'size' => 10,
        ];

        // Helper untuk menambahkan paragraf.
        $addText = function ($text, $style = []) use ($section) {
            $section->addText(
                $text,
                array_merge(['size' => 10], $style),
                ['spaceAfter' => 100]
            );
        };

        // Helper untuk judul pasal.
        $addHeading = function ($text) use ($section) {
            $section->addText(
                $text,
                ['bold' => true, 'size' => 11],
                [
                    'alignment' => 'center',
                    'spaceBefore' => 150,
                    'spaceAfter' => 80,
                ]
            );
        };

        // Memastikan file logo tersedia.
        $logoPath = Storage::disk('local')->path(
            $kontrak->logo_mitra_path
        );

        if (!file_exists($logoPath)) {
            return redirect()
                ->route('kontrak.show', $kontrak->id)
                ->with(
                    'error',
                    'File logo Mitra tidak ditemukan. Silakan unggah ulang logo.'
                );
        }

        // Mulai menyusun dokumen.
                // =========================
        // HALAMAN 1
        // =========================

        // Menambahkan logo Mitra.
        $section->addImage($logoPath, [
            'width' => 100,
            'height' => 65,
            'alignment' => 'center',
        ]);

        // Judul dokumen.
        $section->addText(
            'PERJANJIAN POKOK',
            $boldCenter,
            $center
        );

        $section->addText(
            'TENTANG',
            $boldCenter,
            $center
        );

        $section->addText(
            'KERJA SAMA PEMANFAATAN FASILITAS KOMERSIAL PT ANGKASA PURA INDONESIA',
            ['bold' => true, 'size' => 11],
            $center
        );

        $section->addText(
            'UNTUK KEGIATAN USAHA ' . $namaUsaha,
            ['bold' => true, 'size' => 11],
            $center
        );

        $section->addText(
            'DI ' . $namaLokasi,
            ['bold' => true, 'size' => 11],
            $center
        );

        // Nomor dokumen.
        $addText(
            'NOMOR : ' . $kontrak->nomor_kontrak
        );

        $addText(
            'NOMOR MITRA : ' .
            ($kontrak->nomor_kontrak_mitra ?: '-')
        );

        // Tanggal pembuatan dokumen.
        $addText(
            'Perjanjian Pokok ini dibuat dan ditandatangani pada:'
        );

        $addText(
            'HARI : ' .
            $tanggalDokumen->locale('id')->translatedFormat('l')
        );

        $addText(
            'TANGGAL : ' .
            $tanggalDokumen->locale('id')->translatedFormat('d F Y')
        );

        $addText('Oleh dan antara:');

        // =========================
        // PIHAK PERTAMA
        // =========================

        $addText(
            'I. PT ANGKASA PURA INDONESIA',
            ['bold' => true]
        );

        $addText(
            'Alamat : Jalan Raya Ir. H. Juanda No. 1, Segoro Tambak, Kecamatan Sedati, Kabupaten Sidoarjo, Jawa Timur 61253'
        );

        $addText(
            'Dalam hal ini diwakili oleh : Laksma TNI Muhammad Tohir'
        );

        $addText(
            'Selaku : General Manager PT Angkasa Pura Indonesia Cabang Bandara Internasional Juanda'
        );

        $addText(
            'Dengan demikian sah bertindak untuk dan atas nama PT Angkasa Pura Indonesia, selanjutnya dalam Perjanjian Pokok ini disebut AP INDONESIA.'
        );

        // =========================
        // PIHAK KEDUA
        // =========================

        $addText(
            'II. ' . $namaMitra,
            ['bold' => true]
        );

        $addText(
            'Alamat : ' .
            ($kontrak->alamat_mitra_snapshot ?? '-')
        );

        $addText(
            'Dalam hal ini diwakili oleh : ' .
            ($kontrak->nama_penandatangan_mitra ?? '-')
        );

        $addText(
            'Selaku : ' .
            ($kontrak->jabatan_penandatangan_mitra ?? '-')
        );

        $addText(
            'Dengan demikian sah bertindak untuk dan atas nama ' .
            $namaMitra .
            ', selanjutnya dalam Perjanjian Pokok ini disebut MITRA.'
        );

        // Penutup identitas para pihak.
        $addText(
            'Selanjutnya, AP INDONESIA dan MITRA secara bersama-sama disebut "PARA PIHAK".'
        );
                // =========================
        // HALAMAN 2
        // =========================

        $section->addPageBreak();

        // Pendahuluan.
        $addText(
            'PARA PIHAK terlebih dahulu menerangkan hal-hal sebagai berikut:'
        );

        $addText(
            'a. AP INDONESIA merupakan perseroan terbatas yang bergerak di bidang pelayanan jasa kebandarudaraan.'
        );

        $addText(
            'b. MITRA dalam rangka melaksanakan kegiatan usahanya bermaksud memanfaatkan Fasilitas Komersial milik AP INDONESIA.'
        );

        $addText(
            'c. Bahwa PARA PIHAK telah menyepakati pemanfaatan Fasilitas Komersial sebagaimana dituangkan dalam Berita Acara Kesepakatan [Nomor BAK belum tersedia] (Selanjutnya disebut "Berita Acara Kesepakatan") dan dokumen lain (sebagaimana relevan).'
        );

        $addText(
            'Berdasarkan hal-hal tersebut di atas, PARA PIHAK menyatakan sepakat untuk menandatangani Perjanjian Pokok Tentang Kerja Sama Pemanfaatan Fasilitas Komersial PT Angkasa Pura Indonesia untuk Kegiatan Usaha ' .
            $namaUsaha .
            ' di ' .
            $namaLokasi .
            ' (Selanjutnya disebut "Perjanjian Pokok"), dengan ketentuan dan syarat-syarat sebagai berikut:'
        );

        // =========================
        // PASAL 1
        // POKOK PERJANJIAN
        // =========================

        $addHeading('PASAL 1');

        $addHeading('POKOK PERJANJIAN');

        $addText(
            '(1) AP INDONESIA memberikan izin pemanfaatan Fasilitas Komersial kepada MITRA untuk melakukan kegiatan usaha, dan MITRA sepakat untuk memanfaatkan Fasilitas Komersial milik AP INDONESIA, dengan rincian sebagaimana terlampir pada Perjanjian Pokok ini.'
        );

        $addText(
            '(2) Bentuk Kerja Sama Pemanfaatan Fasilitas Komersial adalah:'
        );

        // Pilihan bentuk kerja sama.
        // Perlu disesuaikan dengan pilihan resmi pada data kerja sama.
        $addText(
            '[Bentuk kerja sama yang disepakati belum tersedia pada data kontrak.]'
        );

        $addText(
            '(3) Atas pemanfaatan Fasilitas Komersial sebagaimana tersebut pada ayat (1) Pasal ini MITRA bersedia membayar biaya dan/atau imbalan sesuai bentuk kerja sama yang disepakati, dengan detail rincian dan cara pembayaran sebagaimana terlampir pada Perjanjian Pokok ini.'
        );
                // =========================
        // HALAMAN 3
        // =========================

        $section->addPageBreak();

        // =========================
        // PASAL 2
        // JANGKA WAKTU
        // =========================

        $addHeading('PASAL 2');

        $addHeading('JANGKA WAKTU');

        $tanggalMulaiText = $tanggalMulai
            ? Carbon::parse($tanggalMulai)->locale('id')->translatedFormat('d F Y')
            : '-';

        $tanggalBerakhirText = $tanggalBerakhir
            ? Carbon::parse($tanggalBerakhir)->locale('id')->translatedFormat('d F Y')
            : '-';

        $addText(
            '(1) Perjanjian Pokok ini berlaku untuk jangka waktu ' .
            $durasi .
            ' (bulan), terhitung sejak tanggal ' .
            $tanggalMulaiText .
            ' sampai dengan tanggal ' .
            $tanggalBerakhirText .
            '.'
        );

        $addText(
            '(2) Perpanjangan jangka waktu Perjanjian Pokok dilakukan berdasarkan kesepakatan PARA PIHAK sesuai ketentuan yang berlaku.'
        );

        // =========================
        // PASAL 3
        // PENINJAUAN TARIF
        // =========================

        $addHeading('PASAL 3');

        $addHeading('PENINJAUAN TARIF');

        $periodeEvaluasi = $kontrak->periode_evaluasi_tarif;
        $satuanEvaluasi = $kontrak->satuan_evaluasi_tarif;

        $addText(
            '(1) Peninjauan tarif atas pemanfaatan Fasilitas Komersial dilakukan setiap ' .
            $periodeEvaluasi .
            ' ' .
            $satuanEvaluasi .
            ' atau sesuai kesepakatan PARA PIHAK berdasarkan ketentuan yang berlaku.'
        );

        // =========================
        // PASAL 4
        // PELAKSANAAN PERJANJIAN
        // =========================

        $addHeading('PASAL 4');

        $addHeading('PELAKSANAAN PERJANJIAN');

        if ($kontrak->klausul_kepemilikan_fasilitas === 'mitra') {
            $addText(
                '(1) Fasilitas atau sarana yang dibangun dan/atau disediakan oleh MITRA dalam rangka pelaksanaan kegiatan usaha menjadi milik MITRA, dengan tetap memperhatikan ketentuan dalam Perjanjian Pokok dan peraturan yang berlaku.'
            );
        } elseif ($kontrak->klausul_kepemilikan_fasilitas === 'ap_indonesia') {
            $addText(
                '(1) Fasilitas atau sarana yang dibangun dan/atau disediakan oleh MITRA dalam rangka pelaksanaan kegiatan usaha menjadi milik AP INDONESIA, dengan tetap memperhatikan ketentuan dalam Perjanjian Pokok dan peraturan yang berlaku.'
            );
        }        
                // =========================
        // HALAMAN 4
        // =========================

        $section->addPageBreak();

        // =========================
        // PASAL 5
        // LAMPIRAN PERJANJIAN
        // =========================

        $addHeading('PASAL 5');
        $addHeading('LAMPIRAN PERJANJIAN');

        $addText(
            '(1) Dokumen-dokumen yang menjadi lampiran Perjanjian Pokok ini merupakan bagian yang tidak terpisahkan dari Perjanjian Pokok.'
        );

        $addText('Lampiran Perjanjian Pokok meliputi:');

        // Ambil daftar lampiran yang dipilih.
        $daftarLampiran = $kontrak->lampiran_perjanjian ?? [];

        if (is_string($daftarLampiran)) {
            $daftarLampiran = json_decode($daftarLampiran, true) ?? [];
        }

        if (!is_array($daftarLampiran)) {
            $daftarLampiran = [];
        }

        $nomorLampiran = 1;

        foreach ($daftarLampiran as $lampiran) {
            $addText(
                $nomorLampiran . '. ' . $lampiran
            );

            $nomorLampiran++;
        }

        // =========================
        // PASAL 6
        // KORESPONDENSI
        // =========================

        $addHeading('PASAL 6');
        $addHeading('KORESPONDENSI');

        // Catatan:
        // Redaksi resmi Pasal 6 perlu dicocokkan dengan formulir.
        // Data kontak berikut ditampilkan sebagai informasi para pihak.

        $addText(
            'Alamat korespondensi MITRA:'
        );

        $addText(
            'Nama: ' . ($kontrak->nama_mitra_snapshot ?? '-')
        );

        $addText(
            'Alamat: ' . ($kontrak->alamat_mitra_snapshot ?? '-')
        );

        $addText(
            'Telepon: ' . ($kontrak->telepon_mitra_snapshot ?? '-')
        );

        $addText(
            'Fax: ' . ($kontrak->fax_mitra_snapshot ?: '-')
        );

        $addText(
            'Email: ' . ($kontrak->email_mitra_snapshot ?? '-')
        );

        $addText(
            'PIC: ' . ($kontrak->nama_pic_mitra_snapshot ?? '-')
        );

        // =========================
        // PASAL 7
        // PENUTUP
        // =========================

        $addHeading('PASAL 7');
        $addHeading('PENUTUP');

        // Redaksi ini merupakan penutup sementara.
        // Sesuaikan dengan redaksi pada formulir resmi.

        $addText(
            'Demikian Perjanjian Pokok ini dibuat untuk dipahami dan dilaksanakan oleh PARA PIHAK sesuai dengan ketentuan yang disepakati.'
        );

        $addText(
            'Perjanjian Pokok ini ditandatangani oleh PARA PIHAK pada tanggal sebagaimana tercantum dalam dokumen ini.'
        );

        // =========================
        // BAGIAN TANDA TANGAN
        // =========================

        $section->addTextBreak(2);

        $tableStyle = [
            'borderSize' => 0,
            'borderColor' => 'FFFFFF',
            'cellMargin' => 100,
            'alignment' => \PhpOffice\PhpWord\SimpleType\JcTable::CENTER,
        ];

        $cellStyle = [
            'valign' => 'center',
        ];

        $phpWord->addTableStyle('TabelTandaTangan', $tableStyle);

        $table = $section->addTable('TabelTandaTangan');

        // Baris jabatan para pihak.
        $table->addRow();

        $table->addCell(4500, $cellStyle)->addText(
            'AP INDONESIA',
            ['bold' => true],
            ['alignment' => 'center']
        );

        $table->addCell(4500, $cellStyle)->addText(
            'MITRA',
            ['bold' => true],
            ['alignment' => 'center']
        );

        // Ruang tanda tangan.
        $table->addRow(1800);

        $table->addCell(4500, $cellStyle)->addText(
            ''
        );

        $table->addCell(4500, $cellStyle)->addText(
            ''
        );

        // Nama penandatangan.
        $table->addRow();

        $table->addCell(4500, $cellStyle)->addText(
            'Laksma TNI Muhammad Tohir',
            ['bold' => true, 'underline' => 'single'],
            ['alignment' => 'center']
        );

        $table->addCell(4500, $cellStyle)->addText(
            $kontrak->nama_penandatangan_mitra ?? '-',
            ['bold' => true, 'underline' => 'single'],
            ['alignment' => 'center']
        );

        // Jabatan penandatangan.
        $table->addRow();

        $table->addCell(4500, $cellStyle)->addText(
            'General Manager',
            [],
            ['alignment' => 'center']
        );

        $table->addCell(4500, $cellStyle)->addText(
            $kontrak->jabatan_penandatangan_mitra ?? '-',
            [],
            ['alignment' => 'center']
        );
                // =========================
        // GENERATE FILE WORD
        // =========================

        // Membuat nama file berdasarkan nomor kontrak.
        $namaFile = 'Perjanjian_Pokok_' .
            $kontrak->nomor_kontrak . '.docx';

        // Mengganti karakter "/" agar aman sebagai nama file.
        $namaFile = str_replace('/', '_', $namaFile);

        // Membuat file sementara.
        $pathSementara = tempnam(
            sys_get_temp_dir(),
            'kontrak_'
        );

        try {

            // Membuat writer Word.
            $writer = IOFactory::createWriter(
                $phpWord,
                'Word2007'
            );

            // Menyimpan dokumen Word.
            $writer->save($pathSementara);

            // Mengirim file Word untuk diunduh.
            return response()
                ->download(
                    $pathSementara,
                    $namaFile,
                    [
                        'Content-Type' =>
                            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    ]
                )
                ->deleteFileAfterSend(true);

        } catch (\Throwable $e) {

            // Menghapus file sementara jika proses gagal.
            if (file_exists($pathSementara)) {
                unlink($pathSementara);
            }

            report($e);

            return redirect()
                ->route('kontrak.show', $kontrak->id)
                ->with(
                    'error',
                    'Dokumen Word gagal dibuat. Silakan periksa kembali data kontrak.'
                );
        }
    }
    
    /**
     * Menghasilkan dokumen Perjanjian Pokok dalam format PDF.
     */
    public function downloadPdf($id)
    {
        $kontrak = Kontrak::with([
            'kerjaSama.tenant',
            'kerjaSama.lokasi',
            'tenant',
            'lokasi',
        ])->findOrFail($id);

        // Pastikan data dokumen sudah dilengkapi.
        if (
            !$kontrak->tanggal_dokumen ||
            !$kontrak->logo_mitra_path ||
            !$kontrak->periode_evaluasi_tarif ||
            !$kontrak->klausul_kepemilikan_fasilitas ||
            !$kontrak->nama_mitra_snapshot ||
            !$kontrak->nama_pic_mitra_snapshot ||
            !$kontrak->nama_penandatangan_mitra ||
            !$kontrak->data_kerja_sama_snapshot ||
            !$kontrak->data_lokasi_snapshot
        ) {
            return redirect()
                ->route('kontrak.show', $kontrak->id)
                ->with(
                    'error',
                    'Lengkapi data dokumen kontrak terlebih dahulu.'
                );
        }

        // Siapkan data untuk halaman dokumen PDF.
        $data = [
            'kontrak' => $kontrak,
            'apIndonesia' => $this->getDataPerusahaan(),
            'isPdf' => true,
        ];

        try {
            // Membuat PDF dari halaman dokumen kontrak.
            $pdf = Pdf::loadView(
                'kontrak.dokumen',
                $data
            )->setPaper('a4', 'portrait');

            // Membuat nama file PDF.
            $namaFile = 'Perjanjian_Pokok_' .
                $kontrak->nomor_kontrak . '.pdf';

            $namaFile = str_replace('/', '_', $namaFile);

            // Mengunduh PDF.
            return $pdf->download($namaFile);

        } catch (\Throwable $e) {
            report($e);

            return redirect()
                ->route('kontrak.show', $kontrak->id)
                ->with(
                    'error',
                    'Dokumen PDF gagal dibuat. Silakan periksa kembali data kontrak.'
                );
        }
    }
/**
 * Menyimpan keputusan tenant terkait perpanjangan kontrak.
 */
public function storeKonfirmasiPerpanjangan(Request $request, $id)
{
    $kontrak = Kontrak::findOrFail($id);

    // Validasi keputusan tenant.
    $validated = $request->validate([
        'status_perpanjangan' => [
            'required',
            'in:Ingin Memperpanjang,Tidak Memperpanjang',
        ],
    ], [
        'status_perpanjangan.required' =>
            'Keputusan tenant wajib dipilih.',

        'status_perpanjangan.in' =>
            'Pilihan keputusan tenant tidak valid.',
    ]);

    // Simpan keputusan perpanjangan ke database.
    $kontrak->status_perpanjangan =
        $validated['status_perpanjangan'];

    $kontrak->save();

    return redirect()
        ->route('kontrak')
        ->with(
            'success',
            'Konfirmasi perpanjangan kontrak berhasil disimpan.'
        );
}
}