<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Tenant;
use App\Models\Lokasi;

class KerjaSama extends Model
{
    protected $fillable = [
        'mitra_usaha',
        'brand',
        'jenis_usaha',
        'bentuk_kerja_sama',
        'tanggal_pengajuan',
        'status',
        'tanggal_mulai',
        'tanggal_berakhir',
        'pic_commercial',
        'catatan',
    ];

    protected $casts = [
        'tanggal_pengajuan' => 'date',
        'tanggal_mulai' => 'date',
        'tanggal_berakhir' => 'date',
    ];

    /**
     * Satu data Kerja Sama memiliki satu Tenant.
     */
    public function tenant()
    {
        return $this->hasOne(Tenant::class);
    }

    /**
     * Mengakses lokasi melalui Tenant milik Kerja Sama.
     */
    public function lokasi()
    {
        return $this->hasOneThrough(
            Lokasi::class,
            Tenant::class,
            'kerja_sama_id',
            'tenant_id',
            'id',
            'id'
        );
    }
}
