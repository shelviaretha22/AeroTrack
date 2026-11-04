<?php

namespace App\Http\Controllers;

use App\Models\KerjaSama;
use App\Models\Tenant;
use App\Models\Lokasi;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\MasterPerusahaan;
use App\Services\BeritaAcaraDokumen;
use PhpOffice\PhpWord\IOFactory;

class KerjaSamaController extends Controller
{
    /**
     * Menentukan status berdasarkan kelengkapan data
     * dan keberadaan file Berita Acara.
     */
    private function tentukanStatus(KerjaSama $kerjaSama): string
    {
        if (
            $kerjaSama->berita_acara_path &&
            Storage::disk('local')->exists($kerjaSama->berita_acara_path)
        ) {
            return 'Aktivasi';
        }

        $tenant = Tenant::where(
            'kerja_sama_id',
            $kerjaSama->id
        )->first();

        $lokasiLengkap = $tenant
            ? Lokasi::where('tenant_id', $tenant->id)->exists()
            : false;

        if (!$tenant && !$lokasiLengkap) {
            return 'Draft';
        }

        if (!$tenant || !$lokasiLengkap) {
            return 'Proses';
        }

        return 'Pengajuan BA';
    }

    /**
     * Menampilkan daftar Kerja Sama.
     */
    public function index()
    {
        $kerjaSamas = KerjaSama::with(['tenant', 'lokasi'])
            ->latest()
            ->get();

        foreach ($kerjaSamas as $kerjaSama) {
            $kerjaSama->status_otomatis =
                $this->tentukanStatus($kerjaSama);
        }

        return view('kerja-sama.index', compact('kerjaSamas'));
    }

    /**
     * Form tambah Kerja Sama.
     */
    public function create()
    {
        return view('kerja-sama.create');
    }

    /**
 * Menyimpan Kerja Sama baru.
 */
public function store(Request $request)
{
    $validated = $request->validate([
        'mitra_usaha' => ['required', 'string', 'max:255'],
        'brand' => ['nullable', 'string', 'max:255'],
        'jenis_usaha' => [
            'required',
            'in:F&B,Retail,Service,Lounge,Duty Free,UMKM',
        ],
        'tanggal_pengajuan' => ['required', 'date'],
        'tanggal_mulai' => ['nullable', 'date'],
        'tanggal_berakhir' => [
            'nullable',
            'date',
            'after_or_equal:tanggal_mulai',
        ],
        'pic_commercial' => ['required', 'string', 'max:255'],
        'jabatan_pic_commercial' => ['nullable', 'string', 'max:255'],
        'catatan' => ['nullable', 'string'],
    ]);

    DB::transaction(function () use ($validated) {
        $nomorTerakhir = KerjaSama::whereNotNull('nomor_kerja_sama')
            ->lockForUpdate()
            ->pluck('nomor_kerja_sama')
            ->map(function ($nomor) {
                if (preg_match('/^KS-(\d+)$/', $nomor, $matches)) {
                    return (int) $matches[1];
                }

                return 0;
            })
            ->max() ?? 0;

        $nomorBaru = 'KS-' . str_pad(
            $nomorTerakhir + 1,
            3,
            '0',
            STR_PAD_LEFT
        );

        $kerjaSama = new KerjaSama();

        $kerjaSama->fill($validated);

        // Bentuk kerja sama ditetapkan otomatis.
        $kerjaSama->bentuk_kerja_sama = 'Revenue Sharing';

        $kerjaSama->nomor_kerja_sama = $nomorBaru;
        $kerjaSama->status = 'Draft';

        $kerjaSama->save();
    });

    return redirect()
        ->route('kerja-sama')
        ->with('success', 'Data kerja sama berhasil disimpan.');
}

    /**
     * Detail Kerja Sama.
     */
    public function show($id)
    {
        $kerjaSama = KerjaSama::with(['tenant', 'lokasi'])
            ->findOrFail($id);

        $kerjaSama->status_otomatis =
            $this->tentukanStatus($kerjaSama);

        return view('kerja-sama.show', compact('kerjaSama'));
    }

