<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\KerjaSama;
use App\Models\Lokasi;
use App\Models\NotifikasiDibaca;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * Menghitung notifikasi langsung dari data yang sudah ada.
 * Tidak ada tabel notifikasi: status "dibaca" disimpan per user
 * berdasarkan kunci yang stabil (lihat NotifikasiDibaca).
 */
class NotifikasiService
{
    /** kategori: tindakan = perlu dikerjakan, info = hanya informasi */
    public const JENIS = [
        'kontrak_berakhir' => [
            'label' => 'Kontrak segera berakhir',
            'deskripsi' => 'Kontrak sistem aktif yang berakhir dalam 90 hari atau sudah lewat.',
            'kategori' => 'tindakan',
        ],
        'aktivasi_menunggu' => [
            'label' => 'Aktivasi lama menunggu keputusan',
            'deskripsi' => 'Pengajuan yang belum diputuskan pusat lebih dari 14 hari.',
            'kategori' => 'tindakan',
        ],
        'siap_diajukan' => [
            'label' => 'BA siap diajukan aktivasi',
            'deskripsi' => 'BA sudah tersedia tetapi aktivasi belum diajukan.',
            'kategori' => 'tindakan',
        ],
        'ba_belum_ttd' => [
            'label' => 'BA belum ditandatangani',
            'deskripsi' => 'BA sudah dibuat tetapi versi final belum diunggah.',
            'kategori' => 'tindakan',
        ],
        'aktivasi_keputusan' => [
            'label' => 'Keputusan aktivasi',
            'deskripsi' => 'Aktivasi yang disetujui atau ditolak dalam 7 hari terakhir.',
            'kategori' => 'info',
        ],
        'lokasi_kosong' => [
            'label' => 'Lokasi kosong lama',
            'deskripsi' => 'Lokasi yang kosong lebih dari 90 hari sejak terakhir diperbarui.',
            'kategori' => 'info',
        ],
        'login_baru' => [
            'label' => 'Login dari IP baru',
            'deskripsi' => 'Akun Anda dipakai login dari alamat IP yang belum pernah dipakai, dalam 7 hari terakhir.',
            'kategori' => 'tindakan',
        ],
    ];

    public const HARI_KONTRAK = 90;
    public const HARI_MENUNGGU = 14;
    public const HARI_KEPUTUSAN = 7;
    public const HARI_KOSONG = 90;

    private const BOBOT_LEVEL = ['penting' => 0, 'sedang' => 1, 'info' => 2];

    /** Daftar mentah (sama untuk semua user), di-cache sebentar. */
    public function semua(): Collection
    {
        return collect(Cache::remember('notifikasi.semua', 60, fn () => $this->hitung()));
    }

    /** Notifikasi milik satu user: sudah disaring preferensi + status dibaca. */
    public function untuk(User $user): Collection
    {
        $preferensi = $user->preferensiNotifikasi();

        // Notifikasi umum (sama untuk semua user) + notifikasi khusus akun ini.
        $items = $this->semua()
            ->concat($this->loginBaru($user))
            ->filter(fn ($i) => $preferensi[$i['jenis']] ?? true)
            ->take(500)
            ->values();

        $dibaca = NotifikasiDibaca::where('user_id', $user->id)
            ->whereIn('kunci', $items->pluck('kunci')->all())
            ->pluck('kunci')
            ->flip();

        return $items
            ->map(fn ($i) => $i + [
                'dibaca' => isset($dibaca[$i['kunci']]),
                'kategori' => self::JENIS[$i['jenis']]['kategori'],
            ])
            ->sortBy(fn ($i) => [
                $i['dibaca'] ? 1 : 0,
                self::BOBOT_LEVEL[$i['level']] ?? 3,
                -strtotime($i['waktu']),
            ])
            ->values();
    }

    /**
     * Peringatan login dari IP baru (diambil dari audit log) untuk satu user.
     * Tidak ikut cache umum karena hanya relevan bagi pemilik akun.
     */
    private function loginBaru(User $user): Collection
    {
        return AuditLog::where('user_id', $user->id)
            ->where('aksi', 'Login dari IP baru')
            ->where('created_at', '>=', now()->subDays(7))
            ->latest('created_at')
            ->limit(10)
            ->get()
            ->map(fn (AuditLog $log) => [
                'kunci' => 'login-baru:' . $log->id,
                'jenis' => 'login_baru',
                'level' => 'penting',
                'judul' => 'Login dari IP baru terdeteksi',
                'pesan' => 'IP ' . ($log->ip_address ?: '-') . ' pada '
                    . $log->created_at->format('d M Y H:i')
                    . '. Jika bukan Anda, segera ganti password.',
                'url' => route('profile.log'),
                'waktu' => $log->created_at->toDateTimeString(),
            ]);
    }

    /** Untuk dropdown lonceng di header. */
    public function ringkas(User $user, int $batas = 6): array
    {
        $semua = $this->untuk($user);

        return [
            'items' => $semua->take($batas),
            'belum' => $semua->where('dibaca', false)->count(),
        ];
    }

    public function tandaiDibaca(User $user, array $kunci): void
    {
        $kunci = array_values(array_unique($kunci));

        if ($kunci === []) {
            return;
        }

        $baris = array_map(fn ($k) => [
            'user_id' => $user->id,
            'kunci' => $k,
            'dibaca_at' => now(),
        ], $kunci);

        NotifikasiDibaca::upsert($baris, ['user_id', 'kunci'], ['dibaca_at']);
    }

