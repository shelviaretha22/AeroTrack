@extends('layouts.app')

@section('page-title', 'Laporan')

@section('content')

@php
    $formatRupiah = fn ($nilai) => 'Rp ' . number_format((float) $nilai, 0, ',', '.');

    $totalKomposisi = array_sum($komposisiStatus);

    $persenAktif = $totalKomposisi > 0
        ? ($komposisiStatus['Aktif'] / $totalKomposisi) * 100
        : 0;

    $persenNonaktif = $totalKomposisi > 0
        ? ($komposisiStatus['Nonaktif'] / $totalKomposisi) * 100
        : 0;

    $persenProses = $totalKomposisi > 0
        ? ($komposisiStatus['Proses'] / $totalKomposisi) * 100
        : 0;

    $persenKontrakLengkap = $totalDokumenKontrak > 0
        ? ($dokumenKontrakLengkap / $totalDokumenKontrak) * 100
        : 0;

    $persenBALengkap = $totalBA > 0
        ? ($baLengkap / $totalBA) * 100
        : 0;

    $tahunSekarang = now()->year;
        // URL drill-down dengan mempertahankan filter laporan.
    $drilldownUrl = function ($type, $value) use ($tahun) {
        return route('laporan.drilldown', array_merge(
            request()->only([
                'jenis_usaha',
                'terminal',
                'status',
                'dokumen',
                'urutan',
            ]),
            [
                'tahun' => $tahun,
                'type' => $type,
                'value' => $value,
            ]
        ));
    };
@endphp

