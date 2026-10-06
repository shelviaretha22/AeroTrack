@extends('layouts.app')

@section('page-title', 'Detail Kontrak')

@section('content')

<div class="max-w-5xl space-y-6">

    {{-- HEADER --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

        <div>

            <p class="text-sm text-[#8BA5A2]">
                Management / Kontrak / Detail
            </p>

            <h2 class="mt-1 text-2xl font-bold text-[#365F65]">
                Detail Kontrak
            </h2>

            <p class="mt-1 text-sm text-[#7A9290]">
                Informasi lengkap kontrak yang telah mendapatkan persetujuan.
            </p>

        </div>


        {{-- STATUS --}}
        <span
            class="inline-flex w-fit items-center rounded-full
                   bg-[#E8F0EE]
                   px-4 py-2
                   text-xs font-semibold
                   text-[#365F65]"
        >
            ✓ Telah Di-ACC
        </span>

    </div>


    {{-- INFORMASI KERJA SAMA --}}
    <div class="rounded-2xl border border-[#E2EBE9] bg-white p-6 sm:p-8">

        <div class="mb-6">

            <h3 class="text-base font-bold text-[#365F65]">
                Informasi Kerja Sama
            </h3>

            <p class="mt-1 text-xs text-[#8BA5A2]">
                Informasi kerja sama yang menjadi dasar kontrak.
            </p>

        </div>


        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">

            {{-- NOMOR KERJA SAMA --}}
            <div>

                <p class="text-xs font-semibold text-[#8BA5A2]">
                    Nomor Kerja Sama
                </p>

                <p class="mt-1 text-sm font-semibold text-[#365F65]">
                    -
                </p>

            </div>


            {{-- NAMA BRAND --}}
            <div>

                <p class="text-xs font-semibold text-[#8BA5A2]">
                    Nama Brand
                </p>

                <p class="mt-1 text-sm font-semibold text-[#365F65]">
                    -
                </p>

            </div>


            {{-- JENIS USAHA --}}
            <div>

                <p class="text-xs font-semibold text-[#8BA5A2]">
                    Jenis Usaha
                </p>

                <p class="mt-1 text-sm font-semibold text-[#365F65]">
                    -
                </p>

            </div>


            {{-- BENTUK KERJA SAMA --}}
            <div>

                <p class="text-xs font-semibold text-[#8BA5A2]">
                    Bentuk Kerja Sama
                </p>

                <p class="mt-1 text-sm font-semibold text-[#365F65]">
                    -
                </p>

            </div>


            {{-- TANGGAL AWAL --}}
            <div>

                <p class="text-xs font-semibold text-[#8BA5A2]">
                    Tanggal Awal Kontrak
                </p>

                <p class="mt-1 text-sm font-semibold text-[#365F65]">
                    -
                </p>

            </div>


            {{-- TANGGAL AKHIR --}}
            <div>

                <p class="text-xs font-semibold text-[#8BA5A2]">
                    Tanggal Akhir Kontrak
                </p>

                <p class="mt-1 text-sm font-semibold text-[#365F65]">
                    -
                </p>

            </div>

        </div>

    </div>


    {{-- INFORMASI LOKASI --}}
    <div class="rounded-2xl border border-[#E2EBE9] bg-white p-6 sm:p-8">

        <div class="mb-6">

            <h3 class="text-base font-bold text-[#365F65]">
                Informasi Lokasi
            </h3>

            <p class="mt-1 text-xs text-[#8BA5A2]">
                Lokasi yang digunakan dalam kontrak.
            </p>

        </div>


        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">

            {{-- TERMINAL --}}
            <div>

                <p class="text-xs font-semibold text-[#8BA5A2]">
                    Terminal
                </p>

                <p class="mt-1 text-sm font-semibold text-[#365F65]">
                    -
                </p>

            </div>


            {{-- NOMOR RO --}}
            <div>

                <p class="text-xs font-semibold text-[#8BA5A2]">
                    Nomor RO
                </p>

                <p class="mt-1 text-sm font-semibold text-[#365F65]">
                    -
                </p>

            </div>


            {{-- KODE RUANG --}}
            <div>

                <p class="text-xs font-semibold text-[#8BA5A2]">
                    Kode Ruang
                </p>

                <p class="mt-1 text-sm font-semibold text-[#365F65]">
                    -
                </p>

            </div>


            {{-- LUAS AREA --}}
            <div>

                <p class="text-xs font-semibold text-[#8BA5A2]">
                    Luas Area
                </p>

                <p class="mt-1 text-sm font-semibold text-[#365F65]">
                    -
                </p>

            </div>


            {{-- NAMA / AREA LOKASI --}}
            <div class="md:col-span-2">

                <p class="text-xs font-semibold text-[#8BA5A2]">
                    Nama / Area Lokasi
                </p>

                <p class="mt-1 text-sm font-semibold text-[#365F65]">
                    -
                </p>

            </div>

        </div>

    </div>


    {{-- BERITA ACARA --}}
    <div class="rounded-2xl border border-[#E2EBE9] bg-white p-6 sm:p-8">

        <div class="mb-6">

            <h3 class="text-base font-bold text-[#365F65]">
                Berita Acara
            </h3>

            <p class="mt-1 text-xs text-[#8BA5A2]">
                Dokumen berita acara yang berkaitan dengan kontrak.
            </p>

        </div>


        <div
            class="flex flex-col gap-4 rounded-xl
                   border border-[#DCE6E4]
                   bg-[#F8FAF9]
                   p-4
                   sm:flex-row
                   sm:items-center
                   sm:justify-between"
        >

            <div class="flex items-center gap-3">

                <div
                    class="flex h-11 w-11 items-center justify-center
                           rounded-xl bg-[#E8F0EE]"
                >
                    <span class="text-lg text-[#365F65]">
                        📄
                    </span>
                </div>

                <div>

                    <p class="text-sm font-semibold text-[#365F65]">
                        Berita Acara Kerja Sama
                    </p>

                    <p class="mt-0.5 text-xs text-[#8BA5A2]">
                        Dokumen tersedia
                    </p>

                </div>

            </div>


            <button
                type="button"
                class="rounded-xl
                       border border-[#DCE6E4]
                       bg-white
                       px-4 py-2.5
                       text-xs font-semibold
                       text-[#527D82]
                       hover:bg-[#F5F8F7]
                       transition"
            >
                Lihat Dokumen
            </button>

        </div>

    </div>


    {{-- TANDA TANGAN COMMERCIAL --}}
    <div class="rounded-2xl border border-[#E2EBE9] bg-white p-6 sm:p-8">

        <div class="mb-6">

            <h3 class="text-base font-bold text-[#365F65]">
                Tanda Tangan Commercial
            </h3>

            <p class="mt-1 text-xs leading-5 text-[#8BA5A2]">
                Tanda tangan Commercial yang telah memberikan persetujuan.
            </p>

        </div>


        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">

            {{-- NAMA --}}
            <div>

                <p class="text-xs font-semibold text-[#8BA5A2]">
                    Nama
                </p>

                <p class="mt-1 text-sm font-semibold text-[#365F65]">
                    -
                </p>

            </div>


            {{-- JABATAN --}}
            <div>

                <p class="text-xs font-semibold text-[#8BA5A2]">
                    Jabatan
                </p>

                <p class="mt-1 text-sm font-semibold text-[#365F65]">
                    -
                </p>

            </div>

        </div>


        {{-- SIGNATURE --}}
        <div class="mt-6">

            <p class="mb-1.5 text-sm font-semibold text-[#527D82]">
                Tanda Tangan
            </p>

            <div
                class="flex min-h-[220px]
                       items-center justify-center
                       rounded-xl
                       border border-[#DCE6E4]
                       bg-[#F8FAF9]
                       p-6"
            >

                <div class="text-center">

                    <p class="text-sm font-semibold text-[#527D82]">
                        Tanda tangan Commercial
                    </p>

                    <p class="mt-1 text-xs text-[#8BA5A2]">
                        Tanda tangan yang telah disimpan akan ditampilkan di sini.
                    </p>

                </div>

            </div>

        </div>

    </div>


    {{-- STATUS KONTRAK --}}
    <div class="rounded-2xl border border-[#E2EBE9] bg-white p-6 sm:p-8">

        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

            <div>

                <p class="text-xs font-semibold text-[#8BA5A2]">
                    Status Kontrak
                </p>

                <p class="mt-1 text-sm text-[#527D82]">
                    Status akan mengikuti tanggal akhir kontrak.
                </p>

            </div>


            <span
                class="inline-flex w-fit items-center rounded-full
                       bg-[#E8F0EE]
                       px-4 py-2
                       text-xs font-semibold
                       text-[#365F65]"
            >
                Aktif
            </span>

        </div>

    </div>


    {{-- BACK --}}
    <div>

        <a
            href="{{ route('kontrak') }}"
            class="text-sm font-semibold text-[#527D82]
                   hover:text-[#365F65]"
        >
            ← Kembali ke Data Kontrak
        </a>

    </div>

</div>

@endsection