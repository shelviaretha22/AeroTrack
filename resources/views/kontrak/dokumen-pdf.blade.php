<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Perjanjian Pokok</title>
    <style>
        @page { margin: 14mm 20mm 14mm 20mm; }
        body { background: #fff; color: #1a1a1a;
            font-family: "Times New Roman", "DejaVu Serif", serif; font-size: 10.5pt; line-height: 1.4; }

        .preview-document-page { page-break-after: always; }
        .preview-document-page:last-child { page-break-after: auto; }

        .preview-paper-header { margin-bottom: 14px; }
        .preview-paper-logos { width: 100%; height: 62px; margin-bottom: 6px; }
        .preview-paper-logo-injourney { float: left; width: 120px; height: 60px; }
        .preview-paper-logo-mitra { float: right; width: 120px; height: 60px; text-align: right; }
        .preview-paper-logo-injourney img, .preview-paper-logo-mitra img { max-height: 58px; max-width: 120px; }
        .preview-paper-logo-mitra span { font-family: Arial, sans-serif; font-size: 8pt; color: #999; }
        .preview-paper-logos:after { content: ""; display: block; clear: both; }

        .preview-page1-heading { text-align: center; margin: 12px 0 8px; clear: both; }
        .preview-page1-heading h2 { margin: 0 0 4px; font-size: 14pt; text-decoration: underline; letter-spacing: .5px; }
        .preview-page1-heading p { text-align: center; margin: 0 0 2px; font-weight: bold; font-size: 11pt; line-height: 1.35; }

        table { border-collapse: collapse; }
        .preview-paper-number-table { width: 80%; margin: 10px auto 14px; font-size: 10.5pt; }
        .preview-paper-number-table td { padding: 2px 4px; vertical-align: top; }
        .preview-paper-number-table td:first-child { width: 175px; white-space: nowrap; }
        .preview-paper-number-table td:nth-child(2) { width: 12px; }

        .preview-paper-date-info { margin: 0 0 8px; }
        .preview-paper-date-info p { margin: 0 0 3px; text-align: left; }
        .preview-paper-date-table td { padding: 1px 6px 1px 0; vertical-align: top; }
        .preview-paper-date-table td:first-child { width: 55px; }
        .preview-paper-date-table td:nth-child(2) { width: 12px; }

        p { margin: 0 0 9px; text-align: justify; }
        h4 { margin: 14px 0 4px; text-align: center; font-size: 11.5pt; text-transform: uppercase; line-height: 1.3; }
        h4 + h4 { margin-top: 0; }

        .preview-page1-parties { line-height: 1.35; }
        .preview-page1-intro { margin: 0 0 4px; text-align: left; }
        .preview-party-section { margin: 0 0 10px; }
        .preview-party-name { margin: 0 0 3px; text-align: left; }
        .preview-party-row { display: table; width: 100%; margin: 0 0 1px; }
        .preview-party-row span { display: table-cell; vertical-align: top; }
        .preview-party-row span:nth-child(1) { width: 200px; }
        .preview-party-row span:nth-child(2) { width: 14px; }
        .preview-party-description { margin: 4px 0 0; text-align: justify; }
        .preview-page1-closing { margin: 6px 0 0; text-align: justify; }

        .preview-paper-list { margin: 0 0 9px 0; padding-left: 22px; }
        .preview-paper-list li { margin-bottom: 3px; }

        .preview-paper-signature { display: table; width: 100%; margin-top: 30px; text-align: center; }
        .preview-paper-signature > div { display: table-cell; width: 50%; vertical-align: top; }
        .signature-space { height: 80px; }
        .signature-name { font-weight: bold; text-decoration: underline; }

        .preview-paper-footer { margin-top: 24px; padding-top: 6px; border-top: 1px solid #ccc;
            text-align: center; font-size: 8.5pt; color: #666; }
    </style>
</head>
<body>
@php
    /*
    |--------------------------------------------------------------------------
    | 1. MODE HALAMAN
    |--------------------------------------------------------------------------
    */

    $isPdf = $isPdf ?? false;
    $signatureData = $signatureData ?? null;
    $isPreview = $isPreview ?? false;


    /*
    |--------------------------------------------------------------------------
    | 2. DATA RELASI
    |--------------------------------------------------------------------------
    */

    $kerjaSama = $kerjaSama ?? $kontrak?->kerjaSama;

    $tenant = $tenant
        ?? $kontrak?->tenant
        ?? $kerjaSama?->tenant;

    $lokasi = $lokasi
        ?? $kontrak?->lokasi
        ?? $kerjaSama?->lokasi;


    /*
    |--------------------------------------------------------------------------
    | 3. IDENTITAS KONTRAK
    |--------------------------------------------------------------------------
    */

    $nomorKontrak = $kontrak?->nomor_kontrak
        ?? '[Nomor kontrak belum tersedia]';

    $nomorKontrakMitra = $kontrak?->nomor_kontrak_mitra ?? '';

    $nomorKerjaSama = $kerjaSama?->nomor_kerja_sama
        ?: (
            $kerjaSama
                ? 'KS-' . str_pad($kerjaSama->id, 5, '0', STR_PAD_LEFT)
                : '-'
        );


    /*
    |--------------------------------------------------------------------------
    | 4. INFORMASI MITRA
    |--------------------------------------------------------------------------
    */

    $mitra = $kerjaSama?->mitra_usaha
        ?? $tenant?->mitra_usaha
        ?? '-';

    $brand = $kerjaSama?->brand
        ?? $tenant?->nama_tenant
        ?? '-';

    $jenisUsaha = $kerjaSama?->jenis_usaha
        ?? $tenant?->jenis_usaha
        ?? '-';

    $npwp = $tenant?->npwp ?? '-';

    $nib = $tenant?->nib ?? '-';

    $kodeTenant = $tenant?->id ?? '-';


    /*
    |--------------------------------------------------------------------------
    | 5. INFORMASI PIC KOMERSIL
    |--------------------------------------------------------------------------
    */

    // PIC dan penandatangan MITRA diambil dari data Tenant (pihak mitra),
    // bukan dari PIC Commercial yang merupakan pegawai AP Indonesia.
    $picMitraNamaDefault = $tenant?->nama_pic ?? '';
    $picMitraJabatanDefault = $tenant?->jabatan_pic ?? '';

    // Data kontrak lama bisa berisi PIC Commercial AP Indonesia yang dulu
    // terisi otomatis di kolom mitra. Nilai seperti itu diabaikan.
    $abaikanBilaMilikAp = function ($nilai, $milikAp) {
        return ($nilai && $nilai !== $milikAp) ? $nilai : '';
    };

    $namaPicCommercial = $kerjaSama?->pic_commercial
        ?? $kontrak?->nama_commercial
        ?? '[Nama PIC Komersil]';

    $jabatanPicCommercial = $kerjaSama?->jabatan_pic_commercial
        ?? $kontrak?->jabatan_commercial
        ?? '[Jabatan PIC Komersil]';


    /*
    |--------------------------------------------------------------------------
    | 6. INFORMASI LOKASI
    |--------------------------------------------------------------------------
    */

    $kodeRuang = $lokasi?->kode_tampilan
        ?: ($lokasi?->kode_ruang ?? '-');

    $namaLokasi = $lokasi?->lokasi ?? '-';

    $terminal = $lokasi?->terminal ?? '-';

    $lantai = $lokasi?->lantai ?? '-';


    /*
    |--------------------------------------------------------------------------
    | 7. LUAS FASILITAS
    |--------------------------------------------------------------------------
    |
    | Nilai awal diambil dari snapshot kontrak.
    | Jika belum tersedia, gunakan data dari Lokasi.
    |
    | Pengguna dapat mengubah nilai melalui form.
    | Nilai hasil perubahan disimpan sebagai snapshot kontrak.
    |
    */

    $luasFasilitas = old(
        'luas_fasilitas_snapshot',
        data_get($kontrak?->data_lokasi_snapshot, 'luas_area')
            ?? $lokasi?->luas_area
            ?? $lokasi?->luas
            ?? $lokasi?->luas_fasilitas
            ?? ''
    );


    /*
    |--------------------------------------------------------------------------
    | 8. TANGGAL DAN DURASI KERJA SAMA
    |--------------------------------------------------------------------------
    */

    $tanggalMulai = $kerjaSama?->tanggal_mulai
        ? \Illuminate\Support\Carbon::parse($kerjaSama->tanggal_mulai)
            ->locale('id')
            ->translatedFormat('d F Y')
        : '[Tanggal mulai kerja sama]';

    $tanggalBerakhir = $kerjaSama?->tanggal_berakhir
        ? \Illuminate\Support\Carbon::parse($kerjaSama->tanggal_berakhir)
            ->locale('id')
            ->translatedFormat('d F Y')
        : '[Tanggal berakhir kerja sama]';

    $tanggalHariIni = now()
        ->locale('id')
        ->translatedFormat('l, d F Y');

    $durasiKerjaSama = $kerjaSama?->tanggal_mulai
        && $kerjaSama?->tanggal_berakhir
        ? (
            \Illuminate\Support\Carbon::parse($kerjaSama->tanggal_mulai)
                ->diffInDays(
                    \Illuminate\Support\Carbon::parse(
                        $kerjaSama->tanggal_berakhir
                    )
                ) . ' hari'
        )
        : '[Durasi sewa belum tersedia]';


    /*
    |--------------------------------------------------------------------------
    | 9. DATA TAMBAHAN KONTRAK
    |--------------------------------------------------------------------------
    */

    $logoMitraPath = $kontrak?->logo_mitra_path ?? null;

    $periodeEvaluasi = $kontrak?->periode_evaluasi_tarif ?? 1;

    $satuanEvaluasi = $kontrak?->satuan_evaluasi_tarif ?? 'tahun';

    $klausulKepemilikan =
        $kontrak?->klausul_kepemilikan_fasilitas ?? '';

    $klausulTambahan = $kontrak?->klausul_tambahan ?? '';


    /*
    |--------------------------------------------------------------------------
    | 10. DATA PENANDATANGAN DAN KONTAK MITRA
    |--------------------------------------------------------------------------
    |
    | Data kontak otomatis mengambil informasi dari Tenant.
    |
    | Jika snapshot kontrak sudah tersedia, gunakan snapshot tersebut.
    | Jika belum tersedia, gunakan data master Tenant.
    |
    | old() digunakan agar input tidak hilang ketika validasi gagal.
    |
    */

    $namaPenandatanganMitra = old(
        'nama_penandatangan_mitra',
        $abaikanBilaMilikAp($kontrak?->nama_penandatangan_mitra, $kerjaSama?->pic_commercial) ?: $picMitraNamaDefault
    );

    $jabatanPenandatanganMitra = old(
        'jabatan_penandatangan_mitra',
        $abaikanBilaMilikAp($kontrak?->jabatan_penandatangan_mitra, $kerjaSama?->jabatan_pic_commercial) ?: $picMitraJabatanDefault
    );

    $alamatMitra = old(
        'alamat_mitra_snapshot',
        $kontrak?->alamat_mitra_snapshot
            ?: ($tenant?->alamat_perusahaan ?? '')
    );

    $domisiliMitra = old(
    'domisili_mitra_snapshot',
    $kontrak?->domisili_mitra_snapshot ?? ''
);

    $teleponMitra = old(
        'telepon_mitra_snapshot',
        $kontrak?->telepon_mitra_snapshot
            ?: ($tenant?->no_hp_pic ?? '')
    );

    $faxMitra = old(
        'fax_mitra_snapshot',
        $kontrak?->fax_mitra_snapshot ?? ''
    );

    $emailMitra = old(
        'email_mitra_snapshot',
        $kontrak?->email_mitra_snapshot
            ?: ($tenant?->email_pic ?? '')
    );


    /*
    |--------------------------------------------------------------------------
    | 11. DATA LAMPIRAN
    |--------------------------------------------------------------------------
    */

    $lampiranTerpilih = $kontrak?->lampiran_perjanjian ?? [];

    if (!is_array($lampiranTerpilih)) {
        $lampiranTerpilih = [];
    }

    /*
     * Mengambil nama dokumen tambahan yang sudah tersimpan.
     *
     * Format penyimpanan:
     * Lainnya: Nama Dokumen
     */

    $namaDokumenLainnya = '';

    foreach ($lampiranTerpilih as $lampiran) {

        if (
            is_string($lampiran)
            && str_starts_with($lampiran, 'Lainnya: ')
        ) {
            $namaDokumenLainnya = substr(
                $lampiran,
                strlen('Lainnya: ')
            );

            break;
        }

    }

    $namaDokumenLainnya = old(
        'nama_dokumen_lainnya',
        $namaDokumenLainnya
    );

    /*
     * Mengubah nilai lampiran khusus menjadi Lainnya
     * agar checkbox dapat dikenali sebagai pilihan aktif.
     */

    $lampiranTerpilih = array_map(
        function ($lampiran) {

            if (
                is_string($lampiran)
                && str_starts_with($lampiran, 'Lainnya: ')
            ) {
                return 'Lainnya';
            }

            return $lampiran;

        },
        $lampiranTerpilih
    );

    /*
     * SUP dan BAK otomatis terpilih.
     */

    $lampiranInput = old(
        'lampiran_perjanjian',
        array_values(
            array_unique(
                array_merge(
                    ['SUP', 'BAK'],
                    $lampiranTerpilih
                )
            )
        )
    );

    $daftarLampiran = [
        'SUP' => 'Syarat-Syarat Umum Perjanjian (SUP)',
        'BAK' => 'Berita Acara Kesepakatan (BAK)',
        'Denah Fasilitas Komersial' => 'Denah Fasilitas Komersial',
        'Perjanjian Tingkat Layanan' => 'Perjanjian Tingkat Layanan',
        'Lainnya' => 'Dokumen Lainnya',
    ];


    /*
    |--------------------------------------------------------------------------
    | 12. KLAUSUL KEPEMILIKAN
    |--------------------------------------------------------------------------
    */

    if ($klausulKepemilikan === 'mitra') {

        $klausulKepemilikanText =
            'Fasilitas usaha yang dibangun tetap menjadi milik Mitra, '
            . 'dan fasilitas komersial dikembalikan sesuai ketentuan.';

    } elseif ($klausulKepemilikan === 'ap_indonesia') {

        $klausulKepemilikanText =
            'Fasilitas usaha yang dibangun menjadi milik AP Indonesia '
            . 'tanpa kompensasi.';

    } else {

        $klausulKepemilikanText =
            '[Klausul kepemilikan belum dipilih]';

    }


    /*
    |--------------------------------------------------------------------------
    | 13. DATA AP INDONESIA
    |--------------------------------------------------------------------------
    |
    | Identitas resmi AP Indonesia tetap mengikuti data yang
    | ditentukan melalui source code sistem.
    |
    | Tidak menggunakan data perkiraan untuk dokumen resmi.
    |
    */


@endphp

@php
    // Gambar dimuat sebagai data URI karena DomPDF tidak dapat
    // memuat gambar melalui URL halaman web.
    $logoApSrc = $logoApSrc ?? '';
    $logoMitraSrc = $logoMitraSrc ?? '';
@endphp

{{-- HALAMAN 1 --}}
                <section class="preview-document-page">

                    <div class="preview-paper-header">

                        <div class="preview-paper-logos">

            {{-- LOGO INJOURNEY AIRPORTS --}}
            <div class="preview-paper-logo-injourney">
                <img
                    src="{{ $logoApSrc }}"
                    alt="Logo InJourney Airports"
                >
            </div>

            {{-- LOGO MITRA --}}
            <div class="preview-paper-logo preview-paper-logo-mitra">

                @if(!empty($logoMitraSrc))
                    <img
                        src="{{ $logoMitraSrc }}"
                        alt="Logo Mitra"
                    >
                @else
                    <span>Logo Mitra</span>
                @endif

            </div>

                </div>
                        {{-- JUDUL PERJANJIAN --}}
<div class="preview-page1-heading">

    <h2>PERJANJIAN POKOK</h2>

    <p>TENTANG</p>

    <p>
        KERJA SAMA PEMANFAATAN FASILITAS KOMERSIAL
        PT ANGKASA PURA INDONESIA
    </p>

    <p>
        UNTUK KEGIATAN USAHA {{ $jenisUsaha }}
    </p>

    <p>
        DI {{ $namaLokasi }}
    </p>

</div>

                    {{-- NOMOR PERJANJIAN --}}
<table class="preview-paper-number-table">
    <tbody>
        <tr>
            <td>Nomor Dokumen Kontrak</td>
            <td>:</td>
            <td>{{ $nomorKontrak }}</td>
        </tr>
        <tr>
            <td>Nomor Kontrak Mitra</td>
            <td>:</td>
            <td>
                {{ $nomorKontrakMitra ?: '-' }}
            </td>
        </tr>
    </tbody>
</table>
                   {{-- KETERANGAN HARI DAN TANGGAL PERJANJIAN --}}
<div class="preview-paper-date-info">

    <p>
        Perjanjian Pokok ini dibuat dan ditandatangani pada:
    </p>

    <table class="preview-paper-date-table">
        <tbody>
            <tr>
                <td>Hari</td>
                <td>:</td>
                <td>
                    {{ \Illuminate\Support\Carbon::parse($kontrak?->tanggal_dokumen ?? now())->locale('id')->translatedFormat('l') }}
                </td>
            </tr>
            <tr>
                <td>Tanggal</td>
                <td>:</td>
                <td>
                    {{ \Illuminate\Support\Carbon::parse($kontrak?->tanggal_dokumen ?? now())->locale('id')->translatedFormat('d F Y') }}
                </td>
            </tr>
        </tbody>
    </table>

</div>
                    

{{-- IDENTITAS PARA PIHAK --}}
<div class="preview-page1-parties">

    <p class="preview-page1-intro">Oleh dan antara:</p>

    {{-- PIHAK PERTAMA: AP INDONESIA --}}
    <div class="preview-party-section">

        <p class="preview-party-name">
            <strong>I. {{ $apIndonesia['nama'] }}</strong>
        </p>

        <div class="preview-party-details">

            <div class="preview-party-row">
                <span>Berkedudukan di</span>
                <span>:</span>
                <span>{{ $apIndonesia['domisili'] ?: '-' }}</span>
            </div>

            <div class="preview-party-row">
                <span>Alamat</span>
                <span>:</span>
                <span>{{ $apIndonesia['alamat'] ?: '-' }}</span>
            </div>

            <div class="preview-party-row">
                <span>Dalam hal ini diwakili oleh</span>
                <span>:</span>
                <span>{{ $apIndonesia['penandatangan'] ?: '-' }}</span>
            </div>

            <div class="preview-party-row">
                <span>Selaku</span>
                <span>:</span>
                <span>{{ $apIndonesia['jabatan'] ?: '-' }}</span>
            </div>

        </div>

        <p class="preview-party-description">
            Dengan demikian sah bertindak untuk dan atas nama
            PT Angkasa Pura Indonesia, selanjutnya dalam Perjanjian
            Pokok ini disebut AP INDONESIA.
        </p>

    </div>


    {{-- PIHAK KEDUA: MITRA --}}
    <div class="preview-party-section">

        <p class="preview-party-name">
            <strong>II. {{ $mitra }}</strong>
        </p>

        <div class="preview-party-details">

            <div class="preview-party-row">
    <span>Berkedudukan di</span>
    <span>:</span>
    <span>
        {{ $domisiliMitra ?: '-' }}
    </span>
</div>

            <div class="preview-party-row">
                <span>Alamat</span>
                <span>:</span>
                <span>{{ $alamatMitra ?: '-' }}</span>
            </div>

            <div class="preview-party-row">
                <span>Dalam hal ini diwakili oleh</span>
                <span>:</span>
                <span>
                    {{ $namaPenandatanganMitra ?: '-' }}
                </span>
            </div>

            <div class="preview-party-row">
                <span>Selaku</span>
                <span>:</span>
                <span>
                    {{ $jabatanPenandatanganMitra ?: '-' }}
                </span>
            </div>

        </div>

        <p class="preview-party-description">
            Dengan demikian sah bertindak untuk dan atas nama
            {{ $mitra }}, selanjutnya dalam Perjanjian Pokok ini
            disebut MITRA.
        </p>

    </div>


    <p class="preview-page1-closing">
        Selanjutnya, AP INDONESIA dan MITRA secara bersama-sama
        disebut “PARA PIHAK”.
    </p>

</div>

<div class="preview-paper-footer">
    Halaman 1 dari 4
</div>

                </section>


                {{-- HALAMAN 2 --}}
                <section
                    class="preview-document-page"
                >

                    <h4>Pasal 2</h4>
                    <h4>Lokasi dan Fasilitas</h4>

                    <p>
                        Pelaksanaan kegiatan usaha dilakukan pada lokasi
                        fasilitas komersial yang telah disepakati oleh PARA PIHAK.
                    </p>

                    <p>
                        Lokasi fasilitas:
                        <strong>{{ $namaLokasi }}</strong>,
                        Terminal {{ $terminal }},
                        Lantai {{ $lantai }},
                        Kode Ruang {{ $kodeRuang }}.
                    </p>

                    <p>
                        Luas fasilitas:
                        <strong>{{ $luasFasilitas ?: '-' }}</strong>.
                    </p>


                    <h4>Pasal 3</h4>
                    <h4>Jangka Waktu Perjanjian</h4>

                    <p>
                        Jangka waktu kerja sama mengikuti periode yang telah
                        ditetapkan dalam data kerja sama dan dokumen perjanjian
                        yang disepakati oleh PARA PIHAK.
                    </p>

                    <p>
                        Tanggal mulai:
                        <strong>{{ $tanggalMulai }}</strong>.
                    </p>

                    <p>
                        Tanggal berakhir:
                        <strong>{{ $tanggalBerakhir }}</strong>.
                    </p>


                    <h4>Pasal 4</h4>
                    <h4>Evaluasi Tarif</h4>

                    <p>
                        Evaluasi tarif dilakukan setiap
                        <strong>
                            {{ $periodeEvaluasi }}
                        </strong>
                        <strong>
                            {{ $satuanEvaluasi }}
                        </strong>
                        sesuai ketentuan dan kesepakatan PARA PIHAK.
                    </p>


                    <div class="preview-paper-footer">
                        Halaman 2 dari 4
                    </div>

                </section>

                {{-- HALAMAN 3 --}}
                <section
                    class="preview-document-page"
                >

                    <h4>Pasal 5</h4>
                    <h4>Hak dan Kewajiban Para Pihak</h4>

                    <p>
                        PARA PIHAK melaksanakan hak dan kewajiban masing-masing
                        berdasarkan ketentuan yang disepakati dalam perjanjian
                        dan dokumen lampiran yang menjadi bagian dari perjanjian.
                    </p>


                    <h4>Pasal 6</h4>
                    <h4>Kepemilikan Fasilitas</h4>

                    <p>
                        Ketentuan kepemilikan fasilitas yang digunakan dalam
                        pelaksanaan kerja sama mengikuti pilihan klausul yang
                        ditetapkan dalam pengisian data kontrak.
                    </p>

                    <p>
                        Pilihan kepemilikan fasilitas:
                        <strong>
                            {{ $klausulKepemilikanText ?: '[Belum dipilih]' }}
                        </strong>.
                    </p>


                    <h4>Pasal 7</h4>
                    <h4>Ketentuan Penutup</h4>

                    <p>
                        Hal-hal yang belum diatur dalam perjanjian ini akan
                        mengikuti ketentuan dalam dokumen pendukung dan
                        kesepakatan tertulis PARA PIHAK.
                    </p>

                    <p>
                        Dokumen lampiran yang dipilih sebagai bagian dari
                        perjanjian:
                    </p>

                    <ul class="preview-paper-list">

                        @forelse($lampiranTerpilih as $lampiran)
                            <li>{{ $daftarLampiran[$lampiran] ?? $lampiran }}</li>
                        @empty
                            <li>[Lampiran belum dipilih]</li>
                        @endforelse

                    </ul>


                    <div class="preview-paper-footer">
                        Halaman 3 dari 4
                    </div>

                </section>


                {{-- HALAMAN 4 --}}
                <section
                    class="preview-document-page"
                >

                    <h4>PENUTUP</h4>

                    <p>
                        Demikian dokumen perjanjian ini dibuat untuk menjadi
                        bahan pemeriksaan dan penyelesaian dokumen kontrak
                        oleh PARA PIHAK.
                    </p>

                    <p>
                        Bagian penandatanganan dan ketentuan final mengikuti
                        dokumen resmi yang telah disetujui oleh pihak berwenang.
                    </p>


                    <div class="preview-paper-signature">

                        <div>
                            <strong>PIHAK PERTAMA</strong>

                            <div class="signature-space"></div>

                            <div class="signature-name">
                                {{ $apIndonesia['penandatangan'] ?: '[Nama Penandatangan AP Indonesia]' }}
                            </div>

                            <div>
                                {{ $apIndonesia['jabatan'] ?: '[Jabatan Penandatangan AP Indonesia]' }}
                            </div>

                            <div>
                                {{ $apIndonesia['nama'] ?: 'PT ANGKASA PURA INDONESIA' }}
                            </div>
                        </div>


                        <div>
                            <strong>PIHAK KEDUA</strong>

                            <div class="signature-space"></div>

                            <div
                                class="signature-name"
                            >
                                {{ $namaPenandatanganMitra ?: '[Nama Penandatangan Mitra]' }}
                            </div>

                            <div
                            >
                                {{ $jabatanPenandatanganMitra ?: '[Jabatan Penandatangan]' }}
                            </div>

                            <div>
                                {{ $mitra }}
                            </div>
                        </div>

                    </div>


                    <div class="preview-paper-footer">
                        Halaman 4 dari 4
                    </div>

                </section>
</body>
</html>
