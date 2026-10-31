
@extends(($isPdf ?? false) ? 'kontrak.layout-pdf' : 'layouts.app')

@section('page-title', 'Isi Dokumen Kontrak')

@section('content')

@php
    /*
    |--------------------------------------------------------------------------
    | 1. MODE HALAMAN
    |--------------------------------------------------------------------------
    */

    $isPdf = $isPdf ?? false;
    $signatureData = $signatureData ?? null;
    $isPreview = $isPreview ?? false;


    /*
    |--------------------------------------------------------------------------
    | 2. DATA RELASI
    |--------------------------------------------------------------------------
    */

    $kerjaSama = $kerjaSama ?? $kontrak?->kerjaSama;

    $tenant = $tenant
        ?? $kontrak?->tenant
        ?? $kerjaSama?->tenant;

    $lokasi = $lokasi
        ?? $kontrak?->lokasi
        ?? $kerjaSama?->lokasi;


    /*
    |--------------------------------------------------------------------------
    | 3. IDENTITAS KONTRAK
    |--------------------------------------------------------------------------
    */

    $nomorKontrak = $kontrak?->nomor_kontrak
        ?? '[Nomor kontrak belum tersedia]';

    $nomorKontrakMitra = $kontrak?->nomor_kontrak_mitra ?? '';

    $nomorKerjaSama = $kerjaSama?->nomor_kerja_sama
        ?: (
            $kerjaSama
                ? 'KS-' . str_pad($kerjaSama->id, 5, '0', STR_PAD_LEFT)
                : '-'
        );


    /*
    |--------------------------------------------------------------------------
    | 4. INFORMASI MITRA
    |--------------------------------------------------------------------------
    */

    $mitra = $kerjaSama?->mitra_usaha
        ?? $tenant?->mitra_usaha
        ?? '-';

    $brand = $kerjaSama?->brand
        ?? $tenant?->nama_tenant
        ?? '-';

    $jenisUsaha = $kerjaSama?->jenis_usaha
        ?? $tenant?->jenis_usaha
        ?? '-';

    $npwp = $tenant?->npwp ?? '-';

    $nib = $tenant?->nib ?? '-';

    $kodeTenant = $tenant?->id ?? '-';


    /*
    |--------------------------------------------------------------------------
    | 5. INFORMASI PIC KOMERSIL
    |--------------------------------------------------------------------------
    */

    $namaPicCommercial = $kerjaSama?->pic_commercial
        ?? $kontrak?->nama_commercial
        ?? '[Nama PIC Komersil]';

    $jabatanPicCommercial = $kerjaSama?->jabatan_pic_commercial
        ?? $kontrak?->jabatan_commercial
        ?? '[Jabatan PIC Komersil]';


    /*
    |--------------------------------------------------------------------------
    | 6. INFORMASI LOKASI
    |--------------------------------------------------------------------------
    */

    $kodeRuang = $lokasi?->kode_ruang ?? '-';

    $namaLokasi = $lokasi?->lokasi ?? '-';

    $terminal = $lokasi?->terminal ?? '-';

    $lantai = $lokasi?->lantai ?? '-';


    /*
    |--------------------------------------------------------------------------
    | 7. LUAS FASILITAS
    |--------------------------------------------------------------------------
    |
    | Nilai awal diambil dari snapshot kontrak.
    | Jika belum tersedia, gunakan data dari Lokasi.
    |
    | Pengguna dapat mengubah nilai melalui form.
    | Nilai hasil perubahan disimpan sebagai snapshot kontrak.
    |
    */

    $luasFasilitas = old(
        'luas_fasilitas_snapshot',
        data_get($kontrak?->data_lokasi_snapshot, 'luas_area')
            ?? $lokasi?->luas_area
            ?? $lokasi?->luas
            ?? $lokasi?->luas_fasilitas
            ?? ''
    );


    /*
    |--------------------------------------------------------------------------
    | 8. TANGGAL DAN DURASI KERJA SAMA
    |--------------------------------------------------------------------------
    */

    $tanggalMulai = $kerjaSama?->tanggal_mulai
        ? \Illuminate\Support\Carbon::parse($kerjaSama->tanggal_mulai)
            ->locale('id')
            ->translatedFormat('d F Y')
        : '[Tanggal mulai kerja sama]';

    $tanggalBerakhir = $kerjaSama?->tanggal_berakhir
        ? \Illuminate\Support\Carbon::parse($kerjaSama->tanggal_berakhir)
            ->locale('id')
            ->translatedFormat('d F Y')
        : '[Tanggal berakhir kerja sama]';

    $tanggalHariIni = now()
        ->locale('id')
        ->translatedFormat('l, d F Y');

    $durasiKerjaSama = $kerjaSama?->tanggal_mulai
        && $kerjaSama?->tanggal_berakhir
        ? (
            \Illuminate\Support\Carbon::parse($kerjaSama->tanggal_mulai)
                ->diffInDays(
                    \Illuminate\Support\Carbon::parse(
                        $kerjaSama->tanggal_berakhir
                    )
                ) . ' hari'
        )
        : '[Durasi sewa belum tersedia]';


    /*
    |--------------------------------------------------------------------------
    | 9. DATA TAMBAHAN KONTRAK
    |--------------------------------------------------------------------------
    */

    $logoMitraPath = $kontrak?->logo_mitra_path ?? null;

    $periodeEvaluasi = $kontrak?->periode_evaluasi_tarif ?? 1;

    $satuanEvaluasi = $kontrak?->satuan_evaluasi_tarif ?? 'tahun';

    $klausulKepemilikan =
        $kontrak?->klausul_kepemilikan_fasilitas ?? '';

    $klausulTambahan = $kontrak?->klausul_tambahan ?? '';


    /*
    |--------------------------------------------------------------------------
    | 10. DATA PENANDATANGAN DAN KONTAK MITRA
    |--------------------------------------------------------------------------
    |
    | Data kontak otomatis mengambil informasi dari Tenant.
    |
    | Jika snapshot kontrak sudah tersedia, gunakan snapshot tersebut.
    | Jika belum tersedia, gunakan data master Tenant.
    |
    | old() digunakan agar input tidak hilang ketika validasi gagal.
    |
    */

    $namaPenandatanganMitra = old(
        'nama_penandatangan_mitra',
        $kontrak?->nama_penandatangan_mitra ?? ''
    );

    $jabatanPenandatanganMitra = old(
        'jabatan_penandatangan_mitra',
        $kontrak?->jabatan_penandatangan_mitra ?? ''
    );

    $alamatMitra = old(
        'alamat_mitra_snapshot',
        $kontrak?->alamat_mitra_snapshot
            ?: ($tenant?->alamat_perusahaan ?? '')
    );

    $domisiliMitra = old(
    'domisili_mitra_snapshot',
    $kontrak?->domisili_mitra_snapshot ?? ''
);

    $teleponMitra = old(
        'telepon_mitra_snapshot',
        $kontrak?->telepon_mitra_snapshot
            ?: ($tenant?->no_hp_pic ?? '')
    );

    $faxMitra = old(
        'fax_mitra_snapshot',
        $kontrak?->fax_mitra_snapshot ?? ''
    );

    $emailMitra = old(
        'email_mitra_snapshot',
        $kontrak?->email_mitra_snapshot
            ?: ($tenant?->email_pic ?? '')
    );


    /*
    |--------------------------------------------------------------------------
    | 11. DATA LAMPIRAN
    |--------------------------------------------------------------------------
    */

    $lampiranTerpilih = $kontrak?->lampiran_perjanjian ?? [];

    if (!is_array($lampiranTerpilih)) {
        $lampiranTerpilih = [];
    }

    /*
     * Mengambil nama dokumen tambahan yang sudah tersimpan.
     *
     * Format penyimpanan:
     * Lainnya: Nama Dokumen
     */

    $namaDokumenLainnya = '';

    foreach ($lampiranTerpilih as $lampiran) {

        if (
            is_string($lampiran)
            && str_starts_with($lampiran, 'Lainnya: ')
        ) {
            $namaDokumenLainnya = substr(
                $lampiran,
                strlen('Lainnya: ')
            );

            break;
        }

    }

    $namaDokumenLainnya = old(
        'nama_dokumen_lainnya',
        $namaDokumenLainnya
    );

    /*
     * Mengubah nilai lampiran khusus menjadi Lainnya
     * agar checkbox dapat dikenali sebagai pilihan aktif.
     */

    $lampiranTerpilih = array_map(
        function ($lampiran) {

            if (
                is_string($lampiran)
                && str_starts_with($lampiran, 'Lainnya: ')
            ) {
                return 'Lainnya';
            }

            return $lampiran;

        },
        $lampiranTerpilih
    );

    /*
     * SUP dan BAK otomatis terpilih.
     */

    $lampiranInput = old(
        'lampiran_perjanjian',
        array_values(
            array_unique(
                array_merge(
                    ['SUP', 'BAK'],
                    $lampiranTerpilih
                )
            )
        )
    );

    $daftarLampiran = [
        'SUP' => 'Syarat-Syarat Umum Perjanjian (SUP)',
        'BAK' => 'Berita Acara Kesepakatan (BAK)',
        'Denah Fasilitas Komersial' => 'Denah Fasilitas Komersial',
        'Perjanjian Tingkat Layanan' => 'Perjanjian Tingkat Layanan',
        'Lainnya' => 'Dokumen Lainnya',
    ];


    /*
    |--------------------------------------------------------------------------
    | 12. KLAUSUL KEPEMILIKAN
    |--------------------------------------------------------------------------
    */

    if ($klausulKepemilikan === 'mitra') {

        $klausulKepemilikanText =
            'Fasilitas usaha yang dibangun tetap menjadi milik Mitra, '
            . 'dan fasilitas komersial dikembalikan sesuai ketentuan.';

    } elseif ($klausulKepemilikan === 'ap_indonesia') {

        $klausulKepemilikanText =
            'Fasilitas usaha yang dibangun menjadi milik AP Indonesia '
            . 'tanpa kompensasi.';

    } else {

        $klausulKepemilikanText =
            '[Klausul kepemilikan belum dipilih]';

    }


    /*
    |--------------------------------------------------------------------------
    | 13. DATA AP INDONESIA
    |--------------------------------------------------------------------------
    |
    | Identitas resmi AP Indonesia tetap mengikuti data yang
    | ditentukan melalui source code sistem.
    |
    | Tidak menggunakan data perkiraan untuk dokumen resmi.
    |
    */


@endphp

{{-- =========================================================
     CSS DASAR HALAMAN
========================================================= --}}

