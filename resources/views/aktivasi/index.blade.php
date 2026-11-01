@extends('layouts.app')

@section('page-title', 'Aktivasi')

@section('content')
@php
    $kelasInput = 'w-full rounded-xl border border-[#DCE6E4] bg-[#F8FAF9] px-3.5 py-2.5 text-sm text-[#365F65] outline-none transition focus:border-[#527D82] focus:bg-white focus:ring-2 focus:ring-[#527D82]/15';

    $persen = fn ($n) => $totalAktivasi > 0 ? round(($n / $totalAktivasi) * 100) : 0;

    // Alur utama dan satu cabang (Ditolak).
    $alur = [
        ['status' => 'Menunggu BA',        'warna' => '#8BA5A2', 'kelas' => 'text-[#527D82]',  'ket' => 'BA belum tersedia'],
        ['status' => 'Belum Diajukan',     'warna' => '#6C8EBF', 'kelas' => 'text-blue-700',   'ket' => 'Siap diajukan'],
        ['status' => 'Menunggu Keputusan', 'warna' => '#D6A14D', 'kelas' => 'text-amber-700',  'ket' => 'Di kantor pusat'],
        ['status' => 'Disetujui',          'warna' => '#31966A', 'kelas' => 'text-emerald-700', 'ket' => 'Kontrak sistem aktif'],
    ];
    $jumlahDitolak = $perAktivasi['Ditolak'] ?? 0;

    $warnaFisik = [
        'BA Belum Dibuat' => '#C9D6D3',
        'BA Belum TTD' => '#D6A14D',
        'BA Selesai' => '#527D82',
        'Kontrak Dalam Proses' => '#6C8EBF',
        'Kontrak Selesai' => '#31966A',
    ];
    $maksFisik = max(1, (int) $perKontrakFisik->max());

    $segmenSistem = [
        ['label' => 'Aktif', 'value' => $sistemAktif, 'color' => '#31966A'],
        ['label' => 'Belum Aktif', 'value' => max(0, $totalAktivasi - $sistemAktif), 'color' => '#C9D6D3'],
    ];

    $adaFilter = request()->hasAny(['search', 'kontrak_fisik', 'kontrak_sistem', 'status']);
@endphp