    /**
     * Form edit Kerja Sama.
     */
    public function edit($id)
    {
        $kerjaSama = KerjaSama::findOrFail($id);

        return view('kerja-sama.edit', compact('kerjaSama'));
    }

    /**
     * Memperbarui Kerja Sama.
     */
    public function update(Request $request, $id)
    {
        $kerjaSama = KerjaSama::findOrFail($id);

        $validated = $request->validate([
    'mitra_usaha' => ['required', 'string', 'max:255'],
    'brand' => ['nullable', 'string', 'max:255'],
    'jenis_usaha' => [
        'required',
        'in:F&B,Retail,Service,Lounge,Duty Free,UMKM',
    ],
    'tanggal_pengajuan' => ['required', 'date'],
    'tanggal_mulai' => ['nullable', 'date'],
    'tanggal_berakhir' => [
        'nullable',
        'date',
        'after_or_equal:tanggal_mulai',
    ],
    'pic_commercial' => ['required', 'string', 'max:255'],
    'jabatan_pic_commercial' => ['nullable', 'string', 'max:255'],
    'catatan' => ['nullable', 'string'],
]);

        // Status dan path BA tidak boleh diubah dari form edit.
        unset(
            $validated['status'],
            $validated['berita_acara_path']
        );

        // Bentuk kerja sama ditetapkan otomatis.
        $validated['bentuk_kerja_sama'] = 'Revenue Sharing';

        $kerjaSama->update($validated);

        return redirect()
            ->route('kerja-sama')
            ->with('success', 'Data kerja sama berhasil diperbarui.');
    }

    /**
     * Membuat PDF jika belum tersedia
     * dan menampilkan halaman editor BA.
     */
    public function beritaAcara($id)
    {
        $kerjaSama = KerjaSama::with(['tenant', 'lokasi'])
            ->findOrFail($id);

        $tenant = $kerjaSama->tenant;
        $lokasi = $kerjaSama->lokasi;

        if (!$tenant || !$lokasi) {
            return redirect()
                ->route('kerja-sama')
                ->with(
                    'error',
                    'Data Tenant dan Lokasi harus lengkap sebelum BA dibuat.'
                );
        }

        $path = $kerjaSama->berita_acara_path;

        // Generate PDF awal jika belum tersedia.
        if (
            !$path ||
            !Storage::disk('local')->exists($path)
        ) {
            $konten = $this->kontenBeritaAcara($kerjaSama);

            try {
                $path = $this->simpanPdfBeritaAcara($kerjaSama, $konten);
            } catch (\Throwable $e) {
                report($e);
                $path = null;
            }

            if (!$path || !Storage::disk('local')->exists($path)) {
                return redirect()
                    ->route('kerja-sama')
                    ->with(
                        'error',
                        'PDF BA gagal disimpan. Status belum diubah.'
                    );
            }

            $kerjaSama->berita_acara_path = $path;
            $kerjaSama->status = 'Aktivasi';
            $kerjaSama->save();
        } elseif ($kerjaSama->status !== 'Aktivasi') {
            $kerjaSama->status = 'Aktivasi';
            $kerjaSama->save();
        }

        // Konten editor: hasil simpan, atau template jika belum pernah disimpan.
        $editorContent = $this->kontenBeritaAcara($kerjaSama);

        return view(
            'kerja-sama.berita-acara-preview',
            compact('kerjaSama', 'editorContent')
        );
    }

