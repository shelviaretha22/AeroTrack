<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Tenant;

class Lokasi extends Model
{
    protected $fillable = [
        'terminal',
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
}