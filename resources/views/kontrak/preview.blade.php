
@extends('layouts.app')

@section('page-title', 'Kontrak')

@section('content')
<div class="preview-page">

    {{-- NAVIGASI KEMBALI --}}
    <div class="preview-back">
        <a href="{{ route('kontrak') }}">
            <span aria-hidden="true">←</span>
            Kembali ke Data Kontrak
        </a>
    </div>

    {{-- TAHAPAN KONTRAK --}}
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

    {{-- PDF VIEWER --}}
    <section class="pdf-viewer">

        <div class="pdf-toolbar">
            <div class="pdf-toolbar-left">
                <span class="pdf-menu-icon">☷</span>
                <span class="page-counter">PDF</span>
            </div>

            <div class="pdf-toolbar-center">
                <button type="button" id="zoom-out" aria-label="Perkecil">−</button>
                <span id="zoom-value">100%</span>
                <button type="button" id="zoom-in" aria-label="Perbesar">+</button>
            </div>

            <div class="pdf-toolbar-right">
                @if($kontrak->final_pdf_path ?? false)
                    <a
                        href="{{ asset('storage/' . $kontrak->final_pdf_path) }}"
                        target="_blank"
                        rel="noopener"
                        aria-label="Unduh atau buka PDF"
                        title="Unduh PDF"
                    >↓</a>
                @else
                    <span class="pdf-tool-disabled" title="PDF belum tersedia">↓</span>
                @endif

                <button
                    type="button"
                    onclick="window.print()"
                    aria-label="Cetak"
                    title="Cetak"
                >▣</button>
            </div>
        </div>

        <div class="pdf-stage">
            @if($kontrak->final_pdf_path ?? false)
                <iframe
                    id="contract-pdf"
                    src="{{ asset('storage/' . $kontrak->final_pdf_path) }}#toolbar=0&view=FitH"
                    title="Preview dokumen kontrak"
                ></iframe>
            @else
                <div class="pdf-empty">
                    <div class="pdf-empty-icon">▤</div>
                    <h3>Dokumen PDF belum tersedia</h3>
                    <p>
                        PDF kontrak akan ditampilkan di sini setelah dokumen
                        final tersedia dan tersimpan di sistem.
                    </p>
                </div>
            @endif
        </div>
    </section>

    {{-- CHECKLIST --}}
    <div class="preview-checklist">
        <label>
            <input type="checkbox" id="document-checked">
            <span>
                Saya telah membaca dan memahami seluruh isi dokumen kontrak ini,
                termasuk identitas para pihak, objek sewa, jangka waktu,
                nilai sewa, dan ketentuan lainnya.
            </span>
        </label>
    </div>

    {{-- TOMBOL BAWAH --}}
    <div class="preview-footer">
        <div class="footer-left">
            <button type="button" class="preview-button" id="detail-button">
                <span>◎</span> Lihat Detail
            </button>

            <button type="button" class="preview-button" onclick="window.print()">
                <span>▣</span> Print
            </button>
        </div>

        <div class="footer-right">
            <a href="{{ route('kontrak') }}" class="preview-button">
                ‹ &nbsp; Kembali
            </a>

            @if($kontrak ?? false)
                <a
                    href="{{ route('kontrak.acc', $kontrak->id) }}"
                    id="continue-button"
                    class="preview-button primary disabled"
                    aria-disabled="true"
                    tabindex="-1"
                >
                    Lanjut &nbsp; →
                </a>
            @else
                <button type="button" class="preview-button primary" disabled>
                    Lanjut &nbsp; →
                </button>
            @endif
        </div>
    </div>

    <p class="preview-message" id="preview-message" role="status"></p>
</div>

