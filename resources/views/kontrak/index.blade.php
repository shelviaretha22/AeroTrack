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

    @if(session('success'))

        <div
            class="rounded-xl
                   border border-[#CFE0DC]
                   bg-[#EEF6F3]
                   px-4 py-3
                   text-sm text-[#365F65]"
        >
            {{ session('success') }}
        </div>

    @endif


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
                    class="w-full rounded-xl
                           border border-[#DCE6E4]
                           bg-[#F8FAF9]
                           px-4 py-3
                           text-sm text-[#365F65]
                           outline-none
                           focus:border-[#527D82]
                           focus:ring-2
                           focus:ring-[#527D82]/10"
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
                    class="w-full rounded-xl
                           border border-[#DCE6E4]
                           bg-[#F8FAF9]
                           px-4 py-3
                           text-sm text-[#365F65]
                           outline-none
                           focus:border-[#527D82]
                           focus:ring-2
                           focus:ring-[#527D82]/10"
                >

                    <option
                        value="newest"
                        {{ request('sort', 'newest') === 'newest' ? 'selected' : '' }}
                    >
                        Terbaru
                    </option>

                    <option
                        value="oldest"
                        {{ request('sort') === 'oldest' ? 'selected' : '' }}
                    >
                        Terlama
                    </option>

                    <option
                        value="az"
                        {{ request('sort') === 'az' ? 'selected' : '' }}
                    >
                        Nama Brand A-Z
                    </option>

                    <option
                        value="za"
                        {{ request('sort') === 'za' ? 'selected' : '' }}
                    >
                        Nama Brand Z-A
                    </option>

                    <option
                        value="active"
                        {{ request('sort') === 'active' ? 'selected' : '' }}
                    >
                        Aktif
                    </option>

                    <option
                        value="inactive"
                        {{ request('sort') === 'inactive' ? 'selected' : '' }}
                    >
                        Non-Aktif
                    </option>

                </select>

            </div>


            {{-- BUTTON FILTER --}}

            <div class="flex items-end justify-end">

                <button
                    type="submit"
                    class="rounded-xl
                           bg-[#E8F0EE]
                           px-5 py-3
                           text-sm font-semibold
                           text-[#365F65]
                           hover:bg-[#DCE9E6]
                           transition"
                >
                    Terapkan Filter
                </button>

            </div>

        </form>

    </div>


    {{-- =========================================================
         TABLE
    ========================================================== --}}

    <div class="overflow-hidden rounded-2xl border border-[#E2EBE9] bg-white">

        <div class="overflow-x-auto">

            <table class="w-full min-w-[1250px]">

                {{-- TABLE HEADER --}}

                <thead class="bg-[#F3F6F5]">

                    <tr>

                        <th
                            class="px-5 py-4 text-left
                                   text-xs font-bold uppercase
                                   tracking-wide text-[#7A9290]"
                        >
                            No
                        </th>

                        <th
                            class="px-5 py-4 text-left
                                   text-xs font-bold uppercase
                                   tracking-wide text-[#7A9290]"
                        >
                            Nomor Kerja Sama
                        </th>

                        <th
                            class="px-5 py-4 text-left
                                   text-xs font-bold uppercase
                                   tracking-wide text-[#7A9290]"
                        >
                            Nama Brand
                        </th>

                        <th
                            class="px-5 py-4 text-left
                                   text-xs font-bold uppercase
                                   tracking-wide text-[#7A9290]"
                        >
                            Lokasi
                        </th>

                        <th
                            class="px-5 py-4 text-left
                                   text-xs font-bold uppercase
                                   tracking-wide text-[#7A9290]"
                        >
                            Tanggal Awal
                        </th>

                        <th
                            class="px-5 py-4 text-left
                                   text-xs font-bold uppercase
                                   tracking-wide text-[#7A9290]"
                        >
                            Tanggal Akhir
                        </th>

                        <th
                            class="px-5 py-4 text-left
                                   text-xs font-bold uppercase
                                   tracking-wide text-[#7A9290]"
                        >
                            Note
                        </th>

                        <th
                            class="px-5 py-4 text-center
                                   text-xs font-bold uppercase
                                   tracking-wide text-[#7A9290]"
                        >
                            Status
                        </th>

                        <th
                            class="px-5 py-4 text-center
                                   text-xs font-bold uppercase
                                   tracking-wide text-[#7A9290]"
                        >
                            Aksi
                        </th>

                    </tr>

                </thead>


                {{-- TABLE BODY --}}

                <tbody class="divide-y divide-[#EEF2F1]">

                    {{-- =================================================
                         DATA KONTRAK
                    ================================================== --}}

                    @forelse($kontraks ?? [] as $index => $kontrak)

                        @php

                            /*
                            |--------------------------------------------------------------------------
                            | Status Kontrak
                            |--------------------------------------------------------------------------
                            |
                            | Untuk sementara logika ini disiapkan di View.
                            | Nanti setelah struktur Kerja Sama tersedia,
                            | sebaiknya perhitungan dipindahkan ke Controller.
                            |
                            */

                            $tanggalAkhir = $kontrak->tanggal_akhir ?? null;

                            $isExpired = false;
                            $akanHabis = false;

                            if ($tanggalAkhir) {

                                $tanggalAkhirCarbon = \Carbon\Carbon::parse($tanggalAkhir);

                                $isExpired = $tanggalAkhirCarbon->isPast();

                                $akanHabis =
                                    !$isExpired &&
                                    now()->diffInDays($tanggalAkhirCarbon, false) <= 30;

                            }

                            $statusAktif = !$isExpired;

                            /*
                            |--------------------------------------------------------------------------
                            | ACC
                            |--------------------------------------------------------------------------
                            */

                            $sudahAcc = $kontrak->sudah_acc ?? false;

                        @endphp


                        {{-- ROW KONTRAK --}}

                        <tr
                            class="{{ $isExpired
                                ? 'bg-[#F7F8F8] text-[#9AA6A4]'
                                : 'hover:bg-[#FAFCFB]' }}
                                   transition"
                        >

                            {{-- NO --}}

                            <td
                                class="px-5 py-4 text-sm
                                       {{ $isExpired
                                            ? 'text-[#9AA6A4]'
                                            : 'text-[#7A9290]' }}"
                            >
                                {{ $index + 1 }}
                            </td>


                            {{-- NOMOR KERJA SAMA --}}

                            <td class="px-5 py-4">

                                <span
                                    class="font-semibold
                                           {{ $isExpired
                                                ? 'text-[#9AA6A4]'
                                                : 'text-[#365F65]' }}"
                                >
                                    {{ $kontrak->nomor_kerja_sama ?? '-' }}
                                </span>

                            </td>


                            {{-- NAMA BRAND --}}

                            <td class="px-5 py-4">

                                <span
                                    class="text-sm font-semibold
                                           {{ $isExpired
                                                ? 'text-[#9AA6A4]'
                                                : 'text-[#365F65]' }}"
                                >
                                    {{ $kontrak->nama_brand ?? '-' }}
                                </span>

                            </td>


                            {{-- LOKASI --}}

                            <td
                                class="px-5 py-4 text-sm
                                       {{ $isExpired
                                            ? 'text-[#9AA6A4]'
                                            : 'text-[#527D82]' }}"
                            >
                                {{ $kontrak->lokasi ?? '-' }}
                            </td>


                            {{-- TANGGAL AWAL --}}

                            <td
                                class="px-5 py-4 text-sm
                                       whitespace-nowrap
                                       {{ $isExpired
                                            ? 'text-[#9AA6A4]'
                                            : 'text-[#527D82]' }}"
                            >

                                @if(!empty($kontrak->tanggal_awal))

                                    {{ \Carbon\Carbon::parse($kontrak->tanggal_awal)->format('d/m/Y') }}

                                @else

                                    -

                                @endif

                            </td>


                            {{-- TANGGAL AKHIR --}}

                            <td
                                class="px-5 py-4 text-sm
                                       whitespace-nowrap
                                       {{ $isExpired
                                            ? 'text-[#9AA6A4]'
                                            : 'text-[#527D82]' }}"
                            >

                                @if($tanggalAkhir)

                                    {{ $tanggalAkhirCarbon->format('d/m/Y') }}

                                @else

                                    -

                                @endif

                            </td>


                            {{-- NOTE --}}

                            <td class="px-5 py-4">

                                @if($akanHabis)

                                    <span
                                        class="inline-flex
                                               rounded-lg
                                               bg-[#FFF7E6]
                                               px-3 py-1.5
                                               text-xs font-semibold
                                               text-[#A47720]"
                                    >
                                        Kontrak akan habis
                                    </span>

                                @elseif($isExpired)

                                    <span
                                        class="text-xs
                                               font-medium
                                               text-[#9AA6A4]"
                                    >
                                        Kontrak telah berakhir
                                    </span>

                                @else

                                    <span
                                        class="text-xs
                                               text-[#8BA5A2]"
                                    >
                                        -
                                    </span>

                                @endif

                            </td>


                            {{-- STATUS --}}

                            <td class="px-5 py-4 text-center">

                                @if($statusAktif)

                                    <span
                                        class="inline-flex
                                               rounded-full
                                               bg-[#E8F0EE]
                                               px-3 py-1
                                               text-xs font-semibold
                                               text-[#365F65]"
                                    >
                                        Aktif
                                    </span>

                                @else

                                    <span
                                        class="inline-flex
                                               rounded-full
                                               bg-[#F3F6F5]
                                               px-3 py-1
                                               text-xs font-semibold
                                               text-[#7A9290]"
                                    >
                                        Non-Aktif
                                    </span>

                                @endif

                            </td>


                            {{-- AKSI --}}

                            <td class="px-5 py-4">

                                <div class="flex items-center justify-center gap-2">

                                    @if($sudahAcc)

                                        {{-- VIEW --}}

                                        <a
                                            href="{{ route('kontrak.show', $kontrak->id) }}"
                                            class="rounded-lg
                                                   bg-[#EEF4F2]
                                                   px-3 py-2
                                                   text-xs font-semibold
                                                   text-[#527D82]
                                                   hover:bg-[#E2ECE9]
                                                   transition"
                                        >
                                            View
                                        </a>

                                    @else

                                        {{-- BERI ACC --}}

                                        <a
                                            href="{{ route('kontrak.acc', $kontrak->id) }}"
                                            class="rounded-lg
                                                   bg-[#365F65]
                                                   px-3 py-2
                                                   text-xs font-semibold
                                                   text-white
                                                   hover:bg-[#294F55]
                                                   transition"
                                        >
                                            Beri ACC
                                        </a>

                                    @endif

                                </div>

                            </td>

                        </tr>


                    @empty

                        {{-- EMPTY STATE --}}

                        <tr>

                            <td
                                colspan="9"
                                class="px-5 py-16 text-center"
                            >

                                <div class="mx-auto max-w-md">

                                    <div
                                        class="mx-auto flex h-14 w-14
                                               items-center justify-center
                                               rounded-2xl
                                               bg-[#F3F6F5]"
                                    >
                                        <span class="text-xl text-[#8BA5A2]">
                                            📄
                                        </span>
                                    </div>

                                    <p
                                        class="mt-4
                                               text-sm font-semibold
                                               text-[#527D82]"
                                    >
                                        Belum ada data kontrak
                                    </p>

                                    <p
                                        class="mt-1
                                               text-xs leading-5
                                               text-[#8BA5A2]"
                                    >
                                        Data kontrak akan muncul setelah data
                                        Kerja Sama tersedia dan telah memiliki
                                        informasi kontrak.
                                    </p>

                                </div>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>


    {{-- =========================================================
         KETERANGAN
    ========================================================== --}}

    <div
        class="rounded-xl
               border border-[#DCE6E4]
               bg-[#F3F6F5]
               px-4 py-3"
    >

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

@endsection