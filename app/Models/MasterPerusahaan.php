<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterPerusahaan extends Model
{
    protected $table = 'master_perusahaan';

    protected $fillable = [
    'nama_perusahaan',
    'domisili',
    'alamat',
    'nama_penandatangan',
    'jabatan_penandatangan',
    'telepon',
    'email',
    ];
}