<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}