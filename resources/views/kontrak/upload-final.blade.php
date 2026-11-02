
@extends('layouts.app')


@section('page-title', 'Upload Dokumen Kontrak')

@section('content')


{{-- MODAL KONFIRMASI HAPUS DOKUMEN --}}
<div
    class="delete-modal-overlay"
    id="deleteModal"
    aria-hidden="true"
>
    <div
        class="delete-modal"
        role="dialog"
        aria-modal="true"
        aria-labelledby="deleteModalTitle"
    >
        <div class="delete-modal-header">
            <h3 id="deleteModalTitle">
                Hapus Dokumen
            </h3>

            <button
                type="button"
                class="delete-modal-close"
                id="closeDeleteModal"
                aria-label="Tutup"
            >
                ✕
            </button>
        </div>

        <p class="delete-modal-description">
            Apakah kamu yakin ingin menghapus dokumen kontrak final ini?
            Dokumen yang dihapus tidak dapat dikembalikan.
        </p>

        <div class="delete-modal-actions">
            <button
                type="button"
                class="delete-modal-confirm"
                id="confirmDeleteFile"
            >
                <i class="fas fa-trash-alt"></i>
                Hapus
            </button>

            <button
                type="button"
                class="delete-modal-cancel"
                id="cancelDeleteModal"
            >
                Batal
            </button>
        </div>
    </div>
</div>

