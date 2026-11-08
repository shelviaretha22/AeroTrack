@props([
    'segments' => [],   // [['label' => 'Terminal 1', 'value' => 12, 'color' => '#527D82'], ...]
    'size' => 'h-44 w-44',
])
@php
    $total = (float) collect($segments)->sum('value');
    $akumulasi = 0;
    $deskripsi = collect($segments)->map(fn ($s) => $s['label'] . ' ' . $s['value'])->implode(', ');
@endphp

<div class="relative shrink-0 {{ $size }}">
    <svg viewBox="0 0 42 42" class="h-full w-full -rotate-90" role="img" aria-label="Diagram pie: {{ $deskripsi }}">
        {{-- Lingkaran penuh (pie): jari-jari = setengah dari lingkaran 100 satuan, tebal garis = jari-jari --}}
        <circle cx="21" cy="21" r="7.9577" pathLength="100" fill="none" stroke="#E7EFEC" stroke-width="15.9155"/>
        @if ($total > 0)
            @foreach ($segments as $s)
                @php $persen = ($s['value'] / $total) * 100; @endphp
                @if ($persen > 0)
                    <circle cx="21" cy="21" r="7.9577" pathLength="100" fill="none"
                            stroke="{{ $s['color'] }}" stroke-width="15.9155"
                            stroke-dasharray="{{ round($persen, 3) }} {{ round(100 - $persen, 3) }}"
                            stroke-dashoffset="{{ round(-$akumulasi, 3) }}">
                        <title>{{ $s['label'] }}: {{ $s['value'] }} ({{ round($persen, 1) }}%)</title>
                    </circle>
                    @php $akumulasi += $persen; @endphp
                @endif
            @endforeach
        @endif
    </svg>
</div>
