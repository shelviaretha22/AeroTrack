@php
    $nomorDokumen = $kerjaSama->nomor_kerja_sama
        ?: 'KS-' . str_pad($kerjaSama->id, 3, '0', STR_PAD_LEFT);

    $titik = '................................';

    // Tanggal selalu memakai nama bulan Indonesia.
    $formatTanggal = function ($nilai) use ($titik) {
        return $nilai
            ? \Carbon\Carbon::parse($nilai)->locale('id')->translatedFormat('d F Y')
            : $titik;
    };

    $tanggal = $formatTanggal($kerjaSama->tanggal_pengajuan);

    // Data PT Angkasa Pura Indonesia dari Master Perusahaan (jika sudah diisi).
    $perusahaan = $perusahaan ?? null;

    $formatNilai = function ($nilai) {
        return $nilai !== null && $nilai !== ''
            ? number_format((float) $nilai, 2, ',', '.')
            : '................................';
    };
@endphp

<div style="font-family: Arial, sans-serif; font-size: 11pt; color: #000; line-height: 1.5;">

    {{-- JUDUL DOKUMEN --}}
    <div style="text-align: center;">
        <p><strong>CONTOH FORMULIR III</strong></p>

        <h2>
            BERITA ACARA KESEPAKATAN<br>
            KERJA SAMA PEMANFAATAN FASILITAS KOMERSIAL<br>
            UNTUK KEGIATAN USAHA {{ strtoupper($kerjaSama->jenis_usaha ?? '...............................') }}<br>
            DI ........................................
        </h2>

        <p>
            <strong>Nomor: BA. ................................</strong>
        </p>
    </div>

    <br>

    {{-- I. UMUM --}}
    <h3>I. UMUM</h3>

    <p style="text-align: justify;">
        Pada hari ini ................................, tanggal
        ................................, bulan ................................,
        tahun ................................ (....-....-........),
        telah dilakukan proses pemilihan mitra melalui
        [negosiasi langsung / seleksi penunjukan langsung / seleksi terbatas /
        seleksi terbuka]* yang menghasilkan kesepakatan antara
        PT Angkasa Pura Indonesia dengan Mitra untuk kegiatan usaha
        {{ $kerjaSama->jenis_usaha ?? '................................' }}
        di {{ $lokasi->lokasi ?? '................................' }}.
        Selanjutnya dokumen ini akan menjadi bagian yang tidak terpisahkan
        dengan Syarat-syarat Umum Perjanjian sebagai satu kesatuan,
        dengan identitas para pihak sebagai berikut:
    </p>

    <p><strong>1. PT ANGKASA PURA INDONESIA</strong></p>

    <table style="width: 100%;">
        <tr>
            <td style="width: 35%;">Nama Penanggung Jawab</td>
            <td>: {{ $perusahaan->nama_penandatangan ?? $titik }}</td>
        </tr>
        <tr>
            <td>Jabatan Penanggung Jawab</td>
            <td>: {{ $perusahaan->jabatan_penandatangan ?? $titik }}</td>
        </tr>
        <tr>
            <td>Alamat/Domisili Perusahaan</td>
            <td>: {{ $perusahaan->alamat ?? $titik }}</td>
        </tr>
        <tr>
            <td>No. Telepon/Fax</td>
            <td>: {{ trim(($perusahaan->telepon ?? '') . (!empty($perusahaan->fax) ? ' / ' . $perusahaan->fax : '')) ?: $titik }}</td>
        </tr>
        <tr>
            <td>E-mail</td>
            <td>: {{ $perusahaan->email ?? $titik }}</td>
        </tr>
    </table>

    <br>

    <p><strong>2. PT/CV {{ $tenant->mitra_usaha ?? $kerjaSama->mitra_usaha ?? '................................' }}</strong></p>

    <table style="width: 100%;">
        <tr>
            <td style="width: 35%;">Nama Penanggung Jawab</td>
            <td>: {{ $tenant->nama_pic ?? '................................' }}</td>
        </tr>
        <tr>
            <td>Jabatan Penanggung Jawab</td>
            <td>: {{ $tenant->jabatan_pic ?? '................................' }}</td>
        </tr>
        <tr>
            <td>Alamat/Domisili Perusahaan</td>
            <td>: {{ $tenant->alamat_perusahaan ?? '................................' }}</td>
        </tr>
        <tr>
            <td>No. Telepon/Fax</td>
            <td>: {{ $tenant->no_hp_pic ?? '................................' }}</td>
        </tr>
        <tr>
            <td>E-mail</td>
            <td>: {{ $tenant->email_pic ?? '................................' }}</td>
        </tr>
    </table>

    <br>

    {{-- II. DASAR --}}
    <h3>II. DASAR</h3>

    <ol>
        <li>Pengumuman Peluang Kegiatan Usaha Nomor: ................................</li>
        <li>Surat Undangan Negosiasi Langsung/Seleksi Penunjukan Langsung/Seleksi Terbatas Nomor: ................................</li>
        <li>Surat Peminatan/Permohonan Perpanjangan PT/CV {{ $tenant->mitra_usaha ?? $kerjaSama->mitra_usaha ?? '................................' }} Nomor: ................................</li>
    </ol>

    {{-- III. OBJEK KERJA SAMA --}}
    <h3>III. OBJEK KERJA SAMA</h3>

    <table border="1" cellspacing="0" cellpadding="6" style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="text-align: center;">
                <th style="width: 8%;">No.</th>
                <th style="width: 20%;">Kode Ruang</th>
                <th style="width: 32%;">Lokasi</th>
                <th style="width: 20%;">Luas (m²)</th>
                <th style="width: 20%;">Sales Right</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="text-align: center;">1.</td>
                <td>{{ $lokasi->kode_tampilan ?? '................................' }}</td>
                <td>{{ $lokasi->lokasi ?? '................................' }}</td>
                <td style="text-align: center;">{{ $formatNilai($lokasi->luas_area ?? null) }}</td>
                <td>................................</td>
            </tr>
        </tbody>
    </table>

    <br>

    {{-- IV. HASIL KESEPAKATAN --}}
    <h3>IV. HASIL KESEPAKATAN</h3>

    <ol>
        <li>
            Bahwa telah dilaksanakan pembukaan dan penilaian persyaratan
            administrasi dengan hasil sebagaimana terlampir.
        </li>

        <li>
            Bentuk kerja sama adalah
            <strong>{{ $kerjaSama->bentuk_kerja_sama ?? '................................' }}</strong>.
        </li>

        <li>
            Brand dan detail produk:
            <table style="width: 100%;">
                <tr>
                    <td style="width: 25%;">Brand</td>
                    <td>: {{ $tenant->nama_tenant ?? $kerjaSama->brand ?? '................................' }}</td>
                </tr>
                <tr>
                    <td>Detail Produk</td>
                    <td>: ................................................</td>
                </tr>
            </table>
        </li>

        <li>
            Jangka waktu kerja sama terhitung sejak tanggal
            <strong>{{ $formatTanggal($kerjaSama->tanggal_mulai ?? null) }}</strong>
            sampai dengan
            <strong>{{ $formatTanggal($kerjaSama->tanggal_berakhir ?? null) }}</strong>.
            Dalam hal Mitra bermaksud memperpanjang kerja sama, maka wajib
            memberitahukan maksud tersebut kepada PT Angkasa Pura Indonesia
            secara tertulis selambat-lambatnya 3 (tiga) bulan sebelum
            kerja sama berakhir.
        </li>

        <li>
            Aspek Komersial (dapat disesuaikan sesuai kesepakatan):

            <table style="width: 100%;">
                <tr>
                    <td style="width: 35%; vertical-align: top;">
                        Biaya Sewa/Pemanfaatan
                    </td>
                    <td>
                        a. Tarif Sewa per m²/bulan = Rp ................................<br>
                        b. Cara Pembayaran ................................
                    </td>
                </tr>
                <tr>
                    <td style="vertical-align: top;">
                        Imbalan Revenue Share
                    </td>
                    <td>
                        a. ........... % Revenue Share<br>
                        b. Minimum Omzet Bruto (MOB)<br>
                        c. Cara Pembayaran ................................
                    </td>
                </tr>
                <tr>
                    <td style="vertical-align: top;">
                        Biaya Pemakaian Utilitas
                    </td>
                    <td>
                        a. Listrik (Prabayar/Pascabayar)<br>
                        b. Air (Prabayar/Pascabayar)<br>
                        c. Utilitas lainnya<br>
                        Dikenakan biaya sesuai dengan ketentuan yang berlaku.
                    </td>
                </tr>
                <tr>
                    <td style="vertical-align: top;">
                        Security Deposit/Jaminan Pelaksanaan
                    </td>
                    <td>
                        a. Dalam bentuk: Tunai/Rekening Deposit/Invoice Financing.<br>
                        b. Nilai/Saldo Minimal Security Deposit/Jaminan Pelaksanaan adalah ................................<br>
                        c. Wajib menyampaikan Security Deposit/Jaminan Pelaksanaan
                        Tunai/Rekening Deposit/Invoice Financing dan surat kuasa
                        pendebetan Rekening Deposit kepada PT Angkasa Pura Indonesia
                        selambat-lambatnya ................................ sejak
                        tanggal BAK ini ditandatangani.
                    </td>
                </tr>
                <tr>
                    <td>Surcharge</td>
                    <td>: ................................................</td>
                </tr>
            </table>

            <p>
                Revenue Share dibayarkan kepada PT Angkasa Pura Indonesia
                setiap bulan berdasarkan realisasi omzet atau MOB,
                mana yang lebih tinggi.
            </p>
        </li>

        <li>
            Tarif-tarif yang dikenakan belum termasuk pajak sesuai
            ketentuan yang berlaku.
        </li>

        <li>
            Biaya perawatan, pembongkaran dan pajak yang timbul akibat
            pelaksanaan kegiatan usaha sepenuhnya menjadi beban dan
            tanggung jawab Mitra.
        </li>

        <li>
            PT/CV {{ $tenant->mitra_usaha ?? $kerjaSama->mitra_usaha ?? '................................' }}
            tidak diperbolehkan mengalihkan izin usaha ini kepada pihak lain
            tanpa persetujuan PT Angkasa Pura Indonesia.
        </li>

        <li>
            PT/CV {{ $tenant->mitra_usaha ?? $kerjaSama->mitra_usaha ?? '................................' }}
            berkomitmen untuk memenuhi segala kewajiban yang telah disepakati
            dalam Berita Acara ini.
        </li>

        <li>
            PT/CV {{ $tenant->mitra_usaha ?? $kerjaSama->mitra_usaha ?? '................................' }}
            bersedia untuk mengikuti seluruh ketentuan yang berlaku
            di PT Angkasa Pura Indonesia.
        </li>

        <li>
            PT Angkasa Pura Indonesia berhak melakukan pengaturan,
            pemindahan atau pembatalan/pengakhiran penyewaan atau hal-hal
            lain yang dianggap perlu untuk kepentingan operasional atau
            komersial dalam rangka penyelenggaraan Bandar Udara.
        </li>

        <li>
            PT Angkasa Pura Indonesia dapat melakukan penundaan pelaksanaan
            kerja sama apabila terdapat indikasi adanya penyimpangan dan/atau
            kecurangan dalam transaksi bisnis yang menyebabkan kerugian
            bagi PT Angkasa Pura Indonesia.
        </li>

        <li>
            BAK ini dapat digunakan sebagai dasar penagihan kewajiban Mitra.
        </li>

        <li>
            Apabila BAK tidak ditandatangani/tidak dikembalikan oleh Mitra
            dalam waktu 14 (empat belas) hari kalender sejak dikirimkan
            oleh PT Angkasa Pura Indonesia, maka kesepakatan dapat dibatalkan.
        </li>

        <li>
            Hal-hal yang tidak diatur dalam Berita Acara Kesepakatan ini,
            dilaksanakan oleh Mitra sesuai ketentuan Syarat-syarat Umum
            Kerja Sama (terlampir) yang merupakan bagian tidak terpisahkan
            dari Berita Acara ini.
        </li>
    </ol>

    {{-- V. PENUTUP --}}
    <h3>V. PENUTUP</h3>

    <ol>
        <li>
            Dalam rangka mewujudkan Tata Kelola Perusahaan Yang Baik
            (Good Corporate Governance), dalam proses dan/atau pelaksanaan
            kerja sama, PARA PIHAK sepakat untuk tidak:
            <ol type="a">
                <li>Menerima dan/atau memberikan gratifikasi; dan</li>
                <li>Melakukan Korupsi, Kolusi dan Nepotisme (KKN).</li>
            </ol>
        </li>

        <li>
            Demikian Berita Acara ini dibuat dalam 2 (dua) rangkap dan
            setiap rangkap dibubuhi meterai yang cukup untuk dipergunakan
            sebagaimana mestinya.
        </li>
    </ol>

    <br><br>

    {{-- TANDA TANGAN --}}
    <p style="text-align: right;">
        ................................, {{ $tanggal }}
    </p>

    <table style="width: 100%; text-align: center;">
        <tr>
            <td style="width: 50%;">
                <strong>PT/CV {{ $tenant->mitra_usaha ?? $kerjaSama->mitra_usaha ?? '................................' }}</strong>
            </td>
            <td style="width: 50%;">
                <strong>PT ANGKASA PURA INDONESIA</strong>
            </td>
        </tr>
        <tr>
            <td style="height: 100px;"></td>
            <td></td>
        </tr>
        <tr>
            <td>
                <strong><u>{{ $tenant->nama_pic ?? '(Nama)' }}</u></strong><br>
                {{ $tenant->jabatan_pic ?? '(Jabatan)' }}
            </td>
            <td>
                <strong><u>(Nama)</u></strong><br>
                [Pejabat Berwenang Penandatangan]<br>
                Bandar Udara ................................
            </td>
        </tr>
    </table>

    <br>

    <p>
        <em>*Formulir dapat menyesuaikan dengan pola kerja sama.</em>
    </p>

    <hr>

    {{-- LAMPIRAN PERSYARATAN ADMINISTRASI --}}
    <div style="page-break-before: always;"></div>

    <h3 style="text-align: center;">
        LAMPIRAN BERITA ACARA NEGOSIASI LANGSUNG<br>
        (KHUSUS PEMILIHAN MITRA DENGAN METODE NEGOSIASI LANGSUNG)
    </h3>

    <p>
        Nomor : ................................<br>
        Tanggal : {{ $tanggal }}
    </p>

    <h3 style="text-align: center;">PERSYARATAN ADMINISTRASI*</h3>

    <table border="1" cellspacing="0" cellpadding="6" style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="text-align: center;">
                <th style="width: 65%;">Syarat Administrasi</th>
                <th style="width: 17%;">Ada</th>
                <th style="width: 18%;">Tidak</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>a. Akta Pendirian Perusahaan berikut perubahannya berbentuk badan hukum atau badan usaha yang telah disahkan sesuai ketentuan yang berlaku.</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>b. Nomor Induk Berusaha (NIB) dan/atau izin usaha yang masih berlaku sesuai dengan bidang usaha.</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>c. Nomor Pokok Wajib Pajak (NPWP) Perusahaan.</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>d. Surat Pengukuhan Pengusaha Kena Pajak (SPPKP) sesuai ketentuan perpajakan yang berlaku.</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>e. Bukti Setor Pajak Tahunan (SPT) terakhir dan/atau Surat Pemberitahuan Pajak Terutang (SPPT).</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>f. Kartu Tanda Penduduk (KTP) penanggung jawab.</td>
                <td></td>
                <td></td>
            </tr>
        </tbody>
    </table>

    <p><em>*Persyaratan dapat disesuaikan.</em></p>

</div>