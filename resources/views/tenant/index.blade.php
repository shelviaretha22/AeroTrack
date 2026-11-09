@extends('layouts.app')

@section('page-title', 'Tenant')

@section('content')

    {{-- =========================================================
     HEADER HALAMAN
========================================================== --}}

<div class="mb-7">

    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">

        {{-- JUDUL HALAMAN --}}
        <div>
            <h2 class="text-2xl font-bold text-[#365F65]">
                Tenant
            </h2>

            {{-- Pertahankan deskripsi halaman yang sudah ada di sini --}}
        </div>

        {{-- BUTTON LAPORAN DAN WHAT-IF --}}
        <div class="flex flex-wrap items-center gap-3">

            {{-- BUTTON LAPORAN --}}
            <a href="{{ route('tenant.laporan') }}"
               class="inline-flex h-10 items-center justify-center gap-2 rounded-lg border border-[#DCE6E4] bg-white px-5 text-xs font-semibold text-[#365F65] shadow-sm transition hover:bg-[#F1F6F4]">

                <svg class="h-4 w-4"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke="currentColor"
                     stroke-width="1.8">
                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          d="M8 3H5a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-3M8 3v5h5M8 3l9 9m-8 2h6m-6 4h6"/>
                </svg>

                Laporan

            </a>

            {{-- BUTTON WHAT-IF --}}
            <a href="{{ route('tenant.whatif') }}"
               class="inline-flex h-10 items-center justify-center gap-2 rounded-lg bg-[#365F65] px-5 text-xs font-semibold text-white shadow-sm transition hover:bg-[#2F5358]">

                <svg class="h-4 w-4"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke="currentColor"
                     stroke-width="1.8">
                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          d="M4 7h9m4 0h3M4 17h3m4 0h9M13 4v6M7 14v6"/>
                </svg>

                Simulasi What-If

            </a>

        </div>

    </div>

