
@extends('layouts.app')

@section('page-title', 'What-If Kontrak')

@section('content')

@php
    $hariIni = now()->startOfDay();

    $formatTanggal = function ($tanggal) {
        return $tanggal
            ? \Carbon\Carbon::parse($tanggal)->format('d/m/Y')
            : '-';
    };
@endphp

<style>
    #kontrak-whatif-page {
        width: 100%;
        min-width: 0;
    }

    #kontrak-whatif-page .whatif-card {
        min-width: 0;
        padding: 20px;
        border: 1px solid #DCE6E4;
        border-radius: 15px;
        background: white;
    }

    #kontrak-whatif-page .whatif-table-wrap {
        width: 100%;
        overflow-x: auto;
    }

    #kontrak-whatif-page .whatif-table {
        width: 100%;
        min-width: 750px;
        border-collapse: collapse;
    }

    #kontrak-whatif-page .whatif-table th {
        padding: 12px;
        background: #F1F6F4;
        color: #527D82;
        font-size: 11px;
        text-align: left;
        white-space: nowrap;
    }

    #kontrak-whatif-page .whatif-table td {
        padding: 12px;
        border-bottom: 1px solid #EDF2F0;
        color: #527D82;
        font-size: 12px;
        vertical-align: middle;
    }

    #kontrak-whatif-page .whatif-select {
        border: 1px solid #DCE6E4;
        border-radius: 9px;
        background: white;
        padding: 9px 12px;
        color: #365F65;
        font-size: 12px;
        outline: none;
    }

    #kontrak-whatif-page .whatif-checkbox {
        width: 16px;
        height: 16px;
        accent-color: #365F65;
        cursor: pointer;
    }

    #kontrak-whatif-page .whatif-stat {
        border: 1px solid #DCE6E4;
        border-radius: 12px;
        background: #F8FAF9;
        padding: 15px;
    }

    #kontrak-whatif-page .whatif-stat-value {
        margin-top: 6px;
        font-size: 25px;
        font-weight: 700;
        color: #365F65;
    }
</style>

<div id="kontrak-whatif-page">

    {{-- HEADER --}}
    <div class="mb-6">

        <a href="{{ route('kontrak') }}"
           class="mb-4 inline-flex items-center gap-2 text-sm font-medium text-[#527D82] hover:text-[#365F65]">

            <svg class="h-4 w-4"
                 fill="none"
                 viewBox="0 0 24 24"
                 stroke="currentColor"
                 stroke-width="2">
                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      d="M15 19l-7-7 7-7"/>
            </svg>

            Kembali ke Kontrak

        </a>

        <h2 class="text-2xl font-bold text-[#365F65]">
            What-If Analysis Kontrak
        </h2>

        <p class="mt-1 text-sm text-[#8BA5A2]">
            Simulasi kondisi kontrak untuk membantu analisis masa kerja sama,
            penyelesaian dokumen, dan keputusan perpanjangan.
        </p>

    </div>


    {{-- INFORMASI SIMULASI --}}
    <div class="mb-6 rounded-xl border border-[#DCE6E4] bg-[#F1F6F4] p-4">

        <div class="flex items-start gap-3">

            <div class="rounded-lg bg-white p-2 text-[#527D82]">
                <svg class="h-5 w-5"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke="currentColor"
                     stroke-width="1.8">
                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          d="M12 9v3m0 4h.01M10.3 3.86L2.6 17.2A2 2 0 004.3 20h15.4a2 2 0 001.7-2.8L13.7 3.86a2 2 0 00-3.4 0z"/>
                </svg>
            </div>

            <div>
                <p class="text-sm font-bold text-[#365F65]">
                    Mode Simulasi
                </p>

                <p class="mt-1 text-xs leading-relaxed text-[#527D82]">
                    Hasil What-If merupakan simulasi berdasarkan data kontrak
                    yang tersimpan. Perubahan pilihan simulasi tidak mengubah
                    data kontrak maupun database AeroTrack.
                </p>
            </div>

        </div>

    </div>


    {{-- =========================================================
         SIMULASI 1: KONTRAK MENDEKATI AKHIR
    ========================================================== --}}
    <section class="whatif-card mb-6">

        <div class="mb-5 flex flex-wrap items-center justify-between gap-3">

            <div>
                <h3 class="text-base font-bold text-[#365F65]">
                    1. Simulasi Kontrak Mendekati Akhir
                </h3>

                <p class="mt-1 text-xs text-[#8BA5A2]">
                    Identifikasi kontrak yang akan berakhir dalam periode tertentu.
                </p>
            </div>

            <form action="{{ route('kontrak.whatif') }}" method="GET"
      class="flex flex-wrap items-center gap-2">

    <label for="periode"
           class="text-xs font-semibold text-[#527D82]">
        Periode:
    </label>

    <select name="periode"
            id="periode"
            class="whatif-select">

        <option value="30" {{ $periode == 30 ? 'selected' : '' }}>
            30 Hari
        </option>

        <option value="60" {{ $periode == 60 ? 'selected' : '' }}>
            60 Hari
        </option>

        <option value="90" {{ $periode == 90 ? 'selected' : '' }}>
            90 Hari
        </option>

    </select>

    <button type="submit"
            class="rounded-lg bg-[#365F65] px-4 py-2 text-xs font-bold text-white hover:bg-[#294B50]">
        Jalankan Simulasi
    </button>

