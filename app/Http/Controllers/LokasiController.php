<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\KerjaSama;
use App\Models\Lokasi;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LokasiController extends Controller
{
    /** Daftar jenis ruangan baku. */
    public const JENIS_RUANGAN = [
        'Store', 'Counter', 'Customer Service',
        'Lounge', 'Antena', 'Gudang', 'Lainnya',
    ];

    /**
     * Menampilkan daftar lokasi dan statistik dashboard.
     */
    public function index(Request $request)
    {
        /*
         * STATISTIK DASHBOARD
         * Dihitung dari seluruh data lokasi, tidak terpengaruh filter tabel.
         */
        $totalLokasi = Lokasi::count();

        $totalKosong = Lokasi::where('status', 'Kosong')
            ->whereNull('tenant_id')
            ->count();

        $totalTerisi = Lokasi::where('status', 'Terisi')->count();

        // Catatan: operator pembanding harus diikuti nilainya.
        // where('kolom', '<>') tanpa nilai dibaca sebagai kolom = '<>'
        // sehingga tidak pernah ada data yang cocok.
        $totalJenisRuangan = Lokasi::whereNotNull('jenis_ruangan')
            ->where('jenis_ruangan', '<>', '')
            ->distinct()
            ->count('jenis_ruangan');

        $persentaseKosong = $totalLokasi > 0
            ? round(($totalKosong / $totalLokasi) * 100, 1)
            : 0;

        $persentaseTerisi = $totalLokasi > 0
            ? round(($totalTerisi / $totalLokasi) * 100, 1)
            : 0;

        $totalLuas = (float) Lokasi::sum('luas_area');

        $luasTerisi = (float) Lokasi::where('status', 'Terisi')
            ->sum('luas_area');

        /*
         * GRAFIK PER TERMINAL (total, terisi, kosong)
         */
        $lokasiPerTerminal = Lokasi::select(
            'terminal',
            DB::raw('COUNT(*) as total'),
            DB::raw("SUM(CASE WHEN status = 'Terisi' THEN 1 ELSE 0 END) as terisi")
        )
            ->whereNotNull('terminal')
            ->where('terminal', '<>', '')
            ->groupBy('terminal')
            ->orderBy('terminal')
            ->get();

        /*
         * GRAFIK PER JENIS RUANGAN (total, terisi, kosong)
         */
        $lokasiPerJenis = Lokasi::select(
            'jenis_ruangan',
            DB::raw('COUNT(*) as total'),
            DB::raw("SUM(CASE WHEN status = 'Terisi' THEN 1 ELSE 0 END) as terisi")
        )
            ->whereNotNull('jenis_ruangan')
            ->where('jenis_ruangan', '<>', '')
            ->groupBy('jenis_ruangan')
            ->orderByDesc('total')
            ->orderBy('jenis_ruangan')
            ->get();

        /*
         * HEATMAP OKUPANSI: terminal x lantai
         */
        $matriksLantai = Lokasi::select(
            'terminal',
            'lantai',
            DB::raw('COUNT(*) as total'),
            DB::raw("SUM(CASE WHEN status = 'Terisi' THEN 1 ELSE 0 END) as terisi")
        )
            ->whereNotNull('terminal')->where('terminal', '<>', '')
            ->whereNotNull('lantai')->where('lantai', '<>', '')
            ->groupBy('terminal', 'lantai')
            ->get()
            ->keyBy(fn ($r) => $r->terminal . '|' . $r->lantai);

        $daftarTerminal = $matriksLantai->pluck('terminal')->unique()->sort()->values();

        $daftarLantai = $matriksLantai->pluck('lantai')->unique()
            ->sortBy(fn ($l) => is_numeric($l) ? (int) $l : 999)
            ->values();

        /*
         * DENAH OKUPANSI: setiap ruangan sebagai satu kotak
         * terminal -> lantai -> daftar ruangan
         */
        $denah = Lokasi::select(
            'id', 'terminal', 'lantai', 'kode_ruang',
            'jenis_ruangan', 'status', 'luas_area', 'lokasi'
        )
            ->whereNotNull('terminal')->where('terminal', '<>', '')
            ->orderBy('terminal')
            ->orderBy('kode_ruang')
            ->get()
            ->groupBy('terminal')
            ->map(fn ($perTerminal) => $perTerminal
                ->groupBy(fn ($l) => $l->lantai ?: '-')
                ->sortKeys(SORT_NATURAL));

        /*
         * KOMPOSISI JENIS USAHA tenant pada lokasi yang terisi
         */
        $perJenisUsaha = Lokasi::query()
            ->join('tenants', 'tenants.id', '=', 'lokasis.tenant_id')
            ->leftJoin('kerja_samas', 'kerja_samas.id', '=', 'tenants.kerja_sama_id')
            ->where('lokasis.status', 'Terisi')
            ->select(
                DB::raw("COALESCE(NULLIF(tenants.jenis_usaha, ''), NULLIF(kerja_samas.jenis_usaha, ''), 'Belum diisi') as jenis_usaha"),
                DB::raw('COUNT(*) as total'),
                DB::raw('COALESCE(SUM(lokasis.luas_area), 0) as luas')
            )
            ->groupBy('jenis_usaha')
            ->orderByDesc('total')
            ->get();

        /*
         * PILIHAN FILTER
         * Diambil dari data yang benar-benar ada, digabung dengan daftar baku.
         */
        $pilihanTerminal = Lokasi::whereNotNull('terminal')
            ->where('terminal', '<>', '')
            ->distinct()
            ->orderBy('terminal')
            ->pluck('terminal');

        $pilihanLantai = Lokasi::whereNotNull('lantai')
            ->where('lantai', '<>', '')
            ->distinct()
            ->pluck('lantai')
            ->sortBy(fn ($l) => is_numeric($l) ? (int) $l : 999)
            ->values();

        $pilihanJenis = collect(self::JENIS_RUANGAN)
            ->merge(
                Lokasi::whereNotNull('jenis_ruangan')
                    ->where('jenis_ruangan', '<>', '')
                    ->distinct()
                    ->pluck('jenis_ruangan')
            )
            ->unique()
            ->values();

        /*
         * DATA TABEL
         */
        $query = Lokasi::with('tenant');

        // Pencarian lokasi (termasuk nama tenant).
        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('terminal', 'like', "%{$search}%")
                    ->orWhere('nomor_ro', 'like', "%{$search}%")
                    ->orWhere('kode_ruang', 'like', "%{$search}%")
                    ->orWhere('lokasi', 'like', "%{$search}%")
                    ->orWhere('jenis_ruangan', 'like', "%{$search}%")
                    ->orWhereHas('tenant', function ($t) use ($search) {
                        $t->where('nama_tenant', 'like', "%{$search}%")
                            ->orWhere('mitra_usaha', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('terminal')) {
            $query->where('terminal', $request->terminal);
        }

        if ($request->filled('lantai')) {
            $query->where('lantai', $request->lantai);
        }

        if ($request->filled('jenis_ruangan')) {
            $query->where('jenis_ruangan', $request->jenis_ruangan);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Pengurutan data.
        switch ($request->get('sort', 'newest')) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;

            case 'az':
                $query->orderBy('kode_ruang', 'asc');
                break;

            case 'za':
                $query->orderBy('kode_ruang', 'desc');
                break;

            case 'luas_desc':
                $query->orderByDesc('luas_area');
                break;

            case 'luas_asc':
                $query->orderBy('luas_area', 'asc');
                break;

            default:
                $query->orderBy('created_at', 'desc')->orderByDesc('id');
                break;
        }

        $perPage = (int) $request->get('per_page', 15);
        $perPage = in_array($perPage, [10, 15, 25, 50, 100], true)
            ? $perPage
            : 15;

        $lokasis = $query->paginate($perPage)->withQueryString();

        return view('lokasi.index', compact(
            'lokasis',
            'perPage',
            'totalLokasi',
            'totalKosong',
            'totalTerisi',
            'totalJenisRuangan',
            'persentaseKosong',
            'persentaseTerisi',
            'totalLuas',
            'luasTerisi',
            'lokasiPerTerminal',
            'lokasiPerJenis',
            'matriksLantai',
            'daftarTerminal',
            'daftarLantai',
            'denah',
            'perJenisUsaha',
            'pilihanTerminal',
            'pilihanLantai',
            'pilihanJenis'
        ));
    }

    /**
     * Drilldown okupansi (JSON): Terminal > Lantai > Jenis Ruangan > Ruangan.
     *
     * Level yang dikembalikan ditentukan oleh filter yang sudah dipilih,
     * sehingga setiap klik "turun" satu tingkat dan datanya langsung
     * dihitung dari tabel lokasis.
     */
    public function drilldown(Request $request)
    {
        // Nilai kosong/NULL di database ditampilkan dan difilter sebagai "-".
        $kosong = '-';
        $ekspresi = fn (string $kolom) => "COALESCE(NULLIF({$kolom}, ''), '{$kosong}')";

        $filter = [
            'terminal'      => trim((string) $request->query('terminal', '')),
            'lantai'        => trim((string) $request->query('lantai', '')),
            'jenis_ruangan' => trim((string) $request->query('jenis_ruangan', '')),
        ];

        $dasar = Lokasi::query();

        foreach ($filter as $kolom => $nilai) {
            if ($nilai === '') {
                continue;
            }

            $dasar->whereRaw($ekspresi($kolom) . ' = ?', [$nilai]);
        }

        // Tentukan level berikutnya dari filter yang sudah terisi.
        $level = match (true) {
            $filter['terminal'] === ''      => ['kunci' => 'terminal', 'judul' => 'Terminal'],
            $filter['lantai'] === ''        => ['kunci' => 'lantai', 'judul' => 'Lantai'],
            $filter['jenis_ruangan'] === '' => ['kunci' => 'jenis_ruangan', 'judul' => 'Jenis Ruangan'],
            default                         => ['kunci' => 'ruangan', 'judul' => 'Ruangan'],
        };

        // Level paling bawah: daftar ruangan.
        if ($level['kunci'] === 'ruangan') {
            $rooms = (clone $dasar)
                ->with('tenant:id,nama_tenant')
                ->orderBy('kode_ruang')
                ->get()
                ->map(fn (Lokasi $l) => [
                    'id'     => $l->id,
                    'kode'   => $l->kode_ruang,
                    'lokasi' => $l->lokasi,
                    'status' => $l->status,
                    'luas'   => $l->luas_area !== null ? (float) $l->luas_area : null,
                    'tenant' => $l->tenant?->nama_tenant,
                    'url'    => route('lokasi.show', $l->id),
                ])
                ->values();

            $terisi = $rooms->where('status', 'Terisi')->count();

            return response()->json([
                'kunci'    => $level['kunci'],
                'judul'    => $level['judul'],
                'items'    => [],
                'rooms'    => $rooms,
                'ringkasan' => $this->ringkasanDrilldown($rooms->count(), $terisi),
            ]);
        }

        $kolomGrup = $ekspresi($level['kunci']);

        $items = (clone $dasar)
            ->select(
                DB::raw("{$kolomGrup} as nilai"),
                DB::raw('COUNT(*) as total'),
                DB::raw("SUM(CASE WHEN status = 'Terisi' THEN 1 ELSE 0 END) as terisi"),
                DB::raw('COALESCE(SUM(luas_area), 0) as luas'),
                DB::raw("COALESCE(SUM(CASE WHEN status = 'Terisi' THEN luas_area ELSE 0 END), 0) as luas_terisi")
            )
            ->groupBy(DB::raw($kolomGrup))
            ->get()
            ->sortBy(fn ($r) => (string) $r->nilai, SORT_NATURAL)
            ->values()
            ->map(fn ($r) => [
                'nilai'       => (string) $r->nilai,
                'nama'        => (string) $r->nilai === $kosong ? 'Belum diisi' : (string) $r->nilai,
                'total'       => (int) $r->total,
                'terisi'      => (int) $r->terisi,
                'kosong'      => (int) $r->total - (int) $r->terisi,
                'okupansi'    => $r->total > 0 ? round($r->terisi / $r->total * 100, 1) : 0,
                'luas'        => round((float) $r->luas, 2),
                'luas_terisi' => round((float) $r->luas_terisi, 2),
            ]);

        return response()->json([
            'kunci'     => $level['kunci'],
            'judul'     => $level['judul'],
            'items'     => $items,
            'rooms'     => [],
            'ringkasan' => $this->ringkasanDrilldown($items->sum('total'), $items->sum('terisi')),
        ]);
    }

    private function ringkasanDrilldown(int $total, int $terisi): array
    {
        return [
            'total'    => $total,
            'terisi'   => $terisi,
            'kosong'   => $total - $terisi,
            'okupansi' => $total > 0 ? round($terisi / $total * 100, 1) : 0,
        ];
    }

    /**
     * Menampilkan form tambah lokasi.
     */
    public function create()
    {
        return view('lokasi.create');
    }

    /**
     * Menyimpan lokasi baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'terminal' => [
                'required',
                'in:T1,T2',
            ],
            'lantai' => [
                'required',
                'string',
                'max:20',
            ],
            'jenis_ruangan' => [
                'required',
                'string',
                'max:100',
            ],
            'nomor_ro' => [
                'nullable',
                'string',
                'max:255',
            ],
            'lokasi' => [
                'required',
                'string',
                'max:255',
            ],
            'luas_area' => [
                'nullable',
                'numeric',
                'min:0',
            ],
            'catatan' => [
                'nullable',
                'string',
            ],
        ]);

        // Buat nomor ruang otomatis berdasarkan terminal dan lantai.
        $lokasi = DB::transaction(function () use ($validated) {
            $prefix = $validated['terminal']
                . '-' . str_pad(
                    (string) $validated['lantai'],
                    2,
                    '0',
                    STR_PAD_LEFT
                )
                . '-';

            // Kunci tabel untuk mengurangi risiko nomor ganda
            // ketika dua lokasi dibuat bersamaan.
            $lokasiTerakhir = Lokasi::where(
                'kode_ruang',
                'like',
                $prefix . '%'
            )
                ->orderByDesc('kode_ruang')
                ->lockForUpdate()
                ->first();

            $nomorTerakhir = 0;

            if ($lokasiTerakhir) {
                $bagianNomor = substr(
                    $lokasiTerakhir->kode_ruang,
                    strlen($prefix)
                );

                if (ctype_digit($bagianNomor)) {
                    $nomorTerakhir = (int) $bagianNomor;
                }
            }

            $kodeRuang = $prefix
                . str_pad($nomorTerakhir + 1, 3, '0', STR_PAD_LEFT);

            // Pastikan kode belum pernah digunakan.
            while (Lokasi::where('kode_ruang', $kodeRuang)->exists()) {
                $nomorTerakhir++;

                $kodeRuang = $prefix
                    . str_pad($nomorTerakhir + 1, 3, '0', STR_PAD_LEFT);
            }

            return Lokasi::create([
                'terminal' => $validated['terminal'],
                'lantai' => $validated['lantai'],
                'jenis_ruangan' => $validated['jenis_ruangan'],
                'nomor_ro' => $validated['nomor_ro'] ?? null,
                'kode_ruang' => $kodeRuang,
                'lokasi' => $validated['lokasi'],
                'luas_area' => $validated['luas_area'] ?? null,
                'catatan' => $validated['catatan'] ?? null,
                'status' => 'Kosong',
                'tenant_id' => null,
            ]);
        });

        AuditLog::catat(
            'Lokasi',
            'Lokasi ditambahkan',
            "Lokasi {$lokasi->kode_ruang} ({$lokasi->lokasi}) ditambahkan.",
            $lokasi
        );

        return redirect()
            ->route('lokasi.index')
            ->with(
                'success',
                'Lokasi ' . $lokasi->kode_ruang . ' berhasil ditambahkan.'
            );
    }

    /**
     * Menampilkan detail lokasi.
     */
    public function show(Lokasi $lokasi)
    {
        $lokasi->load('tenant.kerjaSama');

        // Ruangan lain di terminal & lantai yang sama (untuk mini denah).
        $ruanganSelantai = Lokasi::select(
            'id', 'kode_ruang', 'status', 'jenis_ruangan', 'luas_area', 'lokasi'
        )
            ->where('terminal', $lokasi->terminal)
            ->where('lantai', $lokasi->lantai)
            ->orderBy('kode_ruang')
            ->get();

        return view('lokasi.show', compact('lokasi', 'ruanganSelantai'));
    }

    /**
     * Menampilkan form edit lokasi.
     */
    public function edit(Lokasi $lokasi)
    {
        return view('lokasi.edit', compact('lokasi'));
    }

    /**
     * Memperbarui data lokasi.
     */
    public function update(Request $request, Lokasi $lokasi)
    {
        $validated = $request->validate([
            'terminal' => ['required', 'in:T1,T2'],
            'lantai' => ['required', 'string', 'max:20'],
            'jenis_ruangan' => ['required', 'string', 'max:100'],
            'nomor_ro' => ['nullable', 'string', 'max:255'],
            'lokasi' => ['required', 'string', 'max:255'],
            'luas_area' => ['nullable', 'numeric', 'min:0'],
            'catatan' => ['nullable', 'string'],
        ]);

        // kode_ruang, status, dan tenant_id tidak diubah melalui form edit.
        $lokasi->fill($validated);
        $berubah = $lokasi->getDirty();
        $asli = $lokasi->getOriginal();
        $lokasi->save();

        if ($berubah) {
            AuditLog::catat(
                'Lokasi',
                'Lokasi diubah',
                "Lokasi {$lokasi->kode_ruang} diubah. "
                    . AuditLog::ringkasPerubahan($asli, $berubah),
                $lokasi
            );
        }

        return redirect()
            ->route('lokasi.index')
            ->with('success', 'Data lokasi berhasil diperbarui.');
    }

    /**
     * Menghapus lokasi.
     */
    public function destroy(Lokasi $lokasi)
    {
        if (
            $lokasi->tenant_id !== null ||
            $lokasi->status !== 'Kosong'
        ) {
            return redirect()
                ->route('lokasi.index')
                ->with(
                    'error',
                    'Lokasi yang sedang terisi tidak dapat dihapus.'
                );
        }

        $kodeRuang = $lokasi->kode_ruang;

        $lokasi->delete();

        AuditLog::catat(
            'Lokasi',
            'Lokasi dihapus',
            "Lokasi {$kodeRuang} ({$lokasi->lokasi}) dihapus.",
            $lokasi,
            'peringatan'
        );

        return redirect()
            ->route('lokasi.index')
            ->with(
                'success',
                "Lokasi {$kodeRuang} berhasil dihapus."
            );
    }

    /**
     * Menampilkan lokasi kosong untuk kerja sama.
     */
    public function pilihUntukKerjaSama(KerjaSama $kerjaSama)
    {
        $tenant = Tenant::where(
            'kerja_sama_id',
            $kerjaSama->id
        )->first();

        if (!$tenant) {
            return redirect()
                ->route('kerja-sama')
                ->with('error', 'Isi data tenant terlebih dahulu.');
        }

        $lokasiTerpasang = Lokasi::where(
            'tenant_id',
            $tenant->id
        )->exists();

        if ($lokasiTerpasang) {
            return redirect()
                ->route('kerja-sama')
                ->with('info', 'Tenant ini sudah memiliki lokasi.');
        }

        $lokasis = Lokasi::with('tenant.kerjaSama')
            ->where('status', 'Kosong')
            ->whereNull('tenant_id')
            ->orderBy('terminal')
            ->orderBy('lantai')
            ->orderBy('kode_ruang')
            ->get();

        return view('lokasi.pilih', compact(
            'kerjaSama',
            'tenant',
            'lokasis'
        ));
    }

    /**
     * Menghubungkan lokasi kosong dengan tenant.
     */
    public function simpanPilihan(
        Request $request,
        KerjaSama $kerjaSama
    ) {
        $request->validate([
            'lokasi_id' => [
                'required',
                'integer',
                'exists:lokasis,id',
            ],
        ]);

        $tenant = Tenant::where(
            'kerja_sama_id',
            $kerjaSama->id
        )->firstOrFail();

        if (Lokasi::where('tenant_id', $tenant->id)->exists()) {
            return redirect()
                ->route('kerja-sama')
                ->with('info', 'Tenant ini sudah memiliki lokasi.');
        }

        $lokasiTerpilih = null;

        DB::transaction(function () use ($request, $tenant, &$lokasiTerpilih) {
            $lokasi = Lokasi::whereKey($request->lokasi_id)
                ->lockForUpdate()
                ->firstOrFail();

            if (
                $lokasi->status !== 'Kosong' ||
                $lokasi->tenant_id !== null
            ) {
                throw ValidationException::withMessages([
                    'lokasi_id' =>
                        'Lokasi ini sudah terisi. Silakan pilih lokasi lain.',
                ]);
            }

            $lokasi->update([
                'tenant_id' => $tenant->id,
                'status' => 'Terisi',
            ]);

            $lokasiTerpilih = $lokasi;
        });

        if ($lokasiTerpilih) {
            AuditLog::catat(
                'Lokasi',
                'Lokasi dihubungkan ke tenant',
                "Lokasi {$lokasiTerpilih->kode_ruang} dihubungkan ke tenant {$tenant->nama_tenant}.",
                $lokasiTerpilih
            );
        }

        return redirect()
            ->route('kerja-sama')
            ->with(
                'success',
                'Lokasi berhasil dihubungkan dengan tenant.'
            );
    }
}