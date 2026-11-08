<?php

namespace App\Http\Controllers;

use App\Rules\AmanDariMalware;
use App\Services\IntegritasDokumen;
use App\Models\Aktivasi;
use App\Models\AktivasiProgres;
use App\Models\AktivasiRiwayat;
use App\Models\AuditLog;
use App\Models\KerjaSama;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class AktivasiController extends Controller
{
    /**
     * Menampilkan kerja sama yang BA-nya sudah tersedia.
     */
    public function index(Request $request)
    {
        $semuaData = KerjaSama::with([
            'tenant',
            'lokasi.tenant',
            'aktivasi',
            'kontrakTerbaru',
        ])
            ->latest()
            ->get();

        $dataAktivasi = $semuaData;

        if ($request->filled('search')) {
            $keyword = strtolower(trim($request->search));

            $dataAktivasi = $dataAktivasi->filter(
                function ($kerjaSama) use ($keyword) {
                    $dataCari = [
                        $kerjaSama->aktivasi?->nomor_pengajuan,
                        $kerjaSama->mitra_usaha,
                        $kerjaSama->brand,
                        $kerjaSama->tenant?->nama_tenant,
                        (string) $kerjaSama->id,
                    ];

                    foreach ($dataCari as $nilai) {
                        if (
                            $nilai !== null &&
                            str_contains(
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

        if ($request->filled('kontrak_fisik')) {
            $dataAktivasi = $dataAktivasi->filter(
                fn ($ks) => $ks->posisiKontrakFisik()
                    === $request->kontrak_fisik
            );
        }

        if ($request->filled('kontrak_sistem')) {
            $dataAktivasi = $dataAktivasi->filter(
                fn ($ks) => $ks->statusKontrakSistem()
                    === $request->kontrak_sistem
            );
        }

        if ($request->filled('status')) {
            $dataAktivasi = $dataAktivasi->filter(
                fn ($ks) => $ks->statusAktivasi() === $request->status
            );
        }

        /*
         * DATA DIAGRAM
         * Dihitung dari seluruh kerja sama, tidak terpengaruh filter tabel.
         */
        $perAktivasi = collect(KerjaSama::STATUS_AKTIVASI)->mapWithKeys(
            fn ($status) => [
                $status => $semuaData
                    ->filter(fn ($ks) => $ks->statusAktivasi() === $status)
                    ->count(),
            ]
        );

        $perKontrakFisik = collect(KerjaSama::POSISI_KONTRAK_FISIK)->mapWithKeys(
            fn ($posisi) => [
                $posisi => $semuaData
                    ->filter(fn ($ks) => $ks->posisiKontrakFisik() === $posisi)
                    ->count(),
            ]
        );

        $perTerminal = $semuaData
            ->groupBy(fn ($ks) => $ks->lokasi?->terminal ?: 'Belum ada lokasi')
            ->map(fn ($grup) => [
                'total' => $grup->count(),
                'aktif' => $grup
                    ->filter(fn ($ks) => $ks->statusKontrakSistem() === 'Aktif')
                    ->count(),
                'menunggu' => $grup
                    ->filter(fn ($ks) => $ks->statusAktivasi() === 'Menunggu Keputusan')
                    ->count(),
            ])
            ->sortKeys();

        // Kontrak sistem yang sudah aktif dan akan berakhir dalam 90 hari
        // (atau sudah lewat), diurutkan dari yang paling mendesak.
        $segeraBerakhir = $semuaData
            ->filter(fn ($ks) => $ks->tanggal_berakhir
                && $ks->statusKontrakSistem() === 'Aktif'
                && $ks->tanggal_berakhir->lte(now()->addDays(90)))
            ->sortBy('tanggal_berakhir')
            ->take(5)
            ->values();

        return view('aktivasi.index', [
            'perAktivasi' => $perAktivasi,
            'perKontrakFisik' => $perKontrakFisik,
            'perTerminal' => $perTerminal,
            'segeraBerakhir' => $segeraBerakhir,
            'dataAktivasi' => $dataAktivasi->values(),
            'totalAktivasi' => $semuaData->count(),
            'menunggu' => $semuaData->filter(
                fn ($ks) => $ks->statusAktivasi() === 'Menunggu Keputusan'
            )->count(),
            'disetujui' => $semuaData->filter(
                fn ($ks) => $ks->statusAktivasi() === 'Disetujui'
            )->count(),
            'sistemAktif' => $semuaData->filter(
                fn ($ks) => $ks->statusKontrakSistem() === 'Aktif'
            )->count(),
            'opsiKontrakFisik' => KerjaSama::POSISI_KONTRAK_FISIK,
            'opsiKontrakSistem' => KerjaSama::STATUS_KONTRAK_SISTEM,
            'opsiAktivasi' => KerjaSama::STATUS_AKTIVASI,
        ]);
    }

    /**
     * Halaman Detail: tiga bagian per Kerja Sama.
     */
    public function show(KerjaSama $kerjaSama)
    {
        $kerjaSama->load([
            'tenant',
            'lokasi.tenant',
            'aktivasi',
            'aktivasis' => fn ($q) => $q->orderByDesc('id'),
            'progres',
            'kontrakTerbaru',
            'aktivasiRiwayats.user',
        ]);

        return view('aktivasi.show', [
            'kerjaSama' => $kerjaSama,
            'tenant' => $kerjaSama->tenant,
            'lokasi' => $kerjaSama->lokasi,
            'progres' => $kerjaSama->progres,
            'kontrak' => $kerjaSama->kontrakTerbaru,
        ]);
    }

    /**
     * Catatan perkembangan Kontrak Fisik.
     * Posisi BA/kontrak dihitung otomatis dari data.
     */
    public function updateKontrakFisik(
        Request $request,
        KerjaSama $kerjaSama
    ) {
        $validated = $request->validate([
            'kontrak_fisik_catatan' => ['nullable', 'string', 'max:2000'],
        ]);

        DB::transaction(function () use ($kerjaSama, $validated) {
            $progres = AktivasiProgres::firstOrNew([
                'kerja_sama_id' => $kerjaSama->id,
            ]);

            $progres->kontrak_fisik_catatan =
                $validated['kontrak_fisik_catatan'] ?? null;
            $progres->kontrak_fisik_updated_at = now();
            $progres->save();

            $this->catatRiwayat(
                $kerjaSama->id,
                'Kontrak Fisik',
                'Catatan perkembangan diperbarui.'
            );
        });

        return redirect()
            ->route('aktivasi.show', $kerjaSama->id)
            ->with('success', 'Catatan Kontrak Fisik berhasil disimpan.');
    }

    private function catatRiwayat(
        int $kerjaSamaId,
        string $komponen,
        string $keterangan
    ): void {
        AktivasiRiwayat::create([
            'kerja_sama_id' => $kerjaSamaId,
            'user_id' => Auth::id(),
            'komponen' => $komponen,
            'keterangan' => $keterangan,
        ]);

        // Salinan ke audit log pusat (ikut IP dan perangkat).
        AuditLog::catat('Aktivasi', 'Perubahan ' . $komponen, $keterangan);
    }

    /**
     * Menyimpan tanda tangan PIC Commercial (gambar langsung / unggah).
     */
    private function simpanTtd(Request $request): string
    {
        if ($request->ttd_type === 'upload') {
            return $request->file('ttd_file')
                ->store('aktivasi/ttd', 'local');
        }

        $data = (string) $request->ttd_draw;

        if (!preg_match('#^data:image/png;base64,#', $data)) {
            throw new \RuntimeException('Tanda tangan tidak valid.');
        }

        $biner = base64_decode(
            substr($data, strpos($data, ',') + 1),
            true
        );

        if (
            $biner === false ||
            strlen($biner) > 2 * 1024 * 1024 ||
            @getimagesizefromstring($biner) === false
        ) {
            throw new \RuntimeException('Tanda tangan tidak valid.');
        }

        $path = 'aktivasi/ttd/' . uniqid('ttd_', true) . '.png';
        Storage::disk('local')->put($path, $biner);

        return $path;
    }

    /**
     * Membuat pengajuan aktivasi baru dan PDF secara otomatis.
     */
    public function requestAktivasi(Request $request, $kerjaSamaId)
    {
        $validated = $request->validate([
            'nama_penandatangan' => ['required', 'string', 'max:150'],
            'jabatan_penandatangan' => ['required', 'string', 'max:150'],
            'ttd_type' => ['required', 'in:draw,upload'],
            'ttd_draw' => ['required_if:ttd_type,draw', 'nullable', 'string'],
            'ttd_file' => [
                'required_if:ttd_type,upload',
                'nullable',
                'file',
                'mimes:png,jpg,jpeg',
                new AmanDariMalware('Aktivasi'),
                'max:2048',
            ],
        ], [
            'nama_penandatangan.required' => 'Nama penandatangan wajib diisi.',
            'jabatan_penandatangan.required' => 'Jabatan penandatangan wajib diisi.',
            'ttd_draw.required_if' => 'Tanda tangan wajib digambar.',
            'ttd_file.required_if' => 'File tanda tangan wajib diunggah.',
            'ttd_file.mimes' => 'Tanda tangan harus berupa PNG atau JPG.',
            'ttd_file.max' => 'Ukuran tanda tangan maksimal 2 MB.',
        ]);

        $aktivasi = null;
        $pathSurat = null;
        $pathTtd = null;

        try {
            $pathTtd = $this->simpanTtd($request);

            $aktivasi = DB::transaction(function () use (
                $kerjaSamaId,
                $validated,
                $pathTtd,
                $request
            ) {
                $kerjaSama = KerjaSama::with([
                    'tenant',
                    'lokasi',
                ])
                    ->lockForUpdate()
                    ->findOrFail($kerjaSamaId);

                if (!$kerjaSama->baTersedia()) {
                    throw new \RuntimeException(
                        'Pengajuan belum dapat dibuat karena dokumen BA belum tersedia.'
                    );
                }

                if (!$kerjaSama->tenant || !$kerjaSama->lokasi) {
                    throw new \RuntimeException(
                        'Data tenant atau lokasi belum lengkap.'
                    );
                }

                $pengajuanTerakhir = Aktivasi::where(
                    'kerja_sama_id',
                    $kerjaSama->id
                )
                    ->orderByDesc('id')
                    ->first();

                if (
                    $pengajuanTerakhir &&
                    $pengajuanTerakhir->status === 'Menunggu Keputusan'
                ) {
                    throw new \RuntimeException(
                        'Pengajuan aktivasi masih menunggu keputusan.'
                    );
                }

                if (
                    $pengajuanTerakhir &&
                    $pengajuanTerakhir->status === 'Disetujui'
                ) {
                    throw new \RuntimeException(
                        'Pengajuan sudah disetujui dan tidak dapat diajukan ulang.'
                    );
                }

                if (
                    $pengajuanTerakhir &&
                    $pengajuanTerakhir->status !== 'Ditolak'
                ) {
                    throw new \RuntimeException(
                        'Status pengajuan sebelumnya belum memungkinkan pengajuan baru.'
                    );
                }

                $aktivasi = Aktivasi::create([
                    'kerja_sama_id' => $kerjaSama->id,
                    'tanggal_pengajuan' => now()->toDateString(),
                    'status' => 'Menunggu Keputusan',
                    'ttd_commercial_path' => $pathTtd,
                    'ttd_type' => $validated['ttd_type'],
                    'nama_penandatangan' => $validated['nama_penandatangan'],
                    'jabatan_penandatangan' => $validated['jabatan_penandatangan'],
                ]);

                $aktivasi->nomor_pengajuan = sprintf(
                    'ACT/JUANDA/%s/%04d',
                    now()->format('Y'),
                    $aktivasi->id
                );

                $aktivasi->save();

                return $aktivasi;
            });

            $kerjaSama = KerjaSama::with([
                'tenant',
                'lokasi',
            ])->findOrFail($kerjaSamaId);

            $pdf = Pdf::loadView(
                'aktivasi.surat-pengajuan-pdf',
                [
                    'aktivasi' => $aktivasi,
                    'kerjaSama' => $kerjaSama,
                    'tenant' => $kerjaSama->tenant,
                    'lokasi' => $kerjaSama->lokasi,
                    'ttdDataUri' => $this->ttdDataUri($aktivasi),
                ]
            );

            $pathSurat = 'aktivasi/pengajuan/'
                . $aktivasi->id
                . '-surat.pdf';

            Storage::disk('local')->put(
                $pathSurat,
                $pdf->output()
            );

            $aktivasi->surat_pengajuan_path = $pathSurat;
            $aktivasi->surat_pengajuan_sha256 = IntegritasDokumen::hash($pathSurat);
            $aktivasi->save();

            $this->catatRiwayat(
                (int) $kerjaSamaId,
                'Aktivasi',
                'Pengajuan ' . $aktivasi->nomor_pengajuan
                    . ' dibuat dengan status "Menunggu Keputusan".'
            );

            return redirect()
                ->route('aktivasi.show', $kerjaSamaId)
                ->with(
                    'success',
                    'Pengajuan aktivasi berhasil dibuat dan surat PDF sudah tersedia.'
                );
        } catch (Throwable $e) {
            report($e);

            if ($pathSurat) {
                Storage::disk('local')->delete($pathSurat);
            }

            // Jika surat gagal dibuat, batalkan pengajuan agar tidak
            // tersisa data "Menunggu Keputusan" tanpa surat.
            if ($aktivasi && !$aktivasi->surat_pengajuan_path) {
                $aktivasi->delete();
            }

            if ($pathTtd) {
                Storage::disk('local')->delete($pathTtd);
            }

            if ($e instanceof \RuntimeException) {
                return back()->with('error', $e->getMessage());
            }

            $kolomHilang = $e instanceof \Illuminate\Database\QueryException
                && (
                    str_contains($e->getMessage(), 'no column')
                    || str_contains($e->getMessage(), 'Unknown column')
                    || str_contains($e->getMessage(), 'no such column')
                );

            $penyebab = $kolomHilang
                ? 'Kolom database belum ada. Jalankan "php artisan migrate" lalu coba lagi.'
                : \Illuminate\Support\Str::limit($e->getMessage(), 300);

            return back()->with(
                'error',
                'Pengajuan gagal dibuat. Penyebab: ' . $penyebab
            );
        }
    }

    /**
     * Mencatat keputusan resmi dari kantor pusat.
     */
    public function catatKeputusan(
        Request $request,
        Aktivasi $aktivasi
    ) {
        if ($aktivasi->status !== 'Menunggu Keputusan') {
            return back()->with(
                'error',
                'Keputusan hanya dapat dicatat untuk pengajuan yang masih menunggu.'
            );
        }

        $validated = $request->validate([
            'status' => [
                'required',
                'in:Disetujui,Ditolak',
            ],
            'tanggal_keputusan' => [
                'required',
                'date',
                'before_or_equal:today',
            ],
            'dokumen_keputusan' => [
                'required',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                new AmanDariMalware('Aktivasi'),
                'max:5120',
            ],
            'catatan' => [
                'nullable',
                'required_if:status,Ditolak',
                'string',
                'max:2000',
            ],
            // Keputusan tidak bisa diubah setelah disimpan, jadi password
            // diminta ulang untuk memastikan yang menekan tombol adalah pemilik akun.
            'password_konfirmasi' => ['required', 'current_password'],
        ], [
            'password_konfirmasi.required' =>
                'Masukkan password Anda untuk mengonfirmasi keputusan.',
            'password_konfirmasi.current_password' =>
                'Password yang dimasukkan tidak sesuai.',
            'catatan.required_if' =>
                'Catatan / alasan wajib diisi jika pengajuan ditolak.',
            'tanggal_keputusan.required' =>
                'Tanggal keputusan wajib diisi.',
            'tanggal_keputusan.before_or_equal' =>
                'Tanggal keputusan tidak boleh melewati hari ini.',
            'dokumen_keputusan.required' =>
                'Dokumen keputusan resmi wajib diunggah.',
            'dokumen_keputusan.mimes' =>
                'Dokumen harus berupa PDF, JPG, JPEG, atau PNG.',
            'dokumen_keputusan.max' =>
                'Ukuran dokumen maksimal 5 MB.',
        ]);

        $pathDokumen = null;

        try {
            $pathDokumen = $request->file('dokumen_keputusan')
                ->store('aktivasi/keputusan', 'local');

            DB::transaction(function () use (
                $aktivasi,
                $validated,
                $pathDokumen
            ) {
                $data = Aktivasi::whereKey($aktivasi->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($data->status !== 'Menunggu Keputusan') {
                    throw new \RuntimeException(
                        'Keputusan pengajuan ini sudah pernah dicatat.'
                    );
                }

                $data->status = $validated['status'];
                $data->tanggal_keputusan = $validated['tanggal_keputusan'];
                $data->dokumen_path = $pathDokumen;
                $data->dokumen_sha256 = IntegritasDokumen::hash($pathDokumen);
                $data->catatan = $validated['catatan'] ?? null;
                $data->save();

                // Nomor konsumen terbit otomatis saat kontrak sistem aktif.
                if ($data->status === 'Disetujui') {
                    AktivasiProgres::terbitkanNomorKonsumen((int) $data->kerja_sama_id);
                }
            });

            $this->catatRiwayat(
                (int) $aktivasi->kerja_sama_id,
                'Aktivasi',
                'Keputusan pengajuan ' . $aktivasi->nomor_pengajuan
                    . ' dicatat: ' . $validated['status'] . '.'
            );

            return redirect()
                ->route('aktivasi.show', $aktivasi->kerja_sama_id)
                ->with(
                    'success',
                    'Keputusan resmi berhasil dicatat.'
                );
        } catch (Throwable $e) {
            if ($pathDokumen) {
                Storage::disk('local')->delete($pathDokumen);
            }

            if ($e instanceof \RuntimeException) {
                return back()->with('error', $e->getMessage());
            }

            report($e);

            return back()->with(
                'error',
                'Keputusan gagal disimpan. Silakan periksa kembali.'
            );
        }
    }

    /**
     * Menampilkan surat pengajuan dalam format PDF.
     */
    public function lihatSurat(Aktivasi $aktivasi)
    {
        abort_unless(
            $aktivasi->surat_pengajuan_path &&
            Storage::disk('local')->exists(
                $aktivasi->surat_pengajuan_path
            ),
            404
        );

        $this->pastikanUtuh($aktivasi, 'surat');

        return response()->file(
            Storage::disk('local')->path(
                $aktivasi->surat_pengajuan_path
            ),
            [
                'X-Content-SHA256' => (string) $aktivasi->surat_pengajuan_sha256,
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="'
                    . $aktivasi->nomor_pengajuan
                    . '.pdf"',
            ]
        );
    }

    /**
     * Mengunduh dokumen keputusan resmi.
     */
    public function lihatDokumenKeputusan(Aktivasi $aktivasi)
    {
        abort_unless(
            $aktivasi->dokumen_path &&
            Storage::disk('local')->exists($aktivasi->dokumen_path),
            404
        );

        $this->pastikanUtuh($aktivasi, 'dokumen');

        return Storage::disk('local')->download(
            $aktivasi->dokumen_path,
            null,
            ['X-Content-SHA256' => (string) $aktivasi->dokumen_sha256]
        );
    }

    /**
     * Status keutuhan dokumen (JSON) untuk satu pengajuan.
     */
    public function integritas(Aktivasi $aktivasi)
    {
        $ringkas = fn (?string $path, ?string $hash) => IntegritasDokumen::periksa($path, $hash);

        return response()->json([
            'nomor_pengajuan'   => $aktivasi->nomor_pengajuan,
            'algoritma'         => 'SHA-256',
            'surat_pengajuan'   => $ringkas($aktivasi->surat_pengajuan_path, $aktivasi->surat_pengajuan_sha256),
            'dokumen_keputusan' => $ringkas($aktivasi->dokumen_path, $aktivasi->dokumen_sha256),
        ]);
    }

    /**
     * Cek hash dokumen sebelum dibuka. Jika isi file berbeda dari saat
     * disimpan, akses ditolak dan kejadian dicatat sebagai peringatan.
     * Dokumen lama tanpa hash dicatat hash-nya saat pertama dibuka.
     */
    private function pastikanUtuh(Aktivasi $aktivasi, string $jenis): void
    {
        [$kolomPath, $kolomHash, $label] = $jenis === 'surat'
            ? ['surat_pengajuan_path', 'surat_pengajuan_sha256', 'Surat pengajuan']
            : ['dokumen_path', 'dokumen_sha256', 'Dokumen keputusan'];

        $hasil = IntegritasDokumen::periksa($aktivasi->{$kolomPath}, $aktivasi->{$kolomHash});

        if ($hasil['status'] === 'belum-tercatat') {
            Aktivasi::whereKey($aktivasi->id)->update([$kolomHash => $hasil['dihitung']]);
            $aktivasi->{$kolomHash} = $hasil['dihitung'];

            AuditLog::catat(
                'Aktivasi',
                'Hash dokumen dicatat',
                "{$label} {$aktivasi->nomor_pengajuan}: hash awal dicatat (dokumen lama).",
                $aktivasi
            );

            return;
        }

        if ($hasil['status'] === 'berubah') {
            AuditLog::catat(
                'Aktivasi',
                'Integritas dokumen gagal',
                "{$label} {$aktivasi->nomor_pengajuan}: hash SHA-256 tidak cocok dengan yang tersimpan.",
                $aktivasi,
                'peringatan'
            );

            abort(409, 'Dokumen tidak dapat dibuka karena isinya berbeda dari saat disimpan (pemeriksaan integritas gagal).');
        }
    }

    private function ttdDataUri(Aktivasi $aktivasi): ?string
    {
        $path = $aktivasi->ttd_commercial_path;

        if (!$path || !Storage::disk('local')->exists($path)) {
            return null;
        }

        $mime = Storage::disk('local')->mimeType($path) ?: 'image/png';

        return 'data:' . $mime . ';base64,'
            . base64_encode(Storage::disk('local')->get($path));
    }
}