</div>
        {{-- =========================================================
         EXECUTIVE SUMMARY TENANT
    ========================================================== --}}

    @php
        $maxKomposisi = max(1, (int) $komposisiJenisUsaha->max('total'));
        $maxTerminal = max(1, (int) $persebaranTerminal->max('total'));
        $maxTren = max(1, (int) $trenAkuisisiTenant->max('total'));
    @endphp

    {{-- RINGKASAN UTAMA --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4 mb-6">

        {{-- TOTAL TENANT --}}
        <div class="rounded-2xl border border-[#DCE6E4] border-t-2 border-t-[#527D82] bg-white p-5 shadow-sm">            <p class="text-xs font-medium text-[#8BA5A2]">
                Total Tenant
            </p>

            <div class="mt-3 flex items-end justify-between">
                <h3 class="text-3xl font-bold text-[#365F65]">
                    {{ number_format($totalTenant, 0, ',', '.') }}
                </h3>

                <span class="rounded-xl bg-[#E8F0EE] p-2 text-[#527D82]">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M16 11a4 4 0 10-8 0 4 4 0 008 0zM4 21a8 8 0 0116 0" />
                    </svg>
                </span>
            </div>

            <p class="mt-2 text-xs text-[#9AAEAB]">
                Tenant yang sudah memiliki data
            </p>
        </div>

        {{-- JENIS USAHA --}}
        <div class="rounded-2xl border border-[#DCE6E4] border-t-2 border-t-[#365F65] bg-white p-5 shadow-sm">            <p class="text-xs font-medium text-[#8BA5A2]">
                Jenis Usaha
            </p>

            <div class="mt-3 flex items-end justify-between">
                <h3 class="text-3xl font-bold text-[#365F65]">
                    {{ number_format($totalJenisUsaha, 0, ',', '.') }}
                </h3>

                <span class="rounded-xl bg-[#EDF2F0] p-2 text-[#365F65]">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </span>
            </div>

            <p class="mt-2 text-xs text-[#9AAEAB]">
                Kategori usaha yang dimiliki tenant
            </p>
        </div>

        {{-- TERMINAL --}}
        <div class="rounded-2xl border border-[#DCE6E4] border-t-2 border-t-[#7895A0] bg-white p-5 shadow-sm">            <p class="text-xs font-medium text-[#8BA5A2]">
                Terminal Terisi
            </p>

            <div class="mt-3 flex items-end justify-between">
                <h3 class="text-3xl font-bold text-[#365F65]">
                    {{ number_format($totalTerminalTerisi, 0, ',', '.') }}
                </h3>

                <span class="rounded-xl bg-[#EAF0F4] p-2 text-[#7895A0]">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3 21h18M5 21V7l8-4v18M19 21V11l-6-4M9 9v.01M9 12v.01M9 15v.01M9 18v.01" />
                    </svg>
                </span>
            </div>

            <p class="mt-2 text-xs text-[#9AAEAB]">
                Terminal dengan lokasi tenant terisi
            </p>
        </div>

        {{-- PENGAJUAN --}}
        <div class="rounded-2xl border border-[#DCE6E4] border-t-2 border-t-[#D6A14D] bg-white p-5 shadow-sm">            <p class="text-xs font-medium text-[#8BA5A2]">
                Pengajuan Kerja Sama
            </p>

            <div class="mt-3 flex items-end justify-between">
                <h3 class="text-3xl font-bold text-[#365F65]">
                    {{ number_format($totalPengajuanEnamBulan, 0, ',', '.') }}
                </h3>

                <span class="rounded-xl bg-[#F8F1E5] p-2 text-[#B88A43]">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M8 7V3m8 4V3M4 11h16M5 5h14a1 1 0 011 1v14H4V6a1 1 0 011-1z" />
                    </svg>
                </span>
            </div>

            <p class="mt-2 text-xs text-[#9AAEAB]">
                Pengajuan dalam 6 bulan terakhir
            </p>
        </div>

    </div>


    {{-- =========================================================
         GRAFIK ANALISIS TENANT
    ========================================================== --}}

    <div class="grid grid-cols-1 gap-4 xl:grid-cols-3 mb-3">

        {{-- =====================================================
             GRAFIK 1: KOMPOSISI JENIS USAHA
        ====================================================== --}}
        <div class="rounded-2xl border border-[#DCE6E4] bg-white p-5 shadow-sm">

            <div class="mb-5">
                <h3 class="text-sm font-bold text-[#365F65]">
                    Komposisi Jenis Usaha
                </h3>

                <p class="mt-1 text-xs text-[#9AAEAB]">
                    Jumlah tenant berdasarkan kategori usaha
                </p>
            </div>
            
            @forelse ($komposisiJenisUsaha as $item)

                @php
                    $persentase = $totalTenant > 0
                        ? round(($item->total / $totalTenant) * 100)
                        : 0;

                    $lebar = ($item->total / $maxKomposisi) * 100;
                @endphp

                <a
                    href="{{ route('tenant.drilldown', [
                        'type' => 'jenis_usaha',
                        'value' => $item->jenis_usaha
                    ]) }}"
                    class="mb-4 block rounded-lg transition hover:bg-[#F8FAF9] hover:px-2 last:mb-0"
                    title="Lihat tenant kategori {{ $item->jenis_usaha }}"
                >

                    <div class="mb-2 flex items-center justify-between gap-2">

                        <span class="text-xs font-medium text-[#527D82]">
                            {{ $item->jenis_usaha }}
                        </span>

                        <span class="whitespace-nowrap text-xs font-semibold text-[#365F65]">
                            {{ $item->total }} tenant

                            <span class="text-[#9AAEAB]">
                                ({{ $persentase }}%)
                            </span>
                        </span>

                    </div>

                    <div class="h-2 overflow-hidden rounded-full bg-[#EDF2F0]">

                        <div
                            class="h-full rounded-full bg-[#527D82]"
                            style="width: {{ $lebar }}%"
                        ></div>

                    </div>

                </a>

            @empty

                <div class="flex min-h-[150px] items-center justify-center text-center">
                    <p class="text-xs text-[#9AAEAB]">
                        Belum ada data komposisi jenis usaha.
                    </p>
                </div>

            @endforelse

            <div class="mt-5 border-t border-[#EEF3F1] pt-3">
                <p class="text-xs text-[#9AAEAB]">
                    Total: {{ $totalTenant }} tenant
                </p>
            </div>

        </div>

        {{-- =====================================================
             GRAFIK 2: PERSEBARAN PER TERMINAL
        ====================================================== --}}
        <div class="rounded-2xl border border-[#DCE6E4] bg-white p-5 shadow-sm">

            <div class="mb-5">
                <h3 class="text-sm font-bold text-[#365F65]">
                    Persebaran per Terminal
                </h3>

                <p class="mt-1 text-xs text-[#9AAEAB]">
                    Jumlah tenant unik pada setiap terminal
                </p>
            </div>
  
            @forelse ($persebaranTerminal as $item)

                @php
                    $lebar = ($item->total / $maxTerminal) * 100;
                @endphp

                <a
                    href="{{ route('tenant.drilldown', [
                        'type' => 'terminal',
                        'value' => $item->terminal
                    ]) }}"
                    class="mb-5 block rounded-lg transition hover:bg-[#F8FAF9] hover:px-2 last:mb-0"
                    title="Lihat tenant di Terminal {{ $item->terminal }}"
                >

                    <div class="mb-2 flex items-center justify-between">

                        <span class="text-xs font-medium text-[#527D82]">
                            Terminal {{ $item->terminal ?: 'Tidak diketahui' }}
                        </span>

                        <span class="text-xs font-semibold text-[#365F65]">
                            {{ $item->total }} tenant
                        </span>

                    </div>

                    <div class="h-2 overflow-hidden rounded-full bg-[#EDF2F0]">

                        <div
                            class="h-full rounded-full bg-[#8BA5A2]"
                            style="width: {{ $lebar }}%"
                        ></div>

                    </div>

                </a>

            @empty

                <div class="flex min-h-[150px] items-center justify-center text-center">
                    <p class="text-xs text-[#9AAEAB]">
                        Belum ada tenant pada lokasi berstatus Terisi.
                    </p>
                </div>

            @endforelse

            <div class="mt-5 border-t border-[#EEF3F1] pt-3">
                <p class="text-xs text-[#9AAEAB]">
                    Berdasarkan lokasi berstatus Terisi
                </p>
            </div>

        </div>

        {{-- =====================================================
             GRAFIK 3: TREN AKUISISI TENANT
        ====================================================== --}}
        <div class="rounded-2xl border border-[#DCE6E4] bg-white p-5 shadow-sm">

            <div class="mb-5">
                <h3 class="text-sm font-bold text-[#365F65]">
                    Tren Akuisisi Tenant
                </h3>

                <p class="mt-1 text-xs text-[#9AAEAB]">
                    Jumlah pengajuan kerja sama per bulan
                </p>
            </div>

            <div class="flex h-[165px] items-end justify-between gap-2 border-b border-[#E8EFED] px-1">
   
                @foreach ($trenAkuisisiTenant as $item)

                    @php
                        $tinggi = $item['total'] > 0
                            ? max(8, ($item['total'] / $maxTren) * 100)
                            : 0;
                    @endphp

                    <a
                        href="{{ route('tenant.drilldown', [
                            'type' => 'bulan',
                            'value' => $item['periode']
                        ]) }}"
                        class="flex h-full min-w-0 flex-1 flex-col items-center justify-end rounded-t-lg transition hover:bg-[#F8FAF9]"
                        title="Lihat pengajuan bulan {{ $item['label'] }}"
                    >

                        <span class="mb-2 text-[10px] font-semibold text-[#527D82]">
                            {{ $item['total'] }}
                        </span>

                        <div
                            class="w-full max-w-[28px] rounded-t-md bg-[#D6A14D]"
                            style="height: {{ $tinggi }}%"
                        ></div>

                        <span class="mt-2 text-[9px] text-[#8BA5A2]">
                            {{ $item['label'] }}
                        </span>

                    </a>

                @endforeach


            </div>

            <div class="mt-4">
                <p class="text-xs text-[#9AAEAB]">
                    Berdasarkan tanggal pengajuan Kerja Sama
                </p>
            </div>

        </div>

    </div>
    
    {{-- =========================================================
         SIMULASI WHAT-IF 
    ========================================================== --}}

    @php
        $dataTenantSimulasi = $komposisiJenisUsaha->pluck('total', 'jenis_usaha');
    @endphp

    <div class="mb-5 rounded-2xl border border-[#DCE6E4] bg-white p-5 shadow-sm">

        {{-- HEADER --}}
        <div class="mb-4 flex items-center gap-3">
            <div class="rounded-xl bg-[#E8F0EE] p-2.5 text-[#527D82]">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M4 7h9m4 0h3M4 17h3m4 0h9M13 4v6M7 14v6"/>
                </svg>
            </div>

            <div>
                <h3 class="text-sm font-bold text-[#365F65]">
                    Simulasi What-If
                </h3>
                <p class="mt-1 text-xs text-[#8BA5A2]">
                    Uji skenario perubahan jumlah tenant per jenis usaha.
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 items-stretch gap-4 xl:grid-cols-3">

            {{-- BAGIAN 1: INPUT SIMULASI --}}
            <div class="flex h-full flex-col rounded-xl border border-[#E2EBE9] p-4">

                <div class="mb-4">
                    <label for="jenisUsahaSimulasi"
                           class="mb-1 block text-xs font-semibold text-[#527D82]">
                        Jenis Usaha
                    </label>

                    <select id="jenisUsahaSimulasi"
                            class="h-9 w-full rounded-lg border border-[#DCE6E4] bg-white px-3 text-xs text-[#365F65] outline-none focus:border-[#527D82] focus:ring-1 focus:ring-[#527D82]">

                        @forelse ($komposisiJenisUsaha as $item)
                            <option value="{{ $item->jenis_usaha }}">
                                {{ $item->jenis_usaha }}
                            </option>
                        @empty
                            <option value="">Belum ada kategori</option>
                        @endforelse

                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">

                    <div>
                        <label for="aksiSimulasi"
                               class="mb-1 block text-xs font-semibold text-[#527D82]">
                            Perubahan
                        </label>

                        <select id="aksiSimulasi"
                                class="h-9 w-full rounded-lg border border-[#DCE6E4] bg-white px-2 text-xs text-[#365F65] outline-none focus:border-[#527D82]">
                            <option value="tambah">Tambah</option>
                            <option value="kurangi">Kurangi</option>
                        </select>
                    </div>

                    <div>
                        <label for="jumlahSimulasi"
                               class="mb-1 block text-xs font-semibold text-[#527D82]">
                            Jumlah
                        </label>

                        <div class="flex h-9 items-center rounded-lg border border-[#DCE6E4]">

                            <button type="button"
                                    id="kurangJumlahSimulasi"
                                    class="flex h-full w-8 items-center justify-center text-[#527D82] hover:bg-[#F3F7F5]">
                                −
                            </button>

                            <input type="number"
                                   id="jumlahSimulasi"
                                   value="3"
                                   min="1"
                                   step="1"
                                   class="h-full w-full min-w-0 border-0 p-0 text-center text-xs font-semibold text-[#365F65] outline-none focus:ring-0">

                            <button type="button"
                                    id="tambahJumlahSimulasi"
                                    class="flex h-full w-8 items-center justify-center text-[#527D82] hover:bg-[#F3F7F5]">
                                +
                            </button>

                        </div>
                    </div>

                </div>

                <p id="pesanErrorSimulasi"
                   class="mt-2 hidden text-xs text-red-600">
                </p>

                <button type="button"
                        id="jalankanSimulasi"
                        class="mt-auto inline-flex h-10 w-full items-center justify-center gap-2 rounded-lg bg-[#365F65] px-4 text-xs font-semibold text-white transition hover:bg-[#2F5358]"
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M5 12h14m-6-6 6 6-6 6"/>
                    </svg>

                    Jalankan Simulasi
                </button>

            </div>

            {{-- BAGIAN 2: HASIL SIMULASI --}}
            <div class="h-full rounded-xl border border-[#E2EBE9] p-4">
                <h4 class="mb-3 text-xs font-bold text-[#365F65]">
                    Hasil Simulasi (Ilustrasi)
                </h4>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">

                        <thead>
                            <tr class="border-b border-[#E8EFED] text-[#8BA5A2]">
                                <th class="pb-3 font-medium">Jenis Usaha</th>
                                <th class="pb-2 text-center font-medium">Saat Ini</th>
                                <th class="pb-2 text-right font-medium">Setelah Simulasi</th>
                            </tr>
                        </thead>

                        <tbody id="hasilTabelSimulasi"
                               class="divide-y divide-[#EEF3F1]">
                        </tbody>

                        <tfoot>
                            <tr class="border-t border-[#DCE6E4] font-bold text-[#365F65]">
                                <td class="pt-3">Total</td>
                                <td id="totalTenantSaatIni"
                                    class="pt-3 text-center">
                                    {{ $totalTenant }}
                                </td>
                                <td id="totalTenantSetelah"
                                    class="pt-3 text-right text-green-700">
                                    {{ $totalTenant }}
                                </td>
                            </tr>
                        </tfoot>

                    </table>
                </div>

            </div>

            {{-- BAGIAN 3: DAMPAK SIMULASI --}}
            <div class="h-full rounded-xl border border-[#E2EBE9] bg-[#F1F7F5] p-4">

                <div class="mb-3 flex items-center gap-2">
                    <svg class="h-5 w-5 text-[#527D82]"
                         fill="none" viewBox="0 0 24 24"
                         stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M3 3v18h18M7 14l4-4 4 4 6-7"/>
                    </svg>

                    <h4 class="text-xs font-bold text-[#365F65]">
                        Dampak Simulasi
                    </h4>
                </div>

                <ul id="dampakSimulasi"
                    class="space-y-2 text-xs text-[#527D82]">

                    <li>
                        Jalankan simulasi untuk melihat dampak perubahan portofolio tenant.
                    </li>

                </ul>

            </div>

        </div>

                <div class="mt-4 flex justify-end border-t border-[#EEF3F1] pt-4">
            <a href="{{ route('tenant.whatif') }}"
               class="inline-flex h-10 items-center justify-center gap-2 rounded-lg bg-[#365F65] px-5 text-xs font-semibold text-white transition hover:bg-[#2F5358]">

                Lihat Simulasi Lengkap

                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M5 12h14m-6-6 6 6-6 6"/>
                </svg>

            </a>
        </div>
    </div>

    {{-- SCRIPT WHAT-IF --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const dataAwal = @json($dataTenantSimulasi);
            const totalAwal = Number(@json($totalTenant));

            const jenisUsaha = document.getElementById('jenisUsahaSimulasi');
            const aksi = document.getElementById('aksiSimulasi');
            const jumlah = document.getElementById('jumlahSimulasi');

            const tombolKurang = document.getElementById('kurangJumlahSimulasi');
            const tombolTambah = document.getElementById('tambahJumlahSimulasi');
            const tombolJalankan = document.getElementById('jalankanSimulasi');

            const tabelHasil = document.getElementById('hasilTabelSimulasi');
            const totalSaatIni = document.getElementById('totalTenantSaatIni');
            const totalSetelah = document.getElementById('totalTenantSetelah');
            const dampak = document.getElementById('dampakSimulasi');
            const pesanError = document.getElementById('pesanErrorSimulasi');

            function formatAngka(nilai) {
                return new Intl.NumberFormat('id-ID').format(nilai);
            }

            function buatBaris(kategori, saatIni, setelah) {
                const baris = document.createElement('tr');

                const kolomKategori = document.createElement('td');
                kolomKategori.className = 'py-3 text-[#527D82]';
                kolomKategori.textContent = kategori;

                const kolomSaatIni = document.createElement('td');
                kolomSaatIni.className = 'py-3 text-center text-[#365F65]';
                kolomSaatIni.textContent = formatAngka(saatIni);

                const kolomSetelah = document.createElement('td');
                kolomSetelah.className = 'py-3 text-right font-semibold text-green-700';
                kolomSetelah.textContent = formatAngka(setelah);

                if (saatIni !== setelah) {
                    const perubahan = document.createElement('span');
                    perubahan.className = 'ml-1 text-[10px]';

                    const selisih = setelah - saatIni;
                    perubahan.textContent = '(' + (selisih > 0 ? '+' : '') + selisih + ')';

                    kolomSetelah.appendChild(perubahan);
                }

                baris.append(kolomKategori, kolomSaatIni, kolomSetelah);

                return baris;
            }

            function hitungPersentase(jumlah, total) {
                return total > 0 ? (jumlah / total) * 100 : 0;
            }

            function tampilkanPesan(teks) {
                const item = document.createElement('li');
                item.className = 'flex gap-2';

                const ikon = document.createElement('span');
                ikon.className = 'font-bold text-[#527D82]';
                ikon.textContent = '✓';

                const isi = document.createElement('span');
                isi.textContent = teks;

                item.append(ikon, isi);
                dampak.appendChild(item);
            }

            function jalankanSimulasi() {

                pesanError.classList.add('hidden');
                pesanError.textContent = '';

                const kategoriDipilih = jenisUsaha.value;
                const aksiDipilih = aksi.value;
                const jumlahPerubahan = Number(jumlah.value);

                if (!kategoriDipilih || !Object.prototype.hasOwnProperty.call(dataAwal, kategoriDipilih)) {
                    pesanError.textContent = 'Pilih jenis usaha yang valid.';
                    pesanError.classList.remove('hidden');
                    return;
                }

                if (!Number.isInteger(jumlahPerubahan) || jumlahPerubahan < 1) {
                    pesanError.textContent = 'Jumlah perubahan minimal 1 tenant.';
                    pesanError.classList.remove('hidden');
                    return;
                }

                const hasil = Object.fromEntries(
                    Object.entries(dataAwal).map(([kategori, nilai]) => [
                        kategori,
                        Number(nilai)
                    ])
                );

                const jumlahSebelumnya = hasil[kategoriDipilih];

                if (aksiDipilih === 'kurangi' && jumlahPerubahan > jumlahSebelumnya) {
                    pesanError.textContent =
                        'Jumlah pengurangan tidak boleh melebihi jumlah tenant kategori ' +
                        kategoriDipilih + ' saat ini (' + jumlahSebelumnya + ').';

                    pesanError.classList.remove('hidden');
                    return;
                }

                if (aksiDipilih === 'tambah') {
                    hasil[kategoriDipilih] += jumlahPerubahan;
                } else {
                    hasil[kategoriDipilih] -= jumlahPerubahan;
                }

                const totalBaru = Object.values(hasil).reduce(
                    (total, nilai) => total + nilai,
                    0
                );

                tabelHasil.replaceChildren();

                Object.entries(dataAwal).forEach(([kategori, nilai]) => {
                    tabelHasil.appendChild(
                        buatBaris(kategori, Number(nilai), hasil[kategori])
                    );
                });

                totalSaatIni.textContent = formatAngka(totalAwal);
                totalSetelah.textContent = formatAngka(totalBaru);

                dampak.replaceChildren();

                const selisihTotal = totalBaru - totalAwal;
                const persentaseTotal = totalAwal > 0
                    ? (selisihTotal / totalAwal) * 100
                    : 0;

                tampilkanPesan(
                    'Total tenant ' +
                    (selisihTotal >= 0 ? 'bertambah ' : 'berkurang ') +
                    Math.abs(persentaseTotal).toFixed(1).replace('.', ',') +
                    '% (' + (selisihTotal > 0 ? '+' : '') +
                    formatAngka(selisihTotal) + ' tenant).'
                );

                const proporsiSebelum = hitungPersentase(
                    jumlahSebelumnya,
                    totalAwal
                );

                const proporsiSesudah = hitungPersentase(
                    hasil[kategoriDipilih],
                    totalBaru
                );

                tampilkanPesan(
                    'Proporsi ' + kategoriDipilih +
                    ' berubah dari ' +
                    proporsiSebelum.toFixed(1).replace('.', ',') +
                    '% menjadi ' +
                    proporsiSesudah.toFixed(1).replace('.', '') +
                    '%.'
                );

                const konsentrasiSebelum = Math.max(
                    ...Object.values(dataAwal).map(nilai =>
                        hitungPersentase(Number(nilai), totalAwal)
                    )
                );

                const konsentrasiSesudah = Math.max(
                    ...Object.values(hasil).map(nilai =>
                        hitungPersentase(nilai, totalBaru)
                    )
                );

                if (konsentrasiSesudah < konsentrasiSebelum) {
                    tampilkanPesan('Komposisi portofolio menjadi lebih merata.');
                } else if (konsentrasiSesudah > konsentrasiSebelum) {
                    tampilkanPesan('Komposisi portofolio menjadi lebih terkonsentrasi.');
                } else {
                    tampilkanPesan('Tingkat konsentrasi komposisi portofolio tidak berubah.');
                }
            }

            tombolKurang.addEventListener('click', function () {
                jumlah.value = Math.max(1, Number(jumlah.value || 1) - 1);
            });

            tombolTambah.addEventListener('click', function () {
                jumlah.value = Math.max(1, Number(jumlah.value || 0) + 1);
            });

            tombolJalankan.addEventListener('click', jalankanSimulasi);

            // Tampilkan kondisi awal pada tabel.
            Object.entries(dataAwal).forEach(([kategori, nilai]) => {
                tabelHasil.appendChild(
                    buatBaris(kategori, Number(nilai), Number(nilai))
                );
            });

            totalSaatIni.textContent = formatAngka(totalAwal);
            totalSetelah.textContent = formatAngka(totalAwal);
        });
    </script>

    {{-- =========================================================
         CARD TABEL
    ========================================================== --}}
    <div
        class="overflow-hidden
               rounded-2xl
               border border-[#DCE6E4]
               bg-white
               shadow-sm"
    >

        {{-- =====================================================
             TOOLBAR
        ====================================================== --}}
        <div
            class="flex flex-col gap-4
                   border-b border-[#E8EFED]
                   p-5
                   sm:flex-row
                   sm:items-center
                   sm:justify-between"
        >

            {{-- TITLE --}}
            <div>

                <h3 class="text-sm font-bold text-[#365F65]">
                    Data Tenant
                </h3>

                <p class="mt-1 text-xs text-[#8BA5A2]">
                    Daftar kerja sama yang dapat dilengkapi dengan data tenant.
                </p>

            </div>S

            {{-- =================================================
                 SEARCH + SORTING
            ================================================== --}}
            <div class="w-full sm:w-auto">

                <form
                    method="GET"
                    action="{{ route('tenant.index') }}"
                    class="flex flex-col gap-3 sm:flex-row sm:items-center"
                >

                    {{-- =================================================
                         SORTING
                    ================================================== --}}
                    <div
                        class="flex h-10 items-center
                               rounded-xl
                               border border-[#E2EBE9]
                               bg-[#F7F9F8]
                               px-3"
                    >

                        <span class="mr-2 text-xs text-[#8BA5A2]">
                            Urutkan:
                        </span>

                        <select
                            name="sort"
                            onchange="this.form.submit()"
                            class="border-0
                                   bg-transparent
                                   py-0
                                   pr-7
                                   text-xs
                                   font-semibold
                                   text-[#527D82]
                                   outline-none
                                   focus:ring-0"
                        >

                            <option
                                value="newest"
                                {{ request('sort', 'newest') === 'newest' ? 'selected' : '' }}
                            >
                                Terbaru → Terlama
                            </option>

                            <option
                                value="oldest"
                                {{ request('sort') === 'oldest' ? 'selected' : '' }}
                            >
                                Terlama → Terbaru
                            </option>

                            <option
                                value="az"
                                {{ request('sort') === 'az' ? 'selected' : '' }}
                            >
                                Mitra Usaha A → Z
                            </option>

                            <option
                                value="za"
                                {{ request('sort') === 'za' ? 'selected' : '' }}
                            >
                                Mitra Usaha Z → A
                            </option>

                        </select>

                    </div>


                    {{-- =================================================
                         SEARCH
                    ================================================== --}}
                    <div
                        class="flex h-10 w-full
                               items-center
                               rounded-xl
                               border border-[#E2EBE9]
                               bg-[#F7F9F8]
                               px-3
                               sm:w-[230px]"
                    >

                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-4 w-4 shrink-0 text-[#8BA5A2]"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >

                            <circle
                                cx="11"
                                cy="11"
                                r="7"
                            />

                            <path
                                stroke-linecap="round"
                                d="M20 20l-3.5-3.5"
                            />

                        </svg>

                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Cari mitra, brand, PIC..."
                            class="ml-2 w-full
                                   border-0
                                   bg-transparent
                                   text-xs
                                   text-[#365F65]
                                   outline-none
                                   focus:ring-0
                                   placeholder-[#9AAEAB]"
                        >

                    </div>


                    {{-- =================================================
                         TOMBOL SEARCH
                    ================================================== --}}
                    <button
                        type="submit"
                        class="hidden"
                    >
                        Cari
                    </button>

                </form>

            </div>

        </div>



        {{-- =====================================================
             TABLE
        ====================================================== --}}
        <div class="overflow-x-auto">

            <table class="min-w-full text-left">

                {{-- =================================================
                     TABLE HEADER
                ================================================== --}}
                <thead>

                    <tr
                        class="border-b border-[#E8EFED]
                               bg-[#F8FAF9]"
                    >

                        {{-- NO --}}
                        <th
                            class="px-5 py-4
                                   text-[10px]
                                   font-bold
                                   uppercase
                                   tracking-[0.08em]
                                   text-[#8BA5A2]"
                        >
                            No
                        </th>


                        {{-- MITRA USAHA --}}
                        <th
                            class="px-5 py-4
                                   text-[10px]
                                   font-bold
                                   uppercase
                                   tracking-[0.08em]
                                   text-[#8BA5A2]"
                        >
                            Mitra Usaha
                        </th>


                        {{-- BRAND --}}
                        <th
                            class="px-5 py-4
                                   text-[10px]
                                   font-bold
                                   uppercase
                                   tracking-[0.08em]
                                   text-[#8BA5A2]"
                        >
                            Brand
                        </th>


                        {{-- JENIS USAHA --}}
                        <th
                            class="px-5 py-4
                                   text-[10px]
                                   font-bold
                                   uppercase
                                   tracking-[0.08em]
                                   text-[#8BA5A2]"
                        >
                            Jenis Usaha
                        </th>


                        {{-- PIC COMMERCIAL --}}
                        <th
                            class="px-5 py-4
                                   text-[10px]
                                   font-bold
                                   uppercase
                                   tracking-[0.08em]
                                   text-[#8BA5A2]"
                        >
                            PIC Commercial
                        </th>


                        {{-- DATA TENANT --}}
                        <th
                            class="px-5 py-4
                                   text-center
                                   text-[10px]
                                   font-bold
                                   uppercase
                                   tracking-[0.08em]
                                   text-[#8BA5A2]"
                        >
                            Data Tenant
                        </th>


                        {{-- AKSI --}}
                        <th
                            class="px-5 py-4
                                   text-center
                                   text-[10px]
                                   font-bold
                                   uppercase
                                   tracking-[0.08em]
                                   text-[#8BA5A2]"
                        >
                            Aksi
                        </th>

                    </tr>

                </thead>



                {{-- =================================================
                     TABLE BODY
                ================================================== --}}
                <tbody class="divide-y divide-[#EEF3F1]">

                    @forelse ($kerjaSamas as $index => $kerjaSama)

                        @php
                            $tenant = $tenantByKerjaSama->get($kerjaSama->id);
                        @endphp


                        <tr class="transition hover:bg-[#F8FAF9]">

                            {{-- =================================================
                                 NO
                            ================================================== --}}
                            <td
                                class="whitespace-nowrap
                                       px-5 py-4
                                       text-sm
                                       font-medium
                                       text-[#8BA5A2]"
                            >
                                {{ $index + 1 }}
                            </td>



                            {{-- =================================================
                                 MITRA USAHA
                            ================================================== --}}
                            <td class="px-5 py-4">

                                <div
                                    class="text-sm
                                           font-semibold
                                           text-[#365F65]"
                                >
                                    {{ $kerjaSama->mitra_usaha }}
                                </div>

                                @if ($kerjaSama->tanggal_pengajuan)

                                    <div
                                        class="mt-1
                                               text-xs
                                               text-[#9AAEAB]"
                                    >
                                        Pengajuan:
                                        {{ $kerjaSama->tanggal_pengajuan->format('d M Y') }}
                                    </div>

                                @endif

                            </td>



                            {{-- =================================================
                                 BRAND
                            ================================================== --}}
                            <td
                                class="px-5 py-4
                                       text-sm
                                       text-[#527D82]"
                            >
                                {{ $kerjaSama->brand ?: '-' }}
                            </td>



                            {{-- =================================================
                                 JENIS USAHA
                            ================================================== --}}
                            <td class="px-5 py-4">

                                <span
                                    class="inline-flex
                                           rounded-lg
                                           bg-[#E8F0EE]
                                           px-2.5 py-1
                                           text-xs
                                           font-semibold
                                           text-[#527D82]"
                                >
                                    {{ $kerjaSama->jenis_usaha }}
                                </span>

                            </td>



                            {{-- =================================================
                                 PIC COMMERCIAL
                            ================================================== --}}
                            <td class="px-5 py-4">

                                <div
                                    class="text-sm
                                           font-semibold
                                           text-[#365F65]"
                                >
                                    {{ $kerjaSama->pic_commercial }}
                                </div>

                                @if ($kerjaSama->catatan)

                                    <div
                                        class="mt-1
                                               max-w-[220px]
                                               truncate
                                               text-xs
                                               text-[#9AAEAB]"
                                    >
                                        {{ $kerjaSama->catatan }}
                                    </div>

                                @endif

                            </td>



                            {{-- =================================================
                                 DATA TENANT
                            ================================================== --}}
                            <td class="px-5 py-4">

                                <div
                                    class="flex
                                           items-center
                                           justify-center"
                                >

                                    @if ($tenant)

                                        {{-- =================================================
                                             SUDAH ADA TENANT
                                        ================================================== --}}
                                        <div
                                            class="flex
                                                   flex-col
                                                   items-center
                                                   gap-1.5"
                                        >

                                            <span
                                                class="inline-flex
                                                       items-center
                                                       gap-1.5
                                                       rounded-lg
                                                       bg-[#E8F3EE]
                                                       px-3
                                                       py-1.5
                                                       text-xs
                                                       font-semibold
                                                       text-[#4D856F]"
                                            >

                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    class="h-3.5 w-3.5"
                                                    fill="none"
                                                    viewBox="0 0 24 24"
                                                    stroke="currentColor"
                                                    stroke-width="2"
                                                >

                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        d="m5 12 4 4L19 6"
                                                    />

                                                </svg>

                                                Sudah Diinput

                                            </span>


                                            <a
                                                href="{{ route('tenant.show', $tenant) }}"
                                                class="text-[11px]
                                                       font-medium
                                                       text-[#527D82]
                                                       underline
                                                       decoration-[#B8CBC7]
                                                       underline-offset-2
                                                       transition
                                                       hover:text-[#365F65]"
                                            >
                                                Lihat Data
                                            </a>

                                        </div>


                                    @else

                                        {{-- =================================================
                                             BELUM ADA TENANT
                                        ================================================== --}}
                                        <a
                                            href="{{ route('tenant.create', ['kerja_sama_id' => $kerjaSama->id]) }}"
                                            class="inline-flex
                                                   items-center
                                                   justify-center
                                                   rounded-lg
                                                   bg-[#365F65]
                                                   px-3.5
                                                   py-2
                                                   text-xs
                                                   font-semibold
                                                   text-white
                                                   transition
                                                   hover:bg-[#2F5358]"
                                        >
                                            Input Data
                                        </a>

                                    @endif

                                </div>

                            </td>



                            {{-- =================================================
                                 AKSI
                            ================================================== --}}
                            <td class="px-5 py-4">

                                @if ($tenant)

                                    <div
                                        class="flex
                                               items-center
                                               justify-center
                                               gap-2"
                                    >

                                        {{-- =================================================
                                             DETAIL
                                        ================================================== --}}
                                        <a
                                            href="{{ route('tenant.show', $tenant) }}"
                                            title="Lihat detail"
                                            class="flex h-9 w-9
                                                   items-center justify-center
                                                   rounded-lg
                                                   bg-[#F3F6F5]
                                                   text-[#527D82]
                                                   transition
                                                   hover:bg-[#E1EBE8]"
                                        >

                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                class="h-4 w-4"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                                stroke-width="1.8"
                                            >

                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M2.25 12s3.75-6 9.75-6 9.75 6 9.75 6-3.75 6-9.75 6-9.75-6-9.75-6z"
                                                />

                                                <circle
                                                    cx="12"
                                                    cy="12"
                                                    r="2.5"
                                                />

                                            </svg>

                                        </a>



                                        {{-- =================================================
                                             EDIT
                                        ================================================== --}}
                                        <a
                                            href="{{ route('tenant.edit', $tenant) }}"
                                            title="Edit"
                                            class="flex h-9 w-9
                                                   items-center justify-center
                                                   rounded-lg
                                                   bg-[#F3F6F5]
                                                   text-[#527D82]
                                                   transition
                                                   hover:bg-[#E1EBE8]"
                                        >

                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                class="h-4 w-4"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                                stroke-width="1.8"
                                            >

                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M16.862 3.487a2.1 2.1 0 013 3L8.25 18.1l-4.5 1.2-4.5 1.2 1.2-4.5L16.862 3.487z"
                                                />

                                            </svg>

                                        </a>



                                        {{-- =================================================
                                             DELETE
                                        ================================================== --}}
                                        <form
                                            action="{{ route('tenant.destroy', $tenant) }}"
                                            method="POST"
                                            onsubmit="return confirm('Apakah kamu yakin ingin menghapus data tenant ini?')"
                                        >

                                            @csrf

                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                title="Hapus"
                                                class="flex h-9 w-9
                                                       items-center justify-center
                                                       rounded-lg
                                                       bg-[#FFF4F2]
                                                       text-[#B96B60]
                                                       transition
                                                       hover:bg-[#FDE5E1]"
                                            >

                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    class="h-4 w-4"
                                                    fill="none"
                                                    viewBox="0 0 24 24"
                                                    stroke="currentColor"
                                                    stroke-width="1.8"
                                                >

                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        d="M6 7h12M10 11v6M14 11v6M9 7l1-2h4l1 2m-8 0l.75 13h8.5L17 7"
                                                    />

                                                </svg>

                                            </button>

                                        </form>

                                    </div>

                                @else

                                    <span
                                        class="text-xs
                                               text-[#B1BFBD]"
                                    >
                                        Belum ada data
                                    </span>

                                @endif

                            </td>

                        </tr>


                    @empty

                        {{-- =================================================
                             DATA KOSONG
                        ================================================== --}}
                        <tr>

                            <td
                                colspan="7"
                                class="px-5 py-16 text-center"
                            >

                                <div class="flex flex-col items-center">

                                    <div
                                        class="flex h-14 w-14
                                               items-center justify-center
                                               rounded-2xl
                                               bg-[#E8F0EE]
                                               text-[#527D82]"
                                    >

                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-7 w-7"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                            stroke-width="1.7"
                                        >

                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M16 11a4 4 0 10-8 0 4 4 0 008 0zM4 21a8 8 0 0116 0"
                                            />

                                        </svg>

                                    </div>


                                    <h4
                                        class="mt-4
                                               text-sm
                                               font-bold
                                               text-[#365F65]"
                                    >
                                        Belum ada data kerja sama
                                    </h4>


                                    <p
                                        class="mt-1
                                               max-w-sm
                                               text-xs
                                               text-[#8BA5A2]"
                                    >
                                        Data kerja sama yang telah ditambahkan
                                        akan ditampilkan di tabel ini.
                                    </p>

                                </div>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>



        {{-- =====================================================
             JUMLAH DATA
        ====================================================== --}}
        @if ($kerjaSamas->count() > 0)

            <div
                class="border-t
                       border-[#E8EFED]
                       px-5 py-4"
            >

                <p class="text-xs text-[#8BA5A2]">

                    Menampilkan

                    <span class="font-semibold text-[#527D82]">
                        {{ $kerjaSamas->count() }}
                    </span>

                    kerja sama

                </p>

            </div>

        @endif

    </div>

@endsection