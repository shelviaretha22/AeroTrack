<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\TenantController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LokasiController;
use App\Http\Controllers\LokasiAnalisisController;
use App\Http\Controllers\KontrakController;
use App\Http\Controllers\KerjaSamaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AktivasiController;
use App\Http\Controllers\AktivasiAnalisisController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\PendapatanController;
use App\Http\Controllers\LaporanController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ROOT → LOGIN
Route::get('/', function () {
    return redirect()->route('login');
});

// DASHBOARD
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware('auth')
    ->name('dashboard');

// ================================
// MANAGEMENT MENU
// ================================

// TENANT - DRILLDOWN GRAFIK ANALISIS
Route::get('/tenant/drilldown', [TenantController::class, 'drilldown'])
    ->middleware('auth')
    ->name('tenant.drilldown');

// TENANT - HALAMAN SIMULASI PORTOFOLIO WHAT-IF
Route::get('/tenant/whatif', [TenantController::class, 'whatif'])
    ->middleware('auth')
    ->name('tenant.whatif');

// TENANT - HALAMAN LAPORAN
Route::middleware('auth')->prefix('tenant')->name('tenant.')->group(function () {
    Route::get('/laporan', [TenantController::class, 'laporan'])
        ->name('laporan');

    Route::get('/laporan/pdf', [TenantController::class, 'laporanPdf'])
        ->name('laporan.pdf');

    // Ekspor Laporan Tenant ke Excel
    Route::get('/laporan/excel', [TenantController::class, 'laporanExcel'])
        ->name('laporan.excel');
});

// TENANT
Route::resource('/tenant', TenantController::class)
    ->middleware('auth');

// LOKASI - data drilldown okupansi (harus sebelum resource agar tidak
// dianggap sebagai /lokasi/{lokasi})
Route::get('/lokasi/drilldown', [LokasiController::class, 'drilldown'])
    ->middleware('auth')
    ->name('lokasi.drilldown');

// LOKASI - analisis what-if dan laporan Lokasi + Aktivasi (hasil ETL).
// Harus sebelum resource agar tidak dianggap sebagai /lokasi/{lokasi}.
Route::middleware('auth')->prefix('lokasi')->name('lokasi.')->group(function () {
    Route::get('/whatif', [LokasiAnalisisController::class, 'whatif'])->name('whatif');
    Route::get('/laporan', [LokasiAnalisisController::class, 'laporan'])->name('laporan');
    Route::get('/laporan/pdf', [LokasiAnalisisController::class, 'laporanPdf'])->name('laporan.pdf');
    Route::get('/laporan/excel', [LokasiAnalisisController::class, 'laporanExcel'])->name('laporan.excel');
    Route::post('/laporan/etl', [LokasiAnalisisController::class, 'jalankanEtl'])->name('laporan.etl');
});

// LOKASI
Route::resource('/lokasi', LokasiController::class)
    ->middleware('auth');

// ================================
// LOKASI - KERJA SAMA
// ================================

Route::get(
    '/kerja-sama/{kerjaSama}/pilih-lokasi',
    [LokasiController::class, 'pilihUntukKerjaSama']
)->middleware('auth')
    ->name('kerja-sama.lokasi.pilih');

Route::post(
    '/kerja-sama/{kerjaSama}/pilih-lokasi',
    [LokasiController::class, 'simpanPilihan']
)->middleware('auth')
    ->name('lokasi.simpan-pilihan');

// ================================
// KERJA SAMA
// ================================

// Daftar Kerja Sama
Route::get(
    '/kerja-sama',
    [KerjaSamaController::class, 'index']
)->middleware('auth')
    ->name('kerja-sama');

// Form tambah
Route::get(
    '/kerja-sama/create',
    [KerjaSamaController::class, 'create']
)->middleware('auth')
    ->name('kerja-sama.create');

// Simpan data baru
Route::post(
    '/kerja-sama',
    [KerjaSamaController::class, 'store']
)->middleware('auth')
    ->name('kerja-sama.store');

// ================================
// BERITA ACARA
// ================================

// Buat/generate BA dan tampilkan halaman preview
Route::get(
    '/kerja-sama/{id}/berita-acara',
    [KerjaSamaController::class, 'beritaAcara']
)->middleware('auth')
    ->name('kerja-sama.berita-acara');

// Tampilkan file PDF BA di browser
Route::get(
    '/kerja-sama/{id}/berita-acara/pdf',
    [KerjaSamaController::class, 'lihatPdfBeritaAcara']
)->middleware('auth')
    ->name('kerja-sama.berita-acara.pdf');

