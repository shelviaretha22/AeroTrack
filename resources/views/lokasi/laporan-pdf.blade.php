<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Lokasi dan Aktivasi</title>

    <style>
        @page { size: A4; margin: 1.6cm 1.8cm; }

        body {
            font-family: "DejaVu Sans", sans-serif;
            font-size: 9pt;
            margin: 0;
            line-height: 1.35;
            color: #222;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #365F65;
            padding-bottom: 10px;
            margin-bottom: 12px;
        }
        .header h2 { color: #365F65; margin: 0; font-size: 14pt; }
        .header p { margin: 3px 0 0; color: #555; }

        h3 { color: #365F65; font-size: 10.5pt; margin: 14px 0 5px; }

        table { width: 100%; border-collapse: collapse; margin-bottom: 4px; }
        th, td { border: 1px solid #CBD9D6; padding: 4px 6px; }
        th { background: #E8F0EF; color: #365F65; text-align: left; font-size: 8pt; }
        td.angka, th.angka { text-align: right; }

        .kpi td { border: 1px solid #CBD9D6; text-align: center; padding: 6px; }
        .kpi .nilai { font-size: 13pt; font-weight: bold; color: #365F65; }
        .kpi .label { font-size: 7.5pt; color: #666; }

        .catatan { margin-top: 14px; font-size: 7.5pt; color: #777; }
    </style>
</head>
<body>
@php
    $fmt = fn ($v, $d = 0) => number_format((float) $v, $d, ',', '.');
@endphp

<div class="header">
    <h2>Laporan Lokasi &amp; Aktivasi</h2>
    <p>
        @if ($data['tanggal_okupansi'])
            Data per {{ \Illuminate\Support\Carbon::parse($data['tanggal_okupansi'])->format('d/m/Y') }}
        @endif
        @if ($terminal) &middot; Terminal {{ $terminal }} @endif
    </p>
</div>

<table class="kpi">
    <tr>
        <td><div class="nilai">{{ $fmt($data['kpi']['total_ruangan']) }}</div><div class="label">Total ruangan</div></td>
        <td><div class="nilai">{{ $fmt($data['kpi']['okupansi'], 1) }}%</div><div class="label">Okupansi</div></td>
        <td><div class="nilai">{{ $fmt($data['kpi']['total_aktivasi']) }}</div><div class="label">Pengajuan aktivasi</div></td>
        <td><div class="nilai">{{ $fmt($data['kpi']['disetujui']) }}</div><div class="label">Disetujui</div></td>
    </tr>
</table>

<h3>Okupansi per Terminal</h3>
<table>
    <thead>
        <tr>
            <th>Terminal</th>
            <th class="angka">Total</th>
            <th class="angka">Terisi</th>
            <th class="angka">Kosong</th>
            <th class="angka">Okupansi</th>
            <th class="angka">Luas total (m²)</th>
            <th class="angka">Luas terisi (m²)</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data['per_terminal'] as $r)
            <tr>
                <td>{{ $r['nama'] }}</td>
                <td class="angka">{{ $fmt($r['total']) }}</td>
                <td class="angka">{{ $fmt($r['terisi']) }}</td>
                <td class="angka">{{ $fmt($r['kosong']) }}</td>
                <td class="angka">{{ $fmt($r['okupansi'], 1) }}%</td>
                <td class="angka">{{ $fmt($r['luas_total'], 2) }}</td>
                <td class="angka">{{ $fmt($r['luas_terisi'], 2) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<h3>Okupansi per Lantai</h3>
<table>
    <thead>
        <tr>
            <th>Terminal / Lantai</th>
            <th class="angka">Total</th>
            <th class="angka">Terisi</th>
            <th class="angka">Kosong</th>
            <th class="angka">Okupansi</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data['per_lantai'] as $r)
            <tr>
                <td>{{ $r['nama'] }}</td>
                <td class="angka">{{ $fmt($r['total']) }}</td>
                <td class="angka">{{ $fmt($r['terisi']) }}</td>
                <td class="angka">{{ $fmt($r['kosong']) }}</td>
                <td class="angka">{{ $fmt($r['okupansi'], 1) }}%</td>
            </tr>
        @endforeach
    </tbody>
</table>

<h3>Aktivasi per Periode dan Status</h3>
<table>
    <thead>
        <tr>
            <th>Periode pengajuan</th>
            <th>Status</th>
            <th class="angka">Jumlah</th>
            <th class="angka">Rata-rata hari ke keputusan</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data['aktivasi'] as $r)
            <tr>
                <td>{{ $r->periode === '-' ? 'Belum diajukan' : $r->periode }}</td>
                <td>{{ $r->status }}</td>
                <td class="angka">{{ $fmt($r->jumlah) }}</td>
                <td class="angka">{{ $r->rata_hari_keputusan !== null ? $fmt($r->rata_hari_keputusan, 1) : '-' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<p class="catatan">
    Dicetak {{ $dicetak->format('d/m/Y H:i') }}. Sumber: tabel ringkasan hasil ETL (ringkasan_okupansi dan ringkasan_aktivasi).
</p>
</body>
</html>
