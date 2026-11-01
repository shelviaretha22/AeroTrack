{{-- Lonceng notifikasi di header. Data: $notifRingkas (dari View Composer). --}}
@php
    $belum = $notifRingkas['belum'] ?? 0;
    $daftarLonceng = $notifRingkas['items'] ?? collect();
@endphp

<div class="relative" x-data="{ buka: false }" @keydown.escape.window="buka = false" @click.outside="buka = false">
    <button type="button" @click="buka = !buka" :aria-expanded="buka" aria-haspopup="true"
            aria-label="Notifikasi{{ $belum > 0 ? ', ' . $belum . ' belum dibaca' : '' }}"
            class="relative flex h-10 w-10 items-center justify-center rounded-xl bg-[#F3F6F5] text-[#527D82] transition hover:bg-[#E8F0EE]">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M18 8a6 6 0 00-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M10 21h4"/>
        </svg>

        @if ($belum > 0)
            <span class="absolute -right-1 -top-1 flex h-[18px] min-w-[18px] items-center justify-center rounded-full bg-red-500 px-1 text-[10px] font-bold leading-none text-white ring-2 ring-white">
                {{ $belum > 9 ? '9+' : $belum }}
            </span>
        @endif
    </button>

    <div x-show="buka" x-transition.origin.top.right style="display: none;"
         class="absolute right-[-3.5rem] z-50 mt-3 w-[calc(100vw-2rem)] max-w-sm overflow-hidden rounded-2xl border border-[#E2EBE9] bg-white shadow-xl sm:right-0 sm:w-96">

        <div class="flex items-center justify-between gap-3 border-b border-[#E2EBE9] px-4 py-3">
            <div>
                <p class="text-sm font-bold text-[#365F65]">Notifikasi</p>
                <p class="text-xs text-[#8BA5A2]">{{ $belum > 0 ? $belum . ' belum dibaca' : 'Semua sudah dibaca' }}</p>
            </div>

            @if ($belum > 0)
                <form action="{{ route('notifikasi.baca-semua') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-xs font-semibold text-[#527D82] hover:underline">Tandai semua dibaca</button>
                </form>
            @endif
        </div>

        <div class="max-h-[26rem] divide-y divide-[#EEF3F1] overflow-y-auto">
            @forelse ($daftarLonceng as $n)
                <a href="{{ route('notifikasi.buka', ['k' => $n['kunci']]) }}"
                   class="flex gap-3 px-4 py-3 transition hover:bg-[#F8FAF9] {{ $n['dibaca'] ? '' : 'bg-[#F8FBFA]' }}">
                    <x-notif-icon :jenis="$n['jenis']" :level="$n['level']" />

                    <div class="min-w-0 flex-1">
                        <p class="text-[13px] leading-snug text-[#365F65] {{ $n['dibaca'] ? 'font-medium' : 'font-bold' }}">{{ $n['judul'] }}</p>
                        <p class="mt-0.5 truncate text-xs text-[#7A9290]">{{ $n['pesan'] }}</p>
                    </div>

                    @unless ($n['dibaca'])
                        <span class="mt-1.5 h-2 w-2 shrink-0 rounded-full bg-[#4F8189]" title="Belum dibaca"></span>
                    @endunless
                </a>
            @empty
                <div class="px-4 py-10 text-center">
                    <p class="text-sm font-semibold text-[#365F65]">Tidak ada notifikasi</p>
                    <p class="mt-1 text-xs text-[#8BA5A2]">Semua urusan sudah beres.</p>
                </div>
            @endforelse
        </div>

        <a href="{{ route('notifikasi.index') }}"
           class="block border-t border-[#E2EBE9] bg-[#F8FAF9] px-4 py-3 text-center text-sm font-semibold text-[#527D82] transition hover:bg-[#F3F6F5]">
            Lihat semua notifikasi
        </a>
    </div>
</div>