<div class="space-y-6">

    {{-- Header --}}
    <div>
        <p class="text-sm text-[#8BA5A2]">Management / Aktivasi</p>
        <h1 class="mt-1 text-2xl font-bold text-[#365F65]">Aktivasi</h1>
        <p class="mt-1 text-sm text-[#7A9290]">
            Pantau Kontrak Fisik, Kontrak Sistem, dan pengajuan Aktivasi setiap tenant. Pengelolaan dilakukan melalui tombol Detail.
        </p>
    </div>

    {{-- Notifikasi --}}
    @if (session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800" role="status">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700" role="status">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <p class="font-semibold">Data belum berhasil disimpan.</p>
            <ul class="mt-2 list-inside list-disc">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Statistik (klik untuk menyaring tabel) --}}
    <section class="grid grid-cols-2 gap-3 sm:gap-4 xl:grid-cols-4" aria-label="Ringkasan aktivasi">

        <a href="{{ route('aktivasi') }}"
           class="block rounded-2xl border border-[#E2EBE9] bg-white p-4 shadow-sm transition hover:border-[#B6C7C4] hover:shadow sm:p-5">
            <div class="flex items-center justify-between gap-3">
                <p class="text-sm font-medium text-[#7A9290]">Total Kerja Sama</p>
                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-[#EAF2F1] text-[#365F65]">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h7l5 5v11a2 2 0 0 1-2 2Z"/>
                    </svg>
                </span>
            </div>
            <p class="mt-2 text-3xl font-bold text-[#365F65]">{{ $totalAktivasi }}</p>
            <p class="mt-1 text-xs text-[#8BA5A2]">Semua kerja sama terdaftar</p>
        </a>

        <a href="{{ route('aktivasi', ['status' => 'Menunggu Keputusan']) }}"
           class="block rounded-2xl border border-[#E2EBE9] bg-white p-4 shadow-sm transition hover:border-amber-300 hover:shadow sm:p-5">
            <div class="flex items-center justify-between gap-3">
                <p class="text-sm font-medium text-[#7A9290]">Menunggu Keputusan</p>
                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-amber-50 text-amber-700">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
                        <circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>
                    </svg>
                </span>
            </div>
            <p class="mt-2 text-3xl font-bold text-amber-700">{{ $menunggu }}</p>
            <div class="mt-2 h-1.5 overflow-hidden rounded-full bg-[#E7EFEC]">
                <div class="h-full rounded-full bg-amber-500" style="width: {{ $persen($menunggu) }}%"></div>
            </div>
            <p class="mt-1 text-xs text-[#8BA5A2]">{{ $persen($menunggu) }}% dari total</p>
        </a>

        <a href="{{ route('aktivasi', ['status' => 'Disetujui']) }}"
           class="block rounded-2xl border border-[#E2EBE9] bg-white p-4 shadow-sm transition hover:border-emerald-300 hover:shadow sm:p-5">
            <div class="flex items-center justify-between gap-3">
                <p class="text-sm font-medium text-[#7A9290]">Disetujui</p>
                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-emerald-50 text-emerald-700">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m5 12 4 4L19 6"/>
                    </svg>
                </span>
            </div>
            <p class="mt-2 text-3xl font-bold text-emerald-700">{{ $disetujui }}</p>
            <div class="mt-2 h-1.5 overflow-hidden rounded-full bg-[#E7EFEC]">
                <div class="h-full rounded-full bg-emerald-600" style="width: {{ $persen($disetujui) }}%"></div>
            </div>
            <p class="mt-1 text-xs text-[#8BA5A2]">{{ $persen($disetujui) }}% dari total</p>
        </a>

        <a href="{{ route('aktivasi', ['kontrak_sistem' => 'Aktif']) }}"
           class="block rounded-2xl border border-[#E2EBE9] bg-white p-4 shadow-sm transition hover:border-emerald-300 hover:shadow sm:p-5">
            <div class="flex items-center justify-between gap-3">
                <p class="text-sm font-medium text-[#7A9290]">Kontrak Sistem Aktif</p>
                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-blue-50 text-blue-700">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M13 2 4 14h7l-1 8 9-12h-7l1-8Z"/>
                    </svg>
                </span>
            </div>
            <p class="mt-2 text-3xl font-bold text-blue-700">{{ $sistemAktif }}</p>
            <div class="mt-2 h-1.5 overflow-hidden rounded-full bg-[#E7EFEC]">
                <div class="h-full rounded-full bg-blue-500" style="width: {{ $persen($sistemAktif) }}%"></div>
            </div>
            <p class="mt-1 text-xs text-[#8BA5A2]">{{ $persen($sistemAktif) }}% dari total</p>
        </a>
    </section>

    {{-- Diagram --}}
    <section x-data="{ buka: true }" class="rounded-2xl border border-[#E2EBE9] bg-white shadow-sm">
        <button type="button" @click="buka = !buka" :aria-expanded="buka"
                class="flex w-full items-center justify-between gap-3 px-5 py-4 text-left">
            <span>
                <span class="block font-bold text-[#365F65]">Diagram Aktivasi</span>
                <span class="mt-0.5 block text-sm text-[#7A9290]">Alur pengajuan, posisi kontrak fisik, kontrak sistem, dan kontrak yang segera berakhir.</span>
            </span>
            <svg class="h-5 w-5 shrink-0 text-[#527D82] transition" :class="buka ? 'rotate-180' : ''" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="m6 9 6 6 6-6"/>
            </svg>
        </button>

        <div x-show="buka" x-transition class="space-y-6 border-t border-[#E2EBE9] p-5 sm:p-6">

            {{-- Alur aktivasi --}}
            <div class="rounded-xl border border-[#E2EBE9] p-5">
                <h3 class="font-bold text-[#365F65]">Alur Pengajuan Aktivasi</h3>
                <p class="mt-1 text-sm text-[#7A9290]">Posisi setiap kerja sama dalam proses. Klik tahap untuk menyaring tabel.</p>

                {{-- Bar gabungan --}}
                @if ($totalAktivasi > 0)
                    <div class="mt-5 flex h-3 overflow-hidden rounded-full bg-[#E7EFEC]" role="img"
                         aria-label="Sebaran status aktivasi dari {{ $totalAktivasi }} kerja sama">
                        @foreach ($alur as $tahap)
                            @php $n = $perAktivasi[$tahap['status']] ?? 0; @endphp
                            @if ($n > 0)
                                <div style="width: {{ ($n / $totalAktivasi) * 100 }}%; background: {{ $tahap['warna'] }}"
                                     title="{{ $tahap['status'] }}: {{ $n }}"></div>
                            @endif
                        @endforeach
                        @if ($jumlahDitolak > 0)
                            <div style="width: {{ ($jumlahDitolak / $totalAktivasi) * 100 }}%; background: #D14B4B"
                                 title="Ditolak: {{ $jumlahDitolak }}"></div>
                        @endif
                    </div>
                @endif

                {{-- Simpul alur --}}
                <div class="mt-5 grid grid-cols-2 gap-3 lg:grid-cols-[1fr_auto_1fr_auto_1fr_auto_1fr] lg:items-stretch lg:gap-2">
                    @foreach ($alur as $i => $tahap)
                        @php $n = $perAktivasi[$tahap['status']] ?? 0; @endphp

                        <a href="{{ route('aktivasi', ['status' => $tahap['status']]) }}"
                           class="group block rounded-xl border border-[#E2EBE9] bg-[#F8FAF9] p-4 transition hover:border-[#B6C7C4] hover:bg-white hover:shadow-sm
                                  {{ request('status') === $tahap['status'] ? 'ring-2 ring-[#527D82]' : '' }}">
                            <span class="block h-1 w-8 rounded-full" style="background: {{ $tahap['warna'] }}"></span>
                            <p class="mt-3 text-xs font-semibold text-[#7A9290]">{{ $i + 1 }}. {{ $tahap['status'] }}</p>
                            <p class="mt-1 text-2xl font-bold {{ $tahap['kelas'] }}">{{ $n }}</p>
                            <p class="mt-0.5 text-xs text-[#8BA5A2]">{{ $persen($n) }}% · {{ $tahap['ket'] }}</p>
                        </a>

                        @if (! $loop->last)
                            <div class="hidden items-center justify-center text-[#B6C7C4] lg:flex" aria-hidden="true">
                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="m9 6 6 6-6 6"/>
                                </svg>
                            </div>
                        @endif
                    @endforeach
                </div>

                {{-- Cabang: ditolak --}}
                <div class="mt-3 flex flex-wrap items-center gap-3 rounded-xl border border-dashed border-red-200 bg-red-50/50 px-4 py-3">
                    <svg class="h-4 w-4 shrink-0 text-red-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M6 3v6a4 4 0 0 0 4 4h8m0 0-4-4m4 4-4 4"/>
                    </svg>
                    <p class="text-sm text-[#527D82]">Cabang dari <span class="font-semibold">Menunggu Keputusan</span>:</p>
                    <a href="{{ route('aktivasi', ['status' => 'Ditolak']) }}"
                       class="inline-flex items-center gap-2 rounded-full bg-white px-3 py-1 text-sm font-semibold text-red-700 ring-1 ring-red-200 transition hover:bg-red-50">
                        Ditolak <span class="rounded-full bg-red-100 px-2 text-xs">{{ $jumlahDitolak }}</span>
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">

                {{-- Posisi kontrak fisik --}}
                <div class="rounded-xl border border-[#E2EBE9] p-5">
                    <h3 class="font-bold text-[#365F65]">Posisi Kontrak Fisik</h3>
                    <p class="mt-1 text-sm text-[#7A9290]">Dari BA sampai kontrak selesai.</p>

                    <div class="mt-5 space-y-4">
                        @foreach ($perKontrakFisik as $posisi => $n)
                            <div>
                                <div class="mb-1.5 flex items-center justify-between gap-3 text-sm">
                                    <a href="{{ route('aktivasi', ['kontrak_fisik' => $posisi]) }}"
                                       class="font-semibold text-[#527D82] hover:underline">{{ $posisi }}</a>
                                    <span class="font-bold text-[#365F65]">{{ $n }}</span>
                                </div>
                                <div class="h-3 overflow-hidden rounded-full bg-[#E7EFEC]">
                                    <div class="h-full rounded-full"
                                         style="width: {{ $n > 0 ? max(4, ($n / $maksFisik) * 100) : 0 }}%; background: {{ $warnaFisik[$posisi] ?? '#8BA5A2' }}"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Kontrak sistem --}}
                <div class="rounded-xl border border-[#E2EBE9] p-5">
                    <h3 class="font-bold text-[#365F65]">Kontrak Sistem</h3>
                    <p class="mt-1 text-sm text-[#7A9290]">Aktif otomatis setelah pusat menyetujui.</p>

                    <div class="mt-5 flex flex-col items-center gap-5">
                        <x-donut :segments="$segmenSistem" :center="$persen($sistemAktif) . '%'" sub="Sudah aktif" />

                        <div class="w-full space-y-3">
                            @foreach ($segmenSistem as $seg)
                                <div class="flex items-center gap-3">
                                    <span class="h-3 w-3 shrink-0 rounded-full" style="background: {{ $seg['color'] }}"></span>
                                    <span class="flex-1 text-sm text-[#527D82]">{{ $seg['label'] }}</span>
                                    <span class="text-sm font-bold text-[#365F65]">{{ $seg['value'] }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Per terminal --}}
                <div class="rounded-xl border border-[#E2EBE9] p-5">
                    <h3 class="font-bold text-[#365F65]">Aktivasi per Terminal</h3>
                    <p class="mt-1 text-sm text-[#7A9290]">Berdasarkan lokasi tenant.</p>

                    @php $maksTerminal = max(1, (int) $perTerminal->max('total')); @endphp

                    <div class="mt-5 space-y-5">
                        @forelse ($perTerminal as $nama => $d)
                            @php $belum = $d['total'] - $d['aktif'] - $d['menunggu']; @endphp
                            <div>
                                <div class="mb-1.5 flex items-center justify-between gap-3 text-sm">
                                    <span class="font-semibold text-[#527D82]">{{ $nama }}</span>
                                    <span class="font-bold text-[#365F65]">{{ $d['total'] }} kerja sama</span>
                                </div>
                                <div class="flex h-3 overflow-hidden rounded-full bg-[#E7EFEC]"
                                     style="width: {{ max(10, ($d['total'] / $maksTerminal) * 100) }}%"
                                     role="img" aria-label="{{ $nama }}: {{ $d['aktif'] }} aktif, {{ $d['menunggu'] }} menunggu keputusan, {{ $belum }} belum">
                                    <div class="bg-emerald-600" style="width: {{ ($d['aktif'] / $d['total']) * 100 }}%"></div>
                                    <div class="bg-amber-500" style="width: {{ ($d['menunggu'] / $d['total']) * 100 }}%"></div>
                                </div>
                                <p class="mt-1 text-xs text-[#8BA5A2]">{{ $d['aktif'] }} aktif · {{ $d['menunggu'] }} menunggu · {{ $belum }} lainnya</p>
                            </div>
                        @empty
                            <p class="rounded-xl bg-[#F8FAF9] px-4 py-8 text-center text-sm text-[#7A9290]">Belum ada data.</p>
                        @endforelse
                    </div>

                    <div class="mt-5 flex items-center gap-4 border-t border-[#E2EBE9] pt-3 text-xs text-[#7A9290]">
                        <span class="inline-flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full bg-emerald-600"></span>Aktif</span>
                        <span class="inline-flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full bg-amber-500"></span>Menunggu</span>
                    </div>
                </div>
            </div>

            {{-- Segera berakhir --}}
            <div class="rounded-xl border border-[#E2EBE9] p-5">
                <h3 class="font-bold text-[#365F65]">Masa Berlaku Segera Berakhir</h3>
                <p class="mt-1 text-sm text-[#7A9290]">Kontrak sistem aktif yang berakhir dalam 90 hari ke depan atau sudah lewat.</p>

                @forelse ($segeraBerakhir as $ks)
                    @php
                        $sisa = (int) floor(now()->startOfDay()->diffInDays($ks->tanggal_berakhir->copy()->startOfDay(), false));
                        $lewat = $sisa < 0;
                        $mendesak = $sisa <= 30;
                        $totalMasa = $ks->tanggal_mulai ? max(1, $ks->tanggal_mulai->diffInDays($ks->tanggal_berakhir)) : null;
                        $berjalan = $totalMasa ? max(0, min(100, round(($ks->tanggal_mulai->diffInDays(now(), false) / $totalMasa) * 100))) : 100;
                    @endphp
                    <a href="{{ route('aktivasi.show', $ks->id) }}"
                       class="mt-4 flex flex-col gap-3 rounded-xl border border-[#E2EBE9] bg-[#F8FAF9] p-4 transition first:mt-5 hover:border-[#B6C7C4] hover:bg-white sm:flex-row sm:items-center">
                        <div class="min-w-0 sm:w-64">
                            <p class="truncate font-semibold text-[#365F65]">{{ $ks->mitra_usaha ?: '-' }}</p>
                            <p class="mt-0.5 text-xs text-[#8BA5A2]">
                                {{ $ks->brand ?: $ks->tenant?->nama_tenant ?: '-' }} · {{ $ks->lokasi?->kode_tampilan ?: 'Tanpa lokasi' }}
                            </p>
                        </div>

                        <div class="flex-1">
                            <div class="h-2.5 overflow-hidden rounded-full bg-[#E7EFEC]">
                                <div class="h-full rounded-full {{ $lewat ? 'bg-red-500' : ($mendesak ? 'bg-amber-500' : 'bg-[#365F65]') }}"
                                     style="width: {{ $berjalan }}%"></div>
                            </div>
                            <p class="mt-1 text-xs text-[#8BA5A2]">Berakhir {{ $ks->tanggal_berakhir->format('d M Y') }}</p>
                        </div>

                        <span class="inline-flex shrink-0 justify-center rounded-full px-3 py-1.5 text-xs font-semibold
                            {{ $lewat ? 'bg-red-50 text-red-700' : ($mendesak ? 'bg-amber-50 text-amber-700' : 'bg-[#EAF2F1] text-[#365F65]') }}">
                            {{ $lewat ? 'Lewat ' . abs($sisa) . ' hari' : $sisa . ' hari lagi' }}
                        </span>
                    </a>
                @empty
                    <p class="mt-5 rounded-xl bg-[#F8FAF9] px-4 py-8 text-center text-sm text-[#7A9290]">
                        Tidak ada kontrak aktif yang berakhir dalam 90 hari.
                    </p>
                @endforelse
            </div>
        </div>
    </section>

    {{-- Daftar Aktivasi --}}
    <section class="overflow-hidden rounded-2xl border border-[#E2EBE9] bg-white shadow-sm">

        <div class="border-b border-[#E2EBE9] p-5">
            <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
                <div>
                    <h2 class="font-bold text-[#365F65]">Daftar Tenant</h2>
                    <p class="mt-0.5 text-sm text-[#8BA5A2]">
                        Seluruh kerja sama beserta posisi BA/kontrak dan status aktivasinya.
                    </p>
                </div>

                <form method="GET" action="{{ route('aktivasi') }}"
                      class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:flex xl:flex-wrap xl:items-end">

                    <div class="xl:w-64">
                        <label for="search" class="mb-1 block text-xs font-semibold text-[#527D82]">Pencarian</label>
                        <input type="search" id="search" name="search" value="{{ request('search') }}"
                               placeholder="Mitra, brand, nomor…" class="{{ $kelasInput }}">
                    </div>

                    <div>
                        <label for="kontrak_fisik" class="mb-1 block text-xs font-semibold text-[#527D82]">Kontrak Fisik</label>
                        <select id="kontrak_fisik" name="kontrak_fisik" onchange="this.form.submit()" class="{{ $kelasInput }}">
                            <option value="">Semua</option>
                            @foreach ($opsiKontrakFisik as $pilihan)
                                <option value="{{ $pilihan }}" @selected(request('kontrak_fisik') === $pilihan)>{{ $pilihan }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="kontrak_sistem" class="mb-1 block text-xs font-semibold text-[#527D82]">Kontrak Sistem</label>
                        <select id="kontrak_sistem" name="kontrak_sistem" onchange="this.form.submit()" class="{{ $kelasInput }}">
                            <option value="">Semua</option>
                            @foreach ($opsiKontrakSistem as $pilihan)
                                <option value="{{ $pilihan }}" @selected(request('kontrak_sistem') === $pilihan)>{{ $pilihan }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="status" class="mb-1 block text-xs font-semibold text-[#527D82]">Aktivasi</label>
                        <select id="status" name="status" onchange="this.form.submit()" class="{{ $kelasInput }}">
                            <option value="">Semua</option>
                            @foreach ($opsiAktivasi as $pilihanStatus)
                                <option value="{{ $pilihanStatus }}" @selected(request('status') === $pilihanStatus)>{{ $pilihanStatus }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="submit"
                                class="inline-flex items-center rounded-xl bg-[#365F65] px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-[#294F55]">
                            Cari
                        </button>
                        @if ($adaFilter)
                            <a href="{{ route('aktivasi') }}"
                               class="inline-flex items-center rounded-xl border border-[#DCE6E4] px-4 py-2.5 text-sm font-semibold text-[#527D82] transition hover:bg-[#F5F8F7]">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            @if ($adaFilter)
                <div class="mt-4 flex flex-wrap items-center gap-2 border-t border-[#E2EBE9] pt-3">
                    <span class="text-xs text-[#8BA5A2]">Filter aktif:</span>
                    @foreach (['search' => 'Cari', 'kontrak_fisik' => 'Kontrak Fisik', 'kontrak_sistem' => 'Kontrak Sistem', 'status' => 'Aktivasi'] as $key => $label)
                        @if (request()->filled($key))
                            <a href="{{ request()->fullUrlWithQuery([$key => null]) }}"
                               class="inline-flex items-center gap-1.5 rounded-full bg-[#EAF2F1] px-3 py-1 text-xs font-semibold text-[#365F65] transition hover:bg-[#DCE9E7]"
                               title="Hapus filter ini">
                                {{ $label }}: {{ request($key) }} <span aria-hidden="true">×</span>
                            </a>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[1250px] text-left text-sm">
                <thead class="bg-[#F3F6F5] text-xs uppercase tracking-wide text-[#527D82]">
                    <tr>
                        <th class="px-5 py-4 font-semibold">No.</th>
                        <th class="px-5 py-4 font-semibold">Tenant / Mitra</th>
                        <th class="px-5 py-4 font-semibold">Lokasi</th>
                        <th class="px-5 py-4 font-semibold">Progres</th>
                        <th class="px-5 py-4 font-semibold">Kontrak Fisik</th>
                        <th class="px-5 py-4 font-semibold">Kontrak Sistem</th>
                        <th class="px-5 py-4 font-semibold">Aktivasi</th>
                        <th class="px-5 py-4 font-semibold">Masa Berlaku</th>
                        <th class="px-5 py-4 text-center font-semibold">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-[#E2EBE9]">
                    @forelse ($dataAktivasi as $index => $kerjaSama)
                        @php
                            $lokasi = $kerjaSama->lokasi;
                            $statusAktivasi = $kerjaSama->statusAktivasi();
                            $statusFisik = $kerjaSama->posisiKontrakFisik();
                            $statusSistem = $kerjaSama->statusKontrakSistem();

                            $tahap = $kerjaSama->tahapProgres();
                            $tahapSelesai = collect($tahap)->where('status', 'done')->count();

                            $badgeAktivasi = match ($statusAktivasi) {
                                'Disetujui' => 'bg-emerald-50 text-emerald-700',
                                'Ditolak' => 'bg-red-50 text-red-700',
                                'Menunggu Keputusan' => 'bg-amber-50 text-amber-700',
                                default => 'bg-[#F3F6F5] text-[#7A9290]',
                            };

                            $badgeFisik = match ($statusFisik) {
                                'Kontrak Selesai' => 'bg-emerald-50 text-emerald-700',
                                'Kontrak Dalam Proses' => 'bg-blue-50 text-blue-700',
                                'BA Selesai' => 'bg-teal-50 text-teal-700',
                                'BA Belum TTD' => 'bg-amber-50 text-amber-700',
                                default => 'bg-[#F3F6F5] text-[#7A9290]',
                            };

                            $badgeSistem = $statusSistem === 'Aktif'
                                ? 'bg-emerald-50 text-emerald-700'
                                : 'bg-[#F3F6F5] text-[#7A9290]';
                        @endphp

                        <tr class="transition hover:bg-[#F8FAF9]">
                            <td class="px-5 py-4 text-[#8BA5A2]">{{ $index + 1 }}</td>

                            <td class="px-5 py-4">
                                <p class="font-semibold text-[#365F65]">{{ $kerjaSama->mitra_usaha ?: '-' }}</p>
                                <p class="mt-1 text-xs text-[#7A9290]">
                                    {{ $kerjaSama->brand ?: $kerjaSama->tenant?->nama_tenant ?: '-' }}
                                    · KS-{{ str_pad($kerjaSama->id, 4, '0', STR_PAD_LEFT) }}
                                </p>
                            </td>

                            <td class="px-5 py-4 text-[#527D82]">
                                @if ($lokasi)
                                    <p class="font-medium">{{ $lokasi->terminal ?: '-' }}</p>
                                    <p class="mt-1 text-xs text-[#7A9290]">{{ $lokasi->kode_tampilan ?: $lokasi->lokasi ?: '' }}</p>
                                @else
                                    -
                                @endif
                            </td>

                            <td class="px-5 py-4">
                                <div class="flex items-center gap-2.5">
                                    <div class="flex gap-1" role="img"
                                         aria-label="Progres {{ $tahapSelesai }} dari {{ count($tahap) }} tahap">
                                        @foreach ($tahap as $t)
                                            <span class="h-2 w-5 rounded-full {{ match ($t['status']) {
                                                    'done' => 'bg-[#365F65]',
                                                    'rejected' => 'bg-red-400',
                                                    default => 'bg-[#E1EAE8]',
                                                } }}"
                                                  title="{{ $t['label'] }}: {{ match ($t['status']) {
                                                    'done' => 'selesai',
                                                    'rejected' => 'ditolak',
                                                    default => 'belum',
                                                } }}"></span>
                                        @endforeach
                                    </div>
                                    <span class="text-xs font-semibold tabular-nums text-[#527D82]">{{ $tahapSelesai }}/{{ count($tahap) }}</span>
                                </div>
                            </td>

                            <td class="px-5 py-4">
                                <span class="inline-flex whitespace-nowrap rounded-full px-3 py-1.5 text-xs font-semibold {{ $badgeFisik }}">
                                    {{ $statusFisik }}
                                </span>
                            </td>

                            <td class="px-5 py-4">
                                <span class="inline-flex whitespace-nowrap rounded-full px-3 py-1.5 text-xs font-semibold {{ $badgeSistem }}">
                                    {{ $statusSistem }}
                                </span>
                                @if ($statusSistem === 'Aktif' && $kerjaSama->tanggal_mulai)
                                    <p class="mt-1 text-xs text-[#7A9290]">Sejak {{ $kerjaSama->tanggal_mulai->format('d M Y') }}</p>
                                @endif
                            </td>

                            <td class="px-5 py-4">
                                <span class="inline-flex whitespace-nowrap rounded-full px-3 py-1.5 text-xs font-semibold {{ $badgeAktivasi }}">
                                    {{ $statusAktivasi }}
                                </span>
                            </td>

                            <td class="px-5 py-4 text-[#527D82]">
                                @if ($kerjaSama->tanggal_mulai && $kerjaSama->tanggal_berakhir)
                                    {{ $kerjaSama->tanggal_mulai->format('d M Y') }}
                                    – {{ $kerjaSama->tanggal_berakhir->format('d M Y') }}
                                @else
                                    -
                                @endif
                            </td>

                            <td class="px-5 py-4 text-center">
                                <a href="{{ route('aktivasi.show', $kerjaSama->id) }}"
                                   class="inline-block whitespace-nowrap rounded-lg bg-[#365F65] px-4 py-2 text-xs font-semibold text-white transition hover:bg-[#294F55]">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-5 py-14 text-center">
                                <p class="font-semibold text-[#365F65]">Data aktivasi belum ditemukan</p>
                                <p class="mt-1 text-sm text-[#8BA5A2]">
                                    Pastikan dokumen BA tersedia atau ubah kata kunci pencarian.
                                </p>
                                @if ($adaFilter)
                                    <a href="{{ route('aktivasi') }}" class="mt-3 inline-block text-sm font-semibold text-[#527D82] hover:underline">Reset filter</a>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="flex flex-col gap-2 border-t border-[#E2EBE9] px-5 py-4 text-sm text-[#8BA5A2] sm:flex-row sm:items-center sm:justify-between">
            <p>Menampilkan {{ $dataAktivasi->count() }} dari {{ $totalAktivasi }} kerja sama</p>
            <p>Pengajuan aktivasi baru dapat dibuat setelah BA tersedia.</p>
        </div>
    </section>

</div>
@endsection
