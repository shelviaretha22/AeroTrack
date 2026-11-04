@extends('layouts.app')

@section('page-title', 'Identitas Perusahaan')

@section('content')

@php
    $input = 'mt-1.5 w-full rounded-xl border border-[#DCE6E4] bg-white px-4 py-2.5 text-sm text-[#365F65] placeholder-[#A0B3B0] shadow-sm transition focus:border-[#527D82] focus:outline-none focus:ring-2 focus:ring-[#527D82]/20';
    $label = 'block text-xs font-bold uppercase tracking-wide text-[#8BA5A2]';
@endphp

<div class="min-h-screen bg-[#F3F6F5] py-6">
<div class="mx-auto max-w-5xl">

    {{-- HEADER --}}
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-[#365F65]">Identitas Perusahaan</h1>
            <p class="mt-1 text-sm text-[#8BA5A2]">Informasi ini dipakai sebagai Pihak Pertama dalam dokumen kontrak.</p>
        </div>
        <a href="{{ route('profile.index') }}"
           class="inline-flex items-center justify-center gap-2 rounded-xl border border-[#DCE6E4] bg-white px-4 py-2.5 text-sm font-semibold text-[#365F65] shadow-sm transition hover:bg-[#F8FAF9]">
            ← Kembali ke Profil
        </a>
    </div>

    {{-- NOTIFIKASI --}}
    @if (session('success'))
        <div class="mb-5 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-700">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <p class="font-bold">Periksa kembali data yang dimasukkan.</p>
            <ul class="mt-1 list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('profile.perusahaan.update') }}" method="POST" class="space-y-5">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 gap-5 lg:grid-cols-5">

            {{-- INFORMASI PERUSAHAAN --}}
            <section class="rounded-2xl border border-[#DCE6E4] bg-white p-6 shadow-sm lg:col-span-3">
                <div class="mb-5 flex items-center gap-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-[#E2EBE9] text-xl">🏢</div>
                    <div>
                        <h2 class="text-base font-bold text-[#365F65]">Informasi Perusahaan</h2>
                        <p class="text-xs text-[#8BA5A2]">Identitas resmi perusahaan.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label for="nama_perusahaan" class="{{ $label }}">Nama Perusahaan</label>
                        <input type="text" id="nama_perusahaan" name="nama_perusahaan" required
                               value="{{ old('nama_perusahaan', $perusahaan->nama_perusahaan) }}" class="{{ $input }}">
                    </div>

                    <div>
                        <label for="domisili" class="{{ $label }}">Domisili</label>
                        <input type="text" id="domisili" name="domisili"
                               value="{{ old('domisili', $perusahaan->domisili) }}" class="{{ $input }}">
                    </div>

                    <div>
                        <label for="telepon" class="{{ $label }}">Nomor Telepon</label>
                        <input type="text" id="telepon" name="telepon"
                               value="{{ old('telepon', $perusahaan->telepon) }}" class="{{ $input }}">
                    </div>

                    <div class="sm:col-span-2">
                        <label for="fax" class="{{ $label }}">Nomor Fax</label>
                        <input type="text" id="fax" name="fax"
                               value="{{ old('fax', $perusahaan->fax ?? '(031) 8667506') }}" class="{{ $input }}">
                    </div>

                    <div class="sm:col-span-2">
                        <label for="alamat" class="{{ $label }}">Alamat Perusahaan</label>
                        <textarea id="alamat" name="alamat" rows="3" class="{{ $input }}">{{ old('alamat', $perusahaan->alamat) }}</textarea>
                    </div>
                </div>
            </section>

            {{-- INFORMASI PENANDATANGAN --}}
            <section class="rounded-2xl border border-[#DCE6E4] bg-white p-6 shadow-sm lg:col-span-2">
                <div class="mb-5 flex items-center gap-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-[#FBF1DC] text-xl">✍</div>
                    <div>
                        <h2 class="text-base font-bold text-[#365F65]">Penandatangan</h2>
                        <p class="text-xs text-[#8BA5A2]">Pejabat yang mewakili perusahaan.</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <label for="nama_penandatangan" class="{{ $label }}">Nama Penandatangan</label>
                        <input type="text" id="nama_penandatangan" name="nama_penandatangan"
                               value="{{ old('nama_penandatangan', $perusahaan->nama_penandatangan) }}" class="{{ $input }}">
                    </div>

                    <div>
                        <label for="jabatan_penandatangan" class="{{ $label }}">Jabatan</label>
                        <input type="text" id="jabatan_penandatangan" name="jabatan_penandatangan"
                               value="{{ old('jabatan_penandatangan', $perusahaan->jabatan_penandatangan) }}" class="{{ $input }}">
                    </div>

                    <div>
                        <label for="email" class="{{ $label }}">Email Perusahaan</label>
                        <input type="email" id="email" name="email"
                               value="{{ old('email', $perusahaan->email) }}" class="{{ $input }}">
                    </div>
                </div>
            </section>
        </div>

        {{-- FOOTER AKSI --}}
        <div class="flex flex-col-reverse gap-3 rounded-2xl border border-[#DCE6E4] bg-white p-4 shadow-sm sm:flex-row sm:items-center sm:justify-end">
            <a href="{{ route('profile.index') }}"
               class="inline-flex items-center justify-center rounded-xl border border-[#DCE6E4] px-5 py-2.5 text-sm font-semibold text-[#527D82] transition hover:bg-[#F8FAF9]">
                Batal
            </a>
            <button type="submit"
                    class="inline-flex items-center justify-center rounded-xl bg-[#365F65] px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-[#294F55] focus:outline-none focus:ring-2 focus:ring-[#527D82]/40">
                Simpan Perubahan
            </button>
        </div>
    </form>

</div>
</div>
@endsection