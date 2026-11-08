@extends('layouts.app')

@section('page-title', 'Data Lokasi')

@section('content')
@php
    // Pengurutan lewat judul kolom: klik sekali menaik, klik lagi menurun.
    $sortAktif = request('sort', 'newest');
    $urlUrut = function (string $naik, string $turun) use ($sortAktif) {
        return request()->fullUrlWithQuery([
            'sort' => $sortAktif === $naik ? $turun : $naik,
            'page' => null,
        ]);
    };
    $panahUrut = function (string $naik, string $turun) use ($sortAktif) {
        return $sortAktif === $naik ? '↑' : ($sortAktif === $turun ? '↓' : '');
    };

    $filterAktif = collect([
        'search' => 'Cari',
        'terminal' => 'Terminal',
        'lantai' => 'Lantai',
        'jenis_ruangan' => 'Jenis',
        'status' => 'Status',
    ])->filter(fn ($label, $key) => request()->filled($key));

    $kelasInput = 'w-full rounded-xl border border-[#DCE6E4] bg-[#F8FAF9] px-3.5 py-2.5 text-sm text-[#365F65] outline-none transition focus:border-[#527D82] focus:bg-white focus:ring-2 focus:ring-[#527D82]/15';

    $luasFormat = fn ($v) => number_format((float) $v, 0, ',', '.');
@endphp

