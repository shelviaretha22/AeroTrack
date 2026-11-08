@extends('layouts.app')

@section('page-title', 'Dashboard')

@section('content')

@php
    $pct = fn ($a, $t) => $t > 0 ? round($a / $t * 100, 1) : 0;
    $rp  = fn ($n) => 'Rp ' . number_format($n, 0, ',', '.');

    // Warna status kontrak: dipakai SAMA di semua kartu, tabel, dan badge.
    $warnaStatus = [
        'Aktif'              => '#2F9E5A',
        'Proses Pusat'       => '#D6A14D',
        'Proses Cabang'      => '#527D82',
        'Negosiasi'          => '#8A6BB5',
    ];
    $warnaTerminal = ['#365F65', '#D6A14D', '#8FB339', '#8A6BB5'];

    $ikon = [
        'doc'   => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
        'check' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
        'x'     => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',
        'build' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5',
        'touch' => 'M7 11.5V14m0-2.5v-6a1.5 1.5 0 113 0m-3 6a1.5 1.5 0 00-3 0v2a7 7 0 007 7h3a7 7 0 007-7v-3.5a1.5 1.5 0 00-3 0m-6-3V11m0-5.5v-1a1.5 1.5 0 013 0v1m0 0V11m0-5.5a1.5 1.5 0 013 0v3m0 0V11',
        'hash'  => 'M7 20l4-16m2 16l4-16M6 9h14M4 15h14',
    ];

    $segmenLokasi = $distribusiLokasi->keys()->values()->map(fn ($n, $i) => [
        'label' => $n,
        'value' => $distribusiLokasi[$n],
        'color' => $warnaTerminal[$i % count($warnaTerminal)],
    ])->all();

    $pctAktif = $pct($aktif, $total);
    $selectCls = 'w-full rounded-lg border border-[#DCE6E4] bg-white px-2.5 py-2 text-xs text-[#365F65] focus:border-[#527D82] focus:outline-none';

    // Badge status (warna dari $warnaStatus, latar transparan agar aman di tema gelap)
    $badge = function (string $s) use ($warnaStatus) {
        $c = $warnaStatus[$s] ?? '#7A9290';
        return '<span class="inline-flex items-center gap-1.5 whitespace-nowrap rounded-full px-2.5 py-0.5 text-[11px] font-semibold" style="color:' . $c . ';background:' . $c . '22">'
            . '<span class="h-1.5 w-1.5 rounded-full" style="background:' . $c . '"></span>' . e($s) . '</span>';
    };
@endphp