</form>
        </div>

        <div class="mb-5 grid grid-cols-1 gap-3 sm:grid-cols-3">

            <div class="whatif-stat">
                <p class="text-xs text-[#8BA5A2]">
                    Periode Simulasi
                </p>

                <p class="whatif-stat-value">
                    {{ $periode }} Hari
                </p>
            </div>

            <div class="whatif-stat">
                <p class="text-xs text-[#8BA5A2]">
                    Kontrak Mendekati Akhir
                </p>

                <p class="whatif-stat-value">
                    {{ $kontrakMendekatiAkhir->count() }}
                </p>
            </div>

            <div class="whatif-stat">
                <p class="text-xs text-[#8BA5A2]">
                    Tanggal Batas Simulasi
                </p>

                <p class="mt-3 text-lg font-bold text-[#365F65]">
                    {{ $hariIni->copy()->addDays($periode)->format('d/m/Y') }}
                </p>
            </div>

        </div>

        <div class="whatif-table-wrap">

            <table class="whatif-table">

                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Nomor Kontrak</th>
                        <th>Mitra Usaha</th>
                        <th>Nama Tenant</th>
                        <th>Tanggal Berakhir</th>
                        <th>Sisa Hari</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse ($kontrakMendekatiAkhir as $index => $kontrak)

                        @php
                            $tanggalBerakhir = \Carbon\Carbon::parse(
                                $kontrak->kerjaSama->tanggal_berakhir
                            )->startOfDay();

                            $sisaHari = (int) $hariIni->diffInDays(
                                $tanggalBerakhir,
                                false
                            );
                        @endphp

                        <tr>
                            <td>{{ $index + 1 }}</td>

                            <td class="font-semibold">
                                {{ $kontrak->nomor_kontrak ?: '-' }}
                            </td>

                            <td>
                                {{ $kontrak->nama_mitra_snapshot
                                    ?: $kontrak->kerjaSama?->mitra_usaha
                                    ?: '-' }}
                            </td>

                            <td>
                                {{ $kontrak->tenant?->nama_tenant ?: '-' }}
                            </td>

                            <td>
                                {{ $formatTanggal($kontrak->kerjaSama?->tanggal_berakhir) }}
                            </td>

                            <td>
                                <span class="rounded-full bg-[#FFF6E8] px-3 py-1 text-xs font-bold text-[#A8782E]">
                                    {{ $sisaHari }} Hari
                                </span>
                            </td>
                        </tr>

                    @empty

                        <tr>
                            <td colspan="6" class="py-8 text-center text-[#9AAEAB]">
                                Tidak ada kontrak yang akan berakhir dalam
                                {{ $periode }} hari ke depan.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </section>


    {{-- =========================================================
         SIMULASI 2: PENYELESAIAN DRAFT
    ========================================================== --}}
    <section class="whatif-card mb-6">

        <div class="mb-5">

            <h3 class="text-base font-bold text-[#365F65]">
                2. Simulasi Penyelesaian Dokumen Draft
            </h3>

            <p class="mt-1 text-xs leading-relaxed text-[#8BA5A2]">
                Pilih kontrak Draft yang diasumsikan telah memiliki dokumen final.
                Sistem akan menghitung proyeksi status setelah dokumen tersedia.
            </p>

        </div>

        <div class="mb-5 grid grid-cols-1 gap-3 sm:grid-cols-3">

            <div class="whatif-stat">
                <p class="text-xs text-[#8BA5A2]">
                    Total Kontrak Draft
                </p>

                <p class="whatif-stat-value">
                    {{ $totalDraft }}
                </p>
            </div>

            <div class="whatif-stat">
                <p class="text-xs text-[#8BA5A2]">
                    Draft Dipilih
                </p>

                <p id="draftTerpilih" class="whatif-stat-value">
                    0
                </p>
            </div>

            <div class="whatif-stat">
                <p class="text-xs text-[#8BA5A2]">
                    Proyeksi Kontrak Aktif
                </p>

                <p id="proyeksiAktif" class="whatif-stat-value">
                    0
                </p>
            </div>

        </div>

        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">

    <p class="text-xs font-semibold text-[#527D82]">
        Pilih kontrak yang ingin disimulasikan:
    </p>

    <div class="flex flex-wrap gap-2">

        <button type="button"
                id="hitungSimulasiDraft"
                class="rounded-lg bg-[#365F65] px-4 py-2 text-xs font-bold text-white hover:bg-[#294B50]">
            Hitung What-If
        </button>

        <button type="button"
                id="resetSimulasiDraft"
                class="rounded-lg border border-[#DCE6E4] px-4 py-2 text-xs font-bold text-[#527D82] hover:bg-[#F1F6F4]">
            Reset Pilihan
        </button>

    </div>

