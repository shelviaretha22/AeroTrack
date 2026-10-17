
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
