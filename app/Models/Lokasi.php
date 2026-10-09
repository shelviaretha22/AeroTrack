<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Tenant;

class Lokasi extends Model
{
    protected $fillable = [
        'terminal',
        'lantai',
        'jenis_ruangan',
        'nomor_ro',
        'kode_ruang',
        'lokasi',
        'luas_area',
        'status',
        'tenant_id',
        'catatan',
    ];

    /**
     * Lokasi dimiliki oleh satu tenant.
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Kode lokasi asli tetap tersimpan di database.
     * Kode tampilan menyesuaikan jenis usaha tenant.
     */
    public function getKodeTampilanAttribute()
    {
        $jenisUsaha = $this->tenant?->jenis_usaha
            ?? $this->tenant?->kerjaSama?->jenis_usaha;

        $kodeUsaha = [
            'F&B'       => 'FB',
            'Retail'    => 'RTL',
            'Service'   => 'SRV',
            'Lounge'    => 'LNG',
            'Duty Free' => 'DTF',
            'UMKM'      => 'UMK',
        ];

        // Jika belum terhubung ke tenant, tampilkan kode asli.
        if (!$jenisUsaha || !isset($kodeUsaha[$jenisUsaha])) {
            return $this->kode_ruang;
        }

        // Ambil nomor urut dari kode asli, misalnya T1-01-001.
        $bagianKode = explode('-', $this->kode_ruang);
        $nomorUrut = end($bagianKode);

        $lantai = is_numeric($this->lantai)
            ? str_pad((string) $this->lantai, 2, '0', STR_PAD_LEFT)
            : $this->lantai;

        return $kodeUsaha[$jenisUsaha]
            . '-' . $lantai
            . '-' . $nomorUrut;
    }
}