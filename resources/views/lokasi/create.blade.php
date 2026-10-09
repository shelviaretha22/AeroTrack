@extends('layouts.app')

@section('page-title', 'Tambah Lokasi')

@section('content')
<div class="w-full max-w-none space-y-6">

    {{-- Header --}}
    <div>
        <p class="text-sm text-[#8BA5A2]">Management / Lokasi</p>

        <h1 class="mt-1 text-2xl font-bold text-[#365F65]">
            Tambah Lokasi Baru
        </h1>

        <p class="mt-1 text-sm text-[#7A9290]">
            Daftarkan ruangan fisik yang tersedia di area bandara.
        </p>
    </div>

    {{-- Pesan validasi --}}
    @if ($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 p-4">
            <p class="mb-2 font-semibold text-red-700">
                Periksa kembali data yang kamu isi.
            </p>

            @foreach ($errors->all() as $error)
                <p class="text-sm text-red-600">{{ $error }}</p>
            @endforeach
        </div>
    @endif

    {{-- Form --}}
    <div class="w-full rounded-2xl border border-[#E2EBE9] bg-white p-5 sm:p-8 lg:p-10">

        <form
            method="POST"
            action="{{ route('lokasi.store') }}"
            class="space-y-6"
        >
            @csrf

            <div>
                <h2 class="text-base font-bold text-[#365F65]">
                    Informasi Ruangan
                </h2>

                <p class="mt-1 text-xs text-[#8BA5A2]">
                    Kode lokasi dibuat otomatis oleh sistem.
                </p>
            </div>

            <div class="grid grid-cols-1 gap-x-6 gap-y-5 md:grid-cols-2">

                {{-- Terminal --}}
                <div>
                    <label for="terminal"
                           class="mb-1.5 block text-sm font-semibold text-[#527D82]">
                        Terminal <span class="text-red-500">*</span>
                    </label>

                    <select
                        id="terminal"
                        name="terminal"
                        required
                        class="w-full rounded-xl border border-[#DCE6E4] bg-[#F8FAF9] px-4 py-3 text-sm text-[#365F65] outline-none focus:border-[#527D82] focus:ring-2 focus:ring-[#527D82]/10"
                    >
                        <option value="">Pilih terminal</option>
                        <option value="T1" {{ old('terminal') == 'T1' ? 'selected' : '' }}>
                            Terminal 1
                        </option>
                        <option value="T2" {{ old('terminal') == 'T2' ? 'selected' : '' }}>
                            Terminal 2
                        </option>
                    </select>

                    @error('terminal')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Lantai --}}
                <div>
                    <label for="lantai"
                           class="mb-1.5 block text-sm font-semibold text-[#527D82]">
                        Lantai <span class="text-red-500">*</span>
                    </label>

                    <select
                        id="lantai"
                        name="lantai"
                        required
                        class="w-full rounded-xl border border-[#DCE6E4] bg-[#F8FAF9] px-4 py-3 text-sm text-[#365F65] outline-none focus:border-[#527D82] focus:ring-2 focus:ring-[#527D82]/10"
                    >
                        <option value="">Pilih lantai</option>

                        @foreach (['1', '2', '3', '4'] as $lantai)
                            <option value="{{ $lantai }}" {{ old('lantai') == $lantai ? 'selected' : '' }}>
                                Lantai {{ $lantai }}
                            </option>
                        @endforeach
                    </select>

                    @error('lantai')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Jenis ruangan --}}
                <div>
                    <label for="jenis_ruangan"
                           class="mb-1.5 block text-sm font-semibold text-[#527D82]">
                        Jenis Ruangan <span class="text-red-500">*</span>
                    </label>

                    <select
                        id="jenis_ruangan"
                        name="jenis_ruangan"
                        required
                        class="w-full rounded-xl border border-[#DCE6E4] bg-[#F8FAF9] px-4 py-3 text-sm text-[#365F65] outline-none focus:border-[#527D82] focus:ring-2 focus:ring-[#527D82]/10"
                    >
                        <option value="">Pilih jenis ruangan</option>

                        @foreach ([
                            'Store',
                            'Counter',
                            'Customer Service',
                            'Lounge',
                            'Antena',
                            'Gudang',
                            'Lainnya'
                        ] as $jenis)
                            <option value="{{ $jenis }}" {{ old('jenis_ruangan') == $jenis ? 'selected' : '' }}>
                                {{ $jenis }}
                            </option>
                        @endforeach
                    </select>

                    @error('jenis_ruangan')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Nomor RO --}}
                <div>
                    <label for="nomor_ro"
                           class="mb-1.5 block text-sm font-semibold text-[#527D82]">
                        Nomor RO
                    </label>

                    <input
                        type="text"
                        id="nomor_ro"
                        name="nomor_ro"
                        value="{{ old('nomor_ro') }}"
                        placeholder="Masukkan nomor RO jika ada"
                        class="w-full rounded-xl border border-[#DCE6E4] bg-[#F8FAF9] px-4 py-3 text-sm text-[#365F65] outline-none focus:border-[#527D82] focus:ring-2 focus:ring-[#527D82]/10"
                    >

                    @error('nomor_ro')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Kode lokasi otomatis --}}
                <div class="md:col-span-2">
                    <label for="kode_preview"
                           class="mb-1.5 block text-sm font-semibold text-[#527D82]">
                        Kode Lokasi
                    </label>

                    <div class="rounded-xl border border-[#DCE6E4] bg-[#EAF1F0] px-4 py-4">
                        <p id="kode_preview"
                           class="break-words text-lg font-bold text-[#365F65]">
                            Pilih terminal dan lantai
                        </p>

                        <p class="mt-1 text-xs text-[#7A9290]">
                            Nomor ruang ditentukan oleh sistem ketika data disimpan.
                        </p>
                    </div>
                </div>

                {{-- Nama / area lokasi --}}
                <div class="md:col-span-2">
                    <label for="lokasi"
                           class="mb-1.5 block text-sm font-semibold text-[#527D82]">
                        Nama / Area Lokasi <span class="text-red-500">*</span>
                    </label>

                    <input
                        type="text"
                        id="lokasi"
                        name="lokasi"
                        value="{{ old('lokasi') }}"
                        required
                        maxlength="255"
                        placeholder="Contoh: Area Keberangkatan"
                        class="w-full rounded-xl border border-[#DCE6E4] bg-[#F8FAF9] px-4 py-3 text-sm text-[#365F65] outline-none focus:border-[#527D82] focus:ring-2 focus:ring-[#527D82]/10"
                    >

                    @error('lokasi')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Luas area --}}
                <div>
                    <label for="luas_area"
                           class="mb-1.5 block text-sm font-semibold text-[#527D82]">
                        Luas Area (m²)
                    </label>

                    <input
                        type="number"
                        id="luas_area"
                        name="luas_area"
                        value="{{ old('luas_area') }}"
                        min="0"
                        step="0.01"
                        placeholder="Contoh: 20"
                        class="w-full rounded-xl border border-[#DCE6E4] bg-[#F8FAF9] px-4 py-3 text-sm text-[#365F65] outline-none focus:border-[#527D82] focus:ring-2 focus:ring-[#527D82]/10"
                    >

                    @error('luas_area')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Status --}}
                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-[#527D82]">
                        Status Awal
                    </label>

                    <div class="flex items-center gap-2 rounded-xl border border-[#DCE6E4] bg-[#F8FAF9] px-4 py-3">
                        <span class="h-2.5 w-2.5 rounded-full bg-emerald-500"></span>
                        <span class="text-sm font-semibold text-[#365F65]">
                            Kosong
                        </span>
                    </div>

                    <p class="mt-1 text-xs text-[#8BA5A2]">
                        Berubah menjadi Terisi ketika dihubungkan dengan tenant.
                    </p>
                </div>

                {{-- Catatan --}}
                <div class="md:col-span-2">
                    <label for="catatan"
                           class="mb-1.5 block text-sm font-semibold text-[#527D82]">
                        Keterangan
                    </label>

                    <textarea
                        id="catatan"
                        name="catatan"
                        rows="4"
                        placeholder="Tambahkan informasi lokasi jika diperlukan..."
                        class="w-full resize-y rounded-xl border border-[#DCE6E4] bg-[#F8FAF9] px-4 py-3 text-sm text-[#365F65] outline-none focus:border-[#527D82] focus:ring-2 focus:ring-[#527D82]/10"
                    >{{ old('catatan') }}</textarea>

                    @error('catatan')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Tombol --}}
            <div class="flex flex-col-reverse gap-3 border-t border-[#E2EBE9] pt-5 sm:flex-row sm:justify-end">

                <a
                    href="{{ route('lokasi.index') }}"
                    class="inline-flex items-center justify-center rounded-xl border border-[#DCE6E4] bg-white px-5 py-3 text-sm font-semibold text-[#527D82] transition hover:bg-[#F5F8F7]"
                >
                    Batal
                </a>

                <button
                    type="submit"
                    class="inline-flex items-center justify-center rounded-xl bg-[#365F65] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#294F55] focus:outline-none focus:ring-2 focus:ring-[#527D82] focus:ring-offset-2"
                >
                    Simpan Lokasi
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Pratinjau kode --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const terminal = document.getElementById('terminal');
        const lantai = document.getElementById('lantai');
        const preview = document.getElementById('kode_preview');

        function updateKodePreview() {
            if (terminal.value && lantai.value) {
                preview.textContent = `${terminal.value}-L${lantai.value}-XXX`;
            } else {
                preview.textContent = 'Pilih terminal dan lantai';
            }
        }

        terminal.addEventListener('change', updateKodePreview);
        lantai.addEventListener('change', updateKodePreview);

        updateKodePreview();
    });
</script>
@endsection