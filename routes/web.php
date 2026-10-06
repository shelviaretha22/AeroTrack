<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\LokasiController;
use App\Http\Controllers\KontrakController;

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


// Kerja Sama
Route::get('/kerja-sama', function () {
    return view('kerja-sama.index');
})->middleware('auth')->name('kerja-sama');


// Kontrak
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

Route::get('/profile', function () {
    return view('profile.index');
})->middleware('auth')->name('profile');


// ================================
// AUTHENTICATION
// ================================

require __DIR__.'/auth.php';