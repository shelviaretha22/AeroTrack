<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Throwable;

/**
 * Catatan audit (accounting): siapa melakukan apa, kapan, dan dari mana.
 * Log hanya boleh ditambah, tidak diubah dan tidak dihapus lewat aplikasi.
 */
class AuditLog extends Model
{
    public const UPDATED_AT = null;

    public const MODUL = ['Autentikasi', 'Akun', 'Lokasi', 'Aktivasi'];

    protected $fillable = [
        'user_id',
        'modul',
        'aksi',
        'level',
        'keterangan',
        'subjek_tipe',
        'subjek_id',
        'ip_address',
        'user_agent',
    ];

    protected function casts(): array
    {
        return ['created_at' => 'datetime'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mencatat satu kejadian. Kegagalan mencatat tidak boleh
     * menggagalkan proses utama, sehingga error hanya dilaporkan.
     */
    public static function catat(
        string $modul,
        string $aksi,
        ?string $keterangan = null,
        ?Model $subjek = null,
        string $level = 'info',
        ?int $userId = null
    ): void {
        try {
            $request = request();

            static::create([
                'user_id' => $userId ?? Auth::id(),
                'modul' => $modul,
                'aksi' => $aksi,
                'level' => $level,
                'keterangan' => $keterangan !== null ? mb_substr($keterangan, 0, 2000) : null,
                'subjek_tipe' => $subjek ? class_basename($subjek) : null,
                'subjek_id' => $subjek?->getKey(),
                'ip_address' => $request?->ip(),
                'user_agent' => $request ? mb_substr((string) $request->userAgent(), 0, 255) : null,
            ]);
        } catch (Throwable $e) {
            report($e);
        }
    }

    /**
     * Ringkasan perubahan field: "luas_area: 10 → 12; lokasi: A → B".
     * Hanya nilai field yang diubah; tidak pernah memuat password.
     *
     * @param array<string,mixed> $asli   nilai sebelum diubah
     * @param array<string,mixed> $berubah nilai sesudah (getDirty)
     */
    public static function ringkasPerubahan(array $asli, array $berubah): string
    {
        return collect($berubah)
            ->except(['password', 'remember_token', 'updated_at'])
            ->map(fn ($baru, $kolom) => $kolom . ': '
                . (($asli[$kolom] ?? '') === '' ? '-' : $asli[$kolom])
                . ' → '
                . ($baru === null || $baru === '' ? '-' : $baru))
            ->implode('; ');
    }
}