// Unduh file PDF BA
Route::get(
    '/kerja-sama/{id}/berita-acara/download',
    [KerjaSamaController::class, 'downloadBeritaAcara']
)->middleware('auth')
    ->name('kerja-sama.berita-acara.download');

// Unggah BA final (sudah ditandatangani di luar sistem)
Route::post(
    '/kerja-sama/{id}/berita-acara/final',
    [KerjaSamaController::class, 'unggahBeritaAcaraFinal']
)->middleware('auth')
    ->name('kerja-sama.berita-acara.final.upload');

// Lihat BA final
Route::get(
    '/kerja-sama/{id}/berita-acara/final',
    [KerjaSamaController::class, 'lihatBeritaAcaraFinal']
)->middleware('auth')
    ->name('kerja-sama.berita-acara.final');

// Simpan hasil edit Berita Acara
Route::put(
    '/kerja-sama/{id}/berita-acara/simpan',
    [KerjaSamaController::class, 'simpanBeritaAcara']
)->middleware('auth')
    ->name('kerja-sama.berita-acara.simpan');

// Download Berita Acara Word
Route::get(
    '/kerja-sama/{id}/berita-acara/word',
    [KerjaSamaController::class, 'downloadBeritaAcaraWord']
)->middleware('auth')
    ->name('kerja-sama.berita-acara.word');

// ================================
// DETAIL, EDIT, UPDATE, DELETE
// ================================

// Detail Kerja Sama
Route::get(
    '/kerja-sama/{id}',
    [KerjaSamaController::class, 'show']
)->middleware('auth')
    ->name('kerja-sama.show');

// Form edit
Route::get(
    '/kerja-sama/{id}/edit',
    [KerjaSamaController::class, 'edit']
)->middleware('auth')
    ->name('kerja-sama.edit');

// Update data
Route::put(
    '/kerja-sama/{id}',
    [KerjaSamaController::class, 'update']
)->middleware('auth')
    ->name('kerja-sama.update');

// Hapus data
Route::delete(
    '/kerja-sama/{id}',
    [KerjaSamaController::class, 'destroy']
)->middleware('auth')
    ->name('kerja-sama.destroy');

// ================================
// KONTRAK
// ================================

