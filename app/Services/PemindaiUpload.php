<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Symfony\Component\Process\Process;
use Throwable;

/**
 * Pemeriksaan keamanan file unggahan.
 *
 * Ini bukan pengganti antivirus penuh. Pemeriksaan bawaan menangkap:
 *  - file uji antivirus EICAR,
 *  - file program (Windows .exe/.dll dan Linux ELF) yang menyamar,
 *  - isi file yang tidak sesuai jenis yang diizinkan (cek isi, bukan ekstensi),
 *  - kode PHP atau script tersembunyi di dalam gambar,
 *  - konten aktif di PDF (JavaScript, Launch, file terlampir).
 * Jika ClamAV terpasang dan diaktifkan (CLAMAV_ENABLED=true), file juga
 * dipindai ClamAV. Konten PDF yang dikompres dalam object stream bisa lolos
 * dari pemeriksaan pola, itulah alasan ClamAV tetap disarankan di produksi.
 */
class PemindaiUpload
{
    private const MIME_DIIZINKAN = [
        'application/pdf',
        'image/png',
        'image/jpeg',
        'image/webp',
    ];

    /**
     * Tanda tangan uji EICAR dipecah agar file kode sumber ini sendiri
     * tidak dianggap virus oleh antivirus di komputer pengembang.
     */
    private static function eicar(): string
    {
        return 'X5O!P%@AP[4\\PZX54(P^)7CC)7}$'
            . 'EICAR-STANDARD-ANTIVIRUS-TEST-FILE!'
            . '$H+H*';
    }

    /**
     * @return string|null alasan penolakan, atau null jika file dianggap aman
     */
    public static function pindai(UploadedFile $file): ?string
    {
        $path = $file->getRealPath();

        if (! $path || ! is_readable($path)) {
            return 'File tidak dapat dibaca.';
        }

        $isi = @file_get_contents($path);

        if ($isi === false) {
            return 'File tidak dapat dibaca.';
        }

        if (str_contains($isi, self::eicar())) {
            return 'Terdeteksi sebagai file uji antivirus (EICAR).';
        }

        if (str_starts_with($isi, 'MZ') || str_starts_with($isi, "\x7FELF")) {
            return 'File berisi program yang dapat dijalankan.';
        }

        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($path);

        if (! in_array($mime, self::MIME_DIIZINKAN, true)) {
            return 'Jenis isi file (' . ($mime ?: 'tidak dikenal') . ') tidak diizinkan.';
        }

        if (str_starts_with($mime, 'image/') && preg_match('/<\?php|<\?=|<script\b/i', $isi)) {
            return 'Gambar mengandung kode tersembunyi.';
        }

        if ($mime === 'application/pdf'
            && preg_match('/\/(JavaScript|JS|Launch|EmbeddedFile)\b/', $isi, $cocok)) {
            return 'PDF berisi konten aktif (/' . $cocok[1] . ').';
        }

        if (config('keamanan.clamav.aktif')) {
            return self::pindaiClamav($path);
        }

        return null;
    }

    private static function pindaiClamav(string $path): ?string
    {
        try {
            $proses = new Process([(string) config('keamanan.clamav.biner'), '--no-summary', $path]);
            $proses->setTimeout(30);
            $proses->run();
        } catch (Throwable $e) {
            report($e);

            return 'Pemindai antivirus tidak dapat dijalankan.';
        }

        return match ($proses->getExitCode()) {
            0       => null,
            1       => 'ClamAV mendeteksi malware.',
            default => 'Pemindai antivirus tidak dapat dijalankan.',
        };
    }
}