<style>
    .preview-page {
        color: #365f65;
        width: 100%;
    }

    .preview-back {
        margin-bottom: 6px;
        font-size: 12px;
    }

    .preview-back a {
        color: #244f80;
        text-decoration: none;
    }

    .preview-back a:hover {
        text-decoration: underline;
    }

    .contract-steps {
        display: flex;
        align-items: flex-start;
        justify-content: center;
        max-width: 560px;
        margin: 6px auto 18px;
    }

    .contract-step {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 7px;
        min-width: 104px;
        color: #536778;
        text-align: center;
    }

    .step-number {
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #d3dfe5;
        border-radius: 50%;
        background: #f5f8fa;
        font-size: 12px;
        font-weight: 600;
    }

    .contract-step.active {
        color: #006c61;
        font-weight: 700;
    }

    .contract-step.active .step-number {
        color: #fff;
        background: #0d8d7e;
        border-color: #0d8d7e;
    }

    .step-line {
        height: 2px;
        background: #d3dfe5;
        flex: 1;
        max-width: 115px;
        margin-top: 14px;
    }

    .contract-step.active + .step-line {
        background: #0d8d7e;
    }

    .step-label {
        font-size: 11px;
        white-space: nowrap;
    }

    .pdf-viewer {
        border: 1px solid #dce5e8;
        background: #eaf0f4;
        overflow: hidden;
    }

    .pdf-toolbar {
        min-height: 44px;
        padding: 0 15px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        color: white;
        background: #3e474f;
    }

    .pdf-toolbar-left,
    .pdf-toolbar-center,
    .pdf-toolbar-right {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .pdf-menu-icon {
        font-size: 21px;
    }

    .page-counter {
        padding: 4px 10px;
        border-radius: 4px;
        background: #303940;
        font-size: 11px;
    }

    .pdf-toolbar button,
    .pdf-toolbar-right a {
        color: white;
        border: 0;
        background: transparent;
        text-decoration: none;
        cursor: pointer;
        font-size: 20px;
    }

    .pdf-toolbar-center {
        gap: 0;
        background: #303940;
        border-radius: 5px;
        overflow: hidden;
    }

    .pdf-toolbar-center button {
        width: 34px;
        height: 30px;
    }

    #zoom-value {
        min-width: 60px;
        text-align: center;
        font-size: 11px;
    }

    .pdf-toolbar-right {
        gap: 18px;
    }

    .pdf-tool-disabled {
        opacity: .4;
        font-size: 20px;
    }

    .pdf-stage {
        height: min(68vh, 780px);
        min-height: 420px;
        overflow: auto;
        display: flex;
        align-items: stretch;
        justify-content: center;
        padding: 12px;
        background: #eaf0f4;
    }

    #contract-pdf {
        width: 100%;
        min-height: 100%;
        border: 0;
        background: white;
    }

    .pdf-empty {
        align-self: center;
        width: min(440px, 100%);
        padding: 40px 24px;
        border: 1px solid #e0e8eb;
        border-radius: 6px;
        background: white;
        text-align: center;
    }

    .pdf-empty-icon {
        font-size: 35px;
        color: #7a9290;
    }

    .pdf-empty h3 {
        margin-top: 12px;
        font-size: 16px;
        font-weight: 700;
    }

    .pdf-empty p {
        margin-top: 8px;
        color: #7a9290;
        font-size: 12px;
        line-height: 1.7;
    }

    .preview-checklist {
        padding: 10px 0;
        border-bottom: 1px solid #dce6e8;
        font-size: 11px;
        line-height: 1.5;
    }

    .preview-checklist label {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        cursor: pointer;
    }

    .preview-checklist input {
        margin-top: 3px;
        accent-color: #0d8d7e;
    }

    .preview-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
        padding: 10px 0;
    }

    .footer-left,
    .footer-right {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
    }

    .preview-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        min-height: 34px;
        padding: 7px 16px;
        border: 1px solid #dce6e8;
        border-radius: 5px;
        color: #456773;
        background: white;
        font-size: 11px;
        text-decoration: none;
        cursor: pointer;
    }

    .preview-button:hover:not(:disabled) {
        background: #f3f7f7;
    }

    .preview-button.primary {
        min-width: 130px;
        border-color: #0d8d7e;
        color: white;
        background: #0d8d7e;
    }

    .preview-button:disabled,
    .preview-button.disabled {
        opacity: .45;
        cursor: not-allowed;
        pointer-events: none;
    }

    .preview-message {
        color: #9a4f35;
        font-size: 12px;
    }

    @media (max-width: 700px) {
        .pdf-stage {
            min-height: 360px;
            height: 60vh;
            padding: 6px;
        }

        .step-label {
            font-size: 9px;
        }

        .contract-step {
            min-width: 80px;
        }

        .pdf-toolbar {
            padding: 0 8px;
        }

        .footer-left,
        .footer-right {
            width: 100%;
        }

        .footer-right {
            justify-content: flex-end;
        }
    }

    @media print {
        body * {
            visibility: hidden !important;
        }

        #contract-pdf,
        #contract-pdf * {
            visibility: visible !important;
        }

        .preview-page > :not(.pdf-viewer) {
            display: none !important;
        }

        .pdf-toolbar {
            display: none !important;
        }

        .pdf-stage {
            height: auto;
            padding: 0;
            overflow: visible;
        }
    }
</style>

<script>
    const zoomValue = document.getElementById('zoom-value');
    const zoomIn = document.getElementById('zoom-in');
    const zoomOut = document.getElementById('zoom-out');
    const detailButton = document.getElementById('detail-button');
    const checklist = document.getElementById('document-checked');
    const continueButton = document.getElementById('continue-button');
    const message = document.getElementById('preview-message');

    let zoom = 100;

    function updateZoom() {
        zoomValue.textContent = `${zoom}%`;

        const pdfFrame = document.getElementById('contract-pdf');
        if (pdfFrame) {
            pdfFrame.style.zoom = `${zoom}%`;
            pdfFrame.style.width = `${10000 / zoom}%`;
        }
    }

    zoomIn.addEventListener('click', () => {
        zoom = Math.min(200, zoom + 10);
        updateZoom();
    });

    zoomOut.addEventListener('click', () => {
        zoom = Math.max(50, zoom - 10);
        updateZoom();
    });

    detailButton.addEventListener('click', () => {
        message.textContent =
            'Detail kontrak dapat dibuka setelah dokumen kontrak resmi tersedia.';
    });

    if (checklist && continueButton) {
        checklist.addEventListener('change', () => {
            const enabled = checklist.checked;

            continueButton.classList.toggle('disabled', !enabled);
            continueButton.setAttribute('aria-disabled', String(!enabled));
            continueButton.tabIndex = enabled ? 0 : -1;

            message.textContent = enabled
                ? ''
                : 'Centang checklist sebelum melanjutkan.';
        });

        continueButton.addEventListener('click', (event) => {
            if (!checklist.checked) {
                event.preventDefault();
                message.textContent =
                    'Periksa dan centang dokumen terlebih dahulu.';
            }
        });
    }
</script>
@endsection