    private function hitung(): array
    {
        $hariIni = now()->startOfDay();
        $items = [];

        $kerjaSamas = KerjaSama::with([
            'tenant', 'lokasi', 'aktivasi', 'kontrakTerbaru',
        ])->get();

        foreach ($kerjaSamas as $ks) {
            $kode = 'KS-' . str_pad((string) $ks->id, 4, '0', STR_PAD_LEFT);
            $nama = $ks->mitra_usaha ?: ($ks->brand ?: $kode);
            $url = route('aktivasi.show', $ks->id);
            $statusAktivasi = $ks->statusAktivasi();
            $aktivasi = $ks->aktivasi;

            // 1. Kontrak aktif yang segera berakhir.
            if ($statusAktivasi === 'Disetujui' && $ks->tanggal_berakhir) {
                $sisa = (int) floor(
                    $hariIni->diffInDays($ks->tanggal_berakhir->copy()->startOfDay(), false)
                );

                if ($sisa <= self::HARI_KONTRAK) {
                    $items[] = [
                        'kunci' => 'kontrak-berakhir:' . $ks->id . ':' . $ks->tanggal_berakhir->format('Ymd'),
                        'jenis' => 'kontrak_berakhir',
                        'level' => $sisa <= 30 ? 'penting' : 'sedang',
                        'judul' => $sisa < 0
                            ? "Kontrak {$nama} sudah berakhir"
                            : ($sisa === 0
                                ? "Kontrak {$nama} berakhir hari ini"
                                : "Kontrak {$nama} berakhir {$sisa} hari lagi"),
                        'pesan' => 'Masa berlaku sampai ' . $ks->tanggal_berakhir->format('d M Y') . " · {$kode}",
                        'url' => $url,
                        'waktu' => $ks->tanggal_berakhir->toDateString(),
                    ];
                }
            }

            // 2. Pengajuan terlalu lama menunggu keputusan pusat.
            if ($aktivasi && $statusAktivasi === 'Menunggu Keputusan') {
                $sejak = $aktivasi->tanggal_pengajuan ?? $aktivasi->created_at;
                $hari = (int) floor($sejak->copy()->startOfDay()->diffInDays($hariIni, false));

                if ($hari >= self::HARI_MENUNGGU) {
                    $items[] = [
                        'kunci' => 'aktivasi-menunggu:' . $aktivasi->id,
                        'jenis' => 'aktivasi_menunggu',
                        'level' => $hari >= 30 ? 'penting' : 'sedang',
                        'judul' => "Pengajuan aktivasi {$nama} belum diputuskan",
                        'pesan' => "Sudah menunggu {$hari} hari sejak " . $sejak->format('d M Y') . " · {$kode}",
                        'url' => $url,
                        'waktu' => $sejak->toDateString(),
                    ];
                }
            }

            // 3. BA tersedia, aktivasi belum diajukan.
            if ($statusAktivasi === 'Belum Diajukan') {
                $items[] = [
                    'kunci' => 'siap-diajukan:' . $ks->id,
                    'jenis' => 'siap_diajukan',
                    'level' => 'sedang',
                    'judul' => "BA {$nama} tersedia, aktivasi belum diajukan",
                    'pesan' => "Pengajuan aktivasi sudah bisa dibuat · {$kode}",
                    'url' => $url,
                    'waktu' => ($ks->berita_acara_final_uploaded_at ?? $ks->updated_at)->toDateString(),
                ];
            }

            // 4. BA belum ditandatangani.
            if ($ks->posisiKontrakFisik() === 'BA Belum TTD') {
                $items[] = [
                    'kunci' => 'ba-belum-ttd:' . $ks->id,
                    'jenis' => 'ba_belum_ttd',
                    'level' => 'sedang',
                    'judul' => "BA {$nama} belum ditandatangani",
                    'pesan' => "Unggah BA final setelah ditandatangani · {$kode}",
                    'url' => $url,
                    'waktu' => $ks->updated_at->toDateString(),
                ];
            }

            // 5. Keputusan aktivasi terbaru.
            if (
                $aktivasi
                && in_array($aktivasi->status, ['Disetujui', 'Ditolak'], true)
                && $aktivasi->tanggal_keputusan
                && $aktivasi->tanggal_keputusan->gte($hariIni->copy()->subDays(self::HARI_KEPUTUSAN))
            ) {
                $disetujui = $aktivasi->status === 'Disetujui';

                $items[] = [
                    'kunci' => 'aktivasi-keputusan:' . $aktivasi->id . ':' . $aktivasi->status,
                    'jenis' => 'aktivasi_keputusan',
                    'level' => $disetujui ? 'info' : 'penting',
                    'judul' => "Aktivasi {$nama} " . ($disetujui ? 'disetujui' : 'ditolak'),
                    'pesan' => 'Keputusan pusat tanggal ' . $aktivasi->tanggal_keputusan->format('d M Y') . " · {$kode}",
                    'url' => $url,
                    'waktu' => $aktivasi->tanggal_keputusan->toDateString(),
                ];
            }
        }

        // 6. Lokasi kosong lama (satu notifikasi gabungan).
        $jumlahKosong = Lokasi::where('status', 'Kosong')
            ->whereNull('tenant_id')
            ->where('updated_at', '<', now()->subDays(self::HARI_KOSONG))
            ->count();

        if ($jumlahKosong > 0) {
            $items[] = [
                'kunci' => 'lokasi-kosong:' . $jumlahKosong,
                'jenis' => 'lokasi_kosong',
                'level' => 'info',
                'judul' => "{$jumlahKosong} lokasi kosong lebih dari " . self::HARI_KOSONG . ' hari',
                'pesan' => 'Pertimbangkan penawaran ke calon tenant.',
                'url' => route('lokasi.index', ['status' => 'Kosong', 'sort' => 'oldest']),
                'waktu' => now()->toDateString(),
            ];
        }

        return $items;
    }
}
