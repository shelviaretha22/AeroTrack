@extends('layouts.app')

@section('content')
@php
    $tenant = $kerjaSama->tenant;
    $lokasi = $kerjaSama->lokasi;
    $kontrak = $kerjaSama->kontrakTerbaru;
    $aktivasis = $kerjaSama->aktivasis ?? collect();
    $aktivasiTerbaru = $aktivasis->sortByDesc('created_at')->first();
    $progres = $kerjaSama->progres;

    $statusLaporan = $kontrak->status
        ?? $kerjaSama->status
        ?? 'Proses';

    $statusAktif = strtolower($statusLaporan) === 'aktif';

    $formatTanggal = function ($tanggal) {
        return $tanggal
            ? \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y')
            : '-';
    };

    // Mengambil waktu modifikasi file dari storage lokal sebagai informasi waktu dokumen tersimpan.
    $infoDokumen = function ($path) {
        if (!$path) {
            return null;
        }

        $storage = \Illuminate\Support\Facades\Storage::disk('local');

        if (!$storage->exists($path)) {
            return null;
        }

        return \Carbon\Carbon::createFromTimestamp(
            $storage->lastModified($path)
        )->translatedFormat('d F Y, H:i');
    };

    $formatRupiah = function ($angka) {
        return 'Rp ' . number_format((float) $angka, 0, ',', '.');
    };

    $namaBulan = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
        4 => 'April', 5 => 'Mei', 6 => 'Juni',
        7 => 'Juli', 8 => 'Agustus', 9 => 'September',
        10 => 'Oktober', 11 => 'November', 12 => 'Desember',
    ];
@endphp

