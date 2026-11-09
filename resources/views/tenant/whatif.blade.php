@extends('layouts.app')

@section('page-title', 'What-If')

@section('content')

<style>
    /* =========================================
       WHAT-IF TENANT
    ========================================= */

    #tenant-whatif-page {
        width: 100%;
        max-width: 100%;
        min-width: 0;
        box-sizing: border-box;
    }

    #tenant-whatif-page *,
    #tenant-whatif-page *::before,
    #tenant-whatif-page *::after {
        box-sizing: border-box;
    }

    #tenant-whatif-page .whatif-summary-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 16px;
    }

    #tenant-whatif-page .whatif-main-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.35fr) minmax(0, 0.85fr);
        align-items: stretch;
        gap: 18px;
    }

    #tenant-whatif-page .whatif-comparison-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px 30px;
    }

    #tenant-whatif-page .whatif-card {
        min-width: 0;
        max-width: 100%;
    }

    #tenant-whatif-page .whatif-table {
        width: 100%;
        table-layout: fixed;
        border-collapse: collapse;
    }

    #tenant-whatif-page .whatif-table th,
    #tenant-whatif-page .whatif-table td {
        overflow-wrap: anywhere;
    }

    #tenant-whatif-page .whatif-table input {
        width: 100%;
        max-width: 110px;
    }

    #tenant-whatif-page .whatif-bar-track {
        width: 100%;
        height: 7px;
        overflow: hidden;
        border-radius: 999px;
        background: #EDF2F0;
    }

    #tenant-whatif-page .whatif-bar {
        height: 100%;
        border-radius: 999px;
        transition: width 0.25s ease;
    }

    @media (max-width: 1023px) {
        #tenant-whatif-page .whatif-main-grid {
            grid-template-columns: minmax(0, 1fr);
        }
    }

    @media (max-width: 767px) {
        #tenant-whatif-page .whatif-summary-grid,
        #tenant-whatif-page .whatif-comparison-grid {
            grid-template-columns: minmax(0, 1fr);
        }

        #tenant-whatif-page .whatif-card {
            padding: 16px;
        }

        #tenant-whatif-page .whatif-table th,
        #tenant-whatif-page .whatif-table td {
            padding-left: 7px;
            padding-right: 7px;
        }
    }
</style>

