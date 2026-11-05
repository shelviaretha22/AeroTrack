
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
@endphp

<div class="space-y-6">

    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-[#263F42]">
                Laporan
            </h1>
            <p class="text-sm text-gray-500 mt-1">
                Ringkasan data kerja sama, kontrak, aktivasi, dokumen, dan pendapatan tenant.
            </p>
        </div>

        <div class="text-sm text-gray-500">
            Periode laporan:
            <span class="font-semibold text-[#365F65]">
                Tahun {{ $tahun }}
            </span>
        </div>
    </div>

    {{-- RINGKASAN KONTRAK --}}
    <div>
        <h2 class="text-base font-bold text-[#263F42] mb-3">
            Ringkasan Kontrak
        </h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">

            {{-- TOTAL KONTRAK --}}
<div class="bg-white rounded-2xl border border-[#DCE6E4] p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 font-medium">Total Kontrak</p>
                        <h3 class="text-2xl font-bold text-[#365F65] mt-2">
                            {{ number_format($totalKontrak) }}
                        </h3>
                    </div>
                    <div class="w-11 h-11 rounded-xl bg-[#E7EFED] flex items-center justify-center text-[#365F65]">
                        <i class="fas fa-file-contract text-lg"></i>
                    </div>
                </div>
            </div>

            {{-- KONTRAK AKTIF --}}
            <div class="bg-white rounded-2xl border border-[#DCE6E4] p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 font-medium">Kontrak Aktif</p>
                        <h3 class="text-2xl font-bold text-emerald-700 mt-2">
                            {{ number_format($kontrakAktif) }}
                        </h3>
                    </div>
                    <div class="w-11 h-11 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-700">
                        <i class="fas fa-check-circle text-lg"></i>
                    </div>
                </div>
            </div>

            {{-- KONTRAK NONAKTIF --}}
            <div class="bg-white rounded-2xl border border-[#DCE6E4] p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 font-medium">Kontrak Nonaktif</p>
                        <h3 class="text-2xl font-bold text-gray-600 mt-2">
                            {{ number_format($kontrakNonaktif) }}
                        </h3>
                    </div>
                    <div class="w-11 h-11 rounded-xl bg-gray-100 flex items-center justify-center text-gray-600">
                        <i class="fas fa-file-circle-xmark text-lg"></i>
                    </div>
                </div>
            </div>

            {{-- KONTRAK PROSES --}}
            <div class="bg-white rounded-2xl border border-[#DCE6E4] p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 font-medium">Kontrak dalam Proses</p>
                        <h3 class="text-2xl font-bold text-amber-600 mt-2">
                            {{ number_format($kontrakProses) }}
                        </h3>
                    </div>
                    <div class="w-11 h-11 rounded-xl bg-amber-50 flex items-center justify-center text-amber-600">
                        <i class="fas fa-clock text-lg"></i>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- REKAPITULASI PERIODE KONTRAK --}}
<div class="mt-6">

    <h2 class="text-base font-bold text-[#263F42] mb-3">
        Rekapitulasi Periode Kontrak
    </h2>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

        {{-- KONTRAK LINTAS --}}
        <div class="bg-white rounded-2xl border border-[#DCE6E4] p-5 shadow-sm">

            <div class="flex items-center justify-between">

                <div>
                    <p class="text-xs text-gray-500 font-medium">
                        Lintas
                    </p>

                    <h3 class="text-2xl font-bold text-[#365F65] mt-2">
                        {{ number_format($kontrakLintas) }}
                    </h3>

                    <p class="text-xs text-gray-400 mt-2">
                        Kontrak dengan keputusan tidak memperpanjang
                    </p>
                </div>

                <div class="w-11 h-11 rounded-xl bg-[#E7EFED] flex items-center justify-center text-[#365F65]">
                    <i class="fas fa-file-circle-xmark text-lg"></i>
                </div>

            </div>

        </div>

        {{-- KONTRAK PERPANJANGAN --}}
        <div class="bg-white rounded-2xl border border-[#DCE6E4] p-5 shadow-sm">

            <div class="flex items-center justify-between">

                <div>
                    <p class="text-xs text-gray-500 font-medium">
                        Perpanjangan
                    </p>

                    <h3 class="text-2xl font-bold text-emerald-700 mt-2">
                        {{ number_format($kontrakPerpanjangan) }}
                    </h3>

                    <p class="text-xs text-gray-400 mt-2">
                        Kontrak dengan keputusan ingin memperpanjang
                    </p>
                </div>

                <div class="w-11 h-11 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-700">
                    <i class="fas fa-file-signature text-lg"></i>
                </div>

            </div>

        </div>

    </div>

