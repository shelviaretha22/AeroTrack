@extends('layouts.app')

@section('page-title', 'Input Data Kerja Sama')

@section('content')

<div class="py-2">
    <div class="max-w-6xl mx-auto">

        {{-- HEADER --}}
        <div class="mb-6">

            <h1 class="text-2xl font-bold text-[#365F65]">
                Input Data Kerja Sama
            </h1>

            <p class="mt-1 text-sm text-[#8BA5A2]">
                Tambahkan data kerja sama dengan mitra usaha.
            </p>

        </div>


        {{-- ERROR VALIDATION --}}
        @if ($errors->any())

            <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4">

                <p class="font-semibold text-red-700 mb-2">
                    Terdapat kesalahan:
                </p>

                <ul class="list-disc list-inside text-sm text-red-600 space-y-1">

                    @foreach ($errors->all() as $error)

                        <li>{{ $error }}</li>

                    @endforeach

                </ul>

            </div>

        @endif


        {{-- FORM --}}
        <form action="{{ route('kerja-sama.store') }}" method="POST">

            @csrf


            {{-- ================================================= --}}
            {{-- INFORMASI MITRA --}}
            {{-- ================================================= --}}

            <div class="bg-white rounded-2xl border border-[#DCE6E4] shadow-sm p-6 mb-6">

                <div class="flex items-center gap-3 mb-6">

                    <div class="w-10 h-10 rounded-xl bg-[#E8F0EF] flex items-center justify-center">

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
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0H5m14 0h2m-2 0h-4m-6 0H3m3 0h4M9 7h6m-6 4h6m-6 4h6"
                            />
                        </svg>

                    </div>

                    <div>

                        <h2 class="text-base font-bold text-[#365F65]">
                            Informasi Mitra
                        </h2>

                        <p class="text-xs text-[#8BA5A2]">
                            Informasi dasar mengenai mitra usaha.
                        </p>

                    </div>

                </div>


                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                    {{-- MITRA USAHA --}}
                    <div>

                        <label
                            for="mitra_usaha"
                            class="block text-sm font-semibold text-[#365F65] mb-2"
                        >
                            Mitra Usaha <span class="text-red-500">*</span>
                        </label>

                        <input
                            type="text"
                            id="mitra_usaha"
                            name="mitra_usaha"
                            value="{{ old('mitra_usaha') }}"
                            placeholder="Contoh: PT ABC Indonesia"
                            required
                            class="w-full rounded-xl border border-[#DCE6E4]
                                   bg-[#F9FBFA]
                                   px-4 py-3
                                   text-sm text-[#365F65]
                                   placeholder:text-[#A7B9B7]
                                   focus:border-[#365F65]
                                   focus:ring-2 focus:ring-[#B8CECA]/50
                                   outline-none transition"
                        >

                    </div>


                    {{-- BRAND --}}
                    <div>

                        <label
                            for="brand"
                            class="block text-sm font-semibold text-[#365F65] mb-2"
                        >
                            Brand
                        </label>

                        <input
                            type="text"
                            id="brand"
                            name="brand"
                            value="{{ old('brand') }}"
                            placeholder="Contoh: ABC Coffee"
                            class="w-full rounded-xl border border-[#DCE6E4]
                                   bg-[#F9FBFA]
                                   px-4 py-3
                                   text-sm text-[#365F65]
                                   placeholder:text-[#A7B9B7]
                                   focus:border-[#365F65]
                                   focus:ring-2 focus:ring-[#B8CECA]/50
                                   outline-none transition"
                        >

                    </div>


                    {{-- JENIS USAHA --}}
                    <div>

                        <label
                            for="jenis_usaha"
                            class="block text-sm font-semibold text-[#365F65] mb-2"
                        >
                            Jenis Usaha <span class="text-red-500">*</span>
                        </label>

                        <input
                            type="text"
                            id="jenis_usaha"
                            name="jenis_usaha"
                            value="{{ old('jenis_usaha') }}"
                            placeholder="Contoh: F&B"
                            required
                            class="w-full rounded-xl border border-[#DCE6E4]
                                   bg-[#F9FBFA]
                                   px-4 py-3
                                   text-sm text-[#365F65]
                                   placeholder:text-[#A7B9B7]
                                   focus:border-[#365F65]
                                   focus:ring-2 focus:ring-[#B8CECA]/50
                                   outline-none transition"
                        >

                    </div>


                    {{-- BENTUK KERJA SAMA --}}
                    <div>

                        <label
                            for="bentuk_kerja_sama"
                            class="block text-sm font-semibold text-[#365F65] mb-2"
                        >
                            Bentuk Kerja Sama <span class="text-red-500">*</span>
                        </label>

                        <select
                            id="bentuk_kerja_sama"
                            name="bentuk_kerja_sama"
                            required
                            class="w-full rounded-xl border border-[#DCE6E4]
                                   bg-[#F9FBFA]
                                   px-4 py-3
                                   text-sm text-[#365F65]
                                   focus:border-[#365F65]
                                   focus:ring-2 focus:ring-[#B8CECA]/50
                                   outline-none transition"
                        >

                            <option value="">
                                Pilih bentuk kerja sama
                            </option>

                            <option
                                value="Sewa"
                                {{ old('bentuk_kerja_sama') == 'Sewa' ? 'selected' : '' }}
                            >
                                Sewa
                            </option>

                            <option
                                value="Bagi Hasil"
                                {{ old('bentuk_kerja_sama') == 'Bagi Hasil' ? 'selected' : '' }}
                            >
                                Bagi Hasil
                            </option>

                            <option
                                value="Kemitraan"
                                {{ old('bentuk_kerja_sama') == 'Kemitraan' ? 'selected' : '' }}
                            >
                                Kemitraan
                            </option>

                            <option
                                value="Lainnya"
                                {{ old('bentuk_kerja_sama') == 'Lainnya' ? 'selected' : '' }}
                            >
                                Lainnya
                            </option>

                        </select>

                    </div>

                </div>

            </div>


            {{-- ================================================= --}}
            {{-- INFORMASI PROSES KERJA SAMA --}}
            {{-- ================================================= --}}

            <div class="bg-white rounded-2xl border border-[#DCE6E4] shadow-sm p-6 mb-6">

                <div class="flex items-center gap-3 mb-6">

                    <div class="w-10 h-10 rounded-xl bg-[#E8F0EF] flex items-center justify-center">

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
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                            />
                        </svg>

                    </div>

                    <div>

                        <h2 class="text-base font-bold text-[#365F65]">
                            Informasi Proses Kerja Sama
                        </h2>

                        <p class="text-xs text-[#8BA5A2]">
                            Atur status dan periode kerja sama.
                        </p>

                    </div>

                </div>


                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                    {{-- TANGGAL PENGAJUAN --}}
                    <div>

                        <label
                            for="tanggal_pengajuan"
                            class="block text-sm font-semibold text-[#365F65] mb-2"
                        >
                            Tanggal Pengajuan <span class="text-red-500">*</span>
                        </label>

                        <input
                            type="date"
                            id="tanggal_pengajuan"
                            name="tanggal_pengajuan"
                            value="{{ old('tanggal_pengajuan', now()->format('Y-m-d')) }}"
                            required
                            class="w-full rounded-xl border border-[#DCE6E4]
                                   bg-[#F9FBFA]
                                   px-4 py-3
                                   text-sm text-[#365F65]
                                   focus:border-[#365F65]
                                   focus:ring-2 focus:ring-[#B8CECA]/50
                                   outline-none transition"
                        >

                    </div>


                    {{-- STATUS --}}
                    <div>

                        <label
                            for="status"
                            class="block text-sm font-semibold text-[#365F65] mb-2"
                        >
                            Status Kerja Sama <span class="text-red-500">*</span>
                        </label>

                        <select
                            id="status"
                            name="status"
                            required
                            class="w-full rounded-xl border border-[#DCE6E4]
                                   bg-[#F9FBFA]
                                   px-4 py-3
                                   text-sm text-[#365F65]
                                   focus:border-[#365F65]
                                   focus:ring-2 focus:ring-[#B8CECA]/50
                                   outline-none transition"
                        >

                            <option
                                value="Draft"
                                {{ old('status', 'Draft') == 'Draft' ? 'selected' : '' }}
                            >
                                Draft
                            </option>

                            <option
                                value="Proses"
                                {{ old('status') == 'Proses' ? 'selected' : '' }}
                            >
                                Proses
                            </option>

                            <option
                                value="Disetujui"
                                {{ old('status') == 'Disetujui' ? 'selected' : '' }}
                            >
                                Disetujui
                            </option>

                            <option
                                value="Ditolak"
                                {{ old('status') == 'Ditolak' ? 'selected' : '' }}
                            >
                                Ditolak
                            </option>

                        </select>

                    </div>


                    {{-- TANGGAL MULAI --}}
                    <div>

                        <label
                            for="tanggal_mulai"
                            class="block text-sm font-semibold text-[#365F65] mb-2"
                        >
                            Tanggal Mulai Kerja Sama
                        </label>

                        <input
                            type="date"
                            id="tanggal_mulai"
                            name="tanggal_mulai"
                            value="{{ old('tanggal_mulai') }}"
                            class="w-full rounded-xl border border-[#DCE6E4]
                                   bg-[#F9FBFA]
                                   px-4 py-3
                                   text-sm text-[#365F65]
                                   focus:border-[#365F65]
                                   focus:ring-2 focus:ring-[#B8CECA]/50
                                   outline-none transition"
                        >

                    </div>


                    {{-- TANGGAL BERAKHIR --}}
                    <div>

                        <label
                            for="tanggal_berakhir"
                            class="block text-sm font-semibold text-[#365F65] mb-2"
                        >
                            Tanggal Berakhir Kerja Sama
                        </label>

                        <input
                            type="date"
                            id="tanggal_berakhir"
                            name="tanggal_berakhir"
                            value="{{ old('tanggal_berakhir') }}"
                            class="w-full rounded-xl border border-[#DCE6E4]
                                   bg-[#F9FBFA]
                                   px-4 py-3
                                   text-sm text-[#365F65]
                                   focus:border-[#365F65]
                                   focus:ring-2 focus:ring-[#B8CECA]/50
                                   outline-none transition"
                        >

                    </div>

                </div>

            </div>


            {{-- ================================================= --}}
            {{-- INFORMASI PENGAJUAN --}}
            {{-- ================================================= --}}

            <div class="bg-white rounded-2xl border border-[#DCE6E4] shadow-sm p-6 mb-6">

                <div class="flex items-center gap-3 mb-6">

                    <div class="w-10 h-10 rounded-xl bg-[#E8F0EF] flex items-center justify-center">

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
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                            />
                        </svg>

                    </div>

                    <div>

                        <h2 class="text-base font-bold text-[#365F65]">
                            Informasi Pengajuan
                        </h2>

                        <p class="text-xs text-[#8BA5A2]">
                            Informasi PIC dan keterangan pengajuan.
                        </p>

                    </div>

                </div>


                <div class="space-y-5">

                    {{-- PIC COMMERCIAL --}}
                    <div>

                        <label
                            for="pic_commercial"
                            class="block text-sm font-semibold text-[#365F65] mb-2"
                        >
                            PIC dari Pihak Commercial <span class="text-red-500">*</span>
                        </label>

                        <input
                            type="text"
                            id="pic_commercial"
                            name="pic_commercial"
                            value="{{ old('pic_commercial') }}"
                            placeholder="Contoh: Andi Pratama"
                            required
                            class="w-full rounded-xl border border-[#DCE6E4]
                                   bg-[#F9FBFA]
                                   px-4 py-3
                                   text-sm text-[#365F65]
                                   placeholder:text-[#A7B9B7]
                                   focus:border-[#365F65]
                                   focus:ring-2 focus:ring-[#B8CECA]/50
                                   outline-none transition"
                        >

                    </div>


                    {{-- CATATAN --}}
                    <div>

                        <label
                            for="catatan"
                            class="block text-sm font-semibold text-[#365F65] mb-2"
                        >
                            Catatan / Keterangan
                        </label>

                        <textarea
                            id="catatan"
                            name="catatan"
                            rows="4"
                            placeholder="Contoh: Pengajuan tenant untuk area komersial"
                            class="w-full rounded-xl border border-[#DCE6E4]
                                   bg-[#F9FBFA]
                                   px-4 py-3
                                   text-sm text-[#365F65]
                                   placeholder:text-[#A7B9B7]
                                   focus:border-[#365F65]
                                   focus:ring-2 focus:ring-[#B8CECA]/50
                                   outline-none transition"
                        >{{ old('catatan') }}</textarea>

                    </div>

                </div>

            </div>


            {{-- ================================================= --}}
            {{-- BUTTON --}}
            {{-- ================================================= --}}

            <div class="flex items-center justify-end gap-3">

                <a
                    href="{{ route('kerja-sama') }}"
                    class="px-5 py-3 rounded-xl
                           border border-[#DCE6E4]
                           bg-white
                           text-[#365F65]
                           text-sm font-semibold
                           hover:bg-[#F3F6F5]
                           transition"
                >
                    Batal
                </a>

                <button
                    type="submit"
                    class="px-6 py-3 rounded-xl
                           bg-[#365F65]
                           text-white
                           text-sm font-semibold
                           hover:bg-[#2D5055]
                           shadow-sm
                           transition"
                >
                    Simpan Data
                </button>

            </div>

        </form>

    </div>
</div>

@endsection