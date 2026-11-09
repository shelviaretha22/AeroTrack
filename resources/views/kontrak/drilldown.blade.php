
@extends('layouts.app')

@section('page-title', 'Detail Kontrak')

@section('content')
<div class="space-y-6">

    {{-- HEADER --}}
    <div>
        <a href="{{ route('kontrak') }}"
           class="inline-flex items-center gap-2 text-sm font-medium text-[#527D82] hover:text-[#365F65]">
            <span>&larr;</span>
            Kembali ke Dashboard Kontrak
        </a>

        <p class="mt-4 text-sm text-[#8BA5A2]">
            Management / Kontrak / Detail
        </p>

        <h2 class="mt-1 text-2xl font-bold text-[#365F65]">
            {{ $judul }}
        </h2>

        <p class="mt-1 text-sm text-[#7A9290]">
            {{ $deskripsi }}
        </p>
    </div>

    {{-- JUMLAH DATA --}}
    <div class="rounded-2xl border border-[#E2EBE9] bg-white p-5">
        <p class="text-sm font-medium text-[#7A9290]">
            Total Data
        </p>

        <h3 class="mt-2 text-3xl font-bold text-[#365F65]">
            {{ $type === 'status' && $value === 'Belum Dibuat'
                ? $kandidatKontrak->count()
                : $kontraks->count() }}
        </h3>
    </div>

    {{-- TABEL KONTRAK --}}
    @if (!($type === 'status' && $value === 'Belum Dibuat'))

        <div class="overflow-hidden rounded-2xl border border-[#E2EBE9] bg-white">

            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-sm">

                    <thead class="bg-[#F1F6F4] text-[#365F65]">
                        <tr>
                            <th class="px-5 py-4 font-semibold">No.</th>
                            <th class="px-5 py-4 font-semibold">Nomor Kontrak</th>
                            <th class="px-5 py-4 font-semibold">Brand / Mitra</th>
                            <th class="px-5 py-4 font-semibold">Tenant</th>
                            <th class="px-5 py-4 font-semibold">Lokasi</th>
                            <th class="px-5 py-4 font-semibold">Tanggal Pengajuan</th>
                            <th class="px-5 py-4 font-semibold">Status</th>
                            <th class="px-5 py-4 font-semibold">Perpanjangan</th>
                            <th class="px-5 py-4 font-semibold">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-[#E2EBE9]">

                        @forelse ($kontraks as $kontrak)
                            @php
                                $status = !empty($kontrak->final_pdf_path)
                                    && \Illuminate\Support\Facades\Storage::disk('local')->exists($kontrak->final_pdf_path)
                                    ? (
                                        $kontrak->kerjaSama?->tanggal_berakhir
                                        && \Carbon\Carbon::parse($kontrak->kerjaSama->tanggal_berakhir)->startOfDay()->lt(now()->startOfDay())
                                            ? 'Nonaktif'
                                            : 'Aktif'
                                    )
                                    : 'Draft';
                            @endphp

                            <tr class="transition hover:bg-[#F8FAF9]">
                                <td class="px-5 py-4 text-[#7A9290]">
                                    {{ $loop->iteration }}
                                </td>

                                <td class="px-5 py-4 font-medium text-[#365F65]">
                                    {{ $kontrak->nomor_kontrak ?: '-' }}
                                </td>

                                <td class="px-5 py-4 text-[#55736F]">
                                    {{ $kontrak->kerjaSama?->brand
                                        ?: $kontrak->kerjaSama?->mitra_usaha
                                        ?: $kontrak->nama_mitra_snapshot
                                        ?: '-' }}
                                </td>

                                <td class="px-5 py-4 text-[#55736F]">
                                    {{ $kontrak->tenant?->nama_tenant ?: '-' }}
                                </td>

                                <td class="px-5 py-4 text-[#55736F]">
                                    {{ $kontrak->lokasi?->lokasi ?: '-' }}
                                </td>

                                <td class="px-5 py-4 text-[#55736F]">
                                    {{ $kontrak->tanggal_pengajuan
                                        ? \Carbon\Carbon::parse($kontrak->tanggal_pengajuan)->format('d-m-Y')
                                        : '-' }}
                                </td>

                                <td class="px-5 py-4">
                                    <span class="rounded-full px-3 py-1 text-xs font-semibold
                                        {{ $status === 'Aktif'
                                            ? 'bg-[#E5F5EF] text-[#087F73]'
                                            : ($status === 'Nonaktif'
                                                ? 'bg-[#FFF4DF] text-[#A47720]'
                                                : 'bg-[#FFF0D9] text-[#A16207]') }}">
                                        {{ $status }}
                                    </span>
                                </td>

                                <td class="px-5 py-4 text-[#55736F]">
                                    {{ $kontrak->status_perpanjangan ?: 'Belum Dikonfirmasi' }}
                                </td>

                                <td class="px-5 py-4">
                                    <a href="{{ route('kontrak.show', $kontrak->id) }}"
                                       class="font-semibold text-[#527D82] hover:text-[#365F65]">
                                        Detail
                                    </a>
                                </td>
                            </tr>

                        @empty
                            <tr>
                                <td colspan="9" class="px-5 py-12 text-center">
                                    <p class="font-semibold text-[#365F65]">
                                        Data tidak ditemukan
                                    </p>
                                    <p class="mt-1 text-sm text-[#8BA5A2]">
                                        Belum ada kontrak pada kategori ini.
                                    </p>
                                </td>
                            </tr>
                        @endforelse

                    </tbody>
                </table>
            </div>
        </div>

    @else

        {{-- TABEL KANDIDAT KONTRAK BELUM DIBUAT --}}
        <div class="overflow-hidden rounded-2xl border border-[#E2EBE9] bg-white">

            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-sm">

                    <thead class="bg-[#F1F6F4] text-[#365F65]">
                        <tr>
                            <th class="px-5 py-4 font-semibold">No.</th>
                            <th class="px-5 py-4 font-semibold">Nomor Kerja Sama</th>
                            <th class="px-5 py-4 font-semibold">Brand / Mitra</th>
                            <th class="px-5 py-4 font-semibold">Tenant</th>
                            <th class="px-5 py-4 font-semibold">Lokasi</th>
                            <th class="px-5 py-4 font-semibold">Tanggal Pengajuan</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-[#E2EBE9]">

                        @forelse ($kandidatKontrak as $kerjaSama)
                            <tr class="transition hover:bg-[#F8FAF9]">

                                <td class="px-5 py-4 text-[#7A9290]">
                                    {{ $loop->iteration }}
                                </td>

                                <td class="px-5 py-4 font-medium text-[#365F65]">
                                    {{ $kerjaSama->nomor_kerja_sama ?: '-' }}
                                </td>

                                <td class="px-5 py-4 text-[#55736F]">
                                    {{ $kerjaSama->brand ?: $kerjaSama->mitra_usaha ?: '-' }}
                                </td>

                                <td class="px-5 py-4 text-[#55736F]">
                                    {{ $kerjaSama->tenant?->nama_tenant ?: '-' }}
                                </td>

                                <td class="px-5 py-4 text-[#55736F]">
                                    {{ $kerjaSama->lokasi?->lokasi ?: '-' }}
                                </td>

                                <td class="px-5 py-4 text-[#55736F]">
                                    {{ $kerjaSama->tanggal_pengajuan
                                        ? \Carbon\Carbon::parse($kerjaSama->tanggal_pengajuan)->format('d-m-Y')
                                        : '-' }}
                                </td>

                            </tr>

                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-12 text-center">
                                    <p class="font-semibold text-[#365F65]">
                                        Tidak ada kerja sama yang menunggu kontrak
                                    </p>
                                    <p class="mt-1 text-sm text-[#8BA5A2]">
                                        Semua kandidat kontrak sudah memiliki kontrak atau belum memenuhi persyaratan.
                                    </p>
                                </td>
                            </tr>
                        @endforelse

                    </tbody>
                </table>
            </div>
        </div>

    @endif

</div>
@endsection
