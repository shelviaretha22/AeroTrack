@extends('layouts.app')

@section('page-title', 'Beri ACC Kontrak')

@section('content')

<div class="max-w-5xl space-y-6">

    {{-- HEADER --}}
    <div>

        <p class="text-sm text-[#8BA5A2]">
            Management / Kontrak / Beri ACC
        </p>

        <h2 class="mt-1 text-2xl font-bold text-[#365F65]">
            Beri ACC Kontrak
        </h2>

        <p class="mt-1 text-sm text-[#7A9290]">
            Periksa informasi kerja sama, lokasi, dan berita acara sebelum memberikan persetujuan.
        </p>

    </div>


    {{-- INFORMASI KERJA SAMA --}}
    <div class="rounded-2xl border border-[#E2EBE9] bg-white p-6 sm:p-8">

        <div class="mb-6">

            <h3 class="text-base font-bold text-[#365F65]">
                Informasi Kerja Sama
            </h3>

            <p class="mt-1 text-xs text-[#8BA5A2]">
                Informasi ini berasal dari data Kerja Sama.
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
                Informasi lokasi yang berkaitan dengan kerja sama.
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


            {{-- LUAS --}}
            <div>
                <p class="text-xs font-semibold text-[#8BA5A2]">
                    Luas Area
                </p>

                <p class="mt-1 text-sm font-semibold text-[#365F65]">
                    -
                </p>
            </div>


            {{-- LOKASI --}}
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
                Berita Acara yang telah dibuat pada proses kerja sama.
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
                        Belum ada dokumen
                    </p>

                </div>

            </div>


            <button
                type="button"
                disabled
                class="rounded-xl
                       border border-[#DCE6E4]
                       bg-white
                       px-4 py-2.5
                       text-xs font-semibold
                       text-[#9AAEAB]
                       cursor-not-allowed"
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
                Masukkan identitas Commercial dan tanda tangan sebagai persetujuan kontrak.
            </p>

        </div>


        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">

            {{-- NAMA --}}
            <div>

                <label
                    for="nama_commercial"
                    class="mb-1.5 block text-sm font-semibold text-[#527D82]"
                >
                    Nama
                    <span class="text-red-500">*</span>
                </label>

                <input
                    type="text"
                    id="nama_commercial"
                    name="nama_commercial"
                    placeholder="Masukkan nama Commercial"
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


            {{-- JABATAN --}}
            <div>

                <label
                    for="jabatan_commercial"
                    class="mb-1.5 block text-sm font-semibold text-[#527D82]"
                >
                    Jabatan
                    <span class="text-red-500">*</span>
                </label>

                <input
                    type="text"
                    id="jabatan_commercial"
                    name="jabatan_commercial"
                    placeholder="Masukkan jabatan Commercial"
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

        </div>


        {{-- SIGNATURE --}}
        <div class="mt-6">

            <label
                class="mb-1.5 block text-sm font-semibold text-[#527D82]"
            >
                Tanda Tangan
                <span class="text-red-500">*</span>
            </label>


            <div
                class="flex min-h-[220px]
                       items-center justify-center
                       rounded-xl
                       border-2 border-dashed border-[#CFE0DC]
                       bg-[#F8FAF9]
                       p-6"
            >

                <div class="text-center">

                    <p class="text-sm font-semibold text-[#527D82]">
                        Belum ada tanda tangan
                    </p>

                    <p class="mt-1 text-xs text-[#8BA5A2]">
                        Upload tanda tangan dalam format JPG.
                    </p>

                </div>

            </div>


            {{-- UPLOAD & RESET --}}
            <div class="mt-3 flex flex-wrap gap-3">

                <label
                    for="signature"
                    class="inline-flex cursor-pointer
                           items-center justify-center
                           rounded-xl
                           border border-[#DCE6E4]
                           bg-white
                           px-4 py-2.5
                           text-xs font-semibold
                           text-[#527D82]
                           hover:bg-[#F5F8F7]
                           transition"
                >
                    Upload JPG
                </label>

                <input
                    type="file"
                    id="signature"
                    name="signature"
                    accept=".jpg,.jpeg,image/jpeg"
                    class="hidden"
                >


                <button
                    type="button"
                    class="rounded-xl
                           border border-[#DCE6E4]
                           bg-white
                           px-4 py-2.5
                           text-xs font-semibold
                           text-[#7A9290]
                           hover:bg-[#F5F8F7]
                           transition"
                >
                    Reset
                </button>

            </div>

        </div>

    </div>


    {{-- ACTION --}}
    <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">

        <a
            href="{{ route('kontrak') }}"
            class="inline-flex items-center justify-center
                   rounded-xl
                   border border-[#DCE6E4]
                   bg-white
                   px-5 py-3
                   text-sm font-semibold
                   text-[#527D82]
                   hover:bg-[#F5F8F7]
                   transition"
        >
            Batal
        </a>


        <button
            type="button"
            class="inline-flex items-center justify-center
                   rounded-xl
                   bg-[#365F65]
                   px-5 py-3
                   text-sm font-semibold
                   text-white
                   hover:bg-[#294F55]
                   transition"
        >
            ACC Kontrak
        </button>

    </div>

</div>

@endsection