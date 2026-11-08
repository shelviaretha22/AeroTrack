<?php

return [

    /*
    | Content-Security-Policy dalam mode "report-only": pelanggaran hanya
    | dicatat di console browser dan tidak diblokir. Berguna saat menguji
    | halaman baru. Set CSP_REPORT_ONLY=true di .env untuk mengaktifkan.
    */
    'csp_report_only' => (bool) env('CSP_REPORT_ONLY', false),

    /*
    | Pemindaian upload dengan ClamAV (opsional). Pemeriksaan bawaan aplikasi
    | (tanda tangan EICAR, header program, jenis isi file, konten aktif PDF)
    | selalu berjalan, ClamAV hanya menambah lapisan kedua jika terpasang.
    */
    'clamav' => [
        'aktif' => (bool) env('CLAMAV_ENABLED', false),
        'biner' => env('CLAMAV_BINARY', 'clamscan'),
    ],

];
