@extends('layouts.app')

@section('page-title', 'Dashboard')

@section('content')

@php
    $pct   = fn ($a, $t) => $t > 0 ? round($a / $t * 100, 1) : 0;
    $rp    = fn ($n) => number_format($n, 0, ',', '.');
    $ikon  = [
        'doc'   => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
        'check' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
        'x'     => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',
        'build' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5',
        'store' => 'M3 9l1-5h16l1 5M3 9a3 3 0 006 0 3 3 0 006 0 3 3 0 006 0M5 21V12m14 9V12M9 21v-5h6v5',
        'touch' => 'M7 11.5V14m0-2.5v-6a1.5 1.5 0 113 0m-3 6a1.5 1.5 0 00-3 0v2a7 7 0 007 7h3a7 7 0 007-7v-3.5a1.5 1.5 0 00-3 0m-6-3V11m0-5.5v-1a1.5 1.5 0 013 0v1m0 0V11m0-5.5a1.5 1.5 0 013 0v3m0 0V11',
    ];
    $warnaTerminal = ['#527D82', '#8FB339', '#D6A14D'];
    $terminalList = $perTerminal->keys()->values();
    $segmen = $terminalList->map(fn ($n, $i) => [
        'label' => $n, 'value' => $perTerminal[$n]['aktif'], 'color' => ['#365F65', '#D6A14D', '#B8CECA'][$i % 3],
    ])->all();
    $pctAktif = $pct($aktif, $total);
@endphp