</div>

    {{-- RINGKASAN PENDAPATAN --}}
    <div>
        <h2 class="text-base font-bold text-[#263F42] mb-3">
            Ringkasan Pendapatan dan Tagihan
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

            <div class="bg-white rounded-2xl border border-[#DCE6E4] p-5 shadow-sm">
                <p class="text-xs text-gray-500 font-medium">Total Tagihan</p>
                <h3 class="text-xl font-bold text-[#365F65] mt-2">
                    {{ $formatRupiah($totalTagihan) }}
                </h3>
                <p class="text-xs text-gray-400 mt-2">Periode tahun {{ $tahun }}</p>
            </div>

            <div class="bg-white rounded-2xl border border-[#DCE6E4] p-5 shadow-sm">
                <p class="text-xs text-gray-500 font-medium">Invoice Diberikan</p>
                <h3 class="text-xl font-bold text-emerald-700 mt-2">
                    {{ $formatRupiah($invoiceDiberikan) }}
                </h3>
                <p class="text-xs text-gray-400 mt-2">Tagihan yang invoice-nya telah diberikan</p>
            </div>

            <div class="bg-white rounded-2xl border border-[#DCE6E4] p-5 shadow-sm">
                <p class="text-xs text-gray-500 font-medium">Pending Invoice</p>
                <h3 class="text-xl font-bold text-amber-600 mt-2">
                    {{ $formatRupiah($pendingInvoice) }}
                </h3>
                <p class="text-xs text-gray-400 mt-2">Tagihan yang invoice-nya belum diberikan</p>
            </div>

        </div>
    </div>


    
{{-- GRAFIK KELENGKAPAN DOKUMEN DAN ANALISIS DATA --}}

@php
    $totalAnalisisJenis = $komposisiJenisUsaha->sum();
    $totalAnalisisTerminal = $kontrakPerTerminal->sum();

    $warnaGrafik = [
        '#087F68',
        '#D6A14D',
        '#365F65',
        '#8BA5A2',
        '#A8BDB9',
        '#C5D4D1',
    ];

    $persenKontrakBelum = $totalDokumenKontrak > 0
        ? round(($dokumenKontrakBelumLengkap / $totalDokumenKontrak) * 100)
        : 0;

    $persenBABelum = $totalBA > 0
        ? round(($baBelumLengkap / $totalBA) * 100)
        : 0;
@endphp

