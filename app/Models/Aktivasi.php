<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Aktivasi extends Model
{
    protected $table = 'aktivasis';

    protected $fillable = [
        'kerja_sama_id',
        'nomor_pengajuan',
        'tanggal_pengajuan',
        'surat_pengajuan_path',
        'status',
        'tanggal_keputusan',
        'catatan',
        'dokumen_path',
        'ttd_commercial_path',
        'ttd_type',
        'nama_penandatangan',
        'jabatan_penandatangan',
    ];

    protected $casts = [
        'tanggal_pengajuan' => 'date',
        'tanggal_keputusan' => 'date',
    ];

    public function kerjaSama(): BelongsTo
    {
        return $this->belongsTo(KerjaSama::class);
    }
}