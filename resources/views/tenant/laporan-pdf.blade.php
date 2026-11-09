
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">

    <title>Laporan Tenant AeroTrack</title>

    <style>
        @page {
            size: A4 landscape;
            margin: 25px 25px 35px 25px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            color: #365F65;
            font-size: 9px;
            line-height: 1.45;
        }

        .header {
            border-bottom: 2px solid #527D82;
            padding-bottom: 12px;
            margin-bottom: 16px;
        }

        .header h1 {
            margin: 0;
            font-size: 19px;
            color: #365F65;
        }

        .header p {
            margin: 4px 0 0;
            color: #78918D;
            font-size: 9px;
        }

        .period-info {
            margin-top: 9px;
            font-size: 8px;
            color: #527D82;
        }

        .summary-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 7px 0;
            margin: 0 0 17px -7px;
        }

        .summary-table td {
            width: 25%;
            border: 1px solid #DCE6E4;
            border-top: 3px solid #527D82;
            padding: 10px;
            vertical-align: top;
        }

        .summary-label {
            color: #78918D;
            font-size: 8px;
            font-weight: bold;
        }

        .summary-value {
            color: #365F65;
            font-size: 19px;
            font-weight: bold;
            margin-top: 5px;
        }

        .summary-detail {
            color: #78918D;
            font-size: 7px;
            margin-top: 5px;
        }

        .section-title {
            font-size: 11px;
            font-weight: bold;
            color: #365F65;
            margin: 0 0 9px;
        }

        .chart-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 7px 0;
            margin: 0 0 17px -7px;
        }

        .chart-table td {
            width: 50%;
            border: 1px solid #DCE6E4;
            padding: 11px;
            vertical-align: top;
        }

        .chart-item {
            margin-bottom: 10px;
        }

        .chart-label {
            width: 75%;
            font-size: 8px;
            color: #527D82;
        }

        .chart-value {
            width: 25%;
            text-align: right;
            font-size: 8px;
            font-weight: bold;
            color: #365F65;
        }

        .bar-track {
            width: 100%;
            height: 7px;
            background: #EDF2F0;
            margin-top: 4px;
        }

        .bar-fill {
            height: 7px;
            background: #527D82;
        }

        .table-title {
            margin: 12px 0 8px;
            font-size: 11px;
            font-weight: bold;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .data-table thead {
            display: table-header-group;
        }

        .data-table th {
            background: #365F65;
            color: #FFFFFF;
            padding: 7px 5px;
            text-align: left;
            font-size: 7px;
            border: 1px solid #365F65;
        }

        .data-table td {
            padding: 6px 5px;
            border: 1px solid #DCE6E4;
            font-size: 7px;
            color: #405F60;
            vertical-align: top;
            word-wrap: break-word;
        }

        .data-table tbody tr:nth-child(even) {
            background: #F5F8F7;
        }

        .data-table tr {
            page-break-inside: avoid;
        }

        .text-center {
            text-align: center;
        }

        .empty {
            text-align: center;
            padding: 20px !important;
            color: #78918D !important;
        }

        .footer {
            margin-top: 12px;
            padding-top: 7px;
            border-top: 1px solid #DCE6E4;
            color: #78918D;
            font-size: 7px;
            text-align: right;
        }

        .filter-info {
            font-size: 8px;
            color: #527D82;
            margin-bottom: 12px;
        }
    </style>
</head>

<body>

@php
    $maksJenisUsaha = max(1, (int) $distribusiJenisUsaha->max('total'));
    $maksTerminal = max(1, (int) $distribusiTerminal->max('total'));

    $formatTanggal = function ($tanggal) {
        return $tanggal
            ? \Carbon\Carbon::parse($tanggal)->format('d/m/Y')
            : '-';
    };

    $namaBasisPeriode = ($filters['basis_periode'] ?? 'pengajuan') === 'terbentuk'
        ? 'Tanggal Tenant Terbentuk'
        : 'Tanggal Pengajuan Kerja Sama';
@endphp

{{-- HEADER LAPORAN --}}
<div class="header">
    <h1>LAPORAN TENANT</h1>

    <p>
        AeroTrack - Sistem Monitoring Persewaan Tenant Bandara
    </p>

    <div class="period-info">
        Basis periode: {{ $namaBasisPeriode }}
        <br>

        Tanggal laporan:
        {{ now()->format('d/m/Y H:i') }}
    </div>
</div>

{{-- INFORMASI FILTER --}}
<div class="filter-info">
    <strong>Filter laporan:</strong>

    Periode:
    {{ $filters['tanggal_mulai']
        ? $formatTanggal($filters['tanggal_mulai'])
        : 'Semua tanggal' }}

    s.d.

    {{ $filters['tanggal_selesai']
        ? $formatTanggal($filters['tanggal_selesai'])
        : 'Semua tanggal' }}

    |

    Jenis Usaha:
    {{ $filters['jenis_usaha'] ?: 'Semua' }}

    |

    Terminal:
    {{ $filters['terminal'] ?: 'Semua' }}

    |

    Status Aktivasi:
    {{ $filters['status_aktivasi'] ?: 'Semua' }}
</div>

{{-- RINGKASAN --}}
<h2 class="section-title">Ringkasan Tenant</h2>

<table class="summary-table">
    <tr>
        <td>
            <div class="summary-label">TOTAL TENANT</div>

            <div class="summary-value">
                {{ number_format($totalTenant, 0, ',', '.') }}
            </div>

            <div class="summary-detail">
                Tenant sesuai filter laporan
            </div>
        </td>

        <td>
            <div class="summary-label">TENANT AKTIF</div>

            <div class="summary-value">
                {{ number_format($totalTenantAktif, 0, ',', '.') }}
            </div>

            <div class="summary-detail">
                Aktivasi sistem disetujui
            </div>
        </td>

        <td>
            <div class="summary-label">JENIS USAHA</div>

            <div class="summary-detail">
                @forelse ($distribusiJenisUsaha as $item)
                    {{ $item['label'] }}:
                    <strong>{{ $item['total'] }} Tenant</strong><br>
                @empty
                    Belum ada data.
                @endforelse
            </div>
        </td>

        <td>
            <div class="summary-label">TERMINAL</div>

            <div class="summary-detail">
                @forelse ($distribusiTerminal as $item)
                    {{ $item['label'] }}:
                    <strong>{{ $item['total'] }} Tenant</strong><br>
                @empty
                    Belum ada data.
                @endforelse
            </div>
        </td>
    </tr>
</table>

{{-- GRAFIK DISTRIBUSI --}}
<h2 class="section-title">Analisis Distribusi Tenant</h2>

<table class="chart-table">
    <tr>
        <td>
            <strong>Distribusi Tenant per Jenis Usaha</strong>

            <br><br>

            @forelse ($distribusiJenisUsaha as $item)
                <div class="chart-item">

                    <table width="100%">
                        <tr>
                            <td class="chart-label">
                                {{ $item['label'] }}
                            </td>

                            <td class="chart-value">
                                {{ $item['total'] }} Tenant
                            </td>
                        </tr>
                    </table>

                    <div class="bar-track">
                        <div class="bar-fill"
                             style="width: {{ ($item['total'] / $maksJenisUsaha) * 100 }}%;">
                        </div>
                    </div>

                </div>
            @empty
                <p>Belum ada data jenis usaha.</p>
            @endforelse
        </td>

        <td>
            <strong>Distribusi Tenant per Terminal</strong>

            <br><br>

            @forelse ($distribusiTerminal as $item)
                <div class="chart-item">

                    <table width="100%">
                        <tr>
                            <td class="chart-label">
                                {{ $item['label'] }}
                            </td>

                            <td class="chart-value">
                                {{ $item['total'] }} Tenant
                            </td>
                        </tr>
                    </table>

                    <div class="bar-track">
                        <div class="bar-fill"
                             style="width: {{ ($item['total'] / $maksTerminal) * 100 }}%;">
                        </div>
                    </div>

                </div>
            @empty
                <p>Belum ada data terminal.</p>
            @endforelse
        </td>
    </tr>
</table>

{{-- TABEL DATA TENANT --}}
<h2 class="table-title">Data Tenant</h2>

<table class="data-table">

    <thead>
        <tr>
            <th width="3%">No.</th>
            <th width="12%">Nama Tenant</th>
            <th width="11%">Mitra Usaha</th>
            <th width="9%">Brand</th>
            <th width="9%">Jenis Usaha</th>
            <th width="7%">Terminal</th>
            <th width="12%">Lokasi</th>
            <th width="9%">Tgl. Pengajuan</th>
            <th width="12%">Status Kerja Sama</th>
            <th width="16%">Status Aktivasi</th>
        </tr>
    </thead>

    <tbody>

        @forelse ($tenants as $index => $tenant)

            @php
                $kerjaSama = $tenant->kerjaSama;
                $lokasi = $kerjaSama?->lokasi;
            @endphp

            <tr>
                <td class="text-center">
                    {{ $index + 1 }}
                </td>

                <td>
                    {{ $tenant->nama_tenant ?: '-' }}
                </td>

                <td>
                    {{ $tenant->mitra_usaha ?: '-' }}
                </td>

                <td>
                    {{ $kerjaSama?->brand ?: '-' }}
                </td>

                <td>
                    {{ $tenant->jenis_usaha ?: '-' }}
                </td>

                <td>
                    {{ $lokasi?->terminal ?: '-' }}
                </td>

                <td>
                    {{ $lokasi?->lokasi ?: '-' }}
                </td>

                <td>
                    {{ $formatTanggal($kerjaSama?->tanggal_pengajuan) }}
                </td>

                <td>
                    {{ $kerjaSama?->status ?: '-' }}
                </td>

                <td>
                    {{ $tenant->status_aktivasi_laporan }}
                </td>
            </tr>

        @empty

            <tr>
                <td colspan="10" class="empty">
                    Tidak ada data Tenant yang sesuai dengan filter laporan.
                </td>
            </tr>

        @endforelse

    </tbody>

</table>

<div class="footer">
    AeroTrack | Laporan Tenant
</div>

</body>
</html>