</div>
        <div class="whatif-table-wrap">

            <table class="whatif-table">

                <thead>
                    <tr>
                        <th>Pilih</th>
                        <th>Nomor Kontrak</th>
                        <th>Mitra Usaha</th>
                        <th>Nama Tenant</th>
                        <th>Tanggal Berakhir</th>
                        <th>Proyeksi Status</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse ($kontrakDraft as $kontrak)

                        @php
                            $tanggalBerakhir = $kontrak->kerjaSama?->tanggal_berakhir;

                            $proyeksiStatus = $tanggalBerakhir
                                && \Carbon\Carbon::parse($tanggalBerakhir)->startOfDay()->lt($hariIni)
                                ? 'Nonaktif'
                                : 'Aktif';
                        @endphp

                        <tr>

                            <td>
                                <input type="checkbox"
                                       class="whatif-checkbox draft-checkbox"
                                       data-status="{{ $proyeksiStatus }}"
                                       aria-label="Pilih kontrak {{ $kontrak->nomor_kontrak }}">
                            </td>

                            <td class="font-semibold">
                                {{ $kontrak->nomor_kontrak ?: '-' }}
                            </td>

                            <td>
                                {{ $kontrak->nama_mitra_snapshot
                                    ?: $kontrak->kerjaSama?->mitra_usaha
                                    ?: '-' }}
                            </td>

                            <td>
                                {{ $kontrak->tenant?->nama_tenant ?: '-' }}
                            </td>

                            <td>
                                {{ $formatTanggal($tanggalBerakhir) }}
                            </td>

                            <td>
                                <span class="rounded-full bg-[#EDF5EF] px-3 py-1 text-xs font-bold text-[#4C8060]">
                                    {{ $proyeksiStatus }}
                                </span>
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="6" class="py-8 text-center text-[#9AAEAB]">
                                Tidak ada kontrak Draft untuk disimulasikan.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

        <div class="mt-4 rounded-lg border border-[#DCE6E4] bg-[#F8FAF9] p-4">

            <p class="text-xs leading-relaxed text-[#527D82]">
                <strong>Catatan:</strong>
                Simulasi mengasumsikan dokumen final tersedia untuk kontrak
                yang dipilih. Status proyeksi mengikuti tanggal berakhir kerja
                sama. Hasil ini tidak melakukan upload dokumen dan tidak
                menyimpan perubahan ke database.
            </p>

        </div>

    </section>


    {{-- =========================================================
         SIMULASI 3: PERPANJANGAN
    ========================================================== --}}
    <section class="whatif-card mb-6">


