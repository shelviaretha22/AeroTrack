@props([
    'segments' => [],   // [['label' => 'Terisi', 'value' => 12, 'color' => '#D6A14D'], ...]
    'center' => null,   // angka/teks besar di tengah
    'sub' => null,      // teks kecil di bawah angka
    'size' => 'h-40 w-40',
])
@php
    $total = (float) collect($segments)->sum('value');
    $akumulasi = 0;
    $deskripsi = collect($segments)
        ->map(fn ($s) => $s['label'] . ' ' . $s['value'])
        ->implode(', ');
@endphp

<div class="relative shrink-0 {{ $size }}">
    <svg viewBox="0 0 42 42" class="h-full w-full -rotate-90" role="img" aria-label="Diagram donat: {{ $deskripsi }}">
        <circle cx="21" cy="21" r="15.9155" fill="none" stroke="#E7EFEC" stroke-width="5"/>

        @if ($total > 0)
            @foreach ($segments as $s)
                @php $persen = ($s['value'] / $total) * 100; @endphp
                @if ($persen > 0)
                    <circle cx="21" cy="21" r="15.9155" fill="none"
                            stroke="{{ $s['color'] }}" stroke-width="5"
                            stroke-dasharray="{{ round($persen, 3) }} {{ round(100 - $persen, 3) }}"
                            stroke-dashoffset="{{ round(-$akumulasi, 3) }}">
                        <title>{{ $s['label'] }}: {{ $s['value'] }} ({{ round($persen, 1) }}%)</title>
                    </circle>
                    @php $akumulasi += $persen; @endphp
                @endif
            @endforeach
        @endif
    </svg>

    <div class="absolute inset-0 flex flex-col items-center justify-center text-center">
        <span class="text-2xl font-bold text-[#365F65]">{{ $center ?? number_format($total, 0, ',', '.') }}</span>
        @if ($sub)
            <span class="text-[11px] text-[#7A9290]">{{ $sub }}</span>
        @endif
    </div>
</div>
