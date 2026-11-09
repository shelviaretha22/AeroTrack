
@extends('layouts.app')

@section('page-title', 'Laporan Kontrak')

@section('content')

@php
    $maksStatus = max(1, (int) max($dataStatus));
    $maksTahunan = max(1, (int) $dataTahunan->max());

    $formatTanggal = function ($tanggal) {
        return $tanggal
            ? \Carbon\Carbon::parse($tanggal)->format('d/m/Y')
            : '-';
    };

    $statusFilter = $filters['status'] ?? 'Semua';
    $perpanjanganFilter = $filters['status_perpanjangan'] ?? '';
@endphp

<style>
    #kontrak-report-page {
        width: 100%;
        max-width: 100%;
        min-width: 0;
        box-sizing: border-box;
    }

    #kontrak-report-page *,
    #kontrak-report-page *::before,
    #kontrak-report-page *::after {
        box-sizing: border-box;
    }

    #kontrak-report-page .report-card {
        min-width: 0;
        padding: 20px;
        border: 1px solid #DCE6E4;
        border-radius: 15px;
        background: white;
    }

    #kontrak-report-page .report-summary-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px;
    }

    #kontrak-report-page .report-chart-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 20px;
    }

    #kontrak-report-page .report-filter-label {
        display: block;
        margin-bottom: 7px;
        color: #527D82;
        font-size: 11px;
        font-weight: 700;
    }

    #kontrak-report-page .report-filter-input {
        width: 100%;
        min-width: 0;
        border: 1px solid #DCE6E4;
        border-radius: 9px;
        background: white;
        padding: 10px 12px;
        color: #365F65;
        font-size: 12px;
        outline: none;
    }

    #kontrak-report-page .report-filter-input:focus {
        border-color: #527D82;
        box-shadow: 0 0 0 2px rgba(82,125,130,.10);
    }

    #kontrak-report-page .report-table-wrap {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    #kontrak-report-page .report-table {
        width: 100%;
        min-width: 1250px;
        border-collapse: collapse;
    }

    #kontrak-report-page .report-table th {
        padding: 13px 14px;
        background: #F1F6F4;
        color: #527D82;
        font-size: 11px;
        font-weight: 700;
        text-align: left;
        white-space: nowrap;
    }

    #kontrak-report-page .report-table td {
        padding: 13px 14px;
        border-bottom: 1px solid #EDF2F0;
        color: #527D82;
        font-size: 12px;
        vertical-align: middle;
    }

    #kontrak-report-page .report-table tbody tr:hover {
        background: #F8FAF9;
    }

    #kontrak-report-page .report-bar-track {
        height: 9px;
        overflow: hidden;
        border-radius: 999px;
        background: #EDF2F0;
    }

    #kontrak-report-page .report-bar {
        height: 100%;
        border-radius: 999px;
        background: #527D82;
    }

    #kontrak-report-page .report-status {
        display: inline-flex;
        align-items: center;
        border-radius: 999px;
        padding: 5px 9px;
        font-size: 10px;
        font-weight: 700;
        white-space: nowrap;
    }

    @media (min-width: 1280px) {
        #kontrak-report-page .report-summary-grid {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }
    }

    @media (max-width: 900px) {
        #kontrak-report-page .report-chart-grid {
            grid-template-columns: minmax(0, 1fr);
        }
    }

    @media (max-width: 640px) {
        #kontrak-report-page .report-summary-grid {
            grid-template-columns: minmax(0, 1fr);
        }
    }
</style>

