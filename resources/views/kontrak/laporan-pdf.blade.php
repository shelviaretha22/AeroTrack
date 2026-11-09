
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">

    <title>Laporan Kontrak AeroTrack</title>

    <style>
        @page {
            size: A4 landscape;
            margin: 25px 25px;
        }

        body {
            font-family: Arial, sans-serif;
            color: #365F65;
            font-size: 9px;
        }

        .header {
            border-bottom: 2px solid #365F65;
            padding-bottom: 12px;
            margin-bottom: 18px;
        }

        .header h1 {
            margin: 0;
            font-size: 18px;
            color: #365F65;
        }

        .header p {
            margin: 5px 0 0;
            color: #78918F;
            font-size: 10px;
        }

        .info {
            margin-bottom: 15px;
            color: #78918F;
            font-size: 9px;
        }

        .summary {
            width: 100%;
            margin-bottom: 18px;
            border-collapse: collapse;
        }

        .summary td {
            width: 25%;
            padding: 10px;
            border: 1px solid #DCE6E4;
            background: #F5F8F7;
        }

        .summary-label {
            font-size: 9px;
            color: #78918F;
        }

        .summary-value {
            margin-top: 5px;
            font-size: 17px;
            font-weight: bold;
            color: #365F65;
        }

        .section-title {
            margin: 15px 0 8px;
            font-size: 11px;
            font-weight: bold;
            color: #365F65;
        }

        table.data {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        table.data th {
            padding: 7px 4px;
            background: #365F65;
            color: white;
            font-size: 7px;
            text-align: left;
            border: 1px solid #365F65;
        }

        table.data td {
            padding: 6px 4px;
            border: 1px solid #DCE6E4;
            font-size: 7px;
            vertical-align: top;
            word-wrap: break-word;
        }

        table.data tr:nth-child(even) {
            background: #F5F8F7;
        }

        .center {
            text-align: center;
        }

        .empty {
            padding: 15px;
            text-align: center;
            color: #78918F;
        }

        .footer {
            margin-top: 15px;
            padding-top: 8px;
            border-top: 1px solid #DCE6E4;
            color: #78918F;
            font-size: 8px;
            text-align: right;
        }
    </style>
</head>

<body>

    {{-- HEADER --}}
    <div class="header">
        <h1>LAPORAN KONTRAK TENANT</h1>
        <p>
            AeroTrack - Sistem Monitoring Persewaan Tenant Bandara
        </p>
    </div>

    <div class="info">
        Tanggal Cetak:
        {{ now()->format('d/m/Y H:i') }}
        &nbsp; | &nbsp;
        Jumlah Data:
        {{ number_format($totalKontrak, 0, ',', '.') }} Kontrak
    </div>

    {{-- RINGKASAN --}}
    <table class="summary">
        <tr>
            <td>
                <div class="summary-label">Total Kontrak</div>
                <div class="summary-value">
                    {{ number_format($totalKontrak, 0, ',', '.') }}
                </div>
            </td>

            <td>
                <div class="summary-label">Kontrak Draft</div>
                <div class="summary-value">
                    {{ number_format($totalDraft, 0, ',', '.') }}
                </div>
            </td>

            <td>
                <div class="summary-label">Kontrak Aktif</div>
                <div class="summary-value">
                    {{ number_format($totalAktif, 0, ',', '.') }}
                </div>
            </td>

            <td>
                <div class="summary-label">Kontrak Nonaktif</div>
                <div class="summary-value">
                    {{ number_format($totalNonaktif, 0, ',', '.') }}
                </div>
            </td>
        </tr>
    </table>

    {{-- DISTRIBUSI STATUS --}}
    <div class="section-title">
        Distribusi Status Kontrak
    </div>

    <table class="data" style="margin-bottom: 15px;">
        <thead>
            <tr>
                <th>Status Draft</th>
                <th>Status Aktif</th>
                <th>Status Nonaktif</th>
            </tr>
        </thead>

        <tbody>
            <tr>
                <td>{{ $totalDraft }}</td>
                <td>{{ $totalAktif }}</td>
                <td>{{ $totalNonaktif }}</td>
            </tr>
        </tbody>
    </table>

    {{-- DATA KONTRAK --}}
    <div class="section-title">
        Rincian Data Kontrak
    </div>

    <table class="data">
        <thead>
            <tr>
                <th width="3%">No.</th>
                <th width="10%">Nomor Kontrak</th>
                <th width="9%">Nomor Kerja Sama</th>
                <th width="10%">Mitra Usaha</th>
                <th width="8%">Brand</th>
                <th width="9%">Nama Tenant</th>
                <th width="8%">Jenis Usaha</th>
                <th width="5%">Terminal</th>
                <th width="9%">Lokasi</th>
                <th width="7%">Tgl. Pengajuan</th>
                <th width="7%">Tgl. Berakhir</th>
                <th width="6%">Status</th>
                <th width="9%">Perpanjangan</th>
            </tr>
        </thead>

        <tbody>

            @forelse ($kontraks as $index => $kontrak)

                @php
                    $kerjaSama = $kontrak->kerjaSama;
                    $tenant = $kontrak->tenant;
                    $lokasi = $kontrak->lokasi;

                    $formatTanggal = function ($tanggal) {
                        return $tanggal
                            ? \Carbon\Carbon::parse($tanggal)->format('d/m/Y')
                            : '-';
                    };
                @endphp

                <tr>
                    <td class="center">{{ $index + 1 }}</td>

                    <td>{{ $kontrak->nomor_kontrak ?: '-' }}</td>

                    <td>{{ $kerjaSama?->nomor_kerja_sama ?: '-' }}</td>

                    <td>
                        {{ $kontrak->nama_mitra_snapshot
                            ?: $kerjaSama?->mitra_usaha
                            ?: '-' }}
                    </td>

                    <td>{{ $kerjaSama?->brand ?: '-' }}</td>

                    <td>{{ $tenant?->nama_tenant ?: '-' }}</td>

                    <td>{{ $kerjaSama?->jenis_usaha ?: '-' }}</td>

                    <td>{{ $lokasi?->terminal ?: '-' }}</td>

                    <td>{{ $lokasi?->lokasi ?: '-' }}</td>

                    <td>
                        {{ $formatTanggal($kontrak->tanggal_pengajuan) }}
                    </td>

                    <td>
                        {{ $formatTanggal($kerjaSama?->tanggal_berakhir) }}
                    </td>

                    <td>{{ $kontrak->status_laporan }}</td>

                    <td>
                        {{ $kontrak->status_perpanjangan
                            ?: 'Belum Dikonfirmasi' }}
                    </td>
                </tr>

            @empty

                <tr>
                    <td colspan="13" class="empty">
                        Tidak ada data kontrak yang sesuai dengan filter laporan.
                    </td>
                </tr>

            @endforelse

        </tbody>
    </table>

    <div class="footer">
        AeroTrack | Laporan Kontrak | Dicetak {{ now()->format('d/m/Y H:i') }}
    </div>

</body>
</html>
