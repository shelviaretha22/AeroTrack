@extends('layouts.app')

@section('page-title', 'Tambah Lokasi')

@section('content')

<div class="max-w-4xl space-y-6">

    {{-- HEADER --}}

    <div>

        <p class="text-sm text-[#8BA5A2]">
            Management / Lokasi
        </p>

        <h2 class="mt-1 text-2xl font-bold text-[#365F65]">
            Tambah Lokasi
        </h2>

        <p class="mt-1 text-sm text-[#7A9290]">
            Tambahkan data lokasi fisik yang tersedia di area bandara.
        </p>

    </div>


    {{-- FORM --}}

    <div class="rounded-2xl border border-[#E2EBE9] bg-white p-6 sm:p-8">

        <form
            method="POST"
            action="{{ route('lokasi.store') }}"
            class="space-y-6"
        >

            @csrf


            {{-- INFORMASI LOKASI --}}

            <div>

                <h3 class="text-base font-bold text-[#365F65]">
                    Informasi Lokasi
                </h3>

                <p class="mt-1 text-xs text-[#8BA5A2]">
                    Isi informasi mengenai lokasi fisik yang tersedia.
                </p>

            </div>


            {{-- GRID --}}

            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">


                {{-- TERMINAL --}}

                <div>

                    <label
                        for="terminal"
                        class="mb-1.5 block text-sm font-semibold text-[#527D82]"
                    >
                        Terminal <span class="text-red-500">*</span>
                    </label>

                    <select
                        id="terminal"
                        name="terminal"
                        required
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

                        <option value="">
                            Pilih Terminal
                        </option>

                        <option
                            value="T1"
                            {{ old('terminal') == 'T1' ? 'selected' : '' }}
                        >
                            Terminal 1
                        </option>

                        <option
                            value="T2"
                            {{ old('terminal') == 'T2' ? 'selected' : '' }}
                        >
                            Terminal 2
                        </option>

                    </select>

                    @error('terminal')
                        <p class="mt-1 text-xs text-red-500">
                            {{ $message }}
                        </p>
                    @enderror

                </div>


                {{-- NOMOR RO --}}

                <div>

                    <label
                        for="nomor_ro"
                        class="mb-1.5 block text-sm font-semibold text-[#527D82]"
                    >
                        Nomor RO
                    </label>

                    <input
                        type="text"
                        id="nomor_ro"
                        name="nomor_ro"
                        value="{{ old('nomor_ro') }}"
                        placeholder="Contoh: 30001293"
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

                    @error('nomor_ro')
                        <p class="mt-1 text-xs text-red-500">
                            {{ $message }}
                        </p>
                    @enderror

                </div>


                {{-- KODE RUANG --}}

                <div>

                    <label
                        for="kode_ruang"
                        class="mb-1.5 block text-sm font-semibold text-[#527D82]"
                    >
                        Kode Ruang <span class="text-red-500">*</span>
                    </label>

                    <input
                        type="text"
                        id="kode_ruang"
                        name="kode_ruang"
                        value="{{ old('kode_ruang') }}"
                        placeholder="Contoh: EP-02-24"
                        required
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

                    @error('kode_ruang')
                        <p class="mt-1 text-xs text-red-500">
                            {{ $message }}
                        </p>
                    @enderror

                </div>


                {{-- LUAS AREA --}}

                <div>

                    <label
                        for="luas_area"
                        class="mb-1.5 block text-sm font-semibold text-[#527D82]"
                    >
                        Luas Area (m²)
                    </label>

                    <input
                        type="number"
                        id="luas_area"
                        name="luas_area"
                        value="{{ old('luas_area') }}"
                        placeholder="Contoh: 49"
                        min="0"
                        step="0.01"
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

                    @error('luas_area')
                        <p class="mt-1 text-xs text-red-500">
                            {{ $message }}
                        </p>
                    @enderror

                </div>


                {{-- LOKASI --}}

                <div class="md:col-span-2">

                    <label
                        for="lokasi"
                        class="mb-1.5 block text-sm font-semibold text-[#527D82]"
                    >
                        Nama / Area Lokasi <span class="text-red-500">*</span>
                    </label>

                    <input
                        type="text"
                        id="lokasi"
                        name="lokasi"
                        value="{{ old('lokasi') }}"
                        placeholder="Contoh: Area Keberangkatan"
                        required
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

                    @error('lokasi')
                        <p class="mt-1 text-xs text-red-500">
                            {{ $message }}
                        </p>
                    @enderror

                </div>


                {{-- CATATAN --}}

                <div class="md:col-span-2">

                    <label
                        for="catatan"
                        class="mb-1.5 block text-sm font-semibold text-[#527D82]"
                    >
                        Keterangan
                    </label>

                    <textarea
                        id="catatan"
                        name="catatan"
                        rows="4"
                        placeholder="Tambahkan keterangan jika diperlukan..."
                        class="w-full rounded-xl
                               border border-[#DCE6E4]
                               bg-[#F8FAF9]
                               px-4 py-3
                               text-sm text-[#365F65]
                               outline-none
                               resize-none
                               focus:border-[#527D82]
                               focus:ring-2
                               focus:ring-[#527D82]/10"
                    >{{ old('catatan') }}</textarea>

                    @error('catatan')
                        <p class="mt-1 text-xs text-red-500">
                            {{ $message }}
                        </p>
                    @enderror

                </div>

            </div>


            {{-- INFO STATUS --}}

            <div class="rounded-xl border border-[#DCE6E4] bg-[#F3F6F5] p-4">

                <p class="text-sm font-semibold text-[#365F65]">
                    Status awal lokasi
                </p>

                <p class="mt-1 text-xs leading-5 text-[#7A9290]">
                    Lokasi baru akan otomatis memiliki status
                    <strong>Kosong</strong>.
                    Tenant akan dikaitkan kemudian melalui proses
                    Kerja Sama.
                </p>

            </div>


            {{-- BUTTON --}}

            <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">

                <a
                    href="{{ route('lokasi.index') }}"
                    class="inline-flex items-center justify-center rounded-xl
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
                    type="submit"
                    class="inline-flex items-center justify-center rounded-xl
                           bg-[#365F65]
                           px-5 py-3
                           text-sm font-semibold
                           text-white
                           hover:bg-[#294F55]
                           transition"
                >
                    Simpan Lokasi
                </button>

            </div>

        </form>

    </div>

</div>

@endsection