    /**
     * Menampilkan PDF BA di browser.
     */
    public function lihatPdfBeritaAcara($id)
    {
        $kerjaSama = KerjaSama::findOrFail($id);
        $path = $kerjaSama->berita_acara_path;

        abort_unless(
            $path && Storage::disk('local')->exists($path),
            404,
            'File Berita Acara tidak ditemukan.'
        );

        return response()->file(
            Storage::disk('local')->path($path),
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="Berita-Acara-KS-'
                    . $kerjaSama->id
                    . '.pdf"',
            ]
        );
    }

    /**
     * Mengunduh PDF BA.
     */
    public function downloadBeritaAcara($id)
    {
        $kerjaSama = KerjaSama::findOrFail($id);
        $path = $kerjaSama->berita_acara_path;

        abort_unless(
            $path && Storage::disk('local')->exists($path),
            404,
            'File Berita Acara tidak ditemukan.'
        );

        return Storage::disk('local')->download(
            $path,
            'Berita-Acara-KS-' . $kerjaSama->id . '.pdf'
        );
    }

    /**
     * Mengunggah BA final yang sudah ditandatangani.
     */
    public function unggahBeritaAcaraFinal(Request $request, $id)
    {
        $kerjaSama = KerjaSama::findOrFail($id);

        if (!$kerjaSama->baTersedia()) {
            return back()->with(
                'error',
                'Buat Berita Acara terlebih dahulu sebelum mengunggah BA final.'
            );
        }

        if ($kerjaSama->baFinalTersedia()) {
            return back()->with(
                'error',
                'BA final sudah diunggah dan tidak dapat diganti.'
            );
        }

        $request->validate([
            'berita_acara_final' => [
                'required',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:10240',
            ],
        ], [
            'berita_acara_final.required' => 'Pilih file BA final terlebih dahulu.',
            'berita_acara_final.mimes' => 'BA final harus berupa PDF, JPG, JPEG, atau PNG.',
            'berita_acara_final.max' => 'Ukuran BA final maksimal 10 MB.',
        ]);

        $pathBaru = $request->file('berita_acara_final')
            ->store('berita-acara/final', 'local');

        if (!$pathBaru) {
            return back()->with('error', 'File BA final gagal disimpan.');
        }

        $kerjaSama->berita_acara_final_path = $pathBaru;
        $kerjaSama->berita_acara_final_uploaded_at = now();
        $kerjaSama->save();

        return back()->with('success', 'BA final berhasil diunggah.');
    }

    /**
     * Menampilkan BA final.
     */
    public function lihatBeritaAcaraFinal($id)
    {
        $kerjaSama = KerjaSama::findOrFail($id);

        abort_unless(
            $kerjaSama->baFinalTersedia(),
            404,
            'BA final belum diunggah.'
        );

        return response()->file(
            Storage::disk('local')->path(
                $kerjaSama->berita_acara_final_path
            )
        );
    }

    /**
     * Konten BA: hasil edit yang tersimpan, atau template bila belum ada.
     */
    private function kontenBeritaAcara(KerjaSama $kerjaSama): string
    {
        if (!empty($kerjaSama->berita_acara_content)) {
            return $kerjaSama->berita_acara_content;
        }

        $kerjaSama->loadMissing(['tenant', 'lokasi']);

        return view('kerja-sama.berita-acara-editor-content', [
            'kerjaSama'   => $kerjaSama,
            'tenant'      => $kerjaSama->tenant,
            'lokasi'      => $kerjaSama->lokasi,
            'perusahaan'  => MasterPerusahaan::first(),
        ])->render();
    }

    /**
     * Membuat PDF dari konten BA dan menyimpannya di disk lokal.
     * Mengembalikan path file, atau null jika gagal.
     */
    private function simpanPdfBeritaAcara(KerjaSama $kerjaSama, string $konten): ?string
    {
        $pdf = Pdf::loadView('kerja-sama.berita-acara-dokumen-pdf', [
            'konten' => $konten,
        ])->setPaper('a4', 'portrait');

        $path = $kerjaSama->berita_acara_path
            ?: 'berita-acara/BA-KS-' . $kerjaSama->id . '.pdf';

        $tersimpan = Storage::disk('local')->put($path, $pdf->output());

        return $tersimpan ? $path : null;
    }

    /**
     * Menyimpan hasil edit Berita Acara.
     */
    public function simpanBeritaAcara(Request $request, $id)
    {
        $request->validate([
            'content' => ['required', 'string', 'max:2000000'],
        ]);

        $kerjaSama = KerjaSama::findOrFail($id);

        if ($kerjaSama->baFinalTersedia()) {
            return back()->with(
                'error',
                'BA final sudah diunggah, isi Berita Acara tidak dapat diubah lagi.'
            );
        }

        $content = BeritaAcaraDokumen::bersihkanHtml(
            $request->input('content')
        );

        if (trim(strip_tags($content)) === '') {
            return back()->with(
                'error',
                'Isi Berita Acara tidak boleh kosong.'
            );
        }

        $kerjaSama->berita_acara_content = $content;
        $kerjaSama->save();

        // PDF disinkronkan agar "Unduh PDF" memuat hasil edit terbaru.
        try {
            $path = $this->simpanPdfBeritaAcara($kerjaSama, $content);

            if ($path && $kerjaSama->berita_acara_path !== $path) {
                $kerjaSama->berita_acara_path = $path;
                $kerjaSama->save();
            }
        } catch (\Throwable $e) {
            report($e);

            return redirect()
                ->route('kerja-sama.berita-acara', $id)
                ->with(
                    'error',
                    'Perubahan tersimpan, tetapi PDF gagal diperbarui: '
                        . $e->getMessage()
                );
        }

        return redirect()
            ->route('kerja-sama.berita-acara', $id)
            ->with('success', 'Berita Acara berhasil disimpan.');
    }

    /**
     * Mengunduh Berita Acara dalam format Word.
     */
    public function downloadBeritaAcaraWord($id)
    {
        $kerjaSama = KerjaSama::with(['tenant', 'lokasi'])->findOrFail($id);

        if (!$kerjaSama->tenant || !$kerjaSama->lokasi) {
            return redirect()
                ->route('kerja-sama')
                ->with(
                    'error',
                    'Data Tenant dan Lokasi harus lengkap sebelum BA dibuat.'
                );
        }

        $tempFile = null;

        try {
            $phpWord = BeritaAcaraDokumen::buatWord(
                $this->kontenBeritaAcara($kerjaSama)
            );

            $tempFile = tempnam(sys_get_temp_dir(), 'aerotrack_ba_');

            if ($tempFile === false) {
                throw new \RuntimeException('Gagal membuat file sementara.');
            }

            IOFactory::createWriter($phpWord, 'Word2007')->save($tempFile);

            return response()
                ->download(
                    $tempFile,
                    'Berita-Acara-KS-' . $kerjaSama->id . '.docx'
                )
                ->deleteFileAfterSend(true);
        } catch (\Throwable $e) {
            report($e);

            if ($tempFile && is_file($tempFile)) {
                @unlink($tempFile);
            }

            return redirect()
                ->route('kerja-sama.berita-acara', $id)
                ->with(
                    'error',
                    'Gagal membuat dokumen Word: ' . $e->getMessage()
                );
        }
    }

    /**
     * Menghapus Kerja Sama dan melepaskan lokasi dari Tenant.
     */
    public function destroy($id)
    {
        $kerjaSama = KerjaSama::with('tenant')->findOrFail($id);

        DB::transaction(function () use ($kerjaSama) {
            $tenant = $kerjaSama->tenant;

            if (!$tenant) {
                $tenant = Tenant::where(
                    'kerja_sama_id',
                    $kerjaSama->id
                )->first();
            }

            if ($tenant) {
                Lokasi::where('tenant_id', $tenant->id)
                    ->update([
                        'tenant_id' => null,
                        'status' => 'Kosong',
                    ]);

                $tenant->delete();
            }

            $kerjaSama->delete();
        });

        if (
            $kerjaSama->berita_acara_path &&
            Storage::disk('local')->exists(
                $kerjaSama->berita_acara_path
            )
        ) {
            Storage::disk('local')->delete(
                $kerjaSama->berita_acara_path
            );
        }

        if (
            $kerjaSama->berita_acara_final_path &&
            Storage::disk('local')->exists(
                $kerjaSama->berita_acara_final_path
            )
        ) {
            Storage::disk('local')->delete(
                $kerjaSama->berita_acara_final_path
            );
        }

        return redirect()
            ->route('kerja-sama')
            ->with('success', 'Data kerja sama berhasil dihapus.');
    }
}