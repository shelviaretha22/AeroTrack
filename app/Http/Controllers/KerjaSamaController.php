<?php

namespace App\Http\Controllers;

use App\Models\KerjaSama;
use App\Models\Tenant;
use App\Models\Lokasi;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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
        'bentuk_kerja_sama' => [
            'required',
            'in:Revenue Sharing,MGRS',
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
        // Cari nomor kerja sama terbesar yang sudah digunakan.
        $nomorTerakhir = KerjaSama::whereNotNull('nomor_kerja_sama')
            ->pluck('nomor_kerja_sama')
            ->map(function ($nomor) {
                if (preg_match('/^KS-(\d+)$/', $nomor, $matches)) {
                    return (int) $matches[1];
                }

                return 0;
            })
            ->max() ?? 0;

        // Buat nomor berikutnya secara otomatis.
        $nomorBaru = 'KS-' . str_pad(
            $nomorTerakhir + 1,
            3,
            '0',
            STR_PAD_LEFT
        );

        // Simpan data kerja sama baru.
        $kerjaSama = new KerjaSama();
        $kerjaSama->fill($validated);

        // Nomor dan status ditentukan oleh sistem.
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
            'bentuk_kerja_sama' => [
                'required',
                'in:Revenue Sharing,MGRS',
            ],
            'tanggal_pengajuan' => ['required', 'date'],
            'tanggal_mulai' => ['nullable', 'date'],
            'tanggal_berakhir' => [
                'nullable',
                'date',
                'after_or_equal:tanggal_mulai',
            ],
            'pic_commercial' => ['required', 'string', 'max:255'],

            // Jabatan PIC Commercial boleh dikosongkan.
            'jabatan_pic_commercial' => ['nullable', 'string', 'max:255'],

            'catatan' => ['nullable', 'string'],
        ]);

        // Status dan path BA tidak boleh diubah dari form edit.
        unset(
            $validated['status'],
            $validated['berita_acara_path']
        );

        $kerjaSama->update($validated);

        return redirect()
            ->route('kerja-sama')
            ->with('success', 'Data kerja sama berhasil diperbarui.');
    }

    /**
     * Membuat dan menyimpan PDF BA jika belum tersedia,
     * kemudian menampilkan halaman preview.
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

        // Buat ulang PDF jika file belum ada.
        if (
            !$path ||
            !Storage::disk('local')->exists($path)
        ) {
            $pdf = Pdf::loadView(
                'kerja-sama.berita-acara-pdf',
                [
                    'kerjaSama' => $kerjaSama,
                    'tenant' => $tenant,
                    'lokasi' => $lokasi,
                    'tanggalCetak' => now(),
                ]
            )->setPaper('a4', 'portrait');

            $path = 'berita-acara/BA-KS-'
                . $kerjaSama->id
                . '.pdf';

            $tersimpan = Storage::disk('local')->put(
                $path,
                $pdf->output()
            );

            if (
                !$tersimpan ||
                !Storage::disk('local')->exists($path)
            ) {
                return redirect()
                    ->route('kerja-sama')
                    ->with(
                        'error',
                        'PDF BA gagal disimpan. Status belum diubah.'
                    );
            }

            // Status baru diubah setelah file berhasil disimpan.
            $kerjaSama->berita_acara_path = $path;
            $kerjaSama->status = 'Aktivasi';
            $kerjaSama->save();
        } elseif ($kerjaSama->status !== 'Aktivasi') {
            // Sinkronkan status jika file sudah ada.
            $kerjaSama->status = 'Aktivasi';
            $kerjaSama->save();
        }

        return view(
            'kerja-sama.berita-acara-preview',
            compact('kerjaSama')
        );
    }

    /**
     * Menampilkan file PDF BA di browser.
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

        // Hapus file BA setelah data berhasil dihapus.
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

        return redirect()
            ->route('kerja-sama')
            ->with('success', 'Data kerja sama berhasil dihapus.');
    }
}