<style>
    /* =====================================================
       DASAR

{{-- =========================================================
     CSS DASAR HALAMAN
========================================================= --}}

<style>
    /* =====================================================
       DASAR
    ===================================================== */

    .dokumen-page {
        width: 100%;
        max-width: 1500px;
        margin: 0 auto;
        padding: 12px 20px 24px;
        color: #243B5A;
        font-family: Arial, sans-serif;
        box-sizing: border-box;
    }

    .dokumen-page *,
    .dokumen-page *::before,
    .dokumen-page *::after {
        box-sizing: border-box;
    }

    /* =====================================================
       JUDUL HALAMAN
    ===================================================== */

    .dokumen-page .page-heading {
        margin-bottom: 12px;
        text-align: center;
    }

    .dokumen-page .page-title {
        margin: 0;
        color: #142C5C;
        font-size: 24px;
        font-weight: 700;
    }

    .dokumen-page .page-subtitle {
        margin: 5px 0 0;
        color: #7186AE;
        font-size: 12px;
        line-height: 1.5;
    }

    /* =====================================================
       STEPPER
    ===================================================== */

    .dokumen-page .contract-steps {
        display: flex;
        align-items: flex-start;
        justify-content: center;
        width: 100%;
        max-width: 430px;
        margin: 15px auto 22px;
    }

    .dokumen-page .contract-step {
        display: flex;
        flex-direction: column;
        align-items: center;
        width: 125px;
        color: #91A0B5;
        text-align: center;
    }

    .dokumen-page .step-number {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 31px;
        height: 31px;
        border-radius: 50%;
        background: #D4DCE7;
        color: #FFFFFF;
        font-size: 13px;
        font-weight: 700;
    }

    .dokumen-page .contract-step.active {
        color: #142C5C;
        font-weight: 600;
    }

    .dokumen-page .contract-step.active .step-number {
        background: #2864E8;
    }

    .dokumen-page .step-label {
        margin-top: 6px;
        font-size: 10px;
        line-height: 1.4;
    }

    .dokumen-page .step-line {
        flex: 1;
        height: 2px;
        margin-top: 15px;
        background: #D4DCE7;
    }

    /* =====================================================
       WORKSPACE
    ===================================================== */

    .dokumen-page .document-workspace {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(0, 1.12fr);
        align-items: start;
        gap: 14px;
        width: 100%;
    }

    .dokumen-page .document-form-column,
    .dokumen-page .document-preview-column {
        min-width: 0;
    }

    /* =====================================================
       RESPONSIVE
    ===================================================== */

    @media (max-width: 1000px) {
        .dokumen-page .document-workspace {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 600px) {
        .dokumen-page {
            padding: 10px;
        }

        .dokumen-page .page-title {
            font-size: 20px;
        }

        .dokumen-page .page-subtitle {
            font-size: 11px;
        }
    }

    /* =====================================================
   MODE PDF
===================================================== */

.dokumen-page.pdf-mode {
    width: 100%;
    max-width: none;
    margin: 0;
    padding: 0;
    background: #FFFFFF;
}

/* Sembunyikan seluruh bagian form dan kontrol preview */
.dokumen-page.pdf-mode .page-heading,
.dokumen-page.pdf-mode .contract-steps,
.dokumen-page.pdf-mode .document-form-column,
.dokumen-page.pdf-mode .preview-header,
.dokumen-page.pdf-mode .preview-toolbar,
.dokumen-page.pdf-mode .preview-thumbnails,
.dokumen-page.pdf-mode .preview-confirmation,
.dokumen-page.pdf-mode .preview-warning,
.dokumen-page.pdf-mode .preview-action-area {
    display: none !important;
}

/* Atur kolom preview */
.dokumen-page.pdf-mode .document-workspace {
    display: block;
}

.dokumen-page.pdf-mode .document-preview-column {
    position: static;
    width: 100%;
}

/* Hilangkan tampilan kartu preview */
.dokumen-page.pdf-mode .preview-card {
    padding: 0;
    border: none;
    border-radius: 0;
    box-shadow: none;
    overflow: visible;
}

/* Area kertas */
.dokumen-page.pdf-mode .preview-paper-area {
    width: 100%;
    max-height: none;
    padding: 0;
    overflow: visible;
    background: #FFFFFF;
}

/* Kertas dokumen */
.dokumen-page.pdf-mode .preview-paper {
    width: 100%;
    max-width: none;
    min-height: 0;
    margin: 0;
    padding: 0;
    aspect-ratio: auto;
    box-shadow: none;
    transform: none;
}

/* Tampilkan seluruh halaman */
.dokumen-page.pdf-mode .preview-document-page {
    display: block !important;
    page-break-after: always;
    break-after: page;
}

/* Halaman terakhir tidak memerlukan page break */
.dokumen-page.pdf-mode .preview-document-page:last-child {
    page-break-after: auto;
    break-after: auto;
}
</style>

<style>
    /* =========================================
       FORM DOKUMEN KONTRAK
    ========================================= */

    .document-form-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        padding: 24px;
        margin-bottom: 20px;
    }

    .form-card-heading {
        display: flex;
        align-items: center;
        gap: 12px;
        padding-bottom: 16px;
        margin-bottom: 20px;
        border-bottom: 1px solid #edf0f2;
    }

    .form-card-icon {
        width: 38px;
        height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        background: #EAF0FF;
        color: #2864E8;
        font-size: 18px;
        flex-shrink: 0;
    }

    .form-card-heading h3 {
        margin: 0;
        color: #142C5C;
        font-size: 16px;
        font-weight: 700;
    }

    .form-card-heading p {
        margin: 4px 0 0;
        color: #89939b;
        font-size: 12px;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px 16px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 7px;
        min-width: 0;
    }

    .form-group.full-width {
        grid-column: 1 / -1;
    }

    .form-label {
        color: #243B5A;
        font-size: 13px;
        font-weight: 600;
    }

    .form-label .required {
        color: #dc3545;
    }

    .form-control-custom {
        width: 100%;
        min-height: 42px;
        padding: 10px 12px;
        border: 1px solid #dce1e6;
        border-radius: 8px;
        background: #ffffff;
        color: #344054;
        font-size: 13px;
        outline: none;
        transition: border-color .2s, box-shadow .2s;
        box-sizing: border-box;
    }

    .form-control-custom:focus {
        border-color: #2864E8;
        box-shadow: 0 0 0 3px rgba(40, 100, 232, .10);
    }

    .form-control-custom:disabled,
    .form-control-custom[readonly] {
        background: #f7f8fa;
        color: #68737d;
        cursor: not-allowed;
    }

    textarea.form-control-custom {
        min-height: 85px;
        resize: vertical;
    }

    .form-helper {
        color: #929ba4;
        font-size: 11px;
        line-height: 1.5;
    }

    .input-unit-wrapper {
        display: flex;
        gap: 8px;
    }

    .input-unit-wrapper .form-control-custom {
        flex: 1;
    }

    .input-unit-wrapper select {
        width: 115px;
        flex-shrink: 0;
    }

    /* =========================================
       LOGO MITRA
    ========================================= */

    .logo-upload-wrapper {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 15px;
        border: 1px dashed #cbd2d9;
        border-radius: 10px;
        background: #fafbfc;
    }

    .logo-preview-box {
        width: 76px;
        height: 76px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        border: 1px solid #e5e7eb;
        border-radius: 9px;
        background: #ffffff;
        flex-shrink: 0;
    }

    .logo-preview-box img {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }

    .logo-placeholder {
        color: #aab2ba;
        font-size: 26px;
    }

    .logo-upload-content {
        flex: 1;
        min-width: 0;
    }

    .logo-upload-content strong {
        display: block;
        margin-bottom: 5px;
        color: #243B5A;
        font-size: 13px;
    }

    .logo-upload-content p {
        margin: 0 0 9px;
        color: #929ba4;
        font-size: 11px;
    }

    .upload-file-input {
        max-width: 100%;
        color: #667085;
        font-size: 11px;
    }

    /* =========================================
       KEPEMILIKAN
    ========================================= */

    .ownership-options {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
    }

    .ownership-option {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        padding: 14px;
        border: 1px solid #dce1e6;
        border-radius: 9px;
        cursor: pointer;
        transition: .2s;
    }

    .ownership-option:has(input:checked) {
        border-color: #2864E8;
        background: #F1F5FF;
    }

    .ownership-option input {
        margin-top: 3px;
        accent-color: #2864E8;
    }

    .ownership-option strong {
        display: block;
        color: #243B5A;
        font-size: 12px;
        margin-bottom: 4px;
    }

    .ownership-option span {
        display: block;
        color: #89939b;
        font-size: 11px;
        line-height: 1.5;
    }

    /* =========================================
       LAMPIRAN
    ========================================= */

    .attachment-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .attachment-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 14px;
        border: 1px solid #e5e7eb;
        border-radius: 9px;
        background: #ffffff;
    }

    .attachment-item input {
        width: 16px;
        height: 16px;
        accent-color: #2864E8;
        flex-shrink: 0;
    }

    .attachment-item-content {
        flex: 1;
        min-width: 0;
    }

    .attachment-item-content strong {
        display: block;
        color: #243B5A;
        font-size: 12px;
        font-weight: 600;
    }

    .attachment-item-content small {
        display: block;
        margin-top: 3px;
        color: #929ba4;
        font-size: 10px;
    }

    .attachment-status {
        padding: 4px 8px;
        border-radius: 20px;
        background: #fff0ed;
        color: #c65b4d;
        font-size: 10px;
        font-weight: 600;
        white-space: nowrap;
    }

    .attachment-status.optional {
        background: #EAF0FF;
        color: #2864E8;
    }

    /* =========================================
       INFORMASI PERUSAHAAN
    ========================================= */

    .company-info-box {
        padding: 15px;
        border: 1px solid #e5e7eb;
        border-radius: 9px;
        background: #f8f9fb;
    }

    .company-info-title {
        margin: 0 0 12px;
        color: #243B5A;
        font-size: 13px;
        font-weight: 700;
    }

    .company-info-note {
        margin: 0 0 14px;
        color: #89939b;
        font-size: 11px;
        line-height: 1.6;
    }

    .form-section-divider {
        height: 1px;
        margin: 22px 0;
        background: #edf0f2;
    }

    /* =========================================
       TOMBOL FORM
    ========================================= */

    .form-action-area {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-top: 20px;
        padding: 18px 0 5px;
        border-top: 1px solid #edf0f2;
    }

    .btn-form-back,
    .btn-form-next,
    .btn-form-export {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-height: 42px;
        padding: 10px 17px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
        transition: .2s;
    }

    .btn-form-back {
        border: 1px solid #dce1e6;
        background: #ffffff;
        color: #59636e;
    }

    .btn-form-export {
        border: 1px solid #2864E8;
        background: #ffffff;
        color: #2864E8;
    }

    .btn-form-next {
        border: 1px solid #2864E8;
        background: #2864E8;
        color: #ffffff;
    }

    .btn-form-back:hover {
        background: #f7f8fa;
    }

    .btn-form-export:hover {
        background: #F1F5FF;
    }

    .btn-form-next:hover {
        background: #1D4FC2;
    }

    .form-action-right {
        display: flex;
        align-items: center;
        gap: 9px;
    }

    .form-action-right button:disabled {
        opacity: .55;
        cursor: not-allowed;
    }

    .form-error-message {
        color: #dc3545;
        font-size: 11px;
    }

    /* =========================================
       RESPONSIVE
    ========================================= */

    @media (max-width: 650px) {
        .document-form-card {
            padding: 17px;
        }

        .form-grid {
            grid-template-columns: 1fr;
        }

        .form-group.full-width {
            grid-column: auto;
        }

        .ownership-options {
            grid-template-columns: 1fr;
        }

        .form-action-area {
            align-items: stretch;
            flex-direction: column;
        }

        .form-action-right {
            display: grid;
            grid-template-columns: 1fr 1fr;
        }

        .form-action-right button {
            width: 100%;
        }
    }
