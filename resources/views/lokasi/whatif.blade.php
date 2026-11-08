@extends('layouts.app')

@section('page-title', 'Analisis What-if Lokasi')

@section('content')
@php
    $fmt = fn ($v, $d = 0) => number_format((float) $v, $d, ',', '.');
    $kelasInput = 'w-full rounded-xl border border-[#DCE6E4] bg-[#F8FAF9] px-3.5 py-2.5 text-sm text-[#365F65] outline-none transition focus:border-[#527D82] focus:bg-white focus:ring-2 focus:ring-[#527D82]/15';

    // Tautan drilldown: mempertahankan skenario (mode dan nilai) yang sedang dipakai.
    $url = fn (array $filterBaru) => route('lokasi.whatif', array_merge(
        $filterBaru,
        ['mode' => $mode, 'nilai' => $nilai]
    ));

    $labelMode = [
        'naik'   => 'Okupansi naik',
        'turun'  => 'Okupansi turun',
        'target' => 'Okupansi ditargetkan',
    ][$mode];

    $satuanSelisih = fn ($n) => ($n > 0 ? '+' : '') . $n;
@endphp

<div class="space-y-6">

    {{-- Judul --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-sm text-[#8BA5A2]">Management / Lokasi / What-if</p>
            <h1 class="mt-1 text-2xl font-bold text-[#365F65]">Analisis What-if Okupansi</h1>
            <p class="mt-1 text-sm text-[#7A9290]">
                Simulasikan perubahan okupansi ruangan. Skenario hanya perhitungan, data lokasi tidak diubah.
            </p>
        </div>
        <a href="{{ route('lokasi.index') }}"
           class="inline-flex items-center justify-center rounded-xl border border-[#DCE6E4] bg-white px-4 py-2.5 text-sm font-semibold text-[#365F65] hover:bg-[#F3F6F5]">
            Kembali ke Lokasi
        </a>
    </div>

    {{-- Form skenario --}}
    <form method="GET" action="{{ route('lokasi.whatif') }}"
          class="grid gap-4 rounded-2xl border border-[#E2EBE9] bg-white p-5 shadow-sm sm:grid-cols-3">
        @foreach ($filter as $kunci => $isi)
            <input type="hidden" name="{{ $kunci }}" value="{{ $isi }}">
        @endforeach

        <div>
            <label for="mode" class="mb-1.5 block text-sm font-semibold text-[#365F65]">Jenis skenario</label>
            <select id="mode" name="mode" class="{{ $kelasInput }}">
                <option value="naik" @selected($mode === 'naik')>Okupansi naik (poin persen)</option>
                <option value="turun" @selected($mode === 'turun')>Okupansi turun (poin persen)</option>
                <option value="target" @selected($mode === 'target')>Target okupansi (%)</option>
            </select>
        </div>

        <div>
            <label for="nilai" class="mb-1.5 block text-sm font-semibold text-[#365F65]">Nilai (0 - 100)</label>
            <input id="nilai" type="number" name="nilai" min="0" max="100" step="0.1"
                   value="{{ $nilai }}" class="{{ $kelasInput }}">
        </div>

        <div class="flex items-end">
            <button type="submit"
                    class="w-full rounded-xl bg-[#365F65] px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-[#294F55]">
                Hitung skenario
            </button>
        </div>
    </form>

    @if ($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ $errors->first() }}
        </div>
    @endif

    {{-- Ringkasan --}}
    <div class="grid gap-4 sm:grid-cols-3">
        <div class="rounded-2xl border border-[#E2EBE9] bg-white p-5 shadow-sm">
            <p class="text-sm text-[#7A9290]">Okupansi sekarang</p>
            <p class="mt-1 text-3xl font-bold text-[#365F65]">{{ $fmt($ringkasan['ok_sekarang'], 1) }}%</p>
            <p class="mt-1 text-xs text-[#8BA5A2]">{{ $fmt($ringkasan['terisi']) }} dari {{ $fmt($ringkasan['total']) }} ruangan terisi</p>
        </div>
        <div class="rounded-2xl border border-[#E2EBE9] bg-white p-5 shadow-sm">
            <p class="text-sm text-[#7A9290]">Okupansi skenario ({{ $labelMode }})</p>
            <p class="mt-1 text-3xl font-bold text-[#527D82]">{{ $fmt($ringkasan['ok_skenario'], 1) }}%</p>
            <p class="mt-1 text-xs text-[#8BA5A2]">{{ $fmt($ringkasan['terisi_skenario']) }} ruangan terisi</p>
        </div>
        <div class="rounded-2xl border border-[#E2EBE9] bg-white p-5 shadow-sm">
            <p class="text-sm text-[#7A9290]">Perubahan ruangan terisi</p>
            <p class="mt-1 text-3xl font-bold {{ $ringkasan['selisih'] < 0 ? 'text-red-600' : 'text-emerald-600' }}">
                {{ $satuanSelisih($ringkasan['selisih']) }}
            </p>
            <p class="mt-1 text-xs text-[#8BA5A2]">Estimasi luas terisi {{ $fmt($ringkasan['luas_skenario']) }} dari {{ $fmt($ringkasan['luas']) }} m²</p>
        </div>
    </div>

    {{-- Tabel drilldown + skenario --}}
    <section class="rounded-2xl border border-[#E2EBE9] bg-white shadow-sm">
        <div class="border-b border-[#E2EBE9] px-5 py-4">
            <h2 class="font-bold text-[#365F65]">Per {{ $level['judul'] }}</h2>

            <nav class="mt-2 flex flex-wrap items-center gap-1.5 text-sm" aria-label="Jejak drilldown">
                <a href="{{ $url([]) }}" class="font-semibold text-[#527D82] hover:underline">Semua Terminal</a>

                @if (isset($filter['terminal']))
                    <span class="text-[#B0C4C1]">/</span>
                    <a href="{{ $url(['terminal' => $filter['terminal']]) }}" class="font-semibold text-[#527D82] hover:underline">
                        {{ $filter['terminal'] === '-' ? 'Belum diisi' : $filter['terminal'] }}
                    </a>
                @endif

                @if (isset($filter['lantai']))
                    <span class="text-[#B0C4C1]">/</span>
                    <a href="{{ $url(['terminal' => $filter['terminal'] ?? '', 'lantai' => $filter['lantai']]) }}" class="font-semibold text-[#527D82] hover:underline">
                        Lantai {{ $filter['lantai'] === '-' ? 'belum diisi' : $filter['lantai'] }}
                    </a>
                @endif
            </nav>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-[#F8FAF9] text-left text-xs uppercase tracking-wide text-[#7A9290]">
                    <tr>
                        <th class="px-5 py-3">{{ $level['judul'] }}</th>
                        <th class="px-5 py-3 text-right">Total</th>
                        <th class="px-5 py-3 text-right">Terisi</th>
                        <th class="px-5 py-3 text-right">Okupansi sekarang</th>
                        <th class="px-5 py-3 text-right">Terisi skenario</th>
                        <th class="px-5 py-3 text-right">Okupansi skenario</th>
                        <th class="px-5 py-3 text-right">Perubahan</th>
                        <th class="px-5 py-3">Perbandingan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#EEF3F2] text-[#365F65]">
                    @forelse ($baris as $r)
                        <tr class="hover:bg-[#F8FAF9]">
                            <td class="px-5 py-3 font-semibold">
                                @if (! $dalamSendiri)
                                    @php
                                        $filterBawah = $filter;
                                        $filterBawah[$level['kunci']] = $r['nilai'];
                                    @endphp
                                    <a href="{{ $url($filterBawah) }}" class="text-[#527D82] hover:underline">
                                        {{ $r['nama'] }} &rsaquo;
                                    </a>
                                @else
                                    {{ $r['nama'] }}
                                @endif
                            </td>
                            <td class="px-5 py-3 text-right">{{ $fmt($r['total']) }}</td>
                            <td class="px-5 py-3 text-right">{{ $fmt($r['terisi']) }}</td>
                            <td class="px-5 py-3 text-right">{{ $fmt($r['ok_sekarang'], 1) }}%</td>
                            <td class="px-5 py-3 text-right">{{ $fmt($r['terisi_skenario']) }}</td>
                            <td class="px-5 py-3 text-right font-semibold">{{ $fmt($r['ok_skenario'], 1) }}%</td>
                            <td class="px-5 py-3 text-right font-semibold {{ $r['selisih'] < 0 ? 'text-red-600' : ($r['selisih'] > 0 ? 'text-emerald-600' : '') }}">
                                {{ $satuanSelisih($r['selisih']) }}
                            </td>
                            <td class="w-48 px-5 py-3">
                                <div class="h-2 w-full overflow-hidden rounded-full bg-[#EEF3F2]" title="Sekarang">
                                    <div class="h-2 rounded-full bg-[#9DB8B5]" style="width: {{ min(100, $r['ok_sekarang']) }}%"></div>
                                </div>
                                <div class="mt-1 h-2 w-full overflow-hidden rounded-full bg-[#EEF3F2]" title="Skenario">
                                    <div class="h-2 rounded-full bg-[#365F65]" style="width: {{ min(100, $r['ok_skenario']) }}%"></div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-10 text-center text-[#7A9290]">Belum ada data lokasi pada tingkat ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-[#E2EBE9] px-5 py-3 text-xs text-[#8BA5A2]">
            Skenario berlaku sama untuk setiap {{ strtolower($level['judul']) }}. Jumlah ruangan terisi pada skenario
            dibulatkan dari okupansi hasil skenario. Luas terisi adalah estimasi sebanding jumlah ruangan terisi.
            Batang gelap = skenario, batang terang = kondisi sekarang.
        </div>
    </section>
</div>
@endsection
