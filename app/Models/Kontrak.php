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

        // =====================================
        // RELASI DAN NOMOR KONTRAK
        // =====================================

        'nomor_kontrak',
        'nomor_kontrak_mitra',
        'kerja_sama_id',
        'tenant_id',
        'lokasi_id',

        // =====================================
        // INFORMASI DOKUMEN KONTRAK
        // =====================================

        'tanggal_pengajuan',
        'tanggal_dokumen',
        'logo_mitra_path',
        'periode_evaluasi_tarif',
        'satuan_evaluasi_tarif',
        'klausul_kepemilikan_fasilitas',

        // =====================================
        // SNAPSHOT IDENTITAS DAN KONTAK MITRA
        // =====================================

        'nama_mitra_snapshot',
        'nama_pic_mitra_snapshot',
        'jabatan_pic_mitra_snapshot',
        'alamat_mitra_snapshot',
        'domisili_mitra_snapshot',
        'telepon_mitra_snapshot',
        'fax_mitra_snapshot',
        'email_mitra_snapshot',

        // =====================================
        // SNAPSHOT PENANDATANGAN MITRA
        // =====================================

        'nama_penandatangan_mitra',
        'jabatan_penandatangan_mitra',

        // =====================================
        // SNAPSHOT KERJA SAMA DAN LOKASI
        // =====================================

        'data_kerja_sama_snapshot',
        'data_lokasi_snapshot',

        // =====================================
        // PILIHAN LAMPIRAN PERJANJIAN
        // =====================================

        'lampiran_perjanjian',

        // =====================================
        // FIELD LAMA - TETAP DIPERTAHANKAN
        // =====================================

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

        // Tanggal
        'tanggal_pengajuan' => 'date',
        'tanggal_dokumen' => 'date',
        'tanggal_acc' => 'date',
        'tanggal_konfirmasi' => 'date',

        // Waktu upload
        'final_uploaded_at' => 'datetime',

        // Boolean
        'final_signature_confirmed' => 'boolean',

        // Angka
        'periode_evaluasi_tarif' => 'integer',

        // JSON menjadi array PHP
        'data_kerja_sama_snapshot' => 'array',
        'data_lokasi_snapshot' => 'array',
        'lampiran_perjanjian' => 'array',
    ];

    // =====================================
    // RELASI MODEL
    // =====================================

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
