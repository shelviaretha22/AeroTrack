<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\KerjaSama;
use App\Models\Tenant;
use App\Models\Lokasi;

class Kontrak extends Model
{
    protected $table = 'kontraks';

    protected $fillable = [
        'nomor_kontrak',
        'kerja_sama_id',
        'tenant_id',
        'lokasi_id',
        'tanggal_pengajuan',
        'tanggal_acc',
        'ba_path',
        'ttd_commercial_path',
        'ttd_type',
        'nama_commercial',
        'jabatan_commercial',
        'final_pdf_path',
        'final_uploaded_at',
        'final_signature_confirmed',
        'status',
        'status_perpanjangan',
        'tanggal_konfirmasi',
        'klausul_tambahan',
    ];

    protected $casts = [
        'tanggal_pengajuan' => 'date',
        'tanggal_acc' => 'date',
        'final_uploaded_at' => 'datetime',
        'final_signature_confirmed' => 'boolean',
        'tanggal_konfirmasi' => 'date',
    ];

    public function kerjaSama()
    {
        return $this->belongsTo(KerjaSama::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function lokasi()
    {
        return $this->belongsTo(Lokasi::class);
    }
}
