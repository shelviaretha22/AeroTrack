<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Tenant;
use App\Models\Lokasi;
use App\Models\Aktivasi;
use Illuminate\Support\Facades\Storage;

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
        'berita_acara_final_path',
        'berita_acara_final_uploaded_at',
    ];

    protected $casts = [
        'tanggal_pengajuan' => 'date',
        'tanggal_mulai' => 'date',
        'tanggal_berakhir' => 'date',
        'berita_acara_final_uploaded_at' => 'datetime',
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

    /**
     * Progres Kontrak Fisik dan Kontrak Sistem.
     */
    public function progres(): HasOne
    {
        return $this->hasOne(AktivasiProgres::class);
    }

    /**
     * Riwayat perubahan pada halaman Aktivasi.
     */
    public function aktivasiRiwayats(): HasMany
    {
        return $this->hasMany(AktivasiRiwayat::class)->latest();
    }

    /**
     * Kontrak terbaru milik Kerja Sama (dirujuk, tidak disalin).
     */
    public function kontrakTerbaru(): HasOne
    {
        return $this->hasOne(Kontrak::class)->latestOfMany();
    }

    // ---------------------------------------------------------------
    // Status turunan untuk menu Aktivasi (dihitung dari data, bukan
    // diisi manual).
    // ---------------------------------------------------------------

    public const POSISI_KONTRAK_FISIK = [
        'BA Belum Dibuat',
        'BA Belum TTD',
        'BA Selesai',
        'Kontrak Dalam Proses',
        'Kontrak Selesai',
    ];

    public const STATUS_KONTRAK_SISTEM = ['Belum Aktif', 'Aktif'];

    public const STATUS_AKTIVASI = [
        'Menunggu BA',
        'Belum Diajukan',
        'Menunggu Keputusan',
        'Disetujui',
        'Ditolak',
    ];

    public function baTersedia(): bool
    {
        return $this->berita_acara_path
            && Storage::disk('local')->exists($this->berita_acara_path);
    }

    public function baFinalTersedia(): bool
    {
        return $this->berita_acara_final_path
            && Storage::disk('local')->exists($this->berita_acara_final_path);
    }

    /** Status BA: belum dibuat, belum TTD, atau final (sudah TTD). */
    public function statusBa(): string
    {
        if (!$this->baTersedia()) {
            return 'BA Belum Dibuat';
        }

        return $this->baFinalTersedia() ? 'BA Selesai' : 'BA Belum TTD';
    }

    /** Posisi proses BA dan kontrak fisik. */
    public function posisiKontrakFisik(): string
    {
        $kontrak = $this->kontrakTerbaru;

        if ($kontrak) {
            // Kontrak dianggap selesai bila dokumen final sudah diunggah.
            // Status kontrak sendiri berubah menjadi Aktif/Nonaktif (bukan
            // "Selesai") setelah upload, jadi jangan dipakai sebagai penanda.
            return $kontrak->finalTersedia()
                ? 'Kontrak Selesai'
                : 'Kontrak Dalam Proses';
        }

        return $this->statusBa();
    }

    /** Aktif otomatis setelah Commercial pusat menyetujui aktivasi. */
    public function statusKontrakSistem(): string
    {
        return $this->aktivasi?->status === 'Disetujui'
            ? 'Aktif'
            : 'Belum Aktif';
    }

    public function statusAktivasi(): string
    {
        if ($this->aktivasi) {
            return $this->aktivasi->status;
        }

        return $this->baTersedia() ? 'Belum Diajukan' : 'Menunggu BA';
    }

    /**
     * Lima tahap progres dari BA sampai Kontrak Sistem aktif.
     * Dipakai untuk diagram progres di halaman Aktivasi.
     * status: done | todo | rejected
     */
    public function tahapProgres(): array
    {
        $fisik = $this->posisiKontrakFisik();
        $aktivasi = $this->statusAktivasi();

        $ya = fn (bool $ok) => $ok ? 'done' : 'todo';

        return [
            [
                'label' => 'BA dibuat',
                'status' => $ya($fisik !== 'BA Belum Dibuat'),
            ],
            [
                'label' => 'BA ditandatangani',
                'status' => $ya(in_array($fisik, [
                    'BA Selesai', 'Kontrak Dalam Proses', 'Kontrak Selesai',
                ], true)),
            ],
            [
                'label' => 'Kontrak selesai',
                'status' => $ya($fisik === 'Kontrak Selesai'),
            ],
            [
                'label' => 'Aktivasi diajukan',
                'status' => $ya(in_array($aktivasi, [
                    'Menunggu Keputusan', 'Disetujui', 'Ditolak',
                ], true)),
            ],
            [
                'label' => 'Sistem aktif',
                'status' => $aktivasi === 'Disetujui'
                    ? 'done'
                    : ($aktivasi === 'Ditolak' ? 'rejected' : 'todo'),
            ],
        ];
    }
}
