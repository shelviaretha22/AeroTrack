```blade
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Berita Acara Kerja Sama</title>

    <style>
        @page {
            size: A4 portrait;
            margin: 2.2cm 2.3cm;
        }

        body {
            font-family: "DejaVu Sans", sans-serif;
            font-size: 10pt;
            line-height: 1.6;
            color: #222;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #365F65;
            padding-bottom: 12px;
            margin-bottom: 22px;
        }

        .header h1 {
            font-size: 15pt;
            color: #365F65;
            margin: 0 0 5px;
        }

        .header p {
            font-size: 9pt;
            margin: 0;
            text-align: center;
        }

        .document-number {
            text-align: center;
            margin: 18px 0;
        }

        .document-number h2 {
            font-size: 12pt;
            margin-bottom: 4px;
        }

        p {
            text-align: justify;
        }

        .section-title {
            font-weight: bold;
            color: #365F65;
            margin-top: 18px;
            margin-bottom: 7px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 8px 0 14px;
        }

        td, th {
            border: 1px solid #b8c6c7;
            padding: 7px;
            vertical-align: top;
        }

        th {
            background-color: #eaf0f0;
            text-align: left;
        }

        .label {
            width: 32%;
        }

        .separator {
            width: 3%;
            text-align: center;
        }

        .signatures {
            width: 100%;
            margin-top: 35px;
            border: none;
        }

        .signatures td {
            width: 50%;
            height: 125px;
            text-align: center;
            border: none;
            vertical-align: top;
        }

        .signatures p {
            text-align: center;
            margin: 0;
        }

        .signature-space {
            height: 65px;
        }

        .footer {
            margin-top: 20px;
            font-size: 8pt;
            text-align: center;
            color: #666;
        }
    </style>
</head>

<body>
    @php
        $nomorDokumen = $kerjaSama->nomor_kerja_sama
            ?: 'KS-' . str_pad($kerjaSama->id, 3, '0', STR_PAD_LEFT);

        $tanggal = isset($tanggalCetak)
            ? \Carbon\Carbon::parse($tanggalCetak)->translatedFormat('d F Y')
            : now()->translatedFormat('d F Y');

        $formatTanggal = function ($value) {
            return $value
                ? \Carbon\Carbon::parse($value)->translatedFormat('d F Y')
                : '-';
        };

        $formatNilai = function ($value) {
            return $value !== null && $value !== ''
                ? $value
                : '-';
        };
    @endphp

    <div class="header">
        <h1>BERITA ACARA KERJA SAMA</h1>
        <p>Dokumen Administrasi Commercial Management</p>
        <p>AeroTrack — Airport Tenant System</p>
    </div>

    <div class="document-number">
        <h2>BERITA ACARA</h2>
        <p>Nomor: BA/{{ $nomorDokumen }}</p>
    </div>

    <p>
        Pada hari ini, tanggal {{ $tanggal }}, dibuat Berita Acara
        terkait kerja sama antara pihak pengelola dan mitra usaha
        berdasarkan data administrasi yang tercatat dalam sistem AeroTrack,
        dengan rincian sebagai berikut.
    </p>

    <div class="section-title">I. DATA MITRA USAHA</div>

    <table>
        <tr>
            <td class="label">Nama Tenant</td>
            <td class="separator">:</td>
            <td>{{ $formatNilai($tenant->nama_tenant ?? null) }}</td>
        </tr>
        <tr>
            <td>Nama Mitra Usaha</td>
            <td class="separator">:</td>
            <td>{{ $formatNilai($tenant->mitra_usaha ?? $kerjaSama->mitra_usaha) }}</td>
        </tr>
        <tr>
            <td>Brand</td>
            <td class="separator">:</td>
            <td>{{ $formatNilai($kerjaSama->brand) }}</td>
        </tr>
        <tr>
            <td>Jenis Usaha</td>
            <td class="separator">:</td>
            <td>{{ $formatNilai($kerjaSama->jenis_usaha) }}</td>
        </tr>
        <tr>
            <td>Bentuk Badan Usaha</td>
            <td class="separator">:</td>
            <td>{{ $formatNilai($tenant->bentuk_badan_usaha ?? null) }}</td>
        </tr>
        <tr>
            <td>Alamat Perusahaan</td>
            <td class="separator">:</td>
            <td>{{ $formatNilai($tenant->alamat_perusahaan ?? null) }}</td>
        </tr>
        <tr>
            <td>NPWP</td>
            <td class="separator">:</td>
            <td>{{ $formatNilai($tenant->npwp ?? null) }}</td>
        </tr>
        <tr>
            <td>NIB</td>
            <td class="separator">:</td>
            <td>{{ $formatNilai($tenant->nib ?? null) }}</td>
        </tr>
    </table>

    <div class="section-title">II. DATA PENANGGUNG JAWAB MITRA</div>

    <table>
        <tr>
            <td class="label">Nama PIC</td>
            <td class="separator">:</td>
            <td>{{ $formatNilai($tenant->nama_pic ?? null) }}</td>
        </tr>
        <tr>
            <td>Jabatan</td>
            <td class="separator">:</td>
            <td>{{ $formatNilai($tenant->jabatan_pic ?? null) }}</td>
        </tr>
        <tr>
            <td>Nomor Telepon</td>
            <td class="separator">:</td>
            <td>{{ $formatNilai($tenant->no_hp_pic ?? null) }}</td>
        </tr>
        <tr>
            <td>Email</td>
            <td class="separator">:</td>
            <td>{{ $formatNilai($tenant->email_pic ?? null) }}</td>
        </tr>
    </table>

    <div class="section-title">III. RINCIAN KERJA SAMA</div>

    <table>
        <tr>
            <td class="label">Nomor Kerja Sama</td>
            <td class="separator">:</td>
            <td>{{ $nomorDokumen }}</td>
        </tr>
        <tr>
            <td>Bentuk Kerja Sama</td>
            <td class="separator">:</td>
            <td>{{ $formatNilai($kerjaSama->bentuk_kerja_sama) }}</td>
        </tr>
        <tr>
            <td>Tanggal Mulai</td>
            <td class="separator">:</td>
            <td>{{ $formatTanggal($kerjaSama->tanggal_mulai) }}</td>
        </tr>
        <tr>
            <td>Tanggal Berakhir</td>
            <td class="separator">:</td>
            <td>{{ $formatTanggal($kerjaSama->tanggal_berakhir) }}</td>
        </tr>
        <tr>
            <td>PIC Commercial</td>
            <td class="separator">:</td>
            <td>{{ $formatNilai($kerjaSama->pic_commercial) }}</td>
        </tr>
        <tr>
            <td>Jabatan PIC Commercial</td>
            <td class="separator">:</td>
            <td>{{ $formatNilai($kerjaSama->jabatan_pic_commercial ?? null) }}</td>
        </tr>
        <tr>
            <td>Catatan</td>
            <td class="separator">:</td>
            <td>{{ $formatNilai($kerjaSama->catatan) }}</td>
        </tr>
    </table>

    <div class="section-title">IV. DATA LOKASI USAHA</div>

    <table>
        <tr>
            <td class="label">Terminal</td>
            <td class="separator">:</td>
            <td>{{ $formatNilai($lokasi->terminal ?? null) }}</td>
        </tr>
        <tr>
            <td>Lantai</td>
            <td class="separator">:</td>
            <td>{{ $formatNilai($lokasi->lantai ?? null) }}</td>
        </tr>
        <tr>
            <td>Nomor RO</td>
            <td class="separator">:</td>
            <td>{{ $formatNilai($lokasi->nomor_ro ?? null) }}</td>
        </tr>
        <tr>
            <td>Kode Ruang</td>
            <td class="separator">:</td>
            <td>{{ $formatNilai($lokasi->kode_ruang ?? null) }}</td>
        </tr>
        <tr>
            <td>Jenis Ruangan</td>
            <td class="separator">:</td>
            <td>{{ $formatNilai($lokasi->jenis_ruangan ?? null) }}</td>
        </tr>
        <tr>
            <td>Keterangan Lokasi</td>
            <td class="separator">:</td>
            <td>{{ $formatNilai($lokasi->lokasi ?? null) }}</td>
        </tr>
        <tr>
            <td>Luas Area</td>
            <td class="separator">:</td>
            <td>
                {{ $lokasi->luas_area !== null
                    ? number_format((float) $lokasi->luas_area, 2, ',', '.') . ' m²'
                    : '-' }}
            </td>
        </tr>
    </table>

    <div class="section-title">V. PENUTUP</div>

    <p>
        Demikian Berita Acara ini dibuat berdasarkan data administrasi
        kerja sama yang tercatat dalam sistem untuk diperiksa dan
        ditindaklanjuti oleh pihak-pihak terkait. Dokumen ini perlu
        diverifikasi dan ditandatangani oleh pihak yang berwenang
        sebelum digunakan sebagai dokumen resmi.
    </p>

    <table class="signatures">
        <tr>
            <td>
                <strong>PIHAK PERTAMA</strong>
                <div class="signature-space"></div>
                <strong>
                    {{ $formatNilai($kerjaSama->pic_commercial) }}
                </strong>
                <br>
                {{ $formatNilai($kerjaSama->jabatan_pic_commercial ?? null) }}
            </td>
            <td>
                <strong>PIHAK KEDUA</strong>
                <div class="signature-space"></div>
                <strong>
                    {{ $formatNilai($tenant->nama_pic ?? null) }}
                </strong>
                <br>
                {{ $formatNilai($tenant->jabatan_pic ?? null) }}
            </td>
        </tr>
    </table>

    <div class="footer">
        Dokumen dibuat melalui AeroTrack — Airport Tenant System.
    </div>
</body>
</html>