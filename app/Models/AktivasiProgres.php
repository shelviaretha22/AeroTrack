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
}
