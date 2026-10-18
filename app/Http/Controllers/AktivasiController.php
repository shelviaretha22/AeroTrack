<?php

namespace App\Http\Controllers;

use App\Models\Aktivasi;
use App\Models\KerjaSama;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
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
            'lokasi',
            'aktivasi',
        ])
            ->whereNotNull('berita_acara_path')
            ->latest()
            ->get()
            ->filter(function ($kerjaSama) {
                return Storage::disk('local')->exists(
                    $kerjaSama->berita_acara_path
                );
            });

        $dataAktivasi = $semuaData;

        if ($request->filled('search')) {
            $keyword = strtolower(trim($request->search));

            $dataAktivasi = $dataAktivasi->filter(
                function ($kerjaSama) use ($keyword) {
                    $aktivasi = $kerjaSama->aktivasi;
                    $tenant = $kerjaSama->tenant;

                    $dataCari = [
                        $aktivasi?->nomor_pengajuan,
                        $kerjaSama->mitra_usaha,
                        $kerjaSama->brand,
                        $tenant?->nama_tenant,
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

        if ($request->filled('status')) {
            $statusFilter = $request->status;

            $dataAktivasi = $dataAktivasi->filter(
                function ($kerjaSama) use ($statusFilter) {
                    return (
                        $kerjaSama->aktivasi?->status
                        ?? 'Belum Diajukan'
                    ) === $statusFilter;
                }
            );
        }

        $dataAktivasi = $dataAktivasi->values();

        $totalAktivasi = $semuaData->count();

        $menunggu = $semuaData->filter(function ($kerjaSama) {
            return $kerjaSama->aktivasi?->status
                === 'Menunggu Keputusan';
        })->count();

        $disetujui = $semuaData->filter(function ($kerjaSama) {
            return $kerjaSama->aktivasi?->status === 'Disetujui';
        })->count();

        $ditolak = $semuaData->filter(function ($kerjaSama) {
            return $kerjaSama->aktivasi?->status === 'Ditolak';
        })->count();

        return view('aktivasi.index', compact(
            'dataAktivasi',
            'totalAktivasi',
            'menunggu',
            'disetujui',
            'ditolak'
        ));
    }

    /**
     * Membuat pengajuan aktivasi baru dan PDF secara otomatis.
     */
    public function requestAktivasi($kerjaSamaId)
    {
        $aktivasi = null;
        $pathSurat = null;

        try {
            $aktivasi = DB::transaction(function () use ($kerjaSamaId) {
                $kerjaSama = KerjaSama::with([
                    'tenant',
                    'lokasi',
                ])
                    ->lockForUpdate()
                    ->findOrFail($kerjaSamaId);

                if (
                    !$kerjaSama->berita_acara_path ||
                    !Storage::disk('local')->exists(
                        $kerjaSama->berita_acara_path
                    )
                ) {
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
            $aktivasi->save();

            return redirect()
                ->route('aktivasi')
                ->with(
                    'success',
                    'Pengajuan aktivasi berhasil dibuat dan surat PDF sudah tersedia.'
                );
        } catch (Throwable $e) {
            report($e);

            if ($pathSurat) {
                Storage::disk('local')->delete($pathSurat);
            }

            if ($e instanceof \RuntimeException) {
                return back()->with('error', $e->getMessage());
            }

            return back()->with(
                'error',
                'Pengajuan gagal dibuat. Periksa konfigurasi PDF dan log Laravel.'
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
                'max:5120',
            ],
            'catatan' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ], [
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
                $data->catatan = $validated['catatan'] ?? null;
                $data->save();
            });

            return redirect()
                ->route('aktivasi')
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

        return response()->file(
            Storage::disk('local')->path(
                $aktivasi->surat_pengajuan_path
            ),
            [
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

        return Storage::disk('local')->download(
            $aktivasi->dokumen_path
        );
    }
}