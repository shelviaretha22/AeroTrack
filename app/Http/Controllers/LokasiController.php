<?php

namespace App\Http\Controllers;

use App\Models\Lokasi;
use Illuminate\Http\Request;

class LokasiController extends Controller
{
    /**
     * Menampilkan daftar lokasi.
     */
    public function index(Request $request)
    {
        $query = Lokasi::with('tenant');

        // Search
        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('terminal', 'like', '%' . $search . '%')
                    ->orWhere('nomor_ro', 'like', '%' . $search . '%')
                    ->orWhere('kode_ruang', 'like', '%' . $search . '%')
                    ->orWhere('lokasi', 'like', '%' . $search . '%');
            });
        }

        // Filter terminal
        if ($request->filled('terminal')) {
            $query->where('terminal', $request->terminal);
        }

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Sorting
        $sort = $request->get('sort', 'newest');

        switch ($sort) {
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

        return view('lokasi.index', compact('lokasis'));
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
                'string',
                'max:255',
            ],

            'nomor_ro' => [
                'nullable',
                'string',
                'max:255',
            ],

            'kode_ruang' => [
                'required',
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

        /*
        |--------------------------------------------------------------------------
        | Default lokasi baru
        |--------------------------------------------------------------------------
        |
        | Saat pertama kali dibuat, lokasi belum ditempati tenant.
        |
        */

        $validated['status'] = 'Kosong';
        $validated['tenant_id'] = null;

        Lokasi::create($validated);

        return redirect()
            ->route('lokasi.index')
            ->with('success', 'Data lokasi berhasil ditambahkan.');
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
            'terminal' => [
                'required',
                'string',
                'max:255',
            ],

            'nomor_ro' => [
                'nullable',
                'string',
                'max:255',
            ],

            'kode_ruang' => [
                'required',
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
        $lokasi->delete();

        return redirect()
            ->route('lokasi.index')
            ->with('success', 'Data lokasi berhasil dihapus.');
    }
}