<div class="space-y-6">

        {{-- HEADER --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center">

    <div class="text-sm text-[#7A9290]">
        Periode laporan:
        <span class="font-semibold text-[#365F65]">
            Tahun {{ $tahun }}
        </span>
    </div>

    <div class="flex flex-wrap items-center gap-2">

        <a href="{{ route('laporan.excel', request()->query()) }}"
           class="inline-flex items-center justify-center gap-2 rounded-lg border border-[#365F65] bg-white px-4 py-2.5 text-sm font-semibold text-[#365F65] transition hover:bg-[#F1F6F4]">

            <i class="fas fa-file-excel"></i>
            Unduh Excel

        </a>

        <a href="{{ route('laporan.pdf', request()->query()) }}"
           class="inline-flex items-center justify-center gap-2 rounded-lg bg-[#365F65] px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-[#294B50]">

            <i class="fas fa-file-pdf"></i>
            Unduh PDF

        </a>

    </div>

</div>

    {{-- =========================================================
         FILTER LAPORAN
    ========================================================== --}}
    <div class="rounded-2xl border border-[#DCE6E4] bg-white p-5 shadow-sm">

        <form action="{{ route('laporan') }}" method="GET">

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5">

                {{-- TAHUN --}}
                <div>
                    <label class="mb-2 block text-sm font-medium text-[#527D82]">
                        Tahun
                    </label>

                    <select name="tahun"
                        class="w-full rounded-lg border border-[#DCE6E4] bg-white px-3 py-2.5 text-sm text-[#365F65] focus:border-[#527D82] focus:outline-none">
                        @for ($i = $tahunSekarang - 5; $i <= $tahunSekarang + 1; $i++)
                            <option value="{{ $i }}" @selected($tahun == $i)>
                                {{ $i }}
                            </option>
                        @endfor
                    </select>
                </div>

                {{-- JENIS USAHA --}}
                <div>
                    <label class="mb-2 block text-sm font-medium text-[#527D82]">
                        Jenis Usaha
                    </label>

                    <select name="jenis_usaha"
                        class="w-full rounded-lg border border-[#DCE6E4] bg-white px-3 py-2.5 text-sm text-[#365F65] focus:border-[#527D82] focus:outline-none">
                        <option value="">Semua jenis</option>

                        @foreach (['F&B', 'Retail', 'Service', 'Lounge', 'Duty Free', 'UMKM'] as $jenis)
                            <option value="{{ $jenis }}" @selected($jenisUsaha == $jenis)>
                                {{ $jenis }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- TERMINAL --}}
                <div>
                    <label class="mb-2 block text-sm font-medium text-[#527D82]">
                        Terminal
                    </label>

                    <select name="terminal"
                        class="w-full rounded-lg border border-[#DCE6E4] bg-white px-3 py-2.5 text-sm text-[#365F65] focus:border-[#527D82] focus:outline-none">
                        <option value="">Semua terminal</option>
                        <option value="T1" @selected($terminal == 'T1')>Terminal 1</option>
                        <option value="T2" @selected($terminal == 'T2')>Terminal 2</option>
                    </select>
                </div>

                {{-- STATUS --}}
                <div>
                    <label class="mb-2 block text-sm font-medium text-[#527D82]">
                        Status Kontrak
                    </label>

                    <select name="status"
                        class="w-full rounded-lg border border-[#DCE6E4] bg-white px-3 py-2.5 text-sm text-[#365F65] focus:border-[#527D82] focus:outline-none">

                        <option value="">Semua status</option>
                        <option value="Belum Dibuat" @selected($statusFilter == 'Belum Dibuat')>Belum Dibuat</option>
                        <option value="Draft" @selected($statusFilter == 'Draft')>Draft</option>
                        <option value="Aktif" @selected($statusFilter == 'Aktif')>Aktif</option>
                        <option value="Nonaktif" @selected($statusFilter == 'Nonaktif')>Nonaktif</option>

                    </select>
                </div>

                {{-- DOKUMEN --}}
                <div>
                    <label class="mb-2 block text-sm font-medium text-[#527D82]">
                        Status Dokumen
                    </label>

                    <select name="dokumen"
                        class="w-full rounded-lg border border-[#DCE6E4] bg-white px-3 py-2.5 text-sm text-[#365F65] focus:border-[#527D82] focus:outline-none">
                        <option value="">Semua dokumen</option>
                        <option value="Lengkap" @selected($dokumenFilter == 'Lengkap')>Lengkap</option>
                        <option value="Belum Lengkap" @selected($dokumenFilter == 'Belum Lengkap')>Belum Lengkap</option>
                    </select>
                </div>

            </div>

            <div class="mt-4 flex justify-end gap-2">
                <a href="{{ route('laporan') }}"
                    class="rounded-lg border border-[#DCE6E4] px-4 py-2.5 text-sm font-semibold text-[#527D82] hover:bg-[#F1F6F4]">
                    Reset
                </a>

                <button type="submit"
                    class="rounded-lg bg-[#365F65] px-5 py-2.5 text-sm font-semibold text-white hover:bg-[#294B50]">
                    <i class="fas fa-filter mr-1"></i>
                    Terapkan Filter
                </button>
            </div>

        </form>
    </div>


        {{-- =========================================================
         SUMMARY DASHBOARD LAPORAN - COMPACT
    ========================================================== --}}

    @php
        $belumAktif = max(0, $totalKerjaSama - $totalSistemAktif);

        $totalTerminalT1 = $progressTerminal['T1']['total'] ?? 0;
        $aktifTerminalT1 = $progressTerminal['T1']['aktif'] ?? 0;
        $persenTerminalT1 = $progressTerminal['T1']['persentase'] ?? 0;

        $totalTerminalT2 = $progressTerminal['T2']['total'] ?? 0;
        $aktifTerminalT2 = $progressTerminal['T2']['aktif'] ?? 0;
        $persenTerminalT2 = $progressTerminal['T2']['persentase'] ?? 0;

        $totalPerLokasi = $kontrakPerTerminal->sum();

        $persenT1 = $totalPerLokasi > 0
            ? round((($kontrakPerTerminal['T1'] ?? 0) / $totalPerLokasi) * 100, 1)
            : 0;

        $persenT2 = $totalPerLokasi > 0
            ? round((($kontrakPerTerminal['T2'] ?? 0) / $totalPerLokasi) * 100, 1)
            : 0;
    @endphp

    <section class="grid grid-cols-1 gap-2.5 md:grid-cols-12">

        {{-- TOTAL KONTRAK --}}
        <a href="{{ $drilldownUrl('kontrak', 'semua') }}"
        class="flex min-h-[112px] flex-col justify-center rounded-2xl border border-[#DCE6E4] bg-white px-3 py-3 shadow-sm md:col-span-2 transition hover:border-[#527D82] hover:bg-[#F6F9F8] cursor-pointer">
            <div class="flex items-center justify-center gap-2">
                <i class="fas fa-file-alt text-xl text-[#527D82]"></i>
                <span class="text-sm font-medium text-[#527D82]">
                    Total Kontrak
                </span>
            </div>

            <h2 class="mt-2 text-center text-3xl font-bold leading-none text-[#365F65]">
                {{ number_format($totalKontrak) }}
            </h2>

        </a>


        {{-- KONTRAK AKTIF --}}
        <a href="{{ $drilldownUrl('status', 'Aktif') }}"
        class="flex min-h-[112px] flex-col justify-center rounded-2xl border border-[#DCE6E4] bg-white px-3 py-3 shadow-sm md:col-span-2 transition hover:border-[#527D82] hover:bg-[#F6F9F8] cursor-pointer">
                    <div class="flex items-center justify-center gap-2">
                <i class="fas fa-circle-check text-xl text-[#527D82]"></i>
                <span class="text-sm font-medium text-[#527D82]">
                    Kontrak Aktif
                </span>
            </div>

            <h2 class="mt-2 text-center text-3xl font-bold leading-none text-[#365F65]">
                {{ number_format($kontrakAktif) }}
            </h2>

        </a>


        {{-- PROGRESS AKTIVASI --}}
        <a href="{{ $drilldownUrl('aktivasi', 'aktif') }}"
        class="flex min-h-[112px] flex-col justify-center rounded-2xl border border-[#F0D9A9] bg-[#FBF1DC] px-4 py-3 shadow-sm md:col-span-4 transition hover:border-[#D6A14D] cursor-pointer">
            <p class="text-xs font-medium text-[#527D82]">
                Progress Aktivasi Kontrak
            </p>

            <div class="mt-1 flex items-center justify-between">

                <div>
                    <span class="text-3xl font-bold leading-none text-[#365F65]">
                        {{ $totalSistemAktif }}
                    </span>

                    <span class="text-base text-[#8BA5A2]">
                        / {{ $totalKerjaSama }}
                    </span>
                </div>

                <span class="text-base font-bold text-[#239B59]">
                    {{ $progressAktivasi }}%
                </span>

            </div>

            <div class="mt-2 h-2 overflow-hidden rounded-full bg-white/80">
                <div class="h-full rounded-full bg-[#2F9E5A]"
                     style="width: {{ min(100, $progressAktivasi) }}%">
                </div>
            </div>

            <p class="mt-1.5 text-[11px] text-[#8BA5A2]">
                Kerja sama yang telah disetujui aktivasi sistem
            </p>

        </a>


        {{-- TERMINAL 1 --}}
        <a href="{{ $drilldownUrl('aktivasi_terminal', 'T1') }}"
        class="flex min-h-[112px] flex-col justify-between rounded-2xl border border-[#DCE6E4] bg-white px-3 py-3 shadow-sm md:col-span-2 transition hover:border-[#527D82] hover:bg-[#F6F9F8] cursor-pointer">
            <span class="text-xs font-medium text-[#527D82]">
                T1
            </span>

            <div class="flex items-end justify-between">
                <span class="text-3xl font-bold leading-none text-[#365F65]">
                    {{ $totalTerminalT1 }}
                </span>

                <span class="text-sm font-bold text-[#365F65]">
                    {{ $persenTerminalT1 }}%
                </span>
            </div>

            <div class="h-2 overflow-hidden rounded-full bg-[#E2EBE9]">
                <div class="h-full rounded-full bg-[#365F65]"
                     style="width: {{ min(100, $persenTerminalT1) }}%">
                </div>
            </div>

            <p class="text-[11px] text-[#8BA5A2]">
                {{ $aktifTerminalT1 }} aktif dari {{ $totalTerminalT1 }} kerja sama
            </p>

        </a>


        {{-- TERMINAL 2 --}}
        <a href="{{ $drilldownUrl('aktivasi_terminal', 'T2') }}"
        class="flex min-h-[112px] flex-col justify-between rounded-2xl border border-[#DCE6E4] bg-white px-3 py-3 shadow-sm md:col-span-2 transition hover:border-[#D6A14D] hover:bg-[#F6F9F8] cursor-pointer">
            <span class="text-xs font-medium text-[#527D82]">
                T2
            </span>

            <div class="flex items-end justify-between">
                <span class="text-3xl font-bold leading-none text-[#365F65]">
                    {{ $totalTerminalT2 }}
                </span>

                <span class="text-sm font-bold text-[#D69A3F]">
                    {{ $persenTerminalT2 }}%
                </span>
            </div>

            <div class="h-2 overflow-hidden rounded-full bg-[#E2EBE9]">
                <div class="h-full rounded-full bg-[#D6A14D]"
                     style="width: {{ min(100, $persenTerminalT2) }}%">
                </div>
            </div>

            <p class="text-[11px] text-[#8BA5A2]">
                {{ $aktifTerminalT2 }} aktif dari {{ $totalTerminalT2 }} kerja sama
            </p>

        </a>

    </section>


    {{-- =========================================================
         GRAFIK DAN INFORMASI KONTRAK - COMPACT
    ========================================================== --}}

    <section class="grid grid-cols-1 gap-2.5 xl:grid-cols-12">

        {{-- DISTRIBUSI KONTRAK PER LOKASI --}}
        <div class="rounded-2xl border border-[#DCE6E4] bg-white p-3.5 shadow-sm xl:col-span-5">

            <h3 class="text-sm font-bold text-[#365F65]">
                Distribusi Kontrak per Lokasi
            </h3>

            @if ($totalPerLokasi > 0)

                <div class="mt-3 flex flex-col items-center justify-center gap-3 sm:flex-row">

                    {{-- DONUT CHART --}}
                    <div class="relative flex h-32 w-32 shrink-0 items-center justify-center rounded-full"
                         style="background: conic-gradient(
                            #365F65 0% {{ $persenT1 }}%,
                            #D6A14D {{ $persenT1 }}% 100%
                         );">

                        <div class="flex h-20 w-20 flex-col items-center justify-center rounded-full bg-white">

                            <span class="text-2xl font-bold leading-none text-[#365F65]">
                                {{ $totalPerLokasi }}
                            </span>

                            <span class="mt-1 text-[10px] text-[#8BA5A2]">
                                Kontrak
                            </span>

                        </div>
                    </div>


                    {{-- LEGENDA --}}
                    <div class="w-full space-y-2.5 sm:max-w-[180px]">

                        {{-- T1 --}}
                        <div class="flex items-center justify-between gap-2">

                            <div class="flex items-center gap-2">
                                <span class="h-3 w-3 shrink-0 rounded-full bg-[#365F65]"></span>

                                <span class="text-xs text-[#527D82]">
                                    T1
                                </span>
                            </div>

                            <div class="flex items-center gap-3">
                                <span class="text-xs font-bold text-[#365F65]">
                                    {{ $kontrakPerTerminal['T1'] ?? 0 }}
                                </span>

                                <span class="w-12 text-right text-xs text-[#8BA5A2]">
                                    {{ $persenT1 }}%
                                </span>
                            </div>

                        </div>


                        {{-- T2 --}}
                        <div class="flex items-center justify-between gap-2">

                            <div class="flex items-center gap-2">
                                <span class="h-3 w-3 shrink-0 rounded-full bg-[#D6A14D]"></span>

                                <span class="text-xs text-[#527D82]">
                                    T2
                                </span>
                            </div>

                            <div class="flex items-center gap-3">
                                <span class="text-xs font-bold text-[#365F65]">
                                    {{ $kontrakPerTerminal['T2'] ?? 0 }}
                                </span>

                                <span class="w-12 text-right text-xs text-[#8BA5A2]">
                                    {{ $persenT2 }}%
                                </span>
                            </div>

                        </div>

                    </div>

                </div>

            @else

                <div class="flex h-32 items-center justify-center">
                    <p class="text-xs text-[#8BA5A2]">
                        Belum ada data kontrak berdasarkan terminal.
                    </p>
                </div>

            @endif

        </div>


        {{-- INFORMASI KONTRAK --}}
        <div class="rounded-2xl border border-[#C8DCD8] bg-[#DCEAE7] p-3.5 shadow-sm xl:col-span-7">

            <h3 class="mb-3 text-sm font-bold text-[#365F65]">
                Informasi Kontrak
            </h3>

            <div class="grid grid-cols-1 gap-2.5 sm:grid-cols-2">

                {{-- BELUM AKTIF --}}
                <div class="flex min-h-[78px] items-center gap-3 rounded-xl bg-[#F5F8F7] px-3 py-2.5">

                    <div class="flex w-9 shrink-0 items-center justify-center">
                        <i class="fas fa-circle-xmark text-[28px] text-[#C66D62]"></i>
                    </div>

                    <div class="min-w-0">
                        <p class="text-xs font-medium text-[#527D82]">
                            Belum Aktif
                        </p>

                        <p class="mt-0.5 text-2xl font-bold leading-none text-[#365F65]">
                            {{ $belumAktif }}
                        </p>

                        <p class="mt-1 text-[10px] text-[#8BA5A2]">
                            Belum teraktivasi
                        </p>
                    </div>

                </div>


                {{-- PUSAT --}}
                <div class="flex min-h-[78px] items-center gap-3 rounded-xl bg-[#F5F8F7] px-3 py-2.5">

                    <div class="flex w-9 shrink-0 items-center justify-center">
                        <i class="fas fa-building text-[28px] text-[#D6A14D]"></i>
                    </div>

                    <div class="min-w-0">
                        <p class="text-xs font-medium text-[#527D82]">
                            Pusat
                        </p>

                        <p class="mt-0.5 text-2xl font-bold leading-none text-[#365F65]">
                            —
                        </p>

                        <p class="mt-1 text-[10px] text-[#8BA5A2]">
                            Data aktivasi Commercial Pusat belum tersedia
                        </p>
                    </div>

                </div>


                {{-- PROSES AKTIVASI --}}
                <div class="flex min-h-[78px] items-center gap-3 rounded-xl bg-[#F5F8F7] px-3 py-2.5">

                    <div class="flex w-9 shrink-0 items-center justify-center">
                        <i class="fas fa-hand text-[28px] text-[#527D82]"></i>
                    </div>

                    <div class="min-w-0">
                        <p class="text-xs font-medium text-[#527D82]">
                            Proses Aktivasi
                        </p>

                        <p class="mt-0.5 text-2xl font-bold leading-none text-[#365F65]">
                            {{ $belumAktif }}
                        </p>

                        <p class="mt-1 text-[10px] text-[#8BA5A2]">
                            Belum disetujui aktivasi sistem
                        </p>
                    </div>

                </div>


                {{-- KONTRAK DAN SISTEM --}}
                <div class="flex min-h-[78px] items-center gap-3 rounded-xl bg-[#F5F8F7] px-3 py-2.5">

                    <div class="flex w-9 shrink-0 items-center justify-center">
                        <i class="fas fa-hashtag text-[30px] text-[#2F9E5A]"></i>
                    </div>

                    <div class="min-w-0">
                        <p class="text-xs font-medium leading-tight text-[#527D82]">
                            Kontrak Aktif / Total Kerja Sama
                        </p>

                        <p class="mt-0.5 text-2xl font-bold leading-none text-[#365F65]">
                            {{ $totalSistemAktif }} / {{ $totalKerjaSama }}
                        </p>

                        <p class="mt-1 text-[10px] text-[#8BA5A2]">
                            Kerja sama yang disetujui aktivasi sistem
                        </p>
                    </div>

                </div>

            </div>

        </div>

    </section>

        {{-- =========================================================
         MONITORING PER KATEGORI TENANT
         Mengikuti tampilan Dashboard
    ========================================================== --}}

    @php
        /*
        |----------------------------------------------------------
        | DATA KATEGORI TENANT
        |----------------------------------------------------------
        | Data diambil dari collection $laporan yang sudah
        | mengikuti filter tahun, jenis usaha, terminal,
        | status kontrak, dan kelengkapan dokumen.
        */

        $daftarKategori = [
            'food_beverage' => [
                'label' => 'Food & Beverage',
                'alias' => ['F&B', 'Food & Beverage'],
            ],
            'services' => [
                'label' => 'Services',
                'alias' => ['Service', 'Services'],
            ],
            'retail' => [
                'label' => 'Retail',
                'alias' => ['Retail'],
            ],
            'lounge' => [
                'label' => 'Lounge',
                'alias' => ['Lounge'],
            ],
            'duty_free' => [
                'label' => 'Duty Free',
                'alias' => ['Duty Free'],
            ],
            'umkm' => [
                'label' => 'UMKM',
                'alias' => ['UMKM'],
            ],
        ];

        $perKategoriLaporan = [];

        foreach ($daftarKategori as $key => $kategori) {

            $dataKategori = $laporan->filter(function ($item) use ($kategori) {

                $jenis = trim(
                    $item['kerja_sama']->jenis_usaha ?? ''
                );

                return in_array(
                    $jenis,
                    $kategori['alias'],
                    true
                );
            });

            $jumlahTotal = $dataKategori->count();

            $jumlahAktif = $dataKategori->filter(function ($item) {

                return $item['kerja_sama']->aktivasi?->status === 'Disetujui';

            })->count();

            $perKategoriLaporan[$key] = [
                'label' => $kategori['label'],
                'total' => $jumlahTotal,
                'aktif' => $jumlahAktif,
            ];
        }

        $persentaseKategori = function ($aktif, $total) {

            return $total > 0
                ? round(($aktif / $total) * 100, 1)
                : 0;
        };
    @endphp


    {{-- JUDUL MONITORING KATEGORI --}}
    <section>

        <p class="mb-2 text-sm font-bold text-[#365F65]">
            Monitoring per Kategori Tenant
        </p>

        {{-- GRID ENAM KARTU --}}
        <div class="grid grid-cols-2 gap-3 md:grid-cols-3 xl:grid-cols-6">

            @foreach ($perKategoriLaporan as $key => $d)

                @php
                    $persentaseKategoriAktif = $persentaseKategori(
                        $d['aktif'],
                        $d['total']
                    );

                    $warnaPersentase = $d['total'] > 0
                        && $persentaseKategoriAktif >= 100
                            ? 'text-[#2F9E5A]'
                            : 'text-[#D6812B]';
                @endphp

                <a href="{{ $drilldownUrl('kategori', $d['label']) }}"
                    class="block rounded-2xl border border-[#DCE6E4] bg-white p-3.5 shadow-sm transition hover:border-[#527D82] hover:bg-[#F6F9F8] cursor-pointer">

                    {{-- NAMA KATEGORI --}}
                    <p class="truncate text-xs font-semibold text-[#527D82]"
                       title="{{ $d['label'] }}">
                        {{ $d['label'] }}
                    </p>

                    {{-- JUMLAH DAN PERSENTASE --}}
                    <div class="mt-1 flex items-end justify-between gap-2">

                        <p class="text-2xl font-bold leading-none text-[#365F65]">
                            {{ $d['total'] }}
                        </p>

                        <p class="text-sm font-bold {{ $warnaPersentase }}">
                            {{ $persentaseKategoriAktif }}%
                        </p>

                    </div>

                    {{-- PROGRESS BAR --}}
                    <div class="mt-1.5 h-1.5 overflow-hidden rounded-full bg-[#E2EBE9]">

                        <div class="h-full rounded-full bg-[#2F9E5A]"
                             style="width: {{ min(100, $persentaseKategoriAktif) }}%">
                        </div>

                    </div>

                    {{-- KETERANGAN --}}
                    <p class="mt-1 text-[10px] text-[#8BA5A2]">
                        {{ $d['aktif'] }} aktif dari {{ $d['total'] }} kontrak
                    </p>

                </a>

            @endforeach

        </div>

    </section>

        {{-- =========================================================
     REKAPITULASI PERIODE KONTRAK DAN KELENGKAPAN DOKUMEN
========================================================== --}}