<div id="kontrak-report-page">

    {{-- HEADER --}}
    <div class="mb-6">

        <a href="{{ route('kontrak') }}"
           class="mb-4 inline-flex items-center gap-2 text-sm font-medium text-[#527D82] transition hover:text-[#365F65]">

            <svg class="h-4 w-4"
                 fill="none"
                 viewBox="0 0 24 24"
                 stroke="currentColor"
                 stroke-width="2">
                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      d="M15 19l-7-7 7-7"/>
            </svg>

            Kembali ke Kontrak
        </a>

        <h2 class="text-2xl font-bold text-[#365F65]">
            Laporan Kontrak
        </h2>

        <p class="mt-1 text-sm text-[#8BA5A2]">
            Ringkasan dan analisis kontrak tenant berdasarkan periode, status,
            jenis usaha, lokasi, dan keputusan perpanjangan.
        </p>

    </div>


    {{-- FILTER LAPORAN --}}
    <section class="report-card mb-6">

        <div class="mb-5 flex items-center gap-3">

            <div class="rounded-lg bg-[#EDF2F0] p-2 text-[#527D82]">
                <svg class="h-5 w-5"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke="currentColor"
                     stroke-width="1.8">
                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          d="M3 4h18M6 9h12m-9 5h6m-3 5h.01"/>
                </svg>
            </div>

            <div>
                <h3 class="text-sm font-bold text-[#365F65]">
                    Filter Laporan
                </h3>

                <p class="mt-1 text-xs text-[#8BA5A2]">
                    Tentukan kriteria data kontrak yang ingin ditampilkan.
                </p>
            </div>

        </div>

        <form action="{{ route('kontrak.laporan') }}" method="GET">

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">

                {{-- TANGGAL MULAI --}}
                <div>
                    <label for="tanggal_mulai"
                           class="report-filter-label">
                        Tanggal Pengajuan Mulai
                    </label>

                    <input type="date"
                           name="tanggal_mulai"
                           id="tanggal_mulai"
                           value="{{ $filters['tanggal_mulai'] ?? '' }}"
                           class="report-filter-input">
                </div>

                {{-- TANGGAL SELESAI --}}
                <div>
                    <label for="tanggal_selesai"
                           class="report-filter-label">
                        Tanggal Pengajuan Selesai
                    </label>

                    <input type="date"
                           name="tanggal_selesai"
                           id="tanggal_selesai"
                           value="{{ $filters['tanggal_selesai'] ?? '' }}"
                           class="report-filter-input">
                </div>

                {{-- STATUS KONTRAK --}}
                <div>
                    <label for="status"
                           class="report-filter-label">
                        Status Kontrak
                    </label>

                    <select name="status"
                            id="status"
                            class="report-filter-input">

                        <option value="Semua"
                            {{ $statusFilter === 'Semua' ? 'selected' : '' }}>
                            Semua Status
                        </option>

                        <option value="Draft"
                            {{ $statusFilter === 'Draft' ? 'selected' : '' }}>
                            Draft
                        </option>

                        <option value="Aktif"
                            {{ $statusFilter === 'Aktif' ? 'selected' : '' }}>
                            Aktif
                        </option>

                        <option value="Nonaktif"
                            {{ $statusFilter === 'Nonaktif' ? 'selected' : '' }}>
                            Nonaktif
                        </option>

                    </select>
                </div>

                {{-- JENIS USAHA --}}
                <div>
                    <label for="jenis_usaha"
                           class="report-filter-label">
                        Jenis Usaha
                    </label>

                    <select name="jenis_usaha"
                            id="jenis_usaha"
                            class="report-filter-input">

                        <option value="">
                            Semua Jenis Usaha
                        </option>

                        @foreach ($opsiJenisUsaha as $jenis)
                            <option value="{{ $jenis }}"
                                {{ ($filters['jenis_usaha'] ?? '') === $jenis ? 'selected' : '' }}>
                                {{ $jenis }}
                            </option>
                        @endforeach

                    </select>
                </div>

                {{-- TERMINAL --}}
                <div>
                    <label for="terminal"
                           class="report-filter-label">
                        Terminal
                    </label>

                    <select name="terminal"
                            id="terminal"
                            class="report-filter-input">

                        <option value="">
                            Semua Terminal
                        </option>

                        @foreach ($opsiTerminal as $terminal)
                            <option value="{{ $terminal }}"
                                {{ ($filters['terminal'] ?? '') === $terminal ? 'selected' : '' }}>
                                {{ $terminal }}
                            </option>
                        @endforeach

                    </select>
                </div>

                {{-- STATUS PERPANJANGAN --}}
                <div>
                    <label for="status_perpanjangan"
                           class="report-filter-label">
                        Status Perpanjangan
                    </label>

                    <select name="status_perpanjangan"
                            id="status_perpanjangan"
                            class="report-filter-input">

                        <option value="">
                            Semua Status Perpanjangan
                        </option>

                        <option value="Belum Dikonfirmasi"
                            {{ $perpanjanganFilter === 'Belum Dikonfirmasi' ? 'selected' : '' }}>
                            Belum Dikonfirmasi
                        </option>

                        <option value="Ingin Memperpanjang"
                            {{ $perpanjanganFilter === 'Ingin Memperpanjang' ? 'selected' : '' }}>
                            Ingin Memperpanjang
                        </option>

                        <option value="Tidak Memperpanjang"
                            {{ $perpanjanganFilter === 'Tidak Memperpanjang' ? 'selected' : '' }}>
                            Tidak Memperpanjang
                        </option>

                    </select>
                </div>

            </div>

            {{-- TOMBOL FILTER DAN EKSPOR --}}
            <div class="mt-5 flex flex-wrap items-center gap-3">

                <button type="submit"
                        class="inline-flex items-center gap-2 rounded-lg bg-[#365F65] px-5 py-2.5 text-xs font-bold text-white transition hover:bg-[#527D82]">

                    Terapkan Filter

                </button>

                <a href="{{ route('kontrak.laporan') }}"
                   class="rounded-lg border border-[#DCE6E4] px-5 py-2.5 text-xs font-bold text-[#527D82] transition hover:bg-[#F1F6F4]">
                    Reset Filter
                </a>

                <a href="{{ route('kontrak.laporan.excel', request()->query()) }}"
                   class="inline-flex items-center gap-2 rounded-lg border border-[#6F9B85] px-5 py-2.5 text-xs font-bold text-[#4C8060] transition hover:bg-[#EDF5EF]">

                    Ekspor Excel

                </a>

                <a href="{{ route('kontrak.laporan.pdf', request()->query()) }}"
                   target="_blank"
                   class="inline-flex items-center gap-2 rounded-lg border border-[#D6A14D] px-5 py-2.5 text-xs font-bold text-[#A8782E] transition hover:bg-[#FFF9EF]">

                    Ekspor PDF

                </a>

            </div>

        </form>

    </section>


    {{-- RINGKASAN --}}
    <section class="report-summary-grid mb-6">

        {{-- TOTAL KONTRAK --}}
        <div class="report-card border-t-2 border-t-[#527D82]">

            <p class="text-xs font-semibold text-[#8BA5A2]">
                Total Kontrak
            </p>

            <h3 class="mt-3 text-3xl font-bold text-[#365F65]">
                {{ number_format($totalKontrak, 0, ',', '.') }}
            </h3>

            <p class="mt-1 text-xs text-[#8BA5A2]">
                Kontrak sesuai filter
            </p>

        </div>

        {{-- KONTRAK DRAFT --}}
        <div class="report-card border-t-2 border-t-[#D6A14D]">

            <p class="text-xs font-semibold text-[#8BA5A2]">
                Kontrak Draft
            </p>

            <h3 class="mt-3 text-3xl font-bold text-[#365F65]">
                {{ number_format($totalDraft, 0, ',', '.') }}
            </h3>

            <p class="mt-1 text-xs text-[#8BA5A2]">
                Menunggu dokumen final
            </p>

        </div>

        {{-- KONTRAK AKTIF --}}
        <div class="report-card border-t-2 border-t-[#6F9B85]">

            <p class="text-xs font-semibold text-[#8BA5A2]">
                Kontrak Aktif
            </p>

            <h3 class="mt-3 text-3xl font-bold text-[#365F65]">
                {{ number_format($totalAktif, 0, ',', '.') }}
            </h3>

            <p class="mt-1 text-xs text-[#8BA5A2]">
                Dokumen final tersedia dan masa kerja sama berlaku
            </p>

        </div>

        {{-- KONTRAK NONAKTIF --}}
        <div class="report-card border-t-2 border-t-[#C77C78]">

            <p class="text-xs font-semibold text-[#8BA5A2]">
                Kontrak Nonaktif
            </p>

            <h3 class="mt-3 text-3xl font-bold text-[#365F65]">
                {{ number_format($totalNonaktif, 0, ',', '.') }}
            </h3>

            <p class="mt-1 text-xs text-[#8BA5A2]">
                Masa kerja sama telah berakhir
            </p>

        </div>

    </section>


    {{-- GRAFIK --}}
    <section class="report-chart-grid mb-6">

        {{-- DISTRIBUSI STATUS --}}
        <div class="report-card">

            <div class="mb-5">
                <h3 class="text-sm font-bold text-[#365F65]">
                    Distribusi Status Kontrak
                </h3>

                <p class="mt-1 text-xs text-[#8BA5A2]">
                    Perbandingan jumlah kontrak berdasarkan status.
                </p>
            </div>

            @foreach ($dataStatus as $label => $jumlah)

                <div class="mb-5">

                    <div class="mb-2 flex items-center justify-between gap-3">

                        <span class="text-xs font-medium text-[#527D82]">
                            {{ $label }}
                        </span>

                        <span class="text-xs font-bold text-[#365F65]">
                            {{ number_format($jumlah, 0, ',', '.') }}
                        </span>

                    </div>

                    <div class="report-bar-track">
                        <div class="report-bar"
                             style="width: {{ ($jumlah / $maksStatus) * 100 }}%;">
                        </div>
                    </div>

                </div>

            @endforeach

        </div>


        {{-- JUMLAH PER TAHUN --}}
        <div class="report-card">

            <div class="mb-5">
                <h3 class="text-sm font-bold text-[#365F65]">
                    Jumlah Kontrak per Tahun
                </h3>

                <p class="mt-1 text-xs text-[#8BA5A2]">
                    Distribusi kontrak berdasarkan tahun pengajuan.
                </p>
            </div>

            @forelse ($dataTahunan as $tahun => $jumlah)

                <div class="mb-5">

                    <div class="mb-2 flex items-center justify-between gap-3">

                        <span class="text-xs font-medium text-[#527D82]">
                            {{ $tahun }}
                        </span>

                        <span class="text-xs font-bold text-[#365F65]">
                            {{ number_format($jumlah, 0, ',', '.') }}
                        </span>

                    </div>

                    <div class="report-bar-track">
                        <div class="report-bar"
                             style="width: {{ ($jumlah / $maksTahunan) * 100 }}%;">
                        </div>
                    </div>

                </div>

            @empty

                <p class="text-xs text-[#9AAEAB]">
                    Belum ada data kontrak berdasarkan tahun.
                </p>

            @endforelse

        </div>

    </section>


    {{-- TABEL DATA KONTRAK --}}
    <section class="report-card">

        <div class="mb-5 flex flex-wrap items-center justify-between gap-3">

            <div>
                <h3 class="text-sm font-bold text-[#365F65]">
                    Data Kontrak
                </h3>

                <p class="mt-1 text-xs text-[#8BA5A2]">
                    Daftar kontrak berdasarkan filter yang diterapkan.
                </p>
            </div>

            <span class="rounded-full bg-[#EDF2F0] px-3 py-1.5 text-xs font-bold text-[#527D82]">
                {{ number_format($kontraks->count(), 0, ',', '.') }} Data
            </span>

        </div>

        <div class="report-table-wrap">

            <table class="report-table">

                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Nomor Kontrak</th>
                        <th>Nomor Kerja Sama</th>
                        <th>Mitra Usaha</th>
                        <th>Brand</th>
                        <th>Nama Tenant</th>
                        <th>Jenis Usaha</th>
                        <th>Terminal</th>
                        <th>Tanggal Pengajuan</th>
                        <th>Tanggal Berakhir</th>
                        <th>Status Kontrak</th>
                        <th>Status Perpanjangan</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse ($kontraks as $index => $kontrak)

                        @php
                            $kerjaSama = $kontrak->kerjaSama;
                            $tenant = $kontrak->tenant;
                            $lokasi = $kontrak->lokasi;

                            $statusWarna = match ($kontrak->status_laporan) {
                                'Aktif' => 'background:#EDF5EF;color:#4C8060;',
                                'Nonaktif' => 'background:#FCEFED;color:#B65F5B;',
                                default => 'background:#FFF6E8;color:#A8782E;',
                            };
                        @endphp

                        <tr>

                            <td>
                                {{ $index + 1 }}
                            </td>

                            <td class="font-semibold text-[#365F65]">
                                {{ $kontrak->nomor_kontrak ?: '-' }}
                            </td>

                            <td>
                                {{ $kerjaSama?->nomor_kerja_sama ?: '-' }}
                            </td>

                            <td>
                                {{ $kontrak->nama_mitra_snapshot
                                    ?: $kerjaSama?->mitra_usaha
                                    ?: '-' }}
                            </td>

                            <td>
                                {{ $kerjaSama?->brand ?: '-' }}
                            </td>

                            <td>
                                {{ $tenant?->nama_tenant ?: '-' }}
                            </td>

                            <td>
                                {{ $kerjaSama?->jenis_usaha ?: '-' }}
                            </td>

                            <td>
                                {{ $lokasi?->terminal ?: '-' }}
                            </td>

                            <td>
                                {{ $formatTanggal($kontrak->tanggal_pengajuan) }}
                            </td>

                            <td>
                                {{ $formatTanggal($kerjaSama?->tanggal_berakhir) }}
                            </td>

                            <td>
                                <span class="report-status"
                                      style="{{ $statusWarna }}">
                                    {{ $kontrak->status_laporan }}
                                </span>
                            </td>

                            <td>
                                {{ $kontrak->status_perpanjangan
                                    ?: 'Belum Dikonfirmasi' }}
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="12"
                                class="py-8 text-center text-[#9AAEAB]">
                                Tidak ada data kontrak yang sesuai dengan filter laporan.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </section>

</div>

@endsection
