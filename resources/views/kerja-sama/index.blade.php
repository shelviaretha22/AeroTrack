@extends('layouts.app')

@section('page-title', 'Kerja Sama')

@section('content')

<div class="py-2">
    <div class="max-w-7xl mx-auto">

        {{-- HEADER --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">

            <div>
                <h1 class="text-2xl font-bold text-[#365F65]">
                    Kerja Sama
                </h1>

                <p class="mt-1 text-sm text-[#8BA5A2]">
                    Kelola data kerja sama dengan mitra usaha.
                </p>
            </div>

            <a
                href="{{ route('kerja-sama.create') }}"
                class="inline-flex items-center justify-center gap-2
                       px-5 py-3 rounded-xl
                       bg-[#365F65] text-white
                       text-sm font-semibold
                       hover:bg-[#2D5055]
                       shadow-sm transition"
            >
                <svg
                    class="w-5 h-5"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 4v16m8-8H4"
                    />
                </svg>

                Tambah Kerja Sama
            </a>

        </div>


        {{-- SUCCESS MESSAGE --}}
        @if (session('success'))
            <div
                class="mb-6 flex items-center gap-3
                       rounded-xl border border-[#C9DDD9]
                       bg-[#EDF6F4]
                       px-4 py-3"
            >
                <div class="flex-shrink-0">
                    <svg
                        class="w-5 h-5 text-[#365F65]"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M5 13l4 4L19 7"
                        />
                    </svg>
                </div>

                <p class="text-sm font-medium text-[#365F65]">
                    {{ session('success') }}
                </p>
            </div>
        @endif


        {{-- SUMMARY CARDS --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

            {{-- TOTAL --}}
            <div class="bg-white rounded-2xl border border-[#DCE6E4] p-5 shadow-sm">

                <div class="flex items-center justify-between">

                    <div>
                        <p class="text-sm text-[#8BA5A2]">
                            Total Kerja Sama
                        </p>

                        <p class="mt-1 text-2xl font-bold text-[#365F65]">
                            {{ $kerjaSamas->count() }}
                        </p>
                    </div>

                    <div class="w-11 h-11 rounded-xl bg-[#E8F0EF] flex items-center justify-center">

                        <svg
                            class="w-5 h-5 text-[#365F65]"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h6l4 4v12a2 2 0 01-2 2z"
                            />
                        </svg>

                    </div>

                </div>

            </div>


            {{-- DRAFT --}}
            <div class="bg-white rounded-2xl border border-[#DCE6E4] p-5 shadow-sm">

                <div class="flex items-center justify-between">

                    <div>
                        <p class="text-sm text-[#8BA5A2]">
                            Draft
                        </p>

                        <p class="mt-1 text-2xl font-bold text-[#365F65]">
                            {{ $kerjaSamas->where('status', 'Draft')->count() }}
                        </p>
                    </div>

                    <div class="w-11 h-11 rounded-xl bg-[#F3F6F5] flex items-center justify-center">
                        <span class="w-3 h-3 rounded-full bg-[#8BA5A2]"></span>
                    </div>

                </div>

            </div>


            {{-- PROSES --}}
            <div class="bg-white rounded-2xl border border-[#DCE6E4] p-5 shadow-sm">

                <div class="flex items-center justify-between">

                    <div>
                        <p class="text-sm text-[#8BA5A2]">
                            Dalam Proses
                        </p>

                        <p class="mt-1 text-2xl font-bold text-[#365F65]">
                            {{ $kerjaSamas->where('status', 'Proses')->count() }}
                        </p>
                    </div>

                    <div class="w-11 h-11 rounded-xl bg-[#FFF8E7] flex items-center justify-center">
                        <span class="w-3 h-3 rounded-full bg-[#D5A93A]"></span>
                    </div>

                </div>

            </div>


            {{-- DISETUJUI --}}
            <div class="bg-white rounded-2xl border border-[#DCE6E4] p-5 shadow-sm">

                <div class="flex items-center justify-between">

                    <div>
                        <p class="text-sm text-[#8BA5A2]">
                            Disetujui
                        </p>

                        <p class="mt-1 text-2xl font-bold text-[#365F65]">
                            {{ $kerjaSamas->where('status', 'Disetujui')->count() }}
                        </p>
                    </div>

                    <div class="w-11 h-11 rounded-xl bg-[#EDF6F4] flex items-center justify-center">

                        <svg
                            class="w-5 h-5 text-[#365F65]"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M5 13l4 4L19 7"
                            />
                        </svg>

                    </div>

                </div>

            </div>

        </div>


        
{{-- TABLE --}}
<div class="bg-white rounded-2xl border border-[#DCE6E4] shadow-sm overflow-hidden">

    <div class="px-6 py-5 border-b border-[#DCE6E4]">
        <h2 class="text-base font-bold text-[#365F65]">
            Daftar Kerja Sama
        </h2>
        <p class="mt-1 text-xs text-[#8BA5A2]">
            Kelola kerja sama, data tenant, lokasi, dan pengajuan berita acara.
        </p>
    </div>

    @if ($kerjaSamas->count() > 0)

        <div class="overflow-x-auto">
            <table class="w-full text-sm">

                <thead class="bg-[#F3F6F5]">
                    <tr class="text-left">
                        <th class="px-5 py-4 font-semibold text-[#365F65]">No.</th>
                        <th class="px-5 py-4 font-semibold text-[#365F65]">Mitra Usaha</th>
                        <th class="px-5 py-4 font-semibold text-[#365F65]">Periode</th>
                        <th class="px-5 py-4 font-semibold text-[#365F65]">Data Tenant</th>
                        <th class="px-5 py-4 font-semibold text-[#365F65]">Lokasi</th>
                        <th class="px-5 py-4 font-semibold text-[#365F65]">Berita Acara</th>
                        <th class="px-5 py-4 font-semibold text-[#365F65]">Status</th>
                        <th class="px-5 py-4 text-right font-semibold text-[#365F65]">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-[#DCE6E4]">

                    @foreach ($kerjaSamas as $index => $kerjaSama)

                        @php
                            $tenant = $kerjaSama->tenant;
                            $lokasi = $kerjaSama->lokasi;

                            $tenantLengkap = $tenant !== null;
                            $lokasiLengkap = $lokasi !== null;

                            $statusTampil = $kerjaSama->status_otomatis
                                ?? $kerjaSama->status
                                ?? 'Draft';

                            $dataSiapBA = $tenantLengkap && $lokasiLengkap;
                        @endphp

                        <tr class="hover:bg-[#F8FAF9] transition align-top">

                            {{-- NOMOR --}}
                            <td class="px-5 py-4">
                                <span class="font-semibold text-[#365F65]">
                                    {{ 'KS-' . str_pad($kerjaSama->id, 3, '0', STR_PAD_LEFT) }}
                                </span>
                            </td>

                            {{-- MITRA USAHA --}}
                            <td class="px-5 py-4 min-w-[220px]">
                                <p class="font-semibold text-[#365F65]">
                                    {{ $kerjaSama->mitra_usaha }}
                                </p>

                                <p class="mt-1 text-xs text-[#527D82]">
                                    Brand: {{ $kerjaSama->brand ?: '-' }}
                                </p>

                                <p class="mt-1 text-xs text-[#8BA5A2]">
                                    {{ $kerjaSama->jenis_usaha }}
                                    ·
                                    {{ $kerjaSama->bentuk_kerja_sama }}
                                </p>

                                <p class="mt-1 text-xs text-[#8BA5A2]">
                                    PIC: {{ $kerjaSama->pic_commercial ?: '-' }}
                                </p>
                            </td>

                            {{-- PERIODE --}}
                            <td class="px-5 py-4 min-w-[150px]">
                                <p class="text-[#365F65]">
                                    {{ $kerjaSama->tanggal_mulai
                                        ? $kerjaSama->tanggal_mulai->format('d M Y')
                                        : 'Belum ditentukan' }}
                                </p>

                                <p class="mt-1 text-xs text-[#8BA5A2]">
                                    s/d
                                    {{ $kerjaSama->tanggal_berakhir
                                        ? $kerjaSama->tanggal_berakhir->format('d M Y')
                                        : '-' }}
                                </p>
                            </td>

                            {{-- DATA TENANT --}}
                            <td class="px-5 py-4 min-w-[140px]">
                                @if ($tenantLengkap)
                                    <p class="font-medium text-[#365F65]">
                                        {{ $tenant->nama_tenant }}
                                    </p>

                                    <a
                                        href="{{ route('tenant.show', $tenant->id) }}"
                                        class="inline-flex mt-2 px-3 py-2 rounded-lg
                                               bg-[#E8F0EF] text-[#365F65]
                                               text-xs font-semibold hover:bg-[#DCEAE7]"
                                    >
                                        Lihat Tenant
                                    </a>
                                @else
                                    <p class="text-xs text-[#8BA5A2] mb-2">
                                        Belum diisi
                                    </p>

                                    <a
                                        href="{{ route('tenant.create', ['kerja_sama_id' => $kerjaSama->id]) }}"
                                        class="inline-flex px-3 py-2 rounded-lg
                                               bg-[#365F65] text-white
                                               text-xs font-semibold hover:bg-[#2D5055]"
                                    >
                                        + Isi Tenant
                                    </a>
                                @endif
                            </td>

                            {{-- LOKASI --}}
                            <td class="px-5 py-4 min-w-[140px]">
                                @if ($lokasiLengkap)
                                    <p class="font-medium text-[#365F65]">
                                        {{ $lokasi->lokasi }}
                                    </p>

                                    <p class="mt-1 text-xs text-[#8BA5A2]">
                                        Terminal {{ $lokasi->terminal }}
                                    </p>

                                    <a
                                        href="{{ route('lokasi.show', $lokasi->id) }}"
                                        class="inline-flex mt-2 px-3 py-2 rounded-lg
                                               bg-[#E8F0EF] text-[#365F65]
                                               text-xs font-semibold hover:bg-[#DCEAE7]"
                                    >
                                        Lihat Lokasi
                                    </a>
                                @else
                                    <p class="text-xs text-[#8BA5A2] mb-2">
                                        Belum diisi
                                    </p>

                                    @if ($tenantLengkap)
                                        <a
                                            href="{{ route('lokasi.create', ['tenant_id' => $tenant->id]) }}"
                                            class="inline-flex px-3 py-2 rounded-lg
                                                   bg-[#365F65] text-white
                                                   text-xs font-semibold hover:bg-[#2D5055]"
                                        >
                                            + Isi Lokasi
                                        </a>
                                    @else
                                        <button
                                            type="button"
                                            disabled
                                            title="Isi data tenant terlebih dahulu"
                                            class="inline-flex px-3 py-2 rounded-lg
                                                   bg-gray-100 text-gray-400
                                                   text-xs font-semibold cursor-not-allowed"
                                        >
                                            + Isi Lokasi
                                        </button>
                                    @endif
                                @endif
                            </td>

                            {{-- BERITA ACARA --}}
                            <td class="px-5 py-4 min-w-[140px]">
                                @if ($dataSiapBA)
                                    {{-- Tombol pengajuan BA akan disambungkan
                                         ke route BA setelah route/controller diperiksa. --}}
                                    <span
                                        class="inline-flex px-3 py-2 rounded-lg
                                               bg-[#E8F0EF] text-[#365F65]
                                               text-xs font-semibold"
                                        title="Data siap untuk pengajuan BA"
                                    >
                                        Siap Pengajuan
                                    </span>
                                @else
                                    <button
                                        type="button"
                                        disabled
                                        title="Lengkapi data kerja sama, tenant, dan lokasi terlebih dahulu"
                                        class="inline-flex px-3 py-2 rounded-lg
                                               bg-gray-100 text-gray-400
                                               text-xs font-semibold cursor-not-allowed"
                                    >
                                        Pengajuan BA
                                    </button>
                                @endif
                            </td>

                            {{-- STATUS --}}
                            <td class="px-5 py-4">
                                @if ($statusTampil === 'Aktivasi')
                                    <span class="inline-flex px-3 py-1.5 rounded-full
                                                 bg-[#EDF6F4] text-[#365F65]
                                                 text-xs font-semibold">
                                        Aktivasi
                                    </span>
                                @elseif ($statusTampil === 'Pengajuan BA')
                                    <span class="inline-flex px-3 py-1.5 rounded-full
                                                 bg-[#E8F0EF] text-[#365F65]
                                                 text-xs font-semibold">
                                        Pengajuan BA
                                    </span>
                                @elseif ($statusTampil === 'Proses')
                                    <span class="inline-flex px-3 py-1.5 rounded-full
                                                 bg-[#FFF8E7] text-[#8A6A17]
                                                 text-xs font-semibold">
                                        Proses
                                    </span>
                                @else
                                    <span class="inline-flex px-3 py-1.5 rounded-full
                                                 bg-[#F3F6F5] text-[#6B8582]
                                                 text-xs font-semibold">
                                        Draft
                                    </span>
                                @endif
                            </td>

                            {{-- AKSI --}}
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-2">

                                    <a
                                        href="{{ route('kerja-sama.show', $kerjaSama->id) }}"
                                        title="Detail"
                                        class="w-9 h-9 rounded-lg bg-[#F3F6F5]
                                               text-[#527D82] flex items-center justify-center
                                               hover:bg-[#DCEAE7]"
                                    >
                                        <svg class="w-4 h-4" fill="none"
                                             stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round"
                                                  stroke-linejoin="round" stroke-width="2"
                                                  d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12s-3.75 6.75-9.75 6.75S2.25 12 2.25 12z"/>
                                            <circle cx="12" cy="12" r="2.5"/>
                                        </svg>
                                    </a>

                                    <a
                                        href="{{ route('kerja-sama.edit', $kerjaSama->id) }}"
                                        title="Edit"
                                        class="w-9 h-9 rounded-lg bg-[#EEF3F2]
                                               text-[#365F65] flex items-center justify-center
                                               hover:bg-[#DCE6E4]"
                                    >
                                        <svg class="w-4 h-4" fill="none"
                                             stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round"
                                                  stroke-linejoin="round" stroke-width="2"
                                                  d="M12 20h9M16.5 3.5a2.12 2.12 0 013 3L8 18l-4 1 1-4L16.5 3.5z"/>
                                        </svg>
                                    </a>

                                    <form
                                        action="{{ route('kerja-sama.destroy', $kerjaSama->id) }}"
                                        method="POST"
                                        onsubmit="return confirm('Yakin ingin menghapus data kerja sama ini?')"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            title="Hapus"
                                            class="w-9 h-9 rounded-lg bg-[#FCEEEE]
                                                   text-[#B54B4B] flex items-center justify-center
                                                   hover:bg-[#F8DCDC]"
                                        >
                                            <svg class="w-4 h-4" fill="none"
                                                 stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round"
                                                      stroke-linejoin="round" stroke-width="2"
                                                      d="M3 6h18M8 6V4a1 1 0 011-1h6a1 1 0 011 1v2M19 6l-1 15H6L5 6M10 11v6M14 11v6"/>
                                            </svg>
                                        </button>
                                    </form>

                                </div>
                            </td>

                        </tr>

                    @endforeach

                </tbody>
            </table>
        </div>

    @else

        <div class="px-6 py-16 text-center">
            <div class="mx-auto w-16 h-16 rounded-2xl bg-[#E8F0EF]
                        flex items-center justify-center">
                <svg class="w-8 h-8 text-[#365F65]" fill="none"
                     stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          stroke-width="1.7"
                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h6l4 4v12a2 2 0 01-2 2z"/>
                </svg>
            </div>

            <h3 class="mt-5 text-base font-bold text-[#365F65]">
                Belum Ada Data Kerja Sama
            </h3>

            <p class="mt-2 text-sm text-[#8BA5A2]">
                Tambahkan data kerja sama untuk mulai mengisi tenant dan lokasi.
            </p>

            <a
                href="{{ route('kerja-sama.create') }}"
                class="inline-flex items-center gap-2 mt-5 px-5 py-3 rounded-xl
                       bg-[#365F65] text-white text-sm font-semibold
                       hover:bg-[#2D5055]"
            >
                + Tambah Kerja Sama
            </a>
        </div>

    @endif
</div>

@endsection