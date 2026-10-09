@extends('layouts.app')

@section('page-title', 'Edit Lokasi')

@section('content')
<div class="w-full max-w-none space-y-6">

    {{-- Header --}}
    <div>
        <p class="text-sm text-[#8BA5A2]">Management / Lokasi / Edit</p>

        <h1 class="mt-1 text-2xl font-bold text-[#365F65]">
            Edit Lokasi
        </h1>

        <p class="mt-1 text-sm text-[#7A9290]">
            Perbarui informasi lokasi tenant bandara.
        </p>
    </div>

    {{-- Informasi kode dan status --}}
    <div class="w-full rounded-2xl border border-[#E2EBE9] bg-white p-6 sm:p-8">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-sm text-[#8BA5A2]">Kode Ruang Asli</p>

                <h2 class="mt-1 text-xl font-bold text-[#365F65]">
                    {{ $lokasi->kode_ruang }}
                </h2>

                <p class="mt-1 text-xs text-[#7A9290]">
                    Kode ruang tidak berubah ketika data lokasi diedit.
                </p>
            </div>

            @if ($lokasi->status === 'Terisi')
                <span class="rounded-full bg-blue-50 px-4 py-2 text-sm font-semibold text-blue-700">
                    Terisi
                </span>
            @else
                <span class="rounded-full bg-emerald-50 px-4 py-2 text-sm font-semibold text-emerald-700">
                    {{ $lokasi->status ?? 'Kosong' }}
                </span>
            @endif
        </div>
    </div>

    {{-- Form edit --}}
    <div class="w-full rounded-2xl border border-[#E2EBE9] bg-white p-6 sm:p-8 lg:p-10">

        @if ($errors->any())
            <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                <p class="font-semibold">Data belum berhasil disimpan.</p>
                <ul class="mt-2 list-inside list-disc">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('lokasi.update', $lokasi) }}"
              method="POST"
              class="space-y-6">

            @csrf
            @method('PUT')

            <div>
                <h2 class="text-base font-bold text-[#365F65]">
                    Informasi Ruangan
                </h2>

                <p class="mt-1 text-sm text-[#8BA5A2]">
                    Pastikan informasi ruangan sudah sesuai sebelum menyimpan perubahan.
                </p>
            </div>

            <div class="grid grid-cols-1 gap-x-6 gap-y-5 md:grid-cols-2 xl:grid-cols-3">

                {{-- Terminal --}}
                <div>
                    <label for="terminal"
                           class="mb-2 block text-sm font-semibold text-[#365F65]">
                        Terminal <span class="text-red-500">*</span>
                    </label>

                    <select id="terminal"
                            name="terminal"
                            required
                            class="w-full rounded-xl border border-[#DCE6E4] bg-[#F8FAF9] px-4 py-3 text-sm text-[#365F65] outline-none focus:border-[#527D82] focus:ring-2 focus:ring-[#527D82]/20">
                        <option value="">Pilih Terminal</option>
                        <option value="T1"
                            {{ old('terminal', $lokasi->terminal) === 'T1' ? 'selected' : '' }}>
                            Terminal 1
                        </option>
                        <option value="T2"
                            {{ old('terminal', $lokasi->terminal) === 'T2' ? 'selected' : '' }}>
                            Terminal 2
                        </option>
                    </select>

                    @error('terminal')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Lantai --}}
                <div>
                    <label for="lantai"
                           class="mb-2 block text-sm font-semibold text-[#365F65]">
                        Lantai <span class="text-red-500">*</span>
                    </label>

                    <input type="text"
                           id="lantai"
                           name="lantai"
                           value="{{ old('lantai', $lokasi->lantai) }}"
                           placeholder="Contoh: 1"
                           required
                           class="w-full rounded-xl border border-[#DCE6E4] bg-[#F8FAF9] px-4 py-3 text-sm text-[#365F65] placeholder:text-[#A3B5B2] outline-none focus:border-[#527D82] focus:ring-2 focus:ring-[#527D82]/20">

                    @error('lantai')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Jenis Ruangan --}}
                <div>
                    <label for="jenis_ruangan"
                           class="mb-2 block text-sm font-semibold text-[#365F65]">
                        Jenis Ruangan <span class="text-red-500">*</span>
                    </label>

                    <select id="jenis_ruangan"
                            name="jenis_ruangan"
                            required
                            class="w-full rounded-xl border border-[#DCE6E4] bg-[#F8FAF9] px-4 py-3 text-sm text-[#365F65] outline-none focus:border-[#527D82] focus:ring-2 focus:ring-[#527D82]/20">
                        <option value="">Pilih Jenis Ruangan</option>

                        @foreach ([
                            'Retail',
                            'F&B',
                            'Service',
                            'Lounge',
                            'Duty Free',
                            'UMKM',
                            'Kantor',
                            'Gudang',
                            'Lainnya'
                        ] as $jenis)
                            <option value="{{ $jenis }}"
                                {{ old('jenis_ruangan', $lokasi->jenis_ruangan) === $jenis ? 'selected' : '' }}>
                                {{ $jenis }}
                            </option>
                        @endforeach

                        {{-- Mempertahankan nilai lama jika tidak ada dalam pilihan --}}
                        @if (
                            $lokasi->jenis_ruangan &&
                            !in_array($lokasi->jenis_ruangan, [
                                'Retail', 'F&B', 'Service', 'Lounge',
                                'Duty Free', 'UMKM', 'Kantor',
                                'Gudang', 'Lainnya'
                            ])
                        )
                            <option value="{{ $lokasi->jenis_ruangan }}"
                                selected>
                                {{ $lokasi->jenis_ruangan }}
                            </option>
                        @endif
                    </select>

                    @error('jenis_ruangan')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Nomor RO --}}
                <div>
                    <label for="nomor_ro"
                           class="mb-2 block text-sm font-semibold text-[#365F65]">
                        Nomor RO
                    </label>

                    <input type="text"
                           id="nomor_ro"
                           name="nomor_ro"
                           value="{{ old('nomor_ro', $lokasi->nomor_ro) }}"
                           placeholder="Masukkan nomor RO"
                           class="w-full rounded-xl border border-[#DCE6E4] bg-[#F8FAF9] px-4 py-3 text-sm text-[#365F65] placeholder:text-[#A3B5B2] outline-none focus:border-[#527D82] focus:ring-2 focus:ring-[#527D82]/20">

                    @error('nomor_ro')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Nama Lokasi --}}
                <div class="md:col-span-2 xl:col-span-2">
                    <label for="lokasi"
                           class="mb-2 block text-sm font-semibold text-[#365F65]">
                        Nama / Area Lokasi <span class="text-red-500">*</span>
                    </label>

                    <input type="text"
                           id="lokasi"
                           name="lokasi"
                           value="{{ old('lokasi', $lokasi->lokasi) }}"
                           placeholder="Contoh: Area Keberangkatan, dekat Gate 3"
                           required
                           maxlength="255"
                           class="w-full rounded-xl border border-[#DCE6E4] bg-[#F8FAF9] px-4 py-3 text-sm text-[#365F65] placeholder:text-[#A3B5B2] outline-none focus:border-[#527D82] focus:ring-2 focus:ring-[#527D82]/20">

                    @error('lokasi')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Luas Area --}}
                <div>
                    <label for="luas_area"
                           class="mb-2 block text-sm font-semibold text-[#365F65]">
                        Luas Area (m²)
                    </label>

                    <input type="number"
                           id="luas_area"
                           name="luas_area"
                           value="{{ old('luas_area', $lokasi->luas_area) }}"
                           placeholder="Contoh: 25"
                           min="0"
                           step="0.01"
                           class="w-full rounded-xl border border-[#DCE6E4] bg-[#F8FAF9] px-4 py-3 text-sm text-[#365F65] placeholder:text-[#A3B5B2] outline-none focus:border-[#527D82] focus:ring-2 focus:ring-[#527D82]/20">

                    @error('luas_area')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Catatan --}}
                <div class="md:col-span-2 xl:col-span-3">
                    <label for="catatan"
                           class="mb-2 block text-sm font-semibold text-[#365F65]">
                        Keterangan
                    </label>

                    <textarea id="catatan"
                              name="catatan"
                              rows="4"
                              placeholder="Tambahkan keterangan lokasi jika diperlukan"
                              class="w-full resize-y rounded-xl border border-[#DCE6E4] bg-[#F8FAF9] px-4 py-3 text-sm text-[#365F65] placeholder:text-[#A3B5B2] outline-none focus:border-[#527D82] focus:ring-2 focus:ring-[#527D82]/20">{{ old('catatan', $lokasi->catatan) }}</textarea>

                    @error('catatan')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            {{-- Tombol --}}
            <div class="flex flex-col-reverse gap-3 border-t border-[#E2EBE9] pt-6 sm:flex-row sm:justify-end">
                <a href="{{ route('lokasi.show', $lokasi) }}"
                   class="inline-flex justify-center rounded-xl border border-[#DCE6E4] bg-white px-5 py-3 text-sm font-semibold text-[#527D82] hover:bg-[#F5F8F7]">
                    Batal
                </a>

                <button type="submit"
                        class="inline-flex justify-center rounded-xl bg-[#365F65] px-5 py-3 text-sm font-semibold text-white hover:bg-[#294F55] focus:outline-none focus:ring-2 focus:ring-[#527D82]/30">
                    Simpan Perubahan
                </button>
            </div>

        </form>
    </div>
</div>
@endsection