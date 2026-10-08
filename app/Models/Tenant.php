<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\KerjaSama;

class Tenant extends Model
{
    protected $fillable = [
        'kerja_sama_id',

        // Informasi Kerja Sama
        'mitra_usaha',
        'nama_tenant',
        'jenis_usaha',
        'bentuk_kerja_sama',

        // Legalitas Tenant
        'npwp',
        'nib',
        'bentuk_badan_usaha',
        'alamat_perusahaan',

        // PIC Tenant
        'nama_pic',
        'jabatan_pic',
        'no_hp_pic',
        'email_pic',
    ];

    /**
     * Relasi Tenant ke Kerja Sama.
     */
    public function kerjaSama()
    {
        return $this->belongsTo(KerjaSama::class);
    }
}