<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    protected $fillable = [
        'mitra_usaha',
        'nama_tenant',
        'jenis_usaha',
        'bentuk_kerja_sama',
        'npwp',
        'nama_pic',
        'jabatan_pic',
        'no_hp_pic',
        'email_pic',
    ];
}