<section class="grid grid-cols-1 lg:grid-cols-2 items-stretch gap-4">

    @php
        $totalKeputusan = $kontrakLintas + $kontrakPerpanjangan;

        $persenLintas = $totalKeputusan > 0
            ? round(($kontrakLintas / $totalKeputusan) * 100, 1)
            : 0;

        $persenPerpanjangan = $totalKeputusan > 0
            ? round(($kontrakPerpanjangan / $totalKeputusan) * 100, 1)
            : 0;

        $sudutLintas = $persenLintas * 3.6;
    @endphp

    <div class="h-full rounded-2xl border border-[#DCE6E4] bg-white p-4 shadow-sm">

    {{-- HEADER --}}
    <div class="mb-4">
        <h2 class="text-base font-bold text-[#365F65]">
            Rekapitulasi Periode Kontrak
        </h2>

        <p class="mt-1 text-xs text-[#8BA5A2]">
            Ringkasan keputusan perpanjangan kerja sama.
        </p>
    </div>

    {{-- CONTENT --}}
    <div class="grid grid-cols-1 items-center gap-4 sm:grid-cols-[1fr_145px]">

        {{-- INFORMASI LINTAS DAN PERPANJANGAN --}}
        <div class="space-y-3">

            {{-- LINTAS --}}
            <a href="{{ $drilldownUrl('perpanjangan', 'lintas') }}"
            class="flex items-center justify-between gap-3 rounded-xl bg-[#F5F8F7] p-3 transition hover:bg-[#EDF4F1] cursor-pointer">
                <div class="flex min-w-0 items-center gap-3">

                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-[#F7EBD8]">
                        <span class="text-lg leading-none">📄</span>
                    </div>

                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-[#365F65]">
                            Lintas
                        </p>

                        <p class="text-[11px] text-[#8BA5A2]">
                            Tidak memperpanjang kerja sama
                        </p>
                    </div>

                </div>

                <div class="text-right">
                    <p class="text-xl font-bold text-[#365F65]">
                        {{ number_format($kontrakLintas) }}
                    </p>

                    <p class="text-[10px] text-[#8BA5A2]">
                        {{ $persenLintas }}%
                    </p>
                </div>

            </a>


            {{-- PERPANJANGAN --}}
            <a href="{{ $drilldownUrl('perpanjangan', 'perpanjangan') }}"
            class="flex items-center justify-between gap-3 rounded-xl bg-[#F5F8F7] p-3 transition hover:bg-[#EDF4F1] cursor-pointer">
                <div class="flex min-w-0 items-center gap-3">

                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-[#E2EBE9]">
                        <span class="text-lg leading-none">📝</span>
                    </div>

                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-[#365F65]">
                            Perpanjangan
                        </p>

                        <p class="text-[11px] text-[#8BA5A2]">
                            Ingin memperpanjang kerja sama
                        </p>
                    </div>

                </div>

                <div class="text-right">
                    <p class="text-xl font-bold text-[#365F65]">
                        {{ number_format($kontrakPerpanjangan) }}
                    </p>

                    <p class="text-[10px] text-[#8BA5A2]">
                        {{ $persenPerpanjangan }}%
                    </p>
                </div>

            </a>

        </div>


        {{-- GRAFIK DONUT --}}
        <div class="flex flex-col items-center justify-center">

            <div
                class="relative flex h-[116px] w-[116px] items-center justify-center rounded-full"
                style="background: conic-gradient(
                    #D6A14D 0deg {{ $sudutLintas }}deg,
                    #365F65 {{ $sudutLintas }}deg 360deg
                );">

                {{-- LUBANG DONUT --}}
                <div class="flex h-[76px] w-[76px] flex-col items-center justify-center rounded-full bg-white">

                    <span class="text-2xl font-bold leading-none text-[#365F65]">
                        {{ $totalKeputusan }}
                    </span>

                    <span class="mt-1 text-[10px] text-[#8BA5A2]">
                        Total
                    </span>

                </div>

            </div>

            {{-- LEGENDA --}}
            <div class="mt-3 flex flex-col gap-1.5">

                <div class="flex items-center gap-2">
                    <span class="h-2.5 w-2.5 rounded-full bg-[#D6A14D]"></span>
                    <span class="text-[10px] text-[#527D82]">Lintas</span>
                </div>

                <div class="flex items-center gap-2">
                    <span class="h-2.5 w-2.5 rounded-full bg-[#365F65]"></span>
                    <span class="text-[10px] text-[#527D82]">Perpanjangan</span>
                </div>

            </div>

        </div>

    </div>

</div>

@php
    $persenKontrakBelum = $totalDokumenKontrak > 0
        ? round(($dokumenKontrakBelumLengkap / $totalDokumenKontrak) * 100)
        : 0;

    $persenBABelum = $totalBA > 0
        ? round(($baBelumLengkap / $totalBA) * 100)
        : 0;
@endphp

        {{-- =====================================================
             KELENGKAPAN DOKUMEN
        ====================================================== --}}
        <div class="h-full rounded-2xl border border-[#DCE6E4] bg-white p-5 shadow-sm">

            <div class="mb-5">
                <h2 class="text-base font-bold text-[#365F65]">
                    Kelengkapan Dokumen
                </h2>

                <p class="mt-1 text-xs text-[#8BA5A2]">
                    Monitoring ketersediaan dokumen kerja sama.
                </p>
            </div>

            <div class="space-y-5">

                {{-- DOKUMEN KONTRAK --}}
                <div>

                <a href="{{ $drilldownUrl('dokumen_kontrak', 'belum_lengkap') }}"
                class="block transition hover:opacity-80 cursor-pointer">
                        <div class="flex items-center gap-2">

                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-[#E7EFED] text-[#527D82]">
                                <span class="text-lg leading-none">📑</span>
                            </div>

                            <div>
                                <p class="text-xs font-semibold text-[#365F65]">
                                    Kontrak Mitra
                                </p>

                                <p class="text-[11px] text-[#8BA5A2]">
                                    Tanpa nomor kontrak mitra
                                </p>
                            </div>

                        </div>

                        <span class="text-xl font-bold text-[#365F65]">
                            {{ $dokumenKontrakBelumLengkap }}
                        </span>

                    </div>

                    <div class="mt-3 h-2 overflow-hidden rounded-full bg-[#E2EBE9]">
                        <div
                            class="h-full rounded-full bg-[#D6A14D]"
                            style="width: {{ $persenKontrakBelum }}%">
                        </div>
                    </div>

                    <p class="mt-1 text-right text-xs text-[#8BA5A2]">
                        {{ $persenKontrakBelum }}% belum lengkap
                    </p>

                </a>


                <div class="border-t border-[#EDF2F0]"></div>


                {{-- BERITA ACARA FINAL --}}
                <div>

                <a href="{{ $drilldownUrl('berita_acara', 'belum_lengkap') }}"
                class="block transition hover:opacity-80 cursor-pointer">
                        <div class="flex items-center gap-2">

                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-[#E7EFED] text-[#527D82]">
                                <span class="text-lg leading-none">📁</span>
                            </div>

                            <div>
                                <p class="text-xs font-semibold text-[#365F65]">
                                    Berita Acara Final
                                </p>

                                <p class="text-[11px] text-[#8BA5A2]">
                                    Dokumen final belum tersedia
                                </p>
                            </div>

                        </div>

                        <span class="text-xl font-bold text-[#365F65]">
                            {{ $baBelumLengkap }}
                        </span>

                    </div>

                    <div class="mt-3 h-2 overflow-hidden rounded-full bg-[#E2EBE9]">
                        <div
                            class="h-full rounded-full bg-[#D6A14D]"
                            style="width: {{ $persenBABelum }}%">
                        </div>
                    </div>

                    <p class="mt-1 text-right text-xs text-[#8BA5A2]">
                        {{ $persenBABelum }}% belum lengkap
                    </p>

                </a>

            </div>

        </div>

    </section>

    
{{-- TABEL LAPORAN --}}
<div class="overflow-hidden rounded-2xl border border-[#DCE6E4] bg-white">

    {{-- HEADER JUDUL TABEL --}}
    <div class="flex items-center justify-between gap-3 rounded-t-2xl bg-[#365F65] px-5 py-3">

        <h2 class="text-sm font-bold text-white">
            Daftar Laporan Kerja Sama
        </h2>

        <span class="inline-flex items-center rounded-full bg-white/15 px-3 py-1 text-xs font-semibold text-white">
            {{ $laporan->count() }} Data
        </span>

    </div>

    {{-- ISI TABEL --}}
    <div class="overflow-x-auto">

        <table class="w-full min-w-[900px] table-fixed text-sm">

            {{-- HEADER KOLOM --}}
            <thead class="bg-[#E3ECE9]">
                <tr>
                    <th class="w-[6%] px-5 py-3.5 text-left text-xs font-bold text-[#365F65]">
                        No.
                    </th>

                    <th class="w-[19%] px-5 py-3.5 text-left text-xs font-bold text-[#365F65]">
                        No. Kerja Sama
                    </th>

                    <th class="w-[27%] px-5 py-3.5 text-left text-xs font-bold text-[#365F65]">
                        Mitra Usaha
                    </th>

                    <th class="w-[16%] px-5 py-3.5 text-left text-xs font-bold text-[#365F65]">
                        Kode Ruang
                    </th>

                    <th class="w-[16%] px-5 py-3.5 text-center text-xs font-bold text-[#365F65]">
                        Status
                    </th>

                    <th class="w-[16%] px-5 py-3.5 text-center text-xs font-bold text-[#365F65]">
                        Aksi
                    </th>
                </tr>
            </thead>

            {{-- BODY TABEL --}}
            <tbody class="divide-y divide-[#E5ECE9]">

                @forelse ($laporan as $index => $item)

                    @php
                        $ks = $item['kerja_sama'];
                        $lokasiData = $item['lokasi'];
                        $status = $item['status_laporan'];

                        $statusClass = match ($status) {
                            'Aktif' => 'bg-[#E0F2E9] text-[#059669]',
                            'Nonaktif' => 'bg-[#E8ECEB] text-[#64748B]',
                            'Draft' => 'bg-[#FFF0D9] text-[#A16207]',
                            'Belum Dibuat' => 'bg-[#E8ECEB] text-[#64748B]',
                            default => 'bg-[#F1F6F4] text-[#527D82]',
                        };
                    @endphp

                    <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-[#F6F9F8]' }} transition hover:bg-[#EDF4F1]">

                        {{-- NOMOR --}}
                        <td class="px-5 py-3.5 text-xs text-[#8BA5A2]">
                            {{ $index + 1 }}.
                        </td>

                        {{-- NOMOR KERJA SAMA --}}
                        <td class="px-5 py-3.5 font-medium text-[#365F65]">
                            {{ $ks->nomor_kerja_sama ?? '-' }}
                        </td>

                        {{-- MITRA USAHA --}}
                        <td class="px-5 py-3.5">
                            <span class="font-medium text-[#527D82]">
                                {{ $ks->mitra_usaha ?? '-' }}
                            </span>

                            @if (filled($ks->brand))
                                <span class="text-[#A8BDB9]"> · </span>
                                <span class="text-[#8BA5A2]">
                                    {{ $ks->brand }}
                                </span>
                            @endif
                        </td>

                        {{-- KODE RUANG --}}
                        <td class="px-5 py-3.5 text-sm text-[#527D82]">
                            {{ $lokasiData?->kode_ruang ?? '-' }}
                        </td>

                        {{-- STATUS --}}
                        <td class="px-5 py-3.5 text-center">
                            <span class="inline-flex items-center justify-center gap-1.5 whitespace-nowrap rounded-full px-3 py-1 text-xs font-semibold {{ $statusClass }}">

                                <span class="h-1.5 w-1.5 rounded-full bg-current"></span>

                                {{ $status }}

                            </span>
                        </td>

                        {{-- AKSI --}}
                        <td class="px-5 py-3.5 text-center">
                            <a href="{{ route('laporan.detail', $ks->id) }}"
                               class="inline-flex items-center justify-center rounded-lg bg-[#E3ECE9] px-4 py-2 text-xs font-semibold text-[#365F65] transition hover:bg-[#D5E3DF]">

                                Detail

                            </a>
                        </td>

                    </tr>

                @empty

                    <tr class="bg-white">
                        <td colspan="6" class="px-5 py-12 text-center">

                            <div class="flex flex-col items-center">

                                <i class="fas fa-folder-open mb-3 text-3xl text-[#A8BDB9]"></i>

                                <p class="font-semibold text-[#527D82]">
                                    Tidak ada data laporan
                                </p>

                                <p class="mt-1 text-xs text-[#8BA5A2]">
                                    Coba ubah filter tahun atau kriteria pencarian.
                                </p>

                            </div>

                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>

    {{-- PENUTUP HALAMAN LAPORAN --}}

</div>

@endsection