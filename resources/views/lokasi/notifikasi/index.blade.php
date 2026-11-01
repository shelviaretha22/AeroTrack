@extends('layouts.app')

@section('page-title', 'Notifikasi')

@section('content')
@php
    $jenisInfo = \App\Services\NotifikasiService::JENIS;

    $chip = function (bool $aktif) {
        return 'inline-flex items-center gap-1.5 rounded-full px-3.5 py-1.5 text-xs font-semibold transition '
            . ($aktif ? 'bg-[#365F65] text-white' : 'bg-[#F3F6F5] text-[#527D82] hover:bg-[#E8F0EE]');
    };

    $adaFilter = $tampil === 'belum' || $kategori || $jenis;
    $gayaLevel = [
        'penting' => ['teks' => 'Mendesak', 'kelas' => 'bg-red-50 text-red-700'],
        'sedang' => ['teks' => 'Perlu tindakan', 'kelas' => 'bg-amber-50 text-amber-700'],
        'info' => ['teks' => 'Informasi', 'kelas' => 'bg-[#EAF2F1] text-[#365F65]'],
    ];
@endphp

<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-sm text-[#8BA5A2]">AeroTrack / Notifikasi</p>
            <h1 class="mt-1 text-2xl font-bold text-[#365F65]">Notifikasi</h1>
            <p class="mt-1 text-sm text-[#7A9290]">Hal-hal yang perlu perhatian dari data kontrak, aktivasi, dan lokasi.</p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('profile.index') }}#preferensi"
               class="inline-flex items-center justify-center rounded-xl border border-[#DCE6E4] bg-white px-4 py-2.5 text-sm font-semibold text-[#527D82] transition hover:bg-[#F5F8F7]">
                Atur notifikasi
            </a>

            @if ($belumDibaca > 0)
                <form action="{{ route('notifikasi.baca-semua') }}" method="POST">
                    @csrf
                    <button type="submit"
                            class="inline-flex items-center justify-center rounded-xl bg-[#365F65] px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-[#294F55]">
                        Tandai semua dibaca
                    </button>
                </form>
            @endif
        </div>
    </div>

    @foreach (['success' => 'border-emerald-200 bg-emerald-50 text-emerald-800', 'info' => 'border-blue-200 bg-blue-50 text-blue-800'] as $tipe => $kelas)
        @if (session($tipe))
            <div class="rounded-xl border px-4 py-3 text-sm {{ $kelas }}" role="status">{{ session($tipe) }}</div>
        @endif
    @endforeach

    {{-- Ringkasan --}}
    <section class="grid grid-cols-1 gap-3 sm:grid-cols-3 sm:gap-4" aria-label="Ringkasan notifikasi">
        <div class="rounded-2xl border border-[#E2EBE9] bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-[#7A9290]">Belum dibaca</p>
            <p class="mt-2 text-3xl font-bold text-[#365F65]">{{ $belumDibaca }}</p>
            <p class="mt-1 text-xs text-[#8BA5A2]">dari {{ $total }} notifikasi aktif</p>
        </div>

        <div class="rounded-2xl border border-[#E2EBE9] bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-[#7A9290]">Perlu tindakan</p>
            <p class="mt-2 text-3xl font-bold text-amber-700">{{ $perTindakan }}</p>
            <p class="mt-1 text-xs text-[#8BA5A2]">belum dibaca, butuh dikerjakan</p>
        </div>

        <div class="rounded-2xl border border-[#E2EBE9] bg-white p-5 shadow-sm">
            <p class="mb-3 text-sm font-medium text-[#7A9290]">Sebaran jenis</p>
            @if ($total > 0)
                <div class="flex h-3 overflow-hidden rounded-full bg-[#E7EFEC]" role="img" aria-label="Sebaran jenis notifikasi">
                    @foreach ($jenisInfo as $kode => $info)
                        @php $n = $perJenis[$kode] ?? 0; @endphp
                        @if ($n > 0)
                            <div style="width: {{ ($n / $total) * 100 }}%; background: {{ $info['kategori'] === 'tindakan' ? '#D6A14D' : '#527D82' }}; opacity: {{ 0.55 + (array_search($kode, array_keys($jenisInfo)) % 3) * 0.2 }}"
                                 title="{{ $info['label'] }}: {{ $n }}"></div>
                        @endif
                    @endforeach
                </div>
                <div class="mt-2 flex items-center gap-4 text-xs text-[#7A9290]">
                    <span class="inline-flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full bg-[#D6A14D]"></span>Perlu tindakan</span>
                    <span class="inline-flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full bg-[#527D82]"></span>Informasi</span>
                </div>
            @else
                <p class="text-sm text-[#8BA5A2]">Belum ada data.</p>
            @endif
        </div>
    </section>

    {{-- Filter --}}
    <section class="rounded-2xl border border-[#E2EBE9] bg-white p-4 shadow-sm sm:p-5">
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ request()->fullUrlWithQuery(['tampil' => null]) }}" class="{{ $chip($tampil === 'semua') }}">Semua</a>
            <a href="{{ request()->fullUrlWithQuery(['tampil' => 'belum']) }}" class="{{ $chip($tampil === 'belum') }}">
                Belum dibaca
                @if ($belumDibaca > 0)
                    <span class="rounded-full bg-white/25 px-1.5 text-[10px]">{{ $belumDibaca }}</span>
                @endif
            </a>

            <span class="mx-1 hidden h-5 w-px bg-[#E2EBE9] sm:block"></span>

            <a href="{{ request()->fullUrlWithQuery(['kategori' => $kategori === 'tindakan' ? null : 'tindakan', 'jenis' => null]) }}"
               class="{{ $chip($kategori === 'tindakan') }}">Perlu tindakan</a>
            <a href="{{ request()->fullUrlWithQuery(['kategori' => $kategori === 'info' ? null : 'info', 'jenis' => null]) }}"
               class="{{ $chip($kategori === 'info') }}">Informasi</a>
        </div>

        <div class="mt-3 flex flex-wrap items-center gap-2 border-t border-[#E2EBE9] pt-3">
            <span class="text-xs text-[#8BA5A2]">Jenis:</span>
            @foreach ($jenisInfo as $kode => $info)
                @continue(! ($jenisAktif[$kode] ?? true))
                <a href="{{ request()->fullUrlWithQuery(['jenis' => $jenis === $kode ? null : $kode, 'kategori' => null]) }}"
                   class="{{ $chip($jenis === $kode) }}">
                    {{ $info['label'] }}
                    <span class="opacity-70">{{ $perJenis[$kode] ?? 0 }}</span>
                </a>
            @endforeach

            @if ($adaFilter)
                <a href="{{ route('notifikasi.index') }}" class="ml-auto text-xs font-semibold text-[#527D82] hover:underline">Reset filter</a>
            @endif
        </div>
    </section>

    {{-- Daftar --}}
    <section class="overflow-hidden rounded-2xl border border-[#E2EBE9] bg-white shadow-sm">
        <div class="border-b border-[#E2EBE9] px-5 py-4">
            <h2 class="font-bold text-[#365F65]">Daftar Notifikasi</h2>
            <p class="mt-0.5 text-sm text-[#8BA5A2]">
                {{ $daftar->count() }} notifikasi{{ $adaFilter ? ' sesuai filter' : '' }}. Klik untuk membuka halaman terkait.
            </p>
        </div>

        <div class="divide-y divide-[#E2EBE9]">
            @forelse ($daftar as $n)
                @php $gaya = $gayaLevel[$n['level']] ?? $gayaLevel['info']; @endphp
                <div class="flex items-start gap-4 px-5 py-4 transition hover:bg-[#F8FAF9] {{ $n['dibaca'] ? '' : 'bg-[#F8FBFA]' }}">
                    <x-notif-icon :jenis="$n['jenis']" :level="$n['level']" ukuran="h-10 w-10" />

                    <a href="{{ route('notifikasi.buka', ['k' => $n['kunci']]) }}" class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <p class="text-sm text-[#365F65] {{ $n['dibaca'] ? 'font-medium' : 'font-bold' }}">{{ $n['judul'] }}</p>
                            @unless ($n['dibaca'])
                                <span class="h-2 w-2 rounded-full bg-[#4F8189]" title="Belum dibaca"></span>
                            @endunless
                        </div>
                        <p class="mt-1 text-sm text-[#7A9290]">{{ $n['pesan'] }}</p>
                        <div class="mt-2 flex flex-wrap items-center gap-2 text-xs">
                            <span class="rounded-full px-2.5 py-1 font-semibold {{ $gaya['kelas'] }}">{{ $gaya['teks'] }}</span>
                            <span class="rounded-full bg-[#F3F6F5] px-2.5 py-1 font-medium text-[#527D82]">{{ $jenisInfo[$n['jenis']]['label'] }}</span>
                            <span class="text-[#8BA5A2]">{{ \Illuminate\Support\Carbon::parse($n['waktu'])->locale('id')->isoFormat('D MMM YYYY') }}</span>
                        </div>
                    </a>

                    @unless ($n['dibaca'])
                        <form action="{{ route('notifikasi.baca') }}" method="POST" class="shrink-0">
                            @csrf
                            <input type="hidden" name="kunci" value="{{ $n['kunci'] }}">
                            <button type="submit" title="Tandai dibaca" aria-label="Tandai dibaca"
                                    class="flex h-9 w-9 items-center justify-center rounded-lg text-[#527D82] transition hover:bg-[#EAF2F1]">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 4 4L19 6"/></svg>
                            </button>
                        </form>
                    @endunless
                </div>
            @empty
                <div class="px-5 py-16 text-center">
                    <p class="font-semibold text-[#365F65]">
                        {{ $adaFilter ? 'Tidak ada notifikasi yang cocok' : 'Tidak ada notifikasi' }}
                    </p>
                    <p class="mt-1 text-sm text-[#8BA5A2]">
                        {{ $adaFilter ? 'Coba ubah atau reset filter.' : 'Semua urusan kontrak dan aktivasi sedang beres.' }}
                    </p>
                    @if ($adaFilter)
                        <a href="{{ route('notifikasi.index') }}" class="mt-3 inline-block text-sm font-semibold text-[#527D82] hover:underline">Reset filter</a>
                    @endif
                </div>
            @endforelse
        </div>
    </section>
</div>
@endsection
