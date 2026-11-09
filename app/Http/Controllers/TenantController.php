<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\KerjaSama;
use Illuminate\Http\Request;
use App\Models\Lokasi;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TenantReportExport;

class TenantController extends Controller
{
    /**
     * Menampilkan halaman Tenant.
     *
     * Data utama halaman ini berasal dari Kerja Sama.
     * Setiap data Kerja Sama dapat memiliki satu data Tenant.
     */
        public function index(Request $request)
    {
        // =====================================================
        // QUERY DASAR KERJA SAMA UNTUK TABEL TENANT
        // =====================================================
        $query = KerjaSama::query();

        // SEARCH
        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('mitra_usaha', 'like', '%' . $search . '%')
                    ->orWhere('brand', 'like', '%' . $search . '%')
                    ->orWhere('jenis_usaha', 'like', '%' . $search . '%')
                    ->orWhere('bentuk_kerja_sama', 'like', '%' . $search . '%')
                    ->orWhere('pic_commercial', 'like', '%' . $search . '%');
            });
        }

        // SORTING
        $sort = $request->get('sort', 'newest');

        switch ($sort) {
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;

            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;

            case 'az':
                $query->orderBy('mitra_usaha', 'asc');
                break;

            case 'za':
                $query->orderBy('mitra_usaha', 'desc');
                break;

            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        // AMBIL DATA KERJA SAMA
        $kerjaSamas = $query->get();

        // AMBIL DATA TENANT YANG SUDAH TERHUBUNG
        $tenantByKerjaSama = Tenant::whereNotNull('kerja_sama_id')
            ->get()
            ->keyBy('kerja_sama_id');


        // =====================================================
        // EXECUTIVE SUMMARY
        // =====================================================

        // TOTAL TENANT
        $totalTenant = Tenant::count();

        // TOTAL JENIS USAHA
        $totalJenisUsaha = Tenant::query()
            ->whereNotNull('jenis_usaha')
            ->where('jenis_usaha', '<>', '')
            ->distinct()
            ->count('jenis_usaha');


        // =====================================================
        // GRAFIK 1: KOMPOSISI JENIS USAHA
        // SUMBER: TABEL TENANTS
        // =====================================================

        $komposisiJenisUsaha = Tenant::query()
            ->select('jenis_usaha')
            ->selectRaw('COUNT(*) as total')
            ->whereNotNull('jenis_usaha')
            ->where('jenis_usaha', '<>', '')
            ->groupBy('jenis_usaha')
            ->orderByDesc('total')
            ->get();


        // =====================================================
        // GRAFIK 2: PERSEBARAN TENANT PER TERMINAL
        // SUMBER: TABEL LOKASIS DAN TENANTS
        // HANYA LOKASI TERISI
        // =====================================================

        $persebaranTerminal = Lokasi::query()
            ->join('tenants', 'lokasis.tenant_id', '=', 'tenants.id')
            ->where('lokasis.status', 'Terisi')
            ->whereNotNull('lokasis.tenant_id')
            ->select('lokasis.terminal')
            ->selectRaw('COUNT(DISTINCT lokasis.tenant_id) as total')
            ->groupBy('lokasis.terminal')
            ->orderBy('lokasis.terminal')
            ->get();

        // TOTAL TERMINAL YANG MEMILIKI TENANT
        $totalTerminalTerisi = $persebaranTerminal->count();


        // =====================================================
        // GRAFIK 3: TREN AKUISISI TENANT
        // SUMBER: KERJA_SAMAS.TANGGAL_PENGAJUAN
        // PERIODE: 6 BULAN TERAKHIR
        // =====================================================

        $periodeMulai = now()->startOfMonth()->subMonths(5);
        $periodeSelesai = now()->endOfMonth();

        // HITUNG PENGAJUAN PER BULAN
        $pengajuanPerBulan = KerjaSama::query()
            ->selectRaw("
                DATE_FORMAT(tanggal_pengajuan, '%Y-%m') as periode,
                COUNT(*) as total
            ")
            ->whereNotNull('tanggal_pengajuan')
            ->whereBetween('tanggal_pengajuan', [
                $periodeMulai->toDateString(),
                $periodeSelesai->toDateString()
            ])
            ->groupBy('periode')
            ->get()
            ->keyBy('periode');

        // SIAPKAN 6 BULAN, TERMASUK BULAN TANPA PENGAJUAN
        $trenAkuisisiTenant = collect(range(0, 5))
            ->map(function ($offset) use ($periodeMulai, $pengajuanPerBulan) {

                $bulan = $periodeMulai->copy()->addMonths($offset);
                $periode = $bulan->format('Y-m');

                return [
                    'periode' => $periode,
                    'label' => $bulan->format('M Y'),
                    'total' => (int) (
                        $pengajuanPerBulan->get($periode)->total ?? 0
                    ),
                ];
            });

        // TOTAL PENGAJUAN DALAM 6 BULAN
        $totalPengajuanEnamBulan = $trenAkuisisiTenant->sum('total');


        // =====================================================
        // KIRIM DATA KE VIEW
        // =====================================================

        return view('tenant.index', compact(
            'kerjaSamas',
            'tenantByKerjaSama',

            // EXECUTIVE SUMMARY
            'totalTenant',
            'totalJenisUsaha',
            'totalTerminalTerisi',
            'totalPengajuanEnamBulan',

            // GRAFIK
            'komposisiJenisUsaha',
            'persebaranTerminal',
            'trenAkuisisiTenant'
        ));
    }
    /**
     * Menampilkan form input Tenant berdasarkan Kerja Sama.
     */
    public function create(Request $request)
    {
        // VALIDASI KERJA SAMA ID
        $request->validate([
            'kerja_sama_id' => [
                'required',
                'integer',
                'exists:kerja_samas,id',
            ],
        ]);

        // AMBIL DATA KERJA SAMA
        $kerjaSama = KerjaSama::findOrFail(
            $request->kerja_sama_id
        );

        // CEK APAKAH KERJA SAMA SUDAH MEMILIKI TENANT
        $tenant = Tenant::where(
            'kerja_sama_id',
            $kerjaSama->id
        )->first();

        if ($tenant) {
            return redirect()
                ->route('tenant.show', $tenant)
                ->with(
                    'info',
                    'Data Tenant untuk Kerja Sama ini sudah diinput.'
                );
        }

        return view(
            'tenant.create',
            compact('kerjaSama')
        );
    }

    /**
     * Menyimpan data Tenant baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kerja_sama_id' => [
                'required',
                'integer',
                'exists:kerja_samas,id',
            ],

            'mitra_usaha' => [
                'required',
                'string',
                'max:255',
            ],

            'nama_tenant' => [
                'required',
                'string',
                'max:255',
            ],

            'jenis_usaha' => [
                'required',
                'string',
                'max:255',
            ],

            'bentuk_kerja_sama' => [
                'required',
                'string',
                'max:255',
            ],

            // INFORMASI KOMERSIAL TENANT
            'gto' => [
                'required',
                'numeric',
                'min:0',
            ],

            'revenue_sharing' => [
                'required',
                'numeric',
                'between:0,100',
            ],

            // LEGALITAS TENANT
            'npwp' => [
                'required',
                'regex:/^[0-9]+$/',
                'max:30',
            ],

            'nib' => [
                'required',
                'regex:/^[0-9]+$/',
                'max:30',
            ],

            'bentuk_badan_usaha' => [
                'required',
                'string',
                'max:255',
            ],

            'alamat_perusahaan' => [
                'required',
                'string',
            ],

            // PIC TENANT
            'nama_pic' => [
                'required',
                'regex:/^[A-Za-zÀ-ÿ\s]+$/',
                'max:255',
            ],

            'jabatan_pic' => [
                'required',
                'string',
                'max:255',
            ],

            'no_hp_pic' => [
                'required',
                'regex:/^[0-9]+$/',
                'max:20',
            ],

            'email_pic' => [
                'required',
                'email',
                'max:255',
            ],
        ], [
            // PESAN VALIDASI KERJA SAMA
            'kerja_sama_id.required' => 'Data Kerja Sama tidak ditemukan.',
            'kerja_sama_id.exists' => 'Data Kerja Sama tidak valid.',

            // PESAN VALIDASI INFORMASI KOMERSIAL
            'gto.required' => 'GTO wajib diisi.',
            'gto.numeric' => 'GTO harus berupa angka.',
            'gto.min' => 'GTO tidak boleh bernilai negatif.',

            'revenue_sharing.required' => 'Revenue Sharing wajib diisi.',
            'revenue_sharing.numeric' => 'Revenue Sharing harus berupa angka.',
            'revenue_sharing.between' => 'Revenue Sharing harus antara 0 sampai 100.',

            // PESAN VALIDASI LEGALITAS
            'npwp.required' => 'NPWP wajib diisi.',
            'npwp.regex' => 'NPWP hanya boleh berisi angka.',

            'nib.required' => 'NIB wajib diisi.',
            'nib.regex' => 'NIB hanya boleh berisi angka.',

            'bentuk_badan_usaha.required' => 'Bentuk badan usaha wajib dipilih.',

            'alamat_perusahaan.required' => 'Alamat perusahaan/usaha wajib diisi.',

            // PESAN VALIDASI PIC
            'nama_pic.required' => 'Nama PIC wajib diisi.',
            'nama_pic.regex' => 'Nama PIC hanya boleh berisi huruf dan spasi.',

            'jabatan_pic.required' => 'Jabatan PIC wajib diisi.',

            'no_hp_pic.required' => 'No. HP wajib diisi.',
            'no_hp_pic.regex' => 'No. HP hanya boleh berisi angka.',

            'email_pic.required' => 'Email PIC wajib diisi.',
            'email_pic.email' => 'Format email tidak valid.',
        ]);

        // CEK APAKAH KERJA SAMA SUDAH MEMILIKI TENANT
        $existingTenant = Tenant::where(
            'kerja_sama_id',
            $validated['kerja_sama_id']
        )->first();

        if ($existingTenant) {
            return redirect()
                ->route('tenant.show', $existingTenant)
                ->with(
                    'info',
                    'Data Tenant untuk Kerja Sama ini sudah tersedia.'
                );
        }

        // SIMPAN DATA TENANT
        Tenant::create($validated);

        return redirect()
            ->route('tenant.index')
            ->with(
                'success',
                'Data Tenant berhasil disimpan.'
            );
    }

    /**
     * Menampilkan detail Tenant.
     */
    public function show(Tenant $tenant)
    {
        return view(
            'tenant.show',
            compact('tenant')
        );
    }

    /**
     * Menampilkan halaman edit Tenant.
     */
    public function edit(Tenant $tenant)
    {
        return view(
            'tenant.edit',
            compact('tenant')
        );
    }

    /**
     * Memperbarui data Tenant.
     */
    public function update(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            // INFORMASI KOMERSIAL TENANT
            'gto' => [
                'required',
                'numeric',
                'min:0',
            ],

            'revenue_sharing' => [
                'required',
                'numeric',
                'between:0,100',
            ],

            // LEGALITAS TENANT
            'npwp' => [
                'required',
                'regex:/^[0-9]+$/',
                'max:30',
            ],

            'nib' => [
                'required',
                'regex:/^[0-9]+$/',
                'max:30',
            ],

            'bentuk_badan_usaha' => [
                'required',
                'string',
                'max:255',
            ],

            'alamat_perusahaan' => [
                'required',
                'string',
            ],

            // PIC TENANT
            'nama_pic' => [
                'required',
                'regex:/^[A-Za-zÀ-ÿ\s]+$/',
                'max:255',
            ],

            'jabatan_pic' => [
                'required',
                'string',
                'max:255',
            ],

            'no_hp_pic' => [
                'required',
                'regex:/^[0-9]+$/',
                'max:20',
            ],

            'email_pic' => [
                'required',
                'email',
                'max:255',
            ],
        ], [
            // PESAN VALIDASI INFORMASI KOMERSIAL
            'gto.required' => 'GTO wajib diisi.',
            'gto.numeric' => 'GTO harus berupa angka.',
            'gto.min' => 'GTO tidak boleh bernilai negatif.',

            'revenue_sharing.required' => 'Revenue Sharing wajib diisi.',
            'revenue_sharing.numeric' => 'Revenue Sharing harus berupa angka.',
            'revenue_sharing.between' => 'Revenue Sharing harus antara 0 sampai 100.',

            // PESAN VALIDASI LEGALITAS
            'npwp.required' => 'NPWP wajib diisi.',
            'npwp.regex' => 'NPWP hanya boleh berisi angka.',

            'nib.required' => 'NIB wajib diisi.',
            'nib.regex' => 'NIB hanya boleh berisi angka.',

            'bentuk_badan_usaha.required' => 'Bentuk badan usaha wajib dipilih.',

            'alamat_perusahaan.required' => 'Alamat perusahaan/usaha wajib diisi.',

            // PESAN VALIDASI PIC
            'nama_pic.required' => 'Nama PIC wajib diisi.',
            'nama_pic.regex' => 'Nama PIC hanya boleh berisi huruf dan spasi.',

            'jabatan_pic.required' => 'Jabatan PIC wajib diisi.',

            'no_hp_pic.required' => 'No. HP wajib diisi.',
            'no_hp_pic.regex' => 'No. HP hanya boleh berisi angka.',

            'email_pic.required' => 'Email PIC wajib diisi.',
            'email_pic.email' => 'Format email tidak valid.',
        ]);

        // UPDATE DATA TENANT
        $tenant->update($validated);

        return redirect()
            ->route('tenant.index')
            ->with(
                'success',
                'Data Tenant berhasil diperbarui.'
            );
    }

    /**
     * Menghapus data Tenant.
     */
        /**
     * Menghapus data Tenant.
     */
        /**
     * Menghapus data Tenant.
     */
    public function destroy(Tenant $tenant)
    {
        DB::transaction(function () use ($tenant) {
            Lokasi::where('tenant_id', $tenant->id)->update([
                'tenant_id' => null,
                'status' => 'Kosong',
            ]);

            $tenant->delete();
        });

        return redirect()
            ->route('tenant.index')
            ->with('success', 'Data tenant berhasil dihapus.');
    }

    /**
     * Menampilkan daftar data berdasarkan grafik analisis Tenant.
     */
    public function drilldown(Request $request)
    {
        // VALIDASI PARAMETER
        $validated = $request->validate([
            'type' => [
                'required',
                'in:jenis_usaha,terminal,bulan',
            ],
            'value' => [
                'required',
                'string',
                'max:255',
            ],
        ]);

        $type = $validated['type'];
        $value = $validated['value'];

        $items = collect();
        $tenantByKerjaSama = collect();

        // DRILLDOWN 1: JENIS USAHA
        if ($type === 'jenis_usaha') {

            $items = Tenant::query()
                ->where('jenis_usaha', $value)
                ->orderBy('nama_tenant')
                ->get();

            $judul = 'Tenant - ' . $value;

            $deskripsi = 'Daftar tenant berdasarkan kategori jenis usaha ' . $value . '.';
        }

        // DRILLDOWN 2: TERMINAL
        elseif ($type === 'terminal') {

            $items = Tenant::query()
                ->join('lokasis', 'tenants.id', '=', 'lokasis.tenant_id')
                ->where('lokasis.status', 'Terisi')
                ->where('lokasis.terminal', $value)
                ->select('tenants.*')
                ->distinct()
                ->orderBy('tenants.nama_tenant')
                ->get();

            $judul = 'Tenant - Terminal ' . $value;

            $deskripsi = 'Daftar tenant unik yang memiliki lokasi berstatus Terisi pada Terminal ' . $value . '.';
        }

        // DRILLDOWN 3: TREN AKUISISI TENANT
        else {

            // VALIDASI FORMAT BULAN
            $request->validate([
                'value' => [
                    'required',
                    'date_format:Y-m',
                ],
            ]);

            $tanggalMulai = \Carbon\Carbon::createFromFormat(
                'Y-m-d',
                $value . '-01'
            )->startOfMonth();

            $tanggalSelesai = $tanggalMulai->copy()->endOfMonth();

            $items = KerjaSama::query()
                ->whereNotNull('tanggal_pengajuan')
                ->whereBetween('tanggal_pengajuan', [
                    $tanggalMulai->toDateString(),
                    $tanggalSelesai->toDateString(),
                ])
                ->orderBy('tanggal_pengajuan')
                ->get();

            $tenantByKerjaSama = Tenant::query()
                ->whereIn('kerja_sama_id', $items->pluck('id'))
                ->get()
                ->keyBy('kerja_sama_id');

            $judul = 'Pengajuan Kerja Sama - ' . $tanggalMulai->translatedFormat('F Y');

            $deskripsi = 'Daftar pengajuan Kerja Sama berdasarkan tanggal pengajuan pada bulan ' . $tanggalMulai->translatedFormat('F Y') . '.';
        }

                return view('tenant.drilldown', compact(
            'type',
            'value',
            'items',
            'tenantByKerjaSama',
            'judul',
            'deskripsi'
        ));
    }

    /**
     * Menampilkan halaman simulasi portofolio Tenant.
     *
     * Data simulasi hanya dibaca dari database.
     * Perubahan skenario dihitung di browser dan tidak disimpan.
     */
    public function whatif()
    {
        $dataTenantSimulasi = Tenant::query()
            ->select('jenis_usaha')
            ->selectRaw('COUNT(*) as total')
            ->whereNotNull('jenis_usaha')
            ->where('jenis_usaha', '<>', '')
            ->groupBy('jenis_usaha')
            ->orderBy('jenis_usaha')
            ->get()
            ->map(function ($item) {
                return [
                    'jenis_usaha' => $item->jenis_usaha,
                    'total' => (int) $item->total,
                ];
            })
            ->values();

        $totalTenant = Tenant::count();

        return view('tenant.whatif', compact(
            'dataTenantSimulasi',
            'totalTenant'
        ));
    }
        /**
     * Mengambil data laporan Tenant berdasarkan filter.
     *
     * Seluruh ringkasan, grafik, dan tabel menggunakan
     * kumpulan data yang sama.
     */
    /**
 * Mengambil data laporan Tenant berdasarkan filter.
 *
 * Seluruh ringkasan, grafik, dan tabel menggunakan
 * kumpulan data yang sama.
 */
