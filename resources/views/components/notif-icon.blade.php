@props(['jenis', 'level' => 'info', 'ukuran' => 'h-9 w-9'])
@php
    $warna = match ($level) {
        'penting' => 'bg-red-50 text-red-600',
        'sedang' => 'bg-amber-50 text-amber-700',
        default => 'bg-[#EAF2F1] text-[#365F65]',
    };

    // Satu path SVG per jenis notifikasi.
    $ikon = match ($jenis) {
        'kontrak_berakhir' => '<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 11h18M12 14v3l2 1"/>',
        'aktivasi_menunggu' => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
        'siap_diajukan' => '<path d="m22 2-11 11M22 2l-7 20-4-9-9-4 20-7Z"/>',
        'ba_belum_ttd' => '<path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/>',
        'aktivasi_keputusan' => '<circle cx="12" cy="12" r="9"/><path d="m8 12 2.5 2.5L16 9"/>',
        'lokasi_kosong' => '<path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5"/>',
        default => '<path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M10 21h4"/>',
    };
@endphp

<span class="flex shrink-0 items-center justify-center rounded-xl {{ $ukuran }} {{ $warna }}" aria-hidden="true">
    <svg class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">{!! $ikon !!}</svg>
</span>
