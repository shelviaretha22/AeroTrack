<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\KerjaSama;
use Illuminate\Http\Request;
use App\Models\Lokasi;
use Illuminate\Support\Facades\DB;

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
        // QUERY DASAR KERJA SAMA
        // =====================================================
        $query = KerjaSama::query();

        // =====================================================
        // SEARCH
        // =====================================================
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
            // MITRA USAHA A → Z
            // -------------------------------------------------
            case 'az':
                $query->orderBy('mitra_usaha', 'asc');
                break;

            // -------------------------------------------------
            // MITRA USAHA Z → A
            // -------------------------------------------------
            case 'za':
                $query->orderBy('mitra_usaha', 'desc');
                break;

            // -------------------------------------------------
            // DEFAULT
            // -------------------------------------------------
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        // =====================================================
        // AMBIL DATA KERJA SAMA
        // =====================================================
        $kerjaSamas = $query->get();

        // =====================================================
        // AMBIL DATA TENANT YANG SUDAH TERHUBUNG
        // DENGAN KERJA SAMA
        // =====================================================
        $tenantByKerjaSama = Tenant::whereNotNull('kerja_sama_id')
            ->get()
            ->keyBy('kerja_sama_id');

        // =====================================================
        // TAMPILKAN HALAMAN TENANT
        // =====================================================
        return view('tenant.index', compact(
            'kerjaSamas',
            'tenantByKerjaSama'
        ));
    }

    /**
     * Menampilkan form input Tenant berdasarkan Kerja Sama.
     *
     * Contoh:
     * /tenant/create?kerja_sama_id=1
     */
    public function create(Request $request)
    {
        // =====================================================
        // VALIDASI KERJA SAMA ID
        // =====================================================
        $request->validate([
            'kerja_sama_id' => [
                'required',
                'integer',
                'exists:kerja_samas,id',
            ],
        ]);

        // =====================================================
        // AMBIL DATA KERJA SAMA
        // =====================================================
        $kerjaSama = KerjaSama::findOrFail(
            $request->kerja_sama_id
        );

        // =====================================================
        // CEK APAKAH KERJA SAMA SUDAH MEMILIKI TENANT
        // =====================================================
        $tenant = Tenant::where(
            'kerja_sama_id',
            $kerjaSama->id
        )->first();

        // =====================================================
        // JIKA SUDAH ADA TENANT
        // =====================================================
        if ($tenant) {
            return redirect()
                ->route('tenant.show', $tenant)
                ->with(
                    'info',
                    'Data Tenant untuk Kerja Sama ini sudah diinput.'
                );
        }

        // =====================================================
        // TAMPILKAN FORM TENANT
        // =====================================================
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
            'npwp.required' => 'NPWP wajib diisi.',
            'npwp.regex' => 'NPWP hanya boleh berisi angka.',

            'nib.required' => 'NIB wajib diisi.',
            'nib.regex' => 'NIB hanya boleh berisi angka.',

            'bentuk_badan_usaha.required' => 'Bentuk badan usaha wajib dipilih.',

            'alamat_perusahaan.required' => 'Alamat perusahaan/usaha wajib diisi.',

            'nama_pic.required' => 'Nama PIC wajib diisi.',
            'nama_pic.regex' => 'Nama PIC hanya boleh berisi huruf dan spasi.',

            'jabatan_pic.required' => 'Jabatan PIC wajib diisi.',

            'no_hp_pic.required' => 'No. HP wajib diisi.',
            'no_hp_pic.regex' => 'No. HP hanya boleh berisi angka.',

            'email_pic.required' => 'Email PIC wajib diisi.',
            'email_pic.email' => 'Format email tidak valid.',

            'kerja_sama_id.required' => 'Data Kerja Sama tidak ditemukan.',
            'kerja_sama_id.exists' => 'Data Kerja Sama tidak valid.',
        ]);

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
            'npwp.required' => 'NPWP wajib diisi.',
            'npwp.regex' => 'NPWP hanya boleh berisi angka.',

            'nib.required' => 'NIB wajib diisi.',
            'nib.regex' => 'NIB hanya boleh berisi angka.',

            'bentuk_badan_usaha.required' => 'Bentuk badan usaha wajib dipilih.',

            'alamat_perusahaan.required' => 'Alamat perusahaan/usaha wajib diisi.',

            'nama_pic.required' => 'Nama PIC wajib diisi.',
            'nama_pic.regex' => 'Nama PIC hanya boleh berisi huruf dan spasi.',

            'jabatan_pic.required' => 'Jabatan PIC wajib diisi.',

            'no_hp_pic.required' => 'No. HP wajib diisi.',
            'no_hp_pic.regex' => 'No. HP hanya boleh berisi angka.',

            'email_pic.required' => 'Email PIC wajib diisi.',
            'email_pic.email' => 'Format email tidak valid.',
        ]);

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
}