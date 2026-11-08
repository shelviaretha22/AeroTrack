<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Ringkasan email harian untuk pengguna yang mengaktifkan notifikasi email.
// Butuh scheduler berjalan: `php artisan schedule:work` (lokal) atau cron di server.
Schedule::command('notifikasi:kirim-ringkasan')
    ->dailyAt('07:00')
    ->timezone('Asia/Jakarta');

// ETL ringkasan Lokasi (okupansi) dan Aktivasi untuk laporan.
// Butuh scheduler berjalan, sama seperti ringkasan notifikasi di atas.
Schedule::command('etl:lokasi-aktivasi --pemicu=jadwal')
    ->dailyAt('01:00')
    ->timezone('Asia/Jakarta');
