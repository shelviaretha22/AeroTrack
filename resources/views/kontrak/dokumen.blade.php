
@extends(($isPdf ?? false)
    ? 'kontrak.layout-pdf'
    : 'layouts.app')

@section('page-title', 'Dokumen Kontrak')

@section('content')

@php
    $isPdf = $isPdf ?? false;
    $signatureData = $signatureData ?? null;
    $isPreview = $isPreview ?? false;

    $kerjaSama = $kerjaSama ?? $kontrak?->kerjaSama;
    $tenant = $tenant ?? $kontrak?->tenant ?? $kerjaSama?->tenant;
    $lokasi = $lokasi ?? $kontrak?->lokasi ?? $kerjaSama?->lokasi;

    $nomorKontrak = $kontrak?->nomor_kontrak
        ?? '[Nomor kontrak akan dibuat setelah BA dan Aktivasi selesai]';

    $nomorKerjaSama = $kerjaSama?->nomor_kerja_sama
        ?: ($kerjaSama
            ? 'KS-' . str_pad($kerjaSama->id, 5, '0', STR_PAD_LEFT)
            : '-');

    $mitra = $kerjaSama?->mitra_usaha
        ?? $tenant?->mitra_usaha
        ?? '-';

    $brand = $kerjaSama?->brand
        ?? $tenant?->nama_tenant
        ?? '-';

    $jenisUsaha = $kerjaSama?->jenis_usaha
        ?? $tenant?->jenis_usaha
        ?? '-';

    $namaPicCommercial = $kerjaSama?->pic_commercial
    ?? $kontrak?->nama_commercial
    ?? '[Nama PIC Komersil]';

$jabatanPicCommercial = $kerjaSama?->jabatan_pic_commercial
    ?? $kontrak?->jabatan_commercial
    ?? '[Jabatan PIC Komersil]';

$alamatKantorPengelola = '[Alamat kantor pengelola belum dikonfigurasi]';
    $npwp = $tenant?->npwp ?? '-';
    $nib = $tenant?->nib ?? '-';

    $kodeTenant = $tenant?->id ?? '-';

    $kodeRuang = $lokasi?->kode_ruang ?? '-';
    $namaLokasi = $lokasi?->lokasi ?? '-';
    $terminal = $lokasi?->terminal ?? '-';
    $lantai = $lokasi?->lantai ?? '-';

    $tanggalMulai = $kerjaSama?->tanggal_mulai
        ? \Illuminate\Support\Carbon::parse($kerjaSama->tanggal_mulai)
            ->locale('id')->translatedFormat('d F Y')
        : '[Tanggal mulai kerja sama]';

    $tanggalBerakhir = $kerjaSama?->tanggal_berakhir
        ? \Illuminate\Support\Carbon::parse($kerjaSama->tanggal_berakhir)
            ->locale('id')->translatedFormat('d F Y')
        : '[Tanggal berakhir kerja sama]';

    $tanggalHariIni = now()->locale('id')->translatedFormat('l, d F Y');

    $jabatanPicCommercial = $kerjaSama?->jabatan_pic_commercial
        ?? $kontrak?->jabatan_commercial
        ?? '[Jabatan PIC Komersil]';

    $alamatKantorPengelola = 'Jl. Raya Bandara Juanda, Sidoarjo, Jawa Timur';

    
$durasiKerjaSama = $kerjaSama?->tanggal_mulai
    && $kerjaSama?->tanggal_berakhir
    ? (
        \Illuminate\Support\Carbon::parse($kerjaSama->tanggal_mulai)
            ->diffInDays(
                \Illuminate\Support\Carbon::parse($kerjaSama->tanggal_berakhir)
            ) . ' hari'
    )
    : '[Durasi sewa belum tersedia]';


    $klausulTambahan = $kontrak?->klausul_tambahan ?? '';
@endphp

<style>
/* =========================================================
   1. DASAR HALAMAN DAN JUDUL
   ========================================================= */
.dokumen-page {
    width: 100%;
    max-width: 1200px;
    margin: 0 auto;
    padding: 12px 24px 24px;
    color: #243B3B;
    box-sizing: border-box;
}

.dokumen-page .page-heading {
    margin: 0 0 12px;
    padding: 0;
    text-align: left;
}

.dokumen-page .page-title {
    margin: 0;
    color: #244D50;
    font-size: 22px;
    font-weight: 700;
    line-height: 1.3;
}

.dokumen-page .page-subtitle {
    margin: 4px 0 0;
    color: #7A9290;
    font-size: 13px;
    line-height: 1.5;
}

/* =========================================================
   2. INDIKATOR LANGKAH
   ========================================================= */
.dokumen-page .contract-steps {
    display: flex;
    align-items: flex-start;
    justify-content: center;
    max-width: 640px;
    margin: 12px auto 18px;
    gap: 0;
}

.dokumen-page .step-number {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    border: 1px solid #DCE6E4;
    border-radius: 50%;
    background: #F3F6F5;
    color: #7A9290;
    font-size: 13px;
    font-weight: 700;
    box-sizing: border-box;
}

.dokumen-page .contract-step.active {
    color: #365F65;
    font-weight: 700;
}

.dokumen-page .contract-step.active .step-number {
    background: #16877F;
    border-color: #16877F;
    color: #FFFFFF;
}

.dokumen-page .step-line {
    flex: 1;
    height: 2px;
    margin-top: 17px;
    background: #DCE6E4;
}

/* =========================================================
   3. TOOLBAR DAN TOMBOL
   ========================================================= */
.dokumen-page .toolbar {
    width: 100%;
    max-width: 850px;
    margin: 18px auto 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
}

.dokumen-page .toolbar-group {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 8px;
}

.dokumen-page .btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 10px 18px;
    border: 1px solid transparent;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
    cursor: pointer;
    box-sizing: border-box;
}

.dokumen-page .btn-back {
    background: #FFFFFF;
    border-color: #DCE6E4;
    color: #365F65;
}

.dokumen-page .btn-print {
    background: #365F65;
    color: #FFFFFF;
}

.dokumen-page .btn-edit {
    background: #FFFFFF;
    border-color: #16877F;
    color: #16877F;
}

.dokumen-page .btn-save,
.dokumen-page .btn-next {
    background: #16877F;
    color: #FFFFFF;
}

.dokumen-page .btn:disabled {
    opacity: .5;
    cursor: not-allowed;
}

/* =========================================================
   4. CATATAN, PANEL CHECKLIST, DAN PANEL EDIT
   ========================================================= */
.dokumen-page .document-note {
    margin-bottom: 20px;
    padding: 14px 16px;
    border: 1px solid #E7D9B5;
    border-radius: 8px;
    background: #FFF9E9;
    color: #725C2E;
    font-size: 13px;
    line-height: 1.7;
}

.dokumen-page .check-panel,
.dokumen-page .edit-panel {
    max-width: 850px;
    margin: 24px auto 0;
    padding: 22px;
    border: 1px solid #DCE6E4;
    border-radius: 12px;
    background: #FFFFFF;
    box-sizing: border-box;
}

.dokumen-page .check-label {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    color: #365F65;
    font-size: 14px;
    line-height: 1.7;
}

.dokumen-page .check-label input {
    margin-top: 5px;
    accent-color: #16877F;
}

.dokumen-page .check-actions {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
    margin-top: 22px;
    margin-bottom: 18px;
}

.dokumen-page .bottom-actions {
    display: flex;
    align-items: center;
    justify-content: flex-start;
    flex-wrap: wrap;
    gap: 12px;
    margin-top: 0;
    padding-top: 18px;
    border-top: 1px solid #E2E8E7;
}

.dokumen-page .bottom-actions .btn {
    min-height: 42px;
    padding: 10px 20px;
    white-space: nowrap;
}

.dokumen-page .check-help {
    color: #7A9290;
    font-size: 12px;
}

.dokumen-page .edit-panel textarea {
    width: 100%;
    min-height: 140px;
    margin-top: 10px;
    padding: 12px;
    border: 1px solid #CBD8D6;
    border-radius: 8px;
    font-size: 14px;
    line-height: 1.6;
    box-sizing: border-box;
}

.dokumen-page .edit-panel[hidden] {
    display: none !important;
}

/* =========================================================
   5. KERTAS KONTRAK - TAMPILAN BROWSER
   ========================================================= */
.dokumen-page .paper {
    box-sizing: border-box;
    width: 100%;
    max-width: 850px;
    margin: 0 auto 18px;
    padding: 20mm 22mm;
    border: 1px solid #E2E8E7;
    background: #FFFFFF;
    box-shadow: 0 4px 18px rgba(36, 77, 80, 0.06);
    color: #222222;
    font-family: "Times New Roman", serif;
    font-size: 12pt;
    line-height: 1.5;
}

.dokumen-page .paper-header {
    margin-bottom: 24px;
    padding-bottom: 14px;
    border-bottom: 2px solid #365F65;
    text-align: center;
}

.dokumen-page .paper-header h2 {
    margin: 0;
    font-size: 15pt;
    font-weight: 700;
}

.dokumen-page .paper-header p {
    margin: 3px 0;
    font-size: 11pt;
    line-height: 1.4;
    text-align: center;
}

.dokumen-page .document-title {
    margin: 0 0 10px;
    text-align: center;
    font-size: 15pt;
    line-height: 1.4;
    font-weight: 700;
    text-decoration: none;
}

.dokumen-page .paper .airport-title,
.dokumen-page .airport-title {
    display: block;
    width: 100%;
    margin: 0 auto 12px;
    padding: 0;
    box-sizing: border-box;
    text-align: center;
    font-size: 12pt;
    line-height: 1.4;
    font-weight: 700;
}

.dokumen-page .document-number {
    margin: 8px 0 16px;
    padding-bottom: 10px;
    text-align: center;
    font-size: 12pt;
    line-height: 1.4;
    border-bottom: 1px solid #222222;
}

.dokumen-page .paper p {
    margin: 0 0 10px;
    text-align: justify;
    text-indent: 0;
    font-size: 12pt;
    line-height: 1.5;
    orphans: 2;
    widows: 2;
}

/* =========================================================
   6. JUDUL PASAL DAN IDENTITAS PARA PIHAK
   ========================================================= */
.dokumen-page .paper .section-title {
    margin: 22px 0 8px;
    text-align: center;
    font-size: 12pt;
    font-weight: 700;
    line-height: 1.4;
    break-after: avoid;
    page-break-after: avoid;
}

.dokumen-page .paper .section-subtitle {
    margin: 0 0 12px;
    text-align: center;
    font-size: 12pt;
    font-weight: 700;
    line-height: 1.4;
    break-after: avoid;
    page-break-after: avoid;
}

.dokumen-page .paper .party-title {
    margin: 18px 0 8px;
    text-align: left;
    font-size: 12pt;
    font-weight: 700;
    line-height: 1.4;
    break-after: avoid;
    page-break-after: avoid;
}

.dokumen-page .paper table.party-details {
    width: calc(100% - 24px);
    margin: 5px 0 12px 24px;
    table-layout: fixed;
    border-collapse: collapse;
}

.dokumen-page .paper table.party-details .label-col {
    width: 36%;
}

.dokumen-page .paper p.party-description {
    margin: 6px 0 12px;
    padding-left: 24px;
    text-indent: 0;
    line-height: 1.5;
    text-align: justify;
}

/* =========================================================
   7. TABEL DAN RINCIAN OBJEK
   ========================================================= */
.dokumen-page .paper table {
    width: 100%;
    margin: 7px 0 12px;
    border-collapse: collapse;
    table-layout: fixed;
}

.dokumen-page .paper td {
    padding: 4px 5px;
    vertical-align: top;
    overflow-wrap: break-word;
    word-wrap: break-word;
    font-size: 11.5pt;
    line-height: 1.45;
}

.dokumen-page .paper .label-col {
    width: 34%;
}

.dokumen-page .paper table.object-details {
    width: calc(100% - 24px);
    margin: 7px 0 12px 24px;
    table-layout: fixed;
    border-collapse: collapse;
}

/* =========================================================
   8. PENOMORAN AYAT DAN SUBAYAT
   ========================================================= */
.dokumen-page .paper p.clause {
    margin: 6px 0 9px;
    padding-left: 24px;
    text-indent: -24px;
    line-height: 1.5;
    text-align: justify;
}

.dokumen-page .paper p.subclause {
    margin: 5px 0 8px;
    padding-left: 48px;
    text-indent: -24px;
    line-height: 1.45;
    text-align: justify;
}

/* =========================================================
   9. TANDA TANGAN
   Menggunakan tabel agar kompatibel dengan DomPDF.
   ========================================================= */
.dokumen-page .paper .signature-grid {
    width: 100%;
    margin-top: 32px;
    border-collapse: collapse;
    table-layout: fixed;
    page-break-inside: avoid;
    break-inside: avoid;
}

.dokumen-page .paper .signature-grid td {
    width: 50%;
    padding: 0 10px;
    vertical-align: top;
    text-align: center;
    border: none;
    font-size: 11.5pt;
    line-height: 1.4;
}

.dokumen-page .paper .signature-grid p {
    margin: 4px 0;
    text-align: center;
    line-height: 1.4;
}

.dokumen-page .paper .signature-heading {
    min-height: 54px;
}

.dokumen-page .paper .signature-space {
    display: block;
    width: 90%;
    max-width: 180px;
    height: 68px;
    margin: 0 auto 10px;
    border-bottom: 1px solid #000000;
    box-sizing: border-box;
}

.dokumen-page .paper .signature-line {
    height: 0;
    margin: 0 8px 8px;
    border-top: 1px solid #222222;
}

/* =========================================================
   10. RESPONSIVE UNTUK BROWSER / LAYAR KECIL
   ========================================================= */
@media (max-width: 640px) {
    .dokumen-page {
        padding: 12px 12px 20px;
    }

    .dokumen-page .bottom-actions {
        flex-direction: column;
        align-items: stretch;
    }

    .dokumen-page .bottom-actions .btn {
        width: 100%;
        white-space: normal;
    }

    .dokumen-page .contract-step {
        min-width: 76px;
    }

    .dokumen-page .step-label {
        font-size: 10px;
    }

    .dokumen-page .paper {
        padding: 24px 18px;
        font-size: 11pt;
    }

    .dokumen-page .paper .label-col {
        width: 34%;
    }

    .dokumen-page .paper .signature-grid td {
        padding: 0 5px;
    }
}

/* =========================================================
   11. PENGATURAN CETAK DARI BROWSER
   ========================================================= */
@page {
    size: A4 portrait;
    margin: 20mm 22mm;
}

@media print {
    body {
        background: #FFFFFF !important;
    }

    body * {
        visibility: hidden !important;
    }

    .dokumen-page .paper,
    .dokumen-page .paper * {
        visibility: visible !important;
    }

    .dokumen-page {
        width: 100%;
        max-width: none;
        margin: 0;
        padding: 0;
    }

    .dokumen-page .paper {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        max-width: none;
        margin: 0;
        padding: 0;
        border: none;
        box-shadow: none;
        font-size: 12pt;
        line-height: 1.5;
    }

    .dokumen-page .page-heading,
    .dokumen-page .contract-steps,
    .dokumen-page .toolbar,
    .dokumen-page .document-note,
    .dokumen-page .check-panel,
    .dokumen-page .edit-panel {
        display: none !important;
    }

    .dokumen-page .paper .section-title {
        margin-top: 22px;
    }

    .dokumen-page .paper tr,
    .dokumen-page .paper .signature-grid {
        break-inside: avoid;
        page-break-inside: avoid;
    }
}

/* =========================================================
   12. KHUSUS DOMPDF / PDF PREVIEW DAN DOWNLOAD
   Jangan gunakan flex/grid untuk struktur isi PDF.
   ========================================================= */
.dokumen-page.pdf-mode {
    width: 100%;
    max-width: none;
    margin: 0;
    padding: 0;
    color: #222222;
    font-family: "DejaVu Serif", serif;
}


.dokumen-page.pdf-mode .paper {
    width: 100%;
    max-width: none;
    box-sizing: border-box;
    margin: 0;
    padding: 0;
    border: none;
    background: #FFFFFF;
    box-shadow: none;
    color: #222222;
    font-family: "DejaVu Serif", serif;
    font-size: 12pt;
    line-height: 1.5;
}

.dokumen-page.pdf-mode .paper p {
    margin: 0 0 12px;
    font-family: "DejaVu Serif", serif;
    font-size: 12pt;
    line-height: 1.5;
    text-align: justify;
    overflow-wrap: break-word;
    word-wrap: break-word;
}

.dokumen-page.pdf-mode .paper .document-title {
    margin: 0 0 14px;
    font-size: 15pt;
    line-height: 1.4;
    text-align: center;
    font-weight: bold;
}

.dokumen-page.pdf-mode .paper .airport-title {
    display: block;
    width: 100%;
    margin: 0 0 14px;
    text-align: center;
    font-size: 12pt;
    line-height: 1.4;
    font-weight: bold;
}

.dokumen-page.pdf-mode .paper .document-number {
    margin: 8px 0 20px;
    padding-bottom: 12px;
    text-align: center;
    font-size: 12pt;
    line-height: 1.4;
    border-bottom: 1px solid #222222;
}

.dokumen-page.pdf-mode .paper .section-title {
    margin: 22px 0 8px;
    text-align: center;
    font-size: 12pt;
    line-height: 1.4;
    font-weight: bold;
    page-break-after: avoid;
}

.dokumen-page.pdf-mode .paper .section-subtitle {
    margin: 0 0 14px;
    text-align: center;
    font-size: 12pt;
    line-height: 1.4;
    font-weight: bold;
    page-break-after: avoid;
}

.dokumen-page.pdf-mode .paper .party-title {
    margin: 18px 0 8px;
    font-size: 12pt;
    line-height: 1.4;
    font-weight: bold;
    page-break-after: avoid;
}

