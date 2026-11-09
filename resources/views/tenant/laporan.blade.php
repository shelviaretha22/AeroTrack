
@extends('layouts.app')

@section('page-title', 'Laporan Tenant')

@section('content')

@php
    $maksJenisUsaha = max(1, (int) $distribusiJenisUsaha->max('total'));
    $maksTerminal = max(1, (int) $distribusiTerminal->max('total'));

    $formatTanggal = function ($tanggal) {
        return $tanggal
            ? \Carbon\Carbon::parse($tanggal)->format('d/m/Y')
            : '-';
    };
@endphp

<style>
    #tenant-report-page {
        width: 100%;
        max-width: 100%;
        min-width: 0;
        box-sizing: border-box;
    }

    #tenant-report-page *,
    #tenant-report-page *::before,
    #tenant-report-page *::after {
        box-sizing: border-box;
    }

    #tenant-report-page .report-summary-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px;
    }

    #tenant-report-page .report-chart-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 20px;
    }

    #tenant-report-page .report-card {
        min-width: 0;
        border: 1px solid #DCE6E4;
        border-radius: 16px;
        background: white;
        padding: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }

    #tenant-report-page .report-table-wrap {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    #tenant-report-page .report-table {
        width: 100%;
        min-width: 1150px;
        border-collapse: collapse;
    }

    #tenant-report-page .report-table th {
        padding: 13px 14px;
        background: #F1F6F4;
        color: #527D82;
        font-size: 11px;
        font-weight: 700;
        text-align: left;
        white-space: nowrap;
    }

    #tenant-report-page .report-table td {
        padding: 13px 14px;
        border-bottom: 1px solid #EDF2F0;
        color: #527D82;
        font-size: 12px;
        vertical-align: middle;
    }

    #tenant-report-page .report-table tbody tr:hover {
        background: #F8FAF9;
    }

    #tenant-report-page .report-filter-input {
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

    #tenant-report-page .report-filter-input:focus {
        border-color: #527D82;
        box-shadow: 0 0 0 2px rgba(82,125,130,.10);
    }

    #tenant-report-page .report-filter-label {
        display: block;
        margin-bottom: 7px;
        color: #527D82;
        font-size: 11px;
        font-weight: 700;
    }

    #tenant-report-page .report-bar-track {
        height: 9px;
        overflow: hidden;
        border-radius: 999px;
        background: #EDF2F0;
    }

    #tenant-report-page .report-bar {
        height: 100%;
        border-radius: 999px;
        background: #527D82;
    }

    #tenant-report-page .report-status {
        display: inline-flex;
        align-items: center;
        border-radius: 999px;
        padding: 5px 9px;
        font-size: 10px;
        font-weight: 700;
        white-space: nowrap;
    }

    @media (min-width: 1280px) {
        #tenant-report-page .report-summary-grid {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }
    }

    @media (max-width: 900px) {
        #tenant-report-page .report-chart-grid {
            grid-template-columns: minmax(0, 1fr);
        }
    }

    @media (max-width: 640px) {
        #tenant-report-page .report-summary-grid {
            grid-template-columns: minmax(0, 1fr);
        }
    }
</style>