<div class="mb-5 flex flex-wrap items-center justify-between gap-4">

    {{-- JUDUL DAN DESKRIPSI --}}
    <div>
        <h3 class="text-base font-bold text-[#365F65]">
            3. Simulasi Keputusan Perpanjangan
        </h3>

        <p class="mt-1 text-xs text-[#8BA5A2]">
            Analisis kontrak berdasarkan konfirmasi perpanjangan yang telah tersimpan.
        </p>
    </div>

    {{-- TOMBOL SIMULASI PERPANJANGAN --}}
    <div class="flex flex-wrap gap-2">

        <button type="button"
                id="hitungSimulasiPerpanjangan"
                class="rounded-lg bg-[#365F65] px-5 py-2.5 text-xs font-bold text-white transition hover:bg-[#294B50]">
            Tampilkan Analisis
        </button>

        <button type="button"
                id="resetSimulasiPerpanjangan"
                class="rounded-lg border border-[#DCE6E4] px-5 py-2.5 text-xs font-bold text-[#527D82] transition hover:bg-[#F1F6F4]">
            Reset
        </button>

    </div>

</div>

{{-- HASIL ANALISIS --}}
<div id="hasilSimulasiPerpanjangan" class="hidden">

    <button type="button"
            id="hitungSimulasiPerpanjangan"
            class="rounded-lg bg-[#365F65] px-5 py-2.5 text-xs font-bold text-white transition hover:bg-[#294B50]">

        Tampilkan Analisis

    </button>

    <button type="button"
            id="resetSimulasiPerpanjangan"
            class="rounded-lg border border-[#DCE6E4] px-5 py-2.5 text-xs font-bold text-[#527D82] transition hover:bg-[#F1F6F4]">

        Reset

    </button>

</div>

{{-- HASIL ANALISIS --}}
<div id="hasilSimulasiPerpanjangan" class="hidden">

        </div>

        <div class="mb-5 grid grid-cols-1 gap-3 sm:grid-cols-3">

            <div class="whatif-stat">
                <p class="text-xs text-[#8BA5A2]">
                    Ingin Memperpanjang
                </p>

                <p class="whatif-stat-value">
                    {{ $inginMemperpanjang->count() }}
                </p>
            </div>

            <div class="whatif-stat">
                <p class="text-xs text-[#8BA5A2]">
                    Tidak Memperpanjang
                </p>

                <p class="whatif-stat-value">
                    {{ $tidakMemperpanjang->count() }}
                </p>
            </div>

            <div class="whatif-stat">
                <p class="text-xs text-[#8BA5A2]">
                    Belum Dikonfirmasi
                </p>

                <p class="whatif-stat-value">
                    {{ $belumKonfirmasi->count() }}
                </p>
            </div>

        </div>


        {{-- INGIN MEMPERPANJANG --}}
        <div class="mb-6">

            <h4 class="mb-3 text-sm font-bold text-[#365F65]">
                Kontrak Ingin Memperpanjang
            </h4>

            <div class="whatif-table-wrap">

                <table class="whatif-table">

                    <thead>
                        <tr>
                            <th>Nomor Kontrak</th>
                            <th>Mitra Usaha</th>
                            <th>Nama Tenant</th>
                            <th>Tanggal Berakhir</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse ($inginMemperpanjang as $kontrak)

                            <tr>
                                <td>{{ $kontrak->nomor_kontrak ?: '-' }}</td>

                                <td>
                                    {{ $kontrak->nama_mitra_snapshot
                                        ?: $kontrak->kerjaSama?->mitra_usaha
                                        ?: '-' }}
                                </td>

                                <td>
                                    {{ $kontrak->tenant?->nama_tenant ?: '-' }}
                                </td>

                                <td>
                                    {{ $formatTanggal($kontrak->kerjaSama?->tanggal_berakhir) }}
                                </td>
                            </tr>

                        @empty

                            <tr>
                                <td colspan="4" class="py-6 text-center text-[#9AAEAB]">
                                    Belum ada kontrak dengan keputusan ingin memperpanjang.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>


        {{-- TIDAK MEMPERPANJANG --}}
        <div class="mb-6">

            <h4 class="mb-3 text-sm font-bold text-[#365F65]">
                Kontrak Tidak Memperpanjang
            </h4>

            <div class="whatif-table-wrap">

                <table class="whatif-table">

                    <thead>
                        <tr>
                            <th>Nomor Kontrak</th>
                            <th>Mitra Usaha</th>
                            <th>Nama Tenant</th>
                            <th>Tanggal Berakhir</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse ($tidakMemperpanjang as $kontrak)

                            <tr>
                                <td>{{ $kontrak->nomor_kontrak ?: '-' }}</td>

                                <td>
                                    {{ $kontrak->nama_mitra_snapshot
                                        ?: $kontrak->kerjaSama?->mitra_usaha
                                        ?: '-' }}
                                </td>

                                <td>
                                    {{ $kontrak->tenant?->nama_tenant ?: '-' }}
                                </td>

                                <td>
                                    {{ $formatTanggal($kontrak->kerjaSama?->tanggal_berakhir) }}
                                </td>
                            </tr>

                        @empty

                            <tr>
                                <td colspan="4" class="py-6 text-center text-[#9AAEAB]">
                                    Belum ada kontrak dengan keputusan tidak memperpanjang.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>


        {{-- BELUM KONFIRMASI --}}
        <div>

            <h4 class="mb-3 text-sm font-bold text-[#365F65]">
                Kontrak Belum Dikonfirmasi
            </h4>

            <div class="whatif-table-wrap">

                <table class="whatif-table">

                    <thead>
                        <tr>
                            <th>Nomor Kontrak</th>
                            <th>Mitra Usaha</th>
                            <th>Nama Tenant</th>
                            <th>Tanggal Berakhir</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse ($belumKonfirmasi as $kontrak)

                            <tr>
                                <td>{{ $kontrak->nomor_kontrak ?: '-' }}</td>

                                <td>
                                    {{ $kontrak->nama_mitra_snapshot
                                        ?: $kontrak->kerjaSama?->mitra_usaha
                                        ?: '-' }}
                                </td>

                                <td>
                                    {{ $kontrak->tenant?->nama_tenant ?: '-' }}
                                </td>

                                <td>
                                    {{ $formatTanggal($kontrak->kerjaSama?->tanggal_berakhir) }}
                                </td>
                            </tr>

                        @empty

                            <tr>
                                <td colspan="4" class="py-6 text-center text-[#9AAEAB]">
                                    Semua kontrak sudah memiliki konfirmasi perpanjangan.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </section>