<div class="grid grid-cols-1 lg:grid-cols-12 gap-3">

    {{-- KELOMPOK KELENGKAPAN DOKUMEN --}}
    <div class="lg:col-span-4 bg-white rounded-2xl border border-[#DCE6E4] p-4 shadow-sm">

        <h2 class="font-bold text-[#263F42] text-sm">
            Kelengkapan Dokumen
        </h2>

        <p class="text-[11px] text-gray-500 mt-1">
            Jumlah kerja sama yang belum memiliki dokumen yang diperlukan.
        </p>

        <div class="grid grid-cols-2 gap-2 mt-3">

            {{-- KONTRAK MITRA --}}
            <div class="rounded-xl border border-[#E1EAE8] p-3">

                <div class="flex items-start gap-2">

                    <div class="w-8 h-8 shrink-0 rounded-full bg-[#EAF3F1] flex items-center justify-center">
                        <i class="fas fa-file-alt text-[#16856C]"></i>
                    </div>

                    <p class="text-[10px] leading-tight text-[#365F65] font-medium">
                        Kontrak tanpa nomor kontrak mitra
                    </p>

                </div>

                <p class="text-2xl font-bold text-[#263F42] mt-1 ml-10">
                    {{ $dokumenKontrakBelumLengkap }}
                </p>

                <div class="ml-10 mt-2 h-1.5 rounded-full bg-[#E2EBE9] overflow-hidden">
                    <div class="h-full rounded-full bg-[#16856C]"
                        style="width: {{ $persenKontrakBelum }}%">
                    </div>
                </div>

                <p class="text-right text-[10px] font-semibold text-[#16856C] mt-1">
                    {{ $persenKontrakBelum }}%
                </p>

            </div>

            {{-- BA FINAL --}}
            <div class="rounded-xl border border-[#E1EAE8] p-3">

                <div class="flex items-start gap-2">

                    <div class="w-8 h-8 shrink-0 rounded-full bg-[#EAF3F1] flex items-center justify-center">
                        <i class="fas fa-file-alt text-[#16856C]"></i>
                    </div>

                    <p class="text-[10px] leading-tight text-[#365F65] font-medium">
                        BA final belum tersedia
                    </p>

                </div>

                <p class="text-2xl font-bold text-[#263F42] mt-1 ml-10">
                    {{ $baBelumLengkap }}
                </p>

                <div class="ml-10 mt-2 h-1.5 rounded-full bg-[#E2EBE9] overflow-hidden">
                    <div class="h-full rounded-full bg-[#16856C]"
                        style="width: {{ $persenBABelum }}%">
                    </div>
                </div>

                <p class="text-right text-[10px] font-semibold text-[#16856C] mt-1">
                    {{ $persenBABelum }}%
                </p>

            </div>

        </div>
    </div>


    {{-- KELOMPOK ANALISIS DATA --}}
    <div class="lg:col-span-8 bg-white rounded-2xl border border-[#DCE6E4] p-4 shadow-sm">

        <h2 class="font-bold text-[#263F42] text-sm">
            Analisis Data
        </h2>

        <div class="grid grid-cols-1 sm:grid-cols-3 divide-y sm:divide-y-0 sm:divide-x divide-[#DCE6E4] mt-2">

            {{-- GRAFIK KOMPOSISI STATUS KONTRAK --}}
            <div class="px-2 py-2">

                <h3 class="text-[11px] font-bold text-center text-[#263F42]">
                    Komposisi Status Kontrak
                </h3>

                <div class="flex flex-col items-center mt-2">

                    <div class="relative w-20 h-20 rounded-full flex items-center justify-center"
                        style="background: conic-gradient(
                            #087F68 0% {{ $persenAktif }}%,
                            #D6A14D {{ $persenAktif }}% {{ $persenAktif + $persenNonaktif }}%,
                            #DCE6E4 {{ $persenAktif + $persenNonaktif }}% 100%
                        );">

                        <div class="w-12 h-12 rounded-full bg-white flex flex-col items-center justify-center">
                            <span class="text-lg font-bold text-[#263F42]">
                                {{ number_format($totalKomposisi) }}
                            </span>
                            <span class="text-[9px] text-gray-400">Total</span>
                        </div>

                    </div>

                    <div class="w-full mt-2 space-y-1">

                        @foreach ([
                            ['nama' => 'Aktif', 'warna' => '#087F68'],
                            ['nama' => 'Nonaktif', 'warna' => '#D6A14D'],
                            ['nama' => 'Proses', 'warna' => '#DCE6E4'],
                        ] as $status)

                            <div class="flex items-center justify-between text-[10px]">

                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full"
                                        style="background: {{ $status['warna'] }}">
                                    </span>

                                    <span>{{ $status['nama'] }}</span>
                                </div>

                                <span class="font-semibold text-[#365F65]">
                                    {{ $komposisiStatus[$status['nama']] }}
                                </span>

                            </div>

                        @endforeach

                    </div>

                </div>
            </div>


            {{-- GRAFIK JENIS USAHA --}}
            <div class="px-2 py-2">

                <h3 class="text-[11px] font-bold text-center text-[#263F42]">
                    Jenis Usaha
                </h3>

                @php
                    $sudutJenis = 0;
                @endphp

                <div class="flex flex-col items-center mt-2">

                    <div class="relative w-20 h-20 rounded-full flex items-center justify-center"
                        style="background: conic-gradient(
                            
@php
    $bagianJenis = [];
    $sudutJenis = 0;
    $indexJenis = 0;

    foreach ($komposisiJenisUsaha as $nama => $jumlah) {
        $persen = $totalAnalisisJenis > 0
            ? ($jumlah / $totalAnalisisJenis) * 100
            : 0;

        $akhir = $sudutJenis + $persen;

        $warna = $warnaGrafik[
            $indexJenis % count($warnaGrafik)
        ];

        $bagianJenis[] = $warna
            . ' ' . $sudutJenis . '% ' . $akhir . '%';

        $sudutJenis = $akhir;
        $indexJenis++;
    }

    echo $bagianJenis
        ? implode(', ', $bagianJenis)
        : '#DCE6E4 0% 100%';
