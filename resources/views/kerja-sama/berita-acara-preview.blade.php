
@extends('layouts.app')

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

                <a href="{{ route('kerja-sama.berita-acara.download', $kerjaSama->id) }}"
                   style="padding: 10px 16px; border-radius: 6px; text-decoration: none; color: white; background: #365f65;">
                    Unduh PDF
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

        <div style="background: white; padding: 12px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,.08);">
            <iframe
                src="{{ route('kerja-sama.berita-acara.pdf', $kerjaSama->id) }}"
                title="Preview Berita Acara"
                style="width: 100%; height: 78vh; border: none;">
            </iframe>
        </div>

    </div>
</div>
@endsection
