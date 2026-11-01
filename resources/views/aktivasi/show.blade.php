@extends('layouts.app')

@section('page-title', 'Detail Aktivasi')

@section('content')
@php
    $aktivasi = $kerjaSama->aktivasi;
    $statusAktivasi = $kerjaSama->statusAktivasi();
    $posisiFisik = $kerjaSama->posisiKontrakFisik();
    $statusSistem = $kerjaSama->statusKontrakSistem();
    $baTersedia = $kerjaSama->baTersedia();

    $statusKontrakDok = !$kontrak
        ? 'Belum Dibuat'
        : (($kontrak->status === 'Selesai' && $kontrak->final_pdf_path) ? 'Selesai (final diunggah)' : 'Dalam Proses');

    $badgeAktivasi = match ($statusAktivasi) {
        'Disetujui' => 'bg-green-50 text-green-700',
        'Ditolak' => 'bg-red-50 text-red-700',
        'Menunggu Keputusan' => 'bg-amber-50 text-amber-700',
        default => 'bg-gray-100 text-gray-600',
    };
    $badgeFisik = match ($posisiFisik) {
        'Kontrak Selesai' => 'bg-green-50 text-green-700',
        'Kontrak Dalam Proses' => 'bg-blue-50 text-blue-700',
        'BA Selesai' => 'bg-teal-50 text-teal-700',
        'BA Belum TTD' => 'bg-amber-50 text-amber-700',
        default => 'bg-gray-100 text-gray-600',
    };
    $badgeSistem = $statusSistem === 'Aktif'
        ? 'bg-green-50 text-green-700'
        : 'bg-gray-100 text-gray-600';

    $inputClass = 'w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100';
    $btnPrimary = 'rounded-xl bg-[#365F65] px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-[#294b50]';
    $btnOutline = 'rounded-xl border border-[#365F65] px-4 py-2.5 text-sm font-semibold text-[#365F65] transition hover:bg-[#EEF6F3]';
@endphp

