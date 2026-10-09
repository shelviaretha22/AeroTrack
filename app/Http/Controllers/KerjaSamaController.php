<?php

namespace App\Http\Controllers;

use App\Models\KerjaSama;
use App\Models\Tenant;
use App\Models\Lokasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KerjaSamaController extends Controller
{
    /**
     * Menentukan status berdasarkan kelengkapan data.
     *
     * Penanda pengajuan BA akan disambungkan setelah
     * fitur Berita Acara diperiksa.
     */
    private function tentukanStatus(KerjaSama $kerjaSama): string
    {
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
            'catatan' => ['nullable', 'string'],
        ]);

        // Status tidak diambil dari input pengguna.
        $validated['status'] = 'Draft';

        $kerjaSama = KerjaSama::create($validated);

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
            'catatan' => ['nullable', 'string'],
        ]);

        // Status tetap dikelola sistem.
        unset($validated['status']);

        $kerjaSama->update($validated);

        return redirect()
            ->route('kerja-sama')
            ->with('success', 'Data kerja sama berhasil diperbarui.');
    }

    /**
     * Menghapus Kerja Sama.
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
            Lokasi::where('tenant_id', $tenant->id)->update([
                'tenant_id' => null,
                'status' => 'Kosong',
            ]);

            $tenant->delete();
        }

        $kerjaSama->delete();
    });

    return redirect()
        ->route('kerja-sama')
        ->with('success', 'Data kerja sama berhasil dihapus.');
}
}