Route::middleware('auth')->group(function () {

    // Dashboard kontrak
    Route::get(
        '/kontrak',
        [KontrakController::class, 'index']
    )->name('kontrak');

    // Membuat kontrak dari Kerja Sama
    Route::post(
        '/kontrak/buat/{kerjaSamaId}',
        [KontrakController::class, 'store']
    )->name('kontrak.store');

    // DRILL-DOWN DASHBOARD KONTRAK
    Route::get(
        '/kontrak/drilldown',
        [KontrakController::class, 'drilldown']
    )->name('kontrak.drilldown');

    // Menampilkan logo Mitra yang tersimpan secara privat
    Route::get(
        '/kontrak/{id}/logo-mitra',
        [KontrakController::class, 'tampilkanLogoMitra']
    )->name('kontrak.logoMitra');

    // Menyimpan perubahan formulir kontrak
        // Menyimpan perubahan formulir kontrak
    Route::put(
        '/kontrak/{id}/dokumen',
        [KontrakController::class, 'updateDokumen']
    )->name('kontrak.dokumen.update');

    // Menyimpan draft dokumen kontrak
    Route::post(
        '/kontrak/{id}/dokumen/draft',
        [KontrakController::class, 'simpanDraftDokumen']
    )->name('kontrak.dokumen.draft');

    // Mengunduh dokumen kontrak Word
    Route::get(
        '/kontrak/{id}/download-word',
        [KontrakController::class, 'downloadWord']
    )->name('kontrak.word');

    // Pratinjau / cetak dokumen kontrak PDF
    Route::get(
        '/kontrak/{id}/preview-pdf',
        [KontrakController::class, 'downloadPdf']
    )->name('kontrak.pdf');

    // =================================
    // ROUTE ACC LAMA
    // Sementara dipertahankan
    // =================================

    Route::get(
        '/kontrak/{id}/acc',
        [KontrakController::class, 'document']
    )->name('kontrak.acc');

    Route::post(
        '/kontrak/{id}/acc',
        [KontrakController::class, 'storeAcc']
    )->name('kontrak.storeAcc');

    Route::post(
        '/kontrak/{id}/preview-pdf',
        [KontrakController::class, 'previewPdf']
    )->name('kontrak.previewPdf');

    // =================================
    // UPLOAD DOKUMEN KONTRAK FINAL
    // =================================

    // Menampilkan halaman upload dokumen final
    Route::get(
        '/kontrak/{id}/upload-final',
        [KontrakController::class, 'formUploadFinal']
    )->name('kontrak.uploadFinal');

    // Menyimpan PDF ke penyimpanan sementara
    Route::post(
        '/kontrak/{id}/upload-final/temp',
        [KontrakController::class, 'storeUploadFinalTemp']
    )->name('kontrak.uploadFinal.temp');

    // Menampilkan PDF sementara
    Route::get(
        '/kontrak/{id}/upload-final/temp/preview',
        [KontrakController::class, 'previewUploadFinalTemp']
    )->name('kontrak.uploadFinal.temp.preview');

    // Menghapus PDF sementara
    Route::delete(
        '/kontrak/{id}/upload-final/temp',
        [KontrakController::class, 'deleteUploadFinalTemp']
    )->name('kontrak.uploadFinal.temp.delete');

    // Memproses penyimpanan dokumen final
    Route::post(
        '/kontrak/{id}/upload-final',
        [KontrakController::class, 'storeUploadFinal']
    )->name('kontrak.storeUploadFinal');

    // =================================
    // PDF FINAL
    // =================================

    Route::get(
        '/kontrak/{id}/final-pdf',
        [KontrakController::class, 'finalPdf']
    )->name('kontrak.finalPdf');

    // =================================
    // PERPANJANGAN KONTRAK
    // =================================

    Route::post(
        '/kontrak/{id}/konfirmasi-perpanjangan',
        [KontrakController::class, 'storeKonfirmasiPerpanjangan']
    )->name('kontrak.konfirmasi-perpanjangan');

        // =================================
    // WHAT-IF KONTRAK
    // =================================

    Route::get(
        '/kontrak/whatif',
        [KontrakController::class, 'whatif']
    )->name('kontrak.whatif');


    // =================================
    // LAPORAN KONTRAK
    // =================================

    Route::get(
        '/kontrak/laporan',
        [KontrakController::class, 'laporan']
    )->name('kontrak.laporan');

    Route::get(
        '/kontrak/laporan/pdf',
        [KontrakController::class, 'laporanPdf']
    )->name('kontrak.laporan.pdf');

    Route::get(
        '/kontrak/laporan/excel',
        [KontrakController::class, 'laporanExcel']
    )->name('kontrak.laporan.excel');

        // =================================
    // WHAT-IF KONTRAK
    // =================================

    Route::get(
        '/kontrak/whatif',
        [KontrakController::class, 'whatif']
    )->name('kontrak.whatif');


    // =================================
    // LAPORAN KONTRAK
    // =================================

    Route::get(
        '/kontrak/laporan',
        [KontrakController::class, 'laporan']
    )->name('kontrak.laporan');

    Route::get(
        '/kontrak/laporan/pdf',
        [KontrakController::class, 'laporanPdf']
    )->name('kontrak.laporan.pdf');

    Route::get(
        '/kontrak/laporan/excel',
        [KontrakController::class, 'laporanExcel']
    )->name('kontrak.laporan.excel');


    // =================================
    // DETAIL KONTRAK
    // =================================

    Route::get(
        '/kontrak/{id}',
        [KontrakController::class, 'show']
    )->name('kontrak.show');

    // Pratinjau kontrak sebelum dibuat
    Route::get(
        '/kerja-sama/{kerjaSamaId}/pratinjau-kontrak',
        [KontrakController::class, 'previewDocument']
    )->name('kontrak.preview');

});

// ================================
// AKTIVASI
// ================================

Route::middleware('auth')->group(function () {

    // Halaman daftar aktivasi
    Route::get(
        '/aktivasi',
        [AktivasiController::class, 'index']
    )->name('aktivasi');

    // Analisis Aktivasi (SIE): drilldown dan what-if, data JSON
    Route::get('/aktivasi/drilldown', [AktivasiAnalisisController::class, 'drilldown'])
        ->name('aktivasi.drilldown');
    Route::get('/aktivasi/whatif', [AktivasiAnalisisController::class, 'whatif'])
        ->name('aktivasi.whatif');

    // Halaman Detail Aktivasi
    Route::get(
        '/aktivasi/{kerjaSama}',
        [AktivasiController::class, 'show']
    )->whereNumber('kerjaSama')
        ->name('aktivasi.show');

    // Simpan catatan Kontrak Fisik
    Route::post(
        '/aktivasi/{kerjaSama}/kontrak-fisik',
        [AktivasiController::class, 'updateKontrakFisik']
    )->name('aktivasi.kontrak-fisik');


    // Membuat pengajuan aktivasi baru
    Route::post(
        '/aktivasi/{kerjaSamaId}/request',
        [AktivasiController::class, 'requestAktivasi']
    )->name('aktivasi.request');

    // Mencatat keputusan dari kantor pusat
    Route::post(
        '/aktivasi/{aktivasi}/keputusan',
        [AktivasiController::class, 'catatKeputusan']
    )->name('aktivasi.keputusan');

    // Membuka surat pengajuan PDF
    Route::get(
        '/aktivasi/{aktivasi}/surat',
        [AktivasiController::class, 'lihatSurat']
    )->name('aktivasi.surat');

    // Mengunduh dokumen keputusan
    Route::get(
        '/aktivasi/{aktivasi}/dokumen-keputusan',
        [AktivasiController::class, 'lihatDokumenKeputusan']
    )->name('aktivasi.dokumen-keputusan');

    // Status keutuhan dokumen (hash SHA-256), format JSON
    Route::get(
        '/aktivasi/{aktivasi}/integritas',
        [AktivasiController::class, 'integritas']
    )->name('aktivasi.integritas');

});

// ================================
// PENDAPATAN
// ================================

Route::middleware('auth')->group(function () {

    // Halaman Pendapatan
    Route::get(
        '/pendapatan',
        [PendapatanController::class, 'index']
    )->name('pendapatan');

    // Update status invoice
    Route::patch(
        '/pendapatan/{id}/status-invoice',
        [PendapatanController::class, 'updateStatusInvoice']
    )->name('pendapatan.status-invoice');
});


// ================================
// LAPORAN
// ================================

Route::middleware('auth')->group(function () {

    // Halaman utama Laporan
    Route::get(
        '/laporan',
        [LaporanController::class, 'index']
    )->name('laporan');

    // Ekspor Laporan ke PDF
    Route::get(
        '/laporan/pdf',
        [LaporanController::class, 'exportPdf']
    )->name('laporan.pdf');

    // Ekspor Laporan ke Excel
    Route::get(
        '/laporan/excel',
        [LaporanController::class, 'exportExcel']
    )->name('laporan.excel');

    // Drill-down halaman Laporan
    Route::get(
        '/laporan/drilldown',
        [LaporanController::class, 'drilldown']
    )->name('laporan.drilldown');

    // Detail Laporan berdasarkan Kerja Sama
    Route::get(
        '/laporan/{kerjaSama}',
        [LaporanController::class, 'show']
    )->whereNumber('kerjaSama')
     ->name('laporan.detail');

});

// ================================
// PROFILE
// ================================

Route::middleware('auth')->group(function () {

    Route::get(
        '/profile',
        [ProfileController::class, 'index']
    )->name('profile.index');

    Route::patch(
        '/profile',
        [ProfileController::class, 'update']
    )->name('profile.update');

    Route::put(
        '/profile/password',
        [ProfileController::class, 'updatePassword']
    )->name('profile.password.update');

    Route::patch(
        '/profile/photo',
        [ProfileController::class, 'updatePhoto']
    )->name('profile.photo.update');

    // Foto profil disajikan dari disk privat (hanya milik akun yang login).
    Route::get('/profile/foto', [ProfileController::class, 'foto'])
        ->name('profile.foto');

    // Log aktivitas akun (audit log)
    Route::get(
        '/profile/log-aktivitas',
        [ProfileController::class, 'logAktivitas']
    )->name('profile.log');

    Route::patch(
        '/profile/preferences',
        [ProfileController::class, 'updatePreferences']
    )->name('profile.preferences.update');

    // Simpan tema terang/gelap langsung saat dipilih
    Route::patch(
        '/profile/tema',
        [ProfileController::class, 'updateTheme']
    )->name('profile.theme.update');

    // Keluarkan akun dari perangkat/sesi lain
    Route::post(
        '/profile/sesi/keluar-lainnya',
        [ProfileController::class, 'keluarkanSesiLain']
    )->name('profile.sesi.keluar-lainnya');

       // MASTER PERUSAHAAN
    Route::get(
        '/profile/perusahaan',
        [ProfileController::class, 'editPerusahaan']
    )->name('profile.perusahaan.edit');

    Route::put(
        '/profile/perusahaan',
        [ProfileController::class, 'updatePerusahaan']
    )->name('profile.perusahaan.update');

});

// ================================
// AUTHENTICATION
// ================================

require __DIR__.'/auth.php';

// ================================
// NOTIFIKASI
// ================================

Route::middleware('auth')->prefix('notifikasi')->name('notifikasi.')->group(function () {

    Route::get('/', [NotifikasiController::class, 'index'])->name('index');

    // Tandai dibaca lalu buka halaman tujuan
    Route::get('/buka', [NotifikasiController::class, 'buka'])->name('buka');

    Route::post('/baca', [NotifikasiController::class, 'baca'])->name('baca');

    Route::post('/baca-semua', [NotifikasiController::class, 'bacaSemua'])->name('baca-semua');
});