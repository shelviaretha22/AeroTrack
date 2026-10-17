@extends('layouts.app')

@section('page-title', 'Kontrak')

@section('content')

@php
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

    $namaPic = $tenant?->nama_pic ?? '-';
    $jabatanPic = $tenant?->jabatan_pic ?? '-';
    $alamatPerusahaan = $tenant?->alamat_perusahaan ?? '-';
    $npwp = $tenant?->npwp ?? '-';
    $nib = $tenant?->nib ?? '-';

    $kodeRuang = $lokasi?->kode_ruang ?? '-';
    $namaLokasi = $lokasi?->lokasi ?? '-';
    $terminal = $lokasi?->terminal ?? '-';
    $lantai = $lokasi?->lantai ?? '-';

    $tanggalMulai = $kerjaSama?->tanggal_mulai
        ? \Illuminate\Support\Carbon::parse($kerjaSama->tanggal_mulai)->translatedFormat('d F Y')
        : '[Tanggal mulai kerja sama]';

    $tanggalBerakhir = $kerjaSama?->tanggal_berakhir
        ? \Illuminate\Support\Carbon::parse($kerjaSama->tanggal_berakhir)->translatedFormat('d F Y')
        : '[Tanggal berakhir kerja sama]';

    $tanggalDokumen = $isPreview
        ? '[Tanggal ditetapkan saat kontrak resmi dibuat]'
        : '[Tanggal dokumen]';
@endphp


<style>
    .dokumen-page {
        width: 100%;
        max-width: 1200px;
        margin: 0 auto;
        padding: 24px;
        color: #243B3B;
    }

    .dokumen-page .page-heading {
        margin-bottom: 20px;
    }

    .dokumen-page .page-title {
        margin: 0;
        color: #244D50;
        font-size: 26px;
        font-weight: 700;
    }

    .dokumen-page .page-subtitle {
        margin-top: 6px;
        color: #7A9290;
        font-size: 14px;
    }

    .dokumen-page .contract-steps {
        display: flex;
        align-items: flex-start;
        justify-content: center;
        max-width: 720px;
        margin: 28px auto;
        gap: 0;
    }

    .dokumen-page .contract-step {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
        min-width: 110px;
        color: #8BA5A2;
        text-align: center;
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

    .dokumen-page .toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        margin: 24px 0;
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

    .dokumen-page .btn-next {
        background: #16877F;
        color: #FFFFFF;
    }

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

    .dokumen-page .paper {
        width: 100%;
        max-width: 850px;
        margin: 0 auto 28px;
        padding: 48px 56px;
        border: 1px solid #E2E8E7;
        border-radius: 4px;
        background: #FFFFFF;
        box-shadow: 0 4px 18px rgba(36, 77, 80, 0.06);
        color: #222222;
        font-family: "Times New Roman", serif;
        font-size: 14px;
        line-height: 1.8;
    }

    .dokumen-page .paper-header {
        margin-bottom: 28px;
        padding-bottom: 18px;
        border-bottom: 2px solid #365F65;
        text-align: center;
    }

    .dokumen-page .paper-header h2 {
        margin: 0;
        font-size: 17px;
        font-weight: 700;
    }

    .dokumen-page .paper-header p {
        margin: 2px 0;
        font-size: 12px;
    }

    .dokumen-page .document-title {
        margin-top: 24px;
        text-align: center;
        font-size: 17px;
        font-weight: 700;
        text-decoration: underline;
    }

    .dokumen-page .document-number {
        margin: 4px 0 24px;
        text-align: center;
        font-size: 13px;
    }

    .dokumen-page .paper h3 {
        margin: 22px 0 8px;
        font-size: 14px;
        font-weight: 700;
    }

    .dokumen-page .paper p {
        margin: 10px 0;
        text-align: justify;
    }

    .dokumen-page .paper table {
        width: 100%;
        margin: 10px 0 18px;
        border-collapse: collapse;
    }

    .dokumen-page .paper td {
        padding: 3px 5px;
        vertical-align: top;
    }

    .dokumen-page .paper .label-col {
        width: 175px;
    }

    .dokumen-page .signature-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 40px;
        margin-top: 40px;
        text-align: center;
        break-inside: avoid;
    }

    .dokumen-page .signature-space {
        height: 90px;
    }

    .dokumen-page .check-panel {
        max-width: 850px;
        margin: 24px auto 0;
        padding: 22px;
        border: 1px solid #DCE6E4;
        border-radius: 12px;
        background: #FFFFFF;
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
        margin-top: 20px;
    }

    .dokumen-page .check-help {
        color: #7A9290;
        font-size: 12px;
    }

    @media (max-width: 640px) {
        .dokumen-page {
            padding: 14px;
        }

        .dokumen-page .contract-step {
            min-width: 76px;
        }

        .dokumen-page .step-label {
            font-size: 10px;
        }

        .dokumen-page .paper {
            padding: 24px 18px;
            font-size: 12px;
        }

        .dokumen-page .paper .label-col {
            width: 125px;
        }

        .dokumen-page .signature-grid {
            grid-template-columns: 1fr;
            gap: 16px;
        }
    }

    @media print {
        body * {
            visibility: hidden;
        }

        .dokumen-page .paper,
        .dokumen-page .paper * {
            visibility: visible;
        }

        .dokumen-page .paper {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            max-width: none;
            margin: 0;
            padding: 15mm;
            border: none;
            box-shadow: none;
        }

        .dokumen-page .page-heading,
        .dokumen-page .contract-steps,
        .dokumen-page .toolbar,
        .dokumen-page .document-note,
        .dokumen-page .check-panel {
            display: none !important;
        }
    }
