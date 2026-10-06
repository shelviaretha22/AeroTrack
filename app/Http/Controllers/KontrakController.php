<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class KontrakController extends Controller
{
    /**
     * Menampilkan daftar kontrak.
     */
    public function index(Request $request)
    {
        return view('kontrak.index');
    }

    /**
     * Menampilkan halaman Beri ACC.
     */
    public function acc($id)
    {
        return view('kontrak.acc', compact('id'));
    }

    /**
     * Menyimpan persetujuan/ACC kontrak.
     *
     * Sementara belum menyimpan ke database karena
     * struktur Kerja Sama dan Kontrak belum tersedia.
     */
    public function storeAcc(Request $request, $id)
    {
        return redirect()
            ->route('kontrak.show', $id)
            ->with('success', 'Kontrak berhasil di-ACC.');
    }

    /**
     * Menampilkan detail kontrak.
     */
    public function show($id)
    {
        return view('kontrak.show', compact('id'));
    }
}