private function dataLaporanTenant(Request $request)
{
    // VALIDASI FILTER
    $validated = $request->validate([
        'basis_periode' => [
            'nullable',
            'in:pengajuan,terbentuk',
        ],
        'tanggal_mulai' => [
            'nullable',
            'date',
        ],
        'tanggal_selesai' => [
            'nullable',
            'date',
            function ($attribute, $value, $fail) use ($request) {
                if (
                    $value &&
                    $request->filled('tanggal_mulai') &&
                    \Carbon\Carbon::parse($value)->lt(
                        \Carbon\Carbon::parse($request->input('tanggal_mulai'))
                    )
                ) {
                    $fail('Tanggal selesai tidak boleh lebih awal dari tanggal mulai.');
                }
            },
        ],
        'jenis_usaha' => [
            'nullable',
            'string',
            'max:255',
        ],
        'terminal' => [
            'nullable',
            'string',
            'max:255',
        ],
        'status_aktivasi' => [
            'nullable',
            'in:' . implode(',', KerjaSama::STATUS_AKTIVASI),
        ],
    ]);

    $basisPeriode = $validated['basis_periode'] ?? 'pengajuan';

    // QUERY DASAR TENANT BESERTA RELASINYA
    $query = Tenant::query()
        ->with([
            'kerjaSama.aktivasi',
            'kerjaSama.lokasi',
        ]);

    // FILTER TANGGAL
    if (
        !empty($validated['tanggal_mulai']) ||
        !empty($validated['tanggal_selesai'])
    ) {
        if ($basisPeriode === 'pengajuan') {
            // TANGGAL PENGAJUAN BERASAL DARI TABEL KERJA_SAMAS
            $query->join(
                'kerja_samas',
                'tenants.kerja_sama_id',
                '=',
                'kerja_samas.id'
            );

            $kolomTanggal = 'kerja_samas.tanggal_pengajuan';

            $query->select('tenants.*');
        } else {
            // TANGGAL TENANT TERBENTUK BERASAL DARI TABEL TENANTS
            $kolomTanggal = 'tenants.created_at';
        }

        if (!empty($validated['tanggal_mulai'])) {
            $query->whereDate(
                $kolomTanggal,
                '>=',
                $validated['tanggal_mulai']
            );
        }

        if (!empty($validated['tanggal_selesai'])) {
            $query->whereDate(
                $kolomTanggal,
                '<=',
                $validated['tanggal_selesai']
            );
        }
    }

    // FILTER JENIS USAHA
    if (!empty($validated['jenis_usaha'])) {
        $query->where(
            'tenants.jenis_usaha',
            $validated['jenis_usaha']
        );
    }

    // FILTER TERMINAL
    if (!empty($validated['terminal'])) {
        $query->whereHas('kerjaSama.lokasi', function ($q) use ($validated) {
            $q->where('terminal', $validated['terminal'])
                ->where('status', 'Terisi');
        });
    }

    // AMBIL DATA TENANT
    $tenants = $query
        ->orderBy('tenants.nama_tenant')
        ->get();

    // TAMBAHKAN STATUS TURUNAN
    $tenants = $tenants->map(function ($tenant) {
        $kerjaSama = $tenant->kerjaSama;

        $tenant->status_aktivasi_laporan = $kerjaSama
            ? $kerjaSama->statusAktivasi()
            : 'Menunggu BA';

        $tenant->status_tenant_laporan = $kerjaSama
            ? $kerjaSama->statusKontrakSistem()
            : 'Belum Aktif';

        return $tenant;
    });

    // FILTER STATUS AKTIVASI
    if (!empty($validated['status_aktivasi'])) {
        $tenants = $tenants
            ->filter(function ($tenant) use ($validated) {
                return $tenant->status_aktivasi_laporan
                    === $validated['status_aktivasi'];
            })
            ->values();
    }

    // RINGKASAN
    $totalTenant = $tenants->count();

    $totalTenantAktif = $tenants
        ->where('status_tenant_laporan', 'Aktif')
        ->count();

    // DISTRIBUSI JENIS USAHA
    $distribusiJenisUsaha = $tenants
        ->groupBy(function ($tenant) {
            return $tenant->jenis_usaha ?: 'Tidak Diketahui';
        })
        ->map(function ($items, $jenisUsaha) {
            return [
                'label' => $jenisUsaha,
                'total' => $items->count(),
            ];
        })
        ->values();

    // DISTRIBUSI TERMINAL
    $distribusiTerminal = $tenants
        ->filter(function ($tenant) {
            return $tenant->kerjaSama?->lokasi
                && $tenant->kerjaSama->lokasi->status === 'Terisi';
        })
        ->groupBy(function ($tenant) {
            return $tenant->kerjaSama->lokasi->terminal
                ?: 'Tidak Diketahui';
        })
        ->map(function ($items, $terminal) {
            return [
                'label' => $terminal,
                'total' => $items->count(),
            ];
        })
        ->values();

    // OPSI FILTER JENIS USAHA
    $opsiJenisUsaha = Tenant::query()
        ->whereNotNull('jenis_usaha')
        ->where('jenis_usaha', '<>', '')
        ->distinct()
        ->orderBy('jenis_usaha')
        ->pluck('jenis_usaha');

    // OPSI FILTER TERMINAL
    $opsiTerminal = Lokasi::query()
        ->where('status', 'Terisi')
        ->whereNotNull('terminal')
        ->distinct()
        ->orderBy('terminal')
        ->pluck('terminal');

    // KIRIM DATA KE VIEW DAN PDF
    return [
        'tenants' => $tenants,
        'totalTenant' => $totalTenant,
        'totalTenantAktif' => $totalTenantAktif,
        'distribusiJenisUsaha' => $distribusiJenisUsaha,
        'distribusiTerminal' => $distribusiTerminal,
        'opsiJenisUsaha' => $opsiJenisUsaha,
        'opsiTerminal' => $opsiTerminal,
        'statusAktivasi' => KerjaSama::STATUS_AKTIVASI,

        'filters' => [
            'basis_periode' => $basisPeriode,
            'tanggal_mulai' => $validated['tanggal_mulai'] ?? '',
            'tanggal_selesai' => $validated['tanggal_selesai'] ?? '',
            'jenis_usaha' => $validated['jenis_usaha'] ?? '',
            'terminal' => $validated['terminal'] ?? '',
            'status_aktivasi' => $validated['status_aktivasi'] ?? '',
        ],
    ];
}
    /**
     * Menampilkan halaman Laporan Tenant.
     */
    public function laporan(Request $request)
    {
        $data = $this->dataLaporanTenant($request);

        return view('tenant.laporan', $data);
    }

    /**
     * Menghasilkan PDF Laporan Tenant.
     */
        /**
     * Menghasilkan PDF Laporan Tenant.
     */
    public function laporanPdf(Request $request)
    {
        $data = $this->dataLaporanTenant($request);

        $pdf = Pdf::loadView('tenant.laporan-pdf', $data)
            ->setPaper('a4', 'landscape');

        return $pdf->download('Laporan-Tenant.pdf');
    }

    /**
     * Mengekspor Laporan Tenant ke Excel.
     */
    public function laporanExcel(Request $request)
    {
        $data = $this->dataLaporanTenant($request);

        return Excel::download(
            new TenantReportExport($data['tenants']),
            'Laporan-Tenant.xlsx'
        );
    }
}