@endphp

                        );">

                        <div class="w-12 h-12 rounded-full bg-white flex flex-col items-center justify-center">
                            <span class="text-lg font-bold text-[#263F42]">
                                {{ $totalAnalisisJenis }}
                            </span>
                            <span class="text-[9px] text-gray-400">Total</span>
                        </div>

                    </div>

                    <div class="w-full mt-2 space-y-1">

                        @foreach ($komposisiJenisUsaha as $nama => $jumlah)

                            <div class="flex items-center justify-between text-[10px]">

                                <div class="flex items-center gap-1.5 min-w-0">
                                    <span class="w-2 h-2 rounded-full shrink-0"
                                        style="background: {{ $warnaGrafik[$loop->index % count($warnaGrafik)] }}">
                                    </span>

                                    <span class="truncate">{{ $nama }}</span>
                                </div>

                                <div class="flex items-center gap-2 shrink-0">
                                    <span class="font-semibold">{{ $jumlah }}</span>

                                    <span class="text-gray-400">
                                        {{ $totalAnalisisJenis > 0 ? round(($jumlah / $totalAnalisisJenis) * 100) : 0 }}%
                                    </span>
                                </div>

                            </div>

                        @endforeach

                    </div>

                </div>
            </div>


            
{{-- GRAFIK KONTRAK PER TERMINAL --}}

@php
    $totalAnalisisTerminal = $kontrakPerTerminal->sum();

    $warnaTerminal = [
        'T1' => '#365F65',
        'T2' => '#8BA5A2',
    ];

    $bagianTerminal = [];
    $sudutTerminal = 0;

    foreach ($kontrakPerTerminal as $nama => $jumlah) {
        $persen = $totalAnalisisTerminal > 0
            ? ($jumlah / $totalAnalisisTerminal) * 100
            : 0;

        $akhir = $sudutTerminal + $persen;

        $warna = $warnaTerminal[$nama] ?? '#A8BDB9';

        $bagianTerminal[] = "{$warna} {$sudutTerminal}% {$akhir}%";

        $sudutTerminal = $akhir;
    }

    $gradientTerminal = count($bagianTerminal) > 0
        ? implode(', ', $bagianTerminal)
        : '#DCE6E4 0% 100%';
@endphp

<div class="px-2 py-2">

    <h3 class="text-[11px] font-bold text-center text-[#263F42]">
        Kontrak per Terminal
    </h3>

    <div class="flex flex-col items-center mt-2">

        {{-- DONUT CHART --}}
        <div
            class="relative w-20 h-20 rounded-full flex items-center justify-center"
            style="background: conic-gradient({{ $gradientTerminal }});"
        >

            <div class="w-12 h-12 rounded-full bg-white flex flex-col items-center justify-center">

                <span class="text-lg font-bold text-[#263F42]">
                    {{ $totalAnalisisTerminal }}
                </span>

                <span class="text-[9px] text-gray-400">
                    Total
                </span>

            </div>

        </div>

        {{-- LEGENDA TERMINAL --}}
        <div class="w-full mt-2 space-y-1">

            @foreach ($kontrakPerTerminal as $nama => $jumlah)

                <div class="flex items-center justify-between text-[10px]">

                    <div class="flex items-center gap-1.5">

                        <span
                            class="w-2 h-2 rounded-full"
                            style="background-color: {{ $warnaTerminal[$nama] ?? '#A8BDB9' }};"
                        ></span>

                        <span>{{ $nama }}</span>

                    </div>

                    <div class="flex items-center gap-2">

                        <span class="font-semibold">
                            {{ $jumlah }}
                        </span>

                        <span class="text-gray-400">
                            {{ $totalAnalisisTerminal > 0
                                ? round(($jumlah / $totalAnalisisTerminal) * 100)
                                : 0 }}%
                        </span>

                    </div>

                </div>

            @endforeach

        </div>

    </div>

</div>

        </div>
    </div>

