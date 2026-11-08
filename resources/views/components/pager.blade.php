@props(['paginator'])
@php
    $dari = $paginator->total() ? $paginator->firstItem() : 0;
    $sampai = $paginator->total() ? $paginator->lastItem() : 0;
    $btn = 'inline-flex h-7 min-w-[1.75rem] items-center justify-center rounded-lg border border-[#DCE6E4] px-2 text-[#527D82] hover:bg-[#F3F6F5]';
    $off = 'inline-flex h-7 min-w-[1.75rem] items-center justify-center rounded-lg border border-[#E2EBE9] px-2 text-[#B7C6C3] cursor-not-allowed';
@endphp
<div class="flex flex-wrap items-center justify-between gap-2 border-t border-[#E2EBE9] px-4 py-2.5 text-[11px] text-[#8BA5A2]">
    <span>{{ $dari }}–{{ $sampai }} dari {{ $paginator->total() }} data</span>
    <div class="flex items-center gap-1.5">
        @if ($paginator->onFirstPage())
            <span class="{{ $off }}">‹</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="{{ $btn }}" aria-label="Sebelumnya">‹</a>
        @endif
        <span class="px-1">Hal. {{ $paginator->currentPage() }} / {{ max(1, $paginator->lastPage()) }}</span>
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="{{ $btn }}" aria-label="Berikutnya">›</a>
        @else
            <span class="{{ $off }}">›</span>
        @endif
    </div>
</div>
