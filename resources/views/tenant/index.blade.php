@extends('layouts.app')

@section('page-title', 'Tenant')

@section('content')

    {{-- =========================================================
         HEADER HALAMAN
    ========================================================== --}}
    <div class="mb-7">

        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">

            <div>

                <h2 class="text-2xl font-bold text-[#365F65]">
                    Tenant
                </h2>

                <p class="mt-1 text-sm text-[#8BA5A2]">
                    Kelola informasi lengkap tenant yang bekerja sama dengan Commercial.
                </p>

            </div>

        </div>

    </div>



    {{-- =========================================================
         CARD TABEL
    ========================================================== --}}
    <div
        class="overflow-hidden
               rounded-2xl
               border border-[#DCE6E4]
               bg-white
               shadow-sm"
    >

        {{-- =====================================================
             TOOLBAR
        ====================================================== --}}
        <div
            class="flex flex-col gap-4
                   border-b border-[#E8EFED]
                   p-5
                   sm:flex-row
                   sm:items-center
                   sm:justify-between"
        >

            {{-- TITLE --}}
            <div>

                <h3 class="text-sm font-bold text-[#365F65]">
                    Data Tenant
                </h3>

                <p class="mt-1 text-xs text-[#8BA5A2]">
                    Daftar kerja sama yang dapat dilengkapi dengan data tenant.
                </p>

            </div>


            {{-- =================================================
                 SEARCH + SORTING
            ================================================== --}}
            <div class="w-full sm:w-auto">

                <form
                    method="GET"
                    action="{{ route('tenant.index') }}"
                    class="flex flex-col gap-3 sm:flex-row sm:items-center"
                >

                    {{-- =================================================
                         SORTING
                    ================================================== --}}
                    <div
                        class="flex h-10 items-center
                               rounded-xl
                               border border-[#E2EBE9]
                               bg-[#F7F9F8]
                               px-3"
                    >

                        <span class="mr-2 text-xs text-[#8BA5A2]">
                            Urutkan:
                        </span>

                        <select
                            name="sort"
                            onchange="this.form.submit()"
                            class="border-0
                                   bg-transparent
                                   py-0
                                   pr-7
                                   text-xs
                                   font-semibold
                                   text-[#527D82]
                                   outline-none
                                   focus:ring-0"
                        >

                            <option
                                value="newest"
                                {{ request('sort', 'newest') === 'newest' ? 'selected' : '' }}
                            >
                                Terbaru → Terlama
                            </option>

                            <option
                                value="oldest"
                                {{ request('sort') === 'oldest' ? 'selected' : '' }}
                            >
                                Terlama → Terbaru
                            </option>

                            <option
                                value="az"
                                {{ request('sort') === 'az' ? 'selected' : '' }}
                            >
                                Mitra Usaha A → Z
                            </option>

                            <option
                                value="za"
                                {{ request('sort') === 'za' ? 'selected' : '' }}
                            >
                                Mitra Usaha Z → A
                            </option>

                        </select>

                    </div>


                    {{-- =================================================
                         SEARCH
                    ================================================== --}}
                    <div
                        class="flex h-10 w-full
                               items-center
                               rounded-xl
                               border border-[#E2EBE9]
                               bg-[#F7F9F8]
                               px-3
                               sm:w-[230px]"
                    >

                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-4 w-4 shrink-0 text-[#8BA5A2]"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >

                            <circle
                                cx="11"
                                cy="11"
                                r="7"
                            />

                            <path
                                stroke-linecap="round"
                                d="M20 20l-3.5-3.5"
                            />

                        </svg>

                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Cari mitra, brand, PIC..."
                            class="ml-2 w-full
                                   border-0
                                   bg-transparent
                                   text-xs
                                   text-[#365F65]
                                   outline-none
                                   focus:ring-0
                                   placeholder-[#9AAEAB]"
                        >

                    </div>


                    {{-- =================================================
                         TOMBOL SEARCH
                    ================================================== --}}
                    <button
                        type="submit"
                        class="hidden"
                    >
                        Cari
                    </button>

                </form>

            </div>

        </div>



        {{-- =====================================================
             TABLE
        ====================================================== --}}
        <div class="overflow-x-auto">

            <table class="min-w-full text-left">

                {{-- =================================================
                     TABLE HEADER
                ================================================== --}}
                <thead>

                    <tr
                        class="border-b border-[#E8EFED]
                               bg-[#F8FAF9]"
                    >

                        {{-- NO --}}
                        <th
                            class="px-5 py-4
                                   text-[10px]
                                   font-bold
                                   uppercase
                                   tracking-[0.08em]
                                   text-[#8BA5A2]"
                        >
                            No
                        </th>


                        {{-- MITRA USAHA --}}
                        <th
                            class="px-5 py-4
                                   text-[10px]
                                   font-bold
                                   uppercase
                                   tracking-[0.08em]
                                   text-[#8BA5A2]"
                        >
                            Mitra Usaha
                        </th>


                        {{-- BRAND --}}
                        <th
                            class="px-5 py-4
                                   text-[10px]
                                   font-bold
                                   uppercase
                                   tracking-[0.08em]
                                   text-[#8BA5A2]"
                        >
                            Brand
                        </th>


                        {{-- JENIS USAHA --}}
                        <th
                            class="px-5 py-4
                                   text-[10px]
                                   font-bold
                                   uppercase
                                   tracking-[0.08em]
                                   text-[#8BA5A2]"
                        >
                            Jenis Usaha
                        </th>


                        {{-- PIC COMMERCIAL --}}
                        <th
                            class="px-5 py-4
                                   text-[10px]
                                   font-bold
                                   uppercase
                                   tracking-[0.08em]
                                   text-[#8BA5A2]"
                        >
                            PIC Commercial
                        </th>


                        {{-- DATA TENANT --}}
                        <th
                            class="px-5 py-4
                                   text-center
                                   text-[10px]
                                   font-bold
                                   uppercase
                                   tracking-[0.08em]
                                   text-[#8BA5A2]"
                        >
                            Data Tenant
                        </th>


                        {{-- AKSI --}}
                        <th
                            class="px-5 py-4
                                   text-center
                                   text-[10px]
                                   font-bold
                                   uppercase
                                   tracking-[0.08em]
                                   text-[#8BA5A2]"
                        >
                            Aksi
                        </th>

                    </tr>

                </thead>



                {{-- =================================================
                     TABLE BODY
                ================================================== --}}
                <tbody class="divide-y divide-[#EEF3F1]">

                    @forelse ($kerjaSamas as $index => $kerjaSama)

                        @php
                            $tenant = $tenantByKerjaSama->get($kerjaSama->id);
                        @endphp


                        <tr class="transition hover:bg-[#F8FAF9]">

                            {{-- =================================================
                                 NO
                            ================================================== --}}
                            <td
                                class="whitespace-nowrap
                                       px-5 py-4
                                       text-sm
                                       font-medium
                                       text-[#8BA5A2]"
                            >
                                {{ $index + 1 }}
                            </td>



                            {{-- =================================================
                                 MITRA USAHA
                            ================================================== --}}
                            <td class="px-5 py-4">

                                <div
                                    class="text-sm
                                           font-semibold
                                           text-[#365F65]"
                                >
                                    {{ $kerjaSama->mitra_usaha }}
                                </div>

                                @if ($kerjaSama->tanggal_pengajuan)

                                    <div
                                        class="mt-1
                                               text-xs
                                               text-[#9AAEAB]"
                                    >
                                        Pengajuan:
                                        {{ $kerjaSama->tanggal_pengajuan->format('d M Y') }}
                                    </div>

                                @endif

                            </td>



                            {{-- =================================================
                                 BRAND
                            ================================================== --}}
                            <td
                                class="px-5 py-4
                                       text-sm
                                       text-[#527D82]"
                            >
                                {{ $kerjaSama->brand ?: '-' }}
                            </td>



                            {{-- =================================================
                                 JENIS USAHA
                            ================================================== --}}
                            <td class="px-5 py-4">

                                <span
                                    class="inline-flex
                                           rounded-lg
                                           bg-[#E8F0EE]
                                           px-2.5 py-1
                                           text-xs
                                           font-semibold
                                           text-[#527D82]"
                                >
                                    {{ $kerjaSama->jenis_usaha }}
                                </span>

                            </td>



                            {{-- =================================================
                                 PIC COMMERCIAL
                            ================================================== --}}
                            <td class="px-5 py-4">

                                <div
                                    class="text-sm
                                           font-semibold
                                           text-[#365F65]"
                                >
                                    {{ $kerjaSama->pic_commercial }}
                                </div>

                                @if ($kerjaSama->catatan)

                                    <div
                                        class="mt-1
                                               max-w-[220px]
                                               truncate
                                               text-xs
                                               text-[#9AAEAB]"
                                    >
                                        {{ $kerjaSama->catatan }}
                                    </div>

                                @endif

                            </td>



                            {{-- =================================================
                                 DATA TENANT
                            ================================================== --}}
                            <td class="px-5 py-4">

                                <div
                                    class="flex
                                           items-center
                                           justify-center"
                                >

                                    @if ($tenant)

                                        {{-- =================================================
                                             SUDAH ADA TENANT
                                        ================================================== --}}
                                        <div
                                            class="flex
                                                   flex-col
                                                   items-center
                                                   gap-1.5"
                                        >

                                            <span
                                                class="inline-flex
                                                       items-center
                                                       gap-1.5
                                                       rounded-lg
                                                       bg-[#E8F3EE]
                                                       px-3
                                                       py-1.5
                                                       text-xs
                                                       font-semibold
                                                       text-[#4D856F]"
                                            >

                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    class="h-3.5 w-3.5"
                                                    fill="none"
                                                    viewBox="0 0 24 24"
                                                    stroke="currentColor"
                                                    stroke-width="2"
                                                >

                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        d="m5 12 4 4L19 6"
                                                    />

                                                </svg>

                                                Sudah Diinput

                                            </span>


                                            <a
                                                href="{{ route('tenant.show', $tenant) }}"
                                                class="text-[11px]
                                                       font-medium
                                                       text-[#527D82]
                                                       underline
                                                       decoration-[#B8CBC7]
                                                       underline-offset-2
                                                       transition
                                                       hover:text-[#365F65]"
                                            >
                                                Lihat Data
                                            </a>

                                        </div>


                                    @else

                                        {{-- =================================================
                                             BELUM ADA TENANT
                                        ================================================== --}}
                                        <a
                                            href="{{ route('tenant.create', ['kerja_sama_id' => $kerjaSama->id]) }}"
                                            class="inline-flex
                                                   items-center
                                                   justify-center
                                                   rounded-lg
                                                   bg-[#365F65]
                                                   px-3.5
                                                   py-2
                                                   text-xs
                                                   font-semibold
                                                   text-white
                                                   transition
                                                   hover:bg-[#2F5358]"
                                        >
                                            Input Data
                                        </a>

                                    @endif

                                </div>

                            </td>



                            {{-- =================================================
                                 AKSI
                            ================================================== --}}
                            <td class="px-5 py-4">

                                @if ($tenant)

                                    <div
                                        class="flex
                                               items-center
                                               justify-center
                                               gap-2"
                                    >

                                        {{-- =================================================
                                             DETAIL
                                        ================================================== --}}
                                        <a
                                            href="{{ route('tenant.show', $tenant) }}"
                                            title="Lihat detail"
                                            class="flex h-9 w-9
                                                   items-center justify-center
                                                   rounded-lg
                                                   bg-[#F3F6F5]
                                                   text-[#527D82]
                                                   transition
                                                   hover:bg-[#E1EBE8]"
                                        >

                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                class="h-4 w-4"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                                stroke-width="1.8"
                                            >

                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M2.25 12s3.75-6 9.75-6 9.75 6 9.75 6-3.75 6-9.75 6-9.75-6-9.75-6z"
                                                />

                                                <circle
                                                    cx="12"
                                                    cy="12"
                                                    r="2.5"
                                                />

                                            </svg>

                                        </a>



                                        {{-- =================================================
                                             EDIT
                                        ================================================== --}}
                                        <a
                                            href="{{ route('tenant.edit', $tenant) }}"
                                            title="Edit"
                                            class="flex h-9 w-9
                                                   items-center justify-center
                                                   rounded-lg
                                                   bg-[#F3F6F5]
                                                   text-[#527D82]
                                                   transition
                                                   hover:bg-[#E1EBE8]"
                                        >

                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                class="h-4 w-4"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                                stroke-width="1.8"
                                            >

                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M16.862 3.487a2.1 2.1 0 013 3L8.25 18.1l-4.5 1.2-4.5 1.2 1.2-4.5L16.862 3.487z"
                                                />

                                            </svg>

                                        </a>



                                        {{-- =================================================
                                             DELETE
                                        ================================================== --}}
                                        <form
                                            action="{{ route('tenant.destroy', $tenant) }}"
                                            method="POST"
                                            onsubmit="return confirm('Apakah kamu yakin ingin menghapus data tenant ini?')"
                                        >

                                            @csrf

                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                title="Hapus"
                                                class="flex h-9 w-9
                                                       items-center justify-center
                                                       rounded-lg
                                                       bg-[#FFF4F2]
                                                       text-[#B96B60]
                                                       transition
                                                       hover:bg-[#FDE5E1]"
                                            >

                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    class="h-4 w-4"
                                                    fill="none"
                                                    viewBox="0 0 24 24"
                                                    stroke="currentColor"
                                                    stroke-width="1.8"
                                                >

                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        d="M6 7h12M10 11v6M14 11v6M9 7l1-2h4l1 2m-8 0l.75 13h8.5L17 7"
                                                    />

                                                </svg>

                                            </button>

                                        </form>

                                    </div>

                                @else

                                    <span
                                        class="text-xs
                                               text-[#B1BFBD]"
                                    >
                                        Belum ada data
                                    </span>

                                @endif

                            </td>

                        </tr>


                    @empty

                        {{-- =================================================
                             DATA KOSONG
                        ================================================== --}}
                        <tr>

                            <td
                                colspan="7"
                                class="px-5 py-16 text-center"
                            >

                                <div class="flex flex-col items-center">

                                    <div
                                        class="flex h-14 w-14
                                               items-center justify-center
                                               rounded-2xl
                                               bg-[#E8F0EE]
                                               text-[#527D82]"
                                    >

                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-7 w-7"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                            stroke-width="1.7"
                                        >

                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M16 11a4 4 0 10-8 0 4 4 0 008 0zM4 21a8 8 0 0116 0"
                                            />

                                        </svg>

                                    </div>


                                    <h4
                                        class="mt-4
                                               text-sm
                                               font-bold
                                               text-[#365F65]"
                                    >
                                        Belum ada data kerja sama
                                    </h4>


                                    <p
                                        class="mt-1
                                               max-w-sm
                                               text-xs
                                               text-[#8BA5A2]"
                                    >
                                        Data kerja sama yang telah ditambahkan
                                        akan ditampilkan di tabel ini.
                                    </p>

                                </div>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>



        {{-- =====================================================
             JUMLAH DATA
        ====================================================== --}}
        @if ($kerjaSamas->count() > 0)

            <div
                class="border-t
                       border-[#E8EFED]
                       px-5 py-4"
            >

                <p class="text-xs text-[#8BA5A2]">

                    Menampilkan

                    <span class="font-semibold text-[#527D82]">
                        {{ $kerjaSamas->count() }}
                    </span>

                    kerja sama

                </p>

            </div>

        @endif

    </div>

@endsection