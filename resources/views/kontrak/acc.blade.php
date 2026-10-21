
@extends('layouts.app')

@section('page-title', 'ACC Dokumen Kontrak')

@section('content')

<div class="max-w-6xl space-y-6">

    {{-- HEADER --}}
    <div>
        <p class="text-sm text-[#8BA5A2]">
            Management / Kontrak / ACC Dokumen Kontrak
        </p>

        <h2 class="mt-1 text-2xl font-bold text-[#365F65]">
            ACC Dokumen Kontrak
        </h2>

        <p class="mt-1 text-sm text-[#7A9290]">
            Periksa dokumen kontrak dan tentukan metode penandatanganan.
        </p>
    </div>

    {{-- PROGRESS --}}
    <div class="rounded-2xl border border-[#E2EBE9] bg-white p-5 sm:p-6">
        <div class="flex items-center justify-between gap-2">

            <div class="flex flex-1 flex-col items-center text-center">
                <div class="flex h-10 w-10 items-center justify-center rounded-full
                            bg-[#DCE6E4] text-sm font-bold text-[#365F65]">
                    1
                </div>
                <p class="mt-2 text-xs font-semibold text-[#365F65]">
                    Dokumen Kontrak
                </p>
            </div>

            <div class="h-0.5 flex-1 bg-[#16877F]"></div>

            <div class="flex flex-1 flex-col items-center text-center">
                <div class="flex h-10 w-10 items-center justify-center rounded-full
                            bg-[#16877F] text-sm font-bold text-white">
                    2
                </div>
                <p class="mt-2 text-xs font-bold text-[#16877F]">
                    ACC Kontrak
                </p>
            </div>

            <div class="h-0.5 flex-1 bg-[#DCE6E4]"></div>

            <div class="flex flex-1 flex-col items-center text-center">
                <div class="flex h-10 w-10 items-center justify-center rounded-full
                            bg-[#F1F5F4] text-sm font-bold text-[#8BA5A2]">
                    3
                </div>
                <p class="mt-2 text-xs font-semibold text-[#8BA5A2]">
                    Upload Kontrak Final
                </p>
            </div>

        </div>
    </div>

    {{-- PESAN SESSION --}}
    @if (session('success'))
        <div class="rounded-xl border border-green-200 bg-green-50 p-4 text-sm text-green-800">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            <p class="mb-2 font-semibold">Periksa kembali data berikut:</p>
            <ul class="list-inside list-disc space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- FORM ACC --}}
    <form
        action="{{ route('kontrak.storeAcc', $kontrak->id) }}"
        method="POST"
        enctype="multipart/form-data"
        id="formAccKontrak"
    >
        @csrf

    <div class="grid grid-cols-1 items-start gap-5 xl:grid-cols-[minmax(0,1.15fr)_minmax(360px,0.9fr)]">

        {{-- KOLOM KIRI: PREVIEW PDF --}}
        <section class="min-w-0 rounded-xl border border-[#E2EBE9] bg-white p-4">
            <h3 class="text-sm font-bold text-[#365F65]">
                Pratinjau Dokumen Kontrak
            </h3>

            <p class="mt-1 text-xs text-[#8BA5A2]">
                Pratinjau akan diperbarui mengikuti tanda tangan yang dipilih.
            </p>

            
<div class="mt-4 overflow-hidden rounded-lg border border-[#DCE6E4] bg-[#F1F5F4]">
    <iframe
        id="previewFrame"
        title="Pratinjau PDF Kontrak"
        class="block w-full bg-white"
        style="height: 700px;"
    ></iframe>

    
    <div
        id="previewLoading"
        class="hidden p-4 text-center text-sm text-[#527D82]"
    >
        Membuat pratinjau PDF...
    </div>
</div>

<p id="pesanPreview" class="mt-3 text-xs text-[#8BA5A2]" role="status">
    Pilih metode tanda tangan untuk membuat pratinjau PDF.
</p>

</section>

        {{-- KOLOM KANAN: PANEL PENANDATANGANAN --}}
        <section class="min-w-0 space-y-5">

        {{-- IDENTITAS COMMERCIAL --}}
        <div class="rounded-2xl border border-[#E2EBE9] bg-white p-6 sm:p-8">

            <div class="mb-6">
                <h3 class="text-base font-bold text-[#365F65]">
                    Informasi Commercial
                </h3>
                <p class="mt-1 text-xs leading-5 text-[#8BA5A2]">
                    Identitas diambil otomatis dari data Kerja Sama.
                </p>
            </div>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">

                <div>
                    <label for="nama_commercial"
                        class="mb-1.5 block text-sm font-semibold text-[#527D82]">
                        Nama Commercial
                    </label>

                    <input
                        type="text"
                        id="nama_commercial"
                        name="nama_commercial"
                        value="{{ $kontrak->kerjaSama?->pic_commercial ?? $kontrak->nama_commercial }}"
                        readonly
                        class="w-full rounded-xl border border-[#DCE6E4]
                               bg-[#F1F5F4] px-4 py-3 text-sm text-[#365F65]"
                    >
                </div>

                <div>
                    <label for="jabatan_commercial"
                        class="mb-1.5 block text-sm font-semibold text-[#527D82]">
                        Jabatan Commercial
                    </label>

                    <input
                        type="text"
                        id="jabatan_commercial"
                        name="jabatan_commercial"
                        value="{{ $kontrak->kerjaSama?->jabatan_pic_commercial ?? $kontrak->jabatan_commercial }}"
                        readonly
                        class="w-full rounded-xl border border-[#DCE6E4]
                               bg-[#F1F5F4] px-4 py-3 text-sm text-[#365F65]"
                    >
                </div>

            </div>

            {{-- METODE TANDA TANGAN --}}
            <div class="mt-8">
                <h4 class="mb-3 text-sm font-bold text-[#365F65]">
                    Metode Penandatanganan
                </h4>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">

                    <label class="flex cursor-pointer items-start gap-3 rounded-xl
                                  border border-[#DCE6E4] bg-[#F8FAF9] p-4
                                  transition hover:border-[#16877F]">
                        <input
                            type="radio"
                            name="ttd_method"
                            id="metodeOnline"
                            value="online"
                            {{ old('ttd_method', 'online') === 'online' ? 'checked' : '' }}
                            class="mt-1 accent-[#16877F]"
                        >
                        <span>
                            <span class="block text-sm font-semibold text-[#365F65]">
                                Tanda Tangan Online
                            </span>
                            <span class="mt-1 block text-xs leading-5 text-[#8BA5A2]">
                                Gambar tanda tangan langsung atau unggah JPG/JPEG.
                            </span>
                        </span>
                    </label>

                    <label class="flex cursor-pointer items-start gap-3 rounded-xl
                                  border border-[#DCE6E4] bg-[#F8FAF9] p-4
                                  transition hover:border-[#16877F]">
                        <input
                            type="radio"
                            name="ttd_method"
                            id="metodeOffline"
                            value="offline"
                            {{ old('ttd_method') === 'offline' ? 'checked' : '' }}
                            class="mt-1 accent-[#16877F]"
                        >
                        <span>
                            <span class="block text-sm font-semibold text-[#365F65]">
                                Tanda Tangan Offline
                            </span>
                            <span class="mt-1 block text-xs leading-5 text-[#8BA5A2]">
                                Lanjut tanpa mengunggah tanda tangan digital.
                            </span>
                        </span>
                    </label>

                </div>
            </div>

            {{-- AREA TANDA TANGAN ONLINE --}}
            <div id="areaTandaTangan" class="mt-6">

                <h4 class="mb-3 text-sm font-bold text-[#365F65]">
                    Tanda Tangan Commercial
                </h4>

                <div class="mb-4 flex flex-wrap gap-2">
                    <button
                        type="button"
                        id="pilihGambar"
                        aria-pressed="true"
                        class="rounded-lg bg-[#16877F] px-4 py-2.5
                               text-xs font-semibold text-white"
                    >
                        Gambar Tanda Tangan
                    </button>

                    <button
                        type="button"
                        id="pilihUpload"
                        aria-pressed="false"
                        class="rounded-lg border border-[#DCE6E4] bg-white
                               px-4 py-2.5 text-xs font-semibold text-[#527D82]"
                    >
                        Upload JPG/JPEG
                    </button>
                </div>

                {{-- GAMBAR TTD --}}
                <div id="panelGambar">
                    <div class="rounded-xl border-2 border-dashed
                                border-[#CFE0DC] bg-[#F8FAF9] p-3">

                        <canvas
                            id="signatureCanvas"
                            width="700"
                            height="220"
                            class="block w-full rounded-lg bg-white"
                            style="height:180px; touch-action:none; cursor:crosshair;"
                            aria-label="Area menggambar tanda tangan"
                        ></canvas>

                        <input
                            type="hidden"
                            name="signature_data"
                            id="signatureData"
                        >

                        <div class="mt-3 flex flex-wrap items-center justify-between gap-3">
                            <p class="text-xs text-[#8BA5A2]">
                                Gambar menggunakan mouse atau layar sentuh.
                            </p>

                            <button
                                type="button"
                                id="hapusTandaTangan"
                                class="rounded-lg border border-[#DCE6E4] bg-white
                                       px-4 py-2 text-xs font-semibold text-[#527D82]"
                            >
                                Hapus Tanda Tangan
                            </button>
                        </div>
                    </div>
                </div>

                {{-- UPLOAD TTD --}}
                <div id="panelUpload" hidden>
                    <label
                        for="signature"
                        class="flex min-h-[130px] cursor-pointer flex-col
                               items-center justify-center rounded-xl
                               border-2 border-dashed border-[#CFE0DC]
                               bg-[#F8FAF9] p-6 text-center"
                    >
                        <span class="text-sm font-semibold text-[#527D82]">
                            Pilih gambar tanda tangan
                        </span>
                        <span class="mt-1 text-xs text-[#8BA5A2]">
                            Format JPG/JPEG, maksimal 2 MB
                        </span>
                    </label>

                    <input
                        type="file"
                        id="signature"
                        name="signature"
                        accept=".jpg,.jpeg,image/jpeg"
                        class="mt-3 block w-full text-sm text-[#527D82]"
                    >

                    <div id="signatureFileName"
                        class="mt-2 text-xs text-[#16877F]"></div>
                </div>
            </div>

            {{-- INFORMASI OFFLINE --}}
            <div
                id="infoOffline"
                hidden
                class="mt-6 rounded-xl border border-[#E7D9B5] bg-[#FFF9E9] p-4"
            >
                <p class="text-sm font-semibold text-[#725C2E]">
                    Tanda Tangan Offline
                </p>
                <p class="mt-1 text-xs leading-5 text-[#725C2E]">
                    Kamu dapat melanjutkan tanpa tanda tangan digital.
                    Dokumen final yang ditandatangani kedua pihak akan
                    ditangani pada tahap berikutnya.
                </p>
            </div>

                    </div>
        </section>
    </div>

    {{-- TOMBOL AKSI --}}
        <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">

            <a
                href="{{ route('kontrak.show', $kontrak->id) }}"
                class="inline-flex items-center justify-center rounded-xl
                       border border-[#DCE6E4] bg-white px-5 py-3
                       text-sm font-semibold text-[#527D82]
                       transition hover:bg-[#F5F8F7]"
            >
                Kembali
            </a>

            <button
                type="button"
                id="btnPreviewPdf"
                class="inline-flex items-center justify-center rounded-xl
                    border border-[#16877F] bg-white px-5 py-3
                    text-sm font-semibold text-[#16877F]
                    transition hover:bg-[#F1F8F7]"
            >
                Pratinjau PDF
            </button>

            <button
                type="submit"
                id="btnSubmitAcc"
                class="inline-flex items-center justify-center rounded-xl
                       bg-[#365F65] px-5 py-3 text-sm font-semibold text-white
                       transition hover:bg-[#294F55]"
            >
                ACC Kontrak Online
            </button>

        </div>

    </form>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('formAccKontrak');
    const canvas = document.getElementById('signatureCanvas');

    if (!form || !canvas) return;

    const ctx = canvas.getContext('2d');

    const metodeOnline = document.getElementById('metodeOnline');
    const metodeOffline = document.getElementById('metodeOffline');

    const areaTandaTangan = document.getElementById('areaTandaTangan');
    const infoOffline = document.getElementById('infoOffline');

    const panelGambar = document.getElementById('panelGambar');
    const panelUpload = document.getElementById('panelUpload');

    const pilihGambar = document.getElementById('pilihGambar');
    const pilihUpload = document.getElementById('pilihUpload');
    const hapusTandaTangan = document.getElementById('hapusTandaTangan');

    const signatureData = document.getElementById('signatureData');
    const signatureFile = document.getElementById('signature');
    const signatureFileName = document.getElementById('signatureFileName');

    const btnSubmitAcc = document.getElementById('btnSubmitAcc');
    const btnPreviewPdf = document.getElementById('btnPreviewPdf');
    const pesanPreview = document.getElementById('pesanPreview');

    let metodeInput = 'gambar';
    let sedangMenggambar = false;
    let sudahMenggambar = false;

    ctx.strokeStyle = '#172F2E';
    ctx.lineWidth = 2.5;
    ctx.lineCap = 'round';
    ctx.lineJoin = 'round';

    function onlineDipilih() {
        return metodeOnline.checked;
    }

    function fileTersedia() {
        return signatureFile.files.length > 0;
    }

    function tandaTanganSiap() {
        if (!onlineDipilih()) return false;

        if (metodeInput === 'gambar') {
            return sudahMenggambar && signatureData.value !== '';
        }

        return fileTersedia();
    }

    function posisi(event) {
        const rect = canvas.getBoundingClientRect();

        return {
            x: (event.clientX - rect.left) * canvas.width / rect.width,
            y: (event.clientY - rect.top) * canvas.height / rect.height
        };
    }

    function simpanGambarTandaTangan() {
        if (sudahMenggambar) {
            signatureData.value = canvas.toDataURL('image/png');
        } else {
            signatureData.value = '';
        }
    }

    function hapusGambarCanvas() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        signatureData.value = '';
        sudahMenggambar = false;
        sedangMenggambar = false;
    }

    function resetSemuaTandaTangan() {
        hapusGambarCanvas();
        signatureFile.value = '';
        signatureFileName.textContent = '';
    }

    function perbaruiTampilan() {
        const online = onlineDipilih();
        const siap = tandaTanganSiap();

        areaTandaTangan.hidden = !online;
        infoOffline.hidden = online;

        panelGambar.hidden = !online || metodeInput !== 'gambar';
        panelUpload.hidden = !online || metodeInput !== 'upload';

        pilihGambar.setAttribute(
            'aria-pressed',
            String(metodeInput === 'gambar')
        );

        pilihUpload.setAttribute(
            'aria-pressed',
            String(metodeInput === 'upload')
        );

        pilihGambar.className = metodeInput === 'gambar'
            ? 'rounded-lg bg-[#16877F] px-4 py-2.5 text-xs font-semibold text-white'
            : 'rounded-lg border border-[#DCE6E4] bg-white px-4 py-2.5 text-xs font-semibold text-[#527D82]';

        pilihUpload.className = metodeInput === 'upload'
            ? 'rounded-lg bg-[#16877F] px-4 py-2.5 text-xs font-semibold text-white'
            : 'rounded-lg border border-[#DCE6E4] bg-white px-4 py-2.5 text-xs font-semibold text-[#527D82]';

        btnSubmitAcc.textContent = online
            ? 'ACC Kontrak Online'
            : 'ACC Kontrak Offline';

        
        btnPreviewPdf.disabled = online && !siap;

        pesanPreview.textContent = online
            ? (siap
                ? 'Tanda tangan siap. Klik Pratinjau PDF untuk memeriksa dokumen.'
                : 'Tambahkan tanda tangan terlebih dahulu untuk membuat preview.')
            : 'Mode offline dipilih. Klik Pratinjau PDF untuk memeriksa dokumen.';

        signatureFile.required = online && metodeInput === 'upload';

    }

    // Mulai menggambar.
    canvas.addEventListener('pointerdown', function (event) {
        if (!onlineDipilih() || metodeInput !== 'gambar') return;

        event.preventDefault();
        sedangMenggambar = true;
        canvas.setPointerCapture(event.pointerId);

        const p = posisi(event);

        ctx.beginPath();
        ctx.moveTo(p.x, p.y);
        ctx.lineTo(p.x + 0.1, p.y + 0.1);
        ctx.stroke();

        sudahMenggambar = true;
        simpanGambarTandaTangan();
        perbaruiTampilan();
    });

    // Menggambar garis.
    canvas.addEventListener('pointermove', function (event) {
        if (!sedangMenggambar || !onlineDipilih() || metodeInput !== 'gambar') {
            return;
        }

        event.preventDefault();

        const p = posisi(event);
        ctx.lineTo(p.x, p.y);
        ctx.stroke();

        sudahMenggambar = true;
        simpanGambarTandaTangan();
        perbaruiTampilan();
    });

    function berhentiMenggambar() {
        if (!sedangMenggambar) return;

        sedangMenggambar = false;
        simpanGambarTandaTangan();
        perbaruiTampilan();
    }

    canvas.addEventListener('pointerup', berhentiMenggambar);
    canvas.addEventListener('pointercancel', berhentiMenggambar);
    canvas.addEventListener('lostpointercapture', berhentiMenggambar);

    // Hapus TTD.
    hapusTandaTangan.addEventListener('click', function () {
        resetSemuaTandaTangan();
        perbaruiTampilan();
    });

    // Pilih gambar manual.
    pilihGambar.addEventListener('click', function () {
        metodeInput = 'gambar';

        signatureFile.value = '';
        signatureFileName.textContent = '';

        perbaruiTampilan();
    });

    // Pilih upload JPG/JPEG.
    pilihUpload.addEventListener('click', function () {
        metodeInput = 'upload';
        hapusGambarCanvas();

        perbaruiTampilan();
    });

    // Saat file dipilih, cek tipe dan ukuran awal di browser.
    signatureFile.addEventListener('change', function () {
        const file = signatureFile.files[0];

        if (!file) {
            signatureFileName.textContent = '';
            perbaruiTampilan();
            return;
        }

        const namaValid = /\.(jpe?g)$/i.test(file.name);
        const tipeValid = ['image/jpeg', 'image/pjpeg'].includes(file.type);
        const ukuranValid = file.size <= 2 * 1024 * 1024;

        if (!namaValid || !tipeValid || !ukuranValid) {
            alert('Pilih file JPG/JPEG dengan ukuran maksimal 2 MB.');

            signatureFile.value = '';
            signatureFileName.textContent = '';
        } else {
            hapusGambarCanvas();
            signatureFileName.textContent = 'File dipilih: ' + file.name;
        }

        perbaruiTampilan();
    });

    // Ganti metode online/offline.
    metodeOnline.addEventListener('change', function () {
        perbaruiTampilan();
    });

    metodeOffline.addEventListener('change', function () {
        resetSemuaTandaTangan();
        perbaruiTampilan();
    });

    // Validasi sebelum submit.
    form.addEventListener('submit', function (event) {
        if (onlineDipilih() && !tandaTanganSiap()) {
            event.preventDefault();
            alert('Silakan gambar atau unggah tanda tangan terlebih dahulu.');
            return;
        }

        if (!onlineDipilih()) {
            resetSemuaTandaTangan();
        }
    });

        // PREVIEW PDF DI PANEL KIRI
    let previewUrl = null;

    async function buatPreviewPdf() {
        if (onlineDipilih() && !tandaTanganSiap()) {
            alert('Silakan gambar atau unggah tanda tangan terlebih dahulu.');
            return;
        }

        const previewFrame = document.getElementById('previewFrame');
        const previewLoading = document.getElementById('previewLoading');

        btnPreviewPdf.disabled = true;
        pesanPreview.textContent = 'Sedang membuat pratinjau PDF...';
        previewLoading.classList.remove('hidden');

        try {
            const dataForm = new FormData(form);

            const response = await fetch(
                @json(route('kontrak.previewPdf', $kontrak->id)),
                {
                    method: 'POST',
                    body: dataForm,
                    headers: {
                        'Accept': 'application/pdf'
                    },
                    credentials: 'same-origin'
                }
            );

            const contentType = response.headers.get('content-type') || '';

            
if (!response.ok || !contentType.includes('application/pdf')) {
    const isiError = await response.text();

    console.error('Status HTTP:', response.status);
    console.error('Content-Type:', contentType);
    console.error('Respons server:', isiError);

    throw new Error(
        `Server tidak mengembalikan PDF. HTTP ${response.status}, tipe: ${contentType || 'tidak diketahui'}. Buka F12 > Console untuk melihat detail.`
    );
}


            const pdfBlob = await response.blob();

            if (previewUrl) {
                URL.revokeObjectURL(previewUrl);
            }

            previewUrl = URL.createObjectURL(pdfBlob);
            previewFrame.src = previewUrl;

            pesanPreview.textContent =
                'Pratinjau berhasil dibuat. Periksa dokumen sebelum melakukan ACC.';

        } catch (error) {
            console.error('Gagal membuat preview PDF:', error);

            pesanPreview.textContent =
                'Pratinjau gagal dibuat. Periksa Console dan log Laravel.';

            alert(error.message || 'Terjadi kesalahan saat membuat PDF.');

        } finally {
            previewLoading.classList.add('hidden');

            btnPreviewPdf.disabled =
                onlineDipilih() && !tandaTanganSiap();
        }
    }

    btnPreviewPdf.addEventListener('click', buatPreviewPdf);

    perbaruiTampilan();
});
</script>

@endsection