</style>

<div class="dokumen-page">

<div class="page-heading">
    <div>
        <h1 class="page-title">Isi Dokumen Kontrak</h1>
        <p class="page-subtitle">
            Periksa isi dokumen sebelum melanjutkan ke tahap ACC.
        </p>
    </div>
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


    <div class="toolbar">
        <a href="{{ route('kontrak') }}" class="btn btn-back">
            Kembali ke Data Kontrak
        </a>

        <button type="button" class="btn btn-print" onclick="window.print()">
            Cetak / Simpan PDF
        </button>
    </div>

    <div class="document-note">
        <strong>Perhatian:</strong>
        Periksa kembali seluruh data dan isi perjanjian. Dokumen ini merupakan
        kerangka awal untuk implementasi sistem. Pastikan naskah sesuai template
        resmi yang telah disetujui sebelum digunakan sebagai kontrak sebenarnya.
    </div>

    <div class="document-title">
        SURAT PERJANJIAN KERJA SAMA DAN SEWA TENANT
    </div>

    <div style="text-align:center; font-weight:700;">
        BANDAR UDARA INTERNASIONAL JUANDA
    </div>

    <div class="document-number">
        Nomor: {{ $nomorKontrak }}
    </div>

    <p>
        Pada hari ini, [Hari], tanggal [Tanggal], bertempat di Bandar
        Udara Internasional Juanda, para pihak yang bertanda tangan
        di bawah ini:
    </p>

    <h3>1. PIHAK PERTAMA (MITRA USAHA / PENYEWA)</h3>

    <table>
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
            <td>: {{ $namaPic }}</td>
        </tr>
        <tr>
            <td>Jabatan</td>
            <td>: {{ $jabatanPic }}</td>
        </tr>
        <tr>
            <td>Alamat</td>
            <td>: {{ $alamatPerusahaan }}</td>
        </tr>
        <tr>
            <td>Nomor Pokok Wajib Pajak (NPWP)</td>
            <td>: {{ $npwp }}</td>
        </tr>
    </table>

    <p>
        Dalam hal ini bertindak untuk dan atas nama
        {{ $mitra }}, selanjutnya disebut sebagai
        <strong>PIHAK PERTAMA</strong>.
    </p>

    <h3>2. PIHAK KEDUA (PIHAK KOMERSIL / PENGELOLA BANDARA)</h3>

    <table>
        <tr>
            <td class="label-col">Nama</td>
            <td>: {{ $kerjaSama?->pic_commercial ?: '[Nama PIC Komersil]' }}</td>
        </tr>
        <tr>
            <td>Jabatan</td>
            <td>: {{ $kontrak?->jabatan_commercial ?: '[Jabatan PIC Komersil]' }}</td>
        </tr>
        <tr>
            <td>Unit/Instansi</td>
            <td>: PT Angkasa Pura Indonesia (InJourney Airports) – Bandar Udara Internasional Juanda</td>
        </tr>
        <tr>
            <td>Alamat Kantor Pengelola</td>
            <td>: [Alamat Kantor Pengelola]</td>
        </tr>
    </table>

    <p>
        Dalam hal ini bertindak untuk dan atas nama pengelola kegiatan
        komersial Bandar Udara Internasional Juanda sesuai dengan
        kewenangan yang dimiliki, selanjutnya disebut sebagai
        <strong>PIHAK KEDUA</strong>.
    </p>

    <p>
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

    <h3>PASAL I</h3>
    <h3>OBJEK DAN RUANG LINGKUP PERJANJIAN</h3>

    <p>
        1. PIHAK KEDUA memberikan hak pemanfaatan area tenant kepada
        PIHAK PERTAMA untuk menjalankan kegiatan usaha sesuai dengan
        jenis usaha yang telah disepakati.
    </p>

    <p>
        2. Objek perjanjian berada di area komersial Bandar Udara
        Internasional Juanda dengan rincian sebagai berikut:
    </p>

    <table>
        <tr>
            <td class="label-col">a. Kode Tenant</td>
            <td>: {{ $tenant?->kode_tenant ?: '[Kode Tenant belum tersedia]' }}</td>
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
            <td>: {{ $lokasi?->luas_area !== null ? $lokasi->luas_area . ' m²' : '[Luas Area belum tersedia]' }}</td>
        </tr>
        <tr>
            <td>h. Daya Listrik</td>
            <td>: {{ $lokasi?->daya_listrik ?: '[Daya Listrik belum tersedia]' }}</td>
        </tr>
        <tr>
            <td>i. Jenis Usaha</td>
            <td>: {{ $jenisUsaha }}</td>
        </tr>
        <tr>
            <td>j. Bentuk Kerja Sama</td>
            <td>: {{ $kerjaSama?->bentuk_kerja_sama ?: ($tenant?->bentuk_kerja_sama ?: '[Bentuk Kerja Sama belum tersedia]') }}</td>
        </tr>
    </table>

    <p>
        3. Area tenant hanya dapat digunakan untuk kegiatan usaha yang
        telah memperoleh persetujuan PIHAK KEDUA.
    </p>

    <p>
        4. Perubahan lokasi, luas area, jenis usaha, atau bentuk
        pemanfaatan area wajib memperoleh persetujuan tertulis dari
        PIHAK KEDUA.
    </p>

    <p>
        5. Rincian lokasi dan kondisi area tenant dituangkan dalam
        dokumen pendukung atau Berita Acara yang menjadi bagian tidak
        terpisahkan dari perjanjian ini.
    </p>

    <h3>PASAL II</h3>
    <h3>JANGKA WAKTU PERJANJIAN</h3>

    <p>
        1. Perjanjian ini berlaku sejak tanggal {{ $tanggalMulai }}
        sampai dengan tanggal {{ $tanggalBerakhir }}.
    </p>

    <p>
        2. Jangka waktu sewa atau kerja sama adalah selama
        {{ $kerjaSama?->tanggal_mulai && $kerjaSama?->tanggal_berakhir
            ? \Illuminate\Support\Carbon::parse($kerjaSama->tanggal_mulai)->diffInMonths(
                \Illuminate\Support\Carbon::parse($kerjaSama->tanggal_berakhir)
            ) . ' bulan (perkiraan berdasarkan tanggal kerja sama)'
            : '[Durasi Sewa belum tersedia]' }},
        sesuai dengan ketentuan yang disepakati PARA PIHAK.
    </p>

    <p>
        3. Tanggal mulai operasional tenant dapat ditetapkan berdasarkan
        kesiapan area, penyelesaian persyaratan administrasi, dan
        dokumen aktivasi yang telah disetujui.
    </p>

    <p>
        4. Perpanjangan jangka waktu perjanjian dapat dilakukan
        berdasarkan kesepakatan tertulis PARA PIHAK dengan
        memperhatikan ketentuan yang berlaku.
    </p>

    <p>
        5. Permohonan perpanjangan wajib diajukan sebelum berakhirnya
        masa perjanjian sesuai dengan jangka waktu pemberitahuan yang
        disepakati PARA PIHAK.
    </p>

    <p>
        6. Berakhirnya jangka waktu perjanjian tidak menghapus
        kewajiban PARA PIHAK yang masih belum diselesaikan.
    </p>

    <h3>PASAL III</h3>
    <h3>NILAI SEWA DAN KEWAJIBAN PEMBAYARAN</h3>

    <p>
        1. Nilai sewa atau imbalan kerja sama ditetapkan berdasarkan
        kesepakatan PARA PIHAK dengan rincian sebagai berikut:
    </p>

    <table>
        <tr>
            <td class="label-col">a. Nilai Sewa</td>
            <td>: [Nilai Sewa belum tersedia]</td>
        </tr>
        <tr>
            <td>b. Periode Pembayaran</td>
            <td>: [Periode Pembayaran belum tersedia]</td>
        </tr>
        <tr>
            <td>c. Pajak Pertambahan Nilai (PPN)</td>
            <td>: [Persentase PPN sesuai ketentuan yang berlaku]</td>
        </tr>
        <tr>
            <td>d. Biaya Tambahan, apabila ada</td>
            <td>: [Biaya Tambahan belum tersedia]</td>
        </tr>
        <tr>
            <td>e. Total Tagihan</td>
            <td>: [Total Tagihan belum tersedia]</td>
        </tr>
    </table>

    <p>
        2. Pembayaran dilakukan oleh PIHAK PERTAMA kepada PIHAK KEDUA
        sesuai dengan jadwal, mekanisme, dan ketentuan pembayaran yang
        telah disepakati.
    </p>

    <p>
        3. Ketentuan mengenai pajak mengikuti peraturan perpajakan yang
        berlaku. Pengenaan PPN dan pajak lainnya disesuaikan dengan
        ketentuan yang berlaku serta status transaksi PARA PIHAK.
    </p>

    <p>
        4. PIHAK KEDUA berhak menerbitkan tagihan atau dokumen
        pembayaran sesuai dengan nilai dan periode yang telah
        disepakati.
    </p>

    <p>
        5. PIHAK PERTAMA wajib melakukan pembayaran tepat waktu dan
        menyimpan bukti pembayaran sebagai dokumen pendukung.
    </p>

    <p>
        6. Apabila terjadi keterlambatan pembayaran, penanganannya
        dilakukan sesuai dengan ketentuan yang tercantum dalam
        perjanjian dan/atau dokumen pendukung yang telah disepakati
        PARA PIHAK.
    </p>

    <p>
        7. Perubahan nilai sewa, tarif, pajak, atau komponen biaya
        lainnya harus didasarkan pada ketentuan yang berlaku dan/atau
        kesepakatan tertulis PARA PIHAK.
    </p>

    <h3>PASAL IV</h3>
    <h3>HAK DAN KEWAJIBAN PARA PIHAK</h3>

    <p><strong>A. Hak dan Kewajiban PIHAK PERTAMA</strong></p>

    <p>
        1. PIHAK PERTAMA berhak menggunakan area tenant sesuai dengan
        lokasi, luas, dan peruntukan yang telah disepakati.
    </p>
    <p>
        2. PIHAK PERTAMA wajib menjalankan kegiatan usaha sesuai dengan
        izin, ketentuan operasional bandara, standar keselamatan,
        keamanan, dan peraturan yang berlaku.
    </p>
    <p>
        3. PIHAK PERTAMA wajib menjaga kebersihan, ketertiban,
        keamanan, dan kondisi area tenant selama masa perjanjian.
    </p>
    <p>
        4. PIHAK PERTAMA wajib memenuhi kewajiban pembayaran sesuai
        dengan nilai dan jadwal yang telah ditetapkan.
    </p>
    <p>
        5. PIHAK PERTAMA wajib melengkapi dokumen administrasi,
        perizinan, dan persyaratan operasional yang diperlukan.
    </p>
    <p>
        6. PIHAK PERTAMA dilarang mengalihkan hak penggunaan area
        kepada pihak lain tanpa persetujuan tertulis PIHAK KEDUA.
    </p>
    <p>
        7. PIHAK PERTAMA wajib melaporkan kendala operasional yang
        berpengaruh terhadap pelaksanaan perjanjian kepada PIHAK KEDUA.
    </p>
    <p>
        8. PIHAK PERTAMA wajib mematuhi ketentuan mengenai akses,
        penggunaan fasilitas, dan aktivitas komersial di lingkungan
        bandara.
    </p>

    <p><strong>B. Hak dan Kewajiban PIHAK KEDUA</strong></p>

    <p>
        1. PIHAK KEDUA berhak menerima pembayaran sewa atau imbalan
        kerja sama sesuai dengan ketentuan perjanjian.
    </p>
    <p>
        2. PIHAK KEDUA berhak melakukan pemantauan dan evaluasi
        terhadap penggunaan area serta pelaksanaan kewajiban PIHAK
        PERTAMA.
    </p>
    <p>
        3. PIHAK KEDUA wajib menyediakan hak pemanfaatan area sesuai
        dengan objek dan ketentuan yang telah disepakati.
    </p>
    <p>
        4. PIHAK KEDUA wajib menyampaikan informasi mengenai ketentuan
        operasional, administrasi, dan pembayaran yang relevan kepada
        PIHAK PERTAMA.
    </p>
    <p>
        5. PIHAK KEDUA berhak meminta perbaikan atau tindak lanjut
        apabila ditemukan ketidaksesuaian dalam pelaksanaan perjanjian.
    </p>
    <p>
        6. PIHAK KEDUA wajib menindaklanjuti permasalahan yang menjadi
        tanggung jawabnya sesuai dengan kewenangan dan ketentuan yang
        berlaku.
    </p>

    <h3>PASAL V</h3>
    <h3>AKTIVASI DAN PELAKSANAAN OPERASIONAL TENANT</h3>

    <p>
        1. PIHAK PERTAMA wajib menyelesaikan seluruh persyaratan
        administrasi dan operasional sebelum memulai kegiatan usaha.
    </p>
    <p>
        2. Aktivasi tenant dilakukan setelah persyaratan yang
        ditetapkan PIHAK KEDUA dinyatakan lengkap dan memenuhi
        ketentuan.
    </p>
    <p>
        3. Persyaratan aktivasi dapat mencakup dokumen perjanjian,
        perizinan usaha, kesiapan area, kebutuhan utilitas, serta
        dokumen pendukung lainnya sesuai dengan jenis usaha.
    </p>
    <p>
        4. Tanggal aktivasi dan tanggal mulai operasional dicatat
        dalam dokumen administrasi atau Berita Acara yang disepakati
        PARA PIHAK.
    </p>
    <p>
        5. PIHAK PERTAMA tidak diperkenankan memulai operasional
        sebelum memperoleh persetujuan atau pemenuhan persyaratan yang
        diwajibkan oleh PIHAK KEDUA.
    </p>
    <p>
        6. Apabila terdapat kendala yang menghambat aktivasi, PARA
        PIHAK wajib melakukan koordinasi untuk menentukan tindak
        lanjut sesuai dengan tanggung jawab masing-masing.
    </p>
    <p>
        7. Pelaksanaan operasional tenant wajib memperhatikan
        ketentuan keselamatan, keamanan, kebersihan, pelayanan, dan
        tata tertib Bandar Udara Internasional Juanda.
    </p>

    <h3>PASAL VI</h3>
    <h3>DOKUMEN PENDUKUNG DAN ADMINISTRASI</h3>

    <p>
        1. Dokumen pendukung perjanjian dapat meliputi Berita Acara,
        dokumen identitas mitra usaha, dokumen perpajakan, dokumen
        lokasi, rincian nilai sewa, dan dokumen lain yang disepakati
        PARA PIHAK.
    </p>
    <p>
        2. Setiap dokumen pendukung yang telah disetujui PARA PIHAK
        menjadi bagian yang tidak terpisahkan dari perjanjian ini.
    </p>
    <p>
        3. PARA PIHAK wajib memastikan bahwa informasi dan dokumen
        yang diberikan adalah benar, lengkap, dan dapat
        dipertanggungjawabkan.
    </p>
    <p>
        4. Apabila terdapat perubahan data mitra usaha, identitas PIC,
        alamat, dokumen perpajakan, atau informasi lain yang berkaitan
        dengan perjanjian, PIHAK PERTAMA wajib memberitahukan
        perubahan tersebut kepada PIHAK KEDUA.
    </p>
    <p>
        5. Penyimpanan dan penggunaan dokumen dilakukan untuk
        kepentingan administrasi, pelaksanaan perjanjian, dan
        pemenuhan kewajiban sesuai dengan ketentuan yang berlaku.
    </p>

    <h3>PASAL VII</h3>
    <h3>PERUBAHAN DAN ADDENDUM PERJANJIAN</h3>

    <p>
        1. Perubahan terhadap isi perjanjian hanya dapat dilakukan
        berdasarkan kesepakatan tertulis PARA PIHAK.
    </p>
    <p>
        2. Setiap perubahan yang berkaitan dengan objek sewa, nilai
        sewa, jangka waktu, jenis usaha, atau ketentuan lainnya
        dituangkan dalam addendum atau dokumen perubahan yang
        disetujui PARA PIHAK.
    </p>
    <p>
        3. Addendum yang telah ditandatangani oleh PARA PIHAK menjadi
        bagian yang tidak terpisahkan dari perjanjian ini.
    </p>
    <p>
        4. Selama perubahan belum disepakati secara tertulis,
        ketentuan dalam perjanjian yang berlaku tetap menjadi acuan
        pelaksanaan hak dan kewajiban PARA PIHAK.
    </p>

    <h3>PASAL VIII</h3>
    <h3>PENGAKHIRAN PERJANJIAN</h3>

    <p>
        1. Perjanjian ini berakhir setelah jangka waktu sebagaimana
        dimaksud dalam Pasal II berakhir, kecuali PARA PIHAK
        menyepakati perpanjangan secara tertulis.
    </p>
    <p>
        2. Perjanjian dapat diakhiri sebelum berakhirnya jangka waktu
        berdasarkan kesepakatan tertulis PARA PIHAK atau alasan lain
        yang diperbolehkan berdasarkan perjanjian dan ketentuan yang
        berlaku.
    </p>
    <p>
        3. Apabila salah satu pihak tidak memenuhi kewajibannya,
        pihak lainnya dapat menyampaikan pemberitahuan tertulis dan
        meminta penyelesaian sesuai dengan mekanisme yang disepakati.
    </p>
    <p>
        4. Pada saat perjanjian berakhir, PIHAK PERTAMA wajib
        menyelesaikan kewajiban pembayaran yang masih tertunggak
        serta melaksanakan pengembalian area sesuai dengan ketentuan
        yang disepakati.
    </p>
    <p>
        5. Pengakhiran perjanjian tidak menghapus kewajiban yang telah
        timbul sebelum tanggal pengakhiran.
    </p>
    <p>
        6. Tata cara pengakhiran, pengosongan area, dan penyelesaian
        kewajiban dilaksanakan dengan memperhatikan ketentuan yang
        berlaku dan kesepakatan PARA PIHAK.
    </p>

    <h3>PASAL IX</h3>
    <h3>KEADAAN KAHAR (FORCE MAJEURE)</h3>

    <p>
        1. Keadaan kahar adalah peristiwa di luar kendali wajar PARA
        PIHAK yang secara langsung menghambat pelaksanaan sebagian
        atau seluruh kewajiban berdasarkan perjanjian.
    </p>
    <p>
        2. Keadaan kahar dapat meliputi bencana alam, kebakaran besar,
        perang, kerusuhan, wabah, kebijakan pemerintah, atau peristiwa
        lain yang memenuhi kriteria keadaan kahar sesuai dengan
        ketentuan yang berlaku.
    </p>
    <p>
        3. Pihak yang mengalami keadaan kahar wajib memberitahukan
        secara tertulis kepada pihak lainnya dalam waktu yang wajar
        dengan menyertakan informasi dan bukti pendukung yang tersedia.
    </p>
    <p>
        4. PARA PIHAK wajib melakukan koordinasi untuk menentukan
        langkah penyelesaian dan penyesuaian kewajiban yang terdampak.
    </p>
    <p>
        5. Pelaksanaan ketentuan mengenai keadaan kahar tidak secara
        otomatis menghapus kewajiban pembayaran atau kewajiban lain
        yang telah jatuh tempo, kecuali disepakati lain secara
        tertulis atau ditentukan berdasarkan ketentuan yang berlaku.
    </p>

    <h3>PASAL X</h3>
    <h3>PENYELESAIAN PERSELISIHAN</h3>

    <p>
        1. Apabila terjadi perselisihan dalam pelaksanaan perjanjian,
        PARA PIHAK sepakat untuk mengutamakan penyelesaian melalui
        musyawarah untuk mencapai mufakat.
    </p>
    <p>
        2. Apabila musyawarah tidak menghasilkan kesepakatan, PARA
        PIHAK akan menyelesaikan perselisihan melalui mekanisme hukum
        yang berlaku sesuai dengan ketentuan dan kesepakatan yang sah.
    </p>
    <p>
        3. Selama proses penyelesaian perselisihan berlangsung, PARA
        PIHAK tetap melaksanakan kewajiban yang tidak menjadi objek
        perselisihan sepanjang memungkinkan dan sesuai dengan
        ketentuan yang berlaku.
    </p>

    <h3>PASAL XI</h3>
    <h3>KETENTUAN PENUTUP</h3>

    <p>
        1. Perjanjian ini dibuat berdasarkan kesepakatan PARA PIHAK
        tanpa adanya paksaan dari pihak mana pun.
    </p>
    <p>
        2. PARA PIHAK menyatakan telah membaca, memahami, dan
        menyetujui seluruh ketentuan yang tercantum dalam perjanjian ini.
    </p>
    <p>
        3. Hal-hal yang belum diatur dalam perjanjian ini akan
        diselesaikan berdasarkan kesepakatan tertulis PARA PIHAK
        dengan tetap memperhatikan ketentuan peraturan
        perundang-undangan yang berlaku.
    </p>
    <p>
        4. Perjanjian ini mulai berlaku sejak tanggal ditandatangani
        oleh PARA PIHAK, kecuali ditentukan lain secara tertulis.
    </p>
    <p>
        5. Perjanjian dibuat dalam rangkap yang diperlukan, dan setiap
        rangkap memiliki kekuatan hukum yang sama sepanjang memenuhi
        ketentuan yang berlaku.
    </p>

    <p>
        Demikian Surat Perjanjian Kerja Sama dan Sewa Tenant ini
        dibuat untuk dipahami, disetujui, dan dilaksanakan dengan
        penuh tanggung jawab oleh PARA PIHAK.
    </p>

    <p style="text-align:right; margin-top:30px;">
        Sidoarjo, [Tanggal Penandatanganan]
    </p>

    <div class="signature-grid">
        <div>
            <strong>PIHAK PERTAMA</strong>
            <p><strong>MITRA USAHA / PENYEWA</strong></p>
            <div class="signature-space"></div>
            <p>Meterai sesuai ketentuan yang berlaku</p>
            <p>Tanda Tangan: ____________________</p>
            <strong>{{ $namaPic }}</strong>
            <p>{{ $jabatanPic }}</p>
            <p>{{ $mitra }}</p>
        </div>

        <div>
            <strong>PIHAK KEDUA</strong>
            <p><strong>PIHAK KOMERSIL / PENGELOLA BANDARA</strong></p>
            <div class="signature-space"></div>
            <p>Tanda Tangan: ____________________</p>
            <strong>{{ $kerjaSama?->pic_commercial ?: '[Nama PIC Komersil]' }}</strong>
            <p>{{ $kontrak?->jabatan_commercial ?: '[Jabatan PIC Komersil]' }}</p>
            <p>PT Angkasa Pura Indonesia (InJourney Airports)</p>
        </div>
    </div>
</article>

    
@if($isPreview)
    <div class="check-panel">
        <p class="font-semibold text-[#365F65]">
            Mode Pratinjau Dokumen
        </p>

        <p class="mt-2 text-sm leading-6 text-[#7A9290]">
            Halaman ini hanya digunakan untuk memeriksa tampilan dan
            data dokumen. Kontrak resmi belum dibuat. Tahap ACC hanya
            dapat dilakukan setelah proses BA dan Aktivasi diverifikasi.
        </p>

        <div class="check-actions">
            <a href="{{ route('kontrak') }}" class="btn btn-back">
                Kembali ke Data Kontrak
            </a>

            <span class="check-help">
                Pratinjau tidak dapat dilanjutkan ke ACC.
            </span>
        </div>
    </div>
@else
    <form class="check-panel"
          method="GET"
          action="{{ route('kontrak.acc', $kontrak->id) }}">
        <label class="check-label">
            <input
                type="checkbox"
                name="dokumen_diperiksa"
                value="1"
                required
            >
            <span>
                Saya telah membaca dan memeriksa seluruh isi dokumen serta
                memastikan data yang ditampilkan sesuai dengan data kerja sama.
            </span>
        </label>

        <div class="check-actions">
            <span class="check-help">
                Checklist wajib dicentang sebelum melanjutkan.
            </span>

            <button type="submit" class="btn btn-next">
                Lanjut ke Tahap ACC
            </button>
        </div>
    </form>
@endif
</div>
@endsection