/* Tabel identitas pihak untuk PDF */
.dokumen-page.pdf-mode .paper table.party-details {
    width: 100%;
    margin: 5px 0 12px;
    table-layout: fixed;
    border-collapse: collapse;
}

.dokumen-page.pdf-mode .paper table.party-details td:first-child {
    width: 36%;
}

.dokumen-page.pdf-mode .paper table.party-details td:last-child {
    width: 64%;
}

/* Tabel rincian objek untuk PDF */
.dokumen-page.pdf-mode .paper table.object-details {
    width: 100%;
    margin: 7px 0 12px;
    table-layout: fixed;
    border-collapse: collapse;
}

.dokumen-page.pdf-mode .paper table.object-details td:first-child {
    width: 36%;
}

.dokumen-page.pdf-mode .paper table.object-details td:last-child {
    width: 64%;
}

.dokumen-page.pdf-mode .paper table {
    width: 100%;
    max-width: 100%;
    margin-top: 7px;
    margin-bottom: 12px;
    border-collapse: collapse;
    table-layout: fixed;
}

.dokumen-page.pdf-mode .paper td {
    box-sizing: border-box;
    padding: 4px 5px;
    vertical-align: top;
    font-family: "DejaVu Serif", serif;
    font-size: 11.5pt;
    line-height: 1.45;
    overflow-wrap: break-word;
    word-wrap: break-word;
}

.dokumen-page.pdf-mode .paper p.party-description {
    margin: 6px 0 12px;
    padding-left: 24px;
    text-indent: 0;
    font-size: 12pt;
    line-height: 1.5;
    text-align: justify;
}

.dokumen-page.pdf-mode .paper p.clause {
    margin: 6px 0 9px;
    padding-left: 24px;
    text-indent: -24px;
    font-size: 12pt;
    line-height: 1.5;
    text-align: justify;
}

.dokumen-page.pdf-mode .paper p.subclause {
    margin: 5px 0 8px;
    padding-left: 48px;
    text-indent: -24px;
    font-size: 12pt;
    line-height: 1.45;
    text-align: justify;
}

/* Tabel tanda tangan untuk PDF */
.dokumen-page.pdf-mode .paper .signature-grid {
    display: table;
    width: 100%;
    margin-top: 32px;
    border-collapse: collapse;
    table-layout: fixed;
    page-break-inside: avoid;
}

.dokumen-page.pdf-mode .paper .signature-grid tr {
    page-break-inside: avoid;
}

.dokumen-page.pdf-mode .paper .signature-grid td {
    display: table-cell;
    width: 50%;
    padding: 0 10px;
    vertical-align: top;
    text-align: center;
    border: none;
    font-size: 11.5pt;
    line-height: 1.4;
}

.dokumen-page.pdf-mode .paper .signature-grid p {
    margin: 4px 0;
    text-align: center;
    font-size: 11.5pt;
    line-height: 1.4;
}

.dokumen-page.pdf-mode .paper .signature-heading {
    min-height: 54px;
}

.dokumen-page.pdf-mode .paper .signature-space {
    display: block;
    width: 90%;
    max-width: 180px;
    height: 68px;
    margin: 0 auto 10px;
    border-bottom: 1px solid #222222;
}
</style>


