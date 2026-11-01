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
                    <span class="inline-flex items-center gap-2 rounded-full bg-amber-50 px-4 py-2 text-sm font-semibold text-amber-700">
                        <span class="h-2 w-2 rounded-full bg-amber-500"></span> Terisi
                    </span>
                @else
                    <span class="inline-flex items-center gap-2 rounded-full bg-emerald-50 px-4 py-2 text-sm font-semibold text-emerald-700">
                        <span class="h-2 w-2 rounded-full bg-emerald-500"></span> {{ $lokasi->status ?? 'Kosong' }}
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
                            <a href="{{ route('tenant.show', $lokasi->tenant->id) }}" class="text-[#365F65] underline-offset-2 hover:underline">
                                {{ $lokasi->tenant->nama_tenant
                                    ?? $lokasi->tenant->nama
                                    ?? 'Tenant #' . $lokasi->tenant->id }}
                            </a>
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

            @if ($lokasi->status === 'Kosong' && $lokasi->tenant_id === null)
                <form action="{{ route('lokasi.destroy', $lokasi->id) }}" method="POST"
                      class="sm:ml-auto"
                      onsubmit="return confirm('Hapus lokasi {{ $lokasi->kode_ruang }}? Tindakan ini tidak dapat dibatalkan.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="inline-flex w-full items-center justify-center rounded-xl border border-red-200 bg-white px-5 py-3 text-sm font-semibold text-red-600 transition hover:bg-red-50 sm:w-auto">
                        Hapus Lokasi
                    </button>
                </form>
            @else
                <p class="text-xs text-[#8BA5A2] sm:ml-auto">Lokasi yang terisi tidak dapat dihapus.</p>
            @endif

        </div>
    </div>

    {{-- Posisi di lantai + masa berlaku kerja sama --}}
    @php
        $jumlahLantai = $ruanganSelantai->count();
        $terisiLantai = $ruanganSelantai->where('status', 'Terisi')->count();
        $persenLantai = $jumlahLantai > 0 ? round(($terisiLantai / $jumlahLantai) * 100) : 0;

        $kerjaSamaLokasi = $lokasi->tenant?->kerjaSama;
        $mulai = $kerjaSamaLokasi?->tanggal_mulai;
        $berakhir = $kerjaSamaLokasi?->tanggal_berakhir;
        $adaMasaBerlaku = $mulai && $berakhir && $berakhir->gt($mulai);

        if ($adaMasaBerlaku) {
            $totalHari = max(1, $mulai->diffInDays($berakhir));
            $hariBerjalan = max(0, min($totalHari, $mulai->diffInDays(now(), false)));
            $persenBerjalan = round(($hariBerjalan / $totalHari) * 100);
            $sisaHari = (int) floor(now()->startOfDay()->diffInDays($berakhir->copy()->startOfDay(), false));
            $warnaMasa = $sisaHari < 0 ? 'bg-red-500' : ($sisaHari <= 90 ? 'bg-amber-500' : 'bg-[#365F65]');
        }
    @endphp

    <div class="grid w-full grid-cols-1 gap-6 xl:grid-cols-5">

        {{-- Mini denah lantai --}}
        <div class="rounded-2xl border border-[#E2EBE9] bg-white p-5 sm:p-6 xl:col-span-3">
            <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h3 class="font-bold text-[#365F65]">
                        Posisi di {{ $lokasi->terminal ?: 'Terminal' }}
                        @if ($lokasi->lantai) · Lantai {{ $lokasi->lantai }} @endif
                    </h3>
                    <p class="mt-1 text-sm text-[#7A9290]">Ruangan yang bergaris tebal adalah lokasi ini.</p>
                </div>
                <span class="text-sm font-semibold text-[#527D82]">{{ $terisiLantai }}/{{ $jumlahLantai }} terisi · {{ $persenLantai }}%</span>
            </div>

            <div class="mt-4 h-2 overflow-hidden rounded-full bg-[#E7EFEC]">
                <div class="h-full rounded-full bg-amber-500" style="width: {{ $persenLantai }}%"></div>
            </div>

            <div class="mt-5 flex flex-wrap gap-1.5">
                @foreach ($ruanganSelantai as $r)
                    @php
                        $ini = $r->id === $lokasi->id;
                        $isi = $r->status === 'Terisi';
                    @endphp
                    <a href="{{ route('lokasi.show', $r->id) }}"
                       title="{{ $r->kode_ruang }} · {{ $r->jenis_ruangan ?: 'Tanpa jenis' }} · {{ $isi ? 'Terisi' : 'Kosong' }}"
                       @if ($ini) aria-current="true" @endif
                       class="flex h-9 min-w-10 items-center justify-center rounded-md px-1.5 text-[11px] font-bold tabular-nums transition hover:-translate-y-0.5 hover:shadow
                              {{ $isi ? 'bg-amber-500 text-white' : 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-300' }}
                              {{ $ini ? 'ring-2 ring-offset-2 ring-[#365F65] scale-110' : '' }}">
                        {{ last(explode('-', $r->kode_ruang)) }}
                    </a>
                @endforeach
            </div>

            <div class="mt-5 flex flex-wrap items-center gap-x-5 gap-y-2 text-xs text-[#7A9290]">
                <span class="inline-flex items-center gap-2"><span class="h-3.5 w-4 rounded bg-amber-500"></span> Terisi</span>
                <span class="inline-flex items-center gap-2"><span class="h-3.5 w-4 rounded bg-emerald-50 ring-1 ring-emerald-300"></span> Kosong</span>
                <a href="{{ route('lokasi.index', ['terminal' => $lokasi->terminal, 'lantai' => $lokasi->lantai]) }}"
                   class="ml-auto font-semibold text-[#527D82] hover:underline">Lihat semua di lantai ini →</a>
            </div>
        </div>

        {{-- Masa berlaku kerja sama --}}
        <div class="rounded-2xl border border-[#E2EBE9] bg-white p-5 sm:p-6 xl:col-span-2">
            <h3 class="font-bold text-[#365F65]">Masa Berlaku Kerja Sama</h3>

            @if ($lokasi->tenant && $adaMasaBerlaku)
                <p class="mt-1 text-sm text-[#7A9290]">{{ $lokasi->tenant->nama_tenant ?? $lokasi->tenant->mitra_usaha ?? 'Tenant' }}</p>

                <div class="mt-6">
                    <div class="flex items-end justify-between gap-3">
                        <div>
                            <p class="text-3xl font-bold {{ $sisaHari < 0 ? 'text-red-600' : 'text-[#365F65]' }}">
                                {{ $sisaHari < 0 ? abs($sisaHari) : $sisaHari }}
                                <span class="text-sm font-semibold text-[#7A9290]">hari</span>
                            </p>
                            <p class="text-xs text-[#8BA5A2]">{{ $sisaHari < 0 ? 'sejak masa berlaku berakhir' : 'tersisa' }}</p>
                        </div>
                        <span class="text-sm font-semibold text-[#527D82]">{{ $persenBerjalan }}% berjalan</span>
                    </div>

                    <div class="mt-4 h-3 overflow-hidden rounded-full bg-[#E7EFEC]"
                         role="img" aria-label="Masa berlaku berjalan {{ $persenBerjalan }} persen">
                        <div class="h-full rounded-full {{ $warnaMasa }}" style="width: {{ $persenBerjalan }}%"></div>
                    </div>

                    <div class="mt-2 flex justify-between text-xs text-[#8BA5A2]">
                        <span>{{ $mulai->format('d M Y') }}</span>
                        <span>{{ $berakhir->format('d M Y') }}</span>
                    </div>
                </div>

                <a href="{{ route('aktivasi.show', $kerjaSamaLokasi->id) }}"
                   class="mt-6 inline-flex text-sm font-semibold text-[#527D82] hover:underline">Buka status aktivasi →</a>
            @elseif ($lokasi->tenant)
                <p class="mt-5 rounded-xl bg-[#F8FAF9] px-4 py-8 text-center text-sm text-[#7A9290]">
                    Tanggal mulai dan berakhir kerja sama belum diisi.
                </p>
            @else
                <p class="mt-5 rounded-xl bg-[#F8FAF9] px-4 py-8 text-center text-sm text-[#7A9290]">
                    Lokasi ini belum ditempati, jadi belum ada masa berlaku.
                </p>
            @endif
        </div>
    </div>
</div>
@endsection