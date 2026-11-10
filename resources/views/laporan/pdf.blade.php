<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">

    <title>Laporan AeroTrack {{ $tahun }}</title>

    <style>
        @page {
            size: A4 landscape;
            margin: 22px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 9px;
            color: #365F65;
        }

        h1, h2, h3, p {
            margin: 0;
        }

        .header {
            border-bottom: 2px solid #365F65;
            padding-bottom: 12px;
            margin-bottom: 15px;
        }

        .header h1 {
            font-size: 20px;
            margin-bottom: 5px;
        }

        .muted {
            color: #8BA5A2;
        }

        .section-title {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 9px;
            color: #365F65;
        }

        .grid {
            width: 100%;
            border-collapse: separate;
            border-spacing: 6px;
            margin-left: -6px;
            margin-bottom: 12px;
        }

        .card {
            border: 1px solid #DCE6E4;
            background: #F8FAF9;
            padding: 10px;
            vertical-align: top;
        }

        .card-label {
            font-size: 9px;
            color: #527D82;
            margin-bottom: 7px;
        }

        .card-value {
            font-size: 19px;
            font-weight: bold;
            color: #365F65;
        }

        .small {
            font-size: 8px;
            color: #8BA5A2;
            margin-top: 5px;
        }

        .panel {
            border: 1px solid #DCE6E4;
            padding: 12px;
            margin-bottom: 12px;
        }

        .two-column {
            width: 100%;
            border-collapse: separate;
            border-spacing: 6px;
        }

        .bar-track {
            width: 100%;
            height: 8px;
            background: #E2EBE9;
            margin-top: 5px;
        }

        .bar-fill {
            height: 8px;
            background: #365F65;
        }

        .bar-orange {
            background: #D6A14D;
        }

        .bar-green {
            background: #2F9E5A;
        }

        .category-cell {
            width: 16.66%;
            padding: 8px;
            border: 1px solid #DCE6E4;
            background: #F8FAF9;
        }

        .category-value {
            font-size: 17px;
            font-weight: bold;
            margin: 5px 0;
        }

        .donut-table {
            width: 100%;
        }

        .donut-table td {
            vertical-align: middle;
            padding: 4px;
        }

        .legend-dot {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
        }

        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }

        table.data th {
            background: #365F65;
            color: white;
            padding: 7px 5px;
            font-size: 8px;
            text-align: left;
        }

        table.data td {
            border-bottom: 1px solid #DCE6E4;
            padding: 6px 5px;
            font-size: 8px;
        }

        table.data tr:nth-child(even) {
            background: #F4F8F6;
        }

        .footer {
            margin-top: 15px;
            padding-top: 8px;
            border-top: 1px solid #DCE6E4;
            text-align: right;
            color: #8BA5A2;
            font-size: 8px;
        }

        .page-break {
            page-break-before: always;
        }
    </style>
</head>

<body>

@php
    $belumAktif = max(0, $totalKerjaSama - $totalSistemAktif);

    $totalPerLokasi = $kontrakPerTerminal->sum();

    $jumlahT1 = $kontrakPerTerminal['T1'] ?? 0;
    $jumlahT2 = $kontrakPerTerminal['T2'] ?? 0;

    $persenT1 = $totalPerLokasi > 0
        ? round($jumlahT1 / $totalPerLokasi * 100, 1)
        : 0;

    $persenT2 = $totalPerLokasi > 0
        ? round($jumlahT2 / $totalPerLokasi * 100, 1)
        : 0;

    $totalKeputusan = $kontrakLintas + $kontrakPerpanjangan;

    $persenLintas = $totalKeputusan > 0
        ? $kontrakLintas / $totalKeputusan * 100
        : 0;

    $persenPerpanjangan = $totalKeputusan > 0
        ? $kontrakPerpanjangan / $totalKeputusan * 100
        : 0;

    $kategori = [
        'Food & Beverage' => ['F&B', 'Food & Beverage'],
        'Services' => ['Service', 'Services'],
        'Retail' => ['Retail'],
        'Lounge' => ['Lounge'],
        'Duty Free' => ['Duty Free'],
        'UMKM' => ['UMKM'],
    ];

    $dataKategori = [];

    foreach ($kategori as $label => $alias) {
        $items = $laporan->filter(function ($item) use ($alias) {
            return in_array(
                trim($item['kerja_sama']->jenis_usaha ?? ''),
                $alias,
                true
            );
        });

        $total = $items->count();

        $aktif = $items->filter(function ($item) {
            return $item['kerja_sama']->aktivasi?->status === 'Disetujui';
        })->count();

        $dataKategori[$label] = [
            'total' => $total,
            'aktif' => $aktif,
            'persen' => $total > 0 ? round($aktif / $total * 100, 1) : 0,
        ];
    }

    $keliling = 314.16;
    $panjangT1 = $keliling * $persenT1 / 100;
    $panjangLintas = $keliling * $persenLintas / 100;
