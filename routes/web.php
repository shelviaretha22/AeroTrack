<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TenantController;

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
Route::get('/lokasi', function () {
    return view('lokasi.index');
})->middleware('auth')->name('lokasi');


// Kerja Sama
Route::get('/kerja-sama', function () {
    return view('kerja-sama.index');
})->middleware('auth')->name('kerja-sama');


// Kontrak
Route::get('/kontrak', function () {
    return view('kontrak.index');
})->middleware('auth')->name('kontrak');


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