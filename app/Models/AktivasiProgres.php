<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AktivasiProgres extends Model
{
    protected $table = 'aktivasi_progres';

    public const STATUS_KONTRAK_FISIK = [
        'Belum Selesai',
        'Dalam Proses',
        'Menunggu Tanda Tangan',
        'Selesai',
    ];

    public const STATUS_KONTRAK_SISTEM = [
        'Belum Tercatat',
        'Pending Meter',
        'Aktif',
    ];

    protected $fillable = [
        'kerja_sama_id',
        'kontrak_fisik_status',
        'kontrak_fisik_catatan',
        'kontrak_fisik_updated_at',
        'kontrak_sistem_status',
        'nomor_konsumen',
        'tanggal_aktivasi_sistem',
        'kontrak_sistem_catatan',
        'kontrak_sistem_bukti_path',
        'kontrak_sistem_updated_at',
    ];

    protected $casts = [
        'kontrak_fisik_updated_at' => 'datetime',
        'tanggal_aktivasi_sistem' => 'date',
        'kontrak_sistem_updated_at' => 'datetime',
    ];

    public function kerjaSama(): BelongsTo
    {
        return $this->belongsTo(KerjaSama::class);
    }

    /**
     * Menerbitkan nomor konsumen otomatis (format NK-00001) bila belum ada.
     * Dipanggil saat aktivasi disetujui; aman dipanggil berulang.
     */
    public static function terbitkanNomorKonsumen(int $kerjaSamaId): string
    {
        $progres = static::firstOrNew(['kerja_sama_id' => $kerjaSamaId]);

        if (filled($progres->nomor_konsumen)) {
            return $progres->nomor_konsumen;
        }

        $terakhir = static::whereNotNull('nomor_konsumen')
            ->where('nomor_konsumen', 'like', 'NK-%')
            ->orderByDesc('nomor_konsumen')
            ->value('nomor_konsumen');

        $berikut = $terakhir ? ((int) substr($terakhir, 3)) + 1 : 1;
        $progres->nomor_konsumen = 'NK-' . str_pad((string) $berikut, 5, '0', STR_PAD_LEFT);
        $progres->save();

        return $progres->nomor_konsumen;
    }
}