<div id="tenant-whatif-page">

    {{-- HEADER HALAMAN --}}
    <div class="mb-5">
        <a href="{{ route('tenant.index') }}"
           class="mb-3 inline-flex items-center gap-2 text-sm font-medium text-[#527D82] transition hover:text-[#365F65]">

            <svg class="h-4 w-4"
                 fill="none"
                 viewBox="0 0 24 24"
                 stroke="currentColor"
                 stroke-width="2">
                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      d="M15 19l-7-7 7-7"/>
            </svg>

            Kembali ke Tenant
        </a>

        <h2 class="text-2xl font-bold text-[#365F65]">
            What-If
        </h2>

        <p class="mt-1 text-sm text-[#8BA5A2]">
            Analisis skenario perubahan komposisi tenant berdasarkan jenis usaha.
        </p>
    </div>


    {{-- INFORMASI WHAT-IF --}}
    <div class="mb-5 flex min-w-0 items-start gap-3 rounded-xl border border-[#DCE6E4] bg-[#F1F7F5] px-4 py-3">

        <div class="shrink-0 rounded-lg bg-white p-2 text-[#527D82]">
            <svg class="h-5 w-5"
                 fill="none"
                 viewBox="0 0 24 24"
                 stroke="currentColor"
                 stroke-width="1.8">
                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      d="M12 8v4m0 4h.01M4.93 4.93l14.14 14.14M12 2a10 10 0 100 20 10 10 0 000-20z"/>
            </svg>
        </div>

        <div class="min-w-0">
            <h3 class="text-sm font-bold text-[#365F65]">
                Informasi What-If
            </h3>

            <p class="mt-1 text-xs leading-relaxed text-[#527D82]">
                Ubah jumlah tenant untuk melihat kemungkinan komposisi portofolio.
                Perhitungan ini hanya simulasi; data Tenant asli tidak diubah
                dan tidak disimpan ke database.
            </p>
        </div>

    </div>


    {{-- RINGKASAN WHAT-IF --}}
    <div class="whatif-summary-grid mb-5">

        <div class="whatif-card rounded-2xl border border-[#DCE6E4] border-t-2 border-t-[#527D82] bg-white p-4 shadow-sm">

            <p class="text-xs font-medium text-[#8BA5A2]">
                Total Tenant Saat Ini
            </p>

            <h3 id="ringkasanTotalAwal"
                class="mt-1 text-3xl font-bold text-[#365F65]">
                {{ number_format($totalTenant, 0, ',', '.') }}
            </h3>

            <p class="mt-1 text-xs text-[#9AAEAB]">
                Berdasarkan data Tenant
            </p>

        </div>


        <div class="whatif-card rounded-2xl border border-[#DCE6E4] border-t-2 border-t-[#D6A14D] bg-white p-4 shadow-sm">

            <p class="text-xs font-medium text-[#8BA5A2]">
                Total Tenant Setelah What-If
            </p>

            <h3 id="ringkasanTotalBaru"
                class="mt-1 text-3xl font-bold text-[#365F65]">
                {{ number_format($totalTenant, 0, ',', '.') }}
            </h3>

            <p class="mt-1 text-xs text-[#9AAEAB]">
                Hasil perhitungan skenario
            </p>

        </div>


        <div class="whatif-card rounded-2xl border border-[#DCE6E4] border-t-2 border-t-[#7895A0] bg-white p-4 shadow-sm">

            <p class="text-xs font-medium text-[#8BA5A2]">
                Perubahan Total Tenant
            </p>

            <h3 id="ringkasanPerubahan"
                class="mt-1 text-3xl font-bold text-[#365F65]">
                0
            </h3>

            <p id="ringkasanPersentase"
               class="mt-1 text-xs text-[#9AAEAB]">
                Belum ada perubahan skenario
            </p>

        </div>

    </div>


    {{-- PENGATURAN TENANT DAN DAMPAK WHAT-IF --}}
    <div class="whatif-main-grid mb-5">

        {{-- PENGATURAN SKENARIO --}}
        <section class="whatif-card rounded-2xl border border-[#DCE6E4] bg-white p-5 shadow-sm">

            <div class="mb-4">
                <h3 class="text-base font-bold text-[#365F65]">
                    Pengaturan Skenario
                </h3>

                <p class="mt-1 text-xs leading-relaxed text-[#8BA5A2]">
                    Atur penambahan atau pengurangan jumlah tenant
                    berdasarkan jenis usaha.
                </p>
            </div>


            @if ($dataTenantSimulasi->isEmpty())

                <div class="rounded-xl border border-dashed border-[#DCE6E4] p-6 text-center">

                    <p class="text-sm font-semibold text-[#527D82]">
                        Belum ada data jenis usaha.
                    </p>

                    <p class="mt-1 text-xs text-[#8BA5A2]">
                        Tambahkan data Tenant terlebih dahulu untuk memulai What-If.
                    </p>

                </div>

            @else

                <div class="overflow-hidden rounded-xl border border-[#E8EFED]">

                    <table class="whatif-table text-left text-sm">

                        <thead>
                            <tr class="bg-[#F5F8F7]">

                                <th class="w-[30%] px-3 py-3 text-xs font-bold text-[#8BA5A2]">
                                    Jenis Usaha
                                </th>

                                <th class="w-[16%] px-2 py-3 text-center text-xs font-bold text-[#8BA5A2]">
                                    Saat Ini
                                </th>

                                <th class="w-[30%] px-2 py-3 text-center text-xs font-bold text-[#8BA5A2]">
                                    Perubahan (+/-)
                                </th>

                                <th class="w-[24%] px-3 py-3 text-right text-xs font-bold text-[#8BA5A2]">
                                    Setelah
                                </th>

                            </tr>
                        </thead>

                        <tbody class="divide-y divide-[#EEF3F1]">

                            @foreach ($dataTenantSimulasi as $item)

                                <tr class="transition hover:bg-[#F8FAF9]">

                                    <td class="px-3 py-3 font-semibold text-[#365F65]">
                                        {{ $item['jenis_usaha'] }}
                                    </td>

                                    <td class="px-2 py-3 text-center text-[#527D82]"
                                        data-current>
                                        {{ $item['total'] }}
                                    </td>

                                    <td class="px-2 py-3">

                                        <input type="number"
                                               value="0"
                                               step="1"
                                               data-change
                                               data-category="{{ $item['jenis_usaha'] }}"
                                               data-current="{{ $item['total'] }}"
                                               class="mx-auto block h-9 rounded-lg border border-[#DCE6E4] bg-white px-2 text-center text-sm font-semibold text-[#365F65] outline-none transition focus:border-[#527D82] focus:ring-2 focus:ring-[#527D82]/10">

                                    </td>

                                    <td class="px-3 py-3 text-right font-semibold text-[#16803D]"
                                        data-after>
                                        {{ $item['total'] }}
                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                        <tfoot>

                            <tr class="border-t-2 border-[#DCE6E4] bg-[#F8FAF9] font-bold text-[#365F65]">

                                <td class="px-3 py-3">
                                    Total
                                </td>

                                <td id="totalAwalTabel"
                                    class="px-2 py-3 text-center">
                                    {{ $totalTenant }}
                                </td>

                                <td id="totalPerubahanTabel"
                                    class="px-2 py-3 text-center">
                                    0
                                </td>

                                <td id="totalBaruTabel"
                                    class="px-3 py-3 text-right text-[#16803D]">
                                    {{ $totalTenant }}
                                </td>

                            </tr>

                        </tfoot>

                    </table>

                </div>
                                {{-- PESAN ERROR --}}
                <div id="pesanErrorWhatIf"
                     class="mt-3 hidden rounded-lg border border-red-200 bg-red-50 p-3 text-xs text-red-600">
                </div>


                {{-- TOMBOL AKSI --}}
                <div class="mt-4 flex flex-wrap justify-end gap-2">

                    <button type="button"
                            id="resetWhatIf"
                            class="inline-flex h-9 items-center justify-center rounded-lg border border-[#DCE6E4] px-4 text-xs font-semibold text-[#527D82] transition hover:bg-[#F3F7F5]">
                        Reset
                    </button>

                    <button type="button"
                            id="hitungWhatIf"
                            class="inline-flex h-9 items-center justify-center gap-2 rounded-lg bg-[#365F65] px-5 text-xs font-semibold text-white transition hover:bg-[#2F5358]">

                        <svg class="h-4 w-4"
                             fill="none"
                             viewBox="0 0 24 24"
                             stroke="currentColor"
                             stroke-width="2">
                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  d="M5 12h14m-6-6 6 6-6 6"/>
                        </svg>

                        Hitung What-If
                    </button>

                </div>

            @endif

        </section>


        {{-- ANALISIS DAMPAK --}}
        <section class="whatif-card rounded-2xl border border-[#DCE6E4] bg-[#F1F7F5] p-5">

            <div class="mb-3 flex items-center gap-2">

                <svg class="h-5 w-5 shrink-0 text-[#527D82]"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke="currentColor"
                     stroke-width="1.8">
                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          d="M3 3v18h18M7 14l4-4 4 4 6-7"/>
                </svg>

                <h3 class="text-base font-bold text-[#365F65]">
                    Analisis Dampak
                </h3>

            </div>

            <p class="mb-4 text-xs leading-relaxed text-[#8BA5A2]">
                Ringkasan perubahan jumlah dan komposisi tenant
                setelah skenario dihitung.
            </p>

            <div class="mb-4 rounded-xl border border-[#DCE6E4] bg-white p-3">

                <p class="text-xs text-[#8BA5A2]">
                    Perubahan jumlah tenant
                </p>

                <p id="dampakPersentase"
                   class="mt-1 text-2xl font-bold text-[#16803D]">
                    0,0%
                </p>

                <p id="dampakKeterangan"
                   class="mt-1 text-xs text-[#8BA5A2]">
                    Belum ada perubahan skenario
                </p>

            </div>

            <ul id="analisisDampak"
                class="space-y-2 text-xs leading-relaxed text-[#527D82]">

                <li class="flex items-start gap-2">
                    <span class="font-bold text-[#527D82]">•</span>
                    <span>
                        Atur perubahan jumlah tenant, kemudian tekan
                        Hitung What-If untuk melihat dampaknya.
                    </span>
                </li>

            </ul>

        </section>

    </div>


    {{-- PERBANDINGAN KOMPOSISI TENANT --}}
    <section class="whatif-card rounded-2xl border border-[#DCE6E4] bg-white p-5 shadow-sm">

        <div class="mb-4">

            <h3 class="text-base font-bold text-[#365F65]">
                Perbandingan Komposisi Tenant
            </h3>

            <p class="mt-1 text-xs text-[#8BA5A2]">
                Perbandingan proporsi setiap jenis usaha sebelum
                dan sesudah What-If.
            </p>

        </div>


        <div id="perbandinganKomposisi"
             class="whatif-comparison-grid">

            @foreach ($dataTenantSimulasi as $item)

                @php
                    $shareAwal = $totalTenant > 0
                        ? ($item['total'] / $totalTenant) * 100
                        : 0;
                @endphp

                <div data-comparison-row
                     class="min-w-0">

                    <div class="mb-2 flex items-center justify-between gap-2">

                        <span class="min-w-0 truncate text-xs font-semibold text-[#527D82]">
                            {{ $item['jenis_usaha'] }}
                        </span>

                        <span class="shrink-0 text-xs text-[#8BA5A2]"
                              data-share>
                            {{ number_format($shareAwal, 1, ',', '.') }}%
                            <span class="px-1">→</span>
                            {{ number_format($shareAwal, 1, ',', '.') }}%
                        </span>

                    </div>


                    {{-- BATANG KONDISI AWAL --}}
                    <div class="whatif-bar-track mb-1.5">

                        <div data-bar-awal
                             class="whatif-bar bg-[#A4C0BC]"
                             data-initial-share="{{ $shareAwal }}"
                             style="width: {{ $shareAwal }}%">
                        </div>

                    </div>


                    {{-- BATANG HASIL SIMULASI --}}
                    <div class="whatif-bar-track">

                        <div data-bar-simulasi
                             class="whatif-bar bg-[#365F65]"
                             data-initial-share="{{ $shareAwal }}"
                             style="width: {{ $shareAwal }}%">
                        </div>

                    </div>

                </div>

            @endforeach

        </div>


        {{-- KETERANGAN WARNA --}}
        <div class="mt-4 flex flex-wrap items-center gap-4 border-t border-[#EEF3F1] pt-3">

            <div class="flex items-center gap-1.5">
                <span class="h-2 w-2 rounded-full bg-[#A4C0BC]"></span>
                <span class="text-[10px] text-[#8BA5A2]">
                    Kondisi Awal
                </span>
            </div>

            <div class="flex items-center gap-1.5">
                <span class="h-2 w-2 rounded-full bg-[#365F65]"></span>
                <span class="text-[10px] text-[#8BA5A2]">
                    Hasil What-If
                </span>
            </div>

        </div>

    </section>