<div class="min-h-screen bg-[#F3F6F5] py-6">
<div class="mx-auto max-w-[1400px] space-y-4">

    <div>
        <h1 class="text-2xl font-bold text-[#365F65]">Dashboard Kontrak</h1>
        <p class="mt-1 text-sm text-[#8BA5A2]">Monitoring kontrak, aktivasi, dan potensi pendapatan mitra.</p>
    </div>

    {{-- ===================== FILTER ===================== --}}
    <form method="GET" action="{{ route('dashboard') }}" class="rounded-2xl border border-[#DCE6E4] bg-white p-4 shadow-sm">
        <div class="grid grid-cols-2 gap-3 md:grid-cols-3 xl:grid-cols-[repeat(5,minmax(0,1fr))_auto]">
            <label class="block">
                <span class="mb-1 block text-[11px] font-semibold text-[#527D82]">Terminal</span>
                <select name="terminal" class="{{ $selectCls }}" onchange="this.form.submit()">
                    <option value="">Semua terminal</option>
                    @foreach ($opsiTerminal as $o)
                        <option value="{{ $o }}" @selected($filter['terminal'] === $o)>{{ $o }}</option>
                    @endforeach
                </select>
            </label>
            <label class="block">
                <span class="mb-1 block text-[11px] font-semibold text-[#527D82]">Lokasi</span>
                <select name="lokasi" class="{{ $selectCls }}" onchange="this.form.submit()">
                    <option value="">Semua lokasi</option>
                    @foreach ($opsiLokasi as $o)
                        <option value="{{ $o }}" @selected($filter['lokasi'] === $o)>{{ $o }}</option>
                    @endforeach
                </select>
            </label>
            <label class="block">
                <span class="mb-1 block text-[11px] font-semibold text-[#527D82]">Kategori Tenant</span>
                <select name="kategori" class="{{ $selectCls }}" onchange="this.form.submit()">
                    <option value="">Semua kategori</option>
                    @foreach ($opsiKategori as $kunci => $label)
                        <option value="{{ $kunci }}" @selected($filter['kategori'] === $kunci)>{{ $label }}</option>
                    @endforeach
                </select>
            </label>
            <label class="block">
                <span class="mb-1 block text-[11px] font-semibold text-[#527D82]">Status Kontrak</span>
                <select name="status" class="{{ $selectCls }}" onchange="this.form.submit()">
                    <option value="">Semua status</option>
                    @foreach ($opsiStatus as $o)
                        <option value="{{ $o }}" @selected($filter['status'] === $o)>{{ $o }}</option>
                    @endforeach
                </select>
            </label>
            <label class="block">
                <span class="mb-1 block text-[11px] font-semibold text-[#527D82]">Periode Kontrak</span>
                <select name="periode" class="{{ $selectCls }}" onchange="this.form.submit()">
                    <option value="">Semua periode</option>
                    @foreach ($opsiPeriode as $o)
                        <option value="{{ $o }}" @selected($filter['periode'] === $o)>{{ $o }}</option>
                    @endforeach
                </select>
            </label>
            <div class="flex items-end gap-2">
                <noscript><button class="rounded-lg bg-[#527D82] px-3 py-2 text-xs font-semibold text-white">Terapkan</button></noscript>
                @if ($adaFilter)
                    <a href="{{ route('dashboard') }}" class="inline-flex w-full items-center justify-center rounded-lg border border-[#DCE6E4] px-3 py-2 text-xs font-semibold text-[#B96B60] hover:bg-[#F3F6F5]">Reset filter</a>
                @endif
            </div>
        </div>
    </form>

    {{-- ===================== 1. KARTU RINGKASAN ===================== --}}
    <div class="grid grid-cols-2 gap-3 lg:grid-cols-4 xl:grid-cols-6">
        @foreach ([['Total Kontrak', $total, 'doc'], ['Kontrak Aktif', $aktif, 'check']] as [$lbl, $val, $ic])
            <div class="flex flex-col items-center justify-center rounded-2xl border border-[#DCE6E4] bg-white p-4 shadow-sm">
                <div class="flex items-center gap-2 text-[#527D82]">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $ikon[$ic] }}"/></svg>
                    <span class="text-sm font-semibold">{{ $lbl }}</span>
                </div>
                <p class="mt-2 text-4xl font-bold text-[#365F65]">{{ $val }}</p>
            </div>
        @endforeach

        {{-- Progress Aktivasi Kontrak SUB --}}
        <div class="col-span-2 rounded-2xl border border-[#EBD9AE] bg-[#FBF1DC] p-4 shadow-sm lg:col-span-2">
            <p class="text-xs font-semibold text-[#527D82]">Progress Aktivasi Kontrak SUB</p>
            <div class="mt-1 flex items-end justify-between">
                <p class="text-3xl font-bold text-[#365F65]">{{ $aktif }}<span class="text-base font-semibold text-[#8BA5A2]"> / {{ $total }}</span></p>
                <p class="text-lg font-bold text-[#2F9E5A]">{{ $pctAktif }}%</p>
            </div>
            <div class="mt-2 h-2.5 overflow-hidden rounded-full bg-white/70">
                <div class="h-full rounded-full bg-[#2F9E5A]" style="width: {{ $pctAktif }}%"></div>
            </div>
            <p class="mt-1 text-[11px] text-[#8BA5A2]">Kontrak teraktivasi dari seluruh kontrak</p>
        </div>

        {{-- Per terminal --}}
        @foreach ($perTerminal as $nama => $d)
            @php $w = $warnaTerminal[$loop->index % count($warnaTerminal)]; $pt = $pct($d['aktif'], $d['total']); @endphp
            <div class="rounded-2xl border border-[#DCE6E4] bg-white p-4 shadow-sm">
                <p class="truncate text-xs font-semibold text-[#527D82]">{{ $nama }}</p>
                <div class="mt-1 flex items-end justify-between">
                    <p class="text-3xl font-bold text-[#365F65]">{{ $d['total'] }}</p>
                    <p class="text-sm font-bold" style="color: {{ $w }}">{{ $pt }}%</p>
                </div>
                <div class="mt-2 h-2 overflow-hidden rounded-full bg-[#E2EBE9]">
                    <div class="h-full rounded-full" style="width: {{ $pt }}%; background: {{ $w }}"></div>
                </div>
                <p class="mt-1 text-[11px] text-[#8BA5A2]">{{ $d['aktif'] }} aktif dari {{ $d['total'] }} kontrak</p>
            </div>
        @endforeach
    </div>

    {{-- ===================== 2. PIE + 4. KARTU INFO ===================== --}}
    <div class="grid grid-cols-1 gap-4 lg:grid-cols-[minmax(0,5fr)_minmax(0,7fr)]">

        <div class="rounded-2xl border border-[#DCE6E4] bg-white p-4 shadow-sm">
            <p class="text-sm font-bold text-[#365F65]">Distribusi Kontrak per Lokasi</p>
            @if ($total > 0)
                <div class="mt-3 flex flex-col items-center gap-5 sm:flex-row sm:justify-center">
                    <x-pie :segments="$segmenLokasi" size="h-44 w-44"/>
                    <ul class="w-full space-y-2 text-xs sm:w-auto">
                        @foreach ($segmenLokasi as $s)
                            <li class="flex items-center gap-2">
                                <span class="h-3 w-3 shrink-0 rounded-full" style="background: {{ $s['color'] }}"></span>
                                <span class="flex-1 text-[#527D82]">{{ $s['label'] }}</span>
                                <span class="font-bold text-[#365F65]">{{ $s['value'] }}</span>
                                <span class="w-12 text-right font-semibold text-[#7A9290]">{{ $pct($s['value'], $total) }}%</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @else
                <p class="py-12 text-center text-sm text-[#8BA5A2]">Tidak ada data untuk filter ini.</p>
            @endif
        </div>

        <div class="rounded-2xl border border-[#C9DAD7] bg-[#DCE9E6] p-4 shadow-sm">
            <p class="mb-3 text-sm font-bold text-[#365F65]">Informasi Kontrak</p>
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                @foreach ([
                    ['Belum Aktif', $info['belumAktif'], 'x', '#B96B60', 'Belum teraktivasi'],
                    ['Pusat', $info['pusat'], 'build', $warnaStatus['Proses Pusat'], 'Menunggu keputusan Commercial Pusat'],
                    ['Proses Aktivasi', $info['prosesAktivasi'], 'touch', $warnaStatus['Proses Cabang'], 'Sedang di cabang atau pusat'],
                    ['No Kontrak / No Konsumen Sistem', $info['noKontrak'] . ' / ' . $info['noKonsumen'], 'hash', '#2F9E5A', $info['noKontrak'] . ' kontrak bernomor, ' . $info['noKonsumen'] . ' terdaftar di sistem'],
                ] as [$lbl, $val, $ic, $warna, $ket])
                    <div class="flex items-center gap-3 rounded-xl bg-white/70 px-3 py-3">
                        <svg class="h-9 w-9 shrink-0" style="color: {{ $warna }}" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $ikon[$ic] }}"/></svg>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-semibold text-[#527D82]">{{ $lbl }}</p>
                            <p class="text-2xl font-bold text-[#365F65]">{{ $val }}</p>
                            <p class="truncate text-[10px] text-[#8BA5A2]" title="{{ $ket }}">{{ $ket }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ===================== 3. MONITORING PER KATEGORI ===================== --}}
    <div>
        <p class="mb-2 text-sm font-bold text-[#365F65]">Monitoring per Kategori Tenant</p>
        <div class="grid grid-cols-2 gap-3 md:grid-cols-3 xl:grid-cols-6">
            @foreach ($perKategori as $kunci => $d)
                @php $p = $pct($d['aktif'], $d['total']); @endphp
                <div class="rounded-2xl border border-[#DCE6E4] bg-white p-3.5 shadow-sm">
                    <p class="truncate text-xs font-semibold text-[#527D82]">{{ $d['label'] }}</p>
                    <div class="mt-1 flex items-end justify-between">
                        <p class="text-2xl font-bold text-[#365F65]">{{ $d['total'] }}</p>
                        <p class="text-sm font-bold {{ $d['total'] > 0 && $p >= 100 ? 'text-[#2F9E5A]' : 'text-[#D6812B]' }}">{{ $p }}%</p>
                    </div>
                    <div class="mt-1.5 h-1.5 overflow-hidden rounded-full bg-[#E2EBE9]">
                        <div class="h-full rounded-full bg-[#2F9E5A]" style="width: {{ $p }}%"></div>
                    </div>
                    <p class="mt-1 text-[10px] text-[#8BA5A2]">{{ $d['aktif'] }} aktif dari {{ $d['total'] }} kontrak</p>
                </div>
            @endforeach
        </div>
    </div>

    {{-- ===================== 5. POTENSI PENDAPATAN ===================== --}}
    <div class="grid grid-cols-1 gap-4 xl:grid-cols-[minmax(0,6fr)_minmax(0,6fr)]">
        <div id="tbl-potensi" class="flex flex-col overflow-hidden rounded-2xl border border-[#DCE6E4] bg-white shadow-sm">
            <div class="flex items-center justify-between bg-[#527D82] px-4 py-2.5 text-xs font-bold text-white">
                <span>Potensi Pendapatan Kontrak</span><span class="font-medium opacity-80">Rupiah</span>
            </div>
            @php $pctTerima = $grandTotalPotensi > 0 ? min(100, round($grandTotalDiterima / $grandTotalPotensi * 100, 1)) : 0; @endphp
            <div class="border-b border-[#E2EBE9] px-4 py-3">
                <div class="flex flex-wrap items-end justify-between gap-2 text-xs">
                    <span class="text-[#527D82]">Sudah diterima <b class="text-[#2F9E5A]">{{ $rp($grandTotalDiterima) }}</b> dari potensi {{ $rp($grandTotalPotensi) }} per bulan</span>
                    <b class="text-[#365F65]">{{ $pctTerima }}%</b>
                </div>
                <div class="mt-1.5 h-2 overflow-hidden rounded-full bg-[#E2EBE9]"><div class="h-full rounded-full bg-[#2F9E5A]" style="width: {{ $pctTerima }}%"></div></div>
                <p class="mt-1 text-[10px] text-[#8BA5A2]">Diterima = tagihan yang invoicenya sudah diberikan (menu Pendapatan), seluruh periode.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead class="bg-[#F3F6F5] text-[#365F65]">
                        <tr>
                            <th class="px-4 py-2 text-left font-bold">Mitra / Tenant</th>
                            <th class="px-4 py-2 text-right font-bold">Potensi / Bulan</th>
                            <th class="px-4 py-2 text-right font-bold">Sudah Diterima</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($potensi as $b)
                            <tr class="{{ $loop->even ? 'bg-[#F8FAF9]' : '' }}">
                                <td class="px-4 py-2 text-[#527D82]">{{ $b['mitra'] }}</td>
                                <td class="whitespace-nowrap px-4 py-2 text-right text-[#365F65]">{{ $rp($b['potensi']) }}</td>
                                <td class="whitespace-nowrap px-4 py-2 text-right font-semibold {{ $b['diterima'] > 0 ? 'text-[#2F9E5A]' : 'text-[#B7C6C3]' }}">{{ $rp($b['diterima']) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-4 py-8 text-center text-[#8BA5A2]">Belum ada data GTO / revenue sharing.</td></tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="border-t border-[#DCE6E4] font-bold text-[#365F65]">
                            <td class="px-4 py-2.5">Grand Total ({{ $jumlahPotensi }} mitra)</td>
                            <td class="whitespace-nowrap px-4 py-2.5 text-right">{{ $rp($grandTotalPotensi) }}</td>
                            <td class="whitespace-nowrap px-4 py-2.5 text-right text-[#2F9E5A]">{{ $rp($grandTotalDiterima) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="mt-auto"><x-pager :paginator="$potensi"/></div>
        </div>

        {{-- 6 + 7. STATUS & PERIODE --}}
        <div class="space-y-4">
            <div class="overflow-hidden rounded-2xl border border-[#DCE6E4] bg-white shadow-sm">
                <div class="flex justify-between bg-[#D6A14D] px-4 py-2.5 text-xs font-bold text-white">
                    <span>Monitoring Status Kontrak</span><span>Jumlah Kontrak</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-xs">
                        <thead class="bg-[#F3F6F5] text-[#365F65]">
                            <tr>
                                <th class="px-4 py-2 text-left font-bold">Update Status Kontrak</th>
                                <th class="px-4 py-2 text-left font-bold">Nomor Kontrak</th>
                                <th class="px-4 py-2 text-right font-bold">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($tabelStatus as $r)
                                <tr class="align-top {{ $loop->even ? 'bg-[#F8FAF9]' : '' }}">
                                    <td class="px-4 py-2">{!! $badge($r['status']) !!}</td>
                                    <td class="px-4 py-2 text-[#527D82]">
                                        @if ($r['nomor']->isEmpty())
                                            <span class="text-[#B7C6C3]">-</span>
                                        @else
                                            {{ $r['nomor']->take(2)->implode(', ') }}
                                            @if ($r['nomor']->count() > 2)
                                                <span class="text-[#8BA5A2]" title="{{ $r['nomor']->implode(', ') }}">+{{ $r['nomor']->count() - 2 }} lagi</span>
                                            @endif
                                        @endif
                                    </td>
                                    <td class="px-4 py-2 text-right font-semibold text-[#365F65]">{{ $r['jumlah'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="border-t border-[#DCE6E4] font-bold text-[#365F65]">
                                <td class="px-4 py-2.5" colspan="2">Grand Total</td>
                                <td class="px-4 py-2.5 text-right">{{ $total }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="overflow-hidden rounded-2xl border border-[#DCE6E4] bg-white shadow-sm">
                <div class="flex justify-between bg-[#527D82] px-4 py-2.5 text-xs font-bold text-white">
                    <span>Status Periode Kontrak</span><span>Jumlah Kontrak</span>
                </div>
                <table class="w-full text-sm">
                    @foreach ($perPeriode as $s => $n)
                        <tr class="{{ $loop->even ? 'bg-[#F8FAF9]' : '' }}">
                            <td class="w-10 px-4 py-2 text-[#8BA5A2]">{{ $loop->iteration }}.</td>
                            <td class="py-2 text-[#527D82]">{{ $s }}</td>
                            <td class="px-4 py-2 text-right font-semibold text-[#365F65]">{{ $n }}</td>
                        </tr>
                    @endforeach
                    <tr class="border-t border-[#DCE6E4] font-bold text-[#365F65]">
                        <td></td><td class="py-2.5">Grand Total</td><td class="px-4 py-2.5 text-right">{{ $total }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    {{-- ===================== 8. DETAIL MONITORING TENANT ===================== --}}
    <div id="tbl-detail" class="overflow-hidden rounded-2xl border border-[#DCE6E4] bg-white shadow-sm">
        <div class="bg-[#365F65] px-4 py-2.5 text-xs font-bold text-white">Detail Monitoring Tenant</div>
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead class="bg-[#E2EBE9] text-[#365F65]">
                    <tr>
                        <th class="px-4 py-2.5 text-left">No</th>
                        <th class="px-4 py-2.5 text-left">No Konsumen</th>
                        <th class="px-4 py-2.5 text-left">Mitra / Tenant</th>
                        <th class="px-4 py-2.5 text-left">Kode Ruang</th>
                        <th class="px-4 py-2.5 text-left">Status Kontrak</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($detail as $b)
                        <tr class="{{ $loop->even ? 'bg-[#F8FAF9]' : '' }}">
                            <td class="px-4 py-2 text-[#8BA5A2]">{{ $detail->firstItem() + $loop->index }}.</td>
                            <td class="whitespace-nowrap px-4 py-2 text-[#365F65]">{{ $b['no_konsumen'] ?: '-' }}</td>
                            <td class="px-4 py-2 text-[#527D82]">
                                <a href="{{ route('aktivasi.show', $b['id']) }}" class="hover:underline">{{ $b['mitra'] }}</a>
                                @if ($b['brand'])<span class="text-[#8BA5A2]"> · {{ $b['brand'] }}</span>@endif
                            </td>
                            <td class="whitespace-nowrap px-4 py-2 text-[#527D82]">{{ $b['kode_ruang'] ?: '-' }}</td>
                            <td class="px-4 py-2">{!! $badge($b['status']) !!}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-8 text-center text-[#8BA5A2]">Tidak ada data untuk filter ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <x-pager :paginator="$detail"/>
    </div>

</div>
</div>
@endsection
