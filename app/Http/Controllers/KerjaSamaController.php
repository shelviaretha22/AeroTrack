<?php

namespace App\Http\Controllers;

use App\Models\KerjaSama;
use Illuminate\Http\Request;

class KerjaSamaController extends Controller
{
    // Menampilkan daftar data kerja sama
    public function index()
    {
        $kerjaSamas = KerjaSama::latest()->get();

        return view('kerja-sama.index', compact('kerjaSamas'));
    }

    // Menampilkan form input kerja sama
    public function create()
    {
        return view('kerja-sama.create');
    }

    // Menyimpan data kerja sama baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'mitra_usaha' => ['required', 'string', 'max:255'],
            'brand' => ['nullable', 'string', 'max:255'],
            'jenis_usaha' => ['required', 'string', 'max:255'],
            'bentuk_kerja_sama' => ['required', 'string', 'max:255'],

            'tanggal_pengajuan' => ['required', 'date'],
            'status' => ['required', 'in:Draft,Proses,Disetujui,Ditolak'],

            'tanggal_mulai' => ['nullable', 'date'],
            'tanggal_berakhir' => [
                'nullable',
                'date',
                'after_or_equal:tanggal_mulai'
            ],

            'pic_commercial' => ['required', 'string', 'max:255'],
            'catatan' => ['nullable', 'string'],
        ]);

        KerjaSama::create($validated);

        return redirect()
            ->route('kerja-sama')
            ->with('success', 'Data kerja sama berhasil disimpan.');
    }

    // Menampilkan detail kerja sama
    public function show($id)
    {
        $kerjaSama = KerjaSama::findOrFail($id);

        return view('kerja-sama.show', compact('kerjaSama'));
    }

    // Menampilkan form edit
    public function edit($id)
    {
        $kerjaSama = KerjaSama::findOrFail($id);

        return view('kerja-sama.edit', compact('kerjaSama'));
    }

    // Menyimpan perubahan data
    public function update(Request $request, $id)
    {
        $kerjaSama = KerjaSama::findOrFail($id);

        $validated = $request->validate([
            'mitra_usaha' => ['required', 'string', 'max:255'],
            'brand' => ['nullable', 'string', 'max:255'],
            'jenis_usaha' => ['required', 'string', 'max:255'],
            'bentuk_kerja_sama' => ['required', 'string', 'max:255'],

            'tanggal_pengajuan' => ['required', 'date'],
            'status' => ['required', 'in:Draft,Proses,Disetujui,Ditolak'],

            'tanggal_mulai' => ['nullable', 'date'],
            'tanggal_berakhir' => [
                'nullable',
                'date',
                'after_or_equal:tanggal_mulai'
            ],

            'pic_commercial' => ['required', 'string', 'max:255'],
            'catatan' => ['nullable', 'string'],
        ]);

        $kerjaSama->update($validated);

        return redirect()
            ->route('kerja-sama')
            ->with('success', 'Data kerja sama berhasil diperbarui.');
    }

    // Menghapus data kerja sama
    public function destroy($id)
    {
        $kerjaSama = KerjaSama::findOrFail($id);

        $kerjaSama->delete();

        return redirect()
            ->route('kerja-sama')
            ->with('success', 'Data kerja sama berhasil dihapus.');
    }
}