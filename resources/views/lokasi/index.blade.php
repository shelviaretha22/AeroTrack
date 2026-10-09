@extends('layouts.app')

@section('page-title', 'Data Lokasi')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-sm text-[#8BA5A2]">Management / Lokasi</p>
            <h1 class="mt-1 text-2xl font-bold text-[#365F65]">Data Lokasi</h1>
            <p class="mt-1 text-sm text-[#7A9290]">
                Kelola data ruangan fisik yang tersedia di bandara.
            </p>
        </div>

        <a href="{{ route('lokasi.create') }}"
           class="inline-flex items-center justify-center rounded-xl bg-[#365F65] px-5 py-3 text-sm font-semibold text-white hover:bg-[#294F55]">
            + Tambah Lokasi
        </a>
    </div>

    {{-- Notifikasi --}}
    @foreach (['success', 'error', 'info'] as $type)
        @if (session($type))
            <div class="rounded-xl border border-[#DCE6E4] bg-white p-4 text-sm text-[#365F65]">
                {{ session($type) }}
            </div>
        @endif
    @endforeach

    {{-- Dashboard Statistik Lokasi --}}
    <section class="space-y-4">
        <div>
            <h2 class="text-lg font-bold text-[#365F65]">
                Ringkasan Lokasi
            </h2>
            <p class="mt-1 text-sm text-[#7A9290]">
                Statistik seluruh lokasi yang tersimpan di database.
            </p>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">

            {{-- Total Lokasi --}}
            <div class="relative overflow-hidden rounded-2xl border border-[#E2EBE9] bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-sm font-medium text-[#7A9290]">
                            Total Lokasi
                        </p>
                        <p class="mt-3 text-3xl font-bold text-[#365F65]">
                            {{ number_format($totalLokasi, 0, ',', '.') }}
                        </p>
                        <p class="mt-2 text-xs text-[#8BA5A2]">
                            Seluruh lokasi terdaftar
                        </p>
                    </div>
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-[#EAF2F1]">
                        <svg class="h-6 w-6 text-[#365F65]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"/>
                            <circle cx="12" cy="10" r="2.5"/>
                        </svg>
                    </div>
                </div>
                <div class="absolute bottom-0 left-0 h-1 w-full bg-[#527D82]"></div>
            </div>

            {{-- Lokasi Kosong --}}
            <div class="relative overflow-hidden rounded-2xl border border-[#E2EBE9] bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-sm font-medium text-[#7A9290]">
                            Lokasi Kosong
                        </p>
                        <p class="mt-3 text-3xl font-bold text-emerald-700">
                            {{ number_format($totalKosong, 0, ',', '.') }}
                        </p>
                        <p class="mt-2 text-xs text-[#8BA5A2]">
                            {{ $persentaseKosong }}% dari total lokasi
                        </p>
                    </div>
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-emerald-50">
                        <svg class="h-6 w-6 text-emerald-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <circle cx="12" cy="12" r="9"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="m8 12 2.5 2.5L16 9"/>
                        </svg>
                    </div>
                </div>
                <div class="absolute bottom-0 left-0 h-1 bg-emerald-600"
                     style="width: {{ $persentaseKosong }}%"></div>
            </div>

            {{-- Lokasi Terisi --}}
            <div class="relative overflow-hidden rounded-2xl border border-[#E2EBE9] bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-sm font-medium text-[#7A9290]">
                            Lokasi Terisi
                        </p>
                        <p class="mt-3 text-3xl font-bold text-amber-700">
                            {{ number_format($totalTerisi, 0, ',', '.') }}
                        </p>
                        <p class="mt-2 text-xs text-[#8BA5A2]">
                            {{ $persentaseTerisi }}% dari total lokasi
                        </p>
                    </div>
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-amber-50">
                        <svg class="h-6 w-6 text-amber-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <rect x="4" y="3" width="16" height="18" rx="2"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 21v-5h6v5M9 7h.01M15 7h.01M9 11h.01M15 11h.01"/>
                        </svg>
                    </div>
                </div>
                <div class="absolute bottom-0 left-0 h-1 bg-amber-500"
                     style="width: {{ $persentaseTerisi }}%"></div>
            </div>

            {{-- Jenis Ruangan --}}
            <div class="relative overflow-hidden rounded-2xl border border-[#E2EBE9] bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-sm font-medium text-[#7A9290]">
                            Jenis Ruangan
                        </p>
                        <p class="mt-3 text-3xl font-bold text-blue-700">
                            {{ number_format($totalJenisRuangan, 0, ',', '.') }}
                        </p>
                        <p class="mt-2 text-xs text-[#8BA5A2]">
                            Kategori berbeda terdaftar
                        </p>
                    </div>
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-blue-50">
                        <svg class="h-6 w-6 text-blue-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <rect x="3" y="3" width="7" height="7" rx="1"/>
                            <rect x="14" y="3" width="7" height="7" rx="1"/>
                            <rect x="3" y="14" width="7" height="7" rx="1"/>
                            <rect x="14" y="14" width="7" height="7" rx="1"/>
                        </svg>
                    </div>
                </div>
                <div class="absolute bottom-0 left-0 h-1 w-full bg-blue-600"></div>
            </div>

        </div>
    </section>

    {{-- Grafik Dashboard --}}
    <section class="grid grid-cols-1 gap-6 xl:grid-cols-2">

        {{-- Grafik Donat Status Lokasi --}}
        <div class="rounded-2xl border border-[#E2EBE9] bg-white p-5 sm:p-6">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <h2 class="font-bold text-[#365F65]">
                        Komposisi Status Lokasi
                    </h2>
                    <p class="mt-1 text-sm text-[#7A9290]">
                        Perbandingan lokasi kosong dan terisi.
                    </p>
                </div>
                <span class="rounded-lg bg-[#F3F6F5] px-3 py-1.5 text-xs font-semibold text-[#527D82]">
                    Status
                </span>
            </div>

            <div class="mt-6 flex flex-col items-center gap-6 sm:flex-row sm:justify-center">

                <div
                    class="relative flex h-44 w-44 shrink-0 items-center justify-center rounded-full"
                    style="background: conic-gradient(#31966A 0% {{ $persentaseKosong }}%, #D6A14D {{ $persentaseKosong }}% {{ min(100, $persentaseKosong + $persentaseTerisi) }}%, #E7EFEC {{ min(100, $persentaseKosong + $persentaseTerisi) }}% 100%);"
                    role="img"
                    aria-label="Grafik donat komposisi status lokasi"
                >
                    <div class="flex h-28 w-28 flex-col items-center justify-center rounded-full bg-white">
                        <span class="text-3xl font-bold text-[#365F65]">
                            {{ $totalLokasi }}
                        </span>
                        <span class="text-xs text-[#7A9290]">
                            Total lokasi
                        </span>
                    </div>
                </div>

                <div class="w-full space-y-4 sm:max-w-48">
                    <div class="flex items-center gap-3">
                        <span class="h-3 w-3 shrink-0 rounded-full bg-emerald-600"></span>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm text-[#527D82]">Kosong</p>
                            <p class="font-bold text-[#365F65]">
                                {{ $totalKosong }} lokasi
                            </p>
                        </div>
                        <span class="text-sm font-semibold text-emerald-700">
                            {{ $persentaseKosong }}%
                        </span>
                    </div>

                    <div class="flex items-center gap-3">
                        <span class="h-3 w-3 shrink-0 rounded-full bg-amber-500"></span>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm text-[#527D82]">Terisi</p>
                            <p class="font-bold text-[#365F65]">
                                {{ $totalTerisi }} lokasi
                            </p>
                        </div>
                        <span class="text-sm font-semibold text-amber-700">
                            {{ $persentaseTerisi }}%
                        </span>
                    </div>

                    <p class="border-t border-[#E2EBE9] pt-3 text-xs leading-5 text-[#8BA5A2]">
                        Persentase dihitung dari total seluruh lokasi.
                    </p>
                </div>
            </div>
        </div>

        {{-- Grafik Lokasi per Terminal --}}
        <div class="rounded-2xl border border-[#E2EBE9] bg-white p-5 sm:p-6">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <h2 class="font-bold text-[#365F65]">
                        Lokasi per Terminal
                    </h2>
                    <p class="mt-1 text-sm text-[#7A9290]">
                        Jumlah ruangan yang terdaftar di setiap terminal.
                    </p>
                </div>
                <span class="rounded-lg bg-[#EAF2F1] px-3 py-1.5 text-xs font-semibold text-[#365F65]">
                    Terminal
                </span>
            </div>

            @php
                $maksTerminal = max(
                    1,
                    (int) $lokasiPerTerminal->max('total')
                );
            @endphp

            <div class="mt-7 space-y-6">
                @forelse ($lokasiPerTerminal as $terminalItem)
                    <div>
                        <div class="mb-2 flex items-center justify-between gap-3">
                            <span class="text-sm font-semibold text-[#527D82]">
                                {{ $terminalItem->terminal }}
                            </span>
                            <span class="text-sm font-bold text-[#365F65]">
                                {{ $terminalItem->total }} lokasi
                            </span>
                        </div>

                        <div
                            class="h-3 overflow-hidden rounded-full bg-[#E7EFEC]"
                            role="img"
                            aria-label="{{ $terminalItem->terminal }}: {{ $terminalItem->total }} lokasi"
                        >
                            <div
                                class="h-full rounded-full bg-[#527D82] transition-all duration-500"
                                style="width: {{ ($terminalItem->total / $maksTerminal) * 100 }}%"
                            ></div>
                        </div>
                    </div>
                @empty
                    <div class="rounded-xl bg-[#F8FAF9] px-4 py-10 text-center">
                        <p class="font-semibold text-[#365F65]">
                            Belum ada data terminal
                        </p>
                        <p class="mt-1 text-sm text-[#7A9290]">
                            Statistik akan muncul setelah lokasi ditambahkan.
                        </p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Grafik Lokasi per Jenis Ruangan --}}
        <div class="rounded-2xl border border-[#E2EBE9] bg-white p-5 sm:p-6 xl:col-span-2">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="font-bold text-[#365F65]">
                        Lokasi per Jenis Ruangan
                    </h2>
                    <p class="mt-1 text-sm text-[#7A9290]">
                        Distribusi Store, Counter, Customer Service, Lounge, dan kategori lainnya sesuai database.
                    </p>
                </div>
                <span class="w-fit rounded-lg bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700">
                    Jenis ruangan
                </span>
            </div>

            @php
                $maksJenis = max(
                    1,
                    (int) $lokasiPerJenis->max('total')
                );
            @endphp

            @if ($lokasiPerJenis->isNotEmpty())
                <div class="mt-6 grid grid-cols-1 gap-x-8 gap-y-5 md:grid-cols-2">
                    @foreach ($lokasiPerJenis as $jenisItem)
                        <div>
                            <div class="mb-2 flex items-center justify-between gap-3">
                                <span class="break-words text-sm font-semibold text-[#527D82]">
                                    {{ $jenisItem->jenis_ruangan }}
                                </span>
                                <span class="shrink-0 text-sm font-bold text-[#365F65]">
                                    {{ $jenisItem->total }}
                                </span>
                            </div>

                            <div
                                class="h-3 overflow-hidden rounded-full bg-[#E7EFEC]"
                                role="img"
                                aria-label="{{ $jenisItem->jenis_ruangan }}: {{ $jenisItem->total }} lokasi"
                            >
                                <div
                                    class="h-full rounded-full bg-[#527D82] transition-all duration-500"
                                    style="width: {{ ($jenisItem->total / $maksJenis) * 100 }}%"
                                ></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="mt-6 rounded-xl bg-[#F8FAF9] px-4 py-10 text-center">
                    <p class="font-semibold text-[#365F65]">
                        Belum ada data jenis ruangan
                    </p>
                    <p class="mt-1 text-sm text-[#7A9290]">
                        Statistik akan muncul setelah data jenis ruangan tersedia.
                    </p>
                </div>
            @endif
        </div>
    </section>

    {{-- Filter --}}
    <div class="rounded-2xl border border-[#E2EBE9] bg-white p-5 sm:p-6">
        <div class="mb-4">
            <h2 class="font-bold text-[#365F65]">Cari dan Filter Lokasi</h2>
            <p class="mt-1 text-xs text-[#8BA5A2]">
                Gunakan filter untuk menemukan ruangan yang dibutuhkan.
            </p>
        </div>

        <form method="GET" action="{{ route('lokasi.index') }}"
              class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">

            <div>
                <label for="search" class="mb-1.5 block text-sm font-medium text-[#527D82]">
                    Pencarian
                </label>
                <input type="text" id="search" name="search"
                       value="{{ request('search') }}"
                       placeholder="Kode, nama lokasi, nomor RO..."
                       class="w-full rounded-xl border border-[#DCE6E4] bg-[#F8FAF9] px-4 py-3 text-sm text-[#365F65] outline-none focus:border-[#527D82]">
            </div>

            <div>
                <label for="terminal" class="mb-1.5 block text-sm font-medium text-[#527D82]">
                    Terminal
                </label>
                <select id="terminal" name="terminal"
                        class="w-full rounded-xl border border-[#DCE6E4] bg-[#F8FAF9] px-4 py-3 text-sm text-[#365F65]">
                    <option value="">Semua Terminal</option>
                    <option value="T1" @selected(request('terminal') === 'T1')>Terminal 1</option>
                    <option value="T2" @selected(request('terminal') === 'T2')>Terminal 2</option>
                </select>
            </div>

            <div>
                <label for="lantai" class="mb-1.5 block text-sm font-medium text-[#527D82]">
                    Lantai
                </label>
                <select id="lantai" name="lantai"
                        class="w-full rounded-xl border border-[#DCE6E4] bg-[#F8FAF9] px-4 py-3 text-sm text-[#365F65]">
                    <option value="">Semua Lantai</option>
                    @foreach (['1', '2', '3', '4'] as $lantai)
                        <option value="{{ $lantai }}" @selected(request('lantai') == $lantai)>
                            Lantai {{ $lantai }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="jenis_ruangan" class="mb-1.5 block text-sm font-medium text-[#527D82]">
                    Jenis Ruangan
                </label>
                <select id="jenis_ruangan" name="jenis_ruangan"
                        class="w-full rounded-xl border border-[#DCE6E4] bg-[#F8FAF9] px-4 py-3 text-sm text-[#365F65]">
                    <option value="">Semua Jenis</option>
                    @foreach ([
                        'Store', 'Counter', 'Customer Service',
                        'Lounge', 'Antena', 'Gudang', 'Lainnya'
                    ] as $jenis)
                        <option value="{{ $jenis }}" @selected(request('jenis_ruangan') === $jenis)>
                            {{ $jenis }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="status" class="mb-1.5 block text-sm font-medium text-[#527D82]">
                    Status Lokasi
                </label>
                <select id="status" name="status"
                        class="w-full rounded-xl border border-[#DCE6E4] bg-[#F8FAF9] px-4 py-3 text-sm text-[#365F65]">
                    <option value="">Semua Status</option>
                    <option value="Kosong" @selected(request('status') === 'Kosong')>Kosong</option>
                    <option value="Terisi" @selected(request('status') === 'Terisi')>Terisi</option>
                </select>
            </div>

            <div>
                <label for="sort" class="mb-1.5 block text-sm font-medium text-[#527D82]">
                    Urutkan
                </label>
                <select id="sort" name="sort"
                        class="w-full rounded-xl border border-[#DCE6E4] bg-[#F8FAF9] px-4 py-3 text-sm text-[#365F65]">
                    <option value="newest" @selected(request('sort', 'newest') === 'newest')>Terbaru</option>
                    <option value="oldest" @selected(request('sort') === 'oldest')>Terlama</option>
                    <option value="az" @selected(request('sort') === 'az')>Kode A–Z</option>
                    <option value="za" @selected(request('sort') === 'za')>Kode Z–A</option>
                </select>
            </div>

            <div class="flex flex-col gap-3 sm:col-span-2 lg:col-span-3 sm:flex-row sm:justify-end">
                <a href="{{ route('lokasi.index') }}"
                   class="inline-flex items-center justify-center rounded-xl border border-[#DCE6E4] px-5 py-3 text-sm font-semibold text-[#527D82] hover:bg-[#F5F8F7]">
                    Reset Filter
                </a>

                <button type="submit"
                        class="inline-flex items-center justify-center rounded-xl bg-[#365F65] px-5 py-3 text-sm font-semibold text-white hover:bg-[#294F55]">
                    Terapkan Filter
                </button>
            </div>
        </form>
    </div>

    {{-- Tabel Lokasi --}}
    <div class="overflow-hidden rounded-2xl border border-[#E2EBE9] bg-white">
        <div class="border-b border-[#E2EBE9] p-5">
            <h2 class="font-bold text-[#365F65]">Daftar Ruangan</h2>
            <p class="mt-1 text-sm text-[#8BA5A2]">
                Total {{ $lokasis->count() }} lokasi ditemukan.
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-[#F3F6F5] text-[#527D82]">
                    <tr>
                        <th class="whitespace-nowrap px-4 py-4">No.</th>
                        <th class="whitespace-nowrap px-4 py-4">Kode Ruang</th>
                        <th class="whitespace-nowrap px-4 py-4">Nama Tenant</th>
                        <th class="whitespace-nowrap px-4 py-4">Terminal</th>
                        <th class="whitespace-nowrap px-4 py-4">Lantai</th>
                        <th class="whitespace-nowrap px-4 py-4">Jenis Ruangan</th>
                        <th class="whitespace-nowrap px-4 py-4">Nama Lokasi</th>
                        <th class="whitespace-nowrap px-4 py-4">Luas (m²)</th>
                        <th class="whitespace-nowrap px-4 py-4">Status</th>
                        <th class="whitespace-nowrap px-4 py-4">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-[#E2EBE9]">
                    @forelse ($lokasis as $lokasi)
                        <tr class="hover:bg-[#F8FAF9]">
                            <td class="whitespace-nowrap px-4 py-4">
                                {{ $loop->iteration }}
                            </td>

                            <td class="whitespace-nowrap px-4 py-4">
                                <div class="font-semibold text-[#365F65]">
                                    {{ $lokasi->kode_tampilan ?? $lokasi->kode_ruang }}
                                </div>

                                @if (
                                    $lokasi->tenant_id &&
                                    $lokasi->kode_tampilan &&
                                    $lokasi->kode_tampilan !== $lokasi->kode_ruang
                                )
                                    <div class="mt-1 text-xs text-[#8BA5A2]">
                                        Kode asli: {{ $lokasi->kode_ruang }}
                                    </div>
                                @endif
                            </td>

                            <td class="min-w-48 px-4 py-4">
                                @if ($lokasi->tenant)
                                    <span class="font-medium text-[#365F65]">
                                        {{ $lokasi->tenant->nama_tenant
                                            ?? $lokasi->tenant->nama
                                            ?? 'Tenant #' . $lokasi->tenant->id }}
                                    </span>
                                @else
                                    <span class="text-[#8BA5A2]">Belum ditempati</span>
                                @endif
                            </td>

                            <td class="whitespace-nowrap px-4 py-4">
                                {{ $lokasi->terminal ?? '-' }}
                            </td>

                            <td class="whitespace-nowrap px-4 py-4">
                                {{ $lokasi->lantai ? 'Lantai '.$lokasi->lantai : '-' }}
                            </td>

                            <td class="whitespace-nowrap px-4 py-4">
                                {{ $lokasi->jenis_ruangan ?: '-' }}
                            </td>

                            <td class="min-w-44 px-4 py-4">
                                {{ $lokasi->lokasi ?: '-' }}
                            </td>

                            <td class="whitespace-nowrap px-4 py-4">
                                {{ $lokasi->luas_area !== null
                                    ? number_format((float) $lokasi->luas_area, 2, ',', '.')
                                    : '-' }}
                            </td>

                            <td class="whitespace-nowrap px-4 py-4">
                                @if ($lokasi->status === 'Terisi')
                                    <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">
                                        Terisi
                                    </span>
                                @else
                                    <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">
                                        Kosong
                                    </span>
                                @endif
                            </td>

                            {{-- Aksi --}}
                            <td class="whitespace-nowrap px-4 py-4">
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('lokasi.show', $lokasi->id) }}"
                                       class="font-semibold text-[#527D82] hover:underline">
                                        Detail
                                    </a>

                                    <a href="{{ route('lokasi.edit', $lokasi->id) }}"
                                       class="font-semibold text-amber-700 hover:underline">
                                        Edit
                                    </a>

                                    @if ($lokasi->status === 'Kosong' && $lokasi->tenant_id === null)
                                        <form action="{{ route('lokasi.destroy', $lokasi->id) }}"
                                              method="POST"
                                              onsubmit="return confirm('Yakin ingin menghapus lokasi ini?');">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                    class="font-semibold text-red-600 hover:underline">
                                                Hapus
                                            </button>
                                        </form>
                                    @else
                                        <span class="cursor-not-allowed text-sm text-gray-400"
                                              title="Lokasi yang terisi tidak dapat dihapus">
                                            Hapus
                                        </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-4 py-12 text-center">
                                <p class="font-semibold text-[#365F65]">
                                    Lokasi tidak ditemukan
                                </p>
                                <p class="mt-1 text-sm text-[#8BA5A2]">
                                    Coba ubah kata pencarian atau reset filter.
                                </p>
                                <a href="{{ route('lokasi.index') }}"
                                   class="mt-3 inline-block font-semibold text-[#527D82] hover:underline">
                                    Reset Filter
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection