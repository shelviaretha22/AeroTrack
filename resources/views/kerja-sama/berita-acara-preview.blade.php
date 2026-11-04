@extends('layouts.app')

@section('page-title', 'Kerja Sama')

@section('content')
<div style="padding: 24px; background: #f3f6f5; min-height: 100vh; color: #365f65;">

    <div style="max-width: 1200px; margin: auto;">

        <div style="display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap; margin-bottom: 20px;">
            <div>
                <h1 style="font-size: 24px; font-weight: 700; margin: 0;">
                    Preview Berita Acara
                </h1>

                <p style="margin-top: 6px;">
                    Nomor Kerja Sama:
                    {{ $kerjaSama->nomor_kerja_sama ?: 'KS-' . str_pad($kerjaSama->id, 3, '0', STR_PAD_LEFT) }}
                </p>
            </div>

            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                <a href="{{ route('kerja-sama') }}"
                   style="padding: 10px 16px; border: 1px solid #365f65; border-radius: 6px; text-decoration: none; color: #365f65; background: white;">
                    Kembali
                </a>
            </div>
        </div>

        {{-- BA FINAL (TTD DI LUAR SISTEM) --}}
        <div style="background: white; padding: 18px 20px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,.08); margin-bottom: 16px;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 12px; flex-wrap: wrap;">
                <div>
                    <h2 style="font-size: 16px; font-weight: 700; margin: 0;">Berita Acara Final</h2>
                    <p style="margin: 4px 0 0; font-size: 13px; color: #527d82;">
                        Cetak BA, tanda tangani di luar sistem, lalu unggah hasil scan di sini.
                    </p>
                </div>

                @if ($kerjaSama->baFinalTersedia())
                    <span style="padding: 6px 12px; border-radius: 999px; background: #dcfce7; color: #166534; font-size: 12px; font-weight: 600;">
                        BA Selesai · diunggah {{ $kerjaSama->berita_acara_final_uploaded_at?->format('d M Y H:i') }}
                    </span>
                @else
                    <span style="padding: 6px 12px; border-radius: 999px; background: #fef3c7; color: #92400e; font-size: 12px; font-weight: 600;">
                        BA Belum TTD
                    </span>
                @endif
            </div>

            @if (session('success'))
                <p style="margin-top: 12px; padding: 10px 12px; border-radius: 8px; background: #f0fdf4; color: #166534; font-size: 13px;">{{ session('success') }}</p>
            @endif
            @if (session('error'))
                <p style="margin-top: 12px; padding: 10px 12px; border-radius: 8px; background: #fef2f2; color: #b91c1c; font-size: 13px;">{{ session('error') }}</p>
            @endif
            @if ($errors->any())
                <p style="margin-top: 12px; padding: 10px 12px; border-radius: 8px; background: #fef2f2; color: #b91c1c; font-size: 13px;">{{ $errors->first() }}</p>
            @endif

            @if ($kerjaSama->baFinalTersedia())
                <p style="margin: 14px 0 0; font-size: 13px; color: #527d82;">
                    BA final sudah diunggah dan tidak dapat diganti.
                    <a href="{{ route('kerja-sama.berita-acara.final', $kerjaSama->id) }}" target="_blank" rel="noopener"
                       style="font-weight: 600; color: #365f65; text-decoration: underline;">Lihat BA final</a>
                </p>
            @else
                <form method="POST" action="{{ route('kerja-sama.berita-acara.final.upload', $kerjaSama->id) }}"
                      enctype="multipart/form-data"
                      onsubmit="return confirm('BA final hanya dapat diunggah satu kali dan tidak dapat diganti. Lanjutkan unggah?');"
                      style="margin-top: 14px; display: flex; gap: 10px; flex-wrap: wrap; align-items: center;">
                    @csrf
                    <input type="file" name="berita_acara_final" accept=".pdf,.jpg,.jpeg,.png" required
                           style="padding: 8px; border: 1px solid #dce6e4; border-radius: 8px; font-size: 13px;">
                    <button type="submit"
                            style="padding: 10px 16px; border: none; border-radius: 6px; color: white; background: #365f65; font-weight: 600; cursor: pointer;">
                        Unggah BA Final
                    </button>
                </form>
                <p style="margin: 8px 0 0; font-size: 12px; color: #8ba5a2;">PDF, JPG, JPEG, atau PNG. Maksimal 10 MB. Hanya dapat diunggah satu kali.</p>
            @endif
        </div>

        