<div class="min-h-screen bg-[#F3F6F5] py-6">
<div class="mx-auto max-w-[1400px] space-y-4">

    <div>
        <h1 class="text-2xl font-bold text-[#365F65]">Dashboard Kontrak</h1>
        <p class="mt-1 text-sm text-[#8BA5A2]">Monitoring kontrak, aktivasi, dan potensi pendapatan mitra.</p>
    </div>

    {{-- ===================== BARIS 1 ===================== --}}
    <div class="flex flex-wrap gap-3">

        @foreach ([['Total Kontrak', $total, 'doc'], ['Kontrak Aktif', $aktif, 'check']] as [$lbl, $val, $ic])
            <div class="flex min-w-[150px] flex-1 flex-col items-center justify-center rounded-2xl border border-[#DCE6E4] bg-white p-4 shadow-sm">
                <div class="flex items-center gap-2 text-[#527D82]">
                    <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $ikon[$ic] }}"/></svg>
                    <span class="text-sm font-semibold">{{ $lbl }}</span>
                </div>
                <p class="mt-3 text-4xl font-bold text-[#365F65]">{{ $val }}</p>
            </div>
        @endforeach

        {{-- Donut --}}
        <div class="flex min-w-[260px] flex-[1.6] items-center justify-center gap-3 rounded-2xl border border-[#DCE6E4] bg-white p-4 shadow-sm">
            <x-donut :segments="$segmen" :center="$aktif" sub="aktif" size="h-24 w-24"/>
            <ul class="space-y-1.5 text-[11px] text-[#7A9290]">
                @foreach ($segmen as $s)
                    <li class="flex items-center gap-1.5">
                        <span class="h-2.5 w-2.5 rounded-full" style="background: {{ $s['color'] }}"></span>
                        {{ $s['label'] }} <b class="text-[#365F65]">{{ $pct($s['value'], max($aktif,1)) }}%</b>
                    </li>
                @endforeach
            </ul>
        </div>

        {{-- Progress SUB (highlight) + per terminal --}}
        @php
            $kartuProgres = collect([['Progress Aktivasi Kontrak SUB', $aktif, $total, '#527D82', true]])
                ->concat($perTerminal->map(fn ($d, $n) => [$n, $d['aktif'], $d['total'], null, false])->values()->map(function ($r, $i) use ($warnaTerminal) { $r[3] = $warnaTerminal[$i]; return $r; }));
        @endphp
        @foreach ($kartuProgres as [$lbl, $a, $t, $warna, $hl])
            <div class="min-w-[170px] {{ $hl ? 'flex-[1.4]' : 'flex-1' }} rounded-2xl border p-4 shadow-sm {{ $hl ? 'border-[#EBD9AE] bg-[#FBF1DC]' : 'border-[#DCE6E4] bg-white' }}">
                <p class="truncate text-xs font-semibold text-[#527D82]">{{ $lbl }}</p>
                <p class="mt-1 text-3xl font-bold text-[#365F65]">{{ $a }}</p>
                <p class="text-xs font-semibold" style="color: {{ $warna }}">{{ $pct($a, $t) }}%</p>
                <div class="mt-2 h-2 overflow-hidden rounded-full {{ $hl ? 'bg-white/70' : 'bg-[#E2EBE9]' }}">
                    <div class="h-full rounded-full" style="width: {{ $pct($a, $t) }}%; background: {{ $warna }}"></div>
                </div>
                <p class="mt-1 text-right text-[11px] text-[#8BA5A2]">{{ $t }}</p>
            </div>
        @endforeach
    </div>

    {{-- ===================== BARIS 2-3 ===================== --}}
    <div class="grid grid-cols-1 gap-4 xl:grid-cols-[minmax(0,3fr)_minmax(0,9fr)]">

        {{-- KOLOM KIRI --}}
        <div class="space-y-4">
            <div class="rounded-2xl border border-[#C9DAD7] bg-[#DCE9E6] p-4 shadow-sm">
                <div class="grid grid-cols-2 gap-3 xl:grid-cols-1">
                    @foreach ([['Belum Aktif', $belumAktif, 'x', 'text-[#B96B60]'], ['Pusat', $pusat, 'build', 'text-[#365F65]'], ['Cabang', $cabang, 'store', 'text-[#365F65]'], ['Proses Aktivasi', $prosesAktivasi, 'touch', 'text-[#365F65]']] as [$lbl, $val, $ic, $cls])
                        <div class="flex items-center gap-3 rounded-xl bg-white/60 px-3 py-2">
                            <svg class="h-9 w-9 shrink-0 text-[#527D82]" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $ikon[$ic] }}"/></svg>
                            <div class="flex-1 text-center">
                                <p class="text-xs font-semibold {{ $cls }}">{{ $lbl }}</p>
                                <p class="text-2xl font-bold text-[#365F65]">{{ $val }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="overflow-hidden rounded-2xl border border-[#DCE6E4] bg-white shadow-sm">
                <p class="px-4 py-3 text-sm font-bold text-[#365F65]">Potensi Pending Kontrak</p>
                <table class="w-full text-xs">
                    <thead class="bg-[#F3F6F5] text-[#365F65]">
                        <tr><th class="px-4 py-2 text-left font-bold">Mitra Usaha</th><th class="px-4 py-2 text-right font-bold">Total Pendapatan/Bln</th></tr>
                    </thead>
                    <tbody>
                        @forelse ($pending->take(5) as $mitra => $jml)
                            <tr class="{{ $loop->even ? 'bg-[#F8FAF9]' : '' }}">
                                <td class="px-4 py-2 text-[#527D82]">{{ $mitra }}</td>
                                <td class="px-4 py-2 text-right text-[#365F65]">{{ $rp($jml) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="px-4 py-6 text-center text-[#8BA5A2]">Tidak ada tagihan pending.</td></tr>
                        @endforelse
                        <tr class="border-t border-[#DCE6E4] font-bold text-[#365F65]">
                            <td class="px-4 py-2.5">Grand total</td><td class="px-4 py-2.5 text-right">{{ $rp($pending->sum()) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- KOLOM KANAN --}}
        <div class="space-y-4">

            {{-- Band NO KON SYSTEM + jenis usaha --}}
            <div class="flex flex-col gap-4 rounded-2xl border border-[#C9DAD7] bg-[#DCE9E6] p-4 shadow-sm lg:flex-row lg:items-center">
                <div class="flex items-center gap-4 lg:w-[270px] lg:shrink-0">
                    <div class="text-center">
                        <p class="text-[11px] font-bold text-[#365F65]">NO KON SYSTEM</p>
                        <p class="text-6xl font-bold text-[#2F9E5A]">{{ $noKonKontrak + $noKonBA }}</p>
                    </div>
                    <div class="flex-1 space-y-2">
                        @foreach ([['Kontrak', $noKonKontrak], ['Berita Acara', $noKonBA]] as [$l, $v])
                            <div class="flex items-center gap-2">
                                <span class="flex h-9 w-12 items-center justify-center rounded-lg bg-[#7FC48F] text-lg font-semibold text-[#365F65]">{{ $v }}</span>
                                <span class="text-[11px] font-semibold text-[#365F65]">{{ $l }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="grid flex-1 gap-3" style="grid-template-columns: repeat(auto-fit, minmax(120px, 1fr))">
                    @foreach ($perJenis as $nama => $d)
                        @php $p = $pct($d['aktif'], $d['total']); @endphp
                        <div class="rounded-xl bg-white p-3 shadow-sm">
                            <p class="truncate text-[11px] font-semibold text-[#527D82]">{{ $nama }}</p>
                            <p class="mt-0.5 text-2xl font-bold text-[#365F65]">{{ $d['aktif'] }}</p>
                            <p class="text-[11px] font-semibold {{ $p >= 100 ? 'text-[#365F65]' : 'text-[#FF750F]' }}">{{ $p }}%</p>
                            <div class="mt-1.5 h-1.5 overflow-hidden rounded-full bg-[#E2EBE9]"><div class="h-full rounded-full bg-[#527D82]" style="width: {{ $p }}%"></div></div>
                            <p class="mt-1 text-right text-[10px] text-[#8BA5A2]">{{ $d['total'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 lg:grid-cols-[minmax(0,4fr)_minmax(0,6fr)]">

                {{-- Update status --}}
                <div class="flex flex-col overflow-hidden rounded-2xl border border-[#DCE6E4] bg-white shadow-sm">
                    <div class="flex justify-between bg-[#D6A14D] px-4 py-2.5 text-xs font-bold uppercase text-white">
                        <span>Update Status Kontrak</span><span>Nomor Kontrak ▾</span>
                    </div>
                    <div class="flex flex-1 flex-col text-sm">
                        @foreach ($perStatus as $s => $n)
                            <div class="flex min-h-[44px] flex-1 items-center justify-between px-4 {{ $loop->even ? 'bg-[#F8FAF9]' : '' }}">
                                <span class="text-[#527D82]">{{ $s }}</span>
                                <span class="text-[#365F65]">{{ $n }}</span>
                            </div>
                        @endforeach
                        <div class="flex min-h-[48px] items-center justify-between border-t border-[#DCE6E4] px-4 font-bold text-[#365F65]">
                            <span>Grand total</span><span>{{ $total }}</span>
                        </div>
                    </div>
                </div>

                {{-- Periode + daftar kontrak (paginasi Alpine) --}}
                <div class="space-y-4">
                    <div class="overflow-hidden rounded-2xl border border-[#DCE6E4] bg-white shadow-sm">
                        <div class="flex justify-between bg-[#527D82] px-4 py-2.5 text-xs font-bold text-white">
                            <span>Status Periode Kontrak</span><span>Jumlah Kontrak ▾</span>
                        </div>
                        <table class="w-full text-sm">
                            @foreach ($periode as $s => $n)
                                <tr class="{{ $loop->even ? 'bg-[#F8FAF9]' : '' }}">
                                    <td class="w-10 px-4 py-2 text-[#8BA5A2]">{{ $loop->iteration }}.</td>
                                    <td class="py-2 text-[#527D82]">{{ $s }}</td>
                                    <td class="px-4 py-2 text-right text-[#365F65]">{{ $n }}</td>
                                </tr>
                            @endforeach
                            <tr class="border-t border-[#DCE6E4] font-bold text-[#365F65]">
                                <td></td><td class="py-2.5">Grand total</td><td class="px-4 py-2.5 text-right">{{ $total }}</td>
                            </tr>
                        </table>
                    </div>

                    <div x-data="{ p: 0, per: 3, n: {{ $daftarKontrak->count() }} }" class="overflow-hidden rounded-2xl border border-[#DCE6E4] bg-white shadow-sm">
                        <table class="w-full text-xs">
                            <thead class="bg-[#E2EBE9] text-[#365F65]">
                                <tr><th class="px-3 py-2.5 text-left">No</th><th class="px-3 text-left">No Kontrak</th><th class="px-3 text-left">Mitra</th><th class="px-3 text-left">Kode Ruang</th><th class="px-3 text-left">Status</th></tr>
                            </thead>
                            <tbody>
                                @foreach ($daftarKontrak as $i => $k)
                                    <tr x-show="{{ $i }} >= p*per && {{ $i }} < (p+1)*per" class="{{ $i % 2 ? 'bg-[#F8FAF9]' : '' }}" @if($i >= 3) style="display:none" @endif>
                                        <td class="px-3 py-2 text-[#8BA5A2]">{{ $i + 1 }}.</td>
                                        <td class="px-3 text-[#365F65]">{{ $k->nomor_kontrak }}</td>
                                        <td class="px-3 text-[#527D82]">{{ $k->nama_mitra_snapshot ?? $k->kerjaSama->mitra_usaha ?? '-' }}</td>
                                        <td class="px-3 text-[#527D82]">{{ $k->lokasi->kode_ruang ?? '-' }}</td>
                                        <td class="px-3 text-[#527D82]">{{ $k->status }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="flex items-center justify-end gap-3 border-t border-[#E2EBE9] px-3 py-2 text-[11px] text-[#8BA5A2]">
                            <span x-text="(n ? p*per+1 : 0) + ' - ' + Math.min((p+1)*per, n) + ' / ' + n"></span>
                            <button @click="p = Math.max(0, p-1)" class="px-1 text-base hover:text-[#365F65]">‹</button>
                            <button @click="if ((p+1)*per < n) p++" class="px-1 text-base hover:text-[#365F65]">›</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
</div>
@endsection