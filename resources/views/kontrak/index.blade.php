@extends('layouts.app')

@section('page-title', 'Kontrak')

@section('content')

<div class="space-y-6">

    {{-- =========================================================
     HEADER
    ========================================================== --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">

        <div>
            <p class="text-sm text-[#8BA5A2]">
                Management / Kontrak
            </p>

            <h2 class="mt-1 text-2xl font-bold text-[#365F65]">
                Data Kontrak
            </h2>

            <p class="mt-1 text-sm text-[#7A9290]">
                Kelola informasi kontrak tenant berdasarkan kerja sama yang telah dibuat.
            </p>
        </div>
        
        <div class="flex shrink-0 flex-wrap items-center gap-3">

            {{-- BUTTON LAPORAN --}}
            <a href="{{ route('kontrak.laporan') }}"
            class="inline-flex items-center justify-center gap-2 rounded-xl border border-[#DCE6E4] bg-white px-5 py-3 text-sm font-semibold text-[#365F65] transition hover:bg-[#F1F6F4]">

                <svg class="h-5 w-5"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="1.8">
                    <path stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                    <path stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M14 2v6h6M8 13h8M8 17h8"/>
                </svg>

                Laporan

            </a>


            {{-- BUTTON WHAT-IF SIMULASI --}}
            <a href="{{ route('kontrak.whatif') }}"
            class="inline-flex items-center justify-center gap-2 rounded-xl bg-[#365F65] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#527D82]">

                <svg class="h-5 w-5"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="1.8">
                    <path stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M4 21v-7m0-4V3m8 18v-9m0-4V3m8 18v-5m0-4V3"/>
                    <path stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M2 14h4m4-4h4m4 6h4"/>
                </svg>

                What-If Simulasi

            </a>

        </div>

    </div>

    {{-- =========================================================
         SUCCESS MESSAGE
    ========================================================== --}}
    @if (session('success'))
        <div
            id="successToast"
            role="status"
            class="fixed right-6 top-6 z-[9999] flex w-[calc(100%-3rem)] max-w-md items-start gap-3 rounded-xl border border-[#CFE0DC] bg-[#EEF6F3] p-4 shadow-lg"
        >
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-[#DCECE6]">
                <svg
                    class="h-5 w-5 text-[#087F73]"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M5 13l4 4L19 7"
                    />
                </svg>
            </div>

            <div class="min-w-0 flex-1">
                <h3 class="text-sm font-bold text-[#365F65]">
                    Dokumen Berhasil Disimpan
                </h3>

                <p class="mt-1 text-sm leading-relaxed text-[#55736F]">
                    {{ session('success') }}
                </p>
            </div>

            <button
                type="button"
                onclick="document.getElementById('successToast').remove()"
                class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg text-[#55736F] transition hover:bg-[#DCECE6]"
                aria-label="Tutup notifikasi"
            >
                ✕
            </button>
        </div>

        <script>
            setTimeout(function () {
                const toast = document.getElementById('successToast');

                if (toast) {
                    toast.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
                    toast.style.opacity = '0';
                    toast.style.transform = 'translateY(-10px)';

                    setTimeout(function () {
                        toast.remove();
                    }, 400);
                }
            }, 5000);
        </script>
    @endif

    @if (session('error'))
        <div
            role="alert"
            class="rounded-xl border border-[#F0D5C9] bg-[#FFF4EF] px-4 py-3 text-sm text-[#9A4F35]"
        >
            {{ session('error') }}
        </div>
    @endif

    {{-- =========================================================
         SUMMARY DASHBOARD
    ========================================================== --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-5">

        {{-- TOTAL KONTRAK --}}
    <a href="{{ route('kontrak.drilldown', ['type' => 'semua']) }}"
    class="block rounded-2xl border border-[#E2EBE9] bg-white p-5 transition hover:-translate-y-1 hover:border-[#8BA5A2] hover:shadow-md">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm font-medium text-[#7A9290]">
                    Total Kontrak
                </p>

                <h3 class="mt-3 text-3xl font-bold text-[#365F65]">
                    {{ $totalKontrak ?? 0 }}
                </h3>

                <p class="mt-2 text-xs text-[#8BA5A2]">
                    Seluruh kontrak yang tercatat
                </p>
            </div>

            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-[#E8F0EE]">
                <svg class="h-6 w-6 text-[#365F65]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                        d="M7 3.75h7l4 4v12.5H7a2 2 0 0 1-2-2v-12.5a2 2 0 0 1 2-2Z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                        d="M14 3.75v4h4M9 12h6M9 16h6"/>
                </svg>
            </div>
        </div>
    </a>

        {{-- KONTRAK AKTIF --}}
        <a href="{{ route('kontrak.drilldown', ['type' => 'status', 'value' => 'Aktif']) }}"
        class="block rounded-2xl border border-[#E2EBE9] bg-white p-5 transition hover:-translate-y-1 hover:border-[#8BA5A2] hover:shadow-md">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-[#7A9290]">
                        Kontrak Aktif
                    </p>

                    <h3 class="mt-3 text-3xl font-bold text-[#087F73]">
                        {{ $kontrakAktif ?? 0 }}
                    </h3>

                    <p class="mt-2 text-xs text-[#8BA5A2]">
                        PDF final tersedia dan belum berakhir
                    </p>
                </div>

                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-[#E5F5EF]">
                    <svg class="h-6 w-6 text-[#087F73]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m5 12 4 4L19 6"/>
                    </svg>
                </div>
            </div>
        </a>

        {{-- KONTRAK NONAKTIF --}}
        <a href="{{ route('kontrak.drilldown', ['type' => 'status', 'value' => 'Nonaktif']) }}"
        class="block rounded-2xl border border-[#E2EBE9] bg-white p-5 transition hover:-translate-y-1 hover:border-[#8BA5A2] hover:shadow-md">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-[#7A9290]">
                        Kontrak Nonaktif
                    </p>

                    <h3 class="mt-3 text-3xl font-bold text-[#A47720]">
                        {{ $kontrakNonAktif ?? 0 }}
                    </h3>

                    <p class="mt-2 text-xs text-[#8BA5A2]">
                        Masa kontrak telah berakhir
                    </p>
                </div>

                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-[#FFF4DF]">
                    <svg class="h-6 w-6 text-[#A47720]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="9" stroke-width="1.8"/>
                        <path stroke-linecap="round" stroke-width="1.8"
                              d="M12 7v5l3 2"/>
                    </svg>
                </div>
            </div>
        </a>

        {{-- KONTRAK DRAFT --}}
        <a href="{{ route('kontrak.drilldown', ['type' => 'status', 'value' => 'Draft']) }}"
        class="block rounded-2xl border border-[#E2EBE9] bg-white p-5 transition hover:-translate-y-1 hover:border-[#8BA5A2] hover:shadow-md">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-[#7A9290]">
                        Kontrak Draft
                    </p>

                    <h3 class="mt-3 text-3xl font-bold text-[#A16207]">
                        {{ $kontrakDraft ?? 0 }}
                    </h3>

                    <p class="mt-2 text-xs text-[#8BA5A2]">
                        PDF final belum diunggah
                    </p>
                </div>

                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-[#FFF0D9]">
                    <svg class="h-6 w-6 text-[#A16207]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="9" stroke-width="1.8"/>
                        <path stroke-linecap="round" stroke-width="1.8"
                              d="M12 7v5l3 2"/>
                    </svg>
                </div>
            </div>
        </a>

        {{-- KERJA SAMA BELUM DIBUATKAN KONTRAK --}}
        <a href="{{ route('kontrak.drilldown', ['type' => 'status', 'value' => 'Belum Dibuat']) }}"
        class="block rounded-2xl border border-[#E2EBE9] bg-white p-5 transition hover:-translate-y-1 hover:border-[#8BA5A2] hover:shadow-md">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-[#7A9290]">
                        Belum Dibuat
                    </p>

                    <h3 class="mt-3 text-3xl font-bold text-[#527D82]">
                        {{ $kontrakBelumDibuat ?? 0 }}
                    </h3>

                    <p class="mt-2 text-xs text-[#8BA5A2]">
                        Kerja sama belum memiliki kontrak
                    </p>
                </div>

                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-[#EDF2F1]">
                    <svg class="h-6 w-6 text-[#527D82]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                              d="M12 5v14M5 12h14"/>
                    </svg>
                </div>
            </div>
        </a>

    </div>

    {{-- =========================================================
        GRAFIK DASHBOARD KONTRAK
    ========================================================== --}}

    <div class="grid grid-cols-1 gap-5 lg:grid-cols-2 2xl:grid-cols-3">

        {{-- GRAFIK 1: KOMPOSISI STATUS KONTRAK --}}
        <div class="min-w-0 rounded-2xl border border-[#E2EBE9] bg-white p-5">

            <div class="mb-4">
                <h3 class="text-base font-bold text-[#365F65]">
                    Komposisi Status Kontrak
                </h3>

                <p class="mt-1 text-xs text-[#7A9290]">
                    Perbandingan status seluruh kontrak
                </p>
            </div>

            <div class="relative h-[250px] w-full">
                <canvas id="grafikStatusKontrak"></canvas>
            </div>

        </div>


        {{-- GRAFIK 2: STATUS PERPANJANGAN --}}
        <div class="min-w-0 rounded-2xl border border-[#E2EBE9] bg-white p-5">

            <div class="mb-4">
                <h3 class="text-base font-bold text-[#365F65]">
                    Status Perpanjangan Kontrak
                </h3>

                <p class="mt-1 text-xs text-[#7A9290]">
                    Perbandingan keputusan perpanjangan mitra
                </p>
            </div>

            <div class="relative h-[250px] w-full">
                <canvas id="grafikPerpanjanganKontrak"></canvas>
            </div>

        </div>


        {{-- GRAFIK 3: KONTRAK PER TAHUN --}}
        <div class="min-w-0 rounded-2xl border border-[#E2EBE9] bg-white p-5">

            <div class="mb-4">
                <h3 class="text-base font-bold text-[#365F65]">
                    Kontrak per Tahun
                </h3>

                <p class="mt-1 text-xs text-[#7A9290]">
                    Jumlah kontrak berdasarkan tahun pengajuan
                </p>
            </div>

            <div class="relative h-[250px] w-full">
                <canvas id="grafikKontrakTahunan"></canvas>
            </div>

        </div>

    </div>
    
{{-- =========================================================
     SIMULASI WHAT-IF KONTRAK
========================================================== --}}

<div id="kontrak-index-whatif"
     class="overflow-hidden rounded-2xl border border-[#DCE6E4] bg-white p-6">

    {{-- HEADER --}}
    <div class="mb-5 flex items-center gap-3">

        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-[#E8F0EE] text-[#527D82]">
            <svg class="h-6 w-6"
                 fill="none"
                 viewBox="0 0 24 24"
                 stroke="currentColor"
                 stroke-width="1.8">
                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      d="M4 7h9m4 0h3M4 17h3m4 0h9M13 4v6M7 14v6"/>
            </svg>
        </div>

        <div>
            <h3 class="text-base font-bold text-[#365F65]">
                Simulasi What-If
            </h3>

            <p class="mt-1 text-sm text-[#8BA5A2]">
                Uji skenario perubahan kondisi portofolio kontrak.
            </p>
        </div>

    </div>


    {{-- TIGA KOLOM SIMULASI --}}
    <div class="grid grid-cols-1 items-stretch gap-4 xl:grid-cols-3">

        {{-- KOLOM 1: PENGATURAN --}}
        <section class="flex min-w-0 flex-col rounded-xl border border-[#DCE6E4] p-5">

            <h4 class="mb-4 text-sm font-bold text-[#365F65]">
                Pengaturan Simulasi
            </h4>

            <label for="jenisSkenarioKontrak"
                   class="mb-2 block text-xs font-semibold text-[#527D82]">
                Jenis Skenario
            </label>

            <select id="jenisSkenarioKontrak"
                    class="w-full rounded-lg border border-[#DCE6E4] bg-white px-3 py-2.5 text-xs text-[#365F65] outline-none focus:border-[#527D82]">

                <option value="akhir">
                    Kontrak Mendekati Akhir
                </option>

                <option value="draft">
                    Penyelesaian Dokumen Draft
                </option>

                <option value="perpanjangan">
                    Keputusan Perpanjangan
                </option>

            </select>


            {{-- INPUT PERIODE --}}
            <div id="pengaturanPeriodeKontrak" class="mt-4">

                <label for="periodeKontrak"
                       class="mb-2 block text-xs font-semibold text-[#527D82]">
                    Periode Pemantauan
                </label>

                <select id="periodeKontrak"
                        class="w-full rounded-lg border border-[#DCE6E4] bg-white px-3 py-2.5 text-xs text-[#365F65] outline-none focus:border-[#527D82]">

                    <option value="30">30 Hari</option>
                    <option value="60">60 Hari</option>
                    <option value="90">90 Hari</option>

                </select>

            </div>


            {{-- INPUT PILIHAN DRAFT --}}
            <div id="pengaturanDraftKontrak"
                 class="mt-4 hidden">

                <div class="mb-2 flex items-center justify-between gap-2">

                    <label class="text-xs font-semibold text-[#527D82]">
                        Pilih Kontrak Draft
                    </label>

                    <button type="button"
                            id="pilihSemuaDraftKontrak"
                            class="text-[10px] font-semibold text-[#527D82] hover:underline">
                        Pilih Semua
                    </button>

                </div>

                <div id="daftarDraftKontrak"
                     class="max-h-40 space-y-2 overflow-y-auto rounded-lg border border-[#DCE6E4] p-3">

                </div>

                <p class="mt-2 text-[10px] leading-relaxed text-[#8BA5A2]">
                    Kontrak yang dipilih diasumsikan telah memiliki dokumen final.
                    Simulasi tidak melakukan upload atau menyimpan perubahan.
                </p>

            </div>


            {{-- TOMBOL --}}
            <div class="mt-auto pt-6">

                <button type="button"
                        id="jalankanWhatIfKontrak"
                        class="flex w-full items-center justify-center gap-2 rounded-lg bg-[#365F65] px-4 py-3 text-xs font-bold text-white transition hover:bg-[#2F5358]">

                    <svg class="h-4 w-4"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke="currentColor"
                         stroke-width="2">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M9 7h6m-6 4h6m-6 4h3m6 5H6a2 2 0 01-2-2V6a2 2 0 012-2h8l5 5v9a2 2 0 01-2 2z"/>
                    </svg>

                    Jalankan Simulasi

                </button>

                <button type="button"
                        id="resetWhatIfKontrak"
                        class="mt-2 w-full rounded-lg border border-[#DCE6E4] px-4 py-2.5 text-xs font-semibold text-[#527D82] transition hover:bg-[#F1F6F4]">
                    Reset Simulasi
                </button>

            </div>

        </section>


        {{-- KOLOM 2: HASIL SIMULASI --}}
        <section class="min-w-0 rounded-xl border border-[#DCE6E4] p-5">

            <h4 class="mb-1 text-sm font-bold text-[#365F65]">
                Hasil Simulasi
            </h4>

            <p class="mb-4 text-xs text-[#8BA5A2]">
                Perbandingan kondisi aktual dan hasil skenario.
            </p>

            <div id="hasilAwalWhatIfKontrak"
                 class="flex min-h-[190px] items-center justify-center rounded-lg bg-[#F8FAF9] p-5 text-center">

                <p class="text-xs leading-relaxed text-[#8BA5A2]">
                    Atur skenario, kemudian tekan
                    <strong>Jalankan Simulasi</strong>
                    untuk melihat hasil perhitungannya.
                </p>

            </div>

            <div id="hasilTabelWhatIfKontrak"
                 class="hidden">

                <div class="mb-4 grid grid-cols-2 gap-3">

                    <div class="rounded-lg bg-[#F1F6F4] p-3">
                        <p class="text-[10px] text-[#8BA5A2]">
                            Kondisi Saat Ini
                        </p>

                        <p id="nilaiAktualWhatIfKontrak"
                           class="mt-1 text-2xl font-bold text-[#365F65]">
                            0
                        </p>
                    </div>

                    <div class="rounded-lg bg-[#F1F6F4] p-3">
                        <p class="text-[10px] text-[#8BA5A2]">
                            Hasil Simulasi
                        </p>

                        <p id="nilaiHasilWhatIfKontrak"
                           class="mt-1 text-2xl font-bold text-[#087F73]">
                            0
                        </p>
                    </div>

                </div>

                <div class="overflow-x-auto">

                    <table class="w-full text-xs">

                        <thead>
                            <tr class="border-b border-[#E8EFED] text-left text-[#8BA5A2]">
                                <th class="py-3 pr-2">Keterangan</th>
                                <th class="py-3 text-right">Jumlah</th>
                            </tr>
                        </thead>

                        <tbody id="isiHasilWhatIfKontrak"
                               class="divide-y divide-[#EDF2F0]">

                        </tbody>

                    </table>

                </div>

            </div>

        </section>


        {{-- KOLOM 3: DAMPAK --}}
        <section class="min-w-0 rounded-xl border border-[#DCE6E4] bg-[#F1F7F5] p-5">

            <div class="mb-4 flex items-center gap-2">

                <svg class="h-5 w-5 text-[#527D82]"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke="currentColor"
                     stroke-width="1.8">
                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          d="M3 3v18h18M7 14l4-4 4 4 6-7"/>
                </svg>

                <h4 class="text-sm font-bold text-[#365F65]">
                    Dampak Simulasi
                </h4>

            </div>

            <ul id="dampakWhatIfKontrak"
                class="space-y-3 text-xs leading-relaxed text-[#527D82]">

                <li>
                    Jalankan simulasi untuk melihat dampak skenario terhadap
                    portofolio kontrak.
                </li>

            </ul>

        </section>

    </div>


    {{-- FOOTER --}}
    <div class="mt-5 flex flex-col gap-3 border-t border-[#EDF2F0] pt-4 sm:flex-row sm:items-center sm:justify-between">

        <p class="text-xs text-[#8BA5A2]">
            Simulasi hanya menggunakan data aktual sebagai dasar perhitungan.
            Data asli tidak diubah.
        </p>

        <a href="{{ route('kontrak.whatif') }}"
           class="inline-flex shrink-0 items-center justify-center gap-2 rounded-lg bg-[#365F65] px-5 py-3 text-xs font-semibold text-white transition hover:bg-[#2F5358]">

            Lihat Simulasi Lengkap

            <svg class="h-4 w-4"
                 fill="none"
                 viewBox="0 0 24 24"
                 stroke="currentColor"
                 stroke-width="2">
                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      d="M5 12h14m-6-6 6 6-6 6"/>
            </svg>

        </a>

    </div>

</div>


{{-- SCRIPT WHAT-IF KONTRAK --}}
<script>
document.addEventListener('DOMContentLoaded', function () {

    const dataKontrak = @json($dataWhatIfKontrak);

    const hariIni = new Date();
    hariIni.setHours(0, 0, 0, 0);

    const pilihanSkenario = document.getElementById('jenisSkenarioKontrak');
    const pilihanPeriode = document.getElementById('periodeKontrak');

    const pengaturanPeriode = document.getElementById('pengaturanPeriodeKontrak');
    const pengaturanDraft = document.getElementById('pengaturanDraftKontrak');

    const daftarDraft = document.getElementById('daftarDraftKontrak');
    const pilihSemuaDraft = document.getElementById('pilihSemuaDraftKontrak');

    const tombolHitung = document.getElementById('jalankanWhatIfKontrak');
    const tombolReset = document.getElementById('resetWhatIfKontrak');

    const hasilAwal = document.getElementById('hasilAwalWhatIfKontrak');
    const hasilTabel = document.getElementById('hasilTabelWhatIfKontrak');

    const nilaiAktual = document.getElementById('nilaiAktualWhatIfKontrak');
    const nilaiHasil = document.getElementById('nilaiHasilWhatIfKontrak');

    const isiHasil = document.getElementById('isiHasilWhatIfKontrak');
    const dampak = document.getElementById('dampakWhatIfKontrak');

    const formatAngka = nilai =>
        new Intl.NumberFormat('id-ID').format(nilai);

    const tanggalKeDate = tanggal => {
        if (!tanggal) return null;

        const bagian = tanggal.split('-').map(Number);

        return new Date(
            bagian[0],
            bagian[1] - 1,
            bagian[2]
        );
    };

    const hitungSisaHari = tanggal => {
        const akhir = tanggalKeDate(tanggal);

        if (!akhir) return null;

        return Math.ceil(
            (akhir.getTime() - hariIni.getTime()) / 86400000
        );
    };

    const buatBaris = (label, jumlah) => {

        const tr = document.createElement('tr');

        const tdLabel = document.createElement('td');
        tdLabel.className = 'py-3 pr-2 text-[#527D82]';
        tdLabel.textContent = label;

        const tdJumlah = document.createElement('td');
        tdJumlah.className = 'py-3 text-right font-semibold text-[#365F65]';
        tdJumlah.textContent = formatAngka(jumlah);

        tr.append(tdLabel, tdJumlah);

        isiHasil.appendChild(tr);
    };

    function siapkanTampilanSkenario() {

        const skenario = pilihanSkenario.value;

        pengaturanPeriode.classList.toggle(
            'hidden',
            skenario !== 'akhir'
        );

        pengaturanDraft.classList.toggle(
            'hidden',
            skenario !== 'draft'
        );
    }

    function tampilkanDampak(pesan) {

        const li = document.createElement('li');

        li.className = 'flex items-start gap-2';

        const ikon = document.createElement('span');
        ikon.className = 'font-bold text-[#527D82]';
        ikon.textContent = '✓';

        const teks = document.createElement('span');
        teks.textContent = pesan;

        li.append(ikon, teks);

        dampak.appendChild(li);
    }

    function tampilkanHasil(aktual, hasil, baris, pesanDampak) {

        hasilAwal.classList.add('hidden');
        hasilTabel.classList.remove('hidden');

        nilaiAktual.textContent = formatAngka(aktual);
        nilaiHasil.textContent = formatAngka(hasil);

        isiHasil.replaceChildren();

        baris.forEach(item => {
            buatBaris(item.label, item.jumlah);
        });

        dampak.replaceChildren();

        pesanDampak.forEach(pesan => {
            tampilkanDampak(pesan);
        });
    }

    function hitungAkhir() {

        const periode = Number(pilihanPeriode.value);

        const kontrakAkanBerakhir = dataKontrak.filter(kontrak => {

            if (kontrak.status !== 'Aktif') return false;

            const sisaHari = hitungSisaHari(kontrak.tanggal_berakhir);

            return sisaHari !== null &&
                sisaHari >= 0 &&
                sisaHari <= periode;
        });

        const jumlah = kontrakAkanBerakhir.length;

        tampilkanHasil(
            dataKontrak.filter(kontrak => kontrak.status === 'Aktif').length,
            jumlah,
            [
                {
                    label: 'Total kontrak aktif',
                    jumlah: dataKontrak.filter(kontrak => kontrak.status === 'Aktif').length
                },
                {
                    label: 'Mendekati akhir',
                    jumlah: jumlah
                },
                {
                    label: 'Kontrak lainnya',
                    jumlah: dataKontrak.filter(kontrak => kontrak.status === 'Aktif').length - jumlah
                }
            ],
            [
                jumlah > 0
                    ? jumlah + ' kontrak aktif akan berakhir dalam ' + periode + ' hari ke depan.'
                    : 'Tidak ada kontrak aktif yang akan berakhir dalam ' + periode + ' hari ke depan.',
                'Daftar kontrak tersebut dapat dijadikan prioritas untuk evaluasi dan tindak lanjut.',
                'Hasil dihitung berdasarkan tanggal berakhir kerja sama.'
            ]
        );
    }

    function hitungDraft() {

        const terpilih = Array.from(
            daftarDraft.querySelectorAll('input[type="checkbox"]:checked')
        );

        const jumlahDraftAwal = dataKontrak.filter(
            kontrak => kontrak.status === 'Draft'
        ).length;

        const jumlahDipilih = terpilih.length;

        const proyeksiAktif = terpilih.filter(input => {
            return input.dataset.proyeksi === 'Aktif';
        }).length;

        const proyeksiNonaktif = jumlahDipilih - proyeksiAktif;

        tampilkanHasil(
            jumlahDraftAwal,
            jumlahDraftAwal - jumlahDipilih,
            [
                {
                    label: 'Total kontrak Draft saat ini',
                    jumlah: jumlahDraftAwal
                },
                {
                    label: 'Draft diasumsikan selesai',
                    jumlah: jumlahDipilih
                },
                {
                    label: 'Proyeksi menjadi Aktif',
                    jumlah: proyeksiAktif
                },
                {
                    label: 'Proyeksi menjadi Nonaktif',
                    jumlah: proyeksiNonaktif
                },
                {
                    label: 'Sisa Draft',
                    jumlah: jumlahDraftAwal - jumlahDipilih
                }
            ],
            [
                jumlahDipilih + ' kontrak Draft diasumsikan telah memiliki dokumen final.',
                proyeksiAktif + ' kontrak diproyeksikan menjadi Aktif.',
                proyeksiNonaktif + ' kontrak diproyeksikan menjadi Nonaktif karena masa kerja samanya telah berakhir.',
                'Simulasi ini tidak mengunggah dokumen dan tidak mengubah status database.'
            ]
        );
    }

    function hitungPerpanjangan() {

        const ingin = dataKontrak.filter(
            kontrak => kontrak.status_perpanjangan === 'Ingin Memperpanjang'
        ).length;

        const tidak = dataKontrak.filter(
            kontrak => kontrak.status_perpanjangan === 'Tidak Memperpanjang'
        ).length;

        const belum = dataKontrak.filter(
            kontrak => kontrak.status_perpanjangan === 'Belum Dikonfirmasi'
        ).length;

        tampilkanHasil(
            dataKontrak.length,
            ingin,
            [
                {
                    label: 'Ingin Memperpanjang',
                    jumlah: ingin
                },
                {
                    label: 'Tidak Memperpanjang',
                    jumlah: tidak
                },
                {
                    label: 'Belum Dikonfirmasi',
                    jumlah: belum
                }
            ],
            [
                ingin + ' kontrak memiliki keputusan ingin memperpanjang.',
                tidak + ' kontrak memiliki keputusan tidak memperpanjang.',
                belum + ' kontrak belum memiliki konfirmasi perpanjangan.',
                'Analisis ini menggunakan keputusan perpanjangan yang sudah tersimpan.'
            ]
        );
    }

    function jalankanSimulasi() {

        const skenario = pilihanSkenario.value;

        if (skenario === 'akhir') {
            hitungAkhir();
            return;
        }

        if (skenario === 'draft') {

            const jumlahDipilih = daftarDraft.querySelectorAll(
                'input[type="checkbox"]:checked'
            ).length;

            if (jumlahDipilih === 0) {
                alert('Pilih minimal satu kontrak Draft untuk disimulasikan.');
                return;
            }

            hitungDraft();
            return;
        }

        if (skenario === 'perpanjangan') {
            hitungPerpanjangan();
        }
    }

    function resetSimulasi() {

        pilihanSkenario.value = 'akhir';
        pilihanPeriode.value = '30';

        daftarDraft.querySelectorAll(
            'input[type="checkbox"]'
        ).forEach(input => {
            input.checked = false;
        });

        hasilTabel.classList.add('hidden');
        hasilAwal.classList.remove('hidden');

        hasilAwal.querySelector('p').textContent =
            'Atur skenario, kemudian tekan Jalankan Simulasi untuk melihat hasil perhitungannya.';

        dampak.replaceChildren();

        const pesanAwal = document.createElement('li');
        pesanAwal.textContent =
            'Jalankan simulasi untuk melihat dampak skenario terhadap portofolio kontrak.';

        dampak.appendChild(pesanAwal);

        siapkanTampilanSkenario();
    }


    // Membuat daftar kontrak Draft.
    const semuaDraft = dataKontrak.filter(
        kontrak => kontrak.status === 'Draft'
    );

    if (semuaDraft.length === 0) {

        const pesan = document.createElement('p');
        pesan.className = 'text-xs text-[#8BA5A2]';
        pesan.textContent = 'Tidak ada kontrak Draft untuk disimulasikan.';

        daftarDraft.appendChild(pesan);

        pilihSemuaDraft.disabled = true;
        pilihSemuaDraft.classList.add('opacity-40', 'cursor-not-allowed');

    } else {

        semuaDraft.forEach(kontrak => {

            const label = document.createElement('label');

            label.className =
                'flex cursor-pointer items-start gap-2 rounded-md p-1.5 hover:bg-[#F8FAF9]';

            const checkbox = document.createElement('input');

            checkbox.type = 'checkbox';
            checkbox.value = kontrak.id;
            checkbox.className = 'mt-0.5 accent-[#365F65]';

            const sisaHari = hitungSisaHari(kontrak.tanggal_berakhir);

            checkbox.dataset.proyeksi =
                sisaHari !== null && sisaHari < 0
                    ? 'Nonaktif'
                    : 'Aktif';

            const teks = document.createElement('span');

            teks.className = 'text-xs text-[#527D82]';

            teks.textContent =
                kontrak.nomor_kontrak + ' - ' + kontrak.tenant;

            label.append(checkbox, teks);

            daftarDraft.appendChild(label);
        });
    }

    pilihanSkenario.addEventListener(
        'change',
        siapkanTampilanSkenario
    );

    tombolHitung.addEventListener(
        'click',
        jalankanSimulasi
    );

    tombolReset.addEventListener(
        'click',
        resetSimulasi
    );

    pilihSemuaDraft.addEventListener('click', function () {

        daftarDraft.querySelectorAll(
            'input[type="checkbox"]'
        ).forEach(input => {
            input.checked = true;
        });

    });

    siapkanTampilanSkenario();

});
</script>


    
{{-- =========================================================
     SEARCH & FILTER KONTRAK
========================================================== --}}
<div class="rounded-2xl border border-[#E2EBE9] bg-white p-5">

    <form
        id="formFilterKontrak"
        method="GET"
        action="{{ route('kontrak') }}"
        class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6"
    >

        {{-- PENCARIAN --}}
        <div class="sm:col-span-2">
            <label for="search" class="mb-1.5 block text-xs font-semibold text-[#527D82]">
                Cari Kontrak
            </label>

            <input
                type="text"
                id="search"
                name="search"
                value="{{ request('search') }}"
                placeholder="Nomor kontrak, brand, atau lokasi..."
                class="w-full rounded-xl border border-[#DCE6E4] bg-[#F8FAF9] px-4 py-3 text-sm text-[#365F65] outline-none focus:border-[#527D82]"
            >
        </div>

        {{-- PERIODE --}}
        <div>
            <label for="tahun" class="mb-1.5 block text-xs font-semibold text-[#527D82]">
                Periode
            </label>

            <select
                id="tahun"
                name="tahun"
                class="filter-otomatis w-full rounded-xl border border-[#DCE6E4] bg-[#F8FAF9] px-3 py-3 text-sm text-[#365F65]"
            >
                <option value="">Semua Tahun</option>

                @foreach ($tahunTersedia as $tahun)
                    <option value="{{ $tahun }}" {{ request('tahun') == $tahun ? 'selected' : '' }}>
                        {{ $tahun }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- TERMINAL --}}
        <div>
            <label for="terminal" class="mb-1.5 block text-xs font-semibold text-[#527D82]">
                Terminal
            </label>

            <select
                id="terminal"
                name="terminal"
                class="filter-otomatis w-full rounded-xl border border-[#DCE6E4] bg-[#F8FAF9] px-3 py-3 text-sm text-[#365F65]"
            >
                <option value="">Semua Terminal</option>

                @foreach ($terminalTersedia as $terminal)
                    <option value="{{ $terminal }}" {{ request('terminal') == $terminal ? 'selected' : '' }}>
                        {{ $terminal }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- JENIS USAHA --}}
        <div>
            <label for="jenis_usaha" class="mb-1.5 block text-xs font-semibold text-[#527D82]">
                Jenis Usaha
            </label>

            <select
                id="jenis_usaha"
                name="jenis_usaha"
                class="filter-otomatis w-full rounded-xl border border-[#DCE6E4] bg-[#F8FAF9] px-3 py-3 text-sm text-[#365F65]"
            >
                <option value="">Semua Jenis</option>

                @foreach ($jenisUsahaTersedia as $jenis)
                    <option value="{{ $jenis }}" {{ request('jenis_usaha') == $jenis ? 'selected' : '' }}>
                        {{ $jenis }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- STATUS --}}
        <div>
            <label for="status" class="mb-1.5 block text-xs font-semibold text-[#527D82]">
                Status Kontrak
            </label>

            <select
                id="status"
                name="status"
                class="filter-otomatis w-full rounded-xl border border-[#DCE6E4] bg-[#F8FAF9] px-3 py-3 text-sm text-[#365F65]"
            >
                <option value="">Semua Status</option>
                <option value="Belum Dibuat" {{ request('status') === 'Belum Dibuat' ? 'selected' : '' }}>Belum Dibuat</option>
                <option value="Draft" {{ request('status') === 'Draft' ? 'selected' : '' }}>Draft</option>
                <option value="Aktif" {{ request('status') === 'Aktif' ? 'selected' : '' }}>Aktif</option>
                <option value="Nonaktif" {{ request('status') === 'Nonaktif' ? 'selected' : '' }}>Nonaktif</option>
            </select>
        </div>

        {{-- PENGURUTAN --}}
        <div>
            <label for="sort" class="mb-1.5 block text-xs font-semibold text-[#527D82]">
                Urutkan
            </label>

            <select
                id="sort"
                name="sort"
                class="filter-otomatis w-full rounded-xl border border-[#DCE6E4] bg-[#F8FAF9] px-3 py-3 text-sm text-[#365F65]"
            >
                <option value="newest" {{ request('sort', 'newest') === 'newest' ? 'selected' : '' }}>Terbaru</option>
                <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>Terlama</option>
                <option value="az" {{ request('sort') === 'az' ? 'selected' : '' }}>Brand A-Z</option>
                <option value="za" {{ request('sort') === 'za' ? 'selected' : '' }}>Brand Z-A</option>
            </select>
        </div>

    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('formFilterKontrak');

    if (!form) return;

    // Filter langsung diterapkan saat pilihan berubah.
    form.querySelectorAll('.filter-otomatis').forEach(function (select) {
        select.addEventListener('change', function () {
            form.requestSubmit();
        });
    });

    // Pencarian dikirim saat pengguna menekan Enter.
    const inputSearch = form.querySelector('#search');

    if (inputSearch) {
        inputSearch.addEventListener('keydown', function (event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                form.requestSubmit();
            }
        });
    }
});
</script>

    {{-- =========================================================
         TABEL GABUNGAN KONTRAK DAN KANDIDAT
    ========================================================== --}}
    
@php
    $daftarKandidat = collect($kandidatKontrak ?? []);

    $daftarKontrak = collect($kontraks ?? [])
        ->sortBy(function ($kontrak) {

            $adaPdfFinal = filled($kontrak->final_pdf_path)
                && \Illuminate\Support\Facades\Storage::disk('local')
                    ->exists($kontrak->final_pdf_path);

            $tanggalAkhir = $kontrak->kerjaSama?->tanggal_berakhir;

            $isExpired = $tanggalAkhir
                ? \Carbon\Carbon::parse($tanggalAkhir)
                    ->startOfDay()
                    ->lt(now()->startOfDay())
                : false;

            if (!$adaPdfFinal) {
                return 1; // Draft
            }

            if ($isExpired) {
                return 3; // Nonaktif
            }

            return 2; // Aktif
        })
        ->values();

    $nomorBaris = 0;
@endphp


    <div class="overflow-hidden rounded-2xl border border-[#E2EBE9] bg-white">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[1500px]">

                <thead class="bg-[#F3F6F5]">
                    <tr>
                        @foreach ([
                            'No.',
                            'Nomor Kontrak',
                            'Nomor Kerja Sama',
                            'Tenant',
                            'Lokasi',
                            'Durasi',
                            'Status',
                            'Konfirmasi Perpanjangan',
                            'Note',
                            'Aksi',
                        ] as $judul)
                            <th class="px-4 py-4 text-left text-xs font-bold uppercase tracking-wide text-[#7A9290]">
                                {{ $judul }}
                            </th>
                        @endforeach
                    </tr>
                </thead>

                <tbody class="divide-y divide-[#EEF2F1]">

                    {{-- =================================================
                         KANDIDAT YANG BELUM MEMILIKI KONTRAK
                    ================================================== --}}
                    @foreach ($daftarKandidat as $kandidat)
                        @php
                            $nomorBaris++;

                            $tenantKandidat = $kandidat->tenant;
                            $lokasiKandidat = $kandidat->lokasi;

                            $tanggalMulai = $kandidat->tanggal_mulai;
                            $tanggalBerakhir = $kandidat->tanggal_berakhir;
                        @endphp

                        <tr class="transition hover:bg-[#FAFCFB]">

                            <td class="px-4 py-4 text-sm text-[#7A9290]">
                                {{ $nomorBaris }}
                            </td>

                            <td class="px-4 py-4 text-sm text-[#9AA6A4]">
                                -
                                <p class="mt-1 text-xs italic">
                                    Belum dibuat
                                </p>
                            </td>

                            <td class="px-4 py-4 text-sm font-semibold text-[#365F65]">
                                {{ $kandidat->nomor_kerja_sama ?? '-' }}
                            </td>

                            <td class="px-4 py-4">
                                <p class="text-sm font-semibold text-[#365F65]">
                                    {{ $tenantKandidat?->mitra_usaha ?? $kandidat->mitra_usaha ?? '-' }}
                                </p>

                                <p class="mt-1 text-sm text-[#527D82]">
                                    {{ $tenantKandidat?->nama_tenant ?? $kandidat->brand ?? '-' }}
                                </p>
                            </td>

                            <td class="px-4 py-4">
                                <p class="text-sm font-semibold text-[#365F65]">
                                    {{ $lokasiKandidat?->kode_ruang ?? '-' }}
                                </p>

                                <p class="mt-1 text-xs text-[#7A9290]">
                                    {{ $lokasiKandidat?->lokasi ?? '-' }}
                                </p>

                                @if ($lokasiKandidat)
                                    <p class="mt-1 text-xs text-[#8BA5A2]">
                                        Terminal {{ $lokasiKandidat->terminal ?? '-' }}
                                        - Lantai {{ $lokasiKandidat->lantai ?? '-' }}
                                    </p>
                                @endif
                            </td>

                            <td class="px-4 py-4 text-sm text-[#527D82]">
                                @if ($tanggalMulai && $tanggalBerakhir)
                                    {{ \Carbon\Carbon::parse($tanggalMulai)->format('d M Y') }}
                                    -
                                    {{ \Carbon\Carbon::parse($tanggalBerakhir)->format('d M Y') }}

                                    <p class="mt-1 text-xs text-[#8BA5A2]">
                                        ({{ \Carbon\Carbon::parse($tanggalMulai)
                                            ->diffInMonths(\Carbon\Carbon::parse($tanggalBerakhir)) }} bulan)
                                    </p>
                                @else
                                    -
                                @endif
                            </td>

                            {{-- STATUS KANDIDAT --}}
                            <td class="px-4 py-4">
                                <span class="inline-flex rounded-lg bg-[#F3F6F5] px-3 py-1.5 text-xs font-semibold text-[#527D82]">
                                    Belum Dibuat
                                </span>
                            </td>

                            {{-- KONFIRMASI PERPANJANGAN KANDIDAT --}}
                            <td class="px-4 py-4 text-sm text-[#8BA5A2]">
                                -
                            </td>

                            {{-- NOTE KANDIDAT --}}
                            <td class="px-4 py-4 text-sm text-[#527D82]">
                                Kerja sama sudah memenuhi syarat untuk dibuatkan kontrak.

                                <p class="mt-1 text-xs text-[#8BA5A2]">
                                    Aktivasi: {{ $kandidat->aktivasi?->nomor_pengajuan ?? '-' }}
                                </p>
                            </td>

                            {{-- AKSI KANDIDAT --}}
                            <td class="px-4 py-4">
                                <form
                                    method="POST"
                                    action="{{ route('kontrak.store', $kandidat->id) }}"
                                    onsubmit="return confirm('Buat kontrak untuk kerja sama ini?')"
                                >
                                    @csrf

                                    <button
                                        type="submit"
                                        class="whitespace-nowrap rounded-lg bg-[#087F73] px-3 py-2.5 text-xs font-semibold text-white transition hover:bg-[#06685F]"
                                    >
                                        Buat Kontrak
                                    </button>
                                </form>
                            </td>

                        </tr>
                    @endforeach
                                        {{-- =================================================
                         KONTRAK YANG SUDAH DIBUAT
                    ================================================== --}}
                    @foreach ($daftarKontrak as $kontrak)
                        @php
                            $nomorBaris++;

                            $kerjaSama = $kontrak->kerjaSama;
                            $tenant = $kontrak->tenant;
                            $lokasi = $kontrak->lokasi;

                            $tanggalAwal = $kerjaSama?->tanggal_mulai;
                            $tanggalAkhir = $kerjaSama?->tanggal_berakhir;

                            $isExpired = $tanggalAkhir
                                ? \Carbon\Carbon::parse($tanggalAkhir)
                                    ->startOfDay()
                                    ->lt(now()->startOfDay())
                                : false;

                            $akanHabis = $tanggalAkhir
                            ? (
                                !$isExpired &&
                                now()->startOfDay()->diffInDays(
                                    \Carbon\Carbon::parse($tanggalAkhir)->startOfDay()
                                ) <= 30
                            )
                            : false;

                            /*
                             * Status ditentukan berdasarkan keberadaan PDF final
                             * dan tanggal berakhir kerja sama.
                             */
                            $adaPdfFinal = filled($kontrak->final_pdf_path)
                                && \Illuminate\Support\Facades\Storage::disk('local')
                                    ->exists($kontrak->final_pdf_path);

                            $statusKontrak = !$adaPdfFinal
                                ? 'Draft'
                                : ($isExpired ? 'Nonaktif' : 'Aktif');

                            $batasKonfirmasi = $tanggalAkhir
                                ? \Carbon\Carbon::parse($tanggalAkhir)
                                    ->subMonthNoOverflow()
                                    ->startOfDay()
                                : null;

                            $bolehKonfirmasi = $adaPdfFinal
                                && $tanggalAkhir
                                && !$isExpired
                                && now()->startOfDay()->greaterThanOrEqualTo($batasKonfirmasi);
                                                    @endphp

                        
<tr class="{{ $statusKontrak === 'Nonaktif'
    ? 'bg-[#EEF1F0] opacity-65'
    : 'bg-white'
}} transition">


                            <td class="px-4 py-4 text-sm text-[#7A9290]">
                                {{ $nomorBaris }}
                            </td>

                            <td class="px-4 py-4">
                                <p class="text-sm font-semibold text-[#365F65]">
                                    {{ $kontrak->nomor_kontrak ?? '-' }}
                                </p>
                            </td>

                            <td class="px-4 py-4 text-sm font-semibold text-[#365F65]">
                                {{ $kerjaSama?->nomor_kerja_sama ?? '-' }}
                            </td>

                            <td class="px-4 py-4">
                                <p class="text-sm font-semibold text-[#365F65]">
                                    {{ $tenant?->mitra_usaha ?? $kerjaSama?->mitra_usaha ?? '-' }}
                                </p>

                                <p class="mt-1 text-sm text-[#527D82]">
                                    {{ $tenant?->nama_tenant ?? $kerjaSama?->brand ?? '-' }}
                                </p>

                                @if ($tenant?->nama_pic)
                                    <p class="mt-1 text-xs text-[#8BA5A2]">
                                        PIC: {{ $tenant->nama_pic }}
                                    </p>
                                @endif
                            </td>

                            <td class="px-4 py-4">
                                <p class="text-sm font-semibold text-[#365F65]">
                                    {{ $lokasi?->kode_ruang ?? '-' }}
                                </p>

                                <p class="mt-1 text-xs text-[#7A9290]">
                                    {{ $lokasi?->lokasi ?? '-' }}
                                </p>

                                @if ($lokasi)
                                    <p class="mt-1 text-xs text-[#8BA5A2]">
                                        Terminal {{ $lokasi->terminal ?? '-' }}
                                        - Lantai {{ $lokasi->lantai ?? '-' }}
                                    </p>
                                @endif
                            </td>

                            {{-- DURASI --}}
                            <td class="px-4 py-4 text-sm text-[#527D82]">
                                @if ($tanggalAwal && $tanggalAkhir)
                                    {{ \Carbon\Carbon::parse($tanggalAwal)->format('d M Y') }}
                                    -
                                    {{ \Carbon\Carbon::parse($tanggalAkhir)->format('d M Y') }}

                                    <p class="mt-1 text-xs text-[#8BA5A2]">
                                        ({{ \Carbon\Carbon::parse($tanggalAwal)
                                            ->diffInMonths(\Carbon\Carbon::parse($tanggalAkhir)) }} bulan)
                                    </p>
                                @else
                                    -
                                @endif
                            </td>

                            {{-- STATUS KONTRAK --}}
                            <td class="px-4 py-4">
                                @if ($statusKontrak === 'Aktif')
                                    <span class="inline-flex rounded-lg bg-[#D1FAE5] px-3 py-1.5 text-xs font-semibold text-[#047857]">
                                        Aktif
                                    </span>
                                @elseif ($statusKontrak === 'Nonaktif')
                                    <span class="inline-flex rounded-lg bg-[#F3F6F5] px-3 py-1.5 text-xs font-semibold text-[#64748B]">
                                        Nonaktif
                                    </span>
                                @else
                                    <span class="inline-flex rounded-lg bg-[#FFF0D9] px-3 py-1.5 text-xs font-semibold text-[#A16207]">
                                        Draft
                                    </span>
                                @endif
                            </td>

                            {{-- KONFIRMASI PERPANJANGAN --}}
                            <td class="px-4 py-4">
                                @if (!$adaPdfFinal)
                                    <span class="text-xs text-[#8BA5A2]">
                                        -
                                    </span>

                                @elseif ($kontrak->status_perpanjangan === 'Ingin Memperpanjang')
                                    <span class="inline-flex rounded-lg bg-[#D1FAE5] px-3 py-1.5 text-xs font-semibold text-[#047857]">
                                        Ingin Memperpanjang
                                    </span>

                                    @if ($kontrak->tanggal_konfirmasi)
                                        <p class="mt-1 text-xs text-[#8BA5A2]">
                                            {{ $kontrak->tanggal_konfirmasi->format('d M Y') }}
                                        </p>
                                    @endif

                                @elseif ($kontrak->status_perpanjangan === 'Tidak Memperpanjang')
                                    <span class="inline-flex rounded-lg bg-[#FEE2E2] px-3 py-1.5 text-xs font-semibold text-[#B91C1C]">
                                        Tidak Memperpanjang
                                    </span>

                                    @if ($kontrak->tanggal_konfirmasi)
                                        <p class="mt-1 text-xs text-[#8BA5A2]">
                                            {{ $kontrak->tanggal_konfirmasi->format('d M Y') }}
                                        </p>
                                    @endif

                                @elseif ($bolehKonfirmasi)
                                    <button
                                        type="button"
                                        onclick="bukaModalPerpanjangan(
                                            {{ $kontrak->id }},
                                            @js($kontrak->nomor_kontrak)
                                        )"
                                        class="whitespace-nowrap rounded-lg bg-[#365F65] px-3 py-2.5 text-xs font-semibold text-white transition hover:bg-[#294F55]"
                                    >
                                        Konfirmasi
                                    </button>

                                @else
                                    <span class="text-xs text-[#8BA5A2]">
                                        Belum waktunya
                                    </span>

                                    <p class="mt-1 text-xs text-[#8BA5A2]">
                                        Aktif mulai
                                        {{ $batasKonfirmasi?->format('d M Y') ?? '-' }}
                                    </p>
                                @endif
                            </td>

                            
{{-- NOTE --}}
<td class="px-4 py-4 text-sm text-[#527D82]">

    @if ($statusKontrak === 'Nonaktif')

        Masa kontrak telah berakhir.

    @elseif ($statusKontrak === 'Aktif' && $akanHabis)

        <span class="font-semibold text-[#A47720]">
            Kontrak akan habis.
        </span>

    @elseif ($statusKontrak === 'Aktif')

        PDF final telah diunggah dan kontrak masih berlaku.

    @else

        Dokumen kontrak belum memiliki PDF final.

    @endif

</td>


                            
{{-- AKSI --}}
<td class="px-4 py-4">

    {{-- KONTRAK SUDAH MEMILIKI PDF FINAL --}}
    @if ($adaPdfFinal)

        <a
            href="{{ route('kontrak.finalPdf', $kontrak->id) }}"
            target="_blank"
            rel="noopener noreferrer"
            class="inline-flex items-center justify-center whitespace-nowrap rounded-lg border border-[#DCE6E4] bg-white px-3 py-2.5 text-xs font-semibold text-[#365F65] transition hover:bg-[#F3F6F5]"
        >
            Detail
        </a>

    {{-- PROGRES TERAKHIR ADALAH UPLOAD DOKUMEN FINAL --}}
    @elseif ($kontrak->progress_tahap === 'upload_final')

        <a
            href="{{ route('kontrak.uploadFinal', $kontrak->id) }}"
            class="inline-flex items-center justify-center whitespace-nowrap rounded-lg bg-[#365F65] px-3 py-2.5 text-xs font-semibold text-white transition hover:bg-[#294F55]"
        >
            Lanjutkan
        </a>

    {{-- DEFAULT: KEMBALI KE ISI DOKUMEN KONTRAK --}}
    @else

        <a
            href="{{ route('kontrak.acc', $kontrak->id) }}"
            class="inline-flex items-center justify-center whitespace-nowrap rounded-lg bg-[#365F65] px-3 py-2.5 text-xs font-semibold text-white transition hover:bg-[#294F55]"
        >
            Lanjutkan
        </a>

    @endif

</td>

                        </tr>
                    @endforeach
                                        {{-- EMPTY STATE --}}
                    @if ($nomorBaris === 0)
                        <tr>
                            <td colspan="10" class="px-5 py-16 text-center">
                                <div class="mx-auto max-w-md">
                                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-[#F3F6F5]">
                                        <span class="text-xl text-[#8BA5A2]">📄</span>
                                    </div>

                                    <p class="mt-4 text-sm font-semibold text-[#527D82]">
                                        Belum ada data kontrak
                                    </p>

                                    <p class="mt-1 text-xs leading-5 text-[#8BA5A2]">
                                        Data akan muncul setelah kerja sama memenuhi
                                        syarat kontrak atau kontrak berhasil dibuat.
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endif

                </tbody>
            </table>
        </div>
    </div>

    {{-- =========================================================
         KETERANGAN
    ========================================================== --}}
    <div class="rounded-xl border border-[#DCE6E4] bg-[#F3F6F5] px-4 py-3">
    <p class="text-xs leading-5 text-[#7A9290]">
        <span class="font-semibold text-[#527D82]">
            Keterangan:
        </span>

        Kontrak yang PDF finalnya sudah tersimpan dan masa sewanya
        telah berakhir akan berstatus
        <strong>Nonaktif</strong>, terlepas dari keputusan perpanjangan mitra.

        Jika masa sewa kontrak aktif tersisa maksimal 1 bulan,
        sistem akan memberikan catatan
        <strong>Kontrak akan habis</strong>.
    </p>
</div>

</div>

{{-- DATA GRAFIK --}}
<script>
    window.dataStatusKontrak = @json($dataStatus ?? []);
    window.dataPerpanjanganKontrak = @json($dataPerpanjanganKontrak ?? []);
    window.dataKontrakTahunan = @json($dataTahunan ?? []);

    // URL tujuan drill-down dashboard kontrak
    window.urlDrilldownKontrak = @json(route('kontrak.drilldown'));
</script>

{{-- SCRIPT DRILL-DOWN GRAFIK KONTRAK --}}
<script>
document.addEventListener('DOMContentLoaded', function () {

    function pasangDrilldownGrafik() {

        if (!window.Chart || !window.urlDrilldownKontrak) {
            return false;
        }

        const konfigurasiGrafik = [
            {
                id: 'grafikStatusKontrak',
                type: 'status'
            },
            {
                id: 'grafikPerpanjanganKontrak',
                type: 'perpanjangan'
            },
            {
                id: 'grafikKontrakTahunan',
                type: 'tahun'
            }
        ];

        let semuaGrafikSiap = true;

        konfigurasiGrafik.forEach(function (item) {

            const canvas = document.getElementById(item.id);

            if (!canvas) {
                return;
            }

            const chart = window.Chart.getChart(canvas);

            if (!chart) {
                semuaGrafikSiap = false;
                return;
            }

            // Mencegah pemasangan event berulang.
            if (chart.options._drilldownSudahDipasang) {
                return;
            }

            chart.options._drilldownSudahDipasang = true;

            chart.options.onClick = function (event, elements) {

                if (!elements.length) {
                    return;
                }

                const index = elements[0].index;
                const label = chart.data.labels[index];

                if (label === undefined || label === null) {
                    return;
                }

                const url = new URL(
                    window.urlDrilldownKontrak,
                    window.location.origin
                );

                url.searchParams.set('type', item.type);
                url.searchParams.set('value', label);

                window.location.href = url.toString();
            };

            chart.options.onHover = function (event, elements) {
                canvas.style.cursor = elements.length
                    ? 'pointer'
                    : 'default';
            };

            chart.update();
        });

        return semuaGrafikSiap;
    }

    let percobaan = 0;

    function tungguGrafik() {

        percobaan++;

        const siap = pasangDrilldownGrafik();

        if (!siap && percobaan < 50) {
            setTimeout(tungguGrafik, 100);
        }
    }

    tungguGrafik();

});
</script>

{{-- =========================================================
     MODAL KONFIRMASI PERPANJANGAN
========================================================== --}}
<div
    id="modalPerpanjangan"
    class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 p-4"
    role="dialog"
    aria-modal="true"
    aria-labelledby="judulModalPerpanjangan"
>
    <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">

        <h3
            id="judulModalPerpanjangan"
            class="text-lg font-bold text-[#365F65]"
        >
            Konfirmasi Perpanjangan
        </h3>

        <p class="mt-2 text-sm text-[#7A9290]">
            Nomor kontrak:
            <span
                id="nomorKontrakModal"
                class="font-semibold text-[#365F65]"
            ></span>
        </p>

        <p class="mt-3 text-sm text-[#527D82]">
            Catat keputusan tenant setelah dikonfirmasi oleh petugas komersial.
        </p>

        <form
            id="formPerpanjangan"
            method="POST"
            class="mt-5"
        >
            @csrf

            <label
                for="status_perpanjangan"
                class="mb-2 block text-sm font-semibold text-[#365F65]"
            >
                Keputusan tenant
            </label>

            <select
                id="status_perpanjangan"
                name="status_perpanjangan"
                required
                class="w-full rounded-xl border border-[#DCE6E4] bg-[#F8FAF9] px-4 py-3 text-sm text-[#365F65] focus:border-[#527D82] focus:outline-none focus:ring-2 focus:ring-[#527D82]/10"
            >
                <option value="">Pilih keputusan</option>

                <option value="Ingin Memperpanjang">
                    Ingin Memperpanjang
                </option>

                <option value="Tidak Memperpanjang">
                    Tidak Memperpanjang
                </option>
            </select>

            <div class="mt-6 flex justify-end gap-3">
                <button
                    type="button"
                    onclick="tutupModalPerpanjangan()"
                    class="rounded-xl border border-[#DCE6E4] px-4 py-2.5 text-sm font-semibold text-[#527D82] transition hover:bg-[#F3F6F5]"
                >
                    Batal
                </button>

                <button
                    id="tombolSimpanPerpanjangan"
                    type="submit"
                    class="rounded-xl bg-[#087F73] px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-[#06685F]"
                >
                    Simpan Konfirmasi
                </button>
            </div>
        </form>
    </div>
</div>

{{-- =========================================================
     JAVASCRIPT MODAL
========================================================== --}}
<script>
    function bukaModalPerpanjangan(id, nomorKontrak) {
        const modal = document.getElementById('modalPerpanjangan');
        const form = document.getElementById('formPerpanjangan');
        const pilihan = document.getElementById('status_perpanjangan');

        form.action = `{{ url('/kontrak') }}/${id}/konfirmasi-perpanjangan`;

        document.getElementById('nomorKontrakModal').textContent = nomorKontrak;
        pilihan.value = '';

        modal.classList.remove('hidden');
        modal.classList.add('flex');

        pilihan.focus();
    }

    function tutupModalPerpanjangan() {
        const modal = document.getElementById('modalPerpanjangan');

        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            tutupModalPerpanjangan();
        }
    });

    document.getElementById('modalPerpanjangan').addEventListener('click', function (event) {
        if (event.target === this) {
            tutupModalPerpanjangan();
        }
    });
</script>

@endsection