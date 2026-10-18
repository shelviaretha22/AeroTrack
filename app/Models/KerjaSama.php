<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Tenant;
use App\Models\Lokasi;
use App\Models\Aktivasi;

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
        'jabatan_pic_commercial',
        'catatan',
        'berita_acara_path',
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

    /**
     * Mengambil satu pengajuan aktivasi terbaru.
     */
    public function aktivasi(): HasOne
    {
        return $this->hasOne(Aktivasi::class)->latestOfMany();
    }

    /**
     * Mengambil seluruh riwayat pengajuan aktivasi.
     */
    public function aktivasis(): HasMany
    {
        return $this->hasMany(Aktivasi::class);
    }
}