<div class="dokumen-page {{ $isPdf ? 'pdf-mode' : '' }}">

    @if (!$isPdf)

        <div class="page-heading">
            <h1 class="page-title">Isi Dokumen Kontrak</h1>
            <p class="page-subtitle">
                Periksa isi dokumen sebelum melanjutkan ke tahap ACC.
            </p>
        </div>

        <div class="contract-steps">
            <div class="contract-step active">
                <span class="step-number">1</span>
                <span class="step-label">Dokumen Kontrak</span>
            </div>

            <div class="step-line"></div>

            <div class="contract-step">
                <span class="step-number">2</span>
                <span class="step-label">ACC Kontrak</span>
            </div>

            <div class="step-line"></div>

            <div class="contract-step">
                <span class="step-number">3</span>
                <span class="step-label">Upload Kontrak Final</span>
            </div>
        </div>

    @endif

    <article class="paper">

        <div class="document-title">
            SURAT PERJANJIAN KERJA SAMA DAN SEWA TENANT
        </div>

        <p class="airport-title">
            BANDAR UDARA INTERNASIONAL JUANDA
        </p>

        <div class="document-number">
            Nomor: {{ $nomorKontrak }}
        </div>

        <p>
            Pada hari ini, {{ $tanggalHariIni }}, bertempat di Bandar
            Udara Internasional Juanda, para pihak yang bertanda tangan
            di bawah ini:
        </p>

        <p class="party-title">
            1. PIHAK PERTAMA (MITRA USAHA / PENYEWA)
        </p>

        <table class="party-details">
            <tr>
                <td class="label-col">Nama Mitra Usaha</td>
                <td>: {{ $mitra }}</td>
            </tr>
            <tr>
                <td>Nama Brand</td>
                <td>: {{ $brand }}</td>
            </tr>
            <tr>
                <td>Jenis Usaha</td>
                <td>: {{ $jenisUsaha }}</td>
            </tr>
            <tr>
    <td>Nama PIC Tenant</td>
    <td>: {{ $tenant?->nama_pic ?? '-' }}</td>
    </tr>
    <tr>
        <td>Jabatan</td>
        <td>: {{ $tenant?->jabatan_pic ?? '-' }}</td>
    </tr>
    <tr>
        <td>Alamat</td>
        <td>: {{ $tenant?->alamat_perusahaan ?? '-' }}</td>
    </tr>
    <tr>
        <td>Nomor Pokok Wajib Pajak (NPWP)</td>
        <td>: {{ $tenant?->npwp ?? '-' }}</td>
    </tr>
        </table>

        <p class="party-description">
            Dalam hal ini bertindak untuk dan atas nama {{ $mitra }},
            selanjutnya disebut sebagai <strong>PIHAK PERTAMA</strong>.
        </p>

        <p class="party-title">
            2. PIHAK KEDUA (PIHAK KOMERSIL / PENGELOLA BANDARA)
        </p>

        <table class="party-details">
            <tr>
                <td class="label-col">Nama</td>
                <td>: {{ $namaPicCommercial }}</td>
            </tr>
            <tr>
                <td>Jabatan</td>
                <td>: {{ $jabatanPicCommercial }}</td>
            </tr>
            <tr>
                <td>Unit/Instansi</td>
                <td>: PT Angkasa Pura Indonesia (InJourney Airports) –
                    Bandar Udara Internasional Juanda</td>
            </tr>
            <tr>
                <td>Alamat Kantor Pengelola</td>
                <td>: {{ $alamatKantorPengelola }}</td>
            </tr>
        </table>

        <p class="party-description">
            Dalam hal ini bertindak untuk dan atas nama pengelola kegiatan
            komersial Bandar Udara Internasional Juanda sesuai dengan
            kewenangan yang dimiliki, selanjutnya disebut sebagai
            <strong>PIHAK KEDUA</strong>.
        </p>

        <p class="party-description">
            PIHAK PERTAMA dan PIHAK KEDUA secara bersama-sama selanjutnya
            disebut sebagai PARA PIHAK.
        </p>

        <p>
            PARA PIHAK terlebih dahulu menerangkan bahwa PIHAK KEDUA
            memiliki kewenangan untuk mengelola pemanfaatan area komersial
            di lingkungan Bandar Udara Internasional Juanda, sedangkan
            PIHAK PERTAMA bermaksud menjalankan kegiatan usaha pada area
            yang disepakati.
        </p>

        <p>
            Berdasarkan hal tersebut, PARA PIHAK sepakat untuk mengadakan
            perjanjian kerja sama dan sewa tenant dengan ketentuan sebagai
            berikut.
        </p>

        <h3 class="section-title">PASAL I</h3>
        <h3 class="section-subtitle">OBJEK DAN RUANG LINGKUP PERJANJIAN</h3>

        <p class="clause">
            1. PIHAK KEDUA memberikan hak pemanfaatan area tenant kepada
            PIHAK PERTAMA untuk menjalankan kegiatan usaha sesuai dengan
            jenis usaha yang telah disepakati.
        </p>

        <p class="clause">
            2. Objek perjanjian berada di area komersial Bandar Udara
            Internasional Juanda dengan rincian sebagai berikut:
        </p>

        <table class="object-details">
            <tr>
                <td class="label-col">a. ID Tenant</td>
                <td>: {{ $kodeTenant }}</td>
            </tr>
            <tr>
                <td>b. Kode Lokasi</td>
                <td>: {{ $kodeRuang }}</td>
            </tr>
            <tr>
                <td>c. Nama Lokasi</td>
                <td>: {{ $namaLokasi }}</td>
            </tr>
            <tr>
                <td>d. Terminal</td>
                <td>: {{ $terminal }}</td>
            </tr>
            <tr>
                <td>e. Lantai</td>
                <td>: {{ $lantai }}</td>
            </tr>
            <tr>
                <td>f. Jenis Ruangan</td>
                <td>: {{ $lokasi?->jenis_ruangan ?: '[Jenis Ruangan belum tersedia]' }}</td>
            </tr>
            <tr>
                <td>g. Luas Area</td>
                <td>: {{ $lokasi?->luas_area !== null
                    ? $lokasi->luas_area . ' m²'
                    : '[Luas Area belum tersedia]' }}</td>
            </tr>
            <tr>
                <td>h. Jenis Usaha</td>
                <td>: {{ $jenisUsaha }}</td>
            </tr>
            <tr>
                <td>i. Bentuk Kerja Sama</td>
                <td>: {{ $kerjaSama?->bentuk_kerja_sama
                    ?: ($tenant?->bentuk_kerja_sama ?: '[Bentuk Kerja Sama belum tersedia]') }}</td>
            </tr>
        </table>

        <p class="clause">
            3. Area tenant hanya dapat digunakan untuk kegiatan usaha yang
            telah memperoleh persetujuan PIHAK KEDUA.
        </p>

        <p class="clause">
            4. Perubahan lokasi, luas area, jenis usaha, atau bentuk
            pemanfaatan area wajib memperoleh persetujuan tertulis dari
            PIHAK KEDUA.
        </p>

        <p class="clause">
            5. Rincian lokasi dan kondisi area tenant dituangkan dalam
            dokumen pendukung atau Berita Acara yang menjadi bagian tidak
            terpisahkan dari perjanjian ini.
        </p>

        <h3 class="section-title">PASAL II</h3>
        <h3 class="section-subtitle">JANGKA WAKTU PERJANJIAN</h3>

        <p class="clause">
            1. Perjanjian ini berlaku sejak tanggal {{ $tanggalMulai }}
            sampai dengan tanggal {{ $tanggalBerakhir }}.
        </p>

        <p class="clause">
            2. Jangka waktu sewa atau kerja sama adalah selama
            {{ $durasiKerjaSama }}, sesuai dengan ketentuan yang
            disepakati PARA PIHAK.
        </p>

        <p class="clause">
            3. Tanggal mulai operasional tenant dapat ditetapkan berdasarkan
            kesiapan area, penyelesaian persyaratan administrasi, dan
            dokumen aktivasi yang telah disetujui.
        </p>

        <p class="clause">
            4. Perpanjangan jangka waktu perjanjian dapat dilakukan
            berdasarkan kesepakatan tertulis PARA PIHAK dengan
            memperhatikan ketentuan yang berlaku.
        </p>

        <p class="clause">
            5. Permohonan perpanjangan wajib diajukan sebelum berakhirnya
            masa perjanjian sesuai dengan jangka waktu pemberitahuan yang
            disepakati PARA PIHAK.
        </p>

        <p class="clause">
            6. Berakhirnya jangka waktu perjanjian tidak menghapus
            kewajiban PARA PIHAK yang masih belum diselesaikan.
        </p>
        
    <h3 class="section-title">PASAL III</h3>
    <h3 class="section-subtitle">
        NILAI SEWA DAN KEWAJIBAN PEMBAYARAN
    </h3>

    <p class="clause">
        1. Nilai sewa atau imbalan atas pelaksanaan kerja sama
        ditetapkan berdasarkan kesepakatan PARA PIHAK dan dituangkan
        dalam dokumen tertulis yang menjadi bagian dari perjanjian ini.
    </p>

    <p class="clause">
        2. PARA PIHAK sepakat bahwa ketentuan mengenai besaran nilai
        sewa, tata cara penagihan, mekanisme pembayaran, serta
        kewajiban keuangan lainnya dilaksanakan berdasarkan
        kesepakatan tertulis dan ketentuan yang berlaku.
    </p>

    <p class="clause">
        3. Setiap perubahan nilai sewa atau ketentuan pembayaran
        wajib memperoleh persetujuan tertulis PARA PIHAK dan dapat
        dituangkan dalam dokumen pendukung atau addendum yang menjadi
        bagian dari perjanjian ini.
    </p>

        <h3 class="section-title">PASAL IV</h3>
        <h3 class="section-subtitle">HAK DAN KEWAJIBAN PARA PIHAK</h3>

        <p><strong>A. Hak dan Kewajiban PIHAK PERTAMA</strong></p>

        <p class="clause">
            1. PIHAK PERTAMA berhak menggunakan area tenant sesuai dengan
            lokasi, luas, dan peruntukan yang telah disepakati.
        </p>
        <p class="clause">
            2. PIHAK PERTAMA wajib menjalankan kegiatan usaha sesuai dengan
            izin, ketentuan operasional bandara, standar keselamatan,
            keamanan, dan peraturan yang berlaku.
        </p>
        <p class="clause">
            3. PIHAK PERTAMA wajib menjaga kebersihan, ketertiban,
            keamanan, dan kondisi area tenant selama masa perjanjian.
        </p>
        <p class="clause">
            4. PIHAK PERTAMA wajib memenuhi kewajiban pembayaran sesuai
            dengan nilai dan jadwal yang telah ditetapkan.
        </p>
        <p class="clause">
            5. PIHAK PERTAMA wajib melengkapi dokumen administrasi,
            perizinan, dan persyaratan operasional yang diperlukan.
        </p>
        <p class="clause">
            6. PIHAK PERTAMA dilarang mengalihkan hak penggunaan area
            kepada pihak lain tanpa persetujuan tertulis PIHAK KEDUA.
        </p>
        <p class="clause">
            7. PIHAK PERTAMA wajib melaporkan kendala operasional yang
            berpengaruh terhadap pelaksanaan perjanjian kepada PIHAK KEDUA.
        </p>
        <p class="clause">
            8. PIHAK PERTAMA wajib mematuhi ketentuan mengenai akses,
            penggunaan fasilitas, dan aktivitas komersial di lingkungan
            bandara.
        </p>

        <p><strong>B. Hak dan Kewajiban PIHAK KEDUA</strong></p>

        <p class="clause">
            1. PIHAK KEDUA berhak menerima pembayaran sewa atau imbalan
            kerja sama sesuai dengan ketentuan perjanjian.
        </p>
        <p class="clause">
            2. PIHAK KEDUA berhak melakukan pemantauan dan evaluasi
            terhadap penggunaan area serta pelaksanaan kewajiban PIHAK
            PERTAMA.
        </p>
        <p class="clause">
            3. PIHAK KEDUA wajib menyediakan hak pemanfaatan area sesuai
            dengan objek dan ketentuan yang telah disepakati.
        </p>
        <p class="clause">
            4. PIHAK KEDUA wajib menyampaikan informasi mengenai ketentuan
            operasional, administrasi, dan pembayaran yang relevan kepada
            PIHAK PERTAMA.
        </p>
        <p class="clause">
            5. PIHAK KEDUA berhak meminta perbaikan atau tindak lanjut
            apabila ditemukan ketidaksesuaian dalam pelaksanaan perjanjian.
        </p>
        <p class="clause">
            6. PIHAK KEDUA wajib menindaklanjuti permasalahan yang menjadi
            tanggung jawabnya sesuai dengan kewenangan dan ketentuan yang
            berlaku.
        </p>

        <h3 class="section-title">PASAL V</h3>
        <h3 class="section-subtitle">AKTIVASI DAN PELAKSANAAN OPERASIONAL TENANT</h3>

        <p class="clause">
            1. PIHAK PERTAMA wajib menyelesaikan seluruh persyaratan
            administrasi dan operasional sebelum memulai kegiatan usaha.
        </p>
        <p class="clause">
            2. Aktivasi tenant dilakukan setelah persyaratan yang
            ditetapkan PIHAK KEDUA dinyatakan lengkap dan memenuhi
            ketentuan.
        </p>
        <p class="clause">
            3. Persyaratan aktivasi dapat mencakup dokumen perjanjian,
            perizinan usaha, kesiapan area, kebutuhan utilitas, serta
            dokumen pendukung lainnya sesuai dengan jenis usaha.
        </p>
        <p class="clause">
            4. Tanggal aktivasi dan tanggal mulai operasional dicatat
            dalam dokumen administrasi atau Berita Acara yang disepakati
            PARA PIHAK.
        </p>
        <p class="clause">
            5. PIHAK PERTAMA tidak diperkenankan memulai operasional
            sebelum memperoleh persetujuan atau pemenuhan persyaratan yang
            diwajibkan oleh PIHAK KEDUA.
        </p>
        <p class="clause">
            6. Apabila terdapat kendala yang menghambat aktivasi, PARA
            PIHAK wajib melakukan koordinasi untuk menentukan tindak
            lanjut sesuai dengan tanggung jawab masing-masing.
        </p>
        <p class="clause">
            7. Pelaksanaan operasional tenant wajib memperhatikan
            ketentuan keselamatan, keamanan, kebersihan, pelayanan, dan
            tata tertib Bandar Udara Internasional Juanda.
        </p>

        <h3 class="section-title">PASAL VI</h3>
        <h3 class="section-subtitle">DOKUMEN PENDUKUNG DAN ADMINISTRASI</h3>

        <p class="clause">
            1. Dokumen pendukung perjanjian dapat meliputi Berita Acara,
            dokumen identitas mitra usaha, dokumen perpajakan, dokumen
            lokasi, rincian nilai sewa, dan dokumen lain yang disepakati
            PARA PIHAK.
        </p>
        <p class="clause">
            2. Setiap dokumen pendukung yang telah disetujui PARA PIHAK
            menjadi bagian yang tidak terpisahkan dari perjanjian ini.
        </p>
        <p class="clause">
            3. PARA PIHAK wajib memastikan bahwa informasi dan dokumen
            yang diberikan adalah benar, lengkap, dan dapat
            dipertanggungjawabkan.
        </p>
        <p class="clause">
            4. Apabila terdapat perubahan data mitra usaha, identitas PIC,
            alamat, dokumen perpajakan, atau informasi lain yang berkaitan
            dengan perjanjian, PIHAK PERTAMA wajib memberitahukan
            perubahan tersebut kepada PIHAK KEDUA.
        </p>
        <p class="clause">
            5. Penyimpanan dan penggunaan dokumen dilakukan untuk
            kepentingan administrasi, pelaksanaan perjanjian, dan
            pemenuhan kewajiban sesuai dengan ketentuan yang berlaku.
        </p>

        <h3 class="section-title">PASAL VII</h3>
        <h3 class="section-subtitle">PERUBAHAN DAN ADDENDUM PERJANJIAN</h3>

        <p class="clause">
            1. Perubahan terhadap isi perjanjian hanya dapat dilakukan
            berdasarkan kesepakatan tertulis PARA PIHAK.
        </p>
        <p class="clause">
            2. Setiap perubahan yang berkaitan dengan objek sewa, nilai
            sewa, jangka waktu, jenis usaha, atau ketentuan lainnya
            dituangkan dalam addendum atau dokumen perubahan yang
            disetujui PARA PIHAK.
        </p>
        <p class="clause">
            3. Addendum yang telah ditandatangani oleh PARA PIHAK menjadi
            bagian yang tidak terpisahkan dari perjanjian ini.
        </p>
        <p class="clause">
            4. Selama perubahan belum disepakati secara tertulis,
            ketentuan dalam perjanjian yang berlaku tetap menjadi acuan
            pelaksanaan hak dan kewajiban PARA PIHAK.
        </p>

        <h3 class="section-title">PASAL VIII</h3>
        <h3 class="section-subtitle">PENGAKHIRAN PERJANJIAN</h3>

        <p class="clause">
            1. Perjanjian ini berakhir setelah jangka waktu sebagaimana
            dimaksud dalam Pasal II berakhir, kecuali PARA PIHAK
            menyepakati perpanjangan secara tertulis.
        </p>
        <p class="clause">
            2. Perjanjian dapat diakhiri sebelum berakhirnya jangka waktu
            berdasarkan kesepakatan tertulis PARA PIHAK atau alasan lain
            yang diperbolehkan berdasarkan perjanjian dan ketentuan yang
            berlaku.
        </p>
        <p class="clause">
            3. Apabila salah satu pihak tidak memenuhi kewajibannya,
            pihak lainnya dapat menyampaikan pemberitahuan tertulis dan
            meminta penyelesaian sesuai dengan mekanisme yang disepakati.
        </p>
        <p class="clause">
            4. Pada saat perjanjian berakhir, PIHAK PERTAMA wajib
            menyelesaikan kewajiban pembayaran yang masih tertunggak
            serta melaksanakan pengembalian area sesuai dengan ketentuan
            yang disepakati.
        </p>
        <p class="clause">
            5. Pengakhiran perjanjian tidak menghapus kewajiban yang telah
            timbul sebelum tanggal pengakhiran.
        </p>
        <p class="clause">
            6. Tata cara pengakhiran, pengosongan area, dan penyelesaian
            kewajiban dilaksanakan dengan memperhatikan ketentuan yang
            berlaku dan kesepakatan PARA PIHAK.
        </p>

        <h3 class="section-title">PASAL IX</h3>
        <h3 class="section-subtitle">KEADAAN KAHAR (FORCE MAJEURE)</h3>

        <p class="clause">
            1. Keadaan kahar adalah peristiwa di luar kendali wajar PARA
            PIHAK yang secara langsung menghambat pelaksanaan sebagian
            atau seluruh kewajiban berdasarkan perjanjian.
        </p>
        <p class="clause">
            2. Keadaan kahar dapat meliputi bencana alam, kebakaran besar,
            perang, kerusuhan, wabah, kebijakan pemerintah, atau peristiwa
            lain yang memenuhi kriteria keadaan kahar sesuai dengan
            ketentuan yang berlaku.
        </p>
        <p class="clause">
            3. Pihak yang mengalami keadaan kahar wajib memberitahukan
            secara tertulis kepada pihak lainnya dalam waktu yang wajar
            dengan menyertakan informasi dan bukti pendukung yang tersedia.
        </p>
        <p class="clause">
            4. PARA PIHAK wajib melakukan koordinasi untuk menentukan
            langkah penyelesaian dan penyesuaian kewajiban yang terdampak.
        </p>
        <p class="clause">
            5. Pelaksanaan ketentuan mengenai keadaan kahar tidak secara
            otomatis menghapus kewajiban pembayaran atau kewajiban lain
            yang telah jatuh tempo, kecuali disepakati lain secara
            tertulis atau ditentukan berdasarkan ketentuan yang berlaku.
        </p>

        <h3 class="section-title">PASAL X</h3>
        <h3 class="section-subtitle">PENYELESAIAN PERSELISIHAN</h3>

        <p class="clause">
            1. Apabila terjadi perselisihan dalam pelaksanaan perjanjian,
            PARA PIHAK sepakat untuk mengutamakan penyelesaian melalui
            musyawarah untuk mencapai mufakat.
        </p>
        <p class="clause">
            2. Apabila musyawarah tidak menghasilkan kesepakatan, PARA
            PIHAK akan menyelesaikan perselisihan melalui mekanisme hukum
            yang berlaku sesuai dengan ketentuan dan kesepakatan yang sah.
        </p>
        <p class="clause">
            3. Selama proses penyelesaian perselisihan berlangsung, PARA
            PIHAK tetap melaksanakan kewajiban yang tidak menjadi objek
            perselisihan sepanjang memungkinkan dan sesuai dengan
            ketentuan yang berlaku.
        </p>

        <h3 class="section-title">PASAL XI</h3>
        <h3 class="section-subtitle">KETENTUAN PENUTUP</h3>

        <p class="clause">
            1. Perjanjian ini dibuat berdasarkan kesepakatan PARA PIHAK
            tanpa adanya paksaan dari pihak mana pun.
        </p>
        <p class="clause">
            2. PARA PIHAK menyatakan telah membaca, memahami, dan
            menyetujui seluruh ketentuan yang tercantum dalam perjanjian ini.
        </p>
        <p class="clause">
            3. Hal-hal yang belum diatur dalam perjanjian ini akan
            diselesaikan berdasarkan kesepakatan tertulis PARA PIHAK
            dengan tetap memperhatikan ketentuan peraturan
            perundang-undangan yang berlaku.
        </p>
        <p class="clause">
            4. Perjanjian ini mulai berlaku sejak tanggal ditandatangani
            oleh PARA PIHAK, kecuali ditentukan lain secara tertulis.
        </p>
        <p class="clause">
            5. Perjanjian dibuat dalam rangkap yang diperlukan, dan setiap
            rangkap memiliki kekuatan hukum yang sama sepanjang memenuhi
            ketentuan yang berlaku.
        </p>

        @if(!empty($klausulTambahan))
            <h3 class="section-title">KETENTUAN TAMBAHAN</h3>
            <p style="white-space: pre-line;">{{ $klausulTambahan }}</p>
        @endif

        <p>
            Demikian Surat Perjanjian Kerja Sama dan Sewa Tenant ini
            dibuat untuk dipahami, disetujui, dan dilaksanakan dengan
            penuh tanggung jawab oleh PARA PIHAK.
        </p>

        <p style="text-align: right; margin-top: 24px;">
            Sidoarjo, {{ $tanggalHariIni }}
        </p>

        