<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <p class="text-sm text-gray-500">
                <a href="{{ route('aktivasi') }}" class="hover:underline">Management / Aktivasi</a> / Detail
            </p>
            <h1 class="mt-1 text-2xl font-bold text-gray-800">
                {{ $kerjaSama->mitra_usaha ?: '-' }}
            </h1>
            <p class="mt-1 text-sm text-gray-500">
                {{ $kerjaSama->brand ?: $tenant?->nama_tenant ?: '-' }}
                · KS-{{ str_pad($kerjaSama->id, 4, '0', STR_PAD_LEFT) }}
            </p>
        </div>

        <a href="{{ route('aktivasi') }}"
            class="rounded-xl border border-gray-200 px-4 py-2.5 text-center text-sm font-medium text-gray-600 hover:bg-gray-50">
            &larr; Kembali
        </a>
    </div>

    {{-- Notifikasi --}}
    @if (session('success'))
        <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
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

    {{-- Progres keseluruhan --}}
    @php
        $tahapProgres = $kerjaSama->tahapProgres();
        $tahapSelesai = collect($tahapProgres)->where('status', 'done')->count();
        $indeksSekarang = collect($tahapProgres)->search(fn ($t) => $t['status'] === 'todo');
    @endphp
    <section class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm" aria-label="Progres aktivasi">
        <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-sm font-bold uppercase tracking-wide text-[#365F65]">Progres Aktivasi</h2>
                <p class="mt-1 text-sm text-gray-500">Dari pembuatan BA sampai Kontrak Sistem aktif.</p>
            </div>
            <span class="text-sm font-semibold text-[#527D82]">{{ $tahapSelesai }} dari {{ count($tahapProgres) }} tahap selesai</span>
        </div>

        <ol class="mt-6 grid grid-cols-5">
            @foreach ($tahapProgres as $i => $t)
                @php
                    $selesai = $t['status'] === 'done';
                    $ditolak = $t['status'] === 'rejected';
                    $sekarang = $indeksSekarang === $i;
                @endphp
                <li class="relative flex flex-col items-center px-1 text-center" @if ($sekarang) aria-current="step" @endif>
                    @if ($i > 0)
                        <span class="absolute -left-1/2 top-4 h-0.5 w-full {{ $selesai ? 'bg-[#365F65]' : ($ditolak ? 'bg-red-300' : 'bg-[#E2EBE9]') }}" aria-hidden="true"></span>
                    @endif

                    <span class="relative z-10 flex h-8 w-8 items-center justify-center rounded-full text-xs font-bold
                        {{ $selesai ? 'bg-[#365F65] text-white'
                            : ($ditolak ? 'bg-red-500 text-white'
                            : ($sekarang ? 'bg-white text-[#365F65] ring-2 ring-[#527D82]'
                            : 'bg-[#E7EFEC] text-[#8BA5A2]')) }}">
                        @if ($selesai)
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 4 4L19 6"/></svg>
                        @elseif ($ditolak)
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"><path d="m7 7 10 10M17 7 7 17"/></svg>
                        @else
                            {{ $i + 1 }}
                        @endif
                    </span>

                    <span class="mt-2 text-[11px] font-semibold leading-tight sm:text-xs
                        {{ $selesai ? 'text-[#365F65]' : ($ditolak ? 'text-red-600' : ($sekarang ? 'text-[#527D82]' : 'text-[#8BA5A2]')) }}">
                        {{ $t['label'] }}
                    </span>

                    @if ($sekarang)
                        <span class="mt-1 rounded-full bg-[#EAF2F1] px-2 py-0.5 text-[10px] font-semibold text-[#365F65]">Berikutnya</span>
                    @elseif ($ditolak)
                        <span class="mt-1 rounded-full bg-red-50 px-2 py-0.5 text-[10px] font-semibold text-red-600">Ditolak</span>
                    @endif
                </li>
            @endforeach
        </ol>
    </section>

    {{-- Identitas, kerja sama, lokasi --}}
    <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
        <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
            <h2 class="text-sm font-bold uppercase tracking-wide text-[#365F65]">Tenant</h2>
            <dl class="mt-3 space-y-2 text-sm">
                <div><dt class="text-xs text-gray-400">Nama tenant</dt><dd class="text-gray-800">{{ $tenant?->nama_tenant ?: '-' }}</dd></div>
                <div><dt class="text-xs text-gray-400">Jenis usaha</dt><dd class="text-gray-800">{{ $tenant?->jenis_usaha ?: $kerjaSama->jenis_usaha ?: '-' }}</dd></div>
                <div><dt class="text-xs text-gray-400">PIC</dt><dd class="text-gray-800">{{ $tenant?->nama_pic ?: '-' }}@if ($tenant?->jabatan_pic) ({{ $tenant->jabatan_pic }})@endif</dd></div>
                <div><dt class="text-xs text-gray-400">Kontak</dt><dd class="text-gray-800">{{ $tenant?->no_hp_pic ?: '-' }} · {{ $tenant?->email_pic ?: '-' }}</dd></div>
            </dl>
        </div>

        <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
            <h2 class="text-sm font-bold uppercase tracking-wide text-[#365F65]">Kerja Sama</h2>
            <dl class="mt-3 space-y-2 text-sm">
                <div><dt class="text-xs text-gray-400">Bentuk kerja sama</dt><dd class="text-gray-800">{{ $kerjaSama->bentuk_kerja_sama ?: '-' }}</dd></div>
                <div><dt class="text-xs text-gray-400">Status kerja sama</dt><dd class="text-gray-800">{{ $kerjaSama->status ?: '-' }}</dd></div>
                <div>
                    <dt class="text-xs text-gray-400">Masa berlaku</dt>
                    <dd class="text-gray-800">
                        @if ($kerjaSama->tanggal_mulai && $kerjaSama->tanggal_berakhir)
                            {{ $kerjaSama->tanggal_mulai->format('d M Y') }} – {{ $kerjaSama->tanggal_berakhir->format('d M Y') }}
                        @else
                            -
                        @endif
                    </dd>
                </div>
                <div><dt class="text-xs text-gray-400">PIC Commercial</dt><dd class="text-gray-800">{{ $kerjaSama->pic_commercial ?: '-' }}</dd></div>
            </dl>
        </div>

        <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
            <h2 class="text-sm font-bold uppercase tracking-wide text-[#365F65]">Lokasi</h2>
            <dl class="mt-3 space-y-2 text-sm">
                <div><dt class="text-xs text-gray-400">Terminal</dt><dd class="text-gray-800">{{ $lokasi?->terminal ?: '-' }}</dd></div>
                <div><dt class="text-xs text-gray-400">Kode ruang / No. RO</dt><dd class="text-gray-800">{{ $lokasi?->kode_tampilan ?: '-' }} / {{ $lokasi?->nomor_ro ?: '-' }}</dd></div>
                <div><dt class="text-xs text-gray-400">Keterangan</dt><dd class="text-gray-800">{{ $lokasi?->lokasi ?: '-' }}</dd></div>
                <div><dt class="text-xs text-gray-400">Luas area</dt><dd class="text-gray-800">{{ $lokasi?->luas_area ? $lokasi->luas_area . ' m²' : '-' }}</dd></div>
            </dl>
        </div>
    </div>

    <div class="rounded-xl bg-[#EEF6F3] px-4 py-3 text-sm text-[#365F65]">
        Posisi Kontrak Fisik dihitung otomatis dari data BA dan Kontrak. Kontrak Sistem otomatis Aktif setelah Commercial Pusat menyetujui pengajuan aktivasi.
    </div>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">

        {{-- A. KONTRAK FISIK --}}
        <section class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <h2 class="text-lg font-bold text-gray-800">A. Kontrak Fisik</h2>
                    <p class="mt-1 text-xs text-gray-500">Posisi BA dan kontrak (otomatis dari data).</p>
                </div>
                <span class="inline-flex whitespace-nowrap rounded-full px-3 py-1.5 text-xs font-semibold {{ $badgeFisik }}">{{ $posisiFisik }}</span>
            </div>

            <dl class="mt-4 space-y-2 text-sm">
                <div class="flex justify-between gap-3">
                    <dt class="text-gray-400">Berita Acara (BA)</dt>
                    <dd class="font-medium {{ $kerjaSama->baFinalTersedia() ? 'text-green-700' : 'text-amber-700' }}">
                        {{ $baTersedia ? ($kerjaSama->baFinalTersedia() ? 'Selesai (final diunggah)' : 'Dibuat, belum TTD') : 'Belum dibuat' }}
                    </dd>
                </div>
                <div class="flex justify-between gap-3">
                    <dt class="text-gray-400">Kontrak</dt>
                    <dd class="font-medium text-gray-800">{{ $statusKontrakDok }}</dd>
                </div>
            </dl>

            <div class="mt-4 space-y-2 border-t border-gray-100 pt-4 text-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Dokumen</p>

                {{-- BERITA ACARA: tampilkan hanya yang ada. Final menggantikan draf. --}}
                @if ($kerjaSama->baFinalTersedia())
                    <a href="{{ route('kerja-sama.berita-acara.final', $kerjaSama->id) }}" target="_blank" rel="noopener"
                        class="block font-semibold text-[#365F65] underline">Lihat BA final</a>
                @elseif ($baTersedia)
                    <a href="{{ route('kerja-sama.berita-acara.pdf', $kerjaSama->id) }}" target="_blank" rel="noopener"
                        class="block font-semibold text-[#365F65] underline">Lampiran: Berita Acara (draf, belum TTD)</a>
                    <a href="{{ route('kerja-sama.berita-acara', $kerjaSama->id) }}" class="block text-xs font-semibold text-gray-600 underline">Unggah BA final</a>
                @else
                    <p class="text-xs text-gray-400">Berita Acara belum dibuat.</p>
                    <a href="{{ route('kerja-sama') }}" class="block text-xs font-semibold text-gray-600 underline">Buat BA di menu Kerja Sama</a>
                @endif

                {{-- KONTRAK: tampilkan hanya yang ada. Final menggantikan draf. --}}
                @if ($kontrak?->final_pdf_path)
                    <a href="{{ route('kontrak.finalPdf', $kontrak->id) }}" target="_blank" rel="noopener"
                        class="block font-semibold text-[#365F65] underline">Lihat kontrak final</a>
                @elseif ($kontrak)
                    <a href="{{ $kontrak->tanggal_dokumen ? route('kontrak.pdf', $kontrak->id) : route('kontrak.show', $kontrak->id) }}"
                        class="block font-semibold text-[#365F65] underline">Lampiran: Kontrak (draf, belum final)</a>
                @else
                    <p class="text-xs text-gray-400">Kontrak belum dibuat.</p>
                @endif

                @if ($kontrak)
                    <div class="flex flex-wrap gap-3 pt-1">
                        <a href="{{ route('kontrak.show', $kontrak->id) }}" class="text-xs font-semibold text-gray-600 underline">Buka data kontrak</a>
                        @if (!$kontrak->final_pdf_path)
                            <a href="{{ route('kontrak.uploadFinal', $kontrak->id) }}" class="text-xs font-semibold text-gray-600 underline">Unggah dokumen final</a>
                        @endif
                    </div>
                @elseif ($baTersedia)
                    <a href="{{ route('kontrak') }}" class="block text-xs font-semibold text-gray-600 underline">Buat kontrak di menu Kontrak</a>
                @endif
            </div>

            <form method="POST" action="{{ route('aktivasi.kontrak-fisik', $kerjaSama->id) }}"
                class="mt-4 space-y-3 border-t border-gray-100 pt-4">
                @csrf
                <label for="kontrak_fisik_catatan" class="block text-sm font-semibold text-gray-700">Catatan perkembangan</label>
                <textarea id="kontrak_fisik_catatan" name="kontrak_fisik_catatan" rows="3" maxlength="2000"
                    class="{{ $inputClass }}">{{ old('kontrak_fisik_catatan', $progres?->kontrak_fisik_catatan) }}</textarea>
                <p class="text-xs text-gray-400">Diperbarui: {{ $progres?->kontrak_fisik_updated_at?->format('d M Y H:i') ?? 'Belum pernah' }}</p>
                <button type="submit" class="{{ $btnPrimary }}">Simpan Catatan</button>
            </form>
        </section>

        {{-- B. KONTRAK SISTEM --}}
        <section class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <h2 class="text-lg font-bold text-gray-800">B. Kontrak Sistem</h2>
                    <p class="mt-1 text-xs text-gray-500">Kondisi kontrak di sistem operasional bandara.</p>
                </div>
                <span class="inline-flex whitespace-nowrap rounded-full px-3 py-1.5 text-xs font-semibold {{ $badgeSistem }}">{{ $statusSistem }}</span>
            </div>

            @if ($statusSistem === 'Aktif')
                <dl class="mt-4 space-y-2 text-sm">
                    <div class="flex justify-between gap-3">
                        <dt class="text-gray-400">Aktif sesuai tanggal kerja sama</dt>
                        <dd class="text-gray-800">
                            {{ $kerjaSama->tanggal_mulai?->format('d M Y') ?? '-' }}
                            @if ($kerjaSama->tanggal_berakhir)
                                – {{ $kerjaSama->tanggal_berakhir->format('d M Y') }}
                            @endif
                        </dd>
                    </div>
                    <div class="flex justify-between gap-3">
                        <dt class="text-gray-400">Disetujui pada</dt>
                        <dd class="text-gray-800">{{ $aktivasi->tanggal_keputusan?->format('d M Y') ?? '-' }}</dd>
                    </div>
                    <div class="flex justify-between gap-3">
                        <dt class="text-gray-400">Dasar</dt>
                        <dd class="text-gray-800">{{ $aktivasi->nomor_pengajuan }}</dd>
                    </div>
                </dl>
                @if ($aktivasi->dokumen_path)
                    <a href="{{ route('aktivasi.dokumen-keputusan', $aktivasi->id) }}"
                        class="mt-3 inline-block text-sm font-semibold text-green-700 underline">Unduh dokumen persetujuan</a>
                @endif
            @else
                <div class="mt-4 rounded-xl bg-gray-50 p-4 text-sm text-gray-600">
                    @if ($statusAktivasi === 'Menunggu Keputusan')
                        Menunggu keputusan Commercial Pusat. Status berubah menjadi <strong>Aktif</strong> otomatis setelah pengajuan disetujui.
                    @elseif ($statusAktivasi === 'Menunggu BA')
                        Belum dapat diproses karena Berita Acara (BA) belum dibuat.
                    @elseif ($statusAktivasi === 'Ditolak')
                        Pengajuan ditolak. Ajukan ulang agar kontrak dapat diaktifkan.
                    @else
                        Belum ada pengajuan aktivasi. Status menjadi <strong>Aktif</strong> otomatis setelah Commercial Pusat menyetujui.
                    @endif
                </div>
            @endif
        </section>

        {{-- C. AKTIVASI --}}
        <section class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <h2 class="text-lg font-bold text-gray-800">C. Aktivasi</h2>
                    <p class="mt-1 text-xs text-gray-500">Pengajuan ke Commercial Pusat dan keputusannya.</p>
                </div>
                <span class="inline-flex whitespace-nowrap rounded-full px-3 py-1.5 text-xs font-semibold {{ $badgeAktivasi }}">{{ $statusAktivasi }}</span>
            </div>

            @if ($aktivasi)
                <dl class="mt-3 space-y-1 text-sm">
                    <div class="flex justify-between gap-3"><dt class="text-gray-400">Nomor pengajuan</dt><dd class="text-gray-800">{{ $aktivasi->nomor_pengajuan ?? '-' }}</dd></div>
                    <div class="flex justify-between gap-3"><dt class="text-gray-400">Tanggal pengajuan</dt><dd class="text-gray-800">{{ $aktivasi->tanggal_pengajuan?->format('d M Y') ?? '-' }}</dd></div>
                    <div class="flex justify-between gap-3"><dt class="text-gray-400">Penandatangan</dt><dd class="text-right text-gray-800">{{ $aktivasi->nama_penandatangan ?? '-' }}</dd></div>
                    <div class="flex justify-between gap-3"><dt class="text-gray-400">Tanggal keputusan</dt><dd class="text-gray-800">{{ $aktivasi->tanggal_keputusan?->format('d M Y') ?? '-' }}</dd></div>
                </dl>

                @if ($aktivasi->catatan)
                    <p class="mt-2 rounded-lg bg-gray-50 p-3 text-sm text-gray-600">
                        <span class="font-semibold">Catatan keputusan:</span> {{ $aktivasi->catatan }}
                    </p>
                @endif

                <div class="mt-3 flex flex-wrap gap-4 text-sm">
                    @if ($aktivasi->surat_pengajuan_path)
                        <a href="{{ route('aktivasi.surat', $aktivasi->id) }}" target="_blank" rel="noopener"
                            class="font-semibold text-[#365F65] underline">Lihat surat pengajuan</a>
                    @endif
                    @if ($aktivasi->dokumen_path)
                        <a href="{{ route('aktivasi.dokumen-keputusan', $aktivasi->id) }}"
                            class="font-semibold text-green-700 underline">Unduh dokumen keputusan</a>
                    @endif
                </div>
            @else
                <p class="mt-3 text-sm text-gray-500">Belum ada pengajuan aktivasi untuk kerja sama ini.</p>
            @endif

            <div class="mt-4 border-t border-gray-100 pt-4">
                @if (!$baTersedia && (!$aktivasi || $statusAktivasi === 'Ditolak'))
                    <p class="text-sm text-gray-500">Pengajuan dapat dibuat setelah Berita Acara (BA) dibuat.</p>
                @elseif (!$aktivasi || $statusAktivasi === 'Ditolak')
                    <p class="mb-3 text-xs text-gray-500">
                        Surat pengajuan ditandatangani PIC Commercial di sistem, dengan kolom kosong untuk tanda tangan Commercial Pusat.
                        Pengiriman ke Commercial Pusat dilakukan di luar aplikasi.
                    </p>
                    <button type="button" onclick="document.getElementById('ajukan-modal').showModal()" class="{{ $btnPrimary }}">
                        {{ $statusAktivasi === 'Ditolak' ? 'Ajukan Ulang' : 'Ajukan Aktivasi' }}
                    </button>
                @elseif ($statusAktivasi === 'Menunggu Keputusan')
                    <button type="button" onclick="document.getElementById('keputusan-modal').showModal()"
                        class="{{ $btnOutline }}">Catat Keputusan Commercial Pusat</button>
                @else
                    <span class="inline-flex whitespace-nowrap rounded-lg bg-green-50 px-3 py-2 text-xs font-semibold text-green-700">
                        Keputusan terkunci
                    </span>
                    <p class="mt-2 text-xs text-gray-500">Kontrak Sistem otomatis Aktif karena pengajuan disetujui.</p>
                @endif
            </div>

            @if ($kerjaSama->aktivasis->count() > 1)
                <div class="mt-4 border-t border-gray-100 pt-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Riwayat pengajuan</p>
                    <ul class="mt-2 space-y-1 text-sm text-gray-600">
                        @foreach ($kerjaSama->aktivasis as $item)
                            <li>{{ $item->nomor_pengajuan }} · {{ $item->status }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </section>
    </div>

    {{-- Riwayat perubahan --}}
    <section class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
        <h2 class="text-lg font-bold text-gray-800">Riwayat Perubahan</h2>
        <div class="mt-3 divide-y divide-gray-100">
            @forelse ($kerjaSama->aktivasiRiwayats as $riwayat)
                <div class="flex flex-col gap-1 py-3 text-sm sm:flex-row sm:items-start sm:gap-4">
                    <span class="w-40 shrink-0 text-xs text-gray-400">{{ $riwayat->created_at->format('d M Y H:i') }}</span>
                    <span class="w-32 shrink-0 font-semibold text-[#365F65]">{{ $riwayat->komponen }}</span>
                    <span class="flex-1 text-gray-700">{{ $riwayat->keterangan }}</span>
                    <span class="text-xs text-gray-400">{{ $riwayat->user?->name ?? '-' }}</span>
                </div>
            @empty
                <p class="py-6 text-center text-sm text-gray-400">Belum ada perubahan tercatat.</p>
            @endforelse
        </div>
    </section>

    {{-- Modal pengajuan + tanda tangan PIC Commercial --}}
    @if ($baTersedia && (!$aktivasi || $statusAktivasi === 'Ditolak'))
        <dialog id="ajukan-modal"
            class="w-[calc(100%-2rem)] max-w-xl rounded-2xl p-0 shadow-2xl backdrop:bg-gray-900/40">
            <form method="POST" action="{{ route('aktivasi.request', $kerjaSama->id) }}"
                enctype="multipart/form-data" id="form-ajukan">
                @csrf

                <div class="flex items-start justify-between border-b border-gray-100 p-5 sm:p-6">
                    <div>
                        <h2 class="text-lg font-bold text-gray-800">Ajukan Aktivasi</h2>
                        <p class="mt-1 text-sm text-gray-500">Tanda tangan PIC Commercial pada surat pengajuan.</p>
                    </div>
                    <button type="button" onclick="document.getElementById('ajukan-modal').close()"
                        class="rounded-lg px-3 py-1 text-xl text-gray-400 hover:bg-gray-100" aria-label="Tutup">&times;</button>
                </div>

                <div class="max-h-[65vh] space-y-5 overflow-y-auto p-5 sm:p-6">
                    <div>
                        <label for="nama_penandatangan" class="mb-2 block text-sm font-semibold text-gray-700">Nama Penandatangan <span class="text-red-500">*</span></label>
                        <input id="nama_penandatangan" name="nama_penandatangan" required maxlength="150"
                            value="{{ old('nama_penandatangan', $kerjaSama->pic_commercial) }}" class="{{ $inputClass }}">
                    </div>
                    <div>
                        <label for="jabatan_penandatangan" class="mb-2 block text-sm font-semibold text-gray-700">Jabatan <span class="text-red-500">*</span></label>
                        <input id="jabatan_penandatangan" name="jabatan_penandatangan" required maxlength="150"
                            value="{{ old('jabatan_penandatangan', $kerjaSama->jabatan_pic_commercial) }}" class="{{ $inputClass }}">
                    </div>

                    <div>
                        <p class="mb-2 text-sm font-semibold text-gray-700">Tanda Tangan <span class="text-red-500">*</span></p>
                        <div class="mb-3 flex gap-4 text-sm">
                            <label class="flex items-center gap-2"><input type="radio" name="ttd_type" value="draw" checked onchange="ttdMode()"> Gambar langsung</label>
                            <label class="flex items-center gap-2"><input type="radio" name="ttd_type" value="upload" onchange="ttdMode()"> Unggah gambar</label>
                        </div>

                        <div id="ttd-draw">
                            <canvas id="ttd-canvas" width="500" height="180"
                                class="w-full touch-none rounded-xl border border-dashed border-gray-300 bg-white"></canvas>
                            <button type="button" onclick="ttdClear()" class="mt-2 text-xs font-semibold text-gray-500 underline">Hapus tanda tangan</button>
                            <input type="hidden" name="ttd_draw" id="ttd-draw-data">
                        </div>

                        <div id="ttd-upload" class="hidden">
                            <input type="file" name="ttd_file" accept=".png,.jpg,.jpeg"
                                class="block w-full rounded-xl border border-gray-200 p-3 text-sm file:mr-3 file:rounded-lg file:border-0 file:bg-[#EEF6F3] file:px-3 file:py-2 file:font-semibold file:text-[#365F65]">
                            <p class="mt-1 text-xs text-gray-500">PNG atau JPG, maksimal 2 MB.</p>
                        </div>
                    </div>

                    <p class="rounded-xl bg-gray-50 p-3 text-xs text-gray-600">
                        Kolom tanda tangan Commercial Pusat dibiarkan kosong pada surat dan diisi saat dokumen diproses di luar aplikasi.
                    </p>
                </div>

                <div class="flex flex-col-reverse gap-3 border-t border-gray-100 p-5 sm:flex-row sm:justify-end sm:p-6">
                    <button type="button" onclick="document.getElementById('ajukan-modal').close()"
                        class="rounded-xl border border-gray-200 px-5 py-3 text-sm font-semibold text-gray-600 hover:bg-gray-50">Batal</button>
                    <button type="submit" class="rounded-xl bg-[#365F65] px-5 py-3 text-sm font-semibold text-white hover:bg-[#294b50]">Buat Pengajuan</button>
                </div>
            </form>
        </dialog>

        <script>
            (function () {
                const canvas = document.getElementById('ttd-canvas');
                const ctx = canvas.getContext('2d');
                let drawing = false, dirty = false;
                ctx.fillStyle = '#fff'; ctx.fillRect(0, 0, canvas.width, canvas.height);
                ctx.lineWidth = 2.5; ctx.lineCap = 'round'; ctx.strokeStyle = '#111';

                function pos(e) {
                    const r = canvas.getBoundingClientRect();
                    return { x: (e.clientX - r.left) * canvas.width / r.width, y: (e.clientY - r.top) * canvas.height / r.height };
                }
                canvas.addEventListener('pointerdown', e => { drawing = true; const p = pos(e); ctx.beginPath(); ctx.moveTo(p.x, p.y); canvas.setPointerCapture(e.pointerId); });
                canvas.addEventListener('pointermove', e => { if (!drawing) return; const p = pos(e); ctx.lineTo(p.x, p.y); ctx.stroke(); dirty = true; });
                ['pointerup', 'pointercancel'].forEach(ev => canvas.addEventListener(ev, () => drawing = false));

                window.ttdClear = function () { ctx.fillStyle = '#fff'; ctx.fillRect(0, 0, canvas.width, canvas.height); dirty = false; };
                window.ttdMode = function () {
                    const upload = document.querySelector('input[name=ttd_type]:checked').value === 'upload';
                    document.getElementById('ttd-draw').classList.toggle('hidden', upload);
                    document.getElementById('ttd-upload').classList.toggle('hidden', !upload);
                };

                document.getElementById('form-ajukan').addEventListener('submit', function (e) {
                    const mode = document.querySelector('input[name=ttd_type]:checked').value;
                    if (mode === 'draw') {
                        if (!dirty) { e.preventDefault(); alert('Silakan gambar tanda tangan terlebih dahulu.'); return; }
                        document.getElementById('ttd-draw-data').value = canvas.toDataURL('image/png');
                    }
                });

                @if ($errors->any() && old('ttd_type') !== null)
                    document.getElementById('ajukan-modal').showModal();
                @endif
            })();
        </script>
    @endif

    {{-- Modal pencatatan keputusan --}}
    @if ($aktivasi && $statusAktivasi === 'Menunggu Keputusan')
        <dialog id="keputusan-modal"
            class="w-[calc(100%-2rem)] max-w-xl rounded-2xl p-0 shadow-2xl backdrop:bg-gray-900/40">
            <form method="POST" action="{{ route('aktivasi.keputusan', $aktivasi->id) }}" enctype="multipart/form-data">
                @csrf

                <div class="flex items-start justify-between border-b border-gray-100 p-5 sm:p-6">
                    <div>
                        <h2 class="text-lg font-bold text-gray-800">Catat Keputusan Aktivasi</h2>
                        <p class="mt-1 text-sm text-gray-500">{{ $aktivasi->nomor_pengajuan }}</p>
                    </div>
                    <button type="button" onclick="document.getElementById('keputusan-modal').close()"
                        class="rounded-lg px-3 py-1 text-xl text-gray-400 hover:bg-gray-100" aria-label="Tutup">&times;</button>
                </div>

                <div class="max-h-[65vh] space-y-5 overflow-y-auto p-5 sm:p-6">
                    <div class="rounded-xl bg-amber-50 p-4 text-sm text-amber-800">
                        Masukkan keputusan sesuai dokumen resmi dari kantor pusat.
                        Status tidak dapat diedit kembali setelah disimpan.
                    </div>

                    <div>
                        <label for="status-keputusan" class="mb-2 block text-sm font-semibold text-gray-700">
                            Hasil Keputusan <span class="text-red-500">*</span>
                        </label>
                        <select id="status-keputusan" name="status" required class="{{ $inputClass }}">
                            <option value="">Pilih keputusan</option>
                            <option value="Disetujui">Disetujui</option>
                            <option value="Ditolak">Ditolak</option>
                        </select>
                    </div>

                    <div>
                        <label for="tanggal-keputusan" class="mb-2 block text-sm font-semibold text-gray-700">
                            Tanggal Keputusan <span class="text-red-500">*</span>
                        </label>
                        <input id="tanggal-keputusan" type="date" name="tanggal_keputusan" required
                            max="{{ now()->toDateString() }}" class="{{ $inputClass }}">
                    </div>

                    <div>
                        <label for="dokumen-keputusan" class="mb-2 block text-sm font-semibold text-gray-700">
                            Dokumen Keputusan Resmi <span class="text-red-500">*</span>
                        </label>
                        <input id="dokumen-keputusan" type="file" name="dokumen_keputusan" required
                            accept=".pdf,.jpg,.jpeg,.png"
                            class="block w-full rounded-xl border border-gray-200 p-3 text-sm file:mr-3 file:rounded-lg file:border-0 file:bg-[#EEF6F3] file:px-3 file:py-2 file:font-semibold file:text-[#365F65]">
                        <p class="mt-1 text-xs text-gray-500">PDF, JPG, JPEG, atau PNG. Maksimal 5 MB.</p>
                    </div>

                    <div>
                        <label for="catatan-keputusan" class="mb-2 block text-sm font-semibold text-gray-700">Catatan / alasan</label>
                        <textarea id="catatan-keputusan" name="catatan" rows="3" maxlength="2000"
                            placeholder="Wajib diisi jika ditolak..."
                            class="{{ $inputClass }}"></textarea>
                    </div>
                </div>

                <div class="flex flex-col-reverse gap-3 border-t border-gray-100 p-5 sm:flex-row sm:justify-end sm:p-6">
                    <button type="button" onclick="document.getElementById('keputusan-modal').close()"
                        class="rounded-xl border border-gray-200 px-5 py-3 text-sm font-semibold text-gray-600 hover:bg-gray-50">Batal</button>
                    <button type="submit"
                        onclick="return confirm('Pastikan keputusan dan dokumen resmi sudah benar. Keputusan tidak dapat diedit setelah disimpan.');"
                        class="rounded-xl bg-[#365F65] px-5 py-3 text-sm font-semibold text-white hover:bg-[#294b50]">Simpan Keputusan</button>
                </div>
            </form>
        </dialog>
    @endif

</div>
@endsection