</div>
{{-- SCRIPT WHAT-IF --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {

        const halamanWhatIf = document.getElementById('tenant-whatif-page');

        if (!halamanWhatIf) {
            return;
        }

        const totalAwal = Number(@json($totalTenant));

        const inputPerubahan =
            halamanWhatIf.querySelectorAll('[data-change]');

        const tombolHitung =
            document.getElementById('hitungWhatIf');

        const tombolReset =
            document.getElementById('resetWhatIf');

        const pesanError =
            document.getElementById('pesanErrorWhatIf');

        const analisisDampak =
            document.getElementById('analisisDampak');

        const dampakPersentase =
            document.getElementById('dampakPersentase');

        const dampakKeterangan =
            document.getElementById('dampakKeterangan');


        // FORMAT ANGKA
        const formatAngka = nilai =>
            new Intl.NumberFormat('id-ID').format(nilai);


        // FORMAT PERSENTASE
        const formatPersen = nilai =>
            nilai.toFixed(1).replace('.', ',') + '%';


        // MENAMPILKAN ANALISIS DAMPAK
        function tampilkanDampak(teks) {

            const item = document.createElement('li');
            item.className = 'flex items-start gap-2';

            const ikon = document.createElement('span');
            ikon.className = 'font-bold text-[#527D82]';
            ikon.textContent = '•';

            const isi = document.createElement('span');
            isi.textContent = teks;

            item.append(ikon, isi);

            analisisDampak.appendChild(item);
        }


        // PROSES PERHITUNGAN WHAT-IF
        function hitungWhatIf() {

            pesanError.classList.add('hidden');
            pesanError.textContent = '';

            const hasil = [];
            let totalPerubahan = 0;


            // VALIDASI INPUT PERUBAHAN
            for (const input of inputPerubahan) {

                const kategori = input.dataset.category;
                const saatIni = Number(input.dataset.current);
                const perubahan = Number(input.value);

                if (!Number.isInteger(perubahan)) {

                    pesanError.textContent =
                        'Perubahan jumlah tenant harus berupa bilangan bulat.';

                    pesanError.classList.remove('hidden');

                    return;
                }


                const setelah = saatIni + perubahan;

                if (setelah < 0) {

                    pesanError.textContent =
                        'Jumlah tenant kategori ' + kategori +
                        ' tidak boleh kurang dari nol.';

                    pesanError.classList.remove('hidden');

                    return;
                }


                hasil.push({
                    kategori,
                    saatIni,
                    perubahan,
                    setelah
                });

                totalPerubahan += perubahan;
            }


            // MENGHITUNG TOTAL TENANT SETELAH WHAT-IF
            const totalBaru = totalAwal + totalPerubahan;

            if (totalBaru < 0) {

                pesanError.textContent =
                    'Total tenant setelah What-If tidak boleh negatif.';

                pesanError.classList.remove('hidden');

                return;
            }


            // MEMPERBARUI TABEL
            halamanWhatIf.querySelectorAll('[data-change]').forEach(input => {

                const baris = input.closest('tr');

                const data = hasil.find(
                    item => item.kategori === input.dataset.category
                );

                const kolomHasil = baris.querySelector('[data-after]');

                kolomHasil.textContent = formatAngka(data.setelah);

                if (data.perubahan !== 0) {

                    const tanda = data.perubahan > 0 ? '+' : '';

                    kolomHasil.textContent +=
                        ' (' + tanda + formatAngka(data.perubahan) + ')';
                }

            });


            // MEMPERBARUI RINGKASAN
            document.getElementById('ringkasanTotalAwal').textContent =
                formatAngka(totalAwal);

            document.getElementById('ringkasanTotalBaru').textContent =
                formatAngka(totalBaru);

            document.getElementById('ringkasanPerubahan').textContent =
                (totalPerubahan > 0 ? '+' : '') +
                formatAngka(totalPerubahan);

            document.getElementById('ringkasanPersentase').textContent =
                totalAwal > 0
                    ? (totalPerubahan > 0
                        ? 'Kenaikan '
                        : totalPerubahan < 0
                            ? 'Penurunan '
                            : 'Perubahan ') +
                      formatPersen(
                          Math.abs(totalPerubahan / totalAwal * 100)
                      )
                    : 'Total awal belum tersedia';


            // MEMPERBARUI TOTAL TABEL
            const totalAwalTabel =
                document.getElementById('totalAwalTabel');

            const totalPerubahanTabel =
                document.getElementById('totalPerubahanTabel');

            const totalBaruTabel =
                document.getElementById('totalBaruTabel');

            if (totalAwalTabel) {
                totalAwalTabel.textContent = formatAngka(totalAwal);
            }

            if (totalPerubahanTabel) {
                totalPerubahanTabel.textContent =
                    (totalPerubahan > 0 ? '+' : '') +
                    formatAngka(totalPerubahan);
            }

            if (totalBaruTabel) {
                totalBaruTabel.textContent = formatAngka(totalBaru);
            }


            // MEMPERBARUI PERBANDINGAN KOMPOSISI
            halamanWhatIf.querySelectorAll('[data-comparison-row]')
                .forEach((row, index) => {

                    const data = hasil[index];

                    if (!data) {
                        return;
                    }

                    const proporsiAwal = totalAwal > 0
                        ? data.saatIni / totalAwal * 100
                        : 0;

                    const proporsiBaru = totalBaru > 0
                        ? data.setelah / totalBaru * 100
                        : 0;

                    row.querySelector('[data-share]').textContent =
                        formatPersen(proporsiAwal) +
                        ' → ' +
                        formatPersen(proporsiBaru);

                    row.querySelector('[data-bar-awal]').style.width =
                        proporsiAwal + '%';

                    row.querySelector('[data-bar-simulasi]').style.width =
                        proporsiBaru + '%';

                });


            // MEMPERBARUI KARTU DAMPAK
            const persentasePerubahan = totalAwal > 0
                ? totalPerubahan / totalAwal * 100
                : 0;

            dampakPersentase.textContent =
                formatPersen(Math.abs(persentasePerubahan));

            if (totalPerubahan > 0) {

                dampakPersentase.className =
                    'mt-1 text-2xl font-bold text-[#16803D]';

                dampakKeterangan.textContent =
                    'Total tenant mengalami kenaikan.';

            } else if (totalPerubahan < 0) {

                dampakPersentase.className =
                    'mt-1 text-2xl font-bold text-red-600';

                dampakKeterangan.textContent =
                    'Total tenant mengalami penurunan.';

            } else {

                dampakPersentase.className =
                    'mt-1 text-2xl font-bold text-[#365F65]';

                dampakKeterangan.textContent =
                    'Tidak ada perubahan jumlah tenant.';

            }


            // MEMPERBARUI ANALISIS DAMPAK
            analisisDampak.innerHTML = '';

            if (totalPerubahan > 0) {

                tampilkanDampak(
                    'Jumlah tenant bertambah sebanyak ' +
                    formatAngka(totalPerubahan) + ' tenant.'
                );

            } else if (totalPerubahan < 0) {

                tampilkanDampak(
                    'Jumlah tenant berkurang sebanyak ' +
                    formatAngka(Math.abs(totalPerubahan)) + ' tenant.'
                );

            } else {

                tampilkanDampak(
                    'Tidak terdapat perubahan pada jumlah total tenant.'
                );

            }

            tampilkanDampak(
                'Total tenant setelah What-If adalah ' +
                formatAngka(totalBaru) + ' tenant.'
            );

            hasil.forEach(data => {

                if (data.perubahan !== 0) {

                    const jenisPerubahan =
                        data.perubahan > 0 ? 'bertambah' : 'berkurang';

                    tampilkanDampak(
                        'Kategori ' + data.kategori + ' ' +
                        jenisPerubahan + ' sebanyak ' +
                        formatAngka(Math.abs(data.perubahan)) +
                        ' tenant.'
                    );

                }

            });

        }
                // RESET WHAT-IF
        function resetWhatIf() {

            if (pesanError) {
                pesanError.classList.add('hidden');
                pesanError.textContent = '';
            }


            // RESET INPUT DAN HASIL TABEL
            inputPerubahan.forEach(input => {

                input.value = 0;

                const baris = input.closest('tr');
                const kolomHasil = baris.querySelector('[data-after]');

                kolomHasil.textContent =
                    formatAngka(Number(input.dataset.current));

            });


            // RESET RINGKASAN
            document.getElementById('ringkasanTotalAwal').textContent =
                formatAngka(totalAwal);

            document.getElementById('ringkasanTotalBaru').textContent =
                formatAngka(totalAwal);

            document.getElementById('ringkasanPerubahan').textContent = '0';

            document.getElementById('ringkasanPersentase').textContent =
                'Belum ada perubahan skenario';


            // RESET TOTAL TABEL
            const totalAwalTabel =
                document.getElementById('totalAwalTabel');

            const totalPerubahanTabel =
                document.getElementById('totalPerubahanTabel');

            const totalBaruTabel =
                document.getElementById('totalBaruTabel');

            if (totalAwalTabel) {
                totalAwalTabel.textContent = formatAngka(totalAwal);
            }

            if (totalPerubahanTabel) {
                totalPerubahanTabel.textContent = '0';
            }

            if (totalBaruTabel) {
                totalBaruTabel.textContent = formatAngka(totalAwal);
            }


            // RESET PERBANDINGAN KOMPOSISI
            halamanWhatIf.querySelectorAll('[data-comparison-row]')
                .forEach(row => {

                    const shareAwal = Number(
                        row.querySelector('[data-bar-awal]')
                            .dataset.initialShare
                    );

                    row.querySelector('[data-share]').textContent =
                        formatPersen(shareAwal) +
                        ' → ' +
                        formatPersen(shareAwal);

                    row.querySelector('[data-bar-awal]').style.width =
                        shareAwal + '%';

                    row.querySelector('[data-bar-simulasi]').style.width =
                        shareAwal + '%';

                });


            // RESET KARTU DAMPAK
            dampakPersentase.textContent = '0,0%';

            dampakPersentase.className =
                'mt-1 text-2xl font-bold text-[#365F65]';

            dampakKeterangan.textContent =
                'Belum ada perubahan skenario';


            // RESET ANALISIS DAMPAK
            analisisDampak.innerHTML = '';

            const item = document.createElement('li');
            item.className = 'flex items-start gap-2';

            const ikon = document.createElement('span');
            ikon.className = 'font-bold text-[#527D82]';
            ikon.textContent = '•';

            const isi = document.createElement('span');
            isi.textContent =
                'Atur perubahan jumlah tenant, kemudian tekan Hitung What-If untuk melihat dampaknya.';

            item.append(ikon, isi);

            analisisDampak.appendChild(item);

        }


        // EVENT LISTENER
        if (tombolHitung) {
            tombolHitung.addEventListener('click', hitungWhatIf);
        }

        if (tombolReset) {
            tombolReset.addEventListener('click', resetWhatIf);
        }

    });
</script>

@endsection