@endphp

{{-- HEADER --}}
<div class="header">
    <h1>AEROTRACK</h1>

    <p style="font-size: 13px; font-weight: bold;">
        LAPORAN MONITORING PERSEWAAN TENANT BANDARA
    </p>

    <p class="muted">
        Periode laporan: Tahun {{ $tahun }}
    </p>
</div>

{{-- SUMMARY CARDS --}}
<h2 class="section-title">Ringkasan Laporan</h2>

<table class="grid">
    <tr>
        <td class="card" width="20%">
            <div class="card-label">Total Kontrak</div>
            <div class="card-value">{{ number_format($totalKontrak) }}</div>
            <div class="small">Seluruh kontrak tercatat</div>
        </td>

        <td class="card" width="20%">
            <div class="card-label">Kontrak Aktif</div>
            <div class="card-value">{{ number_format($kontrakAktif) }}</div>
            <div class="small">Status aktif</div>
        </td>

        <td class="card" width="20%">
            <div class="card-label">Progress Aktivasi</div>
            <div class="card-value">{{ $progressAktivasi }}%</div>
            <div class="small">{{ $totalSistemAktif }} dari {{ $totalKerjaSama }} kerja sama</div>
        </td>

        <td class="card" width="20%">
            <div class="card-label">Terminal 1</div>
            <div class="card-value">{{ $progressTerminal['T1']['total'] ?? 0 }}</div>
            <div class="small">{{ $progressTerminal['T1']['aktif'] ?? 0 }} aktif</div>
        </td>

        <td class="card" width="20%">
            <div class="card-label">Terminal 2</div>
            <div class="card-value">{{ $progressTerminal['T2']['total'] ?? 0 }}</div>
            <div class="small">{{ $progressTerminal['T2']['aktif'] ?? 0 }} aktif</div>
        </td>
    </tr>
</table>

{{-- GRAFIK DAN INFORMASI KONTRAK --}}
<table class="two-column">
    <tr>
        <td width="50%">
            <div class="panel">
                <h3 class="section-title">Distribusi Kontrak per Lokasi</h3>

                <table class="donut-table">
                    <tr>
                        <td width="150">
                            <svg width="130" height="130" viewBox="0 0 120 120">
                                <circle
                                    cx="60" cy="60" r="50"
                                    fill="none"
                                    stroke="#D6A14D"
                                    stroke-width="16"
                                />

                                <circle
                                    cx="60" cy="60" r="50"
                                    fill="none"
                                    stroke="#365F65"
                                    stroke-width="16"
                                    stroke-dasharray="{{ $panjangT1 }} {{ $keliling - $panjangT1 }}"
                                    transform="rotate(-90 60 60)"
                                />

                                <text x="60" y="57" text-anchor="middle"
                                      font-size="19" font-weight="bold" fill="#365F65">
                                    {{ $totalPerLokasi }}
                                </text>

                                <text x="60" y="72" text-anchor="middle"
                                      font-size="9" fill="#8BA5A2">
                                    Kontrak
                                </text>
                            </svg>
                        </td>

                        <td>
                            <p>
                                <span class="legend-dot" style="background:#365F65;"></span>
                                Terminal 1: <strong>{{ $jumlahT1 }}</strong> ({{ $persenT1 }}%)
                            </p>

                            <p style="margin-top: 10px;">
                                <span class="legend-dot" style="background:#D6A14D;"></span>
                                Terminal 2: <strong>{{ $jumlahT2 }}</strong> ({{ $persenT2 }}%)
                            </p>
                        </td>
                    </tr>
                </table>
            </div>
        </td>

        <td width="50%">
            <div class="panel">
                <h3 class="section-title">Informasi Kontrak</h3>

                <table class="data">
                    <tr>
                        <td>Belum Aktif</td>
                        <td><strong>{{ $belumAktif }}</strong></td>
                    </tr>
                    <tr>
                        <td>Pusat</td>
                        <td>—</td>
                    </tr>
                    <tr>
                        <td>Proses Aktivasi</td>
                        <td><strong>{{ $belumAktif }}</strong></td>
                    </tr>
                    <tr>
                        <td>Kontrak Aktif / Total Kerja Sama</td>
                        <td>
                            <strong>{{ $totalSistemAktif }} / {{ $totalKerjaSama }}</strong>
                        </td>
                    </tr>
                </table>
            </div>
        </td>
    </tr>
</table>

{{-- MONITORING KATEGORI --}}
<div class="panel">
    <h2 class="section-title">Monitoring per Kategori Tenant</h2>

    <table class="grid">
        <tr>
            @foreach ($dataKategori as $label => $item)
                <td class="category-cell">
                    <strong>{{ $label }}</strong>

                    <div class="category-value">
                        {{ $item['total'] }}
                    </div>

                    <div class="bar-track">
                        <div class="bar-fill bar-green"
                             style="width: {{ $item['persen'] }}%;">
                        </div>
                    </div>

                    <p class="small">
                        {{ $item['aktif'] }} aktif ({{ $item['persen'] }}%)
                    </p>
                </td>
            @endforeach
        </tr>
    </table>
