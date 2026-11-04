<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PendapatanTagihan extends Model
{
    protected $table = 'pendapatan_tagihans';

    protected $fillable = [
        'tenant_id',
        'kerja_sama_id',
        'tahun',
        'bulan',
        'jumlah_tagihan',
        'status_invoice',
        'tanggal_diberikan',
    ];

    protected $casts = [
        'status_invoice' => 'boolean',
        'tanggal_diberikan' => 'date',
        'jumlah_tagihan' => 'decimal:2',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function kerjaSama()
    {
        return $this->belongsTo(KerjaSama::class);
    }
}
