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
     * Memastikan dokumen kontrak final benar-benar tersedia.
     */
    private function sudahUploadFinal(Kontrak $kontrak): bool
    {
        return !empty($kontrak->final_pdf_path)
            && Storage::disk('local')->exists($kontrak->final_pdf_path);
    }

    /**
     * Menentukan status yang ditampilkan berdasarkan dokumen final
     * dan tanggal berakhirnya masa kerja sama.
     */
    private function tentukanStatusKontrak(Kontrak $kontrak, $hariIni = null): string
    {
        $hariIni = $hariIni ?: now()->startOfDay();

        if (!$this->sudahUploadFinal($kontrak)) {
            return 'Draft';
        }

        $tanggalAkhir = $kontrak->kerjaSama?->tanggal_berakhir;

        if (
            $tanggalAkhir
            && Carbon::parse($tanggalAkhir)->startOfDay()->lt($hariIni)
        ) {
            return 'Nonaktif';
        }

        return 'Aktif';
    }

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

            'fax' => $perusahaan?->fax ?? '',

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
        | 1. DATA SELURUH KONTRAK DAN STATISTIK
        |--------------------------------------------------------------------------
        */

        $semuaKontrak = Kontrak::with([
    'kerjaSama',
    'tenant',
    'lokasi',
])->get();

// Sinkronisasi status kontrak berdasarkan PDF final
// dan tanggal berakhir kerja sama.
foreach ($semuaKontrak as $kontrak) {
    $statusTerbaru = $this->tentukanStatusKontrak(
        $kontrak,
        $hariIni
    );

    if ($kontrak->status !== $statusTerbaru) {
        $kontrak->status = $statusTerbaru;
        $kontrak->save();
    }
}