<div id="tenant-report-page">

    {{-- HEADER --}}
    <div class="mb-6">

        <a href="{{ route('tenant.index') }}"
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

            Kembali ke Tenant

        </a>

        <h2 class="text-2xl font-bold text-[#365F65]">
            Laporan Tenant
        </h2>

        <p class="mt-1 text-sm text-[#8BA5A2]">
            Ringkasan dan analisis portofolio Tenant berdasarkan data kerja sama dan lokasi.
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
                    Tentukan kriteria data yang ingin ditampilkan.
                </p>
            </div>

        </div>

        <form action="{{ route('tenant.laporan') }}"
              method="GET">

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">

                {{-- BASIS PERIODE --}}
                <div>
                    <label for="basis_periode" class="report-filter-label">
                        Basis Periode
                    </label>

                    <select name="basis_periode"
                            id="basis_periode"
                            class="report-filter-input">

                        <option value="pengajuan"
                            {{ $filters['basis_periode'] === 'pengajuan' ? 'selected' : '' }}>
                            Tanggal Pengajuan Kerja Sama
                        </option>

                        <option value="terbentuk"
                            {{ $filters['basis_periode'] === 'terbentuk' ? 'selected' : '' }}>
                            Tanggal Tenant Terbentuk
                        </option>

                    </select>
                </div>

                {{-- TANGGAL MULAI --}}
                <div>
                    <label for="tanggal_mulai" class="report-filter-label">
                        Tanggal Mulai
                    </label>

                    <input type="date"
                           name="tanggal_mulai"
                           id="tanggal_mulai"
                           value="{{ $filters['tanggal_mulai'] }}"
                           class="report-filter-input">
                </div>

                {{-- TANGGAL SELESAI --}}
                <div>
                    <label for="tanggal_selesai" class="report-filter-label">
                        Tanggal Selesai
                    </label>

                    <input type="date"
                           name="tanggal_selesai"
                           id="tanggal_selesai"
                           value="{{ $filters['tanggal_selesai'] }}"
                           class="report-filter-input">
                </div>

                {{-- JENIS USAHA --}}
                <div>
                    <label for="jenis_usaha" class="report-filter-label">
                        Jenis Usaha
                    </label>

                    <select name="jenis_usaha"
                            id="jenis_usaha"
                            class="report-filter-input">

                        <option value="">Semua Jenis Usaha</option>

                        @foreach ($opsiJenisUsaha as $jenis)
                            <option value="{{ $jenis }}"
                                {{ $filters['jenis_usaha'] === $jenis ? 'selected' : '' }}>
                                {{ $jenis }}
                            </option>
                        @endforeach

                    </select>
                </div>

                {{-- TERMINAL --}}
                <div>
                    <label for="terminal" class="report-filter-label">
                        Terminal
                    </label>

                    <select name="terminal"
                            id="terminal"
                            class="report-filter-input">

                        <option value="">Semua Terminal</option>

                        @foreach ($opsiTerminal as $terminal)
                            <option value="{{ $terminal }}"
                                {{ $filters['terminal'] === $terminal ? 'selected' : '' }}>
                                {{ $terminal }}
                            </option>
                        @endforeach

                    </select>
                </div>

                {{-- STATUS AKTIVASI --}}
                <div>
                    <label for="status_aktivasi" class="report-filter-label">
                        Status Aktivasi
                    </label>

                    <select name="status_aktivasi"
                            id="status_aktivasi"
                            class="report-filter-input">

                        <option value="">Semua Status Aktivasi</option>

                        @foreach ($statusAktivasi as $status)
                            <option value="{{ $status }}"
                                {{ $filters['status_aktivasi'] === $status ? 'selected' : '' }}>
                                {{ $status }}
                            </option>
                        @endforeach

                    </select>
                </div>

            </div>

            {{-- TOMBOL FILTER --}}
            <div class="mt-5 flex flex-wrap items-center gap-3">

                <button type="submit"
                        class="inline-flex items-center gap-2 rounded-lg bg-[#365F65] px-5 py-2.5 text-xs font-bold text-white transition hover:bg-[#527D82]">

                    <svg class="h-4 w-4"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke="currentColor"
                         stroke-width="2">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M5 12h14m-6-6l6 6-6 6"/>
                    </svg>

                    Terapkan Filter

                </button>

                <a href="{{ route('tenant.laporan') }}"
                   class="rounded-lg border border-[#DCE6E4] px-5 py-2.5 text-xs font-bold text-[#527D82] transition hover:bg-[#F1F6F4]">
                    Reset Filter
                </a>

                {{-- TOMBOL EKSPOR EXCEL --}}
