<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Pengajuan Aktivasi</title>

    <style>
        @page {
            size: A4;
            margin: 1.6cm 2cm;
        }

        body {
            font-family: "DejaVu Sans", sans-serif;
            font-size: 10pt;
            margin: 0;
            line-height: 1.35;
            color: #222;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #365F65;
            padding-bottom: 14px;
            margin-bottom: 14px;
        }

        .header h2 {
            color: #365F65;
            margin: 0;
            font-size: 17pt;
        }

        .header p {
            margin: 4px 0 0;
            font-size: 10pt;
        }

        .judul {
            text-align: center;
            font-weight: bold;
            text-decoration: underline;
            margin: 14px 0 4px;
        }

        .nomor {
            text-align: center;
            margin-bottom: 14px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 8px 0;
        }

        td {
            padding: 5px 4px;
            vertical-align: top;
        }

        .label {
            width: 36%;
        }

        .penutup {
            margin-top: 20px;
        }

        .ttd {
            width: 42%;
            margin-left: auto;
            margin-top: 40px;
            text-align: center;
        }

        .ruang-ttd {
            height: 60px;
        }

        .tabel-ttd { margin-top: 18px; }
        .kolom-ttd { width: 50%; text-align: center; }
        .kolom-ttd p { margin: 2px 0; }

        .catatan {
            font-size: 9pt;
            color: #666;
            margin-top: 14px;
            border-top: 1px solid #ccc;
            padding-top: 8px;
        }
    </style>
</head>
<body>

    <div class="header">
        <h2>AEROTRACK</h2>
        <p>Sistem Monitoring Kerja Sama dan Persewaan Tenant</p>
        <p>Bandara Internasional Juanda</p>
    </div>

    <div class="judul">SURAT PENGAJUAN AKTIVASI TENANT</div>

    <div class="nomor">
        Nomor: {{ $aktivasi->nomor_pengajuan }}
    </div>

    <p>
        Kepada Yth.<br>
        Pihak yang Berwenang<br>
        di Tempat
    </p>

    <p>Dengan hormat,</p>

    <p>
        Sehubungan dengan proses kerja sama persewaan tenant di
        Bandara Internasional Juanda, kami mengajukan permohonan
        aktivasi tenant berdasarkan data kerja sama berikut:
    </p>

    <table>
        <tr>
            <td class="label">Mitra Usaha</td>
            <td>: {{ $kerjaSama->mitra_usaha ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Nama Brand</td>
            <td>: {{ $kerjaSama->brand ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Jenis Usaha</td>
            <td>: {{ $kerjaSama->jenis_usaha ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Bentuk Kerja Sama</td>
            <td>: {{ $kerjaSama->bentuk_kerja_sama ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Nama Tenant</td>
            <td>: {{ $tenant->nama_tenant ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Lokasi Tenant</td>
            <td>:
                @if ($lokasi)
                    {{ $lokasi->kode_tampilan ?: '-' }}
                    @if ($lokasi->terminal) — {{ $lokasi->terminal }} @endif
                    @if ($lokasi->lokasi) ({{ $lokasi->lokasi }}) @endif
                @else
                    -
                @endif
            </td>
        </tr>
        <tr>
            <td class="label">Tanggal Pengajuan</td>
            <td>:
                {{ $aktivasi->tanggal_pengajuan
                    ? \Carbon\Carbon::parse($aktivasi->tanggal_pengajuan)->translatedFormat('d F Y')
                    : now()->translatedFormat('d F Y') }}
            </td>
        </tr>
    </table>

    <p>
        Demikian permohonan ini kami sampaikan. Kami berharap proses
        aktivasi dapat ditinjau dan ditindaklanjuti sesuai ketentuan
        yang berlaku. Atas perhatian dan kerja samanya, kami
        mengucapkan terima kasih.
    </p>

    <table class="tabel-ttd">
        <tr>
            <td class="kolom-ttd">
                <p>Menyetujui,</p>
                <p><strong>Commercial Pusat</strong></p>
                <div class="ruang-ttd"></div>
                <p><strong>________________________</strong></p>
                <p>Nama / Jabatan</p>
                <p>Tanggal: ____________________</p>
            </td>
            <td class="kolom-ttd">
                <p>Hormat kami,</p>
                <p><strong>Commercial</strong></p>
                <div class="ruang-ttd">
                    @if (!empty($ttdDataUri))
                        <img src="{{ $ttdDataUri }}" style="max-height: 60px; max-width: 180px;">
                    @endif
                </div>
                <p><strong><u>{{ $aktivasi->nama_penandatangan ?: '________________________' }}</u></strong></p>
                <p>{{ $aktivasi->jabatan_penandatangan ?: 'Penanggung Jawab' }}</p>
            </td>
        </tr>
    </table>

    <div class="catatan">
        Dokumen ini merupakan draf pengajuan yang dihasilkan oleh
        AeroTrack. Format, tujuan surat, kop, dan penandatangan harus
        disesuaikan dengan ketentuan resmi instansi sebelum digunakan.
    </div>

</body>
</html>