$totalKontrak = $semuaKontrak->count();

        $kontrakAktif = $semuaKontrak
            ->filter(fn ($kontrak) => $this->tentukanStatusKontrak($kontrak, $hariIni) === 'Aktif')
            ->count();

        $kontrakNonAktif = $semuaKontrak
            ->filter(fn ($kontrak) => $this->tentukanStatusKontrak($kontrak, $hariIni) === 'Nonaktif')
            ->count();

        $kontrakDraft = $semuaKontrak
            ->filter(fn ($kontrak) => $this->tentukanStatusKontrak($kontrak, $hariIni) === 'Draft')
            ->count();

        /*
        |--------------------------------------------------------------------------
        | 2. DATA GRAFIK KONTRAK PER TAHUN
        |--------------------------------------------------------------------------
        */

        $dataTahunan = $semuaKontrak
            ->filter(fn ($kontrak) => $kontrak->tanggal_pengajuan !== null)
            ->groupBy(function ($kontrak) {
                return Carbon::parse($kontrak->tanggal_pengajuan)->format('Y');
            })
            ->map(fn ($items) => $items->count())
            ->sortKeys();

        $tahunTersedia = $dataTahunan->keys()->values();

        /*
        |--------------------------------------------------------------------------
        | 3. QUERY DAFTAR KONTRAK
        |--------------------------------------------------------------------------
        */

        $query = Kontrak::with([
            'kerjaSama',
            'tenant',
            'lokasi',
        ]);

        // Pencarian berdasarkan nomor kontrak, kerja sama, mitra, tenant, dan lokasi.
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

        // Filter tahun pengajuan.
        if ($request->filled('tahun')) {
            $query->whereYear('tanggal_pengajuan', $request->input('tahun'));
        }

        // Pengurutan daftar kontrak.
        switch ($request->input('sort', 'newest')) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;

            case 'az':
                $query->orderBy(
                    KerjaSama::select('brand')
                        ->whereColumn('kerja_samas.id', 'kontraks.kerja_sama_id')
                );
                break;

            case 'za':
                $query->orderByDesc(
                    KerjaSama::select('brand')
                        ->whereColumn('kerja_samas.id', 'kontraks.kerja_sama_id')
                );
                break;

            default:
                $query->orderByDesc('created_at');
                break;
        }

        $kontraks = $query->get();

        // Filter status dilakukan setelah data diambil karena status Aktif/Nonaktif
        // ditentukan dari keberadaan PDF final dan tanggal berakhir kerja sama.
        if ($request->filled('status') && $request->input('status') !== 'Belum Dibuat') {
            $statusDipilih = $request->input('status');

            $kontraks = $kontraks
                ->filter(fn ($kontrak) => $this->tentukanStatusKontrak($kontrak, $hariIni) === $statusDipilih)
                ->values();
        }

        /*
        |--------------------------------------------------------------------------
        | 4. DATA KANDIDAT KONTRAK (BELUM DIBUAT)
        |--------------------------------------------------------------------------
        */

        $kerjaSamaSudahAdaKontrak = Kontrak::pluck('kerja_sama_id');

        $kandidatKontrakSemua = KerjaSama::with([
            'tenant',
            'lokasi',
            'aktivasi',
        ])
            ->whereNotNull('berita_acara_path')
            ->latest()
            ->get()
            ->filter(function ($kerjaSama) use ($kerjaSamaSudahAdaKontrak) {
                $baTersedia = Storage::disk('local')->exists($kerjaSama->berita_acara_path);

                return $baTersedia
                    && $kerjaSama->tenant !== null
                    && $kerjaSama->lokasi !== null
                    && !$kerjaSamaSudahAdaKontrak->contains($kerjaSama->id);
            })
            ->values();

        // Jumlah Belum Dibuat dihitung sebelum pencarian dan filter tahun.
        $kontrakBelumDibuat = $kandidatKontrakSemua->count();

        $kandidatKontrak = $kandidatKontrakSemua;

        // Pencarian kandidat kontrak.
        if ($request->filled('search')) {
            $keyword = strtolower(trim($request->input('search')));

            $kandidatKontrak = $kandidatKontrak->filter(function ($kerjaSama) use ($keyword) {
                $nilaiPencarian = [
                    $kerjaSama->nomor_kerja_sama,
                    $kerjaSama->mitra_usaha,
                    $kerjaSama->brand,
                    $kerjaSama->tenant?->nama_tenant,
                    $kerjaSama->lokasi?->lokasi,
                    $kerjaSama->lokasi?->kode_ruang,
                ];

                foreach ($nilaiPencarian as $nilai) {
                    if ($nilai !== null && str_contains(strtolower((string) $nilai), $keyword)) {
                        return true;
                    }
                }

                return false;
            });
        }

        // Kandidat hanya ditampilkan saat status Belum Dibuat dipilih atau tanpa filter status.
        if ($request->filled('status') && $request->input('status') !== 'Belum Dibuat') {
            $kandidatKontrak = collect();
        }

        // Filter tahun kandidat berdasarkan tanggal pengajuan kerja sama.
        if ($request->filled('tahun')) {
            $tahun = $request->input('tahun');

            $kandidatKontrak = $kandidatKontrak->filter(function ($kerjaSama) use ($tahun) {
                return $kerjaSama->tanggal_pengajuan
                    && Carbon::parse($kerjaSama->tanggal_pengajuan)->format('Y') === $tahun;
            });
        }

        // Pengurutan kandidat kontrak.
        switch ($request->input('sort', 'newest')) {
            case 'oldest':
                $kandidatKontrak = $kandidatKontrak->sortBy('created_at')->values();
                break;

            case 'az':
                $kandidatKontrak = $kandidatKontrak->sortBy('brand')->values();
                break;

            case 'za':
                $kandidatKontrak = $kandidatKontrak->sortByDesc('brand')->values();
                break;

            default:
                $kandidatKontrak = $kandidatKontrak->sortByDesc('created_at')->values();
                break;
        }

        /*
        |--------------------------------------------------------------------------
        | 5. DATA STATUS UNTUK GRAFIK
        |--------------------------------------------------------------------------
        */

        $dataStatus = [
            'Belum Dibuat' => $kontrakBelumDibuat,
            'Draft' => $kontrakDraft,
            'Aktif' => $kontrakAktif,
            'Nonaktif' => $kontrakNonAktif,
        ];

        /*
        |--------------------------------------------------------------------------
        | 6. KIRIM DATA KE VIEW
        |--------------------------------------------------------------------------
        */

        return view('kontrak.index', compact(
            'kontraks',
            'kandidatKontrak',
            'totalKontrak',
            'kontrakBelumDibuat',
            'kontrakDraft',
            'kontrakAktif',
            'kontrakNonAktif',
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

    // Jika PDF final belum tersedia, tandai pengguna berada
    // pada tahap Isi Dokumen Kontrak.
    if (!$this->sudahUploadFinal($kontrak)) {
        if ($kontrak->progress_tahap !== 'isi_dokumen') {
            $kontrak->progress_tahap = 'isi_dokumen';
            $kontrak->save();
        }
    }

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

        if ($this->sudahUploadFinal($kontrak)) {
            return redirect()
                ->route('kontrak.show', $kontrak->id)
                ->with(
                    'error',
                    'Dokumen kontrak yang sudah selesai tidak dapat diubah.'
                );
        }

                // Tandai bahwa pengguna sedang berada pada tahap Isi Dokumen.
        if ($kontrak->progress_tahap !== 'isi_dokumen') {
            $kontrak->progress_tahap = 'isi_dokumen';
            $kontrak->save();
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

        // Kontrak yang sudah memiliki PDF final tidak dapat diubah.
        if ($this->sudahUploadFinal($kontrak)) {
            return redirect()
                ->route('kontrak.show', $kontrak->id)
                ->with(
                    'error',
                    'Dokumen kontrak yang sudah selesai tidak dapat diubah.'
                );
        }

        // Validasi data formulir.
        $validated = $request->validate([
            'luas_fasilitas_snapshot' => [
                'sometimes',
                'nullable',
                'string',
                'max:255',
            ],

            'konfirmasi_dokumen' => [
                'sometimes',
                'boolean',
            ],
            'logo_mitra' => [
                'sometimes',
                'nullable',
                'image',
                'mimes:jpg,jpeg,png',
                'max:10240',
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
            'logo_mitra.max' => 'Ukuran logo Mitra maksimal 10 MB.',
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
        $kontrak = Kontrak::with('kerjaSama')->findOrFail($id);
        // Jika PDF final belum tersedia, tandai progres
// bahwa pengguna sudah mencapai tahap Upload Dokumen Final.
if (!$this->sudahUploadFinal($kontrak)) {
    if ($kontrak->progress_tahap !== 'upload_final') {
        $kontrak->progress_tahap = 'upload_final';
        $kontrak->save();
    }
}

        $tempUpload = session()->get(
            'kontrak_upload_final_temp.' . $kontrak->id
        );

        // Pastikan file sementara masih tersedia.
        if (
            $tempUpload &&
            (
                empty($tempUpload['path']) ||
                !Storage::disk('local')->exists($tempUpload['path'])
            )
        ) {
            session()->forget(
                'kontrak_upload_final_temp.' . $kontrak->id
            );

            $tempUpload = null;
        }

        return view('kontrak.upload-final', [
            'kontrak' => $kontrak,
            'tempUpload' => $tempUpload,
        ]);
    }

    /**
     * Menyimpan PDF ke penyimpanan sementara.
     */
    public function storeUploadFinalTemp(Request $request, $id)
    {
        $kontrak = Kontrak::findOrFail($id);

        if ($this->sudahUploadFinal($kontrak)) {
            return response()->json([
                'success' => false,
                'message' => 'Kontrak ini sudah selesai.',
            ], 422);
        }

        $request->validate([
            'final_pdf' => [
                'required',
                'file',
                'mimes:pdf',
                'max:10240',
            ],
        ], [
            'final_pdf.required' => 'Silakan pilih dokumen PDF.',
            'final_pdf.file' => 'File yang diunggah tidak valid.',
            'final_pdf.mimes' => 'Dokumen harus menggunakan format PDF.',
            'final_pdf.max' => 'Ukuran PDF maksimal 10 MB.',
        ]);

        $file = $request->file('final_pdf');

        $namaFile = 'kontrak_final_' . $kontrak->id . '_' .
            now()->format('YmdHis') . '_' .
            uniqid() . '.pdf';

        $pathBaru = $file->storeAs(
            'kontrak/temp/' . $kontrak->id,
            $namaFile,
            'local'
        );

        if (!$pathBaru) {
            return response()->json([
                'success' => false,
                'message' => 'PDF gagal disimpan sementara.',
            ], 500);
        }

        // Hapus file sementara sebelumnya jika ada penggantian.
        $sessionKey = 'kontrak_upload_final_temp.' . $kontrak->id;
        $tempLama = session()->get($sessionKey);

        if (
            $tempLama &&
            !empty($tempLama['path']) &&
            Storage::disk('local')->exists($tempLama['path'])
        ) {
            Storage::disk('local')->delete($tempLama['path']);
        }

        // Simpan informasi file sementara ke session.
        $tempUpload = [
            'path' => $pathBaru,
            'name' => $file->getClientOriginalName(),
            'size' => $file->getSize(),
        ];

        session()->put($sessionKey, $tempUpload);

        return response()->json([
            'success' => true,
            'message' => 'PDF berhasil disimpan sementara.',
            'file_name' => $tempUpload['name'],
            'file_size' => $tempUpload['size'],
            'preview_url' => route(
                'kontrak.uploadFinal.temp.preview',
                $kontrak->id
            ),
        ]);
    }

    /**
     * Menampilkan PDF dari penyimpanan sementara.
     */
    public function previewUploadFinalTemp($id)
    {
        $kontrak = Kontrak::findOrFail($id);

        $tempUpload = session()->get(
            'kontrak_upload_final_temp.' . $kontrak->id
        );
                if (
            !$tempUpload ||
            empty($tempUpload['path']) ||
            !Storage::disk('local')->exists($tempUpload['path'])
        ) {
            abort(404, 'PDF sementara tidak ditemukan.');
        }

        return Storage::disk('local')->response(
            $tempUpload['path'],
            $tempUpload['name'] ?? 'dokumen-kontrak.pdf',
            [
                'Content-Type' => 'application/pdf',
            ],
            'inline'
        );
    }


    /**
     * Menghapus PDF dari penyimpanan sementara.
     */
    public function deleteUploadFinalTemp($id)
    {
        $kontrak = Kontrak::findOrFail($id);

        $sessionKey = 'kontrak_upload_final_temp.' . $kontrak->id;

        $tempUpload = session()->get($sessionKey);

        if (
            $tempUpload &&
            !empty($tempUpload['path']) &&
            Storage::disk('local')->exists($tempUpload['path'])
        ) {
            Storage::disk('local')->delete($tempUpload['path']);
        }

        session()->forget($sessionKey);

        return response()->json([
            'success' => true,
            'message' => 'Dokumen sementara berhasil dihapus.',
        ]);
    }


    /**
     * Memproses penyimpanan dokumen kontrak final.
     */
    public function storeUploadFinal(Request $request, $id)
    {
        $kontrak = Kontrak::findOrFail($id);

        // Cegah penyimpanan ulang jika PDF final sudah tersedia.
        if ($this->sudahUploadFinal($kontrak)) {
            return redirect()
                ->route('kontrak')
                ->with('error', 'Kontrak ini sudah selesai.');
        }

        // Validasi konfirmasi tanda tangan.
        $request->validate([
            'final_signature_confirmed' => [
                'required',
                'accepted',
            ],
        ], [
            'final_signature_confirmed.required' =>
                'Konfirmasi tanda tangan wajib dicentang.',
            'final_signature_confirmed.accepted' =>
                'Dokumen harus dikonfirmasi telah ditandatangani kedua pihak.',
        ]);

        // Ambil PDF dari penyimpanan sementara.
        $sessionKey = 'kontrak_upload_final_temp.' . $kontrak->id;

        $tempUpload = session()->get($sessionKey);

        if (
            !$tempUpload ||
            empty($tempUpload['path']) ||
            !Storage::disk('local')->exists($tempUpload['path'])
        ) {
            return back()
                ->withInput()
                ->with(
                    'error',
                    'Dokumen sementara tidak ditemukan. Silakan unggah PDF kembali.'
                );
        }

        $pathSementara = $tempUpload['path'];

        // Buat nama file final.
        $namaFile = 'kontrak_final_' . $kontrak->id . '_' .
            now()->format('YmdHis') . '.pdf';

        // Salin file sementara ke folder final.
        $pathBaru = 'kontrak/final/' . $namaFile;

        $berhasilSalin = Storage::disk('local')->copy(
            $pathSementara,
            $pathBaru
        );

        if (!$berhasilSalin) {
            return back()
                ->withInput()
                ->with(
                    'error',
                    'Dokumen kontrak final gagal disimpan.'
                );
        }

        // Simpan informasi dokumen final ke database.
        $pathLama = $kontrak->final_pdf_path;

        try {
            $kontrak->final_pdf_path = $pathBaru;
            $kontrak->final_uploaded_at = now();
            $kontrak->final_signature_confirmed = true;

            // Tentukan status berdasarkan tanggal akhir masa kerja sama.
            $kontrak->status = $this->tentukanStatusKontrak($kontrak);

            $kontrak->save();
        } catch (\Throwable $e) {
            Storage::disk('local')->delete($pathBaru);

            throw $e;
        }

        // Hapus file final sebelumnya jika ada.
        if (
            $pathLama &&
            $pathLama !== $pathBaru &&
            Storage::disk('local')->exists($pathLama)
        ) {
            Storage::disk('local')->delete($pathLama);
        }

        // Hapus file sementara setelah penyimpanan final berhasil.
        Storage::disk('local')->delete($pathSementara);

        session()->forget($sessionKey);

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
     * Menampilkan logo Mitra yang tersimpan di disk local.
     */
    public function tampilkanLogoMitra($id)
    {
        $kontrak = Kontrak::findOrFail($id);

        // Pastikan path logo tersedia dan file benar-benar ada.
        if (
            !$kontrak->logo_mitra_path ||
            !Storage::disk('local')->exists($kontrak->logo_mitra_path)
        ) {
            abort(404, 'Logo Mitra tidak ditemukan.');
        }

        // Menampilkan file logo tanpa memindahkannya ke folder public.
        return Storage::disk('local')->response(
            $kontrak->logo_mitra_path
        );
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

        // Pembukaan perjanjian.
        $section->addText(
            'PARA PIHAK terlebih dahulu menerangkan hal-hal sebagai berikut:',
            [],
            ['alignment' => 'both', 'spaceAfter' => 100]
        );

        $poinPembukaan = [
            [
                'a.',
                'AP INDONESIA merupakan perseroan terbatas yang bergerak di bidang pelayanan jasa kebandarudaraan.'
            ],
            [
                'b.',
                'MITRA dalam rangka melaksanakan kegiatan usahanya bermaksud memanfaatkan Fasilitas Komersial milik AP INDONESIA.'
            ],
            [
                'c.',
                'Bahwa PARA PIHAK telah menyepakati pemanfaatan Fasilitas Komersial sebagaimana dituangkan dalam Berita Acara Kesepakatan ' .
                ($kerjaSama?->nomor_bak
                    ?: ($kerjaSama?->nomor_berita_acara ?: '[Nomor BA belum tersedia]')) .
                ' (Selanjutnya disebut “Berita Acara Kesepakatan”) dan dokumen lain (sebagaimana relevan).'
            ],
        ];

        foreach ($poinPembukaan as [$nomor, $isi]) {
            $run = $section->addTextRun([
                'alignment' => 'both',
                'indentation' => ['left' => 420, 'hanging' => 300],
                'spaceAfter' => 100,
            ]);

            $run->addText($nomor . '    ');
            $run->addText($isi);
        }

        // Penjelasan Perjanjian Pokok.
        $run = $section->addTextRun([
            'alignment' => 'both',
            'spaceBefore' => 100,
            'spaceAfter' => 180,
        ]);

        $run->addText(
            'Berdasarkan hal-hal tersebut di atas, PARA PIHAK menyatakan sepakat untuk menandatangani Perjanjian Pokok Tentang Kerja Sama Pemanfaatan Fasilitas Komersial PT Angkasa Pura Indonesia untuk Kegiatan Usaha '
        );
        $run->addText($jenisUsaha, ['bold' => true]);
        $run->addText(' di ');
        $run->addText($namaLokasi, ['bold' => true]);
        $run->addText(
            ' (Selanjutnya disebut “Perjanjian Pokok”), dengan ketentuan dan syarat-syarat sebagai berikut:'
        );

        // PASAL 1
        $judulPasal('Pasal 1', 'Pokok Perjanjian');

        $ayatPasal1 = [
            'AP INDONESIA memberikan izin pemanfaatan Fasilitas Komersial kepada MITRA untuk melakukan kegiatan usaha, dan MITRA sepakat untuk memanfaatkan Fasilitas Komersial milik AP INDONESIA, dengan rincian sebagaimana terlampir pada Perjanjian Pokok ini.',
            'Bentuk Kerja Sama Pemanfaatan Fasilitas Komersial yang disepakati PARA PIHAK adalah Revenue Sharing.',
            'Atas pemanfaatan Fasilitas Komersial sebagaimana dimaksud pada ayat (1), MITRA berkewajiban memenuhi ketentuan pembayaran berdasarkan nilai Guaranteed Turnover (GTO) dan persentase Revenue Sharing yang telah disepakati PARA PIHAK, dengan detail rincian dan cara pembayaran, sebagaimana terlampir pada Perjanjian Pokok ini.',
        ];

        foreach ($ayatPasal1 as $index => $isi) {
            $run = $section->addTextRun([
                'alignment' => 'both',
                'indentation' => ['left' => 650, 'hanging' => 450],
                'spaceAfter' => 100,
            ]);

            $run->addText('(' . ($index + 1) . ')    ');

            if ($index === 1) {
                $run->addText(
                    'Bentuk Kerja Sama Pemanfaatan Fasilitas Komersial yang disepakati PARA PIHAK adalah '
                );
                $run->addText('Revenue Sharing', ['bold' => true]);
                $run->addText('.');
            } elseif ($index === 2) {
                $run->addText(
                    'Atas pemanfaatan Fasilitas Komersial sebagaimana dimaksud pada ayat (1), MITRA berkewajiban memenuhi ketentuan pembayaran berdasarkan nilai '
                );
                $run->addText('Guaranteed Turnover (GTO)', ['bold' => true]);
                $run->addText(' dan persentase ');
                $run->addText('Revenue Sharing', ['bold' => true]);
                $run->addText(
                    ' yang telah disepakati PARA PIHAK, dengan detail rincian dan cara pembayaran, sebagaimana terlampir pada Perjanjian Pokok ini.'
                );
            } else {
                $run->addText($isi);
            }
        }


        // =========================
        // HALAMAN 3
        // =========================

        $section->addPageBreak();

        // PASAL 2
        $judulPasal('Pasal 2', 'Jangka Waktu Perjanjian');

        $durasiNilai = null;
        $durasiSatuan = null;

        $tanggalMulaiCarbon = $kerjaSama?->tanggal_mulai
            ? Carbon::parse($kerjaSama->tanggal_mulai)
            : null;

        $tanggalBerakhirCarbon = $kerjaSama?->tanggal_berakhir
            ? Carbon::parse($kerjaSama->tanggal_berakhir)
            : null;

        if (
            $tanggalMulaiCarbon &&
            $tanggalBerakhirCarbon &&
            $tanggalBerakhirCarbon->greaterThanOrEqualTo($tanggalMulaiCarbon)
        ) {
            $selisihTahun = $tanggalMulaiCarbon->diffInYears(
                $tanggalBerakhirCarbon
            );

            if ($selisihTahun >= 1) {
                $durasiNilai = $selisihTahun;
                $durasiSatuan = 'tahun';
            } else {
                $durasiNilai = $tanggalMulaiCarbon->diffInMonths(
                    $tanggalBerakhirCarbon
                );
                $durasiSatuan = 'bulan';
            }
        }

        $run = $section->addTextRun($rata);
        $run->addText('Perjanjian ini berlaku untuk jangka waktu ');
        $run->addText(
            ($durasiNilai ?? '[Durasi]') . ' ' . ($durasiSatuan ?? '[bulan/tahun]'),
            ['bold' => true]
        );
        $run->addText(
            ' terhitung sejak ' . $tanggalMulai .
            ' sampai dengan ' . $tanggalBerakhir . '.'
        );

        // PASAL 3
                // PASAL 3
        $judulPasal('Pasal 3', 'Peninjauan Tarif');

        $section->addText(
            'Besaran :',
            [],
            ['alignment' => 'both', 'spaceAfter' => 40]
        );

        $daftarTarif = [
            'Nilai Guaranteed Turnover (GTO) atas Fasilitas Komersial;',
            'Tarif Utilitas; dan/atau',
            'Imbalan Revenue Sharing;',
        ];

        foreach ($daftarTarif as $index => $isi) {
            $run = $section->addTextRun([
                'alignment' => 'both',
                'indentation' => ['left' => 850, 'hanging' => 300],
                'spaceAfter' => 35,
            ]);

            $run->addText(chr(97 + $index) . '.    ');

            if ($index === 0) {
                $run->addText('Nilai ');
                $run->addText('Guaranteed Turnover (GTO)', ['bold' => true]);
                $run->addText(' atas Fasilitas Komersial;');
            } elseif ($index === 2) {
                $run->addText('Imbalan ');
                $run->addText('Revenue Sharing', ['bold' => true]);
                $run->addText(';');
            } else {
                $run->addText($isi);
            }
        }

        $run = $section->addTextRun([
            'alignment' => 'both',
            'spaceBefore' => 70,
            'spaceAfter' => 100,
        ]);

        $run->addText('Dievaluasi secara periodik setiap ');
        $run->addText(
            $kontrak->periode_evaluasi_tarif . ' ' .
            ($kontrak->satuan_evaluasi_tarif ?: 'tahun'),
            ['bold' => true]
        );
        $run->addText(
            ' masa Perjanjian atau dapat diubah apabila terdapat perubahan ketentuan tarif yang berlaku di lingkungan AP INDONESIA dan disepakati oleh PARA PIHAK.'
        );

        // PASAL 4
        $judulPasal('Pasal 4', 'Pelaksanaan Perjanjian');

        $section->addText(
            'Dalam hal dilakukan pembangunan dan/atau renovasi fasilitas usaha, maka biaya yang timbul menjadi tanggung jawab MITRA sepenuhnya, dengan ketentuan sebagai berikut:',
            $rata,
            ['spaceAfter' => 80]
        );

        $run = $section->addTextRun([
            'alignment' => 'both',
            'indentation' => ['left' => 850, 'hanging' => 300],
            'spaceAfter' => 80,
        ]);

        $run->addText('a.    ');
        $run->addText(
            'MITRA wajib menyerahkan gambar rancang bangun dan/atau jadwal pelaksanaan pembangunan atau renovasi untuk disetujui AP INDONESIA;'
        );

        $run = $section->addTextRun([
            'alignment' => 'both',
            'indentation' => ['left' => 850, 'hanging' => 300],
            'spaceAfter' => 0,
        ]);

        $run->addText('b.    ');

        if ($kontrak->klausul_kepemilikan_fasilitas === 'mitra') {
            $run->addText(
                'Pada akhir Perjanjian disepakati fasilitas usaha yang dibangun oleh MITRA tetap menjadi milik MITRA, dan fasilitas komersial yang dikelola wajib dikembalikan dengan kondisi baik kepada AP INDONESIA selambat-lambatnya 14 (empat belas) hari kalender sejak tanggal berakhirnya Perjanjian, sebagaimana diatur secara teknis dalam Syarat-syarat Umum Perjanjian.'
            );
        } else {
            $run->addText(
                'Pada akhir perjanjian disepakati fasilitas usaha yang dibangun oleh MITRA menjadi milik AP INDONESIA tanpa kompensasi dalam bentuk apapun, sebagaimana diatur secara teknis dalam Syarat-syarat Umum Perjanjian.'
            );
        }


        // =========================
        // HALAMAN 4
        // =========================

        $section->addPageBreak();

        // PASAL 5
        $judulPasal('Pasal 5', 'Dokumen Perjanjian');

        $section->addText(
            'Perjanjian Pokok dan Lampiran Perjanjian merupakan satu kesatuan yang tidak dapat dipisahkan yang terdiri atas :',
            $rata,
            ['spaceAfter' => 50]
        );

        $labelLampiran = [
            'SUP' => 'Syarat-Syarat Umum Perjanjian (SUP)',
            'BAK' => 'Berita Acara Kesepakatan (BAK)',
            'Denah Fasilitas Komersial' => 'Denah Fasilitas Komersial',
            'Perjanjian Tingkat Layanan' => 'Perjanjian Tingkat Layanan',
            'Lainnya' => 'Dokumen Lainnya',
        ];

        $urutanLampiran = [
            'SUP',
            'BAK',
            'Denah Fasilitas Komersial',
            'Perjanjian Tingkat Layanan',
            'Lainnya',
        ];

        $lampiran = $kontrak->lampiran_perjanjian ?? [];

        if (is_string($lampiran)) {
            $lampiran = json_decode($lampiran, true) ?? [];
        }

        if (!is_array($lampiran)) {
            $lampiran = [];
        }

        if (empty($lampiran)) {
            $section->addListItem(
                '[Lampiran belum dipilih]',
                0,
                [],
                \PhpOffice\PhpWord\Style\ListItem::TYPE_BULLET_FILLED,
                ['spaceAfter' => 30]
            );
        } else {
            foreach ($urutanLampiran as $jenisLampiran) {
                foreach ($lampiran as $item) {
                    $item = (string) $item;

                    $jenisItem = str_starts_with($item, 'Lainnya: ')
                        ? 'Lainnya'
                        : $item;

                    if ($jenisItem !== $jenisLampiran) {
                        continue;
                    }

                    if ($jenisItem === 'Lainnya') {
                        $teksLampiran = str_starts_with($item, 'Lainnya: ')
                            ? 'Dokumen Lainnya: ' . substr($item, 9)
                            : 'Dokumen Lainnya';
                    } else {
                        $teksLampiran = $labelLampiran[$jenisItem] ?? $jenisItem;
                    }

                    $section->addListItem(
                        $teksLampiran,
                        0,
                        [],
                        \PhpOffice\PhpWord\Style\ListItem::TYPE_BULLET_FILLED,
                        ['spaceAfter' => 30]
                    );
                }
            }
        }

        // PASAL 6
        $judulPasal('Pasal 6', 'Pemberitahuan');

        $section->addText(
            'Semua surat menyurat atau penyampaian informasi melalui telepon atau faksimili kepada masing-masing PIHAK ditujukan ke alamat sebagai berikut :',
            $rata,
            ['spaceAfter' => 50]
        );

        // Data pemberitahuan AP Indonesia.
        $section->addText(
            'a. ' . ($ap['nama'] ?: 'PT ANGKASA PURA INDONESIA'),
            ['bold' => true],
            ['indentation' => ['left' => 350], 'spaceAfter' => 20]
        );

        $tabelTigaKolom($section, [
            ['Up.', $ap['penandatangan'] ?: '-'],
            ['Alamat', $ap['alamat'] ?: '-'],
            ['Telepon', $ap['telepon'] ?: '-'],
            ['Faksimili', $ap['fax'] ?: '-'],
            ['Email', $ap['email'] ?: '-'],
        ], [1300, 250, 8088]);

        // Data pemberitahuan Mitra.
        $section->addText(
            'b. ' . $mitra,
            ['bold' => true],
            ['indentation' => ['left' => 350], 'spaceBefore' => 50, 'spaceAfter' => 20]
        );

        $tabelTigaKolom($section, [
            ['Up.', $namaMitraTtd],
            ['Alamat', $alamatMitra],
            ['Telepon', $kontrak->telepon_mitra_snapshot ?: ($tenant?->no_hp_pic ?? '-')],
            ['Faksimili', $kontrak->fax_mitra_snapshot ?: '-'],
            ['Email', $kontrak->email_mitra_snapshot ?: ($tenant?->email_pic ?? '-')],
        ], [1300, 250, 8088]);

        // PASAL 7
        $judulPasal('Pasal 7', 'Penutup');

        $section->addText(
            'Perjanjian ini ditandatangani oleh PARA PIHAK, dalam rangkap 2 (dua) asli, bermeterai cukup serta masing-masing mempunyai kekuatan hukum sama.',
            $rata,
            ['spaceAfter' => 100]
        );

        // Tanda tangan: Mitra di kiri, AP Indonesia di kanan.
        $tabelTtd = $section->addTable(array_merge($polosTabel, [
            'width' => $lebarTeks,
            'unit' => 'dxa',
        ]));

        $lebarSel = (int) ($lebarTeks / 2);

        $barisTtd = function (
            $teksKiri,
            $teksKanan,
            array $fontKiri = [],
            array $fontKanan = [],
            $tinggi = null
        ) use ($tabelTtd, $lebarSel) {
            $tinggi ? $tabelTtd->addRow($tinggi) : $tabelTtd->addRow();

            $tabelTtd->addCell($lebarSel)->addText(
                $teksKiri,
                $fontKiri,
                ['alignment' => 'center', 'spaceAfter' => 0]
            );

            $tabelTtd->addCell($lebarSel)->addText(
                $teksKanan,
                $fontKanan,
                ['alignment' => 'center', 'spaceAfter' => 0]
            );
        };

        $tebal = ['bold' => true];
        $tebalGaris = ['bold' => true, 'underline' => 'single'];

        $barisTtd(
            $mitra,
            $ap['nama'] ?: 'PT ANGKASA PURA INDONESIA',
            $tebal,
            $tebal
        );

        // Ruang tanda tangan.
        $barisTtd('', '', [], [], 1500);

        $barisTtd(
            $namaMitraTtd,
            $namaApTtd,
            $tebalGaris,
            $tebalGaris
        );

        $barisTtd(
            $jabatanMitraTtd,
            $jabatanApTtd
        );

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
            // Membuat PDF menggunakan Blade yang sama dengan halaman preview.
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

        // Ambil nama mitra usaha.
        $namaMitra = $kontrak->kerjaSama->mitra_usaha
            ?? 'Mitra Usaha';

        // Redirect ke halaman Kontrak dengan notifikasi sukses.
        return redirect()
            ->route('kontrak')
            ->with(
                'success',
                'Keputusan perpanjangan kontrak ' . $namaMitra . ' berhasil disimpan.'
            );
    }

    /**
     * Menyimpan draft dokumen kontrak.
     *
     * Penyimpanan tidak mewajibkan seluruh formulir lengkap.
     */
    public function simpanDraftDokumen(Request $request, $id)
    {
        $kontrak = Kontrak::with([
            'kerjaSama.tenant',
            'kerjaSama.lokasi',
            'tenant',
            'lokasi',
        ])->findOrFail($id);

        // Kontrak yang sudah memiliki PDF final tidak dapat diubah.
        if ($this->sudahUploadFinal($kontrak)) {
            return response()->json([
                'success' => false,
                'message' => 'Dokumen kontrak yang sudah selesai tidak dapat diubah.',
            ], 403);
        }

        // Validasi hanya terhadap data yang dikirim.
        $validated = $request->validate([
            'nomor_kontrak_mitra' => [
                'sometimes',
                'nullable',
                'string',
                'max:255',
            ],

            'konfirmasi_dokumen' => [
                'sometimes',
                'boolean',
            ],

            'logo_mitra' => [
                'sometimes',
                'nullable',
                'image',
                'mimes:jpg,jpeg,png',
                'max:2048',
            ],

            'periode_evaluasi_tarif' => [
                'sometimes',
                'nullable',
                'integer',
                'min:1',
            ],

            'satuan_evaluasi_tarif' => [
                'sometimes',
                'nullable',
                'in:bulan,tahun',
            ],

            'klausul_kepemilikan_fasilitas' => [
                'sometimes',
                'nullable',
                'in:mitra,ap_indonesia',
            ],

            'luas_fasilitas_snapshot' => [
                'sometimes',
                'nullable',
                'string',
                'max:255',
            ],

            'konfirmasi_dokumen' => [
                'sometimes',
                'boolean',
            ],

            'nama_pic_mitra_snapshot' => [
                'sometimes',
                'nullable',
                'string',
                'max:255',
            ],

            'jabatan_pic_mitra_snapshot' => [
                'sometimes',
                'nullable',
                'string',
                'max:255',
            ],

            'alamat_mitra_snapshot' => [
                'sometimes',
                'nullable',
                'string',
                'max:5000',
            ],

            'domisili_mitra_snapshot' => [
                'sometimes',
                'nullable',
                'string',
                'max:255',
            ],

            'telepon_mitra_snapshot' => [
                'sometimes',
                'nullable',
                'string',
                'max:50',
            ],

            'fax_mitra_snapshot' => [
                'sometimes',
                'nullable',
                'string',
                'max:50',
            ],

            'email_mitra_snapshot' => [
                'sometimes',
                'nullable',
                'email',
                'max:255',
            ],

            'nama_penandatangan_mitra' => [
                'sometimes',
                'nullable',
                'string',
                'max:255',
            ],

            'jabatan_penandatangan_mitra' => [
                'sometimes',
                'nullable',
                'string',
                'max:255',
            ],

            'lampiran_perjanjian' => [
                'sometimes',
                'nullable',
                'array',
            ],

            'lampiran_perjanjian.*' => [
                'string',
                'distinct',
                'in:SUP,BAK,Denah Fasilitas Komersial,Perjanjian Tingkat Layanan',
            ],
        ]);

        // Data yang akan diperbarui.
        $dataUpdate = [];

        $fieldDokumen = [
            'nomor_kontrak_mitra',
            'konfirmasi_dokumen',
            'periode_evaluasi_tarif',
            'satuan_evaluasi_tarif',
            'klausul_kepemilikan_fasilitas',
            'nama_pic_mitra_snapshot',
            'jabatan_pic_mitra_snapshot',
            'alamat_mitra_snapshot',
            'domisili_mitra_snapshot',
            'telepon_mitra_snapshot',
            'fax_mitra_snapshot',
            'email_mitra_snapshot',
            'nama_penandatangan_mitra',
            'jabatan_penandatangan_mitra',
        ];

        foreach ($fieldDokumen as $field) {
            if (array_key_exists($field, $validated)) {
                $dataUpdate[$field] = $validated[$field];
            }
        }

        // Simpan pilihan lampiran jika dikirim.
        if (array_key_exists('lampiran_perjanjian', $validated)) {
            $dataUpdate['lampiran_perjanjian'] = array_values(
                array_unique($validated['lampiran_perjanjian'] ?? [])
            );
        }

        // Simpan logo baru jika pengguna mengunggahnya.
        if ($request->hasFile('logo_mitra')) {
            $logoPathBaru = $request->file('logo_mitra')->store(
                'kontrak-logo-mitra',
                'local'
            );

            if (!$logoPathBaru) {
                return response()->json([
                    'success' => false,
                    'message' => 'Logo Mitra gagal disimpan.',
                ], 500);
            }

            $dataUpdate['logo_mitra_path'] = $logoPathBaru;
        }

        // Perbarui snapshot hanya jika relasi tersedia.
        $kerjaSama = $kontrak->kerjaSama;
        $tenant = $kontrak->tenant;
        $lokasi = $kontrak->lokasi;

        if ($kerjaSama && $tenant) {
            $dataUpdate['nama_mitra_snapshot'] =
                $tenant->mitra_usaha ?: $kerjaSama->mitra_usaha;
        }

        if ($kerjaSama) {
            $dataUpdate['data_kerja_sama_snapshot'] = [
                'nomor_kerja_sama' => $kerjaSama->nomor_kerja_sama,
                'mitra_usaha' => $kerjaSama->mitra_usaha,
                'brand' => $kerjaSama->brand,
                'jenis_usaha' => $kerjaSama->jenis_usaha,
                'bentuk_kerja_sama' => $kerjaSama->bentuk_kerja_sama,
                'tanggal_mulai' => $kerjaSama->tanggal_mulai,
                'tanggal_berakhir' => $kerjaSama->tanggal_berakhir,
            ];
        }

        $snapshotLokasi = $kontrak->data_lokasi_snapshot ?? [];

        if ($lokasi) {
            $snapshotLokasi = array_merge($snapshotLokasi, [
                'terminal' => $lokasi->terminal,
                'lantai' => $lokasi->lantai,
                'jenis_ruangan' => $lokasi->jenis_ruangan,
                'nomor_ro' => $lokasi->nomor_ro,
                'kode_ruang' => $lokasi->kode_ruang,
                'kode_tampilan' => $lokasi->kode_tampilan,
                'lokasi' => $lokasi->lokasi,
            ]);
        }

        if (array_key_exists('luas_fasilitas_snapshot', $validated)) {
            $snapshotLokasi['luas_area'] =
                $validated['luas_fasilitas_snapshot'];
        }

        if (!empty($snapshotLokasi)) {
            $dataUpdate['data_lokasi_snapshot'] = $snapshotLokasi;
        }

        // Isi tanggal dokumen jika belum pernah disimpan.
        if (empty($kontrak->tanggal_dokumen)) {
            $dataUpdate['tanggal_dokumen'] = now()->toDateString();
        }

        // Simpan perubahan draft.
        if (!empty($dataUpdate)) {
            $kontrak->update($dataUpdate);
        }

        return response()->json([
            'success' => true,
            'message' => 'Draft dokumen kontrak berhasil disimpan.',
        ]);
    }
}