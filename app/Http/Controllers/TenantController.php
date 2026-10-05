<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use Illuminate\Http\Request;

class TenantController extends Controller
{
    /**
     * Menampilkan daftar tenant.
     */
    public function index(Request $request)
    {
        // =====================================================
        // QUERY DASAR
        // =====================================================
        $query = Tenant::query();


        // =====================================================
        // SEARCH
        // =====================================================
        if ($request->filled('search')) {

            $search = trim($request->search);

            $query->where(function ($q) use ($search) {

                $q->where('nama_tenant', 'like', '%' . $search . '%')
                    ->orWhere('mitra_usaha', 'like', '%' . $search . '%')
                    ->orWhere('jenis_usaha', 'like', '%' . $search . '%')
                    ->orWhere('bentuk_kerja_sama', 'like', '%' . $search . '%')
                    ->orWhere('nama_pic', 'like', '%' . $search . '%')
                    ->orWhere('jabatan_pic', 'like', '%' . $search . '%')
                    ->orWhere('no_hp_pic', 'like', '%' . $search . '%')
                    ->orWhere('email_pic', 'like', '%' . $search . '%')
                    ->orWhere('npwp', 'like', '%' . $search . '%');

            });
        }


        // =====================================================
        // SORTING
        // =====================================================
        $sort = $request->get('sort', 'newest');


        switch ($sort) {

            // -------------------------------------------------
            // TERBARU → TERLAMA
            // -------------------------------------------------
            case 'newest':

                $query->orderBy('created_at', 'desc');

                break;


            // -------------------------------------------------
            // TERLAMA → TERBARU
            // -------------------------------------------------
            case 'oldest':

                $query->orderBy('created_at', 'asc');

                break;


            // -------------------------------------------------
            // NAMA TENANT A → Z
            // -------------------------------------------------
            case 'az':

                $query->orderBy('nama_tenant', 'asc');

                break;


            // -------------------------------------------------
            // NAMA TENANT Z → A
            // -------------------------------------------------
            case 'za':

                $query->orderBy('nama_tenant', 'desc');

                break;


            // -------------------------------------------------
            // BELUM DITAMBAHKAN DETAIL
            // -------------------------------------------------
            case 'incomplete':

                $query->where(function ($q) {

                    $q->whereNull('npwp')
                        ->orWhere('npwp', '')
                        ->orWhereNull('nama_pic')
                        ->orWhere('nama_pic', '')
                        ->orWhereNull('jabatan_pic')
                        ->orWhere('jabatan_pic', '')
                        ->orWhereNull('no_hp_pic')
                        ->orWhere('no_hp_pic', '')
                        ->orWhereNull('email_pic')
                        ->orWhere('email_pic', '');

                });

                $query->orderBy('created_at', 'desc');

                break;


            // -------------------------------------------------
            // SUDAH DITAMBAHKAN DETAIL
            // -------------------------------------------------
            case 'complete':

                $query->whereNotNull('npwp')
                    ->where('npwp', '!=', '')
                    ->whereNotNull('nama_pic')
                    ->where('nama_pic', '!=', '')
                    ->whereNotNull('jabatan_pic')
                    ->where('jabatan_pic', '!=', '')
                    ->whereNotNull('no_hp_pic')
                    ->where('no_hp_pic', '!=', '')
                    ->whereNotNull('email_pic')
                    ->where('email_pic', '!=', '');

                $query->orderBy('created_at', 'desc');

                break;


            // -------------------------------------------------
            // DEFAULT
            // -------------------------------------------------
            default:

                $query->orderBy('created_at', 'desc');

                break;
        }


        // =====================================================
        // AMBIL DATA TENANT
        // =====================================================
        $tenants = $query->get();


        // =====================================================
        // TAMPILKAN HALAMAN
        // =====================================================
        return view('tenant.index', compact('tenants'));
    }


    /**
     * Menampilkan halaman form tambah tenant.
     *
     * Route ini tetap dipertahankan karena
     * kemungkinan masih digunakan oleh CRUD yang sudah ada.
     */
    public function create()
    {
        return view('tenant.create');
    }


    /**
     * Menyimpan data tenant baru.
     */
    public function store(Request $request)
    {
        // =====================================================
        // VALIDASI
        // =====================================================
        $validated = $request->validate([

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

            'npwp' => [
                'nullable',
                'string',
                'max:255',
            ],

            'nama_pic' => [
                'required',
                'string',
                'max:255',
            ],

            'jabatan_pic' => [
                'nullable',
                'string',
                'max:255',
            ],

            'no_hp_pic' => [
                'required',
                'string',
                'max:255',
            ],

            'email_pic' => [
                'nullable',
                'email',
                'max:255',
            ],

        ]);


        // =====================================================
        // SIMPAN DATA
        // =====================================================
        Tenant::create($validated);


        // =====================================================
        // REDIRECT
        // =====================================================
        return redirect()
            ->route('tenant.index')
            ->with('success', 'Data tenant berhasil ditambahkan.');
    }


    /**
     * Menampilkan detail tenant.
     */
    public function show(Tenant $tenant)
    {
        return view('tenant.show', compact('tenant'));
    }


    /**
     * Menampilkan halaman edit tenant.
     */
    public function edit(Tenant $tenant)
    {
        return view('tenant.edit', compact('tenant'));
    }


    /**
     * Memperbarui data tenant.
     */
    public function update(Request $request, Tenant $tenant)
    {
        // =====================================================
        // VALIDASI
        // =====================================================
        $validated = $request->validate([

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

            'npwp' => [
                'nullable',
                'string',
                'max:255',
            ],

            'nama_pic' => [
                'required',
                'string',
                'max:255',
            ],

            'jabatan_pic' => [
                'nullable',
                'string',
                'max:255',
            ],

            'no_hp_pic' => [
                'required',
                'string',
                'max:255',
            ],

            'email_pic' => [
                'nullable',
                'email',
                'max:255',
            ],

        ]);


        // =====================================================
        // UPDATE DATA
        // =====================================================
        $tenant->update($validated);


        // =====================================================
        // REDIRECT
        // =====================================================
        return redirect()
            ->route('tenant.index')
            ->with('success', 'Data tenant berhasil diperbarui.');
    }


    /**
     * Menghapus data tenant.
     */
    public function destroy(Tenant $tenant)
    {
        // =====================================================
        // HAPUS DATA
        // =====================================================
        $tenant->delete();


        // =====================================================
        // REDIRECT
        // =====================================================
        return redirect()
            ->route('tenant.index')
            ->with('success', 'Data tenant berhasil dihapus.');
    }
}