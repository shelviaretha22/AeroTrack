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
        'nama' => $perusahaan?->nama_perusahaan ?: 'PT ANGKASA PURA INDONESIA',
        // Bila master perusahaan belum diisi, pakai data bawaan Juanda.
        // Isi lewat menu Profil > Perusahaan agar bisa diubah.
        'domisili' => $perusahaan?->domisili ?: 'Sidoarjo',
        'alamat' => $perusahaan?->alamat
            ?: 'Jalan Raya Ir. H. Juanda No. 1, Segoro Tambak, Kecamatan Sedati, Kabupaten Sidoarjo, Jawa Timur 61253',
        'penandatangan' => $perusahaan?->nama_penandatangan
            ?: 'Laksma TNI Muhammad Tohir',
        'jabatan' => $perusahaan?->jabatan_penandatangan
            ?: 'General Manager PT Angkasa Pura Indonesia Cabang Bandara Internasional Juanda',
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
        | - Dokumen Berita Acara sudah dibuat.
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
     * Membuat kontrak dari kerja sama yang Berita Acara-nya sudah dibuat.
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
            'kode_tampilan' => $lokasi->kode_tampilan,
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
     * Mengunduh dokumen Perjanjian Pokok dalam format Word (DOCX).
     * Susunan dan isi dibuat sama dengan versi PDF.
     */
    public function downloadWord($id)
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

        // Memastikan file logo Mitra tersedia.
        $logoMitraPath = Storage::disk('local')->path(
            $kontrak->logo_mitra_path
        );

        if (!file_exists($logoMitraPath)) {
            return redirect()
                ->route('kontrak.show', $kontrak->id)
                ->with(
                    'error',
                    'File logo Mitra tidak ditemukan. Silakan unggah ulang logo.'
                );
        }

        // Membuat nama file berdasarkan nomor kontrak.
        $namaFile = str_replace(
            '/',
            '_',
            'Perjanjian_Pokok_' . $kontrak->nomor_kontrak . '.docx'
        );

        // Membuat file sementara.
        $pathSementara = tempnam(sys_get_temp_dir(), 'kontrak_');

        try {
            $phpWord = $this->susunDokumenWord(
                $kontrak,
                $logoMitraPath,
                public_path('images/angkasa-pura-logo.png')
            );

            IOFactory::createWriter($phpWord, 'Word2007')
                ->save($pathSementara);

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
     * Menyusun isi dokumen Word. Isi mengikuti kontrak.dokumen-pdf.
     */
    private function susunDokumenWord(
        Kontrak $kontrak,
        string $logoMitraPath,
        string $logoApPath
    ): PhpWord {

        $kerjaSama = $kontrak->kerjaSama;
        $tenant = $kontrak->tenant ?? $kerjaSama?->tenant;
        $lokasi = $kontrak->lokasi ?? $kerjaSama?->lokasi;
        $ap = $this->getDataPerusahaan();

        $ks = $kontrak->data_kerja_sama_snapshot ?? [];
        $dl = $kontrak->data_lokasi_snapshot ?? [];

        // Data umum dokumen.
        $mitra = $kerjaSama?->mitra_usaha
            ?? $tenant?->mitra_usaha
            ?? $kontrak->nama_mitra_snapshot
            ?? '-';

        $jenisUsaha = $kerjaSama?->jenis_usaha
            ?? $tenant?->jenis_usaha
            ?? ($ks['jenis_usaha'] ?? '-');

        $namaLokasi = $lokasi?->lokasi ?? ($dl['lokasi'] ?? '-');
        $terminal = $lokasi?->terminal ?? ($dl['terminal'] ?? '-');
        $lantai = $lokasi?->lantai ?? ($dl['lantai'] ?? '-');
        $kodeRuang = $lokasi?->kode_tampilan
            ?: ($lokasi?->kode_ruang ?? ($dl['kode_ruang'] ?? '-'));

        $luas = data_get($dl, 'luas_area')
            ?? $lokasi?->luas_area
            ?? '-';

        $formatTanggal = function ($tanggal) {
            return $tanggal
                ? Carbon::parse($tanggal)->locale('id')->translatedFormat('d F Y')
                : '-';
        };

        $tanggalDokumen = Carbon::parse($kontrak->tanggal_dokumen)
            ->locale('id');

        $tanggalMulai = $formatTanggal(
            $kerjaSama?->tanggal_mulai ?? ($ks['tanggal_mulai'] ?? null)
        );
        $tanggalBerakhir = $formatTanggal(
            $kerjaSama?->tanggal_berakhir ?? ($ks['tanggal_berakhir'] ?? null)
        );

        $alamatMitra = $kontrak->alamat_mitra_snapshot
            ?: ($tenant?->alamat_perusahaan ?? '-');
        $domisiliMitra = $kontrak->domisili_mitra_snapshot ?: '-';

        $klausul = $kontrak->klausul_kepemilikan_fasilitas;
        $klausulText = $klausul === 'mitra'
            ? 'Fasilitas usaha yang dibangun tetap menjadi milik Mitra, dan fasilitas komersial dikembalikan sesuai ketentuan.'
            : ($klausul === 'ap_indonesia'
                ? 'Fasilitas usaha yang dibangun menjadi milik AP Indonesia tanpa kompensasi.'
                : '[Belum dipilih]');

        $labelLampiran = [
            'SUP' => 'Syarat-Syarat Umum Perjanjian (SUP)',
            'BAK' => 'Berita Acara Kesepakatan (BAK)',
            'Denah Fasilitas Komersial' => 'Denah Fasilitas Komersial',
            'Perjanjian Tingkat Layanan' => 'Perjanjian Tingkat Layanan',
            'Lainnya' => 'Dokumen Lainnya',
        ];

        $lampiran = $kontrak->lampiran_perjanjian ?? [];

        if (is_string($lampiran)) {
            $lampiran = json_decode($lampiran, true) ?? [];
        }

        if (!is_array($lampiran)) {
            $lampiran = [];
        }

        // Penandatangan.
        $namaApTtd = $ap['penandatangan'] ?: '[Nama Penandatangan AP Indonesia]';
        $jabatanApTtd = $ap['jabatan'] ?: '[Jabatan Penandatangan AP Indonesia]';
        $namaApPerusahaan = $ap['nama'] ?: 'PT ANGKASA PURA INDONESIA';

        // Abaikan nilai lama yang ternyata PIC Commercial AP Indonesia.
        $namaMitraTtd = $kontrak->nama_penandatangan_mitra;
        $jabatanMitraTtd = $kontrak->jabatan_penandatangan_mitra;

        if ($namaMitraTtd && $namaMitraTtd === $kerjaSama?->pic_commercial) {
            $namaMitraTtd = $tenant?->nama_pic;
        }

        if ($jabatanMitraTtd && $jabatanMitraTtd === $kerjaSama?->jabatan_pic_commercial) {
            $jabatanMitraTtd = $tenant?->jabatan_pic;
        }

        $namaMitraTtd = $namaMitraTtd ?: '-';
        $jabatanMitraTtd = $jabatanMitraTtd ?: '-';

        // =========================
        // PENGATURAN DOKUMEN
        // =========================

        // Escape karakter khusus XML (&, <, >) agar file tidak rusak,
        // misalnya pada jenis usaha "F&B".
        \PhpOffice\PhpWord\Settings::setOutputEscapingEnabled(true);

        $phpWord = new PhpWord();
        $phpWord->setDefaultFontName('Times New Roman');
        $phpWord->setDefaultFontSize(10.5);
        $phpWord->setDefaultParagraphStyle([
            'spaceBefore' => 0,
            'spaceAfter' => 135,
            'lineHeight' => 1.15,
        ]);

        // A4 dengan margin sama seperti PDF (14 mm atas/bawah, 20 mm kiri/kanan).
        $section = $phpWord->addSection([
            'paperSize' => 'A4',
            'orientation' => 'portrait',
            'marginTop' => 794,
            'marginBottom' => 794,
            'marginLeft' => 1134,
            'marginRight' => 1134,
            'footerHeight' => 400,
        ]);

        // Nomor halaman otomatis.
        $section->addFooter()->addPreserveText(
            'Halaman {PAGE} dari {NUMPAGES}',
            ['size' => 8.5, 'color' => '666666'],
            ['alignment' => 'center', 'spaceAfter' => 0]
        );

        $lebarTeks = 9638; // twips: 11906 - (2 x 1134)

        $polosTabel = [
            'borderSize' => 0,
            'borderColor' => 'FFFFFF',
            'cellMargin' => 0,
        ];

        $rata = ['alignment' => 'both'];
        $tengah = ['alignment' => 'center'];

        // Paragraf dengan potongan teks tebal.
        // Setiap bagian berupa string biasa atau [teks, true] untuk tebal.
        $tulis = function ($container, array $bagian, array $paragraf = []) {
            $run = $container->addTextRun(
                array_merge(['alignment' => 'both'], $paragraf)
            );

            foreach ($bagian as $b) {
                if (is_array($b)) {
                    $run->addText($b[0], ['bold' => true]);
                } else {
                    $run->addText($b);
                }
            }
        };

        // Judul pasal (dua baris, tengah, huruf besar).
        $judulPasal = function ($pasal, $judul) use ($section) {
            $section->addText(
                mb_strtoupper($pasal),
                ['bold' => true, 'size' => 11.5],
                ['alignment' => 'center', 'spaceBefore' => 200, 'spaceAfter' => 0, 'keepNext' => true]
            );
            $section->addText(
                mb_strtoupper($judul),
                ['bold' => true, 'size' => 11.5],
                ['alignment' => 'center', 'spaceAfter' => 80, 'keepNext' => true]
            );
        };

        // Menghitung ukuran gambar agar muat dalam kotak (satuan point).
        $ukuranGambar = function ($path, $maksLebar, $maksTinggi) {
            $info = @getimagesize($path);

            if (!$info || !$info[0] || !$info[1]) {
                return [$maksLebar, $maksTinggi];
            }

            $skala = min($maksLebar / $info[0], $maksTinggi / $info[1]);

            return [
                round($info[0] * $skala),
                round($info[1] * $skala),
            ];
        };

        // =========================
        // HALAMAN 1
        // =========================

        // Logo AP Indonesia (kiri) dan logo Mitra (kanan).
        $tabelLogo = $section->addTable(array_merge($polosTabel, [
            'width' => $lebarTeks,
            'unit' => 'dxa',
        ]));
        $tabelLogo->addRow(1250);

        $selKiri = $tabelLogo->addCell($lebarTeks / 2, ['valign' => 'center']);
        $selKanan = $tabelLogo->addCell($lebarTeks / 2, ['valign' => 'center']);

        if (is_file($logoApPath)) {
            [$w, $h] = $ukuranGambar($logoApPath, 110, 44);
            $selKiri->addImage($logoApPath, [
                'width' => $w,
                'height' => $h,
                'alignment' => 'left',
            ]);
        }

        [$w, $h] = $ukuranGambar($logoMitraPath, 110, 44);
        $selKanan->addImage($logoMitraPath, [
            'width' => $w,
            'height' => $h,
            'alignment' => 'right',
        ]);

        // Judul perjanjian.
        $section->addText(
            'PERJANJIAN POKOK',
            ['bold' => true, 'size' => 14, 'underline' => 'single'],
            ['alignment' => 'center', 'spaceBefore' => 200, 'spaceAfter' => 80]
        );

        foreach ([
            'TENTANG',
            'KERJA SAMA PEMANFAATAN FASILITAS KOMERSIAL PT ANGKASA PURA INDONESIA',
            'UNTUK KEGIATAN USAHA ' . $jenisUsaha,
            'DI ' . $namaLokasi,
        ] as $baris) {
            $section->addText(
                $baris,
                ['bold' => true, 'size' => 11],
                ['alignment' => 'center', 'spaceAfter' => 40]
            );
        }

        $section->addTextBreak(0);

        // Tabel tiga kolom: label, titik dua, isi.
        $tabelTigaKolom = function ($container, array $baris, array $lebar) use ($polosTabel) {
            $tabel = $container->addTable($polosTabel);

            foreach ($baris as [$label, $isi]) {
                $tabel->addRow();
                $tabel->addCell($lebar[0])->addText($label, [], ['spaceAfter' => 20]);
                $tabel->addCell($lebar[1])->addText(':', [], ['spaceAfter' => 20]);
                $tabel->addCell($lebar[2])->addText($isi, [], ['spaceAfter' => 20]);
            }
        };

        // Nomor dokumen.
        $tabelTigaKolom($section, [
            ['Nomor Dokumen Kontrak', $kontrak->nomor_kontrak],
            ['Nomor Kontrak Mitra', $kontrak->nomor_kontrak_mitra ?: '-'],
        ], [3000, 300, 5000]);

        $section->addTextBreak(0);

        // Hari dan tanggal perjanjian.
        $section->addText(
            'Perjanjian Pokok ini dibuat dan ditandatangani pada:',
            [],
            ['spaceAfter' => 40]
        );

        $tabelTigaKolom($section, [
            ['Hari', $tanggalDokumen->translatedFormat('l')],
            ['Tanggal', $tanggalDokumen->translatedFormat('d F Y')],
        ], [1100, 300, 8000]);

        $section->addTextBreak(0);

        $section->addText('Oleh dan antara:', [], ['spaceAfter' => 80]);

        // Pihak I: AP Indonesia.
        $section->addText(
            'I. ' . $namaApPerusahaan,
            ['bold' => true],
            ['spaceAfter' => 40]
        );

        $tabelTigaKolom($section, [
            ['Berkedudukan di', $ap['domisili'] ?: '-'],
            ['Alamat', $ap['alamat'] ?: '-'],
            ['Dalam hal ini diwakili oleh', $ap['penandatangan'] ?: '-'],
            ['Selaku', $ap['jabatan'] ?: '-'],
        ], [3000, 300, 6338]);

        $section->addText(
            'Dengan demikian sah bertindak untuk dan atas nama PT Angkasa Pura Indonesia, selanjutnya dalam Perjanjian Pokok ini disebut AP INDONESIA.',
            [],
            array_merge($rata, ['spaceBefore' => 80])
        );

        // Pihak II: Mitra.
        $section->addText(
            'II. ' . $mitra,
            ['bold' => true],
            ['spaceBefore' => 120, 'spaceAfter' => 40]
        );

        $tabelTigaKolom($section, [
            ['Berkedudukan di', $domisiliMitra],
            ['Alamat', $alamatMitra],
            ['Dalam hal ini diwakili oleh', $namaMitraTtd],
            ['Selaku', $jabatanMitraTtd],
        ], [3000, 300, 6338]);

        $section->addText(
            'Dengan demikian sah bertindak untuk dan atas nama ' . $mitra .
            ', selanjutnya dalam Perjanjian Pokok ini disebut MITRA.',
            [],
            array_merge($rata, ['spaceBefore' => 80])
        );

        $section->addText(
            'Selanjutnya, AP INDONESIA dan MITRA secara bersama-sama disebut “PARA PIHAK”.',
            [],
            $rata
        );

        // =========================
        // HALAMAN 2
        // =========================

        $section->addPageBreak();

        $judulPasal('Pasal 2', 'Lokasi dan Fasilitas');

        $section->addText(
            'Pelaksanaan kegiatan usaha dilakukan pada lokasi fasilitas komersial yang telah disepakati oleh PARA PIHAK.',
            [],
            $rata
        );

        $tulis($section, [
            'Lokasi fasilitas: ',
            [$namaLokasi],
            ', Terminal ' . $terminal .
            ', Lantai ' . $lantai .
            ', Kode Ruang ' . $kodeRuang . '.',
        ]);

        $tulis($section, [
            'Luas fasilitas: ',
            [(string) $luas],
            '.',
        ]);

        $judulPasal('Pasal 3', 'Jangka Waktu Perjanjian');

        $section->addText(
            'Jangka waktu kerja sama mengikuti periode yang telah ditetapkan dalam data kerja sama dan dokumen perjanjian yang disepakati oleh PARA PIHAK.',
            [],
            $rata
        );

        $tulis($section, ['Tanggal mulai: ', [$tanggalMulai], '.']);
        $tulis($section, ['Tanggal berakhir: ', [$tanggalBerakhir], '.']);

        $judulPasal('Pasal 4', 'Evaluasi Tarif');

        $tulis($section, [
            'Evaluasi tarif dilakukan setiap ',
            [(string) $kontrak->periode_evaluasi_tarif],
            ' ',
            [(string) ($kontrak->satuan_evaluasi_tarif ?: 'tahun')],
            ' sesuai ketentuan dan kesepakatan PARA PIHAK.',
        ]);

        // =========================
        // HALAMAN 3
        // =========================

        $section->addPageBreak();

        $judulPasal('Pasal 5', 'Hak dan Kewajiban Para Pihak');

        $section->addText(
            'PARA PIHAK melaksanakan hak dan kewajiban masing-masing berdasarkan ketentuan yang disepakati dalam perjanjian dan dokumen lampiran yang menjadi bagian dari perjanjian.',
            [],
            $rata
        );

        $judulPasal('Pasal 6', 'Kepemilikan Fasilitas');

        $section->addText(
            'Ketentuan kepemilikan fasilitas yang digunakan dalam pelaksanaan kerja sama mengikuti pilihan klausul yang ditetapkan dalam pengisian data kontrak.',
            [],
            $rata
        );

        $tulis($section, [
            'Pilihan kepemilikan fasilitas: ',
            [$klausulText],
            '.',
        ]);

        $judulPasal('Pasal 7', 'Ketentuan Penutup');

        $section->addText(
            'Hal-hal yang belum diatur dalam perjanjian ini akan mengikuti ketentuan dalam dokumen pendukung dan kesepakatan tertulis PARA PIHAK.',
            [],
            $rata
        );

        $section->addText(
            'Dokumen lampiran yang dipilih sebagai bagian dari perjanjian:',
            [],
            $rata
        );

        if (empty($lampiran)) {
            $section->addListItem('[Lampiran belum dipilih]', 0, [], \PhpOffice\PhpWord\Style\ListItem::TYPE_BULLET_FILLED, ['spaceAfter' => 40]);
        }

        foreach ($lampiran as $item) {
            if (is_string($item) && str_starts_with($item, 'Lainnya: ')) {
                $teks = substr($item, strlen('Lainnya: '));
            } else {
                $teks = $labelLampiran[$item] ?? $item;
            }

            $section->addListItem(
                (string) $teks,
                0,
                [],
                \PhpOffice\PhpWord\Style\ListItem::TYPE_BULLET_FILLED,
                ['spaceAfter' => 40]
            );
        }

        // =========================
        // HALAMAN 4: PENUTUP DAN TANDA TANGAN
        // =========================

        $section->addPageBreak();

        $section->addText(
            'PENUTUP',
            ['bold' => true, 'size' => 11.5],
            ['alignment' => 'center', 'spaceBefore' => 200, 'spaceAfter' => 80]
        );

        $section->addText(
            'Demikian dokumen perjanjian ini dibuat untuk menjadi bahan pemeriksaan dan penyelesaian dokumen kontrak oleh PARA PIHAK.',
            [],
            $rata
        );

        $section->addText(
            'Bagian penandatanganan dan ketentuan final mengikuti dokumen resmi yang telah disetujui oleh pihak berwenang.',
            [],
            $rata
        );

        $section->addTextBreak(1);

        $tabelTtd = $section->addTable(array_merge($polosTabel, [
            'width' => $lebarTeks,
            'unit' => 'dxa',
        ]));

        $lebarSel = (int) ($lebarTeks / 2);

        $barisTtd = function ($teksKiri, $teksKanan, array $fontKiri = [], array $fontKanan = [], $tinggi = null) use ($tabelTtd, $lebarSel) {
            $tinggi ? $tabelTtd->addRow($tinggi) : $tabelTtd->addRow();
            $tabelTtd->addCell($lebarSel)->addText($teksKiri, $fontKiri, ['alignment' => 'center', 'spaceAfter' => 0]);
            $tabelTtd->addCell($lebarSel)->addText($teksKanan, $fontKanan, ['alignment' => 'center', 'spaceAfter' => 0]);
        };

        $tebal = ['bold' => true];
        $tebalGaris = ['bold' => true, 'underline' => 'single'];

        $barisTtd('PIHAK PERTAMA', 'PIHAK KEDUA', $tebal, $tebal);
        $barisTtd('', '', [], [], 1500);
        $barisTtd($namaApTtd, $namaMitraTtd, $tebalGaris, $tebalGaris);
        $barisTtd($jabatanApTtd, $jabatanMitraTtd);
        $barisTtd($namaApPerusahaan, $mitra);

        return $phpWord;
    }

    /**
     * Mengubah file gambar menjadi data URI agar dapat dimuat DomPDF.
     */
    private function gambarSebagaiDataUri(string $path): string
    {
        if (!is_file($path)) {
            return '';
        }

        $mime = function_exists('mime_content_type')
            ? mime_content_type($path)
            : 'image/png';

        return 'data:' . ($mime ?: 'image/png') . ';base64,'
            . base64_encode(file_get_contents($path));
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
            'logoApSrc' => $this->gambarSebagaiDataUri(
                public_path('images/angkasa-pura-logo.png')
            ),
            'logoMitraSrc' => $kontrak->logo_mitra_path
                ? $this->gambarSebagaiDataUri(
                    Storage::disk('local')->path($kontrak->logo_mitra_path)
                )
                : '',
        ];

        try {
            // Membuat PDF dari halaman dokumen kontrak.
            $pdf = Pdf::loadView(
                'kontrak.dokumen-pdf',
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