</div>


{{-- JAVASCRIPT SIMULASI DRAFT --}}
<script>
document.addEventListener('DOMContentLoaded', function () {

    const checkboxDraft = document.querySelectorAll('.draft-checkbox');

    const draftTerpilih = document.getElementById('draftTerpilih');

    const proyeksiAktif = document.getElementById('proyeksiAktif');

    const resetButton = document.getElementById('resetSimulasiDraft');

    const hitungButton = document.getElementById('hitungSimulasiDraft');

    function hitungSimulasiDraft() {

        let jumlahDipilih = 0;
        let jumlahAktif = 0;

        checkboxDraft.forEach(function (checkbox) {

            if (checkbox.checked) {

                jumlahDipilih++;

                if (checkbox.dataset.status === 'Aktif') {
                    jumlahAktif++;
                }

            }

        });

        draftTerpilih.textContent = jumlahDipilih;

        proyeksiAktif.textContent = jumlahAktif;
    }

    // Jalankan simulasi hanya ketika tombol ditekan.
    hitungButton.addEventListener('click', function () {

            hitungSimulasiDraft();


    // ==========================================
    // SIMULASI KEPUTUSAN PERPANJANGAN
    // ==========================================

    const tombolAnalisisPerpanjangan =
        document.getElementById('hitungSimulasiPerpanjangan');

    const tombolResetPerpanjangan =
        document.getElementById('resetSimulasiPerpanjangan');

    const hasilPerpanjangan =
        document.getElementById('hasilSimulasiPerpanjangan');


    // Tampilkan hasil analisis.
    tombolAnalisisPerpanjangan.addEventListener('click', function () {

        hasilPerpanjangan.classList.remove('hidden');

        tombolAnalisisPerpanjangan.textContent = 'Analisis Ditampilkan';

    });


    // Sembunyikan kembali hasil analisis.
    tombolResetPerpanjangan.addEventListener('click', function () {

        hasilPerpanjangan.classList.add('hidden');

        tombolAnalisisPerpanjangan.textContent = 'Tampilkan Analisis';

    });

});
</script>

@endsection