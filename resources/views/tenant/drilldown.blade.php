
@extends('layouts.app')

@section('page-title', 'Detail Analisis Tenant')

@section('content')

    {{-- HEADER --}}
    <div class="mb-6">

        <a
            href="{{ route('tenant.index') }}"
            class="mb-4 inline-flex items-center gap-2 text-sm font-medium text-[#527D82] transition hover:text-[#365F65]"
        >
            <svg
                class="h-4 w-4"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                stroke-width="2"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M15 19l-7-7 7-7"
                />
            </svg>

            Kembali ke Tenant
        </a>

        <h2 class="text-2xl font-bold text-[#365F65]">
            {{ $judul }}
        </h2>

        <p class="mt-1 text-sm text-[#8BA5A2]">
            {{ $deskripsi }}
        </p>

    </div>


    {{-- RINGKASAN JUMLAH DATA --}}
    <div class="mb-6 rounded-2xl border border-[#DCE6E4] border-l-4 border-l-[#527D82] bg-white p-5 shadow-sm">

        <p class="text-xs font-medium text-[#8BA5A2]">
            @if ($type === 'bulan')
                Total Pengajuan
            @else
                Total Tenant
            @endif
        </p>

        <h3 class="mt-2 text-3xl font-bold text-[#365F65]">
            {{ number_format($items->count(), 0, ',', '.') }}
        </h3>

        <p class="mt-1 text-xs text-[#9AAEAB]">
            @if ($type === 'bulan')
                Pengajuan Kerja Sama pada periode terpilih
            @elseif ($type === 'terminal')
                Tenant unik pada terminal terpilih
            @else
                Tenant pada kategori usaha terpilih
            @endif
        </p>

    </div>


    {{-- TABEL DATA --}}
    <div class="overflow-hidden rounded-2xl border border-[#DCE6E4] bg-white shadow-sm">

        <div class="border-b border-[#E8EFED] p-5">

            <h3 class="text-sm font-bold text-[#365F65]">
                Daftar Data
            </h3>

            <p class="mt-1 text-xs text-[#8BA5A2]">
                Informasi data sesuai pilihan grafik analisis.
            </p>

        </div>


        <div class="overflow-x-auto">

            <table class="min-w-full text-left">

                <thead>
                    <tr class="border-b border-[#E8EFED] bg-[#F8FAF9]">

                        <th class="px-5 py-4 text-[10px] font-bold uppercase tracking-wider text-[#8BA5A2]">
                            No
                        </th>

                        @if ($type === 'bulan')

                            <th class="px-5 py-4 text-[10px] font-bold uppercase tracking-wider text-[#8BA5A2]">
                                Mitra Usaha
                            </th>

                            <th class="px-5 py-4 text-[10px] font-bold uppercase tracking-wider text-[#8BA5A2]">
                                Brand
                            </th>

                            <th class="px-5 py-4 text-[10px] font-bold uppercase tracking-wider text-[#8BA5A2]">
                                Tanggal Pengajuan
                            </th>

                            <th class="px-5 py-4 text-center text-[10px] font-bold uppercase tracking-wider text-[#8BA5A2]">
                                Status Data Tenant
                            </th>

                            <th class="px-5 py-4 text-center text-[10px] font-bold uppercase tracking-wider text-[#8BA5A2]">
                                Aksi
                            </th>

                        @else

                            <th class="px-5 py-4 text-[10px] font-bold uppercase tracking-wider text-[#8BA5A2]">
                                Nama Tenant
                            </th>

                            <th class="px-5 py-4 text-[10px] font-bold uppercase tracking-wider text-[#8BA5A2]">
                                Mitra Usaha
                            </th>

                            <th class="px-5 py-4 text-[10px] font-bold uppercase tracking-wider text-[#8BA5A2]">
                                Jenis Usaha
                            </th>

                            <th class="px-5 py-4 text-center text-[10px] font-bold uppercase tracking-wider text-[#8BA5A2]">
                                Aksi
                            </th>

                        @endif

                    </tr>
                </thead>


                <tbody class="divide-y divide-[#EEF3F1]">

                    @forelse ($items as $index => $item)

                        <tr class="transition hover:bg-[#F8FAF9]">

                            <td class="whitespace-nowrap px-5 py-4 text-sm text-[#8BA5A2]">
                                {{ $index + 1 }}
                            </td>


                            @if ($type === 'bulan')

                                @php
                                    $tenant = $tenantByKerjaSama->get($item->id);
                                @endphp

                                <td class="px-5 py-4 text-sm font-semibold text-[#365F65]">
                                    {{ $item->mitra_usaha ?: '-' }}
                                </td>

                                <td class="px-5 py-4 text-sm text-[#527D82]">
                                    {{ $item->brand ?: '-' }}
                                </td>

                                <td class="whitespace-nowrap px-5 py-4 text-sm text-[#527D82]">
                                    {{ $item->tanggal_pengajuan
                                        ? \Carbon\Carbon::parse($item->tanggal_pengajuan)->format('d M Y')
                                        : '-' }}
                                </td>

                                <td class="px-5 py-4 text-center">

                                    @if ($tenant)

                                        <span class="inline-flex rounded-lg bg-[#E8F3EE] px-3 py-1.5 text-xs font-semibold text-[#4D856F]">
                                            Sudah Diinput
                                        </span>

                                    @else

                                        <span class="inline-flex rounded-lg bg-[#F8F1E5] px-3 py-1.5 text-xs font-semibold text-[#B88A43]">
                                            Belum Diinput
                                        </span>

                                    @endif

                                </td>

                                <td class="px-5 py-4 text-center">

                                    @if ($tenant)

                                        <a
                                            href="{{ route('tenant.show', $tenant) }}"
                                            class="inline-flex items-center rounded-lg bg-[#E8F0EE] px-3 py-2 text-xs font-semibold text-[#527D82] transition hover:bg-[#DCE9E5]"
                                        >
                                            Lihat Tenant
                                        </a>

                                    @else

                                        <a
                                            href="{{ route('tenant.create', ['kerja_sama_id' => $item->id]) }}"
                                            class="inline-flex items-center rounded-lg bg-[#365F65] px-3 py-2 text-xs font-semibold text-white transition hover:bg-[#2F5358]"
                                        >
                                            Input Tenant
                                        </a>

                                    @endif

                                </td>

                            @else

                                <td class="px-5 py-4 text-sm font-semibold text-[#365F65]">
                                    {{ $item->nama_tenant }}
                                </td>

                                <td class="px-5 py-4 text-sm text-[#527D82]">
                                    {{ $item->mitra_usaha ?: '-' }}
                                </td>

                                <td class="px-5 py-4">

                                    <span class="inline-flex rounded-lg bg-[#E8F0EE] px-2.5 py-1 text-xs font-semibold text-[#527D82]">
                                        {{ $item->jenis_usaha ?: '-' }}
                                    </span>

                                </td>

                                <td class="px-5 py-4 text-center">

                                    <a
                                        href="{{ route('tenant.show', $item) }}"
                                        class="inline-flex items-center rounded-lg bg-[#E8F0EE] px-3 py-2 text-xs font-semibold text-[#527D82] transition hover:bg-[#DCE9E5]"
                                    >
                                        Lihat Detail
                                    </a>

                                </td>

                            @endif

                        </tr>

                    @empty

                        <tr>
                            <td
                                colspan="{{ $type === 'bulan' ? 6 : 5 }}"
                                class="px-5 py-16 text-center"
                            >

                                <div class="flex flex-col items-center">

                                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-[#E8F0EE] text-[#527D82]">

                                        <svg
                                            class="h-7 w-7"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                            stroke-width="1.7"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
                                            />
                                        </svg>

                                    </div>

                                    <h4 class="mt-4 text-sm font-bold text-[#365F65]">
                                        Tidak ada data
                                    </h4>

                                    <p class="mt-1 text-xs text-[#8BA5A2]">
                                        Belum terdapat data untuk pilihan analisis ini.
                                    </p>

                                </div>

                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        @if ($items->count() > 0)

            <div class="border-t border-[#E8EFED] px-5 py-4">

                <p class="text-xs text-[#8BA5A2]">
                    Menampilkan
                    <span class="font-semibold text-[#527D82]">
                        {{ $items->count() }}
                    </span>
                    data
                </p>

            </div>

        @endif

    </div>

@endsection
