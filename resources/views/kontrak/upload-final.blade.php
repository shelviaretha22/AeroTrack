
@extends('layouts.app')

@section('page-title', 'Upload Kontrak Final')

@section('content')
<div class="min-h-full space-y-5 bg-[#F4F8FA] p-4 lg:p-6">

    {{-- HEADER DAN PROGRESS --}}
    <div class="mx-auto max-w-[1600px]">

        <div class="mx-auto mb-5 flex max-w-3xl items-center">
            <div class="flex flex-1 flex-col items-center gap-2">
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-[#087F73] text-white">✓</div>
                <span class="text-center text-xs font-medium text-[#365F65] sm:text-sm">Dokumen Kontrak</span>
            </div>

            <div class="mb-6 h-0.5 flex-1 bg-[#087F73]"></div>

            <div class="flex flex-1 flex-col items-center gap-2">
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-[#087F73] text-white">✓</div>
                <span class="text-center text-xs font-medium text-[#365F65] sm:text-sm">ACC Kontrak</span>
            </div>

            <div class="mb-6 h-0.5 flex-1 bg-[#087F73]"></div>

            <div class="flex flex-1 flex-col items-center gap-2">
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-[#087F73] font-semibold text-white">3</div>
                <span class="text-center text-xs font-semibold text-[#087F73] sm:text-sm">Upload Kontrak Final</span>
            </div>
        </div>

        <h1 class="text-2xl font-bold text-[#142344] sm:text-3xl">
            Upload Kontrak Final
        </h1>

        <p class="mt-1 text-sm text-[#60718A] sm:text-base">
            Unggah dokumen kontrak final yang telah ditandatangani oleh kedua pihak.
        </p>
    </div>

    {{-- NOTIFIKASI --}}
    @if (session('success'))
        <div class="mx-auto max-w-[1600px] rounded-xl border border-[#B9DED5] bg-[#EAF7F2] p-4 text-sm text-[#17695F]">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mx-auto max-w-[1600px] rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mx-auto max-w-[1600px] rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            <p class="mb-2 font-semibold">Upload belum dapat diproses:</p>
            <ul class="list-inside list-disc space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- DUA PANEL --}}
    <div class="mx-auto grid max-w-[1600px] grid-cols-1 items-stretch gap-4 xl:grid-cols-2">

        {{-- PANEL KIRI: PREVIEW PDF YANG DIPILIH --}}
        <section class="flex min-h-[620px] flex-col overflow-hidden rounded-xl border border-[#E0EAF0] bg-white shadow-sm xl:h-[calc(100vh-255px)]">

            <div class="border-b border-[#E6EDF2] px-5 py-4">
                <h2 class="text-base font-bold text-[#142344]">
                    Pratinjau Dokumen Kontrak Final
                </h2>

                <p class="mt-1 text-sm leading-5 text-[#60718A]">
                    PDF yang kamu pilih di panel kanan akan ditampilkan di sini.
                </p>
            </div>

            <div class="relative flex-1 bg-[#EAF0F4] p-3 sm:p-4">

                <div
                    id="emptyPreview"
                    class="absolute inset-3 flex flex-col items-center justify-center rounded-lg border border-dashed border-[#C8D7E0] bg-white p-6 text-center sm:inset-4"
                >
                    <svg class="mb-4 h-16 w-16 text-[#9AAEBB]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 3.75h7l5 5v11.5H7a2 2 0 0 1-2-2v-12.5a2 2 0 0 1 2-2Z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14 3.75v5h5M8 13h7M8 16h7"/>
                    </svg>

                    <p class="font-semibold text-[#29405C]">
                        Belum ada PDF untuk ditampilkan
                    </p>

                    <p class="mt-2 max-w-xs text-sm leading-6 text-[#718198]">
                        Pilih dokumen kontrak final melalui panel upload di sebelah kanan untuk melihat pratinjaunya.
                    </p>
                </div>

                <iframe
                    id="uploadedPdfPreview"
                    title="Pratinjau PDF kontrak final yang dipilih"
                    class="relative z-10 hidden h-full min-h-[540px] w-full rounded-md border border-[#D8E2E9] bg-white xl:min-h-0"
                ></iframe>
            </div>

            <div class="border-t border-[#E6EDF2] px-5 py-3">
                <p class="text-xs leading-5 text-[#718198]">
                    Periksa isi PDF sebelum menyimpan. File akan disimpan ke server setelah tombol Simpan Kontrak Final ditekan.
                </p>
            </div>
        </section>

        {{-- PANEL KANAN: UPLOAD --}}
        <section class="flex flex-col rounded-xl border border-[#E0EAF0] bg-white shadow-sm">

            <div class="border-b border-[#E6EDF2] px-5 py-4">
                <h2 class="text-base font-bold text-[#142344]">
                    Unggah Dokumen Kontrak Final
                </h2>

                <p class="mt-1 text-sm leading-5 text-[#60718A]">
                    Dokumen harus memuat tanda tangan kedua pihak, yaitu tenant dan pihak komersil.
                </p>
            </div>

            <form
                id="formUploadFinal"
                action="{{ route('kontrak.storeUploadFinal', $kontrak->id) }}"
                method="POST"
                enctype="multipart/form-data"
                class="flex flex-1 flex-col"
            >
                @csrf

                <div class="flex-1 space-y-4 p-5">

                    {{-- AREA PILIH FILE --}}
                    <label
                        for="final_pdf"
                        id="dropZone"
                        class="flex min-h-[190px] cursor-pointer flex-col items-center justify-center rounded-xl border-2 border-dashed border-[#C7DDEA] bg-[#F8FBFD] px-5 py-7 text-center transition hover:border-[#16877F] hover:bg-[#F0F9F7]"
                    >
                        <svg class="mb-4 h-14 w-14 text-[#49617D]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 18a4.6 4.6 0 0 1-.5-9.17A6 6 0 0 1 18 7.5a4.5 4.5 0 0 1 1 8.89M12 12v9m-4-5 4-4 4 4"/>
                        </svg>

                        <span class="text-sm font-semibold text-[#142344]">
                            Klik untuk memilih file atau seret file ke sini
                        </span>

                        <span class="mt-2 text-xs text-[#60718A]">
                            Format PDF (maks. 10 MB)
                        </span>
                    </label>

                    <input
                        type="file"
                        id="final_pdf"
                        name="final_pdf"
                        accept=".pdf,application/pdf"
                        required
                        class="sr-only"
                    >

                    {{-- INFORMASI FILE --}}
                    <div id="fileInfo" class="hidden rounded-xl border border-[#DDEAF0] bg-[#FAFCFD] p-4">
                        <div class="flex items-center gap-3">
                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-red-50 text-xs font-bold text-red-600">
                                PDF
                            </div>

                            <div class="min-w-0 flex-1">
                                <p id="fileName" class="break-all text-sm font-semibold text-[#142344]"></p>
                                <p id="fileSize" class="mt-1 text-xs text-[#60718A]"></p>
                            </div>

                            <button
                                type="button"
                                id="removeFile"
                                class="shrink-0 rounded-lg px-3 py-2 text-sm font-semibold text-[#60718A] hover:bg-red-50 hover:text-red-600"
                            >
                                Hapus
                            </button>
                        </div>
                    </div>

                    {{-- PRATINJAU FILE DI PANEL KANAN --}}
                    <div id="previewContainer" class="hidden overflow-hidden rounded-xl border border-[#DDE6EC]">
                        <div class="flex items-center justify-between border-b border-[#E6EDF2] px-4 py-3">
                            <span class="text-sm font-semibold text-[#142344]">
                                File yang dipilih
                            </span>

                            <a
                                id="openPreview"
                                href="#"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="text-sm font-semibold text-[#087F73] hover:underline"
                            >
                                Buka file ↗
                            </a>
                        </div>

                        <iframe
                            id="filePreview"
                            title="Pratinjau PDF yang dipilih"
                            class="h-48 w-full bg-[#F4F7F9]"
                        ></iframe>
                    </div>

                    {{-- INFORMASI --}}
                    <div class="rounded-xl border border-[#CFE2FA] bg-[#F0F6FF] p-4">
                        <div class="flex items-start gap-3">
                            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-[#2783E8] text-sm font-bold text-white">i</span>

                            <p class="text-xs leading-5 text-[#29466F]">
                                Pastikan dokumen yang diunggah adalah versi final yang telah ditandatangani oleh kedua pihak dan sesuai dengan dokumen kontrak yang di-ACC.
                            </p>
                        </div>
                    </div>

                    {{-- KONFIRMASI --}}
                    <div class="pt-2">
                        <h3 class="text-base font-bold text-[#142344]">
                            Konfirmasi
                        </h3>

                        <label class="mt-3 flex cursor-pointer items-start gap-3">
                            <input
                                type="checkbox"
                                name="final_signature_confirmed"
                                id="final_signature_confirmed"
                                value="1"
                                required
                                class="mt-1 h-5 w-5 shrink-0 rounded border-[#B8C9D3] accent-[#087F73]"
                                {{ old('final_signature_confirmed') ? 'checked' : '' }}
                            >

                            <span class="text-sm leading-6 text-[#60718A]">
                                Saya telah memeriksa bahwa dokumen final yang diunggah sudah memuat tanda tangan kedua pihak (tenant dan pihak komersil) serta sesuai dengan dokumen kontrak yang di-ACC.
                            </span>
                        </label>
                    </div>
                </div>

                {{-- TOMBOL BAWAH: TANPA LIHAT DETAIL --}}
                <div class="flex flex-col-reverse gap-3 border-t border-[#E6EDF2] bg-white p-4 sm:flex-row sm:items-center sm:justify-between">

                    <a
                        href="{{ route('kontrak') }}"
                        class="inline-flex items-center justify-center gap-2 rounded-lg border border-[#CBD9E3] bg-white px-5 py-3 text-sm font-semibold text-[#29405C] hover:bg-[#F5F8FA]"
                    >
                        <span aria-hidden="true">‹</span>
                        Kembali
                    </a>

                    <button
                        type="submit"
                        id="submitUpload"
                        class="inline-flex items-center justify-center gap-2 rounded-lg bg-[#087F73] px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-[#06685F] disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 4h12l3 3v13H4V4h1Zm3 0v5h8V4M8 20v-7h8v7"/>
                        </svg>

                        Simpan Kontrak Final
                    </button>
                </div>
            </form>
        </section>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('final_pdf');
    const dropZone = document.getElementById('dropZone');
    const fileInfo = document.getElementById('fileInfo');
    const fileName = document.getElementById('fileName');
    const fileSize = document.getElementById('fileSize');
    const removeButton = document.getElementById('removeFile');
    const previewContainer = document.getElementById('previewContainer');
    const filePreview = document.getElementById('filePreview');
    const openPreview = document.getElementById('openPreview');

    const uploadedPdfPreview = document.getElementById('uploadedPdfPreview');
    const emptyPreview = document.getElementById('emptyPreview');

    const form = document.getElementById('formUploadFinal');
    const submitButton = document.getElementById('submitUpload');

    let previewUrl = null;

    function clearPreview() {
        if (previewUrl) {
            URL.revokeObjectURL(previewUrl);
            previewUrl = null;
        }

        filePreview.removeAttribute('src');
        openPreview.removeAttribute('href');
        uploadedPdfPreview.removeAttribute('src');

        previewContainer.classList.add('hidden');
        uploadedPdfPreview.classList.add('hidden');
        emptyPreview.classList.remove('hidden');
    }

    function showFile(file) {
        clearPreview();

        if (!file) {
            fileInfo.classList.add('hidden');
            dropZone.classList.remove('hidden');
            fileName.textContent = '';
            fileSize.textContent = '';
            return;
        }

        const isPdf = file.type === 'application/pdf'
            || file.name.toLowerCase().endsWith('.pdf');

        if (!isPdf) {
            input.value = '';
            fileInfo.classList.add('hidden');
            dropZone.classList.remove('hidden');
            alert('File harus berformat PDF.');
            return;
        }

        if (file.size > 10 * 1024 * 1024) {
            input.value = '';
            fileInfo.classList.add('hidden');
            dropZone.classList.remove('hidden');
            alert('Ukuran file maksimal 10 MB.');
            return;
        }

        fileName.textContent = file.name;
        fileSize.textContent = (file.size / (1024 * 1024)).toFixed(2) + ' MB';

        fileInfo.classList.remove('hidden');
        dropZone.classList.add('hidden');

        previewUrl = URL.createObjectURL(file);

        // PDF yang sama ditampilkan di panel kiri dan panel kanan.
        uploadedPdfPreview.src = previewUrl;
        uploadedPdfPreview.classList.remove('hidden');
        emptyPreview.classList.add('hidden');

        filePreview.src = previewUrl;
        openPreview.href = previewUrl;
        previewContainer.classList.remove('hidden');
    }

    input.addEventListener('change', function () {
        showFile(this.files[0]);
    });

    removeButton.addEventListener('click', function () {
        input.value = '';
        showFile(null);
    });

    ['dragenter', 'dragover'].forEach(function (eventName) {
        dropZone.addEventListener(eventName, function (event) {
            event.preventDefault();
            dropZone.classList.add('border-[#087F73]', 'bg-[#F0F9F7]');
        });
    });

    ['dragleave', 'drop'].forEach(function (eventName) {
        dropZone.addEventListener(eventName, function (event) {
            event.preventDefault();
            dropZone.classList.remove('border-[#087F73]', 'bg-[#F0F9F7]');
        });
    });

    dropZone.addEventListener('drop', function (event) {
        const files = event.dataTransfer.files;

        if (!files.length) return;

        const transfer = new DataTransfer();
        transfer.items.add(files[0]);
        input.files = transfer.files;

        showFile(files[0]);
    });

    form.addEventListener('submit', function (event) {
        const file = input.files[0];
        const confirmed = document.getElementById('final_signature_confirmed').checked;

        if (!file) {
            event.preventDefault();
            alert('Silakan pilih dokumen kontrak final terlebih dahulu.');
            return;
        }

        if (!confirmed) {
            event.preventDefault();
            alert('Silakan centang konfirmasi tanda tangan kedua pihak.');
            return;
        }

        if (!confirm('Pastikan PDF final sudah benar dan ditandatangani kedua pihak. Simpan dokumen ini?')) {
            event.preventDefault();
            return;
        }

        submitButton.disabled = true;
        submitButton.textContent = 'Menyimpan dokumen...';
    });

    window.addEventListener('beforeunload', function () {
        if (previewUrl) {
            URL.revokeObjectURL(previewUrl);
        }
    });
});
</script>
@endsection
