<?php

namespace App\Http\Controllers;

use App\Models\KerjaSama;
use App\Models\Lokasi;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LokasiController extends Controller
{
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

        $totalJenisRuangan = Lokasi::whereNotNull('jenis_ruangan')
            ->where('jenis_ruangan', '<>')
            ->distinct()
            ->count('jenis_ruangan');

        $persentaseKosong = $totalLokasi > 0
            ? round(($totalKosong / $totalLokasi) * 100, 1)
            : 0;

        $persentaseTerisi = $totalLokasi > 0
            ? round(($totalTerisi / $totalLokasi) * 100, 1)
            : 0;

        /*
         * GRAFIK JUMLAH LOKASI PER TERMINAL
         */
        $lokasiPerTerminal = Lokasi::select(
            'terminal',
            DB::raw('COUNT(*) as total')
        )
            ->whereNotNull('terminal')
            ->groupBy('terminal')
            ->orderBy('terminal')
            ->get();

        /*
         * GRAFIK JUMLAH LOKASI PER JENIS RUANGAN
         */
        $lokasiPerJenis = Lokasi::select(
            'jenis_ruangan',
            DB::raw('COUNT(*) as total')
        )
            ->whereNotNull('jenis_ruangan')
            ->where('jenis_ruangan', '<>')
            ->groupBy('jenis_ruangan')
            ->orderByDesc('total')
            ->orderBy('jenis_ruangan')
            ->get();

        /*
         * DATA TABEL
         * Pencarian, filter, dan pengurutan tetap bekerja seperti sebelumnya.
         */
        $query = Lokasi::with('tenant');

        // Pencarian lokasi.
        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('terminal', 'like', "%{$search}%")
                    ->orWhere('nomor_ro', 'like', "%{$search}%")
                    ->orWhere('kode_ruang', 'like', "%{$search}%")
                    ->orWhere('lokasi', 'like', "%{$search}%")
                    ->orWhere('jenis_ruangan', 'like', "%{$search}%");
            });
        }

        // Filter terminal.
        if ($request->filled('terminal')) {
            $query->where('terminal', $request->terminal);
        }

        // Filter lantai.
        if ($request->filled('lantai')) {
            $query->where('lantai', $request->lantai);
        }

        // Filter jenis ruangan.
        if ($request->filled('jenis_ruangan')) {
            $query->where('jenis_ruangan', $request->jenis_ruangan);
        }

        // Filter status.
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

            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $lokasis = $query->get();

        return view('lokasi.index', compact(
            'lokasis',
            'totalLokasi',
            'totalKosong',
            'totalTerisi',
            'totalJenisRuangan',
            'persentaseKosong',
            'persentaseTerisi',
            'lokasiPerTerminal',
            'lokasiPerJenis'
        ));
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
        $lokasi->load('tenant');

        return view('lokasi.show', compact('lokasi'));
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
        $lokasi->update($validated);

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

        DB::transaction(function () use ($request, $tenant) {
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
        });

        return redirect()
            ->route('kerja-sama')
            ->with(
                'success',
                'Lokasi berhasil dihubungkan dengan tenant.'
            );
    }
}