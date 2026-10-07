@extends('layouts.app')

@section('page-title', 'Edit Data Kerja Sama')

@section('content')

<div class="max-w-5xl mx-auto">

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">

        <div>
            <h1 class="text-2xl font-bold text-[#365F65]">
                Edit Data Kerja Sama
            </h1>

            <p class="text-sm text-[#8BA5A2] mt-1">
                Perbarui informasi kerja sama yang sudah tersimpan.
            </p>
        </div>

        <a href="{{ route('kerja-sama.show', $kerjaSama->id) }}"
           class="inline-flex items-center justify-center gap-2
                  px-4 py-2.5
                  rounded-xl
                  border border-[#DCE6E4]
                  bg-white
                  text-sm font-semibold
                  text-[#365F65]
                  hover:bg-[#F3F6F5]
                  transition">

            ← Kembali
        </a>

    </div>


    {{-- VALIDATION ERROR --}}
    @if ($errors->any())

        <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-4">

            <div class="flex items-start gap-3">

                <div class="text-red-500 text-lg">
                    ⚠
                </div>

                <div>
                    <p class="font-semibold text-red-700">
                        Data belum dapat diperbarui.
                    </p>

                    <ul class="mt-2 text-sm text-red-600 list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>

            </div>

        </div>

    @endif


    {{-- FORM --}}
    <form action="{{ route('kerja-sama.update', $kerjaSama->id) }}"
          method="POST">

        @csrf
        @method('PUT')


        {{-- INFORMASI MITRA --}}
        <div class="bg-white rounded-2xl border border-[#DCE6E4] shadow-sm p-6 mb-6">

            <div class="mb-6">
                <h2 class="text-lg font-bold text-[#365F65]">
                    Informasi Mitra
                </h2>

                <p class="text-sm text-[#8BA5A2] mt-1">
                    Informasi dasar mengenai mitra usaha.
                </p>
            </div>


            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                {{-- Mitra Usaha --}}
                <div>
                    <label class="block text-sm font-semibold text-[#365F65] mb-2">
                        Mitra Usaha <span class="text-red-500">*</span>
                    </label>

                    <input
                        type="text"
                        name="mitra_usaha"
                        value="{{ old('mitra_usaha', $kerjaSama->mitra_usaha) }}"
                        placeholder="Contoh: PT ABC Indonesia"
                        required
                        class="w-full rounded-xl border border-[#DCE6E4]
                               px-4 py-3 text-sm
                               text-[#365F65]
                               focus:border-[#8BA5A2]
                               focus:ring-2 focus:ring-[#B8CECA]/50
                               outline-none transition"
                    >
                </div>


                {{-- Brand --}}
                <div>
                    <label class="block text-sm font-semibold text-[#365F65] mb-2">
                        Brand
                    </label>

                    <input
                        type="text"
                        name="brand"
                        value="{{ old('brand', $kerjaSama->brand) }}"
                        placeholder="Contoh: Starbucks"
                        class="w-full rounded-xl border border-[#DCE6E4]
                               px-4 py-3 text-sm
                               text-[#365F65]
                               focus:border-[#8BA5A2]
                               focus:ring-2 focus:ring-[#B8CECA]/50
                               outline-none transition"
                    >
                </div>


                {{-- Jenis Usaha --}}
                <div>
                    <label class="block text-sm font-semibold text-[#365F65] mb-2">
                        Jenis Usaha <span class="text-red-500">*</span>
                    </label>

                    <input
                        type="text"
                        name="jenis_usaha"
                        value="{{ old('jenis_usaha', $kerjaSama->jenis_usaha) }}"
                        placeholder="Contoh: Food & Beverage"
                        required
                        class="w-full rounded-xl border border-[#DCE6E4]
                               px-4 py-3 text-sm
                               text-[#365F65]
                               focus:border-[#8BA5A2]
                               focus:ring-2 focus:ring-[#B8CECA]/50
                               outline-none transition"
                    >
                </div>


                {{-- Bentuk Kerja Sama --}}
                <div>
                    <label class="block text-sm font-semibold text-[#365F65] mb-2">
                        Bentuk Kerja Sama <span class="text-red-500">*</span>
                    </label>

                    <select
                        name="bentuk_kerja_sama"
                        required
                        class="w-full rounded-xl border border-[#DCE6E4]
                               px-4 py-3 text-sm
                               text-[#365F65]
                               bg-white
                               focus:border-[#8BA5A2]
                               focus:ring-2 focus:ring-[#B8CECA]/50
                               outline-none transition"
                    >

                        <option value="">Pilih bentuk kerja sama</option>

                        @foreach ([
                            'Sewa',
                            'Bagi Hasil',
                            'Kemitraan',
                            'Lainnya'
                        ] as $bentuk)

                            <option
                                value="{{ $bentuk }}"
                                {{ old('bentuk_kerja_sama', $kerjaSama->bentuk_kerja_sama) == $bentuk ? 'selected' : '' }}
                            >
                                {{ $bentuk }}
                            </option>

                        @endforeach

                    </select>

                </div>

            </div>

        </div>


        {{-- INFORMASI PROSES --}}
        <div class="bg-white rounded-2xl border border-[#DCE6E4] shadow-sm p-6 mb-6">

            <div class="mb-6">
                <h2 class="text-lg font-bold text-[#365F65]">
                    Informasi Proses Kerja Sama
                </h2>

                <p class="text-sm text-[#8BA5A2] mt-1">
                    Informasi mengenai pengajuan dan periode kerja sama.
                </p>
            </div>


            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                {{-- Tanggal Pengajuan --}}
                <div>
                    <label class="block text-sm font-semibold text-[#365F65] mb-2">
                        Tanggal Pengajuan <span class="text-red-500">*</span>
                    </label>

                    <input
                        type="date"
                        name="tanggal_pengajuan"
                        value="{{ old('tanggal_pengajuan', optional($kerjaSama->tanggal_pengajuan)->format('Y-m-d')) }}"
                        required
                        class="w-full rounded-xl border border-[#DCE6E4]
                               px-4 py-3 text-sm
                               text-[#365F65]
                               focus:border-[#8BA5A2]
                               focus:ring-2 focus:ring-[#B8CECA]/50
                               outline-none transition"
                    >
                </div>


                {{-- Status --}}
                <div>
                    <label class="block text-sm font-semibold text-[#365F65] mb-2">
                        Status <span class="text-red-500">*</span>
                    </label>

                    <select
                        name="status"
                        required
                        class="w-full rounded-xl border border-[#DCE6E4]
                               px-4 py-3 text-sm
                               text-[#365F65]
                               bg-white
                               focus:border-[#8BA5A2]
                               focus:ring-2 focus:ring-[#B8CECA]/50
                               outline-none transition"
                    >

                        @foreach ([
                            'Draft',
                            'Proses',
                            'Disetujui',
                            'Ditolak'
                        ] as $status)

                            <option
                                value="{{ $status }}"
                                {{ old('status', $kerjaSama->status) == $status ? 'selected' : '' }}
                            >
                                {{ $status }}
                            </option>

                        @endforeach

                    </select>

                </div>


                {{-- Tanggal Mulai --}}
                <div>
                    <label class="block text-sm font-semibold text-[#365F65] mb-2">
                        Tanggal Mulai
                    </label>

                    <input
                        type="date"
                        name="tanggal_mulai"
                        value="{{ old('tanggal_mulai', optional($kerjaSama->tanggal_mulai)->format('Y-m-d')) }}"
                        class="w-full rounded-xl border border-[#DCE6E4]
                               px-4 py-3 text-sm
                               text-[#365F65]
                               focus:border-[#8BA5A2]
                               focus:ring-2 focus:ring-[#B8CECA]/50
                               outline-none transition"
                    >
                </div>


                {{-- Tanggal Berakhir --}}
                <div>
                    <label class="block text-sm font-semibold text-[#365F65] mb-2">
                        Tanggal Berakhir
                    </label>

                    <input
                        type="date"
                        name="tanggal_berakhir"
                        value="{{ old('tanggal_berakhir', optional($kerjaSama->tanggal_berakhir)->format('Y-m-d')) }}"
                        class="w-full rounded-xl border border-[#DCE6E4]
                               px-4 py-3 text-sm
                               text-[#365F65]
                               focus:border-[#8BA5A2]
                               focus:ring-2 focus:ring-[#B8CECA]/50
                               outline-none transition"
                    >
                </div>

            </div>

        </div>


        {{-- INFORMASI PIC --}}
        <div class="bg-white rounded-2xl border border-[#DCE6E4] shadow-sm p-6 mb-6">

            <div class="mb-6">
                <h2 class="text-lg font-bold text-[#365F65]">
                    Informasi PIC
                </h2>

                <p class="text-sm text-[#8BA5A2] mt-1">
                    Informasi penanggung jawab dari sisi Commercial.
                </p>
            </div>


            <div class="space-y-5">

                {{-- PIC --}}
                <div>
                    <label class="block text-sm font-semibold text-[#365F65] mb-2">
                        PIC Commercial <span class="text-red-500">*</span>
                    </label>

                    <input
                        type="text"
                        name="pic_commercial"
                        value="{{ old('pic_commercial', $kerjaSama->pic_commercial) }}"
                        placeholder="Nama PIC Commercial"
                        required
                        class="w-full rounded-xl border border-[#DCE6E4]
                               px-4 py-3 text-sm
                               text-[#365F65]
                               focus:border-[#8BA5A2]
                               focus:ring-2 focus:ring-[#B8CECA]/50
                               outline-none transition"
                    >
                </div>


                {{-- Catatan --}}
                <div>
                    <label class="block text-sm font-semibold text-[#365F65] mb-2">
                        Catatan
                    </label>

                    <textarea
                        name="catatan"
                        rows="4"
                        placeholder="Tambahkan catatan jika diperlukan..."
                        class="w-full rounded-xl border border-[#DCE6E4]
                               px-4 py-3 text-sm
                               text-[#365F65]
                               focus:border-[#8BA5A2]
                               focus:ring-2 focus:ring-[#B8CECA]/50
                               outline-none transition"
                    >{{ old('catatan', $kerjaSama->catatan) }}</textarea>
                </div>

            </div>

        </div>


        {{-- BUTTON --}}
        <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3">

            <a
                href="{{ route('kerja-sama.show', $kerjaSama->id) }}"
                class="inline-flex items-center justify-center
                       px-5 py-3
                       rounded-xl
                       border border-[#DCE6E4]
                       bg-white
                       text-sm font-semibold
                       text-[#365F65]
                       hover:bg-[#F3F6F5]
                       transition"
            >
                Batal
            </a>


            <button
                type="submit"
                class="inline-flex items-center justify-center
                       px-5 py-3
                       rounded-xl
                       bg-[#365F65]
                       text-white
                       text-sm font-semibold
                       hover:bg-[#2D5055]
                       transition
                       shadow-sm"
            >
                Simpan Perubahan
            </button>

        </div>

    </form>

</div>

@endsection