<style>
    /* DASAR HALAMAN */
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

    /* JUDUL HALAMAN */
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

    /* STEPPER */
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

    /* TAHAP 1 - SUDAH DILEWATI */
    .dokumen-page .contract-step.completed {
        color: #9CA3AF;
        font-weight: 500;
    }

    .dokumen-page .contract-step.completed .step-number {
        background: #D1D5DB;
        color: #FFFFFF;
    }

    /* TAHAP 2 - SEDANG AKTIF */
    .dokumen-page .contract-step.active {
        color: #087F73;
        font-weight: 600;
    }

    .dokumen-page .contract-step.active .step-number {
        background: #087F73;
        color: #FFFFFF;
    }

    /* GARIS PENGHUBUNG */
    .dokumen-page .step-line.active {
        background: #087F73;
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

    .dokumen-page .step-line.active {
        background: #087F73;
    }
    
    
/* =========================================
   MODAL KONFIRMASI HAPUS DOKUMEN
========================================= */

.delete-modal-overlay {
    position: fixed;
    inset: 0;
    z-index: 9999;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 20px;
    background: rgba(15, 23, 42, .55);
}

.delete-modal-overlay.show {
    display: flex;
}

.delete-modal {
    width: 100%;
    max-width: 420px;
    padding: 25px;
    border-radius: 14px;
    background: #ffffff;
    box-shadow: 0 15px 50px rgba(0, 0, 0, .18);
    animation: deleteModalShow .2s ease;
}

@keyframes deleteModalShow {
    from {
        opacity: 0;
        transform: translateY(10px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.delete-modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
}

.delete-modal-header h3 {
    margin: 0;
    color: #142C5C;
    font-size: 17px;
    font-weight: 700;
}

.delete-modal-close {
    width: 34px;
    height: 34px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: none;
    border-radius: 8px;
    background: #F1F3F5;
    color: #333333;
    font-size: 16px;
    font-weight: 400;
    cursor: pointer;
    transition: .2s;
}

.delete-modal-close:hover {
    background: #E5E7EB;
}

.delete-modal-description {
    margin: 0 0 22px;
    color: #89939b;
    font-size: 12px;
    line-height: 1.7;
}

/* BUTTON SEJAJAR KANAN KIRI */
.delete-modal-actions {
    display: flex;
    gap: 12px;
    width: 100%;
}

.delete-modal-confirm,
.delete-modal-cancel {
    flex: 1;
    min-height: 42px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: .2s;
}

.delete-modal-confirm {
    border: 1px solid #dc3545;
    background: #dc3545;
    color: #ffffff;
}

.delete-modal-confirm:hover {
    background: #bd2635;
}

.delete-modal-cancel {
    border: 1px solid #e0e4e8;
    background: #ffffff;
    color: #68737d;
}

.delete-modal-cancel:hover {
    background: #f7f8fa;
}

.delete-modal-confirm:disabled {
    opacity: .6;
    cursor: not-allowed;
}

body.modal-open {
    overflow: hidden;
}

</style>

<div class="min-h-full space-y-5 bg-[#F4F8FA] p-4 lg:p-6">

    
{{-- HEADER DAN PROGRESS --}}
<div class="dokumen-page">
    <div class="page-heading">
        <h1 class="page-title">Upload Dokumen Kontrak</h1>
        <p class="page-subtitle">
            Unggah dokumen kontrak final yang telah ditandatangani oleh kedua pihak.
        </p>
    </div>

    {{-- STEPPER 2 TAHAP --}}
    <div class="contract-steps">

        {{-- TAHAP 1: SELESAI --}}
        <div class="contract-step completed">
            <div class="step-number">1</div>
            <div class="step-label">
                Isi Dokumen<br>Kontrak
            </div>
        </div>

        <div class="step-line active"></div>

        {{-- TAHAP 2: AKTIF --}}
        <div class="contract-step active">
            <div class="step-number">2</div>
            <div class="step-label">
                Upload Dokumen<br>Kontrak
            </div>
        </div>

    </div>
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
            data-temp-upload-url="{{ route('kontrak.uploadFinal.temp', $kontrak->id) }}"
            data-temp-delete-url="{{ route('kontrak.uploadFinal.temp.delete', $kontrak->id) }}"
            data-temp-preview-url="{{ route('kontrak.uploadFinal.temp.preview', $kontrak->id) }}"
            data-temp-upload='@json($tempUpload)'
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
                        href="{{ route('kontrak.show', $kontrak->id) }}"
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
        const form = document.getElementById('formUploadFinal');

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

        const submitButton = document.getElementById('submitUpload');
        const confirmation = document.getElementById('final_signature_confirmed');

        const uploadUrl = form.dataset.tempUploadUrl;
        const deleteUrl = form.dataset.tempDeleteUrl;
        const previewUrl = form.dataset.tempPreviewUrl;

        const csrfToken = form.querySelector('input[name="_token"]').value;

        let hasTemporaryFile = false;
        let isUploading = false;
        let isDeleting = false;

        function formatFileSize(bytes) {
            return (bytes / (1024 * 1024)).toFixed(2) + ' MB';
        }

        function showEmptyState() {
            fileInfo.classList.add('hidden');
            dropZone.classList.remove('hidden');

            previewContainer.classList.add('hidden');

            uploadedPdfPreview.removeAttribute('src');
            uploadedPdfPreview.classList.add('hidden');

            emptyPreview.classList.remove('hidden');

            filePreview.removeAttribute('src');
            openPreview.removeAttribute('href');

            fileName.textContent = '';
            fileSize.textContent = '';

            hasTemporaryFile = false;
        }

        function showSavedFile(name, size) {
            fileName.textContent = name;
            fileSize.textContent = formatFileSize(size);

            fileInfo.classList.remove('hidden');
            dropZone.classList.add('hidden');

            const url = previewUrl + '?t=' + Date.now();

            uploadedPdfPreview.src = url;
            uploadedPdfPreview.classList.remove('hidden');

            emptyPreview.classList.add('hidden');

            filePreview.src = url;
            openPreview.href = url;

            previewContainer.classList.remove('hidden');

            hasTemporaryFile = true;
        }

        async function uploadTemporaryFile(file) {
            if (!file) return;

            const isPdf = file.type === 'application/pdf'
                || file.name.toLowerCase().endsWith('.pdf');

            if (!isPdf) {
                input.value = '';
                alert('File harus berformat PDF.');
                return;
            }

            if (file.size > 10 * 1024 * 1024) {
                input.value = '';
                alert('Ukuran file maksimal 10 MB.');
                return;
            }

            if (isUploading) return;

            isUploading = true;
            submitButton.disabled = true;
            removeButton.disabled = true;

            const formData = new FormData();
            formData.append('final_pdf', file);

            try {
                const response = await fetch(uploadUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const result = await response.json();

                if (!response.ok || !result.success) {
                    throw new Error(
                        result.message || 'PDF gagal diunggah.'
                    );
                }

                showSavedFile(
                    result.file_name,
                    result.file_size
                );

                input.value = '';

            } catch (error) {
                alert(error.message || 'Terjadi kesalahan saat mengunggah PDF.');
                input.value = '';

            } finally {
                isUploading = false;
                submitButton.disabled = false;
                removeButton.disabled = false;
            }
        }

        input.addEventListener('change', function () {
            uploadTemporaryFile(this.files[0]);
        });

        
        const deleteModal = document.getElementById('deleteModal');
        const closeDeleteModal = document.getElementById('closeDeleteModal');
        const cancelDeleteModal = document.getElementById('cancelDeleteModal');
        const confirmDeleteFile = document.getElementById('confirmDeleteFile');

        function openDeleteModal() {
            if (!hasTemporaryFile || isDeleting) return;

            deleteModal.classList.add('show');
            deleteModal.setAttribute('aria-hidden', 'false');
            document.body.classList.add('modal-open');
        }

        function closeDeleteModalWindow() {
            deleteModal.classList.remove('show');
            deleteModal.setAttribute('aria-hidden', 'true');
            document.body.classList.remove('modal-open');
        }

        // Klik tombol Hapus pada informasi file
        removeButton.addEventListener('click', function () {
            openDeleteModal();
        });

        // Tombol Batal
        cancelDeleteModal.addEventListener('click', function () {
            closeDeleteModalWindow();
        });

        // Tombol X
        closeDeleteModal.addEventListener('click', function () {
            closeDeleteModalWindow();
        });

        // Klik area gelap di luar modal
        deleteModal.addEventListener('click', function (event) {
            if (event.target === deleteModal) {
                closeDeleteModalWindow();
            }
        });

        // Konfirmasi penghapusan
        confirmDeleteFile.addEventListener('click', async function () {
            if (!hasTemporaryFile || isDeleting) return;

            isDeleting = true;
            confirmDeleteFile.disabled = true;
            removeButton.disabled = true;

            confirmDeleteFile.innerHTML =
                '<i class="fas fa-spinner fa-spin"></i> Menghapus...';

            try {
                const response = await fetch(deleteUrl, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });

                const result = await response.json();

                if (!response.ok || !result.success) {
                    throw new Error(
                        result.message || 'Dokumen gagal dihapus.'
                    );
                }

                input.value = '';
                showEmptyState();

                closeDeleteModalWindow();

            } catch (error) {
                alert(
                    error.message ||
                    'Terjadi kesalahan saat menghapus dokumen.'
                );

            } finally {
                isDeleting = false;
                confirmDeleteFile.disabled = false;
                removeButton.disabled = false;

                confirmDeleteFile.innerHTML =
                    '<i class="fas fa-trash-alt"></i> Hapus';
            }
        });


        ['dragenter', 'dragover'].forEach(function (eventName) {
            dropZone.addEventListener(eventName, function (event) {
                event.preventDefault();

                dropZone.classList.add(
                    'border-[#087F73]',
                    'bg-[#F0F9F7]'
                );
            });
        });

        ['dragleave', 'drop'].forEach(function (eventName) {
            dropZone.addEventListener(eventName, function (event) {
                event.preventDefault();

                dropZone.classList.remove(
                    'border-[#087F73]',
                    'bg-[#F0F9F7]'
                );
            });
        });

        dropZone.addEventListener('drop', function (event) {
            const files = event.dataTransfer.files;

            if (!files.length) return;

            uploadTemporaryFile(files[0]);
        });

        form.addEventListener('submit', function (event) {
            if (isUploading) {
                event.preventDefault();
                alert('Tunggu sampai proses upload PDF selesai.');
                return;
            }

            if (!hasTemporaryFile) {
                event.preventDefault();
                alert('Silakan unggah dokumen kontrak final terlebih dahulu.');
                return;
            }

            if (!confirmation.checked) {
                event.preventDefault();
                alert('Silakan centang konfirmasi tanda tangan kedua pihak.');
                return;
            }

            if (!confirm(
                'Pastikan PDF final sudah benar dan ditandatangani kedua pihak. Simpan dokumen ini?'
            )) {
                event.preventDefault();
                return;
            }

            submitButton.disabled = true;
            submitButton.textContent = 'Menyimpan dokumen...';
        });

        // Tampilkan kembali file sementara ketika halaman dibuka ulang.
        const savedFile = form.dataset.tempUpload
            ? JSON.parse(form.dataset.tempUpload)
            : null;

        if (
            savedFile &&
            savedFile.name &&
            savedFile.size
        ) {
            showSavedFile(
                savedFile.name,
                savedFile.size
            );
        } else {
            showEmptyState();
        }
    });
</script>
@endsection
