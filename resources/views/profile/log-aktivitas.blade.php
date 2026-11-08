@extends('layouts.app')

@section('page-title', 'Log Aktivitas')

@section('content')
<div class="w-full space-y-6">

    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-sm text-[#8BA5A2]">Profile / Log Aktivitas</p>
            <h1 class="mt-1 text-2xl font-bold text-[#365F65]">Log Aktivitas Akun</h1>
            <p class="mt-1 text-sm text-[#7A9290]">
                Catatan siapa melakukan apa, kapan, dan dari mana. Log hanya dapat ditambah, tidak dapat diubah atau dihapus.
            </p>
        </div>
        <a href="{{ route('profile.index') }}" class="text-sm font-semibold text-[#527D82] hover:underline">← Kembali ke Profile</a>
    </div>

    {{-- Ringkasan --}}
    <section class="grid grid-cols-1 gap-3 sm:grid-cols-3" aria-label="Ringkasan log">
        <div class="rounded-2xl border border-[#E2EBE9] bg-white p-5">
            <p class="text-sm text-[#8BA5A2]">Total catatan</p>
            <p class="mt-1 text-2xl font-bold text-[#365F65]">{{ $ringkas['total'] }}</p>
        </div>
        <div class="rounded-2xl border border-[#E2EBE9] bg-white p-5">
            <p class="text-sm text-[#8BA5A2]">7 hari terakhir</p>
            <p class="mt-1 text-2xl font-bold text-[#365F65]">{{ $ringkas['tujuhHari'] }}</p>
        </div>
        <div class="rounded-2xl border border-[#E2EBE9] bg-white p-5">
            <p class="text-sm text-[#8BA5A2]">Perlu diperhatikan</p>
            <p class="mt-1 text-2xl font-bold text-[#B96B60]">{{ $ringkas['peringatan'] }}</p>
            <p class="text-xs text-[#8BA5A2]">Login gagal, penguncian, ganti password, penghapusan</p>
        </div>
    </section>

    {{-- Filter --}}
    <form method="GET" action="{{ route('profile.log') }}"
          class="flex flex-wrap items-end gap-3 rounded-2xl border border-[#E2EBE9] bg-white p-4">
        <div>
            <label for="modul" class="block text-xs font-semibold text-[#527D82]">Modul</label>
            <select id="modul" name="modul" class="mt-1 rounded-xl border-[#E2EBE9] text-sm">
                <option value="">Semua modul</option>
                @foreach (\App\Models\AuditLog::MODUL as $m)
                    <option value="{{ $m }}" @selected($modul === $m)>{{ $m }}</option>
                @endforeach
            </select>
        </div>
        <label class="flex items-center gap-2 pb-2 text-sm text-[#365F65]">
            <input type="checkbox" name="level" value="peringatan" @checked($level === 'peringatan') class="rounded border-[#C9D6D3]">
            Hanya yang perlu diperhatikan
        </label>
        <button type="submit" class="rounded-xl bg-[#365F65] px-4 py-2 text-sm font-semibold text-white hover:opacity-90">Terapkan</button>
        @if ($modul || $level)
            <a href="{{ route('profile.log') }}" class="pb-2 text-sm font-semibold text-[#527D82] hover:underline">Reset</a>
        @endif
    </form>

    {{-- Daftar log --}}
    <section class="overflow-hidden rounded-2xl border border-[#E2EBE9] bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="bg-[#F3F6F5] text-xs font-semibold uppercase tracking-wide text-[#527D82]">
                    <tr>
                        <th class="px-4 py-3">Waktu</th>
                        <th class="px-4 py-3">Modul</th>
                        <th class="px-4 py-3">Aksi</th>
                        <th class="px-4 py-3">Keterangan</th>
                        <th class="px-4 py-3">IP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E2EBE9]">
                    @forelse ($log as $r)
                        <tr class="align-top">
                            <td class="whitespace-nowrap px-4 py-3 text-[#365F65]" title="{{ $r->created_at->format('d M Y H:i:s') }}">
                                {{ $r->created_at->format('d M Y H:i') }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="rounded-full bg-[#F3F6F5] px-2 py-0.5 text-xs font-medium text-[#527D82]">{{ $r->modul }}</span>
                            </td>
                            <td class="px-4 py-3 font-semibold {{ $r->level === 'peringatan' ? 'text-[#B96B60]' : 'text-[#365F65]' }}">
                                {{ $r->aksi }}
                            </td>
                            <td class="px-4 py-3 text-[#7A9290]">{{ $r->keterangan ?: '-' }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-[#7A9290]">{{ $r->ip_address ?: '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-[#7A9290]">Belum ada catatan untuk filter ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($log->hasPages())
            <div class="border-t border-[#E2EBE9] px-4 py-3">{{ $log->links() }}</div>
        @endif
    </section>
</div>
@endsection