</style>

<div class="dokumen-page {{ $isPdf ? 'pdf-mode' : '' }}">

    {{-- JUDUL HALAMAN --}}
    <div class="page-heading">

        <h1 class="page-title">
            Isi Dokumen Kontrak
        </h1>

        <p class="page-subtitle">
            Lengkapi data tambahan dan periksa pratinjau dokumen
            sebelum mengunduh kontrak sesuai template resmi.
        </p>

    </div>


    {{-- STEPPER --}}
    <div class="contract-steps">

        <div class="contract-step active">

            <div class="step-number">1</div>

            <div class="step-label">
                Isi Dokumen<br>Kontrak
            </div>

        </div>

        <div class="step-line"></div>

        <div class="contract-step">

            <div class="step-number">2</div>

            <div class="step-label">
                Upload Dokumen<br>Kontrak
            </div>

        </div>

    </div>


    {{-- AREA FORM DAN PRATINJAU --}}
    <div class="document-workspace">

        {{-- KOLOM KIRI: FORM DATA --}}
        <div class="document-form-column">

            <form
                id="documentForm"
                action="{{ $kontrak ? route('kontrak.dokumen.update', $kontrak->id) : '#' }}"
                method="POST"
                enctype="multipart/form-data"
            >

    @csrf

    @if($kontrak)
        @method('PUT')
    @endif

    {{-- =========================================
         1. INFORMASI KERJA SAMA
    ========================================== --}}

    <div class="document-form-card">

        <div class="form-card-heading">
            <div class="form-card-icon">
                <i class="fas fa-handshake"></i>
            </div>

            <div>
                <h3>Informasi Kerja Sama</h3>
                <p>Informasi yang diambil dari data kerja sama sebelumnya.</p>
            </div>
        </div>

        <div class="form-grid">

            <div class="form-group full-width">
                <label class="form-label">Nomor Kerja Sama</label>
                <input
                    type="text"
                    class="form-control-custom"
                    value="{{ $nomorKerjaSama }}"
                    readonly
                >
            </div>

            <div class="form-group">
                <label class="form-label">Nama Mitra</label>
                <input
                    type="text"
                    class="form-control-custom"
                    value="{{ $mitra }}"
                    readonly
                >
            </div>

            <div class="form-group">
                <label class="form-label">Brand / Nama Tenant</label>
                <input
                    type="text"
                    class="form-control-custom"
                    value="{{ $brand }}"
                    readonly
                >
            </div>

            <div class="form-group">
                <label class="form-label">Kegiatan Usaha</label>
                <input
                    type="text"
                    class="form-control-custom"
                    value="{{ $jenisUsaha }}"
                    readonly
                >
            </div>

            <div class="form-group">
                <label class="form-label">Bentuk Kerja Sama</label>
                <input
                    type="text"
                    class="form-control-custom"
                    value="{{ $kerjaSama?->bentuk_kerja_sama ?? '-' }}"
                    readonly
                >
            </div>

            <div class="form-group">
                <label class="form-label">Lokasi</label>
                <input
                    type="text"
                    class="form-control-custom"
                    value="{{ collect([$namaLokasi, $terminal, $lantai, $kodeRuang])->filter(fn($value) => $value && $value !== '-')->implode(' - ') ?: '-' }}"
                    readonly
                >
            </div>

            <div class="form-group">
                <label class="form-label" for="luas_fasilitas_snapshot">
                    Luas Fasilitas
                    <span class="required">*</span>
                </label>

                <input
                    type="text"
                    name="luas_fasilitas_snapshot"
                    id="luas_fasilitas_snapshot"
                    class="form-control-custom"
                    value="{{ old('luas_fasilitas_snapshot', $luasFasilitas) }}"
                    placeholder="Masukkan luas fasilitas"
                    required
                >

                @error('luas_fasilitas_snapshot')
                    <span class="form-error-message">{{ $message }}</span>
                @enderror
            </div>

        </div>
    </div>


    {{-- =========================================
         2. DATA TAMBAHAN KONTRAK
    ========================================== --}}

    <div class="document-form-card">

        <div class="form-card-heading">
            <div class="form-card-icon">
                <i class="fas fa-file-signature"></i>
            </div>

            <div>
                <h3>Data Tambahan Kontrak</h3>
                <p>Lengkapi informasi tambahan yang akan dimasukkan ke dokumen kontrak.</p>
            </div>
        </div>

        <div class="form-grid">

            {{-- LOGO MITRA --}}
            <div class="form-group full-width">
                <label class="form-label">Logo Mitra</label>

                <div class="logo-upload-wrapper">

                    <div class="logo-preview-box" id="logoPreviewBox">
                        @if($logoMitraPath)
                            <img
                                id="logoPreviewImage"
                                src="{{ asset('storage/' . $logoMitraPath) }}"
                                alt="Logo Mitra"
                            >
                        @else
                            <i class="fas fa-image logo-placeholder" id="logoPlaceholder"></i>

                            <img
                                id="logoPreviewImage"
                                src=""
                                alt="Preview Logo Mitra"
                                style="display: none;"
                            >
                        @endif
                    </div>

                    <div class="logo-upload-content">
                        <strong>Unggah Logo Mitra</strong>
                        <p>Format PNG atau JPG dengan ukuran maksimal 5 MB.</p>

                        <input
                            type="file"
                            name="logo_mitra"
                            id="logoMitraInput"
                            class="upload-file-input"
                            accept=".png,.jpg,.jpeg,image/png,image/jpeg"
                        >

                        @error('logo_mitra')
                            <span class="form-error-message">{{ $message }}</span>
                        @enderror
                    </div>

                </div>
            </div>

                {{-- NOMOR KONTRAK MITRA --}}
                <div class="form-group full-width">
                    <label class="form-label" for="nomor_kontrak_mitra">
                        Nomor Kontrak Mitra
                    </label>

                    <input
                        type="text"
                        name="nomor_kontrak_mitra"
                        id="nomor_kontrak_mitra"
                        class="form-control-custom"
                        value="{{ old('nomor_kontrak_mitra', $nomorKontrakMitra) }}"
                        placeholder="Masukkan nomor kontrak dari pihak mitra"
                    >
                </div>


                {{-- PERIODE EVALUASI TARIF --}}
                <div class="form-group full-width">
                    <label class="form-label">
                        Periode Evaluasi Tarif
                        <span class="required">*</span>
                    </label>

                    <div class="input-unit-wrapper">
                        <input
                            type="number"
                            name="periode_evaluasi_tarif"
                            id="periode_evaluasi_tarif"
                            class="form-control-custom"
                            min="1"
                            value="{{ old('periode_evaluasi_tarif', $periodeEvaluasi) }}"
                            required
                        >

                        <select
                            name="satuan_evaluasi_tarif"
                            id="satuan_evaluasi_tarif"
                            class="form-control-custom"
                            required
                        >
                            <option value="bulan"
                                {{ old('satuan_evaluasi_tarif', $satuanEvaluasi) === 'bulan' ? 'selected' : '' }}>
                                Bulan
                            </option>

                            <option value="tahun"
                                {{ old('satuan_evaluasi_tarif', $satuanEvaluasi) === 'tahun' ? 'selected' : '' }}>
                                Tahun
                            </option>
                        </select>
                    </div>

                    <span class="form-helper">
                        Tentukan periode evaluasi tarif sesuai kesepakatan kerja sama.
                    </span>
                </div>


                {{-- KLAUSUL KEPEMILIKAN FASILITAS --}}
                <div class="form-group full-width">
                    <label class="form-label">
                        Klausul Kepemilikan Fasilitas
                        <span class="required">*</span>
                    </label>

                    <div class="ownership-options">

                        <label class="ownership-option">
                            <input
                                type="radio"
                                name="klausul_kepemilikan_fasilitas"
                                value="mitra"
                                {{ old('klausul_kepemilikan_fasilitas', $klausulKepemilikan) === 'mitra' ? 'checked' : '' }}
                                required
                            >

                            <div>
                                <strong>Milik Mitra</strong>
                                <span>
                                    Fasilitas yang digunakan merupakan milik pihak mitra.
                                </span>
                            </div>
                        </label>

                        <label class="ownership-option">
                            <input
                                type="radio"
                                name="klausul_kepemilikan_fasilitas"
                                value="ap_indonesia"
                                {{ old('klausul_kepemilikan_fasilitas', $klausulKepemilikan) === 'ap_indonesia' ? 'checked' : '' }}
                                required
                            >

                            <div>
                                <strong>Milik AP Indonesia</strong>
                                <span>
                                    Fasilitas yang digunakan merupakan milik AP Indonesia.
                                </span>
                            </div>
                        </label>

                    </div>
                </div>

            </div>


            <div class="form-section-divider"></div>


            {{-- LAMPIRAN PERJANJIAN --}}
            <div class="form-group full-width">

                <label class="form-label">
                    Lampiran Perjanjian
                    <span class="required">*</span>
                </label>

                <span class="form-helper">
                    Pilih dokumen yang menjadi bagian dari perjanjian kontrak.
                    SUP dan BAK wajib dicantumkan.
                </span>

                <div class="attachment-list">

                    {{-- SUP WAJIB --}}
                    <label class="attachment-item">
                        <input
                            type="hidden"
                            name="lampiran_perjanjian[]"
                            value="SUP"
                        >

                        <input
                            type="checkbox"
                            checked
                            disabled
                        >

                        <div class="attachment-item-content">
                            <strong>Syarat-Syarat Umum Perjanjian (SUP)</strong>
                            <small>Dokumen syarat umum perjanjian.</small>
                        </div>

                        <span class="attachment-status">Wajib</span>
                    </label>


                    {{-- BAK WAJIB --}}
                    <label class="attachment-item">
                        <input
                            type="hidden"
                            name="lampiran_perjanjian[]"
                            value="BAK"
                        >

                        <input
                            type="checkbox"
                            checked
                            disabled
                        >

                        <div class="attachment-item-content">
                            <strong>Berita Acara Kesepakatan (BAK)</strong>
                            <small>Dokumen berita acara kesepakatan kerja sama.</small>
                        </div>

                        <span class="attachment-status">Wajib</span>
                    </label>


                    {{-- DENAH FASILITAS --}}
                    <label class="attachment-item">
                        <input
                            type="checkbox"
                            name="lampiran_perjanjian[]"
                            value="Denah Fasilitas Komersial"
                            {{ in_array('Denah Fasilitas Komersial', $lampiranInput) ? 'checked' : '' }}
                        >

                        <div class="attachment-item-content">
                            <strong>Denah Fasilitas Komersial</strong>
                            <small>Denah lokasi fasilitas yang digunakan mitra.</small>
                        </div>

                        <span class="attachment-status optional">Opsional</span>
                    </label>


                    {{-- PERJANJIAN TINGKAT LAYANAN --}}
                    <label class="attachment-item">
                        <input
                            type="checkbox"
                            name="lampiran_perjanjian[]"
                            value="Perjanjian Tingkat Layanan"
                            {{ in_array('Perjanjian Tingkat Layanan', $lampiranInput) ? 'checked' : '' }}
                        >

                        <div class="attachment-item-content">
                            <strong>Perjanjian Tingkat Layanan</strong>
                            <small>Dokumen kesepakatan tingkat layanan.</small>
                        </div>

                        <span class="attachment-status optional">Opsional</span>
                    </label>


                    {{-- DOKUMEN LAINNYA --}}
                    <label class="attachment-item">
                        <input
                            type="checkbox"
                            name="lampiran_perjanjian[]"
                            id="lampiranLainnya"
                            value="Lainnya"
                            {{ in_array('Lainnya', $lampiranInput) ? 'checked' : '' }}
                        >

                        <div class="attachment-item-content">
                            <strong>Dokumen Lainnya</strong>
                            <small>Dokumen pendukung lain sesuai kesepakatan.</small>
                        </div>

                        <span class="attachment-status optional">Opsional</span>
                    </label>


                    {{-- INPUT NAMA DOKUMEN LAINNYA --}}
                    <div
                        class="form-group"
                        id="namaDokumenLainnyaWrapper"
                        style="{{ in_array('Lainnya', $lampiranInput) ? '' : 'display: none;' }}"
                    >
                        <label class="form-label" for="nama_dokumen_lainnya">
                            Nama Dokumen Lainnya
                            <span class="required">*</span>
                        </label>

                        <input
                            type="text"
                            name="nama_dokumen_lainnya"
                            id="nama_dokumen_lainnya"
                            class="form-control-custom"
                            value="{{ old('nama_dokumen_lainnya', $namaDokumenLainnya) }}"
                            placeholder="Contoh: Berita Acara Serah Terima"
                        >

                        @error('nama_dokumen_lainnya')
                            <span class="form-error-message">{{ $message }}</span>
                        @enderror
                    </div>

                </div>
            </div>

        </div>

    {{-- =========================================
         3. KONTAK DAN PENANDATANGANAN
    ========================================== --}}

    <div class="document-form-card">

        <div class="form-card-heading">
            <div class="form-card-icon">
                <i class="fas fa-address-card"></i>
            </div>

            <div>
                <h3>Kontak dan Penandatanganan</h3>
                <p>Informasi pihak AP Indonesia dan pihak mitra dalam dokumen kontrak.</p>
            </div>
        </div>


        {{-- PIHAK AP INDONESIA --}}
        <div class="company-info-box">

            <h4 class="company-info-title">
                PIHAK PERTAMA — AP INDONESIA
            </h4>

            <p class="company-info-note">
                Informasi pihak AP Indonesia akan mengikuti data master perusahaan.
                Data yang belum dikonfigurasi tidak akan diisi dengan informasi rekaan.
            </p>

            <div class="form-grid">

                <div class="form-group full-width">
                    <label class="form-label">Nama Perusahaan</label>
                    <input
                        type="text"
                        class="form-control-custom"
                        value="{{ $apIndonesia['nama'] }}"
                        readonly
                    >
                </div>

                <div class="form-group">
                    <label class="form-label">Domisili</label>
                    <input
                        type="text"
                        class="form-control-custom"
                        value="{{ $apIndonesia['domisili'] ?: 'Belum dikonfigurasi' }}"
                        readonly
                    >
                </div>

                <div class="form-group">
                    <label class="form-label">Alamat</label>
                    <input
                        type="text"
                        class="form-control-custom"
                        value="{{ $apIndonesia['alamat'] ?: 'Belum dikonfigurasi' }}"
                        readonly
                    >
                </div>

                <div class="form-group">
                    <label class="form-label">Nama Penandatangan</label>
                    <input
                        type="text"
                        class="form-control-custom"
                        value="{{ $apIndonesia['penandatangan'] ?: 'Belum dikonfigurasi' }}"
                        readonly
                    >
                </div>

                <div class="form-group">
                    <label class="form-label">Jabatan</label>
                    <input
                        type="text"
                        class="form-control-custom"
                        value="{{ $apIndonesia['jabatan'] ?: 'Belum dikonfigurasi' }}"
                        readonly
                    >
                </div>

            </div>
        </div>


        <div class="form-section-divider"></div>


        {{-- PIHAK MITRA --}}
        <div class="form-group full-width">

            <label class="form-label">
                PIHAK KEDUA — MITRA
            </label>

            <span class="form-helper">
                Lengkapi identitas penandatangan dan kontak resmi dari pihak mitra.
            </span>

        </div>


        <div class="form-grid" style="margin-top: 16px;">

        
    {{-- NAMA PIC MITRA --}}
    <div class="form-group">
        <label class="form-label" for="nama_pic_mitra_snapshot">
            Nama PIC Mitra
            <span class="required">*</span>
        </label>

        <input
            type="text"
            name="nama_pic_mitra_snapshot"
            id="nama_pic_mitra_snapshot"
            class="form-control-custom"
            value="{{ old('nama_pic_mitra_snapshot', $namaPicCommercial ?? '') }}"
            placeholder="Masukkan nama PIC mitra"
            required
        >

        @error('nama_pic_mitra_snapshot')
            <span class="form-error-message">{{ $message }}</span>
        @enderror
    </div>

    {{-- JABATAN PIC MITRA --}}
    <div class="form-group">
        <label class="form-label" for="jabatan_pic_mitra_snapshot">
            Jabatan PIC Mitra
            <span class="required">*</span>
        </label>

        <input
            type="text"
            name="jabatan_pic_mitra_snapshot"
            id="jabatan_pic_mitra_snapshot"
            class="form-control-custom"
            value="{{ old('jabatan_pic_mitra_snapshot', $jabatanPicCommercial ?? '') }}"
            placeholder="Masukkan jabatan PIC mitra"
            required
        >

        @error('jabatan_pic_mitra_snapshot')
            <span class="form-error-message">{{ $message }}</span>
        @enderror
    </div>

            {{-- NAMA PENANDATANGAN --}}
            <div class="form-group">
                <label class="form-label" for="nama_penandatangan_mitra">
                    Nama Penandatangan
                    <span class="required">*</span>
                </label>

                <input
                    type="text"
                    name="nama_penandatangan_mitra"
                    id="nama_penandatangan_mitra"
                    class="form-control-custom"
                    value="{{ old('nama_penandatangan_mitra', $namaPenandatanganMitra ?: $namaPicCommercial) }}"
                    placeholder="Masukkan nama penandatangan"
                    required
                >
            </div>


            {{-- JABATAN PENANDATANGAN --}}
            <div class="form-group">
                <label class="form-label" for="jabatan_penandatangan_mitra">
                    Jabatan
                    <span class="required">*</span>
                </label>

                <input
                    type="text"
                    name="jabatan_penandatangan_mitra"
                    id="jabatan_penandatangan_mitra"
                    class="form-control-custom"
                    value="{{ old('jabatan_penandatangan_mitra', $jabatanPenandatanganMitra ?: $jabatanPicCommercial) }}"
                    placeholder="Masukkan jabatan penandatangan"
                    required
                >
            </div>

            {{-- DOMISILI MITRA --}}
<div class="form-group">
    <label class="form-label" for="domisili_mitra_snapshot">
        Domisili / Kota Mitra
        <span class="required">*</span>
    </label>

    <input
        type="text"
        name="domisili_mitra_snapshot"
        id="domisili_mitra_snapshot"
        class="form-control-custom"
        value="{{ old('domisili_mitra_snapshot', $domisiliMitra) }}"
        placeholder="Contoh: Surabaya"
        required
    >

    @error('domisili_mitra_snapshot')
        <span class="form-error-message">{{ $message }}</span>
    @enderror
</div>

            {{-- ALAMAT MITRA --}}
            <div class="form-group full-width">
                <label class="form-label" for="alamat_mitra_snapshot">
                    Alamat Mitra
                    <span class="required">*</span>
                </label>

                <textarea
                    name="alamat_mitra_snapshot"
                    id="alamat_mitra_snapshot"
                    class="form-control-custom"
                    placeholder="Masukkan alamat lengkap mitra"
                    required
                >{{ old('alamat_mitra_snapshot', $alamatMitra) }}</textarea>

                @error('alamat_mitra_snapshot')
                    <span class="form-error-message">{{ $message }}</span>
                @enderror
            </div>


            {{-- NOMOR TELEPON --}}
            <div class="form-group">
                <label class="form-label" for="telepon_mitra_snapshot">
                    Nomor Telepon
                    <span class="required">*</span>
                </label>

                <input
                    type="text"
                    name="telepon_mitra_snapshot"
                    id="telepon_mitra_snapshot"
                    class="form-control-custom"
                    value="{{ old('telepon_mitra_snapshot', $teleponMitra) }}"
                    placeholder="Masukkan nomor telepon"
                    required
                >

                @error('telepon_mitra_snapshot')
                    <span class="form-error-message">{{ $message }}</span>
                @enderror
            </div>


            {{-- NOMOR FAKSIMILI --}}
            <div class="form-group">
                <label class="form-label" for="fax_mitra_snapshot">
                    Nomor Faksimili
                </label>

                <input
                    type="text"
                    name="fax_mitra_snapshot"
                    id="fax_mitra_snapshot"
                    class="form-control-custom"
                    value="{{ old('fax_mitra_snapshot', $faxMitra) }}"
                    placeholder="Masukkan nomor faksimili jika ada"
                >

                @error('fax_mitra_snapshot')
                    <span class="form-error-message">{{ $message }}</span>
                @enderror
            </div>


            {{-- EMAIL MITRA --}}
            <div class="form-group full-width">
                <label class="form-label" for="email_mitra_snapshot">
                    Email
                    <span class="required">*</span>
                </label>

                <input
                    type="email"
                    name="email_mitra_snapshot"
                    id="email_mitra_snapshot"
                    class="form-control-custom"
                    value="{{ old('email_mitra_snapshot', $emailMitra) }}"
                    placeholder="contoh@email.com"
                    required
                >

                @error('email_mitra_snapshot')
                    <span class="form-error-message">{{ $message }}</span>
                @enderror
            </div>

                </div>

    </div>

</form>

</div>

<style>
    /* =========================================
       PREVIEW DOKUMEN KONTRAK
    ========================================= */

    .document-preview-column {
        min-width: 0;
        position: sticky;
        top: 20px;
        align-self: start;
    }

    .preview-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        padding: 20px;
        overflow: hidden;
    }

    .preview-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 17px;
    }

    .preview-header h3 {
        margin: 0;
        color: #142C5C;
        font-size: 16px;
        font-weight: 700;
    }

    .preview-header p {
        margin: 5px 0 0;
        color: #89939b;
        font-size: 11px;
    }

    .preview-live-label {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 9px;
        border-radius: 20px;
        background: #eaf7ef;
        color: #27834c;
        font-size: 10px;
        font-weight: 600;
        white-space: nowrap;
    }

    .preview-live-label span {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #2da45f;
    }

    .preview-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        padding: 10px;
        margin-bottom: 15px;
        border: 1px solid #edf0f2;
        border-radius: 8px;
        background: #f8f9fb;
    }

    .preview-page-navigation,
    .preview-zoom-navigation {
        display: flex;
        align-items: center;
        gap: 7px;
    }

    .preview-tool-button {
    width: 38px;
    height: 38px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: 1px solid #d5dbe2;
    border-radius: 8px;
    background: #ffffff;
    color: #000000;
    cursor: pointer;
    font-size: 15px;
        font-weight: 700;
    }

    .preview-tool-button i {
        color: #000000;
        font-size: 15px;
        font-weight: 900;
    }

    .preview-tool-button:hover {
        border-color: #000000;
        background: #f1f3f5;
        color: #000000;
    }

    .preview-tool-button:hover i {
        color: #000000;
    }

    .preview-tool-button:disabled {
        opacity: 0.45;
        cursor: not-allowed;
    }

    .preview-tool-button:disabled i {
        color: #000000;
    }

    .preview-page-counter,
    .preview-zoom-value {
        color: #59636e;
        font-size: 11px;
        font-weight: 600;
        white-space: nowrap;
    }

    .preview-paper-area {
        width: 100%;
        max-height: 720px;
        overflow: auto;
        padding: 12px 4px;
        border-radius: 8px;
        background: #eef0f2;
    }

    .preview-paper {
    width: 100%;
    max-width: 460px;
    aspect-ratio: 210 / 297;
    min-height: 0;
    margin: 0 auto;
    padding: 42px 38px;
    background: #ffffff;
    color: #252525;
    box-shadow: 0 2px 9px rgba(0,0,0,.09);
    transform-origin: top center;
    font-family: "Times New Roman", serif;
    font-size: 10px;
    line-height: 1.65;
    box-sizing: border-box;
    }

    .preview-paper-header {
        text-align: center;
        margin-bottom: 22px;
    }

    /* =========================================
   LOGO INJOURNEY DAN MITRA
========================================= */

.preview-paper-logos {
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: 100%;
    margin: 0 auto 8px;
}

.preview-paper-logo,
.preview-paper-logo-injourney {
    width: 100px;
    height: 55px;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    border: none;
    border-radius: 0;
    background: transparent;
    box-sizing: border-box;
    }

    .preview-paper-logo img,
    .preview-paper-logo-injourney img {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }

    .preview-paper-logo span {
        font-family: Arial, sans-serif;
        font-size: 8px;
        color: #a0a0a0;
    }

    .preview-paper-company {
        margin: 0;
        font-size: 12px;
        font-weight: bold;
        text-transform: uppercase;
    }

    .preview-paper-subtitle {
        margin: 3px 0 0;
        font-size: 9px;
    }

    .preview-paper-title {
        margin: 20px 0 5px;
        text-align: center;
        font-size: 13px;
        font-weight: bold;
        text-decoration: underline;
        text-transform: uppercase;
    }

    .preview-paper-number-table {
    width: 85%;
    margin: 8px auto 18px;
    border-collapse: collapse;
    font-size: 10px;
    line-height: 1.2;
    text-align: left;
}

.preview-paper-number-table td {
    padding: 3px 5px;
    vertical-align: top;
}

.preview-paper-number-table td:first-child {
    width: 145px;
}

.preview-paper-number-table td:nth-child(2) {
    width: 12px;
}

/* =========================================
   KETERANGAN HARI DAN TANGGAL
========================================= */

.preview-paper-date-info {
    width: 100%;
    margin: 0 0 10px;
    text-align: left;
    line-height: 1.2;
}

.preview-paper-date-info p {
    margin: 0 0 3px;
    text-align: left;
}

.preview-paper-date-table {
    width: auto;
    margin: 0;
    border-collapse: collapse;
    font-size: 10px;
    line-height: 1.2;
    text-align: left;
}

.preview-paper-date-table td {
    padding: 1px 4px 1px 0;
    vertical-align: top;
    text-align: left;
}

.preview-paper-date-table td:first-child {
    width: 55px;
}

.preview-paper-date-table td:nth-child(2) {
    width: 12px;
}

    .preview-paper p {
        margin: 0 0 10px;
        text-align: justify;
    }

    

/* =========================================
   IDENTITAS PARA PIHAK HALAMAN 1
========================================= */


.preview-page1-parties {
    width: 100%;
    margin: 0;
    text-align: left;
    line-height: 1.2;
}

.preview-page1-intro {
    margin: 0 0 3px !important;
    text-align: left !important;
}

.preview-party-section {
    width: 100%;
    margin: 0 0 6px;
    text-align: left;
    line-height: 1.2;
}

.preview-party-name {
    margin: 0 0 2px !important;
    text-align: left !important;
    line-height: 1.2;
}

.preview-party-details {
    width: 100%;
    margin: 0;
    line-height: 1.2;
}

.preview-party-row {
    display: grid;
    grid-template-columns: 190px 12px minmax(0, 1fr);
    column-gap: 5px;
    align-items: start;
    margin: 0 0 1px;
    line-height: 1.2;
    text-align: left;
}

.preview-party-row span {
    min-width: 0;
    text-align: left;
    overflow-wrap: break-word;
    line-height: 1.2;
}

.preview-party-description {
    margin: 3px 0 0 !important;
    text-align: justify !important;
    line-height: 1.2;
}

.preview-page1-closing {
    margin: 0 0 10px !important;
    text-align: justify !important;
    line-height: 1.2;
}


    /* =========================================
       THUMBNAIL HALAMAN
    ========================================= */

    .preview-thumbnails {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 8px;
        margin-top: 15px;
    }

    .preview-thumbnail {
        padding: 7px 5px;
        border: 1px solid #e1e5e9;
        border-radius: 7px;
        background: #ffffff;
        color: #77818b;
        cursor: pointer;
        text-align: center;
        font-size: 10px;
    }

    .preview-thumbnail.active {
        border-color: #2864E8;
        background: #F1F5FF;
        color: #142C5C;
        font-weight: 700;
    }

    .preview-thumbnail-sheet {
        width: 25px;
        height: 34px;
        margin: 0 auto 5px;
        padding: 5px 3px;
        border: 1px solid #dfe3e7;
        background: #ffffff;
    }

    .preview-thumbnail-sheet span {
        display: block;
        height: 2px;
        margin-bottom: 3px;
        background: #d9dee3;
    }

    /* =========================================
       KONFIRMASI PREVIEW
    ========================================= */

    .preview-confirmation {
        margin-top: 16px;
        padding: 14px;
        border: 1px solid #d9e3f5;
        border-radius: 9px;
        background: #F1F5FF;
    }

    .preview-confirmation-heading {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 8px;
        color: #142C5C;
        font-size: 12px;
        font-weight: 700;
    }

    .preview-confirmation p {
        margin: 0 0 12px;
        color: #7186AE;
        font-size: 11px;
        line-height: 1.6;
    }

    .preview-confirmation label {
        display: flex;
        align-items: flex-start;
        gap: 9px;
        color: #243B5A;
        font-size: 11px;
        line-height: 1.6;
        cursor: pointer;
    }

    .preview-confirmation input {
        margin-top: 2px;
        accent-color: #2864E8;
        flex-shrink: 0;
    }

    /* =========================================
       PERINGATAN PREVIEW
    ========================================= */

    .preview-warning {
        display: flex;
        align-items: flex-start;
        gap: 9px;
        margin-top: 13px;
        padding: 12px;
        border-radius: 8px;
        background: #f5f6f8;
        color: #6e7882;
        font-size: 10px;
        line-height: 1.6;
    }

    .preview-warning i {
        margin-top: 2px;
        color: #2864E8;
    }

    /* =========================================
       RESPONSIVE
    ========================================= */

    @media (max-width: 1100px) {
        .document-preview-column {
            position: static;
        }
    }

    @media (max-width: 500px) {
        .preview-card {
            padding: 13px;
        }

        .preview-paper {
            padding: 30px 23px;
            min-height: 560px;
        }

        .preview-toolbar {
            flex-wrap: wrap;
        }

        .preview-header {
            flex-direction: column;
        }
    }

        /* =========================================
       TOMBOL AKSI DI BAWAH PREVIEW
    ========================================= */

    .dokumen-page .preview-action-area {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        width: 100%;
        margin-top: 12px;
    }

    .dokumen-page .form-action-right {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 10px;
        margin-left: auto;
    }

    @media (max-width: 600px) {
        .dokumen-page .preview-action-area {
            flex-wrap: wrap;
        }

        .dokumen-page .form-action-right {
            width: 100%;
            flex-wrap: wrap;
        }

        .dokumen-page .form-action-right button {
            flex: 1 1 100%;
            justify-content: center;
        }
    }
</style>

{{-- =========================================
     KOLOM PREVIEW DOKUMEN
========================================== --}}

<div class="document-preview-column">

    <div class="preview-card">

        <div class="preview-header">

            <div>
                <h3>Preview Dokumen Kontrak</h3>
                <p>Pratinjau susunan dokumen perjanjian.</p>
            </div>

            <span class="preview-live-label">
                <span></span>
                Live Preview
            </span>

        </div>


        {{-- TOOLBAR PREVIEW --}}
        <div class="preview-toolbar">

            <div class="preview-page-navigation">

                <button
                type="button"
                class="preview-tool-button"
                id="previewPrevPage"
                title="Halaman sebelumnya"
            >
                &lt;
            </button>

                <span class="preview-page-counter">
                    <span id="previewCurrentPage">1</span>
                    /
                    <span id="previewTotalPage">4</span>
                </span>

                <button
                    type="button"
                    class="preview-tool-button"
                    id="previewNextPage"
                    title="Halaman selanjutnya"
                >
                    &gt;
                </button>

            </div>


            <div class="preview-zoom-navigation">

                <button
                type="button"
                class="preview-tool-button"
                id="previewZoomOut"
                title="Perkecil"
            >
                −
            </button>

                <span class="preview-zoom-value" id="previewZoomValue">
                    100%
                </span>

                <button
                type="button"
                class="preview-tool-button"
                id="previewZoomIn"
                title="Perbesar"
            >
                +
            </button>

            </div>

        </div>


        {{-- AREA KERTAS A4 --}}
        <div class="preview-paper-area" id="previewPaperArea">

            <div class="preview-paper" id="previewPaper">

                {{-- HALAMAN 1 --}}
                <section class="preview-document-page" data-page="1">

                    <div class="preview-paper-header">

                        <div class="preview-paper-logos">

            {{-- LOGO INJOURNEY AIRPORTS --}}
            <div class="preview-paper-logo-injourney">
                <img
                    src="{{ asset('images/angkasa-pura-logo.png') }}"
                    alt="Logo InJourney Airports"
                >
            </div>

            {{-- LOGO MITRA --}}
            <div class="preview-paper-logo preview-paper-logo-mitra">

                @if($logoMitraPath)
                    <img
                        src="{{ asset('storage/' . $logoMitraPath) }}"
                        alt="Logo Mitra"
                    >
                @else
                    <span>Logo Mitra</span>
                @endif

            </div>

                </div>
                        {{-- JUDUL PERJANJIAN --}}
<div class="preview-page1-heading">

    <h2>PERJANJIAN POKOK</h2>

    <p>TENTANG</p>

    <p>
        KERJA SAMA PEMANFAATAN FASILITAS KOMERSIAL
        PT ANGKASA PURA INDONESIA
    </p>

    <p>
        UNTUK KEGIATAN USAHA {{ $jenisUsaha }}
    </p>

    <p>
        DI {{ $namaLokasi }}
    </p>

</div>

                    {{-- NOMOR PERJANJIAN --}}
<table class="preview-paper-number-table">
    <tbody>
        <tr>
            <td>Nomor Dokumen Kontrak</td>
            <td>:</td>
            <td>{{ $nomorKontrak }}</td>
        </tr>
        <tr>
            <td>Nomor Kontrak Mitra</td>
            <td>:</td>
            <td id="previewNomorKontrakMitra">
                {{ $nomorKontrakMitra ?: '-' }}
            </td>
        </tr>
    </tbody>
</table>
                   {{-- KETERANGAN HARI DAN TANGGAL PERJANJIAN --}}
<div class="preview-paper-date-info">

    <p>
        Perjanjian Pokok ini dibuat dan ditandatangani pada:
    </p>

    <table class="preview-paper-date-table">
        <tbody>
            <tr>
                <td>Hari</td>
                <td>:</td>
                <td>
                    {{ now()->locale('id')->translatedFormat('l') }}
                </td>
            </tr>
            <tr>
                <td>Tanggal</td>
                <td>:</td>
                <td>
                    {{ now()->locale('id')->translatedFormat('d F Y') }}
                </td>
            </tr>
        </tbody>
    </table>

</div>
                    

{{-- IDENTITAS PARA PIHAK --}}
<div class="preview-page1-parties">

    <p class="preview-page1-intro">Oleh dan antara:</p>

    {{-- PIHAK PERTAMA: AP INDONESIA --}}
    <div class="preview-party-section">

        <p class="preview-party-name">
            <strong>I. {{ $apIndonesia['nama'] }}</strong>
        </p>

        <div class="preview-party-details">

            <div class="preview-party-row">
                <span>Berkedudukan di</span>
                <span>:</span>
                <span>{{ $apIndonesia['domisili'] ?: '-' }}</span>
            </div>

            <div class="preview-party-row">
                <span>Alamat</span>
                <span>:</span>
                <span>{{ $apIndonesia['alamat'] ?: '-' }}</span>
            </div>

            <div class="preview-party-row">
                <span>Dalam hal ini diwakili oleh</span>
                <span>:</span>
                <span>{{ $apIndonesia['penandatangan'] ?: '-' }}</span>
            </div>

            <div class="preview-party-row">
                <span>Selaku</span>
                <span>:</span>
                <span>{{ $apIndonesia['jabatan'] ?: '-' }}</span>
            </div>

        </div>

        <p class="preview-party-description">
            Dengan demikian sah bertindak untuk dan atas nama
            PT Angkasa Pura Indonesia, selanjutnya dalam Perjanjian
            Pokok ini disebut AP INDONESIA.
        </p>

    </div>


    {{-- PIHAK KEDUA: MITRA --}}
    <div class="preview-party-section">

        <p class="preview-party-name">
            <strong>II. {{ $mitra }}</strong>
        </p>

        <div class="preview-party-details">

            <div class="preview-party-row">
    <span>Berkedudukan di</span>
    <span>:</span>
    <span data-preview-field="domisili_mitra_snapshot">
        {{ $domisiliMitra ?: '-' }}
    </span>
</div>

            <div class="preview-party-row">
                <span>Alamat</span>
                <span>:</span>
                <span>{{ $alamatMitra ?: '-' }}</span>
            </div>

            <div class="preview-party-row">
                <span>Dalam hal ini diwakili oleh</span>
                <span>:</span>
                <span data-preview-field="nama_penandatangan_mitra">
                    {{ $namaPenandatanganMitra ?: '-' }}
                </span>
            </div>

            <div class="preview-party-row">
                <span>Selaku</span>
                <span>:</span>
                <span data-preview-field="jabatan_penandatangan_mitra">
                    {{ $jabatanPenandatanganMitra ?: '-' }}
                </span>
            </div>

        </div>

        <p class="preview-party-description">
            Dengan demikian sah bertindak untuk dan atas nama
            {{ $mitra }}, selanjutnya dalam Perjanjian Pokok ini
            disebut MITRA.
        </p>

    </div>


    <p class="preview-page1-closing">
        Selanjutnya, AP INDONESIA dan MITRA secara bersama-sama
        disebut “PARA PIHAK”.
    </p>

</div>

<div class="preview-paper-footer">
    Pratinjau dokumen — Halaman 1 dari 4
</div>

                </section>


                {{-- HALAMAN 2 --}}
                <section
                    class="preview-document-page"
                    data-page="2"
                    style="display: none;"
                >

                    <h4>Pasal 2</h4>
                    <h4>Lokasi dan Fasilitas</h4>

                    <p>
                        Pelaksanaan kegiatan usaha dilakukan pada lokasi
                        fasilitas komersial yang telah disepakati oleh PARA PIHAK.
                    </p>

                    <p>
                        Lokasi fasilitas:
                        <strong>{{ $namaLokasi }}</strong>,
                        Terminal {{ $terminal }},
                        Lantai {{ $lantai }},
                        Kode Ruang {{ $kodeRuang }}.
                    </p>

                    <p>
                        Luas fasilitas:
                        <strong>{{ $luasFasilitas ?: '-' }}</strong>.
                    </p>


                    <h4>Pasal 3</h4>
                    <h4>Jangka Waktu Perjanjian</h4>

                    <p>
                        Jangka waktu kerja sama mengikuti periode yang telah
                        ditetapkan dalam data kerja sama dan dokumen perjanjian
                        yang disepakati oleh PARA PIHAK.
                    </p>

                    <p>
                        Tanggal mulai:
                        <strong>{{ $tanggalMulai }}</strong>.
                    </p>

                    <p>
                        Tanggal berakhir:
                        <strong>{{ $tanggalBerakhir }}</strong>.
                    </p>


                    <h4>Pasal 4</h4>
                    <h4>Evaluasi Tarif</h4>

                    <p>
                        Evaluasi tarif dilakukan setiap
                        <strong data-preview-field="periode_evaluasi_tarif">
                            {{ $periodeEvaluasi }}
                        </strong>
                        <strong data-preview-field="satuan_evaluasi_tarif">
                            {{ $satuanEvaluasi }}
                        </strong>
                        sesuai ketentuan dan kesepakatan PARA PIHAK.
                    </p>


                    <div class="preview-paper-footer">
                        Pratinjau dokumen — Halaman 2 dari 4
                    </div>

                </section>

                {{-- HALAMAN 3 --}}
                <section
                    class="preview-document-page"
                    data-page="3"
                    style="display: none;"
                >

                    <h4>Pasal 5</h4>
                    <h4>Hak dan Kewajiban Para Pihak</h4>

                    <p>
                        PARA PIHAK melaksanakan hak dan kewajiban masing-masing
                        berdasarkan ketentuan yang disepakati dalam perjanjian
                        dan dokumen lampiran yang menjadi bagian dari perjanjian.
                    </p>


                    <h4>Pasal 6</h4>
                    <h4>Kepemilikan Fasilitas</h4>

                    <p>
                        Ketentuan kepemilikan fasilitas yang digunakan dalam
                        pelaksanaan kerja sama mengikuti pilihan klausul yang
                        ditetapkan dalam pengisian data kontrak.
                    </p>

                    <p>
                        Pilihan kepemilikan fasilitas:
                        <strong data-preview-ownership>
                            {{ $klausulKepemilikanText ?: '[Belum dipilih]' }}
                        </strong>.
                    </p>


                    <h4>Pasal 7</h4>
                    <h4>Ketentuan Penutup</h4>

                    <p>
                        Hal-hal yang belum diatur dalam perjanjian ini akan
                        mengikuti ketentuan dalam dokumen pendukung dan
                        kesepakatan tertulis PARA PIHAK.
                    </p>

                    <p>
                        Dokumen lampiran yang dipilih sebagai bagian dari
                        perjanjian:
                    </p>

                    <ul class="preview-paper-list" id="previewAttachmentList">

                        @forelse($lampiranTerpilih as $lampiran)
                            <li>{{ $daftarLampiran[$lampiran] ?? $lampiran }}</li>
                        @empty
                            <li>[Lampiran belum dipilih]</li>
                        @endforelse

                    </ul>


                    <div class="preview-paper-footer">
                        Pratinjau dokumen — Halaman 3 dari 4
                    </div>

                </section>


                {{-- HALAMAN 4 --}}
                <section
                    class="preview-document-page"
                    data-page="4"
                    style="display: none;"
                >

                    <h4>PENUTUP</h4>

                    <p>
                        Demikian dokumen perjanjian ini dibuat untuk menjadi
                        bahan pemeriksaan dan penyelesaian dokumen kontrak
                        oleh PARA PIHAK.
                    </p>

                    <p>
                        Bagian penandatanganan dan ketentuan final mengikuti
                        dokumen resmi yang telah disetujui oleh pihak berwenang.
                    </p>


                    <div class="preview-paper-signature">

                        <div>
                            <strong>PIHAK PERTAMA</strong>

                            <div class="signature-space"></div>

                            <div class="signature-name">
                                {{ $namaPicCommercial }}
                            </div>

                            <div>
                                {{ $jabatanPicCommercial }}
                            </div>

                            <div>
                                PT ANGKASA PURA INDONESIA
                            </div>
                        </div>


                        <div>
                            <strong>PIHAK KEDUA</strong>

                            <div class="signature-space"></div>

                            <div
                                class="signature-name"
                                data-preview-field="nama_penandatangan_mitra"
                            >
                                {{ $namaPenandatanganMitra ?: '[Nama Penandatangan Mitra]' }}
                            </div>

                            <div
                                data-preview-field="jabatan_penandatangan_mitra"
                            >
                                {{ $jabatanPenandatanganMitra ?: '[Jabatan Penandatangan]' }}
                            </div>

                            <div>
                                {{ $mitra }}
                            </div>
                        </div>

                    </div>


                    <div class="preview-paper-footer">
                        Pratinjau dokumen — Halaman 4 dari 4
                    </div>

                </section>

            </div>

        </div>


        
        {{-- THUMBNAIL HALAMAN --}}
        <div class="preview-thumbnails">

            @for($page = 1; $page <= 4; $page++)

                <button
                    type="button"
                    class="preview-thumbnail {{ $page === 1 ? 'active' : '' }}"
                    data-preview-thumbnail="{{ $page }}"
                >

                    <div class="preview-thumbnail-sheet">
                        <span></span>
                        <span></span>
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>

                    Halaman {{ $page }}

                </button>

            @endfor

        </div>


        {{-- KONFIRMASI PEMERIKSAAN --}}
        <div class="preview-confirmation">

            <div class="preview-confirmation-heading">
                <i class="fas fa-clipboard-check"></i>
                Pemeriksaan Dokumen
            </div>

            <p>
                Pastikan informasi yang dimasukkan telah sesuai dengan data
                kerja sama dan ketentuan dokumen kontrak.
            </p>

            <label>
                <input
                    type="checkbox"
                    id="konfirmasiDokumen"
                    name="konfirmasi_dokumen"
                    value="1"
                >

                <span>
                    Saya telah memeriksa dan memastikan data serta isi dokumen
                    kontrak sudah sesuai.
                </span>
            </label>

        </div>


        {{-- INFORMASI PREVIEW --}}
        <div class="preview-warning">

            <i class="fas fa-info-circle"></i>

            <span>
                Preview ini merupakan gambaran tampilan dokumen.
                Isi pasal dan format final harus disesuaikan dengan
                Formulir II Perjanjian Pokok yang berlaku sebelum dokumen
                diekspor atau digunakan secara resmi.
            </span>

        </div>


    </div> {{-- Penutup preview-card --}}

    {{-- =========================================
         TOMBOL AKSI DI BAWAH PREVIEW
    ========================================== --}}

    <div class="preview-action-area">

        {{-- TOMBOL KEMBALI --}}
        <a
            href="{{ $kontrak ? route('kontrak.show', $kontrak->id) : url()->previous() }}"
            class="btn-form-back"
        >
            <i class="fas fa-arrow-left"></i>
            Kembali
        </a>


        {{-- TOMBOL EXPORT DAN LANJUT UPLOAD --}}
        <div class="form-action-right">

            <button
                type="button"
                class="btn-form-export"
                id="btnExportDokumen"
            >
                <i class="fas fa-download"></i>
                Export Dokumen
            </button>

            <button
                type="button"
                class="btn-form-next"
                id="btnLanjutUpload"
            >
                Lanjut Upload Dokumen
                <i class="fas fa-arrow-right"></i>
            </button>

        </div>

    </div>


</div> {{-- Penutup document-preview-column --}}


</div> {{-- Penutup document-workspace --}}


</div> {{-- Penutup dokumen-page --}}

{{-- =========================================
     JAVASCRIPT DOKUMEN KONTRAK
========================================= --}}

{{-- =========================================
     CSS MODAL EXPORT DOKUMEN
========================================== --}}

<style>
    /* =========================================
       POPUP EXPORT DOKUMEN
    ========================================= */

    .export-modal-overlay {
        position: fixed;
        inset: 0;
        z-index: 9999;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 20px;
        background: rgba(15, 23, 42, .55);
    }

    .export-modal-overlay.show {
        display: flex;
    }

    .export-modal {
        width: 100%;
        max-width: 420px;
        padding: 25px;
        border-radius: 14px;
        background: #ffffff;
        box-shadow: 0 15px 50px rgba(0,0,0,.18);
        animation: exportModalShow .2s ease;
    }

    @keyframes exportModalShow {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .export-modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
    }

    .export-modal-header h3 {
        margin: 0;
        color: #142C5C;
        font-size: 17px;
        font-weight: 700;
    }

   .export-modal-close {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 34px;
    height: 34px;
    padding: 0;
    border: none;
    border-radius: 8px;
    background: #f2f4f6;
    color: #68737d;
    cursor: pointer;
    transition: background 0.2s ease, color 0.2s ease;
}

.export-modal-close span {
    font-family: Arial, sans-serif;
    font-size: 25px;
    font-weight: 400;
    line-height: 1;
}

.export-modal-close:hover {
    background: #e5e9ed;
    color: #263746;
}
    .export-modal-description {
        margin: 0 0 20px;
        color: #89939b;
        font-size: 12px;
        line-height: 1.6;
    }

    .export-option {
        display: flex;
        align-items: center;
        gap: 13px;
        width: 100%;
        padding: 15px;
        margin-bottom: 10px;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        background: #ffffff;
        color: #374151;
        text-align: left;
        cursor: pointer;
        transition: .2s;
    }

    .export-option:hover {
        border-color: #2864E8;
        background: #F1F5FF;
    }

    .export-option-icon {
        width: 42px;
        height: 42px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 9px;
        font-size: 19px;
        flex-shrink: 0;
    }

    .export-option-icon.word {
        background: #eaf1ff;
        color: #2865b5;
    }

    .export-option-icon.pdf {
        background: #fff0ed;
        color: #d34e42;
    }

    .export-option-text strong {
        display: block;
        margin-bottom: 4px;
        font-size: 13px;
    }

    .export-option-text small {
        color: #929ba4;
        font-size: 11px;
    }

    .export-modal-cancel {
        width: 100%;
        min-height: 40px;
        margin-top: 5px;
        border: 1px solid #e0e4e8;
        border-radius: 8px;
        background: #ffffff;
        color: #68737d;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
    }

    .export-modal-cancel:hover {
        background: #f7f8fa;
    }

        body.modal-open {
        overflow: hidden;
    }
    .preview-page1-heading {
    width: 100%;
    margin: 20px auto 18px;
    text-align: center;
    line-height: 1.2;
}

.preview-page1-heading h2 {
    margin: 0 0 5px;
    text-align: center;
    font-size: 13px;
    font-weight: bold;
    text-transform: uppercase;
    text-decoration: none;
    line-height: 1.2;
}

.preview-page1-heading p {
    margin: 0 0 3px;
    text-align: center;
    line-height: 1.2;
}
</style>

{{-- MODAL EXPORT --}}
<div
    class="export-modal-overlay"
    id="exportModal"
    aria-hidden="true"
