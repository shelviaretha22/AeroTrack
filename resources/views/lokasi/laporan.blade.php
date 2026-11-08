@extends('layouts.app')

@section('page-title', 'Laporan Lokasi & Aktivasi')

@section('content')
@php
    $fmt = fn ($v, $d = 0) => number_format((float) $v, $d, ',', '.');
    $kelasInput = 'rounded-xl border border-[#DCE6E4] bg-[#F8FAF9] px-3.5 py-2.5 text-sm text-[#365F65] outline-none transition focus:border-[#527D82] focus:bg-white focus:ring-2 focus:ring-[#527D82]/15';
    $adaData = $data['tanggal_okupansi'] !== null || $data['tanggal_aktivasi'] !== null;
    $parameter = $terminal ? ['terminal' => $terminal] : [];
    $run = $data['etl_terakhir'];
@endphp

<div class="space-y-6">

    {{-- Judul dan aksi --}}
    <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
        <div>
            <p class="text-sm text-[#8BA5A2]">Management / Lokasi / Laporan</p>
            <h1 class="mt-1 text-2xl font-bold text-[#365F65]">Laporan Lokasi &amp; Aktivasi</h1>
            <p class="mt-1 text-sm text-[#7A9290]">
                Dibaca dari tabel ringkasan hasil ETL
                @if ($data['tanggal_okupansi'])
                    (data per {{ \Illuminate\Support\Carbon::parse($data['tanggal_okupansi'])->translatedFormat('d F Y') }}).
                @else
                    (belum ada data).
                @endif
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('lokasi.laporan.excel', $parameter) }}"
               class="inline-flex items-center justify-center rounded-xl border border-[#DCE6E4] bg-white px-4 py-2.5 text-sm font-semibold text-[#365F65] shadow-sm hover:bg-[#F3F6F5]">
                Unduh Excel
            </a>
            <a href="{{ route('lokasi.laporan.pdf', $parameter) }}"
               class="inline-flex items-center justify-center rounded-xl border border-[#DCE6E4] bg-white px-4 py-2.5 text-sm font-semibold text-[#365F65] shadow-sm hover:bg-[#F3F6F5]">
                Unduh PDF
            </a>
            <form method="POST" action="{{ route('lokasi.laporan.etl') }}">
                @csrf
                <button type="submit"
                        class="inline-flex items-center justify-center rounded-xl bg-[#365F65] px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-[#294F55]">
                    Jalankan ETL
                </button>
            </form>
        </div>
    </div>

    {{-- Notifikasi --}}
    @foreach ([
        'success' => 'border-emerald-200 bg-emerald-50 text-emerald-800',
        'error'   => 'border-red-200 bg-red-50 text-red-700',
    ] as $kunci => $kelas)
        @if (session($kunci))
            <div class="rounded-xl border px-4 py-3 text-sm {{ $kelas }}">{{ session($kunci) }}</div>
        @endif
    @endforeach

    {{-- Filter --}}
    <form method="GET" action="{{ route('lokasi.laporan') }}" class="flex flex-wrap items-center gap-2">
        <label for="terminal" class="text-sm font-semibold text-[#365F65]">Terminal</label>
        <select id="terminal" name="terminal" class="{{ $kelasInput }}" onchange="this.form.submit()">
            <option value="">Semua</option>
            <option value="T1" @selected($terminal === 'T1')>T1</option>
            <option value="T2" @selected($terminal === 'T2')>T2</option>
        </select>
        <noscript><button type="submit" class="{{ $kelasInput }}">Terapkan</button></noscript>
    </form>

    @if (! $adaData)
        <div class="rounded-2xl border border-dashed border-[#DCE6E4] bg-white px-6 py-12 text-center">
            <p class="font-semibold text-[#365F65]">Belum ada data laporan</p>
            <p class="mt-1 text-sm text-[#7A9290]">Tekan "Jalankan ETL" untuk menyusun ringkasan dari data Lokasi dan Aktivasi.</p>
        </div>
    @else
        {{-- KPI --}}
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-2xl border border-[#E2EBE9] bg-white p-5 shadow-sm">
                <p class="text-sm text-[#7A9290]">Total ruangan</p>
                <p class="mt-1 text-3xl font-bold text-[#365F65]">{{ $fmt($data['kpi']['total_ruangan']) }}</p>
            </div>
            <div class="rounded-2xl border border-[#E2EBE9] bg-white p-5 shadow-sm">
                <p class="text-sm text-[#7A9290]">Okupansi</p>
                <p class="mt-1 text-3xl font-bold text-[#365F65]">{{ $fmt($data['kpi']['okupansi'], 1) }}%</p>
                <p class="mt-1 text-xs text-[#8BA5A2]">{{ $fmt($data['kpi']['terisi']) }} ruangan terisi</p>
            </div>
            <div class="rounded-2xl border border-[#E2EBE9] bg-white p-5 shadow-sm">
                <p class="text-sm text-[#7A9290]">Total pengajuan aktivasi</p>
                <p class="mt-1 text-3xl font-bold text-[#365F65]">{{ $fmt($data['kpi']['total_aktivasi']) }}</p>
            </div>
            <div class="rounded-2xl border border-[#E2EBE9] bg-white p-5 shadow-sm">
                <p class="text-sm text-[#7A9290]">Disetujui</p>
                <p class="mt-1 text-3xl font-bold text-[#365F65]">{{ $fmt($data['kpi']['disetujui']) }}</p>
                <p class="mt-1 text-xs text-[#8BA5A2]">{{ $fmt($data['kpi']['persen_setuju'], 1) }}% dari yang sudah diputuskan</p>
            </div>
        </div>

        {{-- Okupansi per terminal --}}
        <section class="rounded-2xl border border-[#E2EBE9] bg-white shadow-sm">
            <h2 class="border-b border-[#E2EBE9] px-5 py-4 font-bold text-[#365F65]">Okupansi per Terminal</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-[#F8FAF9] text-left text-xs uppercase tracking-wide text-[#7A9290]">
                        <tr>
                            <th class="px-5 py-3">Terminal</th>
                            <th class="px-5 py-3 text-right">Total</th>
                            <th class="px-5 py-3 text-right">Terisi</th>
                            <th class="px-5 py-3 text-right">Kosong</th>
                            <th class="px-5 py-3 text-right">Okupansi</th>
                            <th class="px-5 py-3 text-right">Luas total (m²)</th>
                            <th class="px-5 py-3 text-right">Luas terisi (m²)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#EEF3F2] text-[#365F65]">
                        @forelse ($data['per_terminal'] as $r)
                            <tr>
                                <td class="px-5 py-3 font-semibold">{{ $r['nama'] }}</td>
                                <td class="px-5 py-3 text-right">{{ $fmt($r['total']) }}</td>
                                <td class="px-5 py-3 text-right">{{ $fmt($r['terisi']) }}</td>
                                <td class="px-5 py-3 text-right">{{ $fmt($r['kosong']) }}</td>
                                <td class="px-5 py-3 text-right font-semibold">{{ $fmt($r['okupansi'], 1) }}%</td>
                                <td class="px-5 py-3 text-right">{{ $fmt($r['luas_total'], 2) }}</td>
                                <td class="px-5 py-3 text-right">{{ $fmt($r['luas_terisi'], 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="px-5 py-8 text-center text-[#7A9290]">Tidak ada data.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        {{-- Okupansi per lantai --}}
        <section class="rounded-2xl border border-[#E2EBE9] bg-white shadow-sm">
            <h2 class="border-b border-[#E2EBE9] px-5 py-4 font-bold text-[#365F65]">Okupansi per Lantai</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-[#F8FAF9] text-left text-xs uppercase tracking-wide text-[#7A9290]">
                        <tr>
                            <th class="px-5 py-3">Terminal / Lantai</th>
                            <th class="px-5 py-3 text-right">Total</th>
                            <th class="px-5 py-3 text-right">Terisi</th>
                            <th class="px-5 py-3 text-right">Kosong</th>
                            <th class="px-5 py-3 text-right">Okupansi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#EEF3F2] text-[#365F65]">
                        @forelse ($data['per_lantai'] as $r)
                            <tr>
                                <td class="px-5 py-3 font-semibold">{{ $r['nama'] }}</td>
                                <td class="px-5 py-3 text-right">{{ $fmt($r['total']) }}</td>
                                <td class="px-5 py-3 text-right">{{ $fmt($r['terisi']) }}</td>
                                <td class="px-5 py-3 text-right">{{ $fmt($r['kosong']) }}</td>
                                <td class="px-5 py-3 text-right font-semibold">{{ $fmt($r['okupansi'], 1) }}%</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-5 py-8 text-center text-[#7A9290]">Tidak ada data.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        {{-- Aktivasi --}}
        <section class="rounded-2xl border border-[#E2EBE9] bg-white shadow-sm">
            <h2 class="border-b border-[#E2EBE9] px-5 py-4 font-bold text-[#365F65]">Aktivasi per Periode dan Status</h2>

            <div class="flex flex-wrap gap-2 px-5 pt-4">
                @foreach ($data['per_status'] as $s)
                    <span class="rounded-full bg-[#F3F6F5] px-3 py-1 text-xs font-semibold text-[#365F65]">
                        {{ $s['nama'] }}: {{ $fmt($s['jumlah']) }}
                    </span>
                @endforeach
            </div>

            <div class="overflow-x-auto">
                <table class="mt-3 min-w-full text-sm">
                    <thead class="bg-[#F8FAF9] text-left text-xs uppercase tracking-wide text-[#7A9290]">
                        <tr>
                            <th class="px-5 py-3">Periode pengajuan</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3 text-right">Jumlah</th>
                            <th class="px-5 py-3 text-right">Rata-rata hari ke keputusan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#EEF3F2] text-[#365F65]">
                        @forelse ($data['aktivasi'] as $r)
                            <tr>
                                <td class="px-5 py-3 font-semibold">{{ $r->periode === '-' ? 'Belum diajukan' : $r->periode }}</td>
                                <td class="px-5 py-3">{{ $r->status }}</td>
                                <td class="px-5 py-3 text-right">{{ $fmt($r->jumlah) }}</td>
                                <td class="px-5 py-3 text-right">{{ $r->rata_hari_keputusan !== null ? $fmt($r->rata_hari_keputusan, 1) : '-' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-5 py-8 text-center text-[#7A9290]">Tidak ada data.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    @endif

    {{-- Catatan ETL --}}
    <section class="rounded-2xl border border-[#E2EBE9] bg-white shadow-sm">
        <h2 class="border-b border-[#E2EBE9] px-5 py-4 font-bold text-[#365F65]">Catatan ETL</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-[#F8FAF9] text-left text-xs uppercase tracking-wide text-[#7A9290]">
                    <tr>
                        <th class="px-5 py-3">Mulai</th>
                        <th class="px-5 py-3">Pemicu</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3 text-right">Diekstrak</th>
                        <th class="px-5 py-3 text-right">Dimuat</th>
                        <th class="px-5 py-3">Pesan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#EEF3F2] text-[#365F65]">
                    @forelse ($data['riwayat_etl'] as $e)
                        <tr>
                            <td class="px-5 py-3">{{ $e->mulai_at ? \Illuminate\Support\Carbon::parse($e->mulai_at)->format('d/m/Y H:i') : '-' }}</td>
                            <td class="px-5 py-3">{{ $e->pemicu }}</td>
                            <td class="px-5 py-3 font-semibold {{ $e->status === 'gagal' ? 'text-red-600' : ($e->status === 'berhasil' ? 'text-emerald-600' : '') }}">{{ $e->status }}</td>
                            <td class="px-5 py-3 text-right">{{ $fmt($e->baris_diekstrak) }}</td>
                            <td class="px-5 py-3 text-right">{{ $fmt($e->baris_dimuat) }}</td>
                            <td class="px-5 py-3 text-[#7A9290]">{{ $e->pesan ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-5 py-8 text-center text-[#7A9290]">ETL belum pernah dijalankan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
