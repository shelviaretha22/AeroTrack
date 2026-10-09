@extends('layouts.app')

@section('page-title', 'Detail Lokasi')

@section('content')
<div class="w-full space-y-6">

    {{-- Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-sm text-[#8BA5A2]">Management / Lokasi / Detail</p>

            <h1 class="mt-1 text-2xl font-bold text-[#365F65]">
                Detail Lokasi
            </h1>

            <p class="mt-1 text-sm text-[#7A9290]">
                Informasi lengkap ruangan bandara.
            </p>
        </div>

        <a href="{{ route('lokasi.edit', $lokasi) }}"
           class="inline-flex items-center justify-center rounded-xl
                  bg-[#365F65] px-5 py-3 text-sm font-semibold text-white
                  transition hover:bg-[#294F55]">
            Edit Lokasi
        </a>
    </div>

    {{-- Informasi utama --}}
    <div class="w-full rounded-2xl border border-[#E2EBE9] bg-white p-5 sm:p-8">

        <div class="flex flex-col gap-4 border-b border-[#E2EBE9] pb-6 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-[#7A9290]">Kode Ruang</p>

                <h2 class="mt-1 break-words text-2xl font-bold text-[#365F65] sm:text-3xl">
                    {{ $lokasi->kode_tampilan ?? $lokasi->kode_ruang ?? '-' }}
                </h2>

                @if ($lokasi->tenant_id)
                    <p class="mt-2 text-xs text-[#8BA5A2]">
                        Kode tampilan berdasarkan jenis usaha tenant
                    </p>
                @endif
            </div>

            <div>
                @if ($lokasi->status === 'Terisi')
                    <span class="inline-flex rounded-full bg-blue-50 px-4 py-2 text-sm font-semibold text-blue-700">
                        Terisi
                    </span>
                @else
                    <span class="inline-flex rounded-full bg-emerald-50 px-4 py-2 text-sm font-semibold text-emerald-700">
                        {{ $lokasi->status ?? 'Kosong' }}
                    </span>
                @endif
            </div>
        </div>

        {{-- Detail ruangan --}}
        <div class="mt-6">
            <h3 class="mb-5 text-base font-bold text-[#365F65]">
                Informasi Ruangan
            </h3>

            <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">

                {{-- Kode ruang asli --}}
                <div class="min-w-0 rounded-xl border border-[#E2EBE9] bg-[#F8FAF9] p-4">
                    <dt class="text-sm text-[#8BA5A2]">Kode Ruang Asli</dt>
                    <dd class="mt-2 break-words font-semibold text-[#365F65]">
                        {{ $lokasi->kode_ruang ?? '-' }}
                    </dd>
                </div>

                {{-- Terminal --}}
                <div class="min-w-0 rounded-xl border border-[#E2EBE9] bg-[#F8FAF9] p-4">
                    <dt class="text-sm text-[#8BA5A2]">Terminal</dt>
                    <dd class="mt-2 font-semibold text-[#365F65]">
                        {{ $lokasi->terminal ?? '-' }}
                    </dd>
                </div>

                {{-- Lantai --}}
                <div class="min-w-0 rounded-xl border border-[#E2EBE9] bg-[#F8FAF9] p-4">
                    <dt class="text-sm text-[#8BA5A2]">Lantai</dt>
                    <dd class="mt-2 font-semibold text-[#365F65]">
                        {{ $lokasi->lantai ? 'Lantai '.$lokasi->lantai : '-' }}
                    </dd>
                </div>

                {{-- Jenis ruangan --}}
                <div class="min-w-0 rounded-xl border border-[#E2EBE9] bg-[#F8FAF9] p-4">
                    <dt class="text-sm text-[#8BA5A2]">Jenis Ruangan</dt>
                    <dd class="mt-2 break-words font-semibold text-[#365F65]">
                        {{ $lokasi->jenis_ruangan ?: '-' }}
                    </dd>
                </div>

                {{-- Nomor RO --}}
                <div class="min-w-0 rounded-xl border border-[#E2EBE9] bg-[#F8FAF9] p-4">
                    <dt class="text-sm text-[#8BA5A2]">Nomor RO</dt>
                    <dd class="mt-2 break-words font-semibold text-[#365F65]">
                        {{ $lokasi->nomor_ro ?: '-' }}
                    </dd>
                </div>

                {{-- Luas area --}}
                <div class="min-w-0 rounded-xl border border-[#E2EBE9] bg-[#F8FAF9] p-4">
                    <dt class="text-sm text-[#8BA5A2]">Luas Area</dt>
                    <dd class="mt-2 font-semibold text-[#365F65]">
                        {{ $lokasi->luas_area !== null
                            ? number_format((float) $lokasi->luas_area, 2, ',', '.') . ' m²'
                            : '-' }}
                    </dd>
                </div>

                {{-- Nama lokasi --}}
                <div class="min-w-0 rounded-xl border border-[#E2EBE9] bg-[#F8FAF9] p-4 sm:col-span-2 xl:col-span-3">
                    <dt class="text-sm text-[#8BA5A2]">Nama / Area Lokasi</dt>
                    <dd class="mt-2 break-words font-semibold text-[#365F65]">
                        {{ $lokasi->lokasi ?: '-' }}
                    </dd>
                </div>

                {{-- Tenant --}}
                <div class="min-w-0 rounded-xl border border-[#E2EBE9] bg-[#F8FAF9] p-4 sm:col-span-2 xl:col-span-3">
                    <dt class="text-sm text-[#8BA5A2]">Tenant yang Menempati</dt>
                    <dd class="mt-2 break-words font-semibold text-[#365F65]">
                        @if ($lokasi->tenant)
                            {{ $lokasi->tenant->nama_tenant
                                ?? $lokasi->tenant->nama
                                ?? 'Tenant #' . $lokasi->tenant->id }}
                        @else
                            Belum ada tenant
                        @endif
                    </dd>
                </div>

                {{-- Catatan --}}
                <div class="min-w-0 rounded-xl border border-[#E2EBE9] bg-[#F8FAF9] p-4 sm:col-span-2 xl:col-span-3">
                    <dt class="text-sm text-[#8BA5A2]">Keterangan</dt>
                    <dd class="mt-2 whitespace-pre-line break-words text-[#365F65]">
                        {{ $lokasi->catatan ?: '-' }}
                    </dd>
                </div>

                {{-- Tanggal dibuat --}}
                <div class="min-w-0 rounded-xl border border-[#E2EBE9] bg-[#F8FAF9] p-4">
                    <dt class="text-sm text-[#8BA5A2]">Dibuat</dt>
                    <dd class="mt-2 break-words text-sm font-medium text-[#365F65]">
                        {{ $lokasi->created_at?->format('d/m/Y H:i') ?? '-' }}
                    </dd>
                </div>

                {{-- Tanggal diperbarui --}}
                <div class="min-w-0 rounded-xl border border-[#E2EBE9] bg-[#F8FAF9] p-4">
                    <dt class="text-sm text-[#8BA5A2]">Terakhir Diperbarui</dt>
                    <dd class="mt-2 break-words text-sm font-medium text-[#365F65]">
                        {{ $lokasi->updated_at?->format('d/m/Y H:i') ?? '-' }}
                    </dd>
                </div>

            </dl>
        </div>

        {{-- Tombol bawah --}}
        <div class="mt-8 flex flex-col gap-3 border-t border-[#E2EBE9] pt-6 sm:flex-row sm:flex-wrap sm:items-center">

            <a href="{{ route('lokasi.index') }}"
               class="inline-flex items-center justify-center rounded-xl
                      border border-[#DCE6E4] bg-white px-5 py-3
                      text-sm font-semibold text-[#527D82]
                      transition hover:bg-[#F5F8F7]">
                Kembali ke Daftar Lokasi
            </a>

            <a href="{{ route('lokasi.edit', $lokasi) }}"
               class="inline-flex items-center justify-center rounded-xl
                      bg-[#365F65] px-5 py-3 text-sm font-semibold
                      text-white transition hover:bg-[#294F55]">
                Edit Lokasi
            </a>

        </div>
    </div>
</div>
@endsection