<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-sm text-[#8BA5A2]">Management / Lokasi</p>
            <h1 class="mt-1 text-2xl font-bold text-[#365F65]">Data Lokasi</h1>
            <p class="mt-1 text-sm text-[#7A9290]">
                Kelola data ruangan fisik yang tersedia di bandara.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('lokasi.whatif') }}"
               class="inline-flex items-center justify-center rounded-xl border border-[#DCE6E4] bg-white px-4 py-3 text-sm font-semibold text-[#365F65] shadow-sm transition hover:bg-[#F3F6F5]">
                Analisis What-if
            </a>
            <a href="{{ route('lokasi.laporan') }}"
               class="inline-flex items-center justify-center rounded-xl border border-[#DCE6E4] bg-white px-4 py-3 text-sm font-semibold text-[#365F65] shadow-sm transition hover:bg-[#F3F6F5]">
                Laporan Lokasi &amp; Aktivasi
            </a>
            <a href="{{ route('lokasi.create') }}"
               class="inline-flex items-center justify-center gap-2 rounded-xl bg-[#365F65] px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-[#294F55]">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round">
                    <path d="M12 5v14M5 12h14"/>
                </svg>
                Tambah Lokasi
            </a>
        </div>
    </div>

    {{-- Notifikasi --}}
    @foreach ([
        'success' => 'border-emerald-200 bg-emerald-50 text-emerald-800',
        'error' => 'border-red-200 bg-red-50 text-red-700',
        'info' => 'border-blue-200 bg-blue-50 text-blue-800',
    ] as $type => $kelas)
        @if (session($type))
            <div class="rounded-xl border px-4 py-3 text-sm {{ $kelas }}" role="status">
                {{ session($type) }}
            </div>
        @endif
    @endforeach

    {{-- Ringkasan --}}
    <section class="grid grid-cols-2 gap-3 sm:gap-4 xl:grid-cols-4" aria-label="Ringkasan lokasi">

        {{-- Total --}}
        <div class="rounded-2xl border border-[#E2EBE9] bg-white p-4 shadow-sm sm:p-5">
            <div class="flex items-center justify-between gap-3">
                <p class="text-sm font-medium text-[#7A9290]">Total Lokasi</p>
                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-[#EAF2F1] text-[#365F65]">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5"/>
                    </svg>
                </span>
            </div>
            <p class="mt-2 text-3xl font-bold text-[#365F65]">{{ number_format($totalLokasi, 0, ',', '.') }}</p>
            <p class="mt-1 text-xs text-[#8BA5A2]">Total luas {{ $luasFormat($totalLuas) }} m²</p>
        </div>

        {{-- Kosong --}}
        <a href="{{ request()->fullUrlWithQuery(['status' => 'Kosong', 'page' => null]) }}"
                class="block rounded-2xl border border-[#E2EBE9] bg-white p-4 text-left sm:p-5 shadow-sm transition hover:border-emerald-300 hover:shadow">
            <div class="flex items-center justify-between gap-3">
                <p class="text-sm font-medium text-[#7A9290]">Lokasi Kosong</p>
                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-emerald-50 text-emerald-700">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="9"/><path d="m8 12 2.5 2.5L16 9"/>
                    </svg>
                </span>
            </div>
            <p class="mt-2 text-3xl font-bold text-emerald-700">{{ number_format($totalKosong, 0, ',', '.') }}</p>
            <p class="mt-1 text-xs text-[#8BA5A2]">{{ $persentaseKosong }}% · siap ditempati</p>
        </a>

        {{-- Terisi --}}
        <a href="{{ request()->fullUrlWithQuery(['status' => 'Terisi', 'page' => null]) }}"
                class="block rounded-2xl border border-[#E2EBE9] bg-white p-4 text-left sm:p-5 shadow-sm transition hover:border-amber-300 hover:shadow">
            <div class="flex items-center justify-between gap-3">
                <p class="text-sm font-medium text-[#7A9290]">Lokasi Terisi</p>
                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-amber-50 text-amber-700">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="4" y="3" width="16" height="18" rx="2"/><path d="M9 21v-5h6v5M9 7h.01M15 7h.01M9 11h.01M15 11h.01"/>
                    </svg>
                </span>
            </div>
            <p class="mt-2 text-3xl font-bold text-amber-700">{{ number_format($totalTerisi, 0, ',', '.') }}</p>
            <div class="mt-2 h-1.5 overflow-hidden rounded-full bg-[#E7EFEC]">
                <div class="h-full rounded-full bg-amber-500" style="width: {{ $persentaseTerisi }}%"></div>
            </div>
            <p class="mt-1 text-xs text-[#8BA5A2]">Okupansi {{ $persentaseTerisi }}%</p>
        </a>

        {{-- Jenis ruangan --}}
        <div class="rounded-2xl border border-[#E2EBE9] bg-white p-4 shadow-sm sm:p-5">
            <div class="flex items-center justify-between gap-3">
                <p class="text-sm font-medium text-[#7A9290]">Jenis Ruangan</p>
                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-blue-50 text-blue-700">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/>
                        <rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>
                    </svg>
                </span>
            </div>
            <p class="mt-2 text-3xl font-bold text-blue-700">{{ number_format($totalJenisRuangan, 0, ',', '.') }}</p>
            <p class="mt-1 text-xs text-[#8BA5A2]">Kategori ruangan terdaftar</p>
        </div>
    </section>

    {{-- Analisis lokasi --}}
    @php
        $warnaUsaha = ['#365F65', '#D6A14D', '#31966A', '#527D82', '#B5503C', '#6C8EBF', '#A47720', '#8BA5A2'];
        $segmenStatus = [
            ['label' => 'Terisi', 'value' => $totalTerisi, 'color' => '#D6A14D'],
            ['label' => 'Kosong', 'value' => $totalKosong, 'color' => '#31966A'],
        ];
        $segmenUsaha = $perJenisUsaha->values()->map(fn ($r, $i) => [
            'label' => $r->jenis_usaha,
            'value' => (int) $r->total,
            'color' => $r->jenis_usaha === 'Belum diisi' ? '#C9D6D3' : $warnaUsaha[$i % count($warnaUsaha)],
        ])->all();
        $persenLuas = $totalLuas > 0 ? round(($luasTerisi / $totalLuas) * 100, 1) : 0;

        // Warna sel heatmap: makin penuh makin pekat.
        $gayaPanas = fn ($persen) => 'background-color: rgba(54,95,101,' . round(0.10 + ($persen / 100) * 0.80, 2) . ');';
    @endphp

    <section x-data="{ buka: true, tab: 'ringkasan' }" class="rounded-2xl border border-[#E2EBE9] bg-white shadow-sm">
        <div class="flex flex-col gap-3 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-bold text-[#365F65]">Analisis Lokasi</h2>
                <p class="mt-0.5 text-sm text-[#7A9290]">Komposisi status, okupansi per lantai, dan denah seluruh ruangan.</p>
            </div>

            <div class="flex items-center gap-2">
                <div x-show="buka" class="inline-flex rounded-xl bg-[#F3F6F5] p-1 text-sm font-semibold" role="tablist">
                    <button type="button" role="tab" @click="tab = 'ringkasan'"
                            :aria-selected="tab === 'ringkasan'"
                            :class="tab === 'ringkasan' ? 'bg-white text-[#365F65] shadow-sm' : 'text-[#7A9290] hover:text-[#365F65]'"
                            class="rounded-lg px-3.5 py-1.5 transition">Ringkasan</button>
                    <button type="button" role="tab" @click="tab = 'denah'"
                            :aria-selected="tab === 'denah'"
                            :class="tab === 'denah' ? 'bg-white text-[#365F65] shadow-sm' : 'text-[#7A9290] hover:text-[#365F65]'"
                            class="rounded-lg px-3.5 py-1.5 transition">Denah Okupansi</button>
                    <button type="button" role="tab" @click="tab = 'drilldown'"
                            :aria-selected="tab === 'drilldown'"
                            :class="tab === 'drilldown' ? 'bg-white text-[#365F65] shadow-sm' : 'text-[#7A9290] hover:text-[#365F65]'"
                            class="rounded-lg px-3.5 py-1.5 transition">Drilldown</button>
                </div>

                <button type="button" @click="buka = !buka" :aria-expanded="buka" aria-label="Buka atau tutup analisis"
                        class="flex h-9 w-9 items-center justify-center rounded-xl text-[#527D82] transition hover:bg-[#F3F6F5]">
                    <svg class="h-5 w-5 transition" :class="buka ? 'rotate-180' : ''" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m6 9 6 6 6-6"/>
                    </svg>
                </button>
            </div>
        </div>

        <div x-show="buka" x-transition class="border-t border-[#E2EBE9] p-5 sm:p-6">

            {{-- ===== TAB RINGKASAN ===== --}}
            <div x-show="tab === 'ringkasan'" class="space-y-6">

                <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">

                    {{-- Donat status --}}
                    <div class="rounded-xl border border-[#E2EBE9] p-5">
                        <h3 class="font-bold text-[#365F65]">Komposisi Status</h3>
                        <p class="mt-1 text-sm text-[#7A9290]">Perbandingan lokasi terisi dan kosong.</p>

                        <div class="mt-5 flex flex-col items-center gap-5">
                            <x-donut :segments="$segmenStatus" :center="$totalLokasi" sub="Total lokasi" />

                            <div class="w-full space-y-3">
                                <div class="flex items-center gap-3">
                                    <span class="h-3 w-3 shrink-0 rounded-full bg-[#D6A14D]"></span>
                                    <span class="flex-1 text-sm text-[#527D82]">Terisi</span>
                                    <span class="text-sm font-bold text-[#365F65]">{{ $totalTerisi }}</span>
                                    <span class="w-14 text-right text-sm font-semibold text-amber-700">{{ $persentaseTerisi }}%</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="h-3 w-3 shrink-0 rounded-full bg-[#31966A]"></span>
                                    <span class="flex-1 text-sm text-[#527D82]">Kosong</span>
                                    <span class="text-sm font-bold text-[#365F65]">{{ $totalKosong }}</span>
                                    <span class="w-14 text-right text-sm font-semibold text-emerald-700">{{ $persentaseKosong }}%</span>
                                </div>

                                <div class="border-t border-[#E2EBE9] pt-3">
                                    <div class="mb-1.5 flex items-center justify-between text-xs text-[#7A9290]">
                                        <span>Okupansi luas</span>
                                        <span class="font-semibold text-[#365F65]">{{ $persenLuas }}%</span>
                                    </div>
                                    <div class="h-2 overflow-hidden rounded-full bg-[#E7EFEC]">
                                        <div class="h-full rounded-full bg-[#365F65]" style="width: {{ $persenLuas }}%"></div>
                                    </div>
                                    <p class="mt-1.5 text-xs text-[#8BA5A2]">
                                        {{ $luasFormat($luasTerisi) }} dari {{ $luasFormat($totalLuas) }} m² terisi
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Heatmap terminal x lantai --}}
                    <div class="rounded-xl border border-[#E2EBE9] p-5">
                        <h3 class="font-bold text-[#365F65]">Okupansi Terminal × Lantai</h3>
                        <p class="mt-1 text-sm text-[#7A9290]">Makin pekat, makin penuh. Klik sel untuk menyaring tabel.</p>

                        @if ($matriksLantai->isNotEmpty())
                            <div class="mt-5 overflow-x-auto">
                                <table class="w-full border-separate border-spacing-1 text-center text-xs">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            @foreach ($daftarLantai as $l)
                                                <th class="px-1 pb-1 font-semibold text-[#7A9290]">Lt. {{ $l }}</th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($daftarTerminal as $t)
                                            <tr>
                                                <th class="pr-2 text-left font-bold text-[#365F65]">{{ $t }}</th>
                                                @foreach ($daftarLantai as $l)
                                                    @php
                                                        $sel = $matriksLantai->get($t . '|' . $l);
                                                        $pSel = $sel && $sel->total > 0 ? round(($sel->terisi / $sel->total) * 100) : 0;
                                                    @endphp
                                                    <td>
                                                        @if ($sel)
                                                            <a href="{{ request()->fullUrlWithQuery(['terminal' => $t, 'lantai' => $l, 'page' => null]) }}"
                                                               style="{{ $gayaPanas($pSel) }}"
                                                               title="{{ $t }} Lt. {{ $l }}: {{ $sel->terisi }} terisi dari {{ $sel->total }} ruangan"
                                                               class="block rounded-lg px-2 py-2.5 transition hover:ring-2 hover:ring-[#527D82] {{ $pSel >= 45 ? 'text-white' : 'text-[#365F65]' }}">
                                                                <span class="block text-sm font-bold">{{ $pSel }}%</span>
                                                                <span class="block text-[10px] opacity-80">{{ $sel->terisi }}/{{ $sel->total }}</span>
                                                            </a>
                                                        @else
                                                            <div class="rounded-lg border border-dashed border-[#DCE6E4] py-3.5 text-[#B6C7C4]">–</div>
                                                        @endif
                                                    </td>
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-4 flex items-center gap-2 text-[11px] text-[#8BA5A2]">
                                <span>0%</span>
                                <span class="h-2 flex-1 rounded-full" style="background: linear-gradient(to right, rgba(54,95,101,.10), rgba(54,95,101,.90));"></span>
                                <span>100%</span>
                            </div>
                        @else
                            <p class="mt-5 rounded-xl bg-[#F8FAF9] px-4 py-8 text-center text-sm text-[#7A9290]">Belum ada data terminal dan lantai.</p>
                        @endif
                    </div>

                    {{-- Donat jenis usaha --}}
                    <div class="rounded-xl border border-[#E2EBE9] p-5">
                        <h3 class="font-bold text-[#365F65]">Jenis Usaha Tenant</h3>
                        <p class="mt-1 text-sm text-[#7A9290]">Siapa yang menempati lokasi terisi.</p>

                        @if (count($segmenUsaha) > 0)
                            <div class="mt-5 flex flex-col items-center gap-5">
                                <x-donut :segments="$segmenUsaha" :center="$totalTerisi" sub="Lokasi terisi" />

                                <ul class="w-full space-y-2">
                                    @foreach ($segmenUsaha as $seg)
                                        <li class="flex items-center gap-3 text-sm">
                                            <span class="h-3 w-3 shrink-0 rounded-full" style="background: {{ $seg['color'] }}"></span>
                                            <span class="min-w-0 flex-1 truncate text-[#527D82]">{{ $seg['label'] }}</span>
                                            <span class="font-bold text-[#365F65]">{{ $seg['value'] }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @else
                            <p class="mt-5 rounded-xl bg-[#F8FAF9] px-4 py-8 text-center text-sm text-[#7A9290]">Belum ada lokasi yang ditempati tenant.</p>
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">

                    {{-- Per terminal --}}
                    <div class="rounded-xl border border-[#E2EBE9] p-5">
                        <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <h3 class="font-bold text-[#365F65]">Lokasi per Terminal</h3>
                                <p class="mt-1 text-sm text-[#7A9290]">Jumlah ruangan dan okupansi tiap terminal.</p>
                            </div>
                            <div class="flex items-center gap-4 text-xs text-[#7A9290]">
                                <span class="inline-flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full bg-amber-500"></span>Terisi</span>
                                <span class="inline-flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full bg-emerald-600"></span>Kosong</span>
                            </div>
                        </div>

                        @php $maksTerminal = max(1, (int) $lokasiPerTerminal->max('total')); @endphp

                        <div class="mt-5 space-y-5">
                            @forelse ($lokasiPerTerminal as $item)
                                @php $kosongItem = $item->total - $item->terisi; @endphp
                                <div>
                                    <div class="mb-1.5 flex items-center justify-between gap-3 text-sm">
                                        <a href="{{ request()->fullUrlWithQuery(['terminal' => $item->terminal, 'page' => null]) }}"
                                           class="font-semibold text-[#527D82] hover:underline">{{ $item->terminal }}</a>
                                        <span class="font-bold text-[#365F65]">{{ $item->total }} lokasi</span>
                                    </div>
                                    <div class="flex h-3 overflow-hidden rounded-full bg-[#E7EFEC]"
                                         style="width: {{ max(8, ($item->total / $maksTerminal) * 100) }}%"
                                         role="img" aria-label="{{ $item->terminal }}: {{ $item->terisi }} terisi, {{ $kosongItem }} kosong">
                                        <div class="bg-amber-500" style="width: {{ $item->total ? ($item->terisi / $item->total) * 100 : 0 }}%"></div>
                                        <div class="bg-emerald-600" style="width: {{ $item->total ? ($kosongItem / $item->total) * 100 : 0 }}%"></div>
                                    </div>
                                    <p class="mt-1 text-xs text-[#8BA5A2]">{{ $item->terisi }} terisi · {{ $kosongItem }} kosong</p>
                                </div>
                            @empty
                                <p class="rounded-xl bg-[#F8FAF9] px-4 py-8 text-center text-sm text-[#7A9290]">Belum ada data terminal.</p>
                            @endforelse
                        </div>
                    </div>

                    {{-- Per jenis ruangan --}}
                    <div class="rounded-xl border border-[#E2EBE9] p-5">
                        <h3 class="font-bold text-[#365F65]">Lokasi per Jenis Ruangan</h3>
                        <p class="mt-1 text-sm text-[#7A9290]">Klik nama jenis untuk menyaring tabel.</p>

                        @php $maksJenis = max(1, (int) $lokasiPerJenis->max('total')); @endphp

                        @if ($lokasiPerJenis->isNotEmpty())
                            <div class="mt-5 space-y-4">
                                @foreach ($lokasiPerJenis as $item)
                                    @php $kosongItem = $item->total - $item->terisi; @endphp
                                    <div>
                                        <div class="mb-1.5 flex items-center justify-between gap-3 text-sm">
                                            <a href="{{ request()->fullUrlWithQuery(['jenis_ruangan' => $item->jenis_ruangan, 'page' => null]) }}"
                                               class="break-words font-semibold text-[#527D82] hover:underline">{{ $item->jenis_ruangan }}</a>
                                            <span class="shrink-0 text-xs text-[#8BA5A2]">
                                                {{ $item->terisi }}/{{ $item->total }} terisi
                                            </span>
                                        </div>
                                        <div class="flex h-3 overflow-hidden rounded-full bg-[#E7EFEC]"
                                             style="width: {{ max(6, ($item->total / $maksJenis) * 100) }}%"
                                             role="img" aria-label="{{ $item->jenis_ruangan }}: {{ $item->terisi }} terisi, {{ $kosongItem }} kosong">
                                            <div class="bg-amber-500" style="width: {{ ($item->terisi / $item->total) * 100 }}%"></div>
                                            <div class="bg-emerald-600" style="width: {{ ($kosongItem / $item->total) * 100 }}%"></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="mt-5 rounded-xl bg-[#F8FAF9] px-4 py-8 text-center text-sm text-[#7A9290]">Belum ada data jenis ruangan.</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ===== TAB DENAH OKUPANSI ===== --}}
            <div x-show="tab === 'denah'" style="display: none;" class="space-y-6">

                <div class="flex flex-wrap items-center gap-x-5 gap-y-2 text-xs text-[#7A9290]">
                    <span class="inline-flex items-center gap-2">
                        <span class="h-4 w-5 rounded bg-amber-500"></span> Terisi
                    </span>
                    <span class="inline-flex items-center gap-2">
                        <span class="h-4 w-5 rounded bg-emerald-50 ring-1 ring-emerald-300"></span> Kosong
                    </span>
                    <span>Angka di kotak = nomor urut ruangan. Arahkan kursor untuk detail, klik untuk membuka.</span>
                </div>

                @forelse ($denah as $namaTerminal => $perLantai)
                    <div class="rounded-xl border border-[#E2EBE9] p-5">
                        <div class="mb-4 flex items-center justify-between gap-3">
                            <h3 class="font-bold text-[#365F65]">Terminal {{ $namaTerminal }}</h3>
                            @php
                                $semuaRuang = $perLantai->flatten(1);
                                $terisiT = $semuaRuang->where('status', 'Terisi')->count();
                            @endphp
                            <span class="text-xs text-[#8BA5A2]">{{ $terisiT }} dari {{ $semuaRuang->count() }} ruangan terisi</span>
                        </div>

                        <div class="divide-y divide-[#E2EBE9]">
                            @foreach ($perLantai as $namaLantai => $ruangan)
                                @php
                                    $terisiL = $ruangan->where('status', 'Terisi')->count();
                                    $persenL = round(($terisiL / max(1, $ruangan->count())) * 100);
                                @endphp
                                <div class="flex flex-col gap-3 py-4 first:pt-0 last:pb-0 sm:flex-row sm:items-start">
                                    <div class="w-full shrink-0 sm:w-28">
                                        <p class="text-sm font-bold text-[#365F65]">{{ $namaLantai === '-' ? 'Tanpa lantai' : 'Lantai ' . $namaLantai }}</p>
                                        <p class="text-xs text-[#8BA5A2]">{{ $terisiL }}/{{ $ruangan->count() }} · {{ $persenL }}%</p>
                                        <div class="mt-1.5 h-1.5 overflow-hidden rounded-full bg-[#E7EFEC]">
                                            <div class="h-full rounded-full bg-amber-500" style="width: {{ $persenL }}%"></div>
                                        </div>
                                    </div>

                                    <div class="flex flex-1 flex-wrap gap-1.5">
                                        @foreach ($ruangan as $r)
                                            @php
                                                $nomorUrut = last(explode('-', $r->kode_ruang));
                                                $isi = $r->status === 'Terisi';
                                                $infoLuas = $r->luas_area !== null ? ' · ' . number_format((float) $r->luas_area, 0, ',', '.') . ' m²' : '';
                                            @endphp
                                            <a href="{{ route('lokasi.show', $r->id) }}"
                                               title="{{ $r->kode_ruang }} · {{ $r->jenis_ruangan ?: 'Tanpa jenis' }}{{ $infoLuas }} · {{ $isi ? 'Terisi' : 'Kosong' }}"
                                               class="flex h-8 min-w-9 items-center justify-center rounded-md px-1 text-[10px] font-bold tabular-nums transition hover:-translate-y-0.5 hover:shadow
                                                      {{ $isi ? 'bg-amber-500 text-white' : 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-300' }}">
                                                {{ $nomorUrut }}
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <p class="rounded-xl bg-[#F8FAF9] px-4 py-10 text-center text-sm text-[#7A9290]">Belum ada ruangan untuk digambarkan.</p>
                @endforelse
            </div>

            {{-- ===== TAB DRILLDOWN ===== --}}
            <div x-show="tab === 'drilldown'" style="display: none;" class="space-y-5"
                 x-data="lokasiDrilldown('{{ route('lokasi.drilldown') }}')"
                 x-effect="if (tab === 'drilldown' && !dimuat) muat()">

                <p class="text-sm text-[#7A9290]">
                    Klik baris untuk melihat rincian satu tingkat di bawahnya:
                    Terminal → Lantai → Jenis Ruangan → Ruangan. Angka dihitung langsung dari data lokasi.
                </p>

                {{-- Jejak (breadcrumb) --}}
                <nav class="flex flex-wrap items-center gap-1.5 text-sm" aria-label="Jejak drilldown">
                    <button type="button" @click="naik(0)"
                            class="rounded-lg px-2.5 py-1 font-semibold transition"
                            :class="jalur.length === 0 ? 'bg-[#365F65] text-white' : 'bg-[#F3F6F5] text-[#527D82] hover:bg-[#E2EBE9]'">
                        Semua Terminal
                    </button>
                    <template x-for="(j, i) in jalur" :key="i">
                        <span class="flex items-center gap-1.5">
                            <span class="text-[#8BA5A2]">›</span>
                            <button type="button" @click="naik(i + 1)"
                                    class="rounded-lg px-2.5 py-1 font-semibold transition"
                                    :class="i === jalur.length - 1 ? 'bg-[#365F65] text-white' : 'bg-[#F3F6F5] text-[#527D82] hover:bg-[#E2EBE9]'"
                                    x-text="j.label"></button>
                        </span>
                    </template>
                    <button type="button" x-show="jalur.length > 0" @click="naik(jalur.length - 1)"
                            class="ml-auto text-xs font-semibold text-[#527D82] hover:underline">← Naik satu tingkat</button>
                </nav>

                <p x-show="galat" x-text="galat" class="rounded-xl bg-red-50 px-4 py-3 text-sm text-red-700"></p>
                <p x-show="memuat" class="text-sm text-[#7A9290]">Memuat data…</p>

                <template x-if="data && !galat">
                    <div class="space-y-4" :class="memuat ? 'opacity-60' : ''">

                        {{-- Ringkasan level ini --}}
                        <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                            <div class="rounded-xl border border-[#E2EBE9] p-4">
                                <p class="text-xs text-[#8BA5A2]">Total ruangan</p>
                                <p class="mt-1 text-xl font-bold text-[#365F65]" x-text="data.ringkasan.total"></p>
                            </div>
                            <div class="rounded-xl border border-[#E2EBE9] p-4">
                                <p class="text-xs text-[#8BA5A2]">Terisi</p>
                                <p class="mt-1 text-xl font-bold text-[#A47720]" x-text="data.ringkasan.terisi"></p>
                            </div>
                            <div class="rounded-xl border border-[#E2EBE9] p-4">
                                <p class="text-xs text-[#8BA5A2]">Kosong</p>
                                <p class="mt-1 text-xl font-bold text-[#31966A]" x-text="data.ringkasan.kosong"></p>
                            </div>
                            <div class="rounded-xl border border-[#E2EBE9] p-4">
                                <p class="text-xs text-[#8BA5A2]">Okupansi</p>
                                <p class="mt-1 text-xl font-bold text-[#365F65]" x-text="data.ringkasan.okupansi + '%'"></p>
                            </div>
                        </div>

                        {{-- Level agregat: klik baris untuk turun --}}
                        <div x-show="data.kunci !== 'ruangan'" class="overflow-hidden rounded-xl border border-[#E2EBE9]">
                            <table class="min-w-full text-left text-sm">
                                <thead class="bg-[#F3F6F5] text-xs font-semibold uppercase tracking-wide text-[#527D82]">
                                    <tr>
                                        <th class="px-4 py-3" x-text="data.judul"></th>
                                        <th class="px-4 py-3 text-right">Total</th>
                                        <th class="px-4 py-3 text-right">Terisi</th>
                                        <th class="px-4 py-3 text-right">Kosong</th>
                                        <th class="px-4 py-3">Okupansi</th>
                                        <th class="px-4 py-3 text-right">Luas terisi / total (m²)</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-[#E2EBE9]">
                                    <template x-for="r in data.items" :key="r.nilai">
                                        <tr class="cursor-pointer transition hover:bg-[#F8FAF9]" @click="turun(r)">
                                            <td class="px-4 py-3">
                                                <button type="button" class="font-semibold text-[#365F65] hover:underline"
                                                        x-text="(data.kunci === 'lantai' ? 'Lantai ' : (data.kunci === 'terminal' ? 'Terminal ' : '')) + r.nama"></button>
                                            </td>
                                            <td class="px-4 py-3 text-right text-[#365F65]" x-text="r.total"></td>
                                            <td class="px-4 py-3 text-right text-[#A47720]" x-text="r.terisi"></td>
                                            <td class="px-4 py-3 text-right text-[#31966A]" x-text="r.kosong"></td>
                                            <td class="px-4 py-3">
                                                <div class="flex items-center gap-2">
                                                    <div class="h-2 w-28 overflow-hidden rounded-full bg-[#E2EBE9]">
                                                        <div class="h-full rounded-full bg-[#D6A14D]" :style="'width:' + r.okupansi + '%'"></div>
                                                    </div>
                                                    <span class="text-xs text-[#7A9290]" x-text="r.okupansi + '%'"></span>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3 text-right text-[#7A9290]"
                                                x-text="fmt(r.luas_terisi) + ' / ' + fmt(r.luas)"></td>
                                        </tr>
                                    </template>
                                    <tr x-show="data.items.length === 0">
                                        <td colspan="6" class="px-4 py-8 text-center text-[#7A9290]">Tidak ada data pada tingkat ini.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        {{-- Level akhir: daftar ruangan --}}
                        <div x-show="data.kunci === 'ruangan'" class="overflow-hidden rounded-xl border border-[#E2EBE9]">
                            <table class="min-w-full text-left text-sm">
                                <thead class="bg-[#F3F6F5] text-xs font-semibold uppercase tracking-wide text-[#527D82]">
                                    <tr>
                                        <th class="px-4 py-3">Kode ruang</th>
                                        <th class="px-4 py-3">Lokasi</th>
                                        <th class="px-4 py-3">Status</th>
                                        <th class="px-4 py-3">Tenant</th>
                                        <th class="px-4 py-3 text-right">Luas (m²)</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-[#E2EBE9]">
                                    <template x-for="r in data.rooms" :key="r.id">
                                        <tr class="transition hover:bg-[#F8FAF9]">
                                            <td class="px-4 py-3">
                                                <a :href="r.url" class="font-semibold text-[#527D82] hover:underline" x-text="r.kode"></a>
                                            </td>
                                            <td class="px-4 py-3 text-[#365F65]" x-text="r.lokasi"></td>
                                            <td class="px-4 py-3">
                                                <span class="rounded-full px-2 py-0.5 text-xs font-semibold"
                                                      :class="r.status === 'Terisi' ? 'bg-amber-100 text-amber-800' : 'bg-emerald-50 text-emerald-700'"
                                                      x-text="r.status"></span>
                                            </td>
                                            <td class="px-4 py-3 text-[#7A9290]" x-text="r.tenant || '-'"></td>
                                            <td class="px-4 py-3 text-right text-[#7A9290]" x-text="r.luas === null ? '-' : fmt(r.luas)"></td>
                                        </tr>
                                    </template>
                                    <tr x-show="data.rooms.length === 0">
                                        <td colspan="5" class="px-4 py-8 text-center text-[#7A9290]">Tidak ada ruangan.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </section>

    {{-- Filter --}}
    <form method="GET" action="{{ route('lokasi.index') }}"
          class="rounded-2xl border border-[#E2EBE9] bg-white p-4 shadow-sm sm:p-5">

        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-6">

            <div class="sm:col-span-2 lg:col-span-2">
                <label for="search" class="mb-1 block text-xs font-semibold text-[#527D82]">Pencarian</label>
                <div class="relative">
                    <svg class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-[#8BA5A2]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                        <circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>
                    </svg>
                    <input type="search" id="search" name="search" value="{{ request('search') }}"
                           placeholder="Kode, lokasi, nomor RO, atau nama tenant…"
                           class="{{ $kelasInput }} pl-10">
                </div>
            </div>

            <div>
                <label for="terminal" class="mb-1 block text-xs font-semibold text-[#527D82]">Terminal</label>
                <select id="terminal" name="terminal" onchange="this.form.submit()" class="{{ $kelasInput }}">
                    <option value="">Semua</option>
                    @foreach ($pilihanTerminal as $t)
                        <option value="{{ $t }}" @selected(request('terminal') === $t)>{{ $t }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="lantai" class="mb-1 block text-xs font-semibold text-[#527D82]">Lantai</label>
                <select id="lantai" name="lantai" onchange="this.form.submit()" class="{{ $kelasInput }}">
                    <option value="">Semua</option>
                    @foreach ($pilihanLantai as $l)
                        <option value="{{ $l }}" @selected((string) request('lantai') === (string) $l)>Lantai {{ $l }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="jenis_ruangan" class="mb-1 block text-xs font-semibold text-[#527D82]">Jenis</label>
                <select id="jenis_ruangan" name="jenis_ruangan" onchange="this.form.submit()" class="{{ $kelasInput }}">
                    <option value="">Semua</option>
                    @foreach ($pilihanJenis as $j)
                        <option value="{{ $j }}" @selected(request('jenis_ruangan') === $j)>{{ $j }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="status" class="mb-1 block text-xs font-semibold text-[#527D82]">Status</label>
                <select id="status" name="status" onchange="this.form.submit()" class="{{ $kelasInput }}">
                    <option value="">Semua</option>
                    <option value="Kosong" @selected(request('status') === 'Kosong')>Kosong</option>
                    <option value="Terisi" @selected(request('status') === 'Terisi')>Terisi</option>
                </select>
            </div>
        </div>

        <div class="mt-3 flex flex-wrap items-end justify-between gap-3">
            <div class="flex flex-wrap items-end gap-3">
                <div>
                    <label for="sort" class="mb-1 block text-xs font-semibold text-[#527D82]">Urutkan</label>
                    <select id="sort" name="sort" onchange="this.form.submit()" class="{{ $kelasInput }}">
                        <option value="newest" @selected($sortAktif === 'newest')>Terbaru</option>
                        <option value="oldest" @selected($sortAktif === 'oldest')>Terlama</option>
                        <option value="az" @selected($sortAktif === 'az')>Kode A–Z</option>
                        <option value="za" @selected($sortAktif === 'za')>Kode Z–A</option>
                        <option value="luas_desc" @selected($sortAktif === 'luas_desc')>Luas terbesar</option>
                        <option value="luas_asc" @selected($sortAktif === 'luas_asc')>Luas terkecil</option>
                    </select>
                </div>

                <div>
                    <label for="per_page" class="mb-1 block text-xs font-semibold text-[#527D82]">Per halaman</label>
                    <select id="per_page" name="per_page" onchange="this.form.submit()" class="{{ $kelasInput }}">
                        @foreach ([10, 15, 25, 50, 100] as $n)
                            <option value="{{ $n }}" @selected($perPage === $n)>{{ $n }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex items-center gap-2">
                @if ($filterAktif->isNotEmpty())
                    <a href="{{ route('lokasi.index') }}"
                       class="inline-flex items-center rounded-xl border border-[#DCE6E4] px-4 py-2.5 text-sm font-semibold text-[#527D82] transition hover:bg-[#F5F8F7]">
                        Reset
                    </a>
                @endif
                <button type="submit"
                        class="inline-flex items-center rounded-xl bg-[#365F65] px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-[#294F55]">
                    Cari
                </button>
            </div>
        </div>

        @if ($filterAktif->isNotEmpty())
            <div class="mt-3 flex flex-wrap items-center gap-2 border-t border-[#E2EBE9] pt-3">
                <span class="text-xs text-[#8BA5A2]">Filter aktif:</span>
                @foreach ($filterAktif as $key => $label)
                    <a href="{{ request()->fullUrlWithQuery([$key => null, 'page' => null]) }}"
                       class="inline-flex items-center gap-1.5 rounded-full bg-[#EAF2F1] px-3 py-1 text-xs font-semibold text-[#365F65] transition hover:bg-[#DCE9E7]"
                       title="Hapus filter ini">
                        {{ $label }}: {{ request($key) }}
                        <span aria-hidden="true">×</span>
                    </a>
                @endforeach
            </div>
        @endif
    </form>

    {{-- Daftar ruangan --}}
    <section class="overflow-hidden rounded-2xl border border-[#E2EBE9] bg-white shadow-sm">

        <div class="flex flex-col gap-1 border-b border-[#E2EBE9] px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-bold text-[#365F65]">Daftar Ruangan</h2>
                <p class="mt-0.5 text-sm text-[#8BA5A2]">
                    @if ($lokasis->total() > 0)
                        Menampilkan {{ $lokasis->firstItem() }}–{{ $lokasis->lastItem() }}
                        dari {{ number_format($lokasis->total(), 0, ',', '.') }} lokasi
                        @if ($filterAktif->isNotEmpty()) (sesuai filter) @endif
                    @else
                        Tidak ada lokasi yang cocok.
                    @endif
                </p>
            </div>
            <p class="text-xs text-[#8BA5A2]">
                Hapus hanya tersedia untuk lokasi yang masih kosong.
            </p>
        </div>

        {{-- Tabel (layar sedang ke atas) --}}
        <div class="hidden max-h-[680px] overflow-auto md:block">
            <table class="w-full text-left text-sm">
                <thead class="sticky top-0 z-10 bg-[#F3F6F5] text-xs uppercase tracking-wide text-[#527D82]">
                    <tr>
                        <th class="whitespace-nowrap px-4 py-3.5 font-semibold">No.</th>
                        <th class="whitespace-nowrap px-4 py-3.5 font-semibold">
                            <a href="{{ $urlUrut('az', 'za') }}" class="inline-flex items-center gap-1 hover:text-[#294F55]">
                                Kode &amp; Lokasi <span>{{ $panahUrut('az', 'za') }}</span>
                            </a>
                        </th>
                        <th class="whitespace-nowrap px-4 py-3.5 font-semibold">Tenant</th>
                        <th class="whitespace-nowrap px-4 py-3.5 font-semibold">Terminal / Lantai</th>
                        <th class="whitespace-nowrap px-4 py-3.5 font-semibold">Jenis</th>
                        <th class="whitespace-nowrap px-4 py-3.5 text-right font-semibold">
                            <a href="{{ $urlUrut('luas_desc', 'luas_asc') }}" class="inline-flex items-center gap-1 hover:text-[#294F55]">
                                Luas (m²) <span>{{ $panahUrut('luas_desc', 'luas_asc') }}</span>
                            </a>
                        </th>
                        <th class="whitespace-nowrap px-4 py-3.5 font-semibold">Status</th>
                        <th class="whitespace-nowrap px-4 py-3.5 text-right font-semibold">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-[#E2EBE9]">
                    @forelse ($lokasis as $lokasi)
                        @php
                            $terisi = $lokasi->status === 'Terisi';
                            $namaTenant = $lokasi->tenant?->nama_tenant
                                ?? $lokasi->tenant?->mitra_usaha;
                            $bisaHapus = $lokasi->status === 'Kosong' && $lokasi->tenant_id === null;
                        @endphp
                        <tr class="transition hover:bg-[#F8FAF9]">
                            <td class="whitespace-nowrap px-4 py-3.5 text-[#8BA5A2]">
                                {{ $lokasis->firstItem() + $loop->index }}
                            </td>

                            <td class="min-w-52 px-4 py-3.5">
                                <a href="{{ route('lokasi.show', $lokasi->id) }}"
                                   class="font-semibold text-[#365F65] hover:underline">
                                    {{ $lokasi->kode_tampilan ?? $lokasi->kode_ruang }}
                                </a>
                                <p class="mt-0.5 text-xs text-[#7A9290]">{{ $lokasi->lokasi ?: '-' }}</p>
                                @if ($lokasi->tenant_id && $lokasi->kode_tampilan && $lokasi->kode_tampilan !== $lokasi->kode_ruang)
                                    <p class="mt-0.5 text-[11px] text-[#8BA5A2]">Kode asli: {{ $lokasi->kode_ruang }}</p>
                                @endif
                            </td>

                            <td class="min-w-40 px-4 py-3.5">
                                @if ($lokasi->tenant)
                                    <a href="{{ route('tenant.show', $lokasi->tenant->id) }}"
                                       class="font-medium text-[#365F65] hover:underline">
                                        {{ $namaTenant ?: 'Tenant #' . $lokasi->tenant->id }}
                                    </a>
                                @else
                                    <span class="text-[#8BA5A2]">Belum ditempati</span>
                                @endif
                            </td>

                            <td class="whitespace-nowrap px-4 py-3.5 text-[#527D82]">
                                {{ $lokasi->terminal ?: '-' }}
                                <span class="text-[#B6C7C4]">·</span>
                                {{ $lokasi->lantai ? 'Lt. ' . $lokasi->lantai : '-' }}
                            </td>

                            <td class="whitespace-nowrap px-4 py-3.5">
                                @if ($lokasi->jenis_ruangan)
                                    <span class="rounded-md bg-[#F3F6F5] px-2 py-1 text-xs font-medium text-[#527D82]">
                                        {{ $lokasi->jenis_ruangan }}
                                    </span>
                                @else
                                    -
                                @endif
                            </td>

                            <td class="whitespace-nowrap px-4 py-3.5 text-right tabular-nums">
                                {{ $lokasi->luas_area !== null
                                    ? number_format((float) $lokasi->luas_area, 2, ',', '.')
                                    : '-' }}
                            </td>

                            <td class="whitespace-nowrap px-4 py-3.5">
                                <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold
                                    {{ $terisi ? 'bg-amber-50 text-amber-700' : 'bg-emerald-50 text-emerald-700' }}">
                                    <span class="h-1.5 w-1.5 rounded-full {{ $terisi ? 'bg-amber-500' : 'bg-emerald-500' }}"></span>
                                    {{ $terisi ? 'Terisi' : 'Kosong' }}
                                </span>
                            </td>

                            <td class="whitespace-nowrap px-4 py-3.5">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('lokasi.show', $lokasi->id) }}" title="Lihat detail" aria-label="Lihat detail {{ $lokasi->kode_ruang }}"
                                       class="flex h-8 w-8 items-center justify-center rounded-lg text-[#527D82] transition hover:bg-[#EAF2F1]">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/>
                                        </svg>
                                    </a>
                                    <a href="{{ route('lokasi.edit', $lokasi->id) }}" title="Edit lokasi" aria-label="Edit {{ $lokasi->kode_ruang }}"
                                       class="flex h-8 w-8 items-center justify-center rounded-lg text-amber-700 transition hover:bg-amber-50">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/>
                                        </svg>
                                    </a>
                                    @if ($bisaHapus)
                                        <form action="{{ route('lokasi.destroy', $lokasi->id) }}" method="POST"
                                              onsubmit="return confirm('Hapus lokasi {{ $lokasi->kode_ruang }}? Tindakan ini tidak dapat dibatalkan.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Hapus lokasi" aria-label="Hapus {{ $lokasi->kode_ruang }}"
                                                    class="flex h-8 w-8 items-center justify-center rounded-lg text-red-600 transition hover:bg-red-50">
                                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M3 6h18M8 6V4h8v2M6 6l1 14h10l1-14M10 10v6M14 10v6"/>
                                                </svg>
                                            </button>
                                        </form>
                                    @else
                                        <span title="Lokasi terisi tidak dapat dihapus" aria-label="Tidak dapat dihapus"
                                              class="flex h-8 w-8 cursor-not-allowed items-center justify-center rounded-lg text-[#C4D2CF]">
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
                                                <rect x="5" y="11" width="14" height="9" rx="2"/><path d="M8 11V8a4 4 0 0 1 8 0v3"/>
                                            </svg>
                                        </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-14 text-center">
                                <p class="font-semibold text-[#365F65]">Lokasi tidak ditemukan</p>
                                <p class="mt-1 text-sm text-[#8BA5A2]">
                                    @if ($filterAktif->isNotEmpty())
                                        Coba ubah kata pencarian atau hapus sebagian filter.
                                    @else
                                        Belum ada lokasi. Mulai dengan menambahkan ruangan pertama.
                                    @endif
                                </p>
                                <div class="mt-4 flex items-center justify-center gap-3">
                                    @if ($filterAktif->isNotEmpty())
                                        <a href="{{ route('lokasi.index') }}" class="text-sm font-semibold text-[#527D82] hover:underline">Reset filter</a>
                                    @endif
                                    <a href="{{ route('lokasi.create') }}" class="rounded-xl bg-[#365F65] px-4 py-2 text-sm font-semibold text-white hover:bg-[#294F55]">+ Tambah Lokasi</a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Kartu (layar kecil) --}}
        <div class="divide-y divide-[#E2EBE9] md:hidden">
            @forelse ($lokasis as $lokasi)
                @php
                    $terisi = $lokasi->status === 'Terisi';
                    $namaTenant = $lokasi->tenant?->nama_tenant ?? $lokasi->tenant?->mitra_usaha;
                    $bisaHapus = $lokasi->status === 'Kosong' && $lokasi->tenant_id === null;
                @endphp
                <div class="p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <a href="{{ route('lokasi.show', $lokasi->id) }}" class="font-semibold text-[#365F65]">
                                {{ $lokasi->kode_tampilan ?? $lokasi->kode_ruang }}
                            </a>
                            <p class="mt-0.5 break-words text-xs text-[#7A9290]">{{ $lokasi->lokasi ?: '-' }}</p>
                        </div>
                        <span class="inline-flex shrink-0 items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold
                            {{ $terisi ? 'bg-amber-50 text-amber-700' : 'bg-emerald-50 text-emerald-700' }}">
                            <span class="h-1.5 w-1.5 rounded-full {{ $terisi ? 'bg-amber-500' : 'bg-emerald-500' }}"></span>
                            {{ $terisi ? 'Terisi' : 'Kosong' }}
                        </span>
                    </div>

                    <dl class="mt-3 grid grid-cols-2 gap-x-4 gap-y-2 text-xs">
                        <div><dt class="text-[#8BA5A2]">Terminal / Lantai</dt>
                            <dd class="font-medium text-[#365F65]">{{ $lokasi->terminal ?: '-' }} · {{ $lokasi->lantai ? 'Lt. ' . $lokasi->lantai : '-' }}</dd></div>
                        <div><dt class="text-[#8BA5A2]">Jenis</dt>
                            <dd class="font-medium text-[#365F65]">{{ $lokasi->jenis_ruangan ?: '-' }}</dd></div>
                        <div><dt class="text-[#8BA5A2]">Luas</dt>
                            <dd class="font-medium text-[#365F65]">{{ $lokasi->luas_area !== null ? number_format((float) $lokasi->luas_area, 2, ',', '.') . ' m²' : '-' }}</dd></div>
                        <div><dt class="text-[#8BA5A2]">Tenant</dt>
                            <dd class="font-medium text-[#365F65]">{{ $namaTenant ?: 'Belum ditempati' }}</dd></div>
                    </dl>

                    <div class="mt-3 flex items-center gap-4 text-sm font-semibold">
                        <a href="{{ route('lokasi.show', $lokasi->id) }}" class="text-[#527D82]">Detail</a>
                        <a href="{{ route('lokasi.edit', $lokasi->id) }}" class="text-amber-700">Edit</a>
                        @if ($bisaHapus)
                            <form action="{{ route('lokasi.destroy', $lokasi->id) }}" method="POST"
                                  onsubmit="return confirm('Hapus lokasi {{ $lokasi->kode_ruang }}?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600">Hapus</button>
                            </form>
                        @else
                            <span class="text-xs font-normal text-[#B6C7C4]">Terisi, tidak dapat dihapus</span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="px-4 py-12 text-center">
                    <p class="font-semibold text-[#365F65]">Lokasi tidak ditemukan</p>
                    <a href="{{ route('lokasi.index') }}" class="mt-2 inline-block text-sm font-semibold text-[#527D82] hover:underline">Reset filter</a>
                </div>
            @endforelse
        </div>

        @if ($lokasis->hasPages())
            <div class="border-t border-[#E2EBE9] px-5 py-4">
                {{ $lokasis->links() }}
            </div>
        @endif
    </section>
</div>

<script>
    // Komponen drilldown Lokasi: setiap klik meminta data tingkat berikutnya ke server.
    function lokasiDrilldown(url) {
        return {
            dimuat: false,
            memuat: false,
            galat: '',
            data: null,
            jalur: [], // [{kunci, nilai, label}]

            async muat() {
                this.dimuat = true;
                this.memuat = true;
                this.galat = '';

                const q = new URLSearchParams();
                this.jalur.forEach(j => q.set(j.kunci, j.nilai));

                try {
                    const r = await fetch(url + '?' + q.toString(), {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                        credentials: 'same-origin',
                    });
                    if (!r.ok) throw new Error('HTTP ' + r.status);
                    this.data = await r.json();
                } catch (e) {
                    this.galat = 'Data drilldown gagal dimuat. Muat ulang halaman atau coba lagi.';
                } finally {
                    this.memuat = false;
                }
            },

            turun(item) {
                if (!this.data || this.data.kunci === 'ruangan') return;
                const awalan = this.data.kunci === 'lantai' ? 'Lantai ' : (this.data.kunci === 'terminal' ? 'Terminal ' : '');
                this.jalur.push({ kunci: this.data.kunci, nilai: item.nilai, label: awalan + item.nama });
                this.muat();
            },

            naik(jumlah) {
                this.jalur = this.jalur.slice(0, jumlah);
                this.muat();
            },

            fmt(n) {
                return Number(n).toLocaleString('id-ID', { maximumFractionDigits: 2 });
            },
        };
    }
</script>
@endsection
