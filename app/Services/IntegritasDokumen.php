<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

/**
 * Keutuhan dokumen dengan hash SHA-256.
 * Hash dicatat saat dokumen disimpan dan dicek ulang setiap dokumen dibuka.
 */
class IntegritasDokumen
{
    public static function hash(?string $path, string $disk = 'local'): ?string
    {
        if (! $path) {
            return null;
        }

        $mutlak = Storage::disk($disk)->path($path);

        return is_file($mutlak) ? hash_file('sha256', $mutlak) : null;
    }

    /**
     * @return array{status:string, dihitung:?string, tersimpan:?string}
     *   status: tidak-ada | hilang | belum-tercatat | cocok | berubah
     */
    public static function periksa(?string $path, ?string $tersimpan): array
    {
        if (! $path) {
            return ['status' => 'tidak-ada', 'dihitung' => null, 'tersimpan' => $tersimpan];
        }

        $dihitung = self::hash($path);

        if ($dihitung === null) {
            return ['status' => 'hilang', 'dihitung' => null, 'tersimpan' => $tersimpan];
        }

        if ($tersimpan === null || $tersimpan === '') {
            return ['status' => 'belum-tercatat', 'dihitung' => $dihitung, 'tersimpan' => null];
        }

        return [
            'status'    => hash_equals($tersimpan, $dihitung) ? 'cocok' : 'berubah',
            'dihitung'  => $dihitung,
            'tersimpan' => $tersimpan,
        ];
    }
}