<table class="signature-grid">
    <tbody>
        <tr>
            <td>
                <strong>PIHAK PERTAMA</strong>
                <p><strong>MITRA USAHA / PENYEWA</strong></p>

                <div class="signature-space"></div>

                <strong>{{ $tenant?->nama_pic ?? '-' }}</strong>
                <p>{{ $tenant?->jabatan_pic ?? '-' }}</p>
                <p>{{ $mitra }}</p>
            </td>

            <td>
                <strong>PIHAK KEDUA</strong>
                <p><strong>PIHAK KOMERSIL / PENGELOLA BANDARA</strong></p>

                <div class="signature-space">
                    @if (($isPdf ?? false) && !empty($signatureData))
                        <img
                            src="{{ $signatureData }}"
                            alt="Tanda tangan Commercial"
                            style="max-width: 180px; max-height: 60px;"
                        >
                    @endif
                </div>

                <strong>{{ $namaPicCommercial }}</strong>
                <p>{{ $jabatanPicCommercial }}</p>
                <p>PT Angkasa Pura Indonesia (InJourney Airports)</p>
            </td>
        </tr>
    </tbody>
</table>
</article>

@if (!$isPdf)

    {{-- PERHATIAN DOKUMEN --}}
    <div class="document-note">
        <strong>Perhatian:</strong>
        Periksa kembali seluruh data dan isi perjanjian. Pastikan naskah
        sesuai dengan template resmi yang telah disetujui sebelum digunakan
        sebagai kontrak sebenarnya.
    </div>

    {{-- CHECKLIST PERSETUJUAN DOKUMEN --}}
    <form
        class="check-panel"
        method="GET"
        action="{{ route('kontrak.acc', $kontrak->id) }}"
        id="formLanjutACC"
    >
        <label class="check-label">
            <input
                type="checkbox"
                id="checkboxDokumen"
                name="dokumen_diperiksa"
                value="1"
            >

            <span>
                Saya telah memeriksa isi dokumen kontrak dan memastikan
                data kerja sama, tenant, lokasi, serta isi perjanjian
                sudah sesuai.
            </span>
        </label>

        <p class="check-help">
            Centang kotak di atas untuk mengaktifkan tombol lanjut ke tahap ACC.
        </p>

        <div class="bottom-actions">
            <a
                href="{{ route('kontrak') }}"
                class="btn btn-back"
            >
                Kembali
            </a>

            <button
                type="button"
                class="btn btn-print"
                onclick="window.print()"
            >
                Print PDF
            </button>

            <button
                type="submit"
                class="btn btn-next"
                id="btnLanjutACC"
                disabled
            >
                Lanjut ke Tahap ACC
            </button>
        </div>
    </form>

@endif

</div>

<script>


    document.addEventListener('DOMContentLoaded', function () {
        const btnEdit = document.getElementById('btnEditDokumen');
        const btnBatalEdit = document.getElementById('btnBatalEdit');
        const panelEdit = document.getElementById('panelEditDokumen');

        const checkboxDokumen = document.getElementById('checkboxDokumen');
        const btnLanjutACC = document.getElementById('btnLanjutACC');

        if (btnEdit && panelEdit) {
            btnEdit.addEventListener('click', function () {
                panelEdit.hidden = !panelEdit.hidden;

                if (!panelEdit.hidden) {
                    panelEdit.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        }

        if (btnBatalEdit && panelEdit) {
            btnBatalEdit.addEventListener('click', function () {
                panelEdit.hidden = true;
            });
        }

        
        if (checkboxDokumen && btnLanjutACC) {
            btnLanjutACC.disabled = !checkboxDokumen.checked;

            checkboxDokumen.addEventListener('change', function () {
                btnLanjutACC.disabled = !checkboxDokumen.checked;
            });
        }

    });
</script>

@endsection