</div>

{{-- PERIODE KONTRAK --}}
<table class="two-column">
    <tr>
        <td width="50%">
            <div class="panel">
                <h2 class="section-title">Rekapitulasi Periode Kontrak</h2>

                <table class="donut-table">
                    <tr>
                        <td width="150">
                            <svg width="130" height="130" viewBox="0 0 120 120">
                                <circle
                                    cx="60" cy="60" r="50"
                                    fill="none"
                                    stroke="#365F65"
                                    stroke-width="16"
                                />

                                <circle
                                    cx="60" cy="60" r="50"
                                    fill="none"
                                    stroke="#D6A14D"
                                    stroke-width="16"
                                    stroke-dasharray="{{ $panjangLintas }} {{ $keliling - $panjangLintas }}"
                                    transform="rotate(-90 60 60)"
                                />

                                <text x="60" y="57" text-anchor="middle"
                                      font-size="19" font-weight="bold" fill="#365F65">
                                    {{ $totalKeputusan }}
                                </text>

                                <text x="60" y="72" text-anchor="middle"
                                      font-size="9" fill="#8BA5A2">
                                    Total
                                </text>
                            </svg>
                        </td>

                        <td>
                            <p>
                                <span class="legend-dot" style="background:#D6A14D;"></span>
                                Lintas: <strong>{{ $kontrakLintas }}</strong>
                                ({{ round($persenLintas, 1) }}%)
                            </p>

                            <p style="margin-top: 10px;">
                                <span class="legend-dot" style="background:#365F65;"></span>
                                Perpanjangan: <strong>{{ $kontrakPerpanjangan }}</strong>
                                ({{ round($persenPerpanjangan, 1) }}%)
                            </p>
                        </td>
                    </tr>
                </table>
            </div>
        </td>

        <td width="50%">
            <div class="panel">
                <h2 class="section-title">Kelengkapan Dokumen</h2>

                <p>Kontrak Mitra</p>
                <h3>{{ $dokumenKontrakBelumLengkap }} belum lengkap</h3>

                <div class="bar-track">
                    <div class="bar-fill bar-orange"
                         style="width: {{ $totalDokumenKontrak > 0 ? ($dokumenKontrakBelumLengkap / $totalDokumenKontrak * 100) : 0 }}%;">
                    </div>
                </div>

                <p class="small">
                    {{ $dokumenKontrakLengkap }} lengkap dari {{ $totalDokumenKontrak }} kontrak
                </p>

                <br>

                <p>Berita Acara Final</p>
                <h3>{{ $baBelumLengkap }} belum lengkap</h3>

                <div class="bar-track">
                    <div class="bar-fill bar-orange"
                         style="width: {{ $totalBA > 0 ? ($baBelumLengkap / $totalBA * 100) : 0 }}%;">
                    </div>
                </div>

                <p class="small">
                    {{ $baLengkap }} lengkap dari {{ $totalBA }} berita acara
                </p>
            </div>
        </td>
    </tr>
</table>

{{-- TABEL DETAIL --}}
<div class="page-break"></div>

<h2 class="section-title">Daftar Laporan Kerja Sama</h2>

<table class="data">
    <thead>
        <tr>
            <th>No.</th>
            <th>No. Kerja Sama</th>
            <th>Mitra Usaha</th>
            <th>Brand</th>
            <th>Jenis Usaha</th>
            <th>Terminal</th>
            <th>Kode Ruang</th>
            <th>No. Kontrak Mitra</th>
            <th>Status</th>
            <th>Dokumen</th>
        </tr>
    </thead>

    <tbody>
        @forelse ($laporan as $index => $item)
            @php
                $ks = $item['kerja_sama'];
                $kontrak = $item['kontrak'];
                $lokasi = $item['lokasi'];
            @endphp

            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $ks->nomor_kerja_sama ?? '-' }}</td>
                <td>{{ $ks->mitra_usaha ?? '-' }}</td>
                <td>{{ $ks->brand ?? '-' }}</td>
                <td>{{ $ks->jenis_usaha ?? '-' }}</td>
                <td>{{ $lokasi?->terminal ?? '-' }}</td>
                <td>{{ $lokasi?->kode_ruang ?? '-' }}</td>
                <td>{{ $kontrak?->nomor_kontrak_mitra ?? '-' }}</td>
                <td>{{ $item['status_laporan'] }}</td>
                <td>{{ $item['dokumen_lengkap'] ? 'Lengkap' : 'Belum Lengkap' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="10" style="text-align:center;">
                    Tidak ada data sesuai filter laporan.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

<div class="footer">
    AeroTrack | Laporan Monitoring Persewaan Tenant Bandara | Tahun {{ $tahun }}
</div>

</body>
</html>