<a href="{{ route('tenant.laporan.excel', request()->query()) }}"
   class="inline-flex items-center gap-2 rounded-lg border border-[#6F9B85] px-5 py-2.5 text-xs font-bold text-[#4C8060] transition hover:bg-[#EDF5EF]">

    <svg class="h-4 w-4"
         fill="none"
         viewBox="0 0 24 24"
         stroke="currentColor"
         stroke-width="1.8">
        <path stroke-linecap="round"
              stroke-linejoin="round"
              d="M8 3H5a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-3M8 3v5h5M8 3l9 9M9 14h6m-6 4h6"/>
    </svg>

    Ekspor Excel
    </a>

    {{-- TOMBOL EKSPOR PDF --}}
    <a href="{{ route('tenant.laporan.pdf', request()->query()) }}"
    target="_blank"
    class="inline-flex items-center gap-2 rounded-lg border border-[#D6A14D] px-5 py-2.5 text-xs font-bold text-[#A8782E] transition hover:bg-[#FFF9EF]">

        <svg class="h-4 w-4"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
            stroke-width="1.8">
            <path stroke-linecap="round"
                stroke-linejoin="round"
                d="M12 3v12m0 0l-4-4m4 4l4-4M4 17v3h16v-3"/>
        </svg>

        Ekspor PDF

    </a>

            </div>

        </form>

    </section>

    {{-- RINGKASAN --}}
    <section class="report-summary-grid mb-6">

        {{-- TOTAL TENANT --}}
        <div class="report-card border-t-2 border-t-[#527D82]">

            <div class="flex items-center justify-between">

                <p class="text-xs font-semibold text-[#8BA5A2]">
                    Total Tenant
                </p>

                <div class="rounded-lg bg-[#EDF2F0] p-2 text-[#527D82]">
                    <svg class="h-5 w-5"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke="currentColor"
                         stroke-width="1.8">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M3 21h18M5 21V7l8-4v18m6 0V11l-6-4M9 9v.01M9 12v.01M9 15v.01M9 18v.01M15 13v.01M15 16v.01"/>
                    </svg>
                </div>

            </div>

            <h3 class="mt-3 text-3xl font-bold text-[#365F65]">
                {{ number_format($totalTenant, 0, ',', '.') }}
            </h3>

            <p class="mt-1 text-xs text-[#8BA5A2]">
                Tenant sesuai filter
            </p>

        </div>

        {{-- TENANT AKTIF --}}
        <div class="report-card border-t-2 border-t-[#6F9B85]">

            <div class="flex items-center justify-between">

                <p class="text-xs font-semibold text-[#8BA5A2]">
                    Tenant Aktif
                </p>

                <div class="rounded-lg bg-[#EDF5EF] p-2 text-[#6F9B85]">
                    <svg class="h-5 w-5"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke="currentColor"
                         stroke-width="1.8">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M5 13l4 4L19 7"/>
                    </svg>
                </div>

            </div>

            <h3 class="mt-3 text-3xl font-bold text-[#365F65]">
                {{ number_format($totalTenantAktif, 0, ',', '.') }}
            </h3>

            <p class="mt-1 text-xs text-[#8BA5A2]">
                Aktivasi sistem disetujui
            </p>

        </div>

        {{-- JENIS USAHA --}}
        <div class="report-card border-t-2 border-t-[#D6A14D]">

            <p class="mb-3 text-xs font-semibold text-[#8BA5A2]">
                Tenant per Jenis Usaha
            </p>

            @forelse ($distribusiJenisUsaha as $item)

                <div class="mb-2 flex items-center justify-between gap-2">

                    <span class="min-w-0 truncate text-xs text-[#527D82]">
                        {{ $item['label'] }}
                    </span>

                    <span class="shrink-0 text-xs font-bold text-[#365F65]">
                        {{ number_format($item['total'], 0, ',', '.') }}
                    </span>

                </div>

            @empty

                <p class="text-xs text-[#9AAEAB]">
                    Belum ada data jenis usaha.
                </p>

            @endforelse

        </div>

        {{-- TERMINAL --}}
        <div class="report-card border-t-2 border-t-[#7895A0]">

            <p class="mb-3 text-xs font-semibold text-[#8BA5A2]">
                Tenant per Terminal
            </p>

            @forelse ($distribusiTerminal as $item)

                <div class="mb-2 flex items-center justify-between gap-2">

                    <span class="min-w-0 truncate text-xs text-[#527D82]">
                        {{ $item['label'] }}
                    </span>

                    <span class="shrink-0 text-xs font-bold text-[#365F65]">
                        {{ number_format($item['total'], 0, ',', '.') }}
                    </span>

                </div>

            @empty

                <p class="text-xs text-[#9AAEAB]">
                    Belum ada data terminal.
                </p>

            @endforelse

        </div>

    </section>

    {{-- GRAFIK DISTRIBUSI --}}
    <section class="report-chart-grid mb-6">

        {{-- DISTRIBUSI JENIS USAHA --}}
        <div class="report-card">

            <div class="mb-5">

                <h3 class="text-sm font-bold text-[#365F65]">
                    Distribusi Tenant per Jenis Usaha
                </h3>

                <p class="mt-1 text-xs text-[#8BA5A2]">
                    Jumlah tenant berdasarkan kategori jenis usaha.
                </p>

            </div>

            @forelse ($distribusiJenisUsaha as $item)

                <div class="mb-5">

                    <div class="mb-2 flex items-center justify-between gap-3">

                        <span class="text-xs font-medium text-[#527D82]">
                            {{ $item['label'] }}
                        </span>

                        <span class="text-xs font-bold text-[#365F65]">
                            {{ number_format($item['total'], 0, ',', '.') }} Tenant
                        </span>

                    </div>

                    <div class="report-bar-track">

                        <div class="report-bar"
                             style="width: {{ ($item['total'] / $maksJenisUsaha) * 100 }}%">
                        </div>

                    </div>

                </div>

            @empty

                <div class="rounded-xl border border-dashed border-[#DCE6E4] p-8 text-center">
                    <p class="text-xs text-[#8BA5A2]">
                        Belum ada data untuk ditampilkan.
                    </p>
                </div>

            @endforelse

        </div>

        {{-- DISTRIBUSI TERMINAL --}}
        <div class="report-card">

            <div class="mb-5">

                <h3 class="text-sm font-bold text-[#365F65]">
                    Distribusi Tenant per Terminal
                </h3>

                <p class="mt-1 text-xs text-[#8BA5A2]">
                    Jumlah tenant berdasarkan terminal lokasi.
                </p>

            </div>

            @forelse ($distribusiTerminal as $item)

                <div class="mb-5">

                    <div class="mb-2 flex items-center justify-between gap-3">

                        <span class="text-xs font-medium text-[#527D82]">
                            {{ $item['label'] }}
                        </span>

                        <span class="text-xs font-bold text-[#365F65]">
                            {{ number_format($item['total'], 0, ',', '.') }} Tenant
                        </span>

                    </div>

                    <div class="report-bar-track">

                        <div class="report-bar"
                             style="width: {{ ($item['total'] / $maksTerminal) * 100 }}%">
                        </div>

                    </div>

                </div>

            @empty

                <div class="rounded-xl border border-dashed border-[#DCE6E4] p-8 text-center">
                    <p class="text-xs text-[#8BA5A2]">
                        Belum ada data untuk ditampilkan.
                    </p>
                </div>

            @endforelse

        </div>

    </section>

    {{-- TABEL DATA TENANT --}}
    <section class="report-card">

        <div class="mb-5 flex flex-wrap items-center justify-between gap-3">

            <div>

                <h3 class="text-sm font-bold text-[#365F65]">
                    Data Tenant
                </h3>

                <p class="mt-1 text-xs text-[#8BA5A2]">
                    Daftar seluruh tenant yang sesuai dengan filter laporan.
                </p>

            </div>

            <div class="rounded-lg bg-[#EDF2F0] px-3 py-2">

                <span class="text-xs font-bold text-[#365F65]">
                    {{ number_format($tenants->count(), 0, ',', '.') }} Data
                </span>

            </div>

        </div>

        <div class="report-table-wrap">

            <table class="report-table">

                <thead>

                    <tr>
                        <th>No.</th>
                        <th>Nama Tenant</th>
                        <th>Mitra Usaha</th>
                        <th>Brand</th>
                        <th>Jenis Usaha</th>
                        <th>Terminal</th>
                        <th>Lokasi</th>
                        <th>Tanggal Pengajuan</th>
                        <th>Status Kerja Sama</th>
                        <th>Status Aktivasi</th>
                    </tr>

                </thead>

                <tbody>

                    @forelse ($tenants as $index => $tenant)

                        @php
                            $kerjaSama = $tenant->kerjaSama;
                            $lokasi = $kerjaSama?->lokasi;
                            $statusAktivasiTenant = $tenant->status_aktivasi_laporan;
                        @endphp

                        <tr>

                            <td>
                                {{ $index + 1 }}
                            </td>

                            <td>
                                <span class="font-semibold text-[#365F65]">
                                    {{ $tenant->nama_tenant }}
                                </span>
                            </td>

                            <td>
                                {{ $tenant->mitra_usaha ?: '-' }}
                            </td>

                            <td>
                                {{ $kerjaSama?->brand ?: '-' }}
                            </td>

                            <td>
                                {{ $tenant->jenis_usaha ?: '-' }}
                            </td>

                            <td>
                                {{ $lokasi?->terminal ?: '-' }}
                            </td>

                            <td>
                                {{ $lokasi?->lokasi ?: '-' }}
                            </td>

                            <td class="whitespace-nowrap">
                                {{ $formatTanggal($kerjaSama?->tanggal_pengajuan) }}
                            </td>

                            <td>
                                {{ $kerjaSama?->status ?: '-' }}
                            </td>

                            <td>

                                @php
                                    $warnaStatus = match ($statusAktivasiTenant) {
                                        'Disetujui' => 'bg-[#EAF4ED] text-[#4C8060]',
                                        'Ditolak' => 'bg-[#FCEEEE] text-[#B75B5B]',
                                        'Menunggu Keputusan' => 'bg-[#FFF5E5] text-[#A8782E]',
                                        'Belum Diajukan' => 'bg-[#EDF2F0] text-[#527D82]',
                                        default => 'bg-[#F1F2F5] text-[#777F8A]',
                                    };
                                @endphp

                                <span class="report-status {{ $warnaStatus }}">
                                    {{ $statusAktivasiTenant }}
                                </span>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="10" class="py-12 text-center">

                                <div class="flex flex-col items-center">

                                    <svg class="mb-3 h-9 w-9 text-[#A7BCB7]"
                                         fill="none"
                                         viewBox="0 0 24 24"
                                         stroke="currentColor"
                                         stroke-width="1.5">
                                        <path stroke-linecap="round"
                                              stroke-linejoin="round"
                                              d="M3 7h18M5 7v13h14V7M9 11h6m-6 4h6M8 4h8"/>
                                    </svg>

                                    <p class="text-sm font-semibold text-[#527D82]">
                                        Data Tenant Tidak Ditemukan
                                    </p>

                                    <p class="mt-1 text-xs text-[#8BA5A2]">
                                        Coba ubah atau reset filter laporan.
                                    </p>

                                </div>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </section>

</div>

@endsection
