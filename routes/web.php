<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\TenantController;
use App\Http\Controllers\LokasiController;
use App\Http\Controllers\KontrakController;
use App\Http\Controllers\KerjaSamaController;
use App\Http\Controllers\ProfileController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ================================
// ROOT → LOGIN
// ================================

Route::get('/', function () {
    return redirect()->route('login');
});


// ================================
// DASHBOARD
// ================================

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('auth')->name('dashboard');


// ================================
// MANAGEMENT MENU
// ================================

// Tenant
Route::resource('/tenant', TenantController::class)
    ->middleware('auth');


// Lokasi
Route::resource('/lokasi', LokasiController::class)
    ->middleware('auth');

// Lokasi - Kerja Sama
Route::get(
    '/kerja-sama/{kerjaSama}/pilih-lokasi',
    [LokasiController::class, 'pilihUntukKerjaSama']
)->middleware('auth')->name('kerja-sama.lokasi.pilih');

Route::post(
    '/kerja-sama/{kerjaSama}/pilih-lokasi',
    [LokasiController::class, 'simpanPilihan']
)->middleware('auth')->name('lokasi.simpan-pilihan');


// Kerja Sama
Route::get('/kerja-sama', [KerjaSamaController::class, 'index'])
    ->middleware('auth')
    ->name('kerja-sama');

Route::get('/kerja-sama/create', [KerjaSamaController::class, 'create'])
    ->middleware('auth')
    ->name('kerja-sama.create');

Route::post('/kerja-sama', [KerjaSamaController::class, 'store'])
    ->middleware('auth')
    ->name('kerja-sama.store');

// Detail
Route::get('/kerja-sama/{id}', [KerjaSamaController::class, 'show'])
    ->middleware('auth')
    ->name('kerja-sama.show');

// Edit
Route::get('/kerja-sama/{id}/edit', [KerjaSamaController::class, 'edit'])
    ->middleware('auth')
    ->name('kerja-sama.edit');

// Update
Route::put('/kerja-sama/{id}', [KerjaSamaController::class, 'update'])
    ->middleware('auth')
    ->name('kerja-sama.update');

// Delete
Route::delete('/kerja-sama/{id}', [KerjaSamaController::class, 'destroy'])
    ->middleware('auth')
    ->name('kerja-sama.destroy');


// Kontrak
Route::get('/kontrak', [KontrakController::class, 'index'])
    ->middleware('auth')
    ->name('kontrak');

Route::get('/kontrak/{id}/acc', [KontrakController::class, 'acc'])
    ->middleware('auth')
    ->name('kontrak.acc');

Route::post('/kontrak/{id}/acc', [KontrakController::class, 'storeAcc'])
    ->middleware('auth')
    ->name('kontrak.storeAcc');

Route::get('/kontrak/{id}', [KontrakController::class, 'show'])
    ->middleware('auth')
    ->name('kontrak.show');


// Aktivasi
Route::get('/aktivasi', function () {
    return view('aktivasi.index');
})->middleware('auth')->name('aktivasi');


// Pendapatan
Route::get('/pendapatan', function () {
    return view('pendapatan.index');
})->middleware('auth')->name('pendapatan');


// Monitoring
Route::get('/monitoring', function () {
    return view('monitoring.index');
})->middleware('auth')->name('monitoring');


// Laporan
Route::get('/laporan', function () {
    return view('laporan.index');
})->middleware('auth')->name('laporan');


// ================================
// PROFILE
// ================================

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])
        ->name('profile.index');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::put('/profile/password', [
        ProfileController::class,
        'updatePassword',
    ])->name('profile.password.update');

    Route::patch('/profile/photo', [
        ProfileController::class,
        'updatePhoto',
    ])->name('profile.photo.update');

    Route::patch('/profile/preferences', [
        ProfileController::class,
        'updatePreferences',
    ])->name('profile.preferences.update');
});

// ================================
// AUTHENTICATION
// ================================

require __DIR__.'/auth.php';