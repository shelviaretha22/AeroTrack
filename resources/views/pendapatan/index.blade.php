@extends('layouts.app')

@section('page-title', 'Pendapatan')

@section('content')

@php
    $namaBulan = [
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember',
    ];
@endphp

<div class="min-h-screen bg-[#F3F6F5] py-6">

    <div class="mx-auto max-w-7xl">

        {{-- HEADER --}}
        <div class="mb-8 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-[#365F65]">
                    Pendapatan
                </h1>

                <p class="mt-1 text-sm text-[#8BA5A2]">
                    Monitoring tagihan dan penyerahan invoice tenant.
                </p>
            </div>

            <div class="rounded-xl border border-[#DCE6E4] bg-white px-5 py-3 shadow-sm">
                <p class="text-xs text-[#8BA5A2]">Periode laporan</p>

                <p class="text-lg font-bold text-[#365F65]">
                    {{ $namaBulan[$bulanAwal] }} -
                    {{ $namaBulan[$bulanAkhir] }}
                    {{ $tahun }}
                </p>
            </div>
        </div>

        {{-- NOTIFIKASI --}}
        @if (session('success'))
            <div class="mb-6 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ $errors->first() }}
            </div>
        @endif

        {{-- RINGKASAN --}}
        <div class="mb-8 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">

            {{-- TOTAL TAGIHAN --}}
            <div class="rounded-2xl border border-[#DCE6E4] bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-medium text-[#8BA5A2]">
                        Total Tagihan
                    </p>

                    <div class="rounded-xl bg-blue-50 p-3 text-blue-600">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8V6m0 2v8m0 0v2m0-2c-1.657 0-3-.895-3-2m6 0c0 1.105-1.343 2-3 2m9-4a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>

                <h2 class="mt-4 text-xl font-bold text-[#365F65]">
                    Rp {{ number_format($totalTagihan, 0, ',', '.') }}
                </h2>

                <p class="mt-1 text-xs text-[#A0B3B0]">
                    Total tagihan {{ $namaBulan[$bulanAwal] }} -
                    {{ $namaBulan[$bulanAkhir] }} {{ $tahun }}
                </p>
            </div>

            {{-- INVOICE DIBERIKAN --}}
            <div class="rounded-2xl border border-[#DCE6E4] bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-medium text-[#8BA5A2]">
                        Invoice Diberikan
                    </p>

                    <div class="rounded-xl bg-green-50 p-3 text-green-600">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                </div>

                <h2 class="mt-4 text-xl font-bold text-green-600">
                    Rp {{ number_format($totalInvoiceDiberikan, 0, ',', '.') }}
                </h2>

                <p class="mt-1 text-xs text-[#A0B3B0]">
                    {{ $jumlahInvoiceDiberikan }} invoice sudah diberikan
                </p>
            </div>

            {{-- INVOICE BELUM DIBERIKAN --}}
            <div class="rounded-2xl border border-[#DCE6E4] bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-medium text-[#8BA5A2]">
                        Pending Meter
                    </p>

                    <div class="rounded-xl bg-orange-50 p-3 text-orange-500">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>

                <h2 class="mt-4 text-xl font-bold text-orange-500">
                    Rp {{ number_format($totalInvoiceBelumDiberikan, 0, ',', '.') }}
                </h2>

                <p class="mt-1 text-xs text-[#A0B3B0]">
                    {{ $jumlahInvoiceBelumDiberikan }} invoice belum diberikan
                </p>
            </div>

            {{-- JUMLAH TENANT --}}
            <div class="rounded-2xl border border-[#DCE6E4] bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-medium text-[#8BA5A2]">
                        Tenant Periode
                    </p>

                    <div class="rounded-xl bg-purple-50 p-3 text-purple-600">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2"
                                d="M17 20h5V4H2v16h5m10 0v-5a3 3 0 00-3-3H10a3 3 0 00-3 3v5m10 0H7m5-11a3 3 0 100-6 3 3 0 000 6z" />
                        </svg>
                    </div>
                </div>

                <h2 class="mt-4 text-xl font-bold text-[#365F65]">
                    {{ $pendapatanPerTenant->count() }} Tenant
                </h2>

                <p class="mt-1 text-xs text-[#A0B3B0]">
                    Memiliki tagihan pada periode terpilih
                </p>
            </div>

        </div>

        {{-- FILTER --}}
        <div class="mb-8 rounded-2xl border border-[#DCE6E4] bg-white p-5 shadow-sm">

            <form action="{{ route('pendapatan') }}" method="GET"
                class="flex flex-col gap-4">

                <div class="grid grid-cols-1 gap-4 md:grid-cols-4">

                    {{-- TAHUN --}}
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-[#527D82]">
                            Tahun
                        </label>

                        <input
                            type="number"
                            name="tahun"
                            value="{{ $tahun }}"
                            min="2000"
                            max="2100"
                            class="w-full rounded-xl border-[#DCE6E4] text-sm text-[#365F65] focus:border-[#527D82] focus:ring-[#527D82]"
                        >
                    </div>

                    {{-- BULAN AWAL --}}
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-[#527D82]">
                            Dari Bulan
                        </label>

                        <select
                            name="bulan_awal"
                            class="w-full rounded-xl border-[#DCE6E4] text-sm text-[#365F65] focus:border-[#527D82] focus:ring-[#527D82]"
                        >
                            @foreach ($namaBulan as $nomor => $nama)
                                <option value="{{ $nomor }}"
                                    @selected($bulanAwal == $nomor)>
                                    {{ $nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- BULAN AKHIR --}}
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-[#527D82]">
                            Sampai Bulan
                        </label>

                        <select
                            name="bulan_akhir"
                            class="w-full rounded-xl border-[#DCE6E4] text-sm text-[#365F65] focus:border-[#527D82] focus:ring-[#527D82]"
                        >
                            @foreach ($namaBulan as $nomor => $nama)
                                <option value="{{ $nomor }}"
                                    @selected($bulanAkhir == $nomor)>
                                    {{ $nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- CARI TENANT --}}
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-[#527D82]">
                            Cari Tenant
                        </label>

                        <input
                            type="text"
                            name="search"
                            value="{{ $search }}"
                            placeholder="Nama tenant atau mitra..."
                            class="w-full rounded-xl border-[#DCE6E4] text-sm text-[#365F65] placeholder-[#A0B3B0] focus:border-[#527D82] focus:ring-[#527D82]"
                        >
                    </div>

                </div>

                <div class="flex flex-wrap gap-2">
                    <button
                        type="submit"
                        class="rounded-xl bg-[#365F65] px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-[#294F55]"
                    >
                        Terapkan Filter
                    </button>

                    <a
                        href="{{ route('pendapatan') }}"
                        class="rounded-xl border border-[#DCE6E4] bg-white px-5 py-2.5 text-sm font-semibold text-[#527D82] transition hover:bg-[#F3F6F5]"
                    >
                        Reset
                    </a>
                </div>

            </form>
        </div>

        {{-- JUDUL DAFTAR TAGIHAN --}}
        <div class="mb-5">
            <h2 class="text-lg font-bold text-[#365F65]">
                Daftar Tagihan Tenant
            </h2>

            <p class="mt-1 text-sm text-[#8BA5A2]">
                Menampilkan tagihan {{ $namaBulan[$bulanAwal] }} -
                {{ $namaBulan[$bulanAkhir] }} {{ $tahun }}.
                Centang status setelah invoice diberikan kepada tenant.
            </p>
        </div>

        {{-- DAFTAR TENANT --}}
        @forelse ($pendapatanPerTenant as $tenantId => $daftarTagihan)

            @php
                $tagihanPertama = $daftarTagihan->first();

                $tenant = $tagihanPertama->tenant;
                $kerjaSama = $tagihanPertama->kerjaSama;

                $totalTenant = $daftarTagihan->sum('jumlah_tagihan');

                // Masa kontrak (dari data Kerja Sama).
                $kontrakMulai = $kerjaSama?->tanggal_mulai
                    ? \Carbon\Carbon::parse($kerjaSama->tanggal_mulai)->locale('id')
                    : null;
                $kontrakBerakhir = $kerjaSama?->tanggal_berakhir
                    ? \Carbon\Carbon::parse($kerjaSama->tanggal_berakhir)->locale('id')
                    : null;
                $durasiKontrak = null;
                if ($kontrakMulai && $kontrakBerakhir) {
                    $selisih = $kontrakMulai->copy()->startOfDay()
                        ->diff($kontrakBerakhir->copy()->addDay()->startOfDay());
                    $totalBulan = $selisih->y * 12 + $selisih->m;
                    $durasiKontrak = trim(
                        ($totalBulan > 0 ? $totalBulan . ' bulan' : '')
                        . ($selisih->d > 0 ? ' ' . $selisih->d . ' hari' : '')
                    );
                }

                $sudahDiberikan = $daftarTagihan
                    ->where('status_invoice', true)
                    ->count();

                $belumDiberikan = $daftarTagihan
                    ->where('status_invoice', false)
                    ->count();
            @endphp

            <div class="mb-6 overflow-hidden rounded-2xl border border-[#DCE6E4] bg-white shadow-sm">

                {{-- HEADER TENANT --}}
                <div class="border-b border-[#E6EEEC] px-5 py-5">

                    <div class="flex flex-col justify-between gap-4 md:flex-row md:items-center">

                        <div>
                            <div class="flex flex-wrap items-center gap-2">

                                <h3 class="text-lg font-bold text-[#365F65]">
                                    {{ $tenant->nama_tenant ?? '-' }}
                                </h3>

                                <span class="rounded-full bg-[#EAF1F0] px-3 py-1 text-xs font-semibold text-[#527D82]">
                                    {{ $tenant->bentuk_kerja_sama ?? '-' }}
                                </span>

                            </div>

                            <p class="mt-1 text-sm text-[#8BA5A2]">
                                {{ $tenant->mitra_usaha ?? '-' }}
                            </p>

                            <p class="mt-1 text-xs text-[#A0B3B0]">
                                {{ $kerjaSama->nomor_kerja_sama ?? '-' }}
                            </p>

                            {{-- MASA KONTRAK --}}
                            <p class="mt-2 inline-flex flex-wrap items-center gap-x-2 gap-y-1 text-xs text-[#527D82]">
                                <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="4" width="18" height="18" rx="2"/>
                                    <path d="M16 2v4M8 2v4M3 10h18"/>
                                </svg>
                                <span class="font-semibold">Masa kontrak:</span>
                                <span>
                                    {{ $kontrakMulai ? $kontrakMulai->isoFormat('D MMMM Y') : '-' }}
                                    s/d
                                    {{ $kontrakBerakhir ? $kontrakBerakhir->isoFormat('D MMMM Y') : 'Tidak ditentukan' }}
                                </span>
                                @if ($durasiKontrak)
                                    <span class="text-[#8BA5A2]">({{ $durasiKontrak }})</span>
                                @endif
                            </p>
                        </div>

                        <div class="grid grid-cols-2 gap-5 md:text-right">

                            <div>
                                <p class="text-xs text-[#A0B3B0]">
                                    GTO
                                </p>

                                <p class="mt-1 font-semibold text-[#365F65]">
                                    Rp {{ number_format($tenant->gto ?? 0, 0, ',', '.') }}
                                </p>
                            </div>

                            <div>
                                <p class="text-xs text-[#A0B3B0]">
                                    Revenue Sharing
                                </p>

                                <p class="mt-1 font-semibold text-[#365F65]">
                                    {{ number_format($tenant->revenue_sharing ?? 0, 2, ',', '.') }}%
                                </p>
                            </div>

                        </div>

                    </div>

                    {{-- RINGKASAN PER TENANT --}}
                    <div class="mt-4 flex flex-wrap items-center gap-x-5 gap-y-2 border-t border-[#E6EEEC] pt-4 text-sm">

                        <p class="text-[#8BA5A2]">
                            Total tagihan:

                            <span class="font-bold text-[#365F65]">
                                Rp {{ number_format($totalTenant, 0, ',', '.') }}
                            </span>
                        </p>

                        <p class="font-medium text-green-600">
                            {{ $sudahDiberikan }} invoice diberikan
                        </p>

                        <p class="font-medium text-orange-500">
                            {{ $belumDiberikan }} belum diberikan
                        </p>

                    </div>

                </div>

                {{-- TABEL TAGIHAN BULANAN --}}
                <div class="overflow-x-auto">

                    <table class="w-full text-left text-sm">

                        <thead class="bg-[#F3F6F5] text-xs uppercase tracking-wide text-[#8BA5A2]">
                            <tr>
                                <th class="whitespace-nowrap px-5 py-4 font-semibold">
                                    Bulan
                                </th>

                                <th class="whitespace-nowrap px-5 py-4 font-semibold">
                                    Nominal Tagihan
                                </th>

                                <th class="whitespace-nowrap px-5 py-4 font-semibold">
                                    Status Invoice
                                </th>

                                <th class="whitespace-nowrap px-5 py-4 font-semibold">
                                    Tanggal Diberikan
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-[#EDF2F1]">

                            @foreach ($daftarTagihan as $tagihan)

                                <tr class="transition hover:bg-[#F8FAF9]">

                                    <td class="whitespace-nowrap px-5 py-4 font-semibold text-[#365F65]">
                                        {{ $namaBulan[$tagihan->bulan] ?? '-' }}
                                        {{ $tagihan->tahun }}
                                    </td>

                                    <td class="whitespace-nowrap px-5 py-4 font-semibold text-[#365F65]">
                                        Rp {{ number_format($tagihan->jumlah_tagihan, 0, ',', '.') }}
                                    </td>

                                    <td class="whitespace-nowrap px-5 py-4">

                                        <form
                                            action="{{ route('pendapatan.status-invoice', $tagihan->id) }}"
                                            method="POST"
                                        >
                                            @csrf
                                            @method('PATCH')

                                            <input
                                                type="hidden"
                                                name="status_invoice"
                                                value="0"
                                            >

                                            <input
                                                type="hidden"
                                                name="search"
                                                value="{{ $search }}"
                                            >

                                            <input
                                                type="hidden"
                                                name="bulan_awal"
                                                value="{{ $bulanAwal }}"
                                            >

                                            <input
                                                type="hidden"
                                                name="bulan_akhir"
                                                value="{{ $bulanAkhir }}"
                                            >

                                            <label class="inline-flex cursor-pointer items-center gap-2">

                                                <input
                                                    type="checkbox"
                                                    name="status_invoice"
                                                    value="1"
                                                    onchange="this.form.submit()"
                                                    @checked($tagihan->status_invoice)
                                                    class="h-4 w-4 cursor-pointer rounded border-[#B8CECA] text-green-600 focus:ring-green-500"
                                                >

                                                @if ($tagihan->status_invoice)
                                                    <span class="rounded-full bg-green-50 px-3 py-1 text-xs font-semibold text-green-700">
                                                        Sudah Diberikan
                                                    </span>
                                                @else
                                                    <span class="rounded-full bg-orange-50 px-3 py-1 text-xs font-semibold text-orange-700">
                                                        Belum Diberikan
                                                    </span>
                                                @endif

                                            </label>

                                        </form>

                                    </td>

                                    <td class="whitespace-nowrap px-5 py-4 text-[#8BA5A2]">

                                        @if ($tagihan->tanggal_diberikan)
                                            {{ $tagihan->tanggal_diberikan->format('d/m/Y') }}
                                        @else
                                            -
                                        @endif

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            </div>

        @empty

            {{-- EMPTY STATE --}}
            <div class="rounded-2xl border border-dashed border-[#C8D8D5] bg-white px-6 py-16 text-center">

                <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-[#F3F6F5]">

                    <svg class="h-7 w-7 text-[#8BA5A2]" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="2"
                            d="M9 14l2 2 4-4m6-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>

                </div>

                <h3 class="text-lg font-bold text-[#365F65]">
                    Belum Ada Data Pendapatan
                </h3>

                <p class="mx-auto mt-2 max-w-md text-sm text-[#8BA5A2]">
                    Tidak ditemukan tagihan untuk periode
                    {{ $namaBulan[$bulanAwal] }} -
                    {{ $namaBulan[$bulanAkhir] }} {{ $tahun }}.
                    Pastikan aktivasi sudah disetujui pusat, kontrak tenant mencakup periode tersebut, dan data GTO
                    serta Revenue Sharing sudah diisi.
                </p>

            </div>

        @endforelse

        {{-- INFORMASI PERHITUNGAN --}}
        <div class="mt-6 rounded-2xl border border-[#DCE6E4] bg-[#EAF1F0] p-5">

            <h4 class="text-sm font-bold text-[#365F65]">
                Informasi Perhitungan
            </h4>

            <p class="mt-2 text-sm leading-relaxed text-[#527D82]">
                Tagihan bulanan dihitung menggunakan rumus:
                <strong>GTO × Revenue Sharing (%)</strong>.
                Tagihan hanya dibuat untuk kerja sama yang aktivasinya sudah disetujui oleh pusat, dan hanya pada bulan yang termasuk dalam periode kontrak tenant.
                Filter tahun dan rentang bulan digunakan untuk menampilkan total tagihan
                pada periode yang dipilih.
                Status invoice menunjukkan apakah invoice sudah diberikan kepada tenant,
                bukan status pembayaran.
            </p>

        </div>

    </div>

</div>

@endsection