</div>

    {{-- FILTER LAPORAN --}}
    <div class="bg-white rounded-2xl border border-[#DCE6E4] p-5 shadow-sm">

        <div class="flex items-center gap-2 mb-4">
            <i class="fas fa-filter text-[#365F65]"></i>
            <h2 class="font-semibold text-[#263F42]">
                Filter Laporan
            </h2>
        </div>

        <form action="{{ route('laporan') }}" method="GET">

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-3">

                {{-- TAHUN --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-2">
                        Tahun
                    </label>

                    <select name="tahun"
                        class="w-full rounded-xl border-gray-200 text-sm focus:border-[#365F65] focus:ring-[#365F65]">
                        @for ($i = $tahunSekarang - 5; $i <= $tahunSekarang + 1; $i++)
                            <option value="{{ $i }}" @selected($tahun == $i)>
                                {{ $i }}
                            </option>
                        @endfor
                    </select>
                </div>

                {{-- JENIS USAHA --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-2">
                        Jenis Usaha
                    </label>

                    <select name="jenis_usaha"
                        class="w-full rounded-xl border-gray-200 text-sm focus:border-[#365F65] focus:ring-[#365F65]">

                        <option value="">Semua Jenis</option>

                        @foreach (['F&B', 'Retail', 'Service', 'Lounge', 'Duty Free', 'UMKM'] as $jenis)
                            <option value="{{ $jenis }}" @selected($jenisUsaha == $jenis)>
                                {{ $jenis }}
                            </option>
                        @endforeach

                    </select>
                </div>

                {{-- TERMINAL --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-2">
                        Terminal
                    </label>

                    <select name="terminal"
                        class="w-full rounded-xl border-gray-200 text-sm focus:border-[#365F65] focus:ring-[#365F65]">

                        <option value="">Semua Terminal</option>
                        <option value="T1" @selected($terminal == 'T1')>Terminal 1</option>
                        <option value="T2" @selected($terminal == 'T2')>Terminal 2</option>

                    </select>
                </div>

                {{-- STATUS --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-2">
                        Status Kontrak
                    </label>

                    <select name="status"
                        class="w-full rounded-xl border-gray-200 text-sm focus:border-[#365F65] focus:ring-[#365F65]">

                        <option value="">Semua Status</option>
                        <option value="Proses" @selected($statusFilter == 'Proses')>Proses</option>
                        <option value="Aktif" @selected($statusFilter == 'Aktif')>Aktif</option>
                        <option value="Nonaktif" @selected($statusFilter == 'Nonaktif')>Nonaktif</option>

                    </select>
                </div>

                
                {{-- DOKUMEN --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-2">
                        Status Dokumen
                    </label>

                    <select name="dokumen"
                        class="w-full rounded-xl border-gray-200 text-sm focus:border-[#365F65] focus:ring-[#365F65]">

                        <option value="">Semua Dokumen</option>
                        <option value="Lengkap" @selected($dokumenFilter == 'Lengkap')>Lengkap</option>
                        <option value="Belum Lengkap" @selected($dokumenFilter == 'Belum Lengkap')>Belum Lengkap</option>

                    </select>
                </div>

            </div> {{-- PENUTUP GRID FILTER --}}

            {{-- TOMBOL FILTER --}}
            <div class="flex flex-wrap justify-end gap-2 mt-4">

                <a href="{{ route('laporan') }}"
                    class="px-4 py-2 rounded-xl border border-gray-200 text-sm font-medium text-gray-600 hover:bg-gray-50">
                    Reset
                </a>

                <button type="submit"
                    class="px-5 py-2 rounded-xl bg-[#365F65] text-white text-sm font-semibold hover:bg-[#294B50]">
                    <i class="fas fa-filter mr-1"></i>
                    Terapkan Filter
                </button>

            </div>

                    </form>
    </div>

    {{-- TABEL LAPORAN --}}
<div class="bg-white rounded-2xl border border-[#DCE6E4] shadow-sm overflow-hidden mt-8">
        <div class="p-5 border-b border-[#DCE6E4] flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">

            <div>
                <h2 class="font-bold text-[#263F42]">
                    Daftar Laporan Kerja Sama
                </h2>
                <p class="text-xs text-gray-400 mt-1">
                    Menampilkan data kerja sama berdasarkan filter yang dipilih.
                </p>
            </div>

            <span class="text-sm font-semibold text-[#365F65]">
                {{ $laporan->count() }} Data
            </span>

        </div>

        <div class="overflow-x-auto">

            <table class="w-full min-w-[1000px] text-sm">

                <thead class="bg-[#F3F6F5] text-gray-500">
                    <tr>
                        <th class="px-5 py-4 text-left font-semibold">No.</th>
                        <th class="px-5 py-4 text-left font-semibold">No. Kerja Sama</th>
                        <th class="px-5 py-4 text-left font-semibold">Mitra Usaha</th>
                        <th class="px-5 py-4 text-left font-semibold">Lokasi</th>
                        <th class="px-5 py-4 text-left font-semibold">Periode Kerja Sama</th>
                        <th class="px-5 py-4 text-center font-semibold">Status</th>
                        <th class="px-5 py-4 text-center font-semibold">Dokumen</th>
                        <th class="px-5 py-4 text-center font-semibold">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-[#EDF1F0]">

                    @forelse ($laporan as $index => $item)

                        @php
                            $ks = $item['kerja_sama'];
                            $lokasiData = $item['lokasi'];
                            $status = $item['status_laporan'];

                            $statusClass = match ($status) {
                                'Aktif' => 'bg-emerald-50 text-emerald-700',
                                'Nonaktif' => 'bg-gray-100 text-gray-600',
                                default => 'bg-amber-50 text-amber-700',
                            };
                        @endphp

                        <tr class="hover:bg-[#F8FAF9]">

                            <td class="px-5 py-4 text-gray-500">
                                {{ $index + 1 }}
                            </td>

                            <td class="px-5 py-4">
                                <span class="font-semibold text-[#365F65]">
                                    {{ $ks->nomor_kerja_sama ?? '-' }}
                                </span>
                            </td>

                            <td class="px-5 py-4">
                                <p class="font-semibold text-gray-700">
                                    {{ $ks->mitra_usaha ?? '-' }}
                                </p>
                                <p class="text-xs text-gray-400 mt-1">
                                    {{ $ks->brand ?? '-' }}
                                </p>
                            </td>

                            <td class="px-5 py-4">
                                @if ($lokasiData)
                                    <p class="font-medium text-gray-700">
                                        {{ $lokasiData->terminal ?? '-' }}
                                    </p>
                                    <p class="text-xs text-gray-400 mt-1">
                                        {{ $lokasiData->kode_tampilan ?? $lokasiData->kode_ruang ?? '-' }}
                                    </p>
                                @else
                                    <span class="text-gray-400">Belum ditentukan</span>
                                @endif
                            </td>

                            <td class="px-5 py-4 text-gray-600">
                                <p>
                                    {{ $ks->tanggal_mulai
                                        ? \Carbon\Carbon::parse($ks->tanggal_mulai)->format('d/m/Y')
                                        : '-' }}
                                </p>
                                <p class="text-xs text-gray-400 mt-1">
                                    s.d.
                                    {{ $ks->tanggal_berakhir
                                        ? \Carbon\Carbon::parse($ks->tanggal_berakhir)->format('d/m/Y')
                                        : '-' }}
                                </p>
                            </td>

                            <td class="px-5 py-4 text-center">
                                <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold {{ $statusClass }}">
                                    {{ $status }}
                                </span>
                            </td>

                            <td class="px-5 py-4 text-center">
                                @if ($item['dokumen_lengkap'])
                                    <span class="text-emerald-700 text-xs font-semibold">
                                        <i class="fas fa-check-circle mr-1"></i>
                                        Lengkap
                                    </span>
                                @else
                                    <span class="text-amber-600 text-xs font-semibold">
                                        <i class="fas fa-exclamation-circle mr-1"></i>
                                        Belum Lengkap
                                    </span>
                                @endif
                            </td>

                            <td class="px-5 py-4 text-center">
                                <a href="{{ route('laporan.detail', $ks->id) }}"
                                    class="inline-flex items-center gap-1 px-3 py-2 rounded-lg bg-[#E7EFED] text-[#365F65] text-xs font-semibold hover:bg-[#DCE6E4]">

                                    <i class="fas fa-eye"></i>
                                    Detail

                                </a>
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="8" class="px-5 py-12 text-center">

                                <div class="flex flex-col items-center">
                                    <i class="fas fa-folder-open text-3xl text-gray-300 mb-3"></i>

                                    <p class="font-semibold text-gray-500">
                                        Tidak ada data laporan
                                    </p>

                                    <p class="text-xs text-gray-400 mt-1">
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

</div>

@endsection