<div style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,.08);">

    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; margin-bottom:16px;">
        <div>
            <h2 style="font-size:18px; font-weight:700; margin:0;">Editor Berita Acara</h2>
            <p style="font-size:13px; color:#78908d; margin:5px 0 0;">
                Klik bagian dokumen untuk mengubah teks atau data.
            </p>
        </div>

        <a href="{{ route('kerja-sama.berita-acara.word', $kerjaSama->id) }}"
           style="padding:10px 15px; background:#256b55; color:white; border-radius:6px; text-decoration:none; font-size:13px; font-weight:600;">
            Download Word
        </a>
    </div>

    <form method="POST"
          action="{{ route('kerja-sama.berita-acara.simpan', $kerjaSama->id) }}"
          id="form-editor-ba">
        @csrf
        @method('PUT')

        <input type="hidden" name="content" id="editor-content">

        <div style="display:flex; gap:7px; flex-wrap:wrap; padding:12px; background:#f3f6f5; border:1px solid #dce6e4; border-radius:8px 8px 0 0;">

            <button type="button" onmousedown="event.preventDefault()" onclick="formatDokumen('bold')" style="padding:7px 12px; font-weight:bold; cursor:pointer;">B</button>
            <button type="button" onmousedown="event.preventDefault()" onclick="formatDokumen('italic')" style="padding:7px 12px; font-style:italic; cursor:pointer;">I</button>
            <button type="button" onmousedown="event.preventDefault()" onclick="formatDokumen('underline')" style="padding:7px 12px; text-decoration:underline; cursor:pointer;">U</button>
            <button type="button" onmousedown="event.preventDefault()" onclick="formatDokumen('insertUnorderedList')" style="padding:7px 12px; cursor:pointer;">• List</button>
            <button type="button" onmousedown="event.preventDefault()" onclick="formatDokumen('insertOrderedList')" style="padding:7px 12px; cursor:pointer;">1. List</button>
            <button type="button" onmousedown="event.preventDefault()" onclick="formatDokumen('justifyLeft')" style="padding:7px 12px; cursor:pointer;">Rata kiri</button>
            <button type="button" onmousedown="event.preventDefault()" onclick="formatDokumen('justifyCenter')" style="padding:7px 12px; cursor:pointer;">Tengah</button>
            <button type="button" onmousedown="event.preventDefault()" onclick="formatDokumen('justifyRight')" style="padding:7px 12px; cursor:pointer;">Rata kanan</button>

        </div>

        <div id="dokumen-ba"
             contenteditable="{{ $kerjaSama->baFinalTersedia() ? 'false' : 'true' }}"
             style="background:white; color:#222; min-height:900px; padding:55px 65px; border:1px solid #dce6e4; border-top:none; outline:none; font-family:Arial,sans-serif; font-size:14px; line-height:1.7;">

            {!! $editorContent !!}

        </div>

        <div style="display:flex; justify-content:flex-end; margin-top:16px;">
            @if ($kerjaSama->baFinalTersedia())
                <span style="font-size:13px; color:#78908d; align-self:center;">
                    BA final sudah diunggah, isi dokumen tidak dapat diubah lagi.
                </span>
            @else
                <button type="submit"
                        style="padding:11px 22px; border:none; border-radius:6px; background:#365f65; color:white; font-weight:600; cursor:pointer;">
                    Simpan Perubahan
                </button>
            @endif
        </div>

    </form>
</div>

<style>
    #dokumen-ba h2,
    #dokumen-ba h3 {
        color: #365f65;
        margin-top: 22px;
        margin-bottom: 10px;
    }

    #dokumen-ba h2 {
        text-align: center;
        font-size: 20px;
    }

    #dokumen-ba table {
        width: 100%;
        border-collapse: collapse;
        margin: 10px 0 20px;
    }

    #dokumen-ba td,
    #dokumen-ba th {
        padding: 5px 8px;
        vertical-align: top;
    }

    /* Garis hanya untuk tabel yang memang bertanda border (mis. Objek Kerja Sama). */
    #dokumen-ba table[border] td,
    #dokumen-ba table[border] th {
        border: 1px solid #777;
    }

    #dokumen-ba p {
        margin: 9px 0;
    }

    @media print {
        #dokumen-ba {
            border: none !important;
        }
    }
</style>

<script>
    function formatDokumen(command) {
        const editor = document.getElementById('dokumen-ba');

        if (editor.getAttribute('contenteditable') !== 'true') {
            return;
        }

        editor.focus();
        document.execCommand(command, false, null);
    }

    document.getElementById('form-editor-ba').addEventListener('submit', function () {
        document.getElementById('editor-content').value =
            document.getElementById('dokumen-ba').innerHTML;
    });
</script>


    </div>
</div>
@endsection