>
    <div
        class="export-modal"
        role="dialog"
        aria-modal="true"
        aria-labelledby="exportModalTitle"
    >

        <div class="export-modal-header">

            <h3 id="exportModalTitle">
                Export Dokumen Kontrak
            </h3>

            <button
                type="button"
                class="export-modal-close"
                id="closeExportModal"
                aria-label="Tutup"
            >
                <span aria-hidden="true">&times;</span>
            </button>

        </div>

        <p class="export-modal-description">
            Pilih format dokumen yang ingin diekspor.
        </p>

        <button
            type="button"
            class="export-option"
            id="exportWordButton"
        >

            <span class="export-option-icon word">
                <i class="fas fa-file-word"></i>
            </span>

            <span class="export-option-text">
                <strong>Microsoft Word</strong>
                <small>Ekspor dokumen dalam format DOCX.</small>
            </span>

        </button>

        <button
            type="button"
            class="export-option"
            id="exportPdfButton"
        >

            <span class="export-option-icon pdf">
                <i class="fas fa-file-pdf"></i>
            </span>

            <span class="export-option-text">
                <strong>PDF</strong>
                <small>Ekspor dokumen dalam format PDF.</small>
            </span>

        </button>

        <button
            type="button"
            class="export-modal-cancel"
            id="cancelExportModal"
        >
            Batal
        </button>

    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {

    /* =========================================
       ELEMENT FORM DAN PREVIEW
    ========================================= */

    const documentForm = document.getElementById('documentForm');

    const previewPaper = document.getElementById('previewPaper');
    const previewPaperArea = document.getElementById('previewPaperArea');

    const previewPages = document.querySelectorAll('.preview-document-page');
    const previewThumbnails = document.querySelectorAll('[data-preview-thumbnail]');

    const currentPageLabel = document.getElementById('previewCurrentPage');
    const totalPageLabel = document.getElementById('previewTotalPage');

    const previousPageButton = document.getElementById('previewPrevPage');
    const nextPageButton = document.getElementById('previewNextPage');

    const zoomInButton = document.getElementById('previewZoomIn');
    const zoomOutButton = document.getElementById('previewZoomOut');
    const zoomValueLabel = document.getElementById('previewZoomValue');

    const confirmationCheckbox = document.getElementById('konfirmasiDokumen');

    const exportModal = document.getElementById('exportModal');

    let currentPage = 1;
    let currentZoom = 100;

    const totalPages = previewPages.length;

    if (totalPageLabel) {
        totalPageLabel.textContent = totalPages;
    }


    /* =========================================
       NAVIGASI HALAMAN PREVIEW
    ========================================= */

    function showPreviewPage(pageNumber) {

        if (!previewPages.length) {
            return;
        }

        if (pageNumber < 1 || pageNumber > totalPages) {
            return;
        }

        currentPage = pageNumber;

        previewPages.forEach(function (page) {

            const pageNumberAttribute = Number(page.dataset.page);

            page.style.display =
                pageNumberAttribute === currentPage ? 'block' : 'none';

        });


        previewThumbnails.forEach(function (thumbnail) {

            const thumbnailPage = Number(
                thumbnail.dataset.previewThumbnail
            );

            thumbnail.classList.toggle(
                'active',
                thumbnailPage === currentPage
            );

        });


        if (currentPageLabel) {
            currentPageLabel.textContent = currentPage;
        }

        if (previousPageButton) {
            previousPageButton.disabled = currentPage === 1;
        }

        if (nextPageButton) {
            nextPageButton.disabled = currentPage === totalPages;
        }

        if (previewPaperArea) {
            previewPaperArea.scrollTop = 0;
        }

    }


    previousPageButton?.addEventListener('click', function () {
        showPreviewPage(currentPage - 1);
    });


    nextPageButton?.addEventListener('click', function () {
        showPreviewPage(currentPage + 1);
    });


    previewThumbnails.forEach(function (thumbnail) {

        thumbnail.addEventListener('click', function () {

            const pageNumber = Number(
                this.dataset.previewThumbnail
            );

            showPreviewPage(pageNumber);

        });

    });


    showPreviewPage(1);


    /* =========================================
       ZOOM PREVIEW
    ========================================= */

    function updatePreviewZoom() {

        if (!previewPaper) {
            return;
        }

        previewPaper.style.zoom = (currentZoom / 100);

        if (zoomValueLabel) {
            zoomValueLabel.textContent = currentZoom + '%';
        }

        if (zoomOutButton) {
            zoomOutButton.disabled = currentZoom <= 60;
        }

        if (zoomInButton) {
            zoomInButton.disabled = currentZoom >= 140;
        }

    }


    zoomInButton?.addEventListener('click', function () {

        if (currentZoom < 140) {
            currentZoom += 10;
            updatePreviewZoom();
        }

    });


    zoomOutButton?.addEventListener('click', function () {

        if (currentZoom > 60) {
            currentZoom -= 10;
            updatePreviewZoom();
        }

    });


    updatePreviewZoom();


    /* =========================================
       LIVE PREVIEW INPUT FORM
    ========================================= */

    function getInputValue(id) {

        const element = document.getElementById(id);

        return element ? element.value.trim() : '';

    }


    function updatePreviewField(fieldName, value, fallback) {

        const elements = document.querySelectorAll(
            '[data-preview-field="' + fieldName + '"]'
        );

        elements.forEach(function (element) {

            element.textContent = value || fallback;

        });

    }
        const livePreviewFields = {

        'nama_penandatangan_mitra': '[Nama Penandatangan Mitra]',

        'jabatan_penandatangan_mitra': '[Jabatan Penandatangan]',

         'domisili_mitra_snapshot': '-',
        
         'periode_evaluasi_tarif': '1',

        'satuan_evaluasi_tarif': 'tahun'

    };


    Object.keys(livePreviewFields).forEach(function (fieldName) {

        const input = document.getElementById(fieldName);

        if (!input) {
            return;
        }

        input.addEventListener('input', function () {

            updatePreviewField(
                fieldName,
                this.value.trim(),
                livePreviewFields[fieldName]
            );

        });


        input.addEventListener('change', function () {

            updatePreviewField(
                fieldName,
                this.value.trim(),
                livePreviewFields[fieldName]
            );

        });


        updatePreviewField(
            fieldName,
            input.value.trim(),
            livePreviewFields[fieldName]
        );

    });


    /* =========================================
       LIVE PREVIEW KLAUSUL KEPEMILIKAN
    ========================================= */

    const ownershipInputs = document.querySelectorAll(
        'input[name="klausul_kepemilikan_fasilitas"]'
    );

    const ownershipPreview = document.querySelector(
        '[data-preview-ownership]'
    );


    function updateOwnershipPreview() {

        const selectedOwnership = document.querySelector(
            'input[name="klausul_kepemilikan_fasilitas"]:checked'
        );

        if (!ownershipPreview) {
            return;
        }

        if (!selectedOwnership) {
            ownershipPreview.textContent = '[Belum dipilih]';
            return;
        }

        ownershipPreview.textContent =
            selectedOwnership.value === 'mitra'
                ? 'Milik Mitra'
                : 'Milik AP Indonesia';

    }


    ownershipInputs.forEach(function (input) {

        input.addEventListener('change', updateOwnershipPreview);

    });

    updateOwnershipPreview();


    /* =========================================
       LIVE PREVIEW LAMPIRAN
    ========================================= */

    const attachmentInputs = document.querySelectorAll(
        'input[name="lampiran_perjanjian[]"]'
    );

    const attachmentPreview = document.getElementById(
        'previewAttachmentList'
    );

    const lampiranLainnyaCheckbox = document.getElementById(
        'lampiranLainnya'
    );

    const namaDokumenLainnyaInput = document.getElementById(
        'nama_dokumen_lainnya'
    );

    const namaDokumenLainnyaWrapper = document.getElementById(
        'namaDokumenLainnyaWrapper'
    );


    function updateAttachmentPreview() {

        if (!attachmentPreview) {
            return;
        }

        attachmentPreview.innerHTML = '';

        /*
         * SUP dan BAK berasal dari hidden input.
         * Keduanya tetap dimasukkan ke preview karena wajib.
         */

        const selectedAttachments = Array.from(attachmentInputs)
            .filter(function (input) {

                return input.type === 'hidden' || input.checked;

            })
            .map(function (input) {

                return input.value;

            });


        if (selectedAttachments.length === 0) {

            const item = document.createElement('li');

            item.textContent = '[Lampiran belum dipilih]';

            attachmentPreview.appendChild(item);

            return;
        }


        selectedAttachments.forEach(function (attachment) {

            const item = document.createElement('li');

            const attachmentLabels = {

                'SUP': 'Syarat-Syarat Umum Perjanjian (SUP)',

                'BAK': 'Berita Acara Kesepakatan (BAK)',

                'Denah Fasilitas Komersial': 'Denah Fasilitas Komersial',

                'Perjanjian Tingkat Layanan': 'Perjanjian Tingkat Layanan',

                'Lainnya': 'Dokumen Lainnya'

            };


            if (attachment === 'Lainnya') {

                const namaDokumen = namaDokumenLainnyaInput
                    ? namaDokumenLainnyaInput.value.trim()
                    : '';

                item.textContent = namaDokumen
                    ? 'Dokumen Lainnya: ' + namaDokumen
                    : 'Dokumen Lainnya';

            } else {

                item.textContent =
                    attachmentLabels[attachment] || attachment;

            }

            attachmentPreview.appendChild(item);

        });

    }


    function updateNamaDokumenLainnya() {

        if (!lampiranLainnyaCheckbox || !namaDokumenLainnyaWrapper) {
            return;
        }

        const isChecked = lampiranLainnyaCheckbox.checked;

        namaDokumenLainnyaWrapper.style.display =
            isChecked ? 'flex' : 'none';

        if (namaDokumenLainnyaInput) {

            namaDokumenLainnyaInput.required = isChecked;

            if (!isChecked) {
                namaDokumenLainnyaInput.value = '';
            }

        }

        updateAttachmentPreview();

    }


    attachmentInputs.forEach(function (input) {

        input.addEventListener('change', function () {

            if (input.id === 'lampiranLainnya') {
                updateNamaDokumenLainnya();
            } else {
                updateAttachmentPreview();
            }

        });

    });


    namaDokumenLainnyaInput?.addEventListener(
        'input',
        updateAttachmentPreview
    );


    updateNamaDokumenLainnya();
    updateAttachmentPreview();
        /* =========================================
       LIVE PREVIEW LOGO MITRA
    ========================================= */
    /* =========================================
   LIVE PREVIEW NOMOR KONTRAK MITRA
========================================= */

    const nomorKontrakMitraInput = document.getElementById(
        'nomor_kontrak_mitra'
    );

    const previewNomorKontrakMitra = document.getElementById(
        'previewNomorKontrakMitra'
    );

    nomorKontrakMitraInput?.addEventListener('input', function () {
        if (previewNomorKontrakMitra) {
            previewNomorKontrakMitra.textContent =
                this.value.trim() || '-';
        }
    });
    const logoInput = document.getElementById('logoMitraInput');

    const logoPreviewImage = document.getElementById(
        'logoPreviewImage'
    );

    const logoPlaceholder = document.getElementById(
        'logoPlaceholder'
    );

   const previewPaperLogo = document.querySelector(
    '.preview-paper-logo-mitra'
);


    logoInput?.addEventListener('change', function () {

        const file = this.files[0];

        if (!file) {
            return;
        }

        const allowedTypes = [
            'image/png',
            'image/jpeg'
        ];

        if (!allowedTypes.includes(file.type)) {

            alert('Format logo harus PNG atau JPG.');

            this.value = '';

            return;
        }


        // Batas ukuran logo maksimal 5 MB.
        if (file.size > 5 * 1024 * 1024) {

            alert('Ukuran logo maksimal 5 MB.');

            this.value = '';

            return;
        }


        const reader = new FileReader();

        reader.onload = function (event) {

            if (logoPreviewImage) {

                logoPreviewImage.src = event.target.result;

                logoPreviewImage.style.display = 'block';

            }

            if (logoPlaceholder) {
                logoPlaceholder.style.display = 'none';
            }


            if (previewPaperLogo) {

                previewPaperLogo.innerHTML = '';

                const image = document.createElement('img');

                image.src = event.target.result;

                image.alt = 'Logo Mitra';

                previewPaperLogo.appendChild(image);

            }

        };

        reader.readAsDataURL(file);

    });


    /* =========================================
       VALIDASI FORM
    ========================================= */

    function validateDocumentForm() {

        if (!documentForm) {
            return false;
        }


        if (!documentForm.checkValidity()) {

            documentForm.reportValidity();

            return false;

        }


        const confirmationIsChecked =
            confirmationCheckbox && confirmationCheckbox.checked;


        if (!confirmationIsChecked) {

            alert(
                'Silakan centang konfirmasi bahwa dokumen telah diperiksa.'
            );

            confirmationCheckbox?.focus();

            return false;

        }


        return true;

    }
        /* =========================================
       MODAL EXPORT DOKUMEN
    ========================================= */

    function openExportModal() {

        if (!exportModal) {
            return;
        }

        exportModal.classList.add('show');

        exportModal.setAttribute('aria-hidden', 'false');

        document.body.classList.add('modal-open');

    }


    function closeExportModal() {

        if (!exportModal) {
            return;
        }

        exportModal.classList.remove('show');

        exportModal.setAttribute('aria-hidden', 'true');

        document.body.classList.remove('modal-open');

    }


    document.getElementById('btnExportDokumen')
        ?.addEventListener('click', function () {

            if (!validateDocumentForm()) {
                return;
            }

            openExportModal();

        });


    document.getElementById('closeExportModal')
        ?.addEventListener('click', closeExportModal);


    document.getElementById('cancelExportModal')
        ?.addEventListener('click', closeExportModal);


    exportModal?.addEventListener('click', function (event) {

        if (event.target === exportModal) {
            closeExportModal();
        }

    });


    document.addEventListener('keydown', function (event) {

        if (event.key === 'Escape') {
            closeExportModal();
        }

    });


    /* =========================================
   SIMPAN DATA LALU UNDUH DOKUMEN
========================================= */

async function saveAndDownload(downloadUrl) {

    if (!documentForm) {
        alert('Form dokumen tidak ditemukan.');
        return;
    }

    const formData = new FormData(documentForm);

    try {

        const response = await fetch(documentForm.action, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: formData
        });

        const data = await response.json().catch(() => ({}));

        if (!response.ok) {

            if (data.errors) {
                const firstError = Object.values(data.errors)[0];
                alert(Array.isArray(firstError) ? firstError[0] : firstError);
            } else {
                alert(data.message || 'Data dokumen gagal disimpan.');
            }

            return;
        }

        // Jika penyimpanan berhasil, lanjutkan unduhan.
        window.location.href = downloadUrl;

        closeExportModal();

    } catch (error) {

        console.error(error);

        alert('Terjadi kesalahan saat menyimpan dokumen.');

    }
}


/* =========================================
   EXPORT WORD
========================================= */

document.getElementById('exportWordButton')
    ?.addEventListener('click', function () {

        @if($kontrak)
            saveAndDownload(
                @json(route('kontrak.word', $kontrak->id))
            );
        @else
            alert('Data kontrak belum tersedia.');
        @endif

    });


/* =========================================
   EXPORT PDF
========================================= */

document.getElementById('exportPdfButton')
    ?.addEventListener('click', function () {

        @if($kontrak)
            saveAndDownload(
                @json(route('kontrak.pdf', $kontrak->id))
            );
        @else
            alert('Data kontrak belum tersedia.');
        @endif

    });

    
/* =========================================
   LANJUT KE UPLOAD DOKUMEN
========================================= */

document.getElementById('btnLanjutUpload')
    ?.addEventListener('click', function () {

        if (!validateDocumentForm()) {
            return;
        }

        @if($kontrak)

            window.location.href =
                @json(route('kontrak.uploadFinal', $kontrak->id));

        @else

            alert(
                'Data kontrak belum tersedia. Simpan data kerja sama terlebih dahulu.'
            );

        @endif

    });

});
</script>
{{-- PENUTUP HALAMAN --}}
</div>
@endsection