<div class="detail-laporan-page w-full min-w-0 max-w-full space-y-6 overflow-x-hidden">

    {{-- HEADER --}}
    <div>
        <a href="{{ route('laporan') }}"
           class="inline-flex items-center gap-2 text-sm font-medium text-teal-800 hover:text-teal-600">
            <span class="text-lg">←</span>
            Kembali ke Laporan
        </a>

        <div class="mt-4">
            <h1 class="text-2xl font-bold text-slate-800">
                Detail Laporan
            </h1>
            <p class="mt-1 text-sm text-slate-500">
                Informasi lengkap mengenai kerja sama dan aktivitas tenant.
            </p>
        </div>
    </div>

    {{-- RINGKASAN KERJA SAMA --}}
    <div class="overflow-hidden rounded-2xl border border-teal-100 bg-gradient-to-r from-teal-50 to-white">
        <div class="flex flex-col gap-5 p-5 lg:flex-row lg:items-center lg:justify-between">

            <div class="flex min-w-0 items-center gap-4">
                <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-xl bg-teal-100 text-3xl text-teal-800">
                    <i class="fas fa-store"></i>
                </div>

                <div class="min-w-0">
                    <h2 class="text-xl font-bold text-slate-800">
                        {{ $kerjaSama->mitra_usaha ?? $tenant->nama_tenant ?? '-' }}
                    </h2>

                    <p class="mt-1 text-sm text-slate-600">
                        {{ $kerjaSama->brand ?? '-' }}
                        <span class="mx-2 text-slate-300">|</span>
                        {{ $kerjaSama->jenis_usaha ?? '-' }}
                    </p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-5 lg:gap-8">
                <div>
                    <p class="text-xs text-slate-500">Periode Kerja Sama</p>
                    <p class="mt-1 text-sm font-semibold text-slate-700">
                        {{ $formatTanggal($kerjaSama->tanggal_mulai) }}
                        <span class="text-slate-400">–</span>
                        {{ $formatTanggal($kerjaSama->tanggal_berakhir) }}
                    </p>
                </div>

                <div class="h-10 w-px bg-teal-100 hidden lg:block"></div>

                <div>
                    <span class="inline-flex items-center gap-2 rounded-full px-4 py-2 text-sm font-semibold
                        {{ $statusAktif ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">
                        <span class="h-2 w-2 rounded-full
                            {{ $statusAktif ? 'bg-green-600' : 'bg-amber-500' }}">
                        </span>
                        {{ $statusLaporan }}
                    </span>
                </div>
            </div>

        </div>
    </div>

    {{-- NAVIGASI TAB --}}
    <div class="border-b border-slate-200">
        <div class="detail-tabs flex gap-2 overflow-x-auto">

            @php
                $tabs = [
                    'kerjasama' => ['label' => 'Kerja Sama', 'icon' => 'fa-handshake'],
                    'tenant' => ['label' => 'Tenant', 'icon' => 'fa-store'],
                    'lokasi' => ['label' => 'Lokasi', 'icon' => 'fa-map-marker-alt'],
                    'aktivasi' => ['label' => 'Aktivasi', 'icon' => 'fa-power-off'],
                    'kontrak' => ['label' => 'Kontrak', 'icon' => 'fa-file-contract'],
                    'pendapatan' => ['label' => 'Pendapatan', 'icon' => 'fa-coins'],
                ];
            @endphp

            @foreach($tabs as $key => $tab)
                <button type="button"
                    class="detail-tab-button {{ $loop->first ? 'active' : '' }}"
                    data-tab="{{ $key }}">
                    <i class="fas {{ $tab['icon'] }}"></i>
                    <span>{{ $tab['label'] }}</span>
                </button>
            @endforeach

        </div>
    </div>


    {{-- ===================================================== --}}
    {{-- TAB KERJA SAMA --}}
    {{-- ===================================================== --}}
    <section id="tab-kerjasama" class="detail-tab-content">

        <div class="rounded-2xl border border-slate-200 bg-white p-6">
                        <div class="mb-5 flex items-center gap-3 border-b border-slate-100 pb-4">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-teal-50 text-teal-800">
                    <i class="fas fa-handshake"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-slate-800">Informasi Kerja Sama</h3>
                    <p class="text-xs text-slate-500">Identitas dan periode kerja sama</p>
                </div>
            </div>

            <div class="grid gap-x-10 gap-y-5 md:grid-cols-2">

                <div class="detail-field">
    <span>Jenis Usaha</span>
    <strong>{{ $tenant->jenis_usaha ?? '-' }}</strong>
</div>

<div class="detail-field">
    <span>Bentuk Kerja Sama</span>
    <strong>{{ $tenant->bentuk_kerja_sama ?? '-' }}</strong>
</div>

                <div class="detail-field">
                    <span>NPWP</span>
                    <strong>{{ $tenant->npwp ?? '-' }}</strong>
                </div>
                <div class="detail-field">
                    <span>Jenis Usaha</span>
                    <strong>{{ $kerjaSama->jenis_usaha ?? '-' }}</strong>
                </div>

                <div class="detail-field">
                    <span>Tanggal Pengajuan</span>
                    <strong>{{ $formatTanggal($kerjaSama->tanggal_pengajuan) }}</strong>
                </div>

                <div class="detail-field">
                    <span>Status Kerja Sama</span>
                    <strong>{{ $kerjaSama->status ?? '-' }}</strong>
                </div>

                <div class="detail-field">
                    <span>Tanggal Mulai</span>
                    <strong>{{ $formatTanggal($kerjaSama->tanggal_mulai) }}</strong>
                </div>

                <div class="detail-field">
                    <span>Tanggal Berakhir</span>
                    <strong>{{ $formatTanggal($kerjaSama->tanggal_berakhir) }}</strong>
                </div>

                <div class="detail-field">
                    <span>PIC Commercial</span>
                    <strong>{{ $kerjaSama->pic_commercial ?? '-' }}</strong>
                </div>

                <div class="detail-field">
                    <span>Jabatan PIC Commercial</span>
                    <strong>{{ $kerjaSama->jabatan_pic_commercial ?? '-' }}</strong>
                </div>
            </div>

            @if($kerjaSama->catatan)
                <div class="mt-6 rounded-xl bg-slate-50 p-4">
                    <p class="mb-1 text-sm font-semibold text-slate-700">Catatan</p>
                    <p class="text-sm text-slate-600">{{ $kerjaSama->catatan }}</p>
                </div>
            @endif

            {{-- DOKUMEN BERITA ACARA --}}
            <div class="mt-6 border-t border-slate-100 pt-5">
                <h4 class="mb-4 text-sm font-semibold text-slate-800">Dokumen Berita Acara</h4>
                <div class="space-y-3">
                    <div class="document-row">
                        <div class="flex items-center gap-3">
                            <div class="document-icon"><i class="fas fa-file-pdf"></i></div>
                            <div>
                                <p class="text-sm font-semibold text-slate-700">Berita Acara Kerja Sama</p>
                                @if($kerjaSama->berita_acara_path)
                                    <p class="mt-1 text-xs text-slate-400">
                                        {{ $infoDokumen($kerjaSama->berita_acara_path) ? 'Tersimpan: '.$infoDokumen($kerjaSama->berita_acara_path) : 'File tidak ditemukan di penyimpanan.' }}
                                    </p>
                                @else
                                    <p class="mt-1 text-xs text-slate-400">Belum tersedia</p>
                                @endif
                            </div>
                        </div>
                        @if($kerjaSama->berita_acara_path)
                            <a href="{{ route('kerja-sama.berita-acara.pdf', $kerjaSama->id) }}" target="_blank" class="text-sm font-semibold text-teal-700 hover:underline">Lihat File</a>
                        @else
                            <span class="text-xs text-slate-400">Belum tersedia</span>
                        @endif
                    </div>

                    <div class="document-row">
                        <div class="flex items-center gap-3">
                            <div class="document-icon"><i class="fas fa-file-pdf"></i></div>
                            <div>
                                <p class="text-sm font-semibold text-slate-700">Berita Acara Final</p>
                                @if($kerjaSama->berita_acara_final_path)
                                    <p class="mt-1 text-xs text-slate-400">Diunggah: {{ $formatTanggal($kerjaSama->berita_acara_final_uploaded_at) }}</p>
                                @else
                                    <p class="mt-1 text-xs text-slate-400">Belum tersedia</p>
                                @endif
                            </div>
                        </div>
                        @if($kerjaSama->berita_acara_final_path)
                            <a href="{{ route('kerja-sama.berita-acara.final', $kerjaSama->id) }}" target="_blank" class="text-sm font-semibold text-teal-700 hover:underline">Lihat File</a>
                        @else
                            <span class="text-xs text-slate-400">Belum tersedia</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </section>


    {{-- ===================================================== --}}
    {{-- TAB TENANT --}}
    {{-- ===================================================== --}}
    <section id="tab-tenant" class="detail-tab-content hidden">

        <div class="rounded-2xl border border-slate-200 bg-white p-6">
            <div class="mb-5 flex items-center gap-3 border-b border-slate-100 pb-4">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-teal-50 text-teal-800">
                    <i class="fas fa-store"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-slate-800">Informasi Tenant</h3>
                    <p class="text-xs text-slate-500">Identitas perusahaan dan penanggung jawab tenant</p>
                </div>
            </div>

            @if($tenant)
                <div class="grid gap-x-10 gap-y-5 md:grid-cols-2">

                    <div class="detail-field">
                        <span>Nama Tenant</span>
                        <strong>{{ $tenant->nama_tenant ?? '-' }}</strong>
                    </div>

                    <div class="detail-field">
                        <span>Mitra Usaha</span>
                        <strong>{{ $tenant->mitra_usaha ?? $kerjaSama->mitra_usaha ?? '-' }}</strong>
                    </div>

                    <div class="detail-field">
                        <span>Jenis Usaha</span>
                        <strong>{{ $tenant->jenis_usaha ?? '-' }}</strong>
                    </div>
                                            <span>Bentuk Kerja Sama</span>
                        <strong>{{ $tenant->bentuk_kerja_sama ?? '-' }}</strong>
                    </div>

                    <div class="detail-field">
                        <span>NPWP</span>
                        <strong>{{ $tenant->npwp ?? '-' }}</strong>
                    </div>

                    <div class="detail-field">
                        <span>NIB</span>
                        <strong>{{ $tenant->nib ?? '-' }}</strong>
                    </div>

                    <div class="detail-field">
                        <span>Bentuk Badan Usaha</span>
                        <strong>{{ $tenant->bentuk_badan_usaha ?? '-' }}</strong>
                    </div>

                    <div class="detail-field">
                        <span>Alamat Perusahaan</span>
                        <strong>{{ $tenant->alamat_perusahaan ?? '-' }}</strong>
                    </div>

                    <div class="detail-field">
                        <span>Nama PIC</span>
                        <strong>{{ $tenant->nama_pic ?? '-' }}</strong>
                    </div>

                    <div class="detail-field">
                        <span>Jabatan PIC</span>
                        <strong>{{ $tenant->jabatan_pic ?? '-' }}</strong>
                    </div>

                    <div class="detail-field">
                        <span>Nomor HP PIC</span>
                        <strong>{{ $tenant->no_hp_pic ?? '-' }}</strong>
                    </div>

                    <div class="detail-field">
                        <span>Email PIC</span>
                        <strong>{{ $tenant->email_pic ?? '-' }}</strong>
                    </div>

                    <div class="detail-field">
                        <span>GTO</span>
                        <strong>{{ $tenant->gto ?? '-' }}</strong>
                    </div>

                    <div class="detail-field">
                        <span>Revenue Sharing</span>
                        <strong>{{ $tenant->revenue_sharing ?? '-' }}</strong>
                    </div>

                </div>
            @else
                <p class="py-8 text-center text-sm text-slate-500">
                    Data tenant belum tersedia.
                </p>
            @endif
        </div>

    </section>


    {{-- ===================================================== --}}
    {{-- TAB LOKASI --}}
    {{-- ===================================================== --}}
    <section id="tab-lokasi" class="detail-tab-content hidden">

        <div class="rounded-2xl border border-slate-200 bg-white p-6">
            <div class="mb-5 flex items-center gap-3 border-b border-slate-100 pb-4">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-teal-50 text-teal-800">
                    <i class="fas fa-map-marker-alt"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-slate-800">Informasi Lokasi</h3>
                    <p class="text-xs text-slate-500">Detail lokasi dan area persewaan tenant</p>
                </div>
            </div>

            @if($lokasi)
                <div class="grid gap-x-10 gap-y-5 md:grid-cols-2">

                    <div class="detail-field">
                        <span>Terminal</span>
                        <strong>{{ $lokasi->terminal ?? '-' }}</strong>
                    </div>

                    <div class="detail-field">
                        <span>Lantai</span>
                        <strong>{{ $lokasi->lantai ?? '-' }}</strong>
                    </div>

                    <div class="detail-field">
                        <span>Jenis Ruangan</span>
                        <strong>{{ $lokasi->jenis_ruangan ?? '-' }}</strong>
                    </div>

                    <div class="detail-field">
                        <span>Nomor RO</span>
                        <strong>{{ $lokasi->nomor_ro ?? '-' }}</strong>
                    </div>

                    <div class="detail-field">
                        <span>Kode Ruang</span>
                        <strong>{{ $lokasi->kode_ruang ?? '-' }}</strong>
                    </div>

                    <div class="detail-field">
                        <span>Luas Area</span>
                        <strong>
                            {{ $lokasi->luas_area !== null ? $lokasi->luas_area . ' m²' : '-' }}
                        </strong>
                    </div>

                    <div class="detail-field">
                        <span>Lokasi</span>
                        <strong>{{ $lokasi->lokasi ?? '-' }}</strong>
                    </div>

                    <div class="detail-field">
                        <span>Status Lokasi</span>
                        <strong>{{ $lokasi->status ?? '-' }}</strong>
                    </div>

                </div>

                @if($lokasi->catatan)
                    <div class="mt-6 rounded-xl bg-slate-50 p-4">
                        <p class="mb-1 text-sm font-semibold text-slate-700">Catatan Lokasi</p>
                        <p class="text-sm text-slate-600">{{ $lokasi->catatan }}</p>
                    </div>
                @endif
            @else
                <p class="py-8 text-center text-sm text-slate-500">
                    Data lokasi belum tersedia.
                </p>
            @endif
        </div>

    </section>


    {{-- ===================================================== --}}
    {{-- TAB AKTIVASI --}}
    {{-- ===================================================== --}}
    <section id="tab-aktivasi" class="detail-tab-content hidden">

        <div class="space-y-5">

            {{-- DATA PENGAJUAN --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-6">
                <h3 class="mb-5 font-semibold text-slate-800">Informasi Aktivasi</h3>
                                @if($aktivasiTerbaru)
                    <div class="grid gap-x-10 gap-y-5 md:grid-cols-2">

                        <div class="detail-field">
                            <span>Nomor Pengajuan</span>
                            <strong>{{ $aktivasiTerbaru->nomor_pengajuan ?? '-' }}</strong>
                        </div>

                        <div class="detail-field">
                            <span>Tanggal Pengajuan</span>
                            <strong>{{ $formatTanggal($aktivasiTerbaru->tanggal_pengajuan) }}</strong>
                        </div>

                        <div class="detail-field">
                            <span>Status Aktivasi</span>
                            <strong>{{ $aktivasiTerbaru->status ?? '-' }}</strong>
                        </div>

                        <div class="detail-field">
                            <span>Tanggal Keputusan</span>
                            <strong>{{ $formatTanggal($aktivasiTerbaru->tanggal_keputusan) }}</strong>
                        </div>

                    </div>
                @else
                    <p class="py-5 text-sm text-slate-500">
                        Belum terdapat pengajuan aktivasi.
                    </p>
                @endif
            </div>

            {{-- PROGRES AKTIVASI --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-6">
                <h3 class="mb-5 font-semibold text-slate-800">Progres Aktivasi</h3>

                @if($progres)
                    <div class="grid gap-6 md:grid-cols-2">

                        <div class="rounded-xl bg-slate-50 p-4">
                            <p class="text-sm font-semibold text-slate-700">Kontrak Fisik</p>
                            <p class="mt-2 text-sm text-teal-800">
                                {{ $progres->kontrak_fisik_status ?? 'Belum diperbarui' }}
                            </p>

                            @if($progres->kontrak_fisik_catatan)
                                <p class="mt-3 text-sm text-slate-500">
                                    {{ $progres->kontrak_fisik_catatan }}
                                </p>
                            @endif

                            @if($progres->kontrak_fisik_updated_at)
                                <p class="mt-3 text-xs text-slate-400">
                                    Diperbarui:
                                    {{ $formatTanggal($progres->kontrak_fisik_updated_at) }}
                                </p>
                            @endif
                        </div>

                        <div class="rounded-xl bg-slate-50 p-4">
                            <p class="text-sm font-semibold text-slate-700">Kontrak Sistem</p>
                            <p class="mt-2 text-sm text-teal-800">
                                {{ $progres->kontrak_sistem_status ?? 'Belum diperbarui' }}
                            </p>

                            @if($progres->kontrak_sistem_catatan)
                                <p class="mt-3 text-sm text-slate-500">
                                    {{ $progres->kontrak_sistem_catatan }}
                                </p>
                            @endif

                            @if($progres->tanggal_aktivasi_sistem)
                                <p class="mt-3 text-xs text-slate-400">
                                    Tanggal Aktivasi Sistem:
                                    {{ $formatTanggal($progres->tanggal_aktivasi_sistem) }}
                                </p>
                            @endif
                        </div>

                    </div>
                @else
                    <p class="py-5 text-sm text-slate-500">
                        Data progres aktivasi belum tersedia.
                    </p>
                @endif
            </div>

            {{-- RIWAYAT AKTIVASI --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-6">
                <h3 class="mb-5 font-semibold text-slate-800">Riwayat Aktivasi</h3>

                @forelse($kerjaSama->aktivasiRiwayats as $riwayat)
                    <div class="flex gap-4 border-l-2 border-teal-200 pb-5 pl-4 last:pb-0">
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-slate-700">
                                {{ $riwayat->komponen ?? 'Aktivitas' }}
                            </p>
                            <p class="mt-1 text-sm text-slate-600">
                                {{ $riwayat->keterangan ?? '-' }}
                            </p>
                            <p class="mt-2 text-xs text-slate-400">
                                {{ $riwayat->user->name ?? 'Pengguna' }}
                                ·
                                {{ $formatTanggal($riwayat->created_at) }}
                            </p>
                        </div>
                    </div>
                @empty
                    <p class="py-5 text-sm text-slate-500">
                        Belum terdapat riwayat aktivasi.
                    </p>
                @endforelse
            </div>

            {{-- DOKUMEN AKTIVASI --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-6">
                <h3 class="mb-5 font-semibold text-slate-800">Dokumen Aktivasi</h3>
                <div class="space-y-3">
                    <div class="document-row">
                        <div class="flex items-center gap-3">
                            <div class="document-icon"><i class="fas fa-file-alt"></i></div>
                            <div>
                                <p class="text-sm font-semibold text-slate-700">Surat Pengajuan Aktivasi</p>
                                @if($aktivasiTerbaru && $aktivasiTerbaru->surat_pengajuan_path)
                                    <p class="mt-1 text-xs text-slate-400">Diunggah: {{ $infoDokumen($aktivasiTerbaru->surat_pengajuan_path) ?? 'Tanggal tidak tersedia' }}</p>
                                @else
                                    <p class="mt-1 text-xs text-slate-400">Belum tersedia</p>
                                @endif
                            </div>
                        </div>
                        @if($aktivasiTerbaru && $aktivasiTerbaru->surat_pengajuan_path)
                            <a href="{{ route('aktivasi.surat', $aktivasiTerbaru->id) }}" target="_blank" class="text-sm font-semibold text-teal-700 hover:underline">Lihat File</a>
                        @else
                            <span class="text-xs text-slate-400">Belum tersedia</span>
                        @endif
                    </div>

                    <div class="document-row">
                        <div class="flex items-center gap-3">
                            <div class="document-icon"><i class="fas fa-file-alt"></i></div>
                            <div>
                                <p class="text-sm font-semibold text-slate-700">Dokumen Keputusan Aktivasi</p>
                                @if($aktivasiTerbaru && $aktivasiTerbaru->dokumen_path)
                                    <p class="mt-1 text-xs text-slate-400">Diunggah: {{ $infoDokumen($aktivasiTerbaru->dokumen_path) ?? 'Tanggal tidak tersedia' }}</p>
                                @else
                                    <p class="mt-1 text-xs text-slate-400">Belum tersedia</p>
                                @endif
                            </div>
                        </div>
                        @if($aktivasiTerbaru && $aktivasiTerbaru->dokumen_path)
                            <a href="{{ route('aktivasi.dokumen-keputusan', $aktivasiTerbaru->id) }}" target="_blank" class="text-sm font-semibold text-teal-700 hover:underline">Lihat File</a>
                        @else
                            <span class="text-xs text-slate-400">Belum tersedia</span>
                        @endif
                    </div>
                                </div>
            </div>

        </div>

    </section>


    {{-- ===================================================== --}}
{{-- TAB KONTRAK --}}
{{-- ===================================================== --}}
<section id="tab-kontrak" class="detail-tab-content hidden w-full min-w-0 max-w-full">

    <div class="w-full min-w-0 rounded-2xl border border-slate-200 bg-white p-6">

        <div class="mb-5 flex items-center gap-3 border-b border-slate-100 pb-4">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-teal-50 text-teal-800">
                <i class="fas fa-file-contract"></i>
            </div>

            <div>
                <h3 class="font-semibold text-slate-800">Informasi Kontrak</h3>
                <p class="text-xs text-slate-500">
                    Informasi dan dokumen kontrak tenant
                </p>
            </div>
        </div>

        @if($kontrak)

            <div class="grid gap-x-10 gap-y-5 md:grid-cols-2">

                <div class="detail-field">
                    <span>Nomor Kontrak</span>
                    <strong>{{ $kontrak->nomor_kontrak ?? '-' }}</strong>
                </div>

                <div class="detail-field">
                    <span>Nomor Kontrak Mitra</span>
                    <strong>{{ $kontrak->nomor_kontrak_mitra ?? '-' }}</strong>
                </div>

                <div class="detail-field">
                    <span>Tanggal Pengajuan</span>
                    <strong>{{ $formatTanggal($kontrak->tanggal_pengajuan) }}</strong>
                </div>

                <div class="detail-field">
                    <span>Tanggal Dokumen</span>
                    <strong>{{ $formatTanggal($kontrak->tanggal_dokumen) }}</strong>
                </div>

                <div class="detail-field">
                    <span>Status Kontrak</span>
                    <strong>{{ $kontrak->status ?? '-' }}</strong>
                </div>

                <div class="detail-field">
                    <span>Progress Tahap</span>
                    <strong>{{ $kontrak->progress_tahap ?? '-' }}</strong>
                </div>

                <div class="detail-field">
                    <span>Nama Penandatangan Mitra</span>
                    <strong>{{ $kontrak->nama_penandatangan_mitra ?? '-' }}</strong>
                </div>

                <div class="detail-field">
                    <span>Jabatan Penandatangan Mitra</span>
                    <strong>{{ $kontrak->jabatan_penandatangan_mitra ?? '-' }}</strong>
                </div>

                <div class="detail-field">
                    <span>Periode Evaluasi Tarif</span>
                    <strong>
                        {{ $kontrak->periode_evaluasi_tarif ?? '-' }}
                        {{ $kontrak->satuan_evaluasi_tarif ?? '' }}
                    </strong>
                </div>

                <div class="detail-field">
                    <span>Status Perpanjangan</span>
                    <strong>{{ $kontrak->status_perpanjangan ?? '-' }}</strong>
                </div>

            </div>

            {{-- DOKUMEN KONTRAK --}}
            <div class="mt-6 border-t border-slate-100 pt-5">

                <h4 class="mb-4 text-sm font-semibold text-slate-800">
                    Dokumen Kontrak
                </h4>

                <div class="document-row">

                    <div class="flex items-center gap-3">
                        <div class="document-icon">
                            <i class="fas fa-file-pdf"></i>
                        </div>

                        <div>
                            <p class="text-sm font-semibold text-slate-700">
                                Dokumen Kontrak Final
                            </p>

                            @if($kontrak->final_pdf_path)
                                <p class="mt-1 text-xs text-slate-400">
                                    Diunggah:
                                    {{ $formatTanggal($kontrak->final_uploaded_at) }}
                                </p>
                            @else
                                <p class="mt-1 text-xs text-slate-400">
                                    Belum tersedia
                                </p>
                            @endif
                        </div>
                    </div>

                    @if($kontrak->final_pdf_path)
                        <a href="{{ route('kontrak.finalPdf', $kontrak->id) }}"
                           target="_blank"
                           class="text-sm font-semibold text-teal-700 hover:underline">
                            Lihat File
                        </a>
                    @else
                        <span class="text-xs text-slate-400">
                            Belum tersedia
                        </span>
                    @endif

                </div>
            </div>

        @else

            <p class="py-8 text-center text-sm text-slate-500">
                Data kontrak belum tersedia.
            </p>

        @endif

    </div>

</section>

    {{-- ===================================================== --}}
    {{-- TAB PENDAPATAN --}}
    {{-- ===================================================== --}}
<section id="tab-pendapatan" class="detail-tab-content hidden w-full min-w-0 max-w-full">
        <div class="w-full min-w-0 rounded-2xl border border-slate-200 bg-white p-6">
            <div class="mb-5">
                <h3 class="font-semibold text-slate-800">Riwayat Pendapatan</h3>
                <p class="mt-1 text-xs text-slate-500">
                    Daftar tagihan dan status pemberian invoice.
                </p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[650px] text-left text-sm">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50 text-xs uppercase text-slate-500">
                            <th class="px-4 py-3 font-semibold">Periode</th>
                            <th class="px-4 py-3 font-semibold">Jumlah Tagihan</th>
                            <th class="px-4 py-3 font-semibold">Tanggal Diberikan</th>
                            <th class="px-4 py-3 font-semibold">Status Invoice</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @forelse($tagihan as $item)
                            <tr>
                                <td class="px-4 py-4 font-medium text-slate-700">
                                    {{ $namaBulan[(int) $item->bulan] ?? $item->bulan }}
                                    {{ $item->tahun }}
                                </td>

                                <td class="px-4 py-4 text-slate-700">
                                    {{ $formatRupiah($item->jumlah_tagihan) }}
                                </td>

                                <td class="px-4 py-4 text-slate-600">
                                    {{ $formatTanggal($item->tanggal_diberikan) }}
                                </td>
                                                                <td class="px-4 py-4">
                                    @php
                                        $statusInvoice = strtolower($item->status_invoice ?? '');
                                    @endphp

                                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold
                                        {{ $statusInvoice === 'sudah diberikan' || $statusInvoice === 'lunas'
                                            ? 'bg-green-100 text-green-700'
                                            : 'bg-amber-100 text-amber-700' }}">
                                        {{ $item->status_invoice ?? 'Belum diberikan' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-10 text-center text-sm text-slate-400">
                                    Belum terdapat data pendapatan atau tagihan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </section>

</div>


{{-- ===================================================== --}}
{{-- STYLE --}}
{{-- ===================================================== --}}
<style>
    .detail-tabs {
        scrollbar-width: thin;
    }

    .detail-tab-button {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        white-space: nowrap;
        padding: 12px 16px;
        border-bottom: 2px solid transparent;
        color: #64748b;
        font-size: 14px;
        font-weight: 600;
        transition: all .2s ease;
    }

    .detail-tab-button:hover {
        color: #0f766e;
        background: #f0fdfa;
    }

    .detail-tab-button.active {
        color: #0f766e;
        border-bottom-color: #0f766e;
    }

    .detail-tab-content {
        animation: detailFade .2s ease-in-out;
    }

    .detail-field {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .detail-field span {
        color: #94a3b8;
        font-size: 12px;
        font-weight: 500;
    }

    .detail-field strong {
        color: #334155;
        font-size: 14px;
        font-weight: 600;
        overflow-wrap: anywhere;
    }

    .document-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 14px;
    }

    .document-icon {
        display: flex;
        width: 40px;
        height: 40px;
        flex-shrink: 0;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        background: #f0fdfa;
        color: #0f766e;
    }

    @keyframes detailFade {
        from {
            opacity: 0;
            transform: translateY(4px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>


{{-- ===================================================== --}}
{{-- JAVASCRIPT TAB --}}
{{-- ===================================================== --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tabButtons = document.querySelectorAll('.detail-tab-button');
        const tabContents = document.querySelectorAll('.detail-tab-content');

        tabButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                const targetTab = this.dataset.tab;

                tabButtons.forEach(function (item) {
                    item.classList.remove('active');
                });

                tabContents.forEach(function (content) {
                    content.classList.add('hidden');
                });

                this.classList.add('active');

                const targetContent = document.getElementById('tab-' + targetTab);

                if (targetContent) {
                    targetContent.classList.remove('hidden');
                }
            });
        });
    });
</script>

@endsection