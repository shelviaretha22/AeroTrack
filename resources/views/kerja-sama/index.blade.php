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

            {{-- TABLE HEADER --}}
            <div class="px-6 py-5 border-b border-[#DCE6E4]">

                <h2 class="text-base font-bold text-[#365F65]">
                    Daftar Kerja Sama
                </h2>

                <p class="mt-1 text-xs text-[#8BA5A2]">
                    Data kerja sama yang telah ditambahkan.
                </p>

            </div>


            {{-- DATA ADA --}}
            @if ($kerjaSamas->count() > 0)

                <div class="overflow-x-auto">

                    <table class="w-full text-sm">

                        <thead class="bg-[#F3F6F5]">

                            <tr class="text-left">

                                <th class="px-6 py-4 font-semibold text-[#365F65]">
                                    No
                                </th>

                                <th class="px-6 py-4 font-semibold text-[#365F65]">
                                    Mitra Usaha
                                </th>

                                <th class="px-6 py-4 font-semibold text-[#365F65]">
                                    Brand
                                </th>

                                <th class="px-6 py-4 font-semibold text-[#365F65]">
                                    Jenis Usaha
                                </th>

                                <th class="px-6 py-4 font-semibold text-[#365F65]">
                                    Bentuk Kerja Sama
                                </th>

                                <th class="px-6 py-4 font-semibold text-[#365F65]">
                                    Periode
                                </th>

                                <th class="px-6 py-4 font-semibold text-[#365F65]">
                                    Status
                                </th>

                                <th class="px-6 py-4 text-right font-semibold text-[#365F65]">
                                    Aksi
                                </th>

                            </tr>

                        </thead>


                        <tbody class="divide-y divide-[#DCE6E4]">

                            @foreach ($kerjaSamas as $index => $kerjaSama)

                                <tr class="hover:bg-[#F8FAF9] transition">

                                    {{-- NO --}}
                                    <td class="px-6 py-4 text-[#8BA5A2]">
                                        {{ $index + 1 }}
                                    </td>


                                    {{-- MITRA --}}
                                    <td class="px-6 py-4">

                                        <p class="font-semibold text-[#365F65]">
                                            {{ $kerjaSama->mitra_usaha }}
                                        </p>

                                        <p class="mt-1 text-xs text-[#8BA5A2]">
                                            PIC: {{ $kerjaSama->pic_commercial }}
                                        </p>

                                    </td>


                                    {{-- BRAND --}}
                                    <td class="px-6 py-4 text-[#365F65]">
                                        {{ $kerjaSama->brand ?: '-' }}
                                    </td>


                                    {{-- JENIS USAHA --}}
                                    <td class="px-6 py-4 text-[#365F65]">
                                        {{ $kerjaSama->jenis_usaha }}
                                    </td>


                                    {{-- BENTUK KERJA SAMA --}}
                                    <td class="px-6 py-4 text-[#365F65]">
                                        {{ $kerjaSama->bentuk_kerja_sama }}
                                    </td>


                                    {{-- PERIODE --}}
                                    <td class="px-6 py-4">

                                        @if ($kerjaSama->tanggal_mulai)

                                            <p class="text-[#365F65]">
                                                {{ $kerjaSama->tanggal_mulai->format('d M Y') }}
                                            </p>

                                        @else

                                            <p class="text-[#8BA5A2]">
                                                Belum ditentukan
                                            </p>

                                        @endif


                                        @if ($kerjaSama->tanggal_berakhir)

                                            <p class="mt-1 text-xs text-[#8BA5A2]">
                                                s/d {{ $kerjaSama->tanggal_berakhir->format('d M Y') }}
                                            </p>

                                        @endif

                                    </td>


                                    {{-- STATUS --}}
                                    <td class="px-6 py-4">

                                        @if ($kerjaSama->status === 'Disetujui')

                                            <span
                                                class="inline-flex items-center gap-2
                                                       px-3 py-1.5 rounded-full
                                                       bg-[#EDF6F4] text-[#365F65]
                                                       text-xs font-semibold"
                                            >
                                                <span class="w-1.5 h-1.5 rounded-full bg-[#365F65]"></span>
                                                Disetujui
                                            </span>

                                        @elseif ($kerjaSama->status === 'Proses')

                                            <span
                                                class="inline-flex items-center gap-2
                                                       px-3 py-1.5 rounded-full
                                                       bg-[#FFF8E7] text-[#8A6A17]
                                                       text-xs font-semibold"
                                            >
                                                <span class="w-1.5 h-1.5 rounded-full bg-[#D5A93A]"></span>
                                                Proses
                                            </span>

                                        @elseif ($kerjaSama->status === 'Ditolak')

                                            <span
                                                class="inline-flex items-center gap-2
                                                       px-3 py-1.5 rounded-full
                                                       bg-[#FFF0F0] text-[#B45353]
                                                       text-xs font-semibold"
                                            >
                                                <span class="w-1.5 h-1.5 rounded-full bg-[#D66A6A]"></span>
                                                Ditolak
                                            </span>

                                        @else

                                            <span
                                                class="inline-flex items-center gap-2
                                                       px-3 py-1.5 rounded-full
                                                       bg-[#F3F6F5] text-[#6B8582]
                                                       text-xs font-semibold"
                                            >
                                                <span class="w-1.5 h-1.5 rounded-full bg-[#8BA5A2]"></span>
                                                Draft
                                            </span>

                                        @endif

                                    </td>


                                    {{-- AKSI --}}
                                    <td class="px-6 py-4">

                                        <div class="flex items-center justify-end gap-2">

                                            {{-- DETAIL --}}
                                            <a
                                                href="{{ route('kerja-sama.show', $kerjaSama->id) }}"
                                                title="Lihat Detail"
                                                class="w-9 h-9 rounded-lg
                                                       bg-[#F3F6F5]
                                                       text-[#527D82]
                                                       flex items-center justify-center
                                                       hover:bg-[#DCEAE7]
                                                       transition"
                                            >
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    class="w-4 h-4"
                                                    fill="none"
                                                    viewBox="0 0 24 24"
                                                    stroke="currentColor"
                                                    stroke-width="1.8"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12s-3.75 6.75-9.75 6.75S2.25 12 2.25 12z"
                                                    />

                                                    <circle
                                                        cx="12"
                                                        cy="12"
                                                        r="2.5"
                                                    />
                                                </svg>
                                            </a>


                                            {{-- EDIT --}}
                                            <a
                                                href="{{ route('kerja-sama.edit', $kerjaSama->id) }}"
                                                title="Edit"
                                                class="w-9 h-9 rounded-lg
                                                       bg-[#EEF3F2]
                                                       text-[#365F65]
                                                       flex items-center justify-center
                                                       hover:bg-[#DCE6E4]
                                                       transition"
                                            >
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    class="w-4 h-4"
                                                    fill="none"
                                                    viewBox="0 0 24 24"
                                                    stroke="currentColor"
                                                    stroke-width="1.8"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        d="M12 20h9"
                                                    />

                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        d="M16.5 3.5a2.12 2.12 0 013 3L8 18l-4 1 1-4L16.5 3.5z"
                                                    />
                                                </svg>
                                            </a>


                                            {{-- DELETE --}}
                                            <form
                                                action="{{ route('kerja-sama.destroy', $kerjaSama->id) }}"
                                                method="POST"
                                                onsubmit="return confirm('Yakin ingin menghapus data kerja sama ini?')"
                                                class="inline"
                                            >

                                                @csrf
                                                @method('DELETE')

                                                <button
                                                    type="submit"
                                                    title="Hapus"
                                                    class="w-9 h-9 rounded-lg
                                                           bg-[#FCEEEE]
                                                           text-[#B54B4B]
                                                           flex items-center justify-center
                                                           hover:bg-[#F8DCDC]
                                                           transition"
                                                >
                                                    <svg
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        class="w-4 h-4"
                                                        fill="none"
                                                        viewBox="0 0 24 24"
                                                        stroke="currentColor"
                                                        stroke-width="1.8"
                                                    >
                                                        <path
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            d="M3 6h18"
                                                        />

                                                        <path
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            d="M8 6V4a1 1 0 011-1h6a1 1 0 011 1v2"
                                                        />

                                                        <path
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            d="M19 6l-1 15H6L5 6"
                                                        />

                                                        <path
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            d="M10 11v6M14 11v6"
                                                        />
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


            {{-- DATA KOSONG --}}
            @else

                <div class="px-6 py-16 text-center">

                    <div
                        class="mx-auto w-16 h-16 rounded-2xl
                               bg-[#E8F0EF]
                               flex items-center justify-center"
                    >
                        <svg
                            class="w-8 h-8 text-[#365F65]"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="1.7"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h6l4 4v12a2 2 0 01-2 2z"
                            />
                        </svg>
                    </div>

                    <h3 class="mt-5 text-base font-bold text-[#365F65]">
                        Belum Ada Data Kerja Sama
                    </h3>

                    <p class="mt-2 text-sm text-[#8BA5A2] max-w-md mx-auto">
                        Belum ada data kerja sama yang tersimpan.
                        Silakan tambahkan data kerja sama pertama.
                    </p>

                    <a
                        href="{{ route('kerja-sama.create') }}"
                        class="inline-flex items-center gap-2 mt-5
                               px-5 py-3 rounded-xl
                               bg-[#365F65] text-white
                               text-sm font-semibold
                               hover:bg-[#2D5055]
                               transition"
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

                        Tambah Data
                    </a>

                </div>

            @endif

        </div>

    </div>
</div>

@endsection