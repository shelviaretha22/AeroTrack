
<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\TenantController;
use App\Http\Controllers\LokasiController;
use App\Http\Controllers\KontrakController;
use App\Http\Controllers\KerjaSamaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AktivasiController;

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
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('auth')->name('dashboard');

// ================================
// MANAGEMENT MENU
// ================================

// TENANT
Route::resource('/tenant', TenantController::class)
    ->middleware('auth');

// LOKASI
Route::resource('/lokasi', LokasiController::class)
    ->middleware('auth');

// LOKASI - KERJA SAMA
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
// Letakkan sebelum route detail /kerja-sama/{id}
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

Route::get(
    '/kontrak',
    [KontrakController::class, 'index']
)->middleware('auth')
    ->name('kontrak');

Route::put('/kontrak/{id}/dokumen', [
    KontrakController::class,
    'updateDokumen',
])->middleware('auth')->name('kontrak.dokumen.update');

Route::post(
    '/kontrak/buat/{kerjaSamaId}',
    [KontrakController::class, 'store']
)->middleware('auth')
    ->name('kontrak.store');

Route::get(
    '/kontrak/{id}/acc',
    [KontrakController::class, 'acc']
)->middleware('auth')
    ->name('kontrak.acc');

Route::post(
    '/kontrak/{id}/acc',
    [KontrakController::class, 'storeAcc']
)->middleware('auth')
    ->name('kontrak.storeAcc');

Route::post(
    '/kontrak/{id}/preview-pdf',
    [KontrakController::class, 'previewPdf']
)->middleware('auth')->name('kontrak.previewPdf');

Route::get('/kontrak/{id}/final-pdf', [KontrakController::class, 'finalPdf'])
    ->middleware('auth')
    ->name('kontrak.finalPdf');

// ================================
// UPLOAD DOKUMEN KONTRAK FINAL
// ================================

// Menampilkan halaman upload dokumen final
Route::get(
    '/kontrak/{id}/upload-final',
    [KontrakController::class, 'formUploadFinal']
)->middleware('auth')
    ->name('kontrak.uploadFinal');

// Memproses upload dokumen final
Route::post(
    '/kontrak/{id}/upload-final',
    [KontrakController::class, 'storeUploadFinal']
)->middleware('auth')
    ->name('kontrak.storeUploadFinal');

Route::post(
    '/kontrak/{id}/konfirmasi-perpanjangan',
    [KontrakController::class, 'storeKonfirmasiPerpanjangan']
)->middleware('auth')
    ->name('kontrak.konfirmasi-perpanjangan');

Route::get(
    '/kontrak/{id}',
    [KontrakController::class, 'show']
)->middleware('auth')
    ->name('kontrak.show');

Route::get(
    '/kerja-sama/{kerjaSamaId}/pratinjau-kontrak',
    [KontrakController::class, 'previewDocument']
)->middleware('auth')
    ->name('kontrak.preview');


 // ================================
// AKTIVASI
// ================================

Route::middleware('auth')->group(function () {
    // Halaman daftar aktivasi
    Route::get(
        '/aktivasi',
        [AktivasiController::class, 'index']
    )->name('aktivasi');

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
});


// ================================
// PENDAPATAN
// ================================

Route::get('/pendapatan', function () {
    return view('pendapatan.index');
})->middleware('auth')
    ->name('pendapatan');

// ================================
// MONITORING
// ================================

Route::get('/monitoring', function () {
    return view('monitoring.index');
})->middleware('auth')
    ->name('monitoring');

// ================================
// LAPORAN
// ================================

Route::get('/laporan', function () {
    return view('laporan.index');
})->middleware('auth')
    ->name('laporan');

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

    Route::patch(
        '/profile/preferences',
        [ProfileController::class, 'updatePreferences']
    )->name('profile.preferences.update');
});

// ================================
// AUTHENTICATION
// ================================

require __DIR__.'/auth.php';
