@extends('layouts.app')

@section('page-title', 'Lokasi')

@section('content')

<div class="space-y-6">

    {{-- =========================================================
         HEADER
    ========================================================== --}}

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

        <div>
            <p class="text-sm text-[#8BA5A2]">
                Management
            </p>

            <h2 class="mt-1 text-2xl font-bold text-[#365F65]">
                Data Lokasi
            </h2>

            <p class="mt-1 text-sm text-[#7A9290]">
                Daftar lokasi yang tersedia pada area bandara.
            </p>
        </div>


        {{-- TAMBAH LOKASI --}}

        <a
            href="{{ route('lokasi.create') }}"
            class="inline-flex items-center justify-center gap-2
                   rounded-xl
                   bg-[#365F65]
                   px-5 py-3
                   text-sm font-semibold
                   text-white
                   hover:bg-[#294F55]
                   transition"
        >
            <span class="text-lg leading-none">+</span>
            Tambah Lokasi
        </a>

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
         SEARCH & FILTER
    ========================================================== --}}

    <div class="rounded-2xl bg-white border border-[#E2EBE9] p-5">

        <form
            method="GET"
            action="{{ route('lokasi.index') }}"
            class="grid grid-cols-1 gap-3 md:grid-cols-4"
        >

            {{-- SEARCH --}}

            <div class="md:col-span-2">

                <label class="mb-1.5 block text-xs font-semibold text-[#527D82]">
                    Cari Lokasi
                </label>

                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari terminal, nomor RO, kode ruang..."
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


            {{-- TERMINAL --}}

            <div>

                <label class="mb-1.5 block text-xs font-semibold text-[#527D82]">
                    Terminal
                </label>

                <select
                    name="terminal"
                    class="w-full rounded-xl
                           border border-[#DCE6E4]
                           bg-[#F8FAF9]
                           px-4 py-3
                           text-sm text-[#365F65]
                           outline-none
                           focus:border-[#527D82]"
                >

                    <option value="">
                        Semua Terminal
                    </option>

                    <option
                        value="T1"
                        {{ request('terminal') == 'T1' ? 'selected' : '' }}
                    >
                        Terminal 1
                    </option>

                    <option
                        value="T2"
                        {{ request('terminal') == 'T2' ? 'selected' : '' }}
                    >
                        Terminal 2
                    </option>

                </select>

            </div>


            {{-- STATUS --}}

            <div>

                <label class="mb-1.5 block text-xs font-semibold text-[#527D82]">
                    Status
                </label>

                <select
                    name="status"
                    class="w-full rounded-xl
                           border border-[#DCE6E4]
                           bg-[#F8FAF9]
                           px-4 py-3
                           text-sm text-[#365F65]
                           outline-none
                           focus:border-[#527D82]"
                >

                    <option value="">
                        Semua Status
                    </option>

                    <option
                        value="Kosong"
                        {{ request('status') == 'Kosong' ? 'selected' : '' }}
                    >
                        Kosong
                    </option>

                    <option
                        value="Terisi"
                        {{ request('status') == 'Terisi' ? 'selected' : '' }}
                    >
                        Terisi
                    </option>

                </select>

            </div>


            {{-- SORT --}}

            <div>

                <label class="mb-1.5 block text-xs font-semibold text-[#527D82]">
                    Urutkan
                </label>

                <select
                    name="sort"
                    class="w-full rounded-xl
                           border border-[#DCE6E4]
                           bg-[#F8FAF9]
                           px-4 py-3
                           text-sm text-[#365F65]
                           outline-none
                           focus:border-[#527D82]"
                >

                    <option
                        value="newest"
                        {{ request('sort', 'newest') == 'newest' ? 'selected' : '' }}
                    >
                        Terbaru
                    </option>

                    <option
                        value="oldest"
                        {{ request('sort') == 'oldest' ? 'selected' : '' }}
                    >
                        Terlama
                    </option>

                    <option
                        value="az"
                        {{ request('sort') == 'az' ? 'selected' : '' }}
                    >
                        Kode Ruang A-Z
                    </option>

                    <option
                        value="za"
                        {{ request('sort') == 'za' ? 'selected' : '' }}
                    >
                        Kode Ruang Z-A
                    </option>

                </select>

            </div>


            {{-- BUTTON --}}

            <div class="md:col-span-4 flex justify-end">

                <button
                    type="submit"
                    class="rounded-xl
                           bg-[#E8F0EE]
                           px-5 py-2.5
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

    <div class="overflow-hidden rounded-2xl bg-white border border-[#E2EBE9]">

        <div class="overflow-x-auto">

            <table class="w-full min-w-[1100px]">

                <thead class="bg-[#F3F6F5]">

                    <tr>

                        <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wide text-[#7A9290]">
                            No
                        </th>

                        <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wide text-[#7A9290]">
                            Kode Ruang
                        </th>

                        <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wide text-[#7A9290]">
                            Terminal
                        </th>

                        <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wide text-[#7A9290]">
                            No. RO
                        </th>

                        <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wide text-[#7A9290]">
                            Lokasi
                        </th>

                        <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wide text-[#7A9290]">
                            Luas
                        </th>

                        <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wide text-[#7A9290]">
                            Tenant
                        </th>

                        <th class="px-5 py-4 text-center text-xs font-bold uppercase tracking-wide text-[#7A9290]">
                            Status
                        </th>

                        <th class="px-5 py-4 text-center text-xs font-bold uppercase tracking-wide text-[#7A9290]">
                            Aksi
                        </th>

                    </tr>

                </thead>


                <tbody class="divide-y divide-[#EEF2F1]">

                    @forelse($lokasis as $index => $lokasi)

                        <tr class="hover:bg-[#FAFCFB] transition">

                            {{-- NO --}}

                            <td class="px-5 py-4 text-sm text-[#7A9290]">
                                {{ $index + 1 }}
                            </td>


                            {{-- KODE RUANG --}}

                            <td class="px-5 py-4">

                                <span class="font-semibold text-[#365F65]">
                                    {{ $lokasi->kode_ruang ?: '-' }}
                                </span>

                            </td>


                            {{-- TERMINAL --}}

                            <td class="px-5 py-4 text-sm text-[#527D82]">
                                {{ $lokasi->terminal ?: '-' }}
                            </td>


                            {{-- NO RO --}}

                            <td class="px-5 py-4 text-sm text-[#527D82]">
                                {{ $lokasi->nomor_ro ?: '-' }}
                            </td>


                            {{-- LOKASI --}}

                            <td class="px-5 py-4 text-sm text-[#527D82]">
                                {{ $lokasi->lokasi ?: '-' }}
                            </td>


                            {{-- LUAS --}}

                            <td class="px-5 py-4 text-sm text-[#527D82]">

                                @if($lokasi->luas_area)
                                    {{ number_format($lokasi->luas_area, 2, ',', '.') }} m²
                                @else
                                    -
                                @endif

                            </td>


                            {{-- TENANT --}}

                            <td class="px-5 py-4">

                                @if($lokasi->tenant)

                                    <span class="font-semibold text-[#365F65]">
                                        {{ $lokasi->tenant->nama_tenant }}
                                    </span>

                                @else

                                    <span class="text-sm text-[#8BA5A2]">
                                        Belum ditempati
                                    </span>

                                @endif

                            </td>


                            {{-- STATUS --}}

                            <td class="px-5 py-4 text-center">

                                @if($lokasi->status === 'Terisi')

                                    <span
                                        class="inline-flex
                                               rounded-full
                                               bg-[#E8F0EE]
                                               px-3 py-1
                                               text-xs font-semibold
                                               text-[#365F65]"
                                    >
                                        Terisi
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
                                        Kosong
                                    </span>

                                @endif

                            </td>


                            {{-- AKSI --}}

                            <td class="px-5 py-4">

                                <div class="flex items-center justify-center gap-2">

                                    <a
                                        href="{{ route('lokasi.show', $lokasi) }}"
                                        class="rounded-lg
                                               bg-[#EEF4F2]
                                               px-3 py-2
                                               text-xs font-semibold
                                               text-[#527D82]
                                               hover:bg-[#E2ECE9]
                                               transition"
                                    >
                                        Detail
                                    </a>


                                    <a
                                        href="{{ route('lokasi.edit', $lokasi) }}"
                                        class="rounded-lg
                                               bg-[#F5F7F7]
                                               px-3 py-2
                                               text-xs font-semibold
                                               text-[#527D82]
                                               hover:bg-[#E9EEEE]
                                               transition"
                                    >
                                        Edit
                                    </a>

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="9"
                                class="px-5 py-12 text-center"
                            >

                                <p class="text-sm font-semibold text-[#527D82]">
                                    Belum ada data lokasi
                                </p>

                                <p class="mt-1 text-xs text-[#8BA5A2]">
                                    Data lokasi yang ditambahkan akan muncul di sini.
                                </p>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection