
@extends('layouts.app')

@section('page-title', 'Kontrak')

@section('content')

<div class="space-y-6">

    {{-- =========================================================
         HEADER
    ========================================================== --}}
    <div>
        <p class="text-sm text-[#8BA5A2]">
            Management / Kontrak
        </p>

        <h2 class="mt-1 text-2xl font-bold text-[#365F65]">
            Data Kontrak
        </h2>

        <p class="mt-1 text-sm text-[#7A9290]">
            Kelola informasi kontrak tenant berdasarkan kerja sama yang telah dibuat.
        </p>
    </div>

    {{-- =========================================================
         SUCCESS MESSAGE
    ========================================================== --}}
    @if (session('success'))
        <div
            role="status"
            class="rounded-xl border border-[#CFE0DC] bg-[#EEF6F3] px-4 py-3 text-sm text-[#365F65]"
        >
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div
            role="alert"
            class="rounded-xl border border-[#F0D5C9] bg-[#FFF4EF] px-4 py-3 text-sm text-[#9A4F35]"
        >
            {{ session('error') }}
        </div>
    @endif

    {{-- =========================================================
         SUMMARY DASHBOARD
    ========================================================== --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">

        {{-- TOTAL KONTRAK --}}
        <div class="rounded-2xl border border-[#E2EBE9] bg-white p-5">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-[#7A9290]">
                        Total Kontrak
                    </p>
                    <h3 class="mt-3 text-3xl font-bold text-[#365F65]">
                        {{ $totalKontrak ?? 0 }}
                    </h3>
                    <p class="mt-2 text-xs text-[#8BA5A2]">
                        Seluruh kontrak yang tercatat
                    </p>
                </div>

                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-[#E8F0EE]">
                    <svg class="h-6 w-6 text-[#365F65]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                              d="M7 3.75h7l4 4v12.5H7a2 2 0 0 1-2-2v-12.5a2 2 0 0 1 2-2Z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                              d="M14 3.75v4h4M9 12h6M9 16h6"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- KONTRAK AKTIF --}}
        <div class="rounded-2xl border border-[#E2EBE9] bg-white p-5">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-[#7A9290]">
                        Kontrak Aktif
                    </p>
                    <h3 class="mt-3 text-3xl font-bold text-[#087F73]">
                        {{ $kontrakAktif ?? 0 }}
                    </h3>
                    <p class="mt-2 text-xs text-[#8BA5A2]">
                        Sudah di-ACC dan belum berakhir
                    </p>
                </div>

                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-[#E5F5EF]">
                    <svg class="h-6 w-6 text-[#087F73]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="m5 12 4 4L19 6"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- KONTRAK NON-AKTIF --}}
        <div class="rounded-2xl border border-[#E2EBE9] bg-white p-5">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-[#7A9290]">
                        Kontrak Non-Aktif
                    </p>
                    <h3 class="mt-3 text-3xl font-bold text-[#A47720]">
                        {{ $kontrakNonAktif ?? 0 }}
                    </h3>
                    <p class="mt-2 text-xs text-[#8BA5A2]">
                        Masa kontrak telah berakhir
                    </p>
                </div>

                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-[#FFF4DF]">
                    <svg class="h-6 w-6 text-[#A47720]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="9" stroke-width="1.8"/>
                        <path stroke-linecap="round" stroke-width="1.8"
                              d="M12 7v5l3 2"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- KONTRAK DALAM PROSES --}}
        <div class="rounded-2xl border border-[#E2EBE9] bg-white p-5">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-[#7A9290]">
                        Dalam Proses
                    </p>
                    <h3 class="mt-3 text-3xl font-bold text-[#527D82]">
                        {{ $kontrakDalamProses ?? 0 }}
                    </h3>
                    <p class="mt-2 text-xs text-[#8BA5A2]">
                        Kontrak yang belum selesai di-ACC
                    </p>
                </div>

                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-[#EDF2F1]">
                    <svg class="h-6 w-6 text-[#527D82]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="9" stroke-width="1.8"/>
                        <path stroke-linecap="round" stroke-width="1.8"
                              d="M12 7v5l3 2"/>
                    </svg>
                </div>
            </div>
        </div>

    </div>

    {{-- =========================================================
         GRAFIK DASHBOARD
    ========================================================== --}}
    <div class="grid grid-cols-1 gap-5 xl:grid-cols-2">

        {{-- GRAFIK DONAT --}}
        <div class="rounded-2xl border border-[#E2EBE9] bg-white p-5">
            <div class="mb-5">
                <h3 class="text-base font-bold text-[#365F65]">
                    Komposisi Status Kontrak
                </h3>
                <p class="mt-1 text-sm text-[#7A9290]">
                    Perbandingan status seluruh kontrak
                </p>
            </div>

            <div class="relative mx-auto h-[280px] max-w-[380px]">
                <canvas id="grafikStatusKontrak"></canvas>
            </div>
        </div>

        {{-- GRAFIK BATANG --}}
        <div class="rounded-2xl border border-[#E2EBE9] bg-white p-5">
            <div class="mb-5">
                <h3 class="text-base font-bold text-[#365F65]">
                    Kontrak per Tahun
                </h3>
                <p class="mt-1 text-sm text-[#7A9290]">
                    Jumlah kontrak berdasarkan tahun pengajuan
                </p>
            </div>

            <div class="relative h-[280px]">
                <canvas id="grafikKontrakTahunan"></canvas>
            </div>
        </div>

    </div>

    {{-- =========================================================
         SEARCH & SORT
    ========================================================== --}}
    <div class="rounded-2xl border border-[#E2EBE9] bg-white p-5">
        <form
            method="GET"
            action="{{ route('kontrak') }}"
            class="grid grid-cols-1 gap-4 md:grid-cols-4"
        >
            {{-- SEARCH --}}
            <div class="md:col-span-2">
                <label
                    for="search"
                    class="mb-1.5 block text-xs font-semibold text-[#527D82]"
                >
                    Cari Kontrak
                </label>

                <input
                    type="text"
                    id="search"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari nomor kerja sama, brand, atau lokasi..."
                    class="w-full rounded-xl border border-[#DCE6E4] bg-[#F8FAF9] px-4 py-3 text-sm text-[#365F65] outline-none focus:border-[#527D82] focus:ring-2 focus:ring-[#527D82]/10"
                >
            </div>

            {{-- SORT --}}
            <div>
                <label
                    for="sort"
                    class="mb-1.5 block text-xs font-semibold text-[#527D82]"
                >
                    Urutkan
                </label>

                <select
                    id="sort"
                    name="sort"
                    class="w-full rounded-xl border border-[#DCE6E4] bg-[#F8FAF9] px-4 py-3 text-sm text-[#365F65] outline-none focus:border-[#527D82] focus:ring-2 focus:ring-[#527D82]/10"
                >
                    <option value="newest" {{ request('sort', 'newest') === 'newest' ? 'selected' : '' }}>
                        Terbaru
                    </option>

                    <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>
                        Terlama
                    </option>

                    <option value="az" {{ request('sort') === 'az' ? 'selected' : '' }}>
                        Nama Brand A-Z
                    </option>

                    <option value="za" {{ request('sort') === 'za' ? 'selected' : '' }}>
                        Nama Brand Z-A
                    </option>

                    <option value="active" {{ request('sort') === 'active' ? 'selected' : '' }}>
                        Aktif
                    </option>

                    <option value="inactive" {{ request('sort') === 'inactive' ? 'selected' : '' }}>
                        Non-Aktif
                    </option>
                </select>
            </div>

            {{-- BUTTON FILTER --}}
            <div class="flex items-end justify-end">
                <button
                    type="submit"
                    class="rounded-xl bg-[#E8F0EE] px-5 py-3 text-sm font-semibold text-[#365F65] transition hover:bg-[#DCE9E6]"
                >
                    Terapkan Filter
                </button>
            </div>
        </form>
    </div>

    {{-- =========================================================
         TABEL GABUNGAN KONTRAK DAN KANDIDAT
    ========================================================== --}}
    @php
        $daftarKandidat = collect($kandidatKontrak ?? []);
        $daftarKontrak = collect($kontraks ?? []);
        $nomorBaris = 0;
    @endphp

    <div class="overflow-hidden rounded-2xl border border-[#E2EBE9] bg-white">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[1500px]">

                <thead class="bg-[#F3F6F5]">
                    <tr>
                        @foreach ([
                            'No.',
                            'Nomor Kontrak',
                            'Nomor Kerja Sama',
                            'Tenant',
                            'Lokasi',
                            'Durasi',
                            'Status',
                            'Konfirmasi Perpanjangan',
                            'Note',
                            'Aksi',
                        ] as $judul)
                            <th class="px-4 py-4 text-left text-xs font-bold uppercase tracking-wide text-[#7A9290]">
                                {{ $judul }}
                            </th>
                        @endforeach
                    </tr>
                </thead>

                <tbody class="divide-y divide-[#EEF2F1]">

                    {{-- =================================================
                         KANDIDAT YANG BELUM MEMILIKI KONTRAK
                    ================================================== --}}
                    @foreach ($daftarKandidat as $kandidat)
                        @php
                            $nomorBaris++;

                            $tenantKandidat = $kandidat->tenant;
                            $lokasiKandidat = $kandidat->lokasi;

                            $tanggalMulai = $kandidat->tanggal_mulai;
                            $tanggalBerakhir = $kandidat->tanggal_berakhir;
                        @endphp

                        <tr class="transition hover:bg-[#FAFCFB]">

                            <td class="px-4 py-4 text-sm text-[#7A9290]">
                                {{ $nomorBaris }}
                            </td>

                            <td class="px-4 py-4 text-sm text-[#9AA6A4]">
                                -
                                <p class="mt-1 text-xs italic">
                                    Belum dibuat
                                </p>
                            </td>

                            <td class="px-4 py-4 text-sm font-semibold text-[#365F65]">
                                {{ $kandidat->nomor_kerja_sama ?? '-' }}
                            </td>

                            <td class="px-4 py-4">
                                <p class="text-sm font-semibold text-[#365F65]">
                                    {{ $tenantKandidat?->mitra_usaha ?? $kandidat->mitra_usaha ?? '-' }}
                                </p>

                                <p class="mt-1 text-sm text-[#527D82]">
                                    {{ $tenantKandidat?->nama_tenant ?? $kandidat->brand ?? '-' }}
                                </p>
                            </td>

                            <td class="px-4 py-4">
                                <p class="text-sm font-semibold text-[#365F65]">
                                    {{ $lokasiKandidat?->kode_ruang ?? '-' }}
                                </p>

                                <p class="mt-1 text-xs text-[#7A9290]">
                                    {{ $lokasiKandidat?->lokasi ?? '-' }}
                                </p>

                                @if ($lokasiKandidat)
                                    <p class="mt-1 text-xs text-[#8BA5A2]">
                                        Terminal {{ $lokasiKandidat->terminal ?? '-' }}
                                        - Lantai {{ $lokasiKandidat->lantai ?? '-' }}
                                    </p>
                                @endif
                            </td>

                            <td class="px-4 py-4 text-sm text-[#527D82]">
                                @if ($tanggalMulai && $tanggalBerakhir)
                                    {{ \Carbon\Carbon::parse($tanggalMulai)->format('d M Y') }}
                                    -
                                    {{ \Carbon\Carbon::parse($tanggalBerakhir)->format('d M Y') }}

                                    <p class="mt-1 text-xs text-[#8BA5A2]">
                                        ({{ \Carbon\Carbon::parse($tanggalMulai)
                                         ->diffInMonths(\Carbon\Carbon::parse($tanggalBerakhir)) }} bulan)
                                    </p>
                                @else
                                    -
                                @endif
                            </td>

                            {{-- STATUS KANDIDAT --}}
                            <td class="px-4 py-4">
                                <span class="inline-flex rounded-lg bg-[#F3F6F5] px-3 py-1.5 text-xs font-semibold text-[#527D82]">
                                    Belum Kontrak
                                </span>
                            </td>

                            {{-- KONFIRMASI PERPANJANGAN KANDIDAT --}}
                            <td class="px-4 py-4 text-sm text-[#8BA5A2]">
                                -
                            </td>

                            {{-- NOTE KANDIDAT --}}
                            <td class="px-4 py-4 text-sm text-[#527D82]">
                                Kerja sama sudah memenuhi syarat untuk dibuatkan kontrak.

                                <p class="mt-1 text-xs text-[#8BA5A2]">
                                    Aktivasi: {{ $kandidat->aktivasi?->nomor_pengajuan ?? '-' }}
                                </p>
                            </td>

                            {{-- AKSI KANDIDAT --}}
                            <td class="px-4 py-4">
                                <form
                                    method="POST"
                                    action="{{ route('kontrak.store', $kandidat->id) }}"
                                    onsubmit="return confirm('Buat kontrak untuk kerja sama ini?')"
                                >
                                    @csrf

                                    <button
                                        type="submit"
                                        class="whitespace-nowrap rounded-lg bg-[#087F73] px-3 py-2.5 text-xs font-semibold text-white transition hover:bg-[#06685F]"
                                    >
                                        Buat Kontrak
                                    </button>
                                </form>
                            </td>

                        </tr>
                    @endforeach

                    {{-- =================================================
                         KONTRAK YANG SUDAH DIBUAT
                    ================================================== --}}
                    @foreach ($daftarKontrak as $kontrak)
                        @php
                            $nomorBaris++;

                            $kerjaSama = $kontrak->kerjaSama;
                            $tenant = $kontrak->tenant;
                            $lokasi = $kontrak->lokasi;

                            $tanggalAwal = $kerjaSama?->tanggal_mulai;
                            $tanggalAkhir = $kerjaSama?->tanggal_berakhir;

                            $isExpired = $tanggalAkhir
                                ? \Carbon\Carbon::parse($tanggalAkhir)
                                    ->startOfDay()
                                    ->lt(now()->startOfDay())
                                : false;

                            $akanHabis = $tanggalAkhir
                                ? (
                                    !$isExpired &&
                                    now()->startOfDay()->diffInDays(
                                        \Carbon\Carbon::parse($tanggalAkhir)->startOfDay()
                                    ) <= 30
                                )
                                : false;

                            $statusKontrak = $kontrak->status ?? 'Draft';
                            $sudahSelesai = $statusKontrak === 'Selesai';

                            $batasKonfirmasi = $tanggalAkhir
                                ? \Carbon\Carbon::parse($tanggalAkhir)
                                    ->subMonthNoOverflow()
                                    ->startOfDay()
                                : null;

                            $bolehKonfirmasi = $sudahSelesai
                                && $tanggalAkhir
                                && now()->startOfDay()->greaterThanOrEqualTo($batasKonfirmasi);
                        @endphp

                        <tr class="{{ $isExpired ? 'bg-[#F7F8F8]' : 'hover:bg-[#FAFCFB]' }} transition">

                            <td class="px-4 py-4 text-sm text-[#7A9290]">
                                {{ $nomorBaris }}
                            </td>

                            <td class="px-4 py-4">
                                <p class="text-sm font-semibold text-[#365F65]">
                                    {{ $kontrak->nomor_kontrak ?? '-' }}
                                </p>
                            </td>

                            <td class="px-4 py-4 text-sm font-semibold text-[#365F65]">
                                {{ $kerjaSama?->nomor_kerja_sama ?? '-' }}
                            </td>

                            <td class="px-4 py-4">
                                <p class="text-sm font-semibold text-[#365F65]">
                                    {{ $tenant?->mitra_usaha ?? $kerjaSama?->mitra_usaha ?? '-' }}
                                </p>

                                <p class="mt-1 text-sm text-[#527D82]">
                                    {{ $tenant?->nama_tenant ?? $kerjaSama?->brand ?? '-' }}
                                </p>

                                @if ($tenant?->nama_pic)
                                    <p class="mt-1 text-xs text-[#8BA5A2]">
                                        PIC: {{ $tenant->nama_pic }}
                                    </p>
                                @endif
                            </td>

                            <td class="px-4 py-4">
                                <p class="text-sm font-semibold text-[#365F65]">
                                    {{ $lokasi?->kode_ruang ?? '-' }}
                                </p>

                                <p class="mt-1 text-xs text-[#7A9290]">
                                    {{ $lokasi?->lokasi ?? '-' }}
                                </p>

                                @if ($lokasi)
                                    <p class="mt-1 text-xs text-[#8BA5A2]">
                                        Terminal {{ $lokasi->terminal ?? '-' }}
                                        - Lantai {{ $lokasi->lantai ?? '-' }}
                                    </p>
                                @endif
                            </td>

                            {{-- DURASI --}}
                            <td class="px-4 py-4 text-sm text-[#527D82]">
                                @if ($tanggalAwal && $tanggalAkhir)
                                    {{ \Carbon\Carbon::parse($tanggalAwal)->format('d M Y') }}
                                    -
                                    {{ \Carbon\Carbon::parse($tanggalAkhir)->format('d M Y') }}

                                    <p class="mt-1 text-xs text-[#8BA5A2]">
                                        ({{ \Carbon\Carbon::parse($tanggalAwal)
                                            ->diffInMonths(\Carbon\Carbon::parse($tanggalAkhir)) }} bulan)
                                    </p>
                                @else
                                    -
                                @endif
                            </td>

                            {{-- STATUS KONTRAK --}}
                            <td class="px-4 py-4">
                                @if ($sudahSelesai)
                                    <span class="inline-flex rounded-lg bg-[#D1FAE5] px-3 py-1.5 text-xs font-semibold text-[#047857]">
                                        Selesai
                                    </span>
                                @elseif ($statusKontrak === 'Draft')
                                    <span class="inline-flex rounded-lg bg-[#FFF0D9] px-3 py-1.5 text-xs font-semibold text-[#A16207]">
                                        Draft
                                    </span>
                                @else
                                    <span class="inline-flex rounded-lg bg-[#DBEAFE] px-3 py-1.5 text-xs font-semibold text-[#1D4ED8]">
                                        {{ $statusKontrak }}
                                    </span>
                                @endif
                            </td>

                            {{-- KONFIRMASI PERPANJANGAN --}}
                            <td class="px-4 py-4">
                                @if (!$sudahSelesai)
                                    <span class="text-xs text-[#8BA5A2]">
                                        -
                                    </span>

                                @elseif ($kontrak->status_perpanjangan === 'Ingin Memperpanjang')
                                    <span class="inline-flex rounded-lg bg-[#D1FAE5] px-3 py-1.5 text-xs font-semibold text-[#047857]">
                                        Ingin Memperpanjang
                                    </span>

                                    @if ($kontrak->tanggal_konfirmasi)
                                        <p class="mt-1 text-xs text-[#8BA5A2]">
                                            {{ $kontrak->tanggal_konfirmasi->format('d M Y') }}
                                        </p>
                                    @endif

                                @elseif ($kontrak->status_perpanjangan === 'Tidak Memperpanjang')
                                    <span class="inline-flex rounded-lg bg-[#FEE2E2] px-3 py-1.5 text-xs font-semibold text-[#B91C1C]">
                                        Tidak Memperpanjang
                                    </span>

                                    @if ($kontrak->tanggal_konfirmasi)
                                        <p class="mt-1 text-xs text-[#8BA5A2]">
                                            {{ $kontrak->tanggal_konfirmasi->format('d M Y') }}
                                        </p>
                                    @endif

                                @elseif ($bolehKonfirmasi)
                                    <button
                                        type="button"
                                        onclick="bukaModalPerpanjangan(
                                            {{ $kontrak->id }},
                                            @js($kontrak->nomor_kontrak)
                                        )"
                                        class="whitespace-nowrap rounded-lg bg-[#365F65] px-3 py-2.5 text-xs font-semibold text-white transition hover:bg-[#294F55]"
                                    >
                                        Konfirmasi
                                    </button>

                                @else
                                    <span class="text-xs text-[#8BA5A2]">
                                        Belum waktunya
                                    </span>

                                    <p class="mt-1 text-xs text-[#8BA5A2]">
                                        Aktif mulai
                                        {{ $batasKonfirmasi?->format('d M Y') ?? '-' }}
                                    </p>
                                @endif
                            </td>

                            {{-- NOTE --}}
                            <td class="px-4 py-4 text-sm text-[#527D82]">
                                @if ($isExpired)
                                    Kontrak telah berakhir.
                                @elseif ($akanHabis)
                                    <span class="font-semibold text-[#A47720]">
                                        Kontrak akan habis.
                                    </span>
                                @elseif ($sudahSelesai)
                                    Kontrak telah selesai diproses.
                                @elseif ($statusKontrak === 'Draft')
                                    Dokumen kontrak sedang disusun.
                                @else
                                    Kontrak sedang dalam proses.
                                @endif
                            </td>

                            
{{-- AKSI --}}
<td class="px-4 py-4">

    @if (filled($kontrak->final_pdf_path))

        <a
            href="{{ route('kontrak.finalPdf', $kontrak->id) }}"
            target="_blank"
            rel="noopener noreferrer"
            class="inline-flex items-center justify-center whitespace-nowrap rounded-lg border border-[#DCE6E4] bg-white px-3 py-2.5 text-xs font-semibold text-[#365F65] transition hover:bg-[#F3F6F5]"
        >
            Detail
        </a>

    @elseif ($sudahSelesai)

        <span class="text-xs text-[#8BA5A2]">
            PDF final belum tersedia
        </span>

    @elseif (blank($kontrak->nama_commercial) || blank($kontrak->jabatan_commercial))

        <a
            href="{{ route('kontrak.show', $kontrak->id) }}"
            class="inline-flex items-center justify-center whitespace-nowrap rounded-lg bg-[#365F65] px-3 py-2.5 text-xs font-semibold text-white transition hover:bg-[#294F55]"
        >
            Lanjutkan
        </a>

    @else

        <a
            href="{{ route('kontrak.uploadFinal', $kontrak->id) }}"
            class="inline-flex items-center justify-center whitespace-nowrap rounded-lg bg-[#365F65] px-3 py-2.5 text-xs font-semibold text-white transition hover:bg-[#294F55]"
        >
            Lanjutkan
        </a>

    @endif

</td>


                        </tr>
                    @endforeach

                    {{-- EMPTY STATE --}}
                    @if ($nomorBaris === 0)
                        <tr>
                            <td colspan="10" class="px-5 py-16 text-center">
                                <div class="mx-auto max-w-md">
                                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-[#F3F6F5]">
                                        <span class="text-xl text-[#8BA5A2]">📄</span>
                                    </div>

                                    <p class="mt-4 text-sm font-semibold text-[#527D82]">
                                        Belum ada data kontrak
                                    </p>

                                    <p class="mt-1 text-xs leading-5 text-[#8BA5A2]">
                                        Data akan muncul setelah kerja sama memenuhi
                                        syarat kontrak atau kontrak berhasil dibuat.
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endif

                </tbody>
            </table>
        </div>
    </div>

    {{-- =========================================================
         KETERANGAN
    ========================================================== --}}
    <div class="rounded-xl border border-[#DCE6E4] bg-[#F3F6F5] px-4 py-3">
        <p class="text-xs leading-5 text-[#7A9290]">
            <span class="font-semibold text-[#527D82]">
                Keterangan:
            </span>

            Kontrak yang telah melewati tanggal akhir akan ditampilkan
            dengan warna abu-abu dan berstatus
            <strong>Non-Aktif</strong>.
            Jika masa kontrak tersisa maksimal 1 bulan,
            sistem akan memberikan catatan
            <strong>Kontrak akan habis</strong>.
        </p>
    </div>

</div>

{{-- =========================================================
     DATA GRAFIK
========================================================== --}}
<script>
    window.dataStatusKontrak = @json($dataStatus ?? []);
    window.dataKontrakTahunan = @json($dataTahunan ?? []);
</script>

{{-- =========================================================
     MODAL KONFIRMASI PERPANJANGAN
========================================================== --}}
<div
    id="modalPerpanjangan"
    class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 p-4"
    role="dialog"
    aria-modal="true"
    aria-labelledby="judulModalPerpanjangan"
>
    <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">

        <h3
            id="judulModalPerpanjangan"
            class="text-lg font-bold text-[#365F65]"
        >
            Konfirmasi Perpanjangan
        </h3>

        <p class="mt-2 text-sm text-[#7A9290]">
            Nomor kontrak:
            <span
                id="nomorKontrakModal"
                class="font-semibold text-[#365F65]"
            ></span>
        </p>

        <p class="mt-3 text-sm text-[#527D82]">
            Catat keputusan tenant setelah dikonfirmasi oleh petugas komersial.
        </p>

        <form
            id="formPerpanjangan"
            method="POST"
            class="mt-5"
        >
            @csrf

            <label
                for="status_perpanjangan"
                class="mb-2 block text-sm font-semibold text-[#365F65]"
            >
                Keputusan tenant
            </label>

            <select
                id="status_perpanjangan"
                name="status_perpanjangan"
                required
                class="w-full rounded-xl border border-[#DCE6E4] bg-[#F8FAF9] px-4 py-3 text-sm text-[#365F65] focus:border-[#527D82] focus:outline-none focus:ring-2 focus:ring-[#527D82]/10"
            >
                <option value="">Pilih keputusan</option>
                <option value="Ingin Memperpanjang">
                    Ingin Memperpanjang
                </option>
                <option value="Tidak Memperpanjang">
                    Tidak Memperpanjang
                </option>
            </select>

            <div class="mt-6 flex justify-end gap-3">
                <button
                    type="button"
                    onclick="tutupModalPerpanjangan()"
                    class="rounded-xl border border-[#DCE6E4] px-4 py-2.5 text-sm font-semibold text-[#527D82] transition hover:bg-[#F3F6F5]"
                >
                    Batal
                </button>

                <button
                    id="tombolSimpanPerpanjangan"
                    type="submit"
                    class="rounded-xl bg-[#087F73] px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-[#06685F]"
                >
                    Simpan Konfirmasi
                </button>
            </div>
        </form>
    </div>
</div>

{{-- =========================================================
     JAVASCRIPT MODAL
========================================================== --}}
<script>
    function bukaModalPerpanjangan(id, nomorKontrak) {
        const modal = document.getElementById('modalPerpanjangan');
        const form = document.getElementById('formPerpanjangan');
        const pilihan = document.getElementById('status_perpanjangan');

        form.action = `{{ url('/kontrak') }}/${id}/konfirmasi-perpanjangan`;

        document.getElementById('nomorKontrakModal').textContent = nomorKontrak;
        pilihan.value = '';

        modal.classList.remove('hidden');
        modal.classList.add('flex');

        pilihan.focus();
    }

    function tutupModalPerpanjangan() {
        const modal = document.getElementById('modalPerpanjangan');

        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            tutupModalPerpanjangan();
        }
    });

    document.getElementById('modalPerpanjangan').addEventListener('click', function (event) {
        if (event.target === this) {
            tutupModalPerpanjangan();
        }
    });
</script>

@endsection
