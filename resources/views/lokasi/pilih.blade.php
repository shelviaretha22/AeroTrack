@extends('layouts.app')

@section('page-title', 'Pilih Lokasi')

@section('content')
<div class="w-full space-y-6">

    {{-- Header --}}
    <div>
        <p class="text-sm text-[#8BA5A2]">Management / Kerja Sama / Isi Lokasi</p>
        <h1 class="mt-1 text-2xl font-bold text-[#365F65]">Pilih Lokasi Tenant</h1>
        <p class="mt-1 text-sm text-[#7A9290]">
            Cari dan pilih lokasi yang sudah terdaftar untuk kerja sama ini.
        </p>
    </div>

    {{-- Informasi kerja sama --}}
    <div class="rounded-2xl border border-[#E2EBE9] bg-white p-5">
        <p class="text-sm text-[#8BA5A2]">Kerja Sama</p>
        <p class="mt-1 font-bold text-[#365F65]">
            {{ $kerjaSama->nama_kerja_sama ?? $kerjaSama->nama ?? 'Kerja Sama #' . $kerjaSama->id }}
        </p>
        <p class="mt-2 text-sm text-[#7A9290]">
            Tenant:
            {{ $tenant->nama_tenant ?? $tenant->nama ?? $tenant->mitra_usaha ?? 'Tenant #' . $tenant->id }}
        </p>
    </div>

    {{-- Pesan --}}
    @if ($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 p-4">
            @foreach ($errors->all() as $error)
                <p class="text-sm text-red-600">{{ $error }}</p>
            @endforeach
        </div>
    @endif

    @if (session('error'))
        <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            {{ session('error') }}
        </div>
    @endif

    @if (session('success'))
        <div class="rounded-xl border border-green-200 bg-green-50 p-4 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    {{-- Pencarian dan filter --}}
    <div class="rounded-2xl border border-[#E2EBE9] bg-white p-5">
        <div class="mb-4">
            <h2 class="font-bold text-[#365F65]">Cari Lokasi</h2>
            <p class="mt-1 text-sm text-[#7A9290]">
                Gunakan pencarian atau filter untuk menemukan ruangan yang sesuai.
            </p>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">

            <div class="sm:col-span-2">
                <label for="cariLokasi" class="mb-2 block text-sm font-medium text-[#527D82]">
                    Pencarian
                </label>
                <input
                    id="cariLokasi"
                    type="text"
                    placeholder="Cari kode, nama lokasi, atau nomor RO..."
                    class="w-full rounded-xl border border-[#DCE6E4] px-4 py-3 text-sm focus:border-[#527D82] focus:outline-none focus:ring-2 focus:ring-[#527D82]/20"
                >
            </div>

            <div>
                <label for="filterTerminal" class="mb-2 block text-sm font-medium text-[#527D82]">
                    Terminal
                </label>
                <select
                    id="filterTerminal"
                    class="w-full rounded-xl border border-[#DCE6E4] bg-white px-4 py-3 text-sm focus:border-[#527D82] focus:outline-none"
                >
                    <option value="">Semua Terminal</option>
                    @foreach ($lokasis->pluck('terminal')->filter()->unique()->sort() as $terminal)
                        <option value="{{ $terminal }}">{{ $terminal }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="filterLantai" class="mb-2 block text-sm font-medium text-[#527D82]">
                    Lantai
                </label>
                <select
                    id="filterLantai"
                    class="w-full rounded-xl border border-[#DCE6E4] bg-white px-4 py-3 text-sm focus:border-[#527D82] focus:outline-none"
                >
                    <option value="">Semua Lantai</option>
                    @foreach ($lokasis->pluck('lantai')->filter()->unique()->sort() as $lantai)
                        <option value="{{ $lantai }}">
                            {{ is_numeric($lantai) ? 'Lantai ' . $lantai : $lantai }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="filterJenis" class="mb-2 block text-sm font-medium text-[#527D82]">
                    Jenis Ruangan
                </label>
                <select
                    id="filterJenis"
                    class="w-full rounded-xl border border-[#DCE6E4] bg-white px-4 py-3 text-sm focus:border-[#527D82] focus:outline-none"
                >
                    <option value="">Semua Jenis</option>
                    @foreach ($lokasis->pluck('jenis_ruangan')->filter()->unique()->sort() as $jenis)
                        <option value="{{ $jenis }}">{{ $jenis }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="filterStatus" class="mb-2 block text-sm font-medium text-[#527D82]">
                    Status
                </label>
                <select
                    id="filterStatus"
                    class="w-full rounded-xl border border-[#DCE6E4] bg-white px-4 py-3 text-sm focus:border-[#527D82] focus:outline-none"
                >
                    <option value="">Semua Status</option>
                    <option value="Kosong">Kosong</option>
                    <option value="Terisi">Terisi</option>
                </select>
            </div>
        </div>

        <div class="mt-4 flex flex-wrap items-center gap-3">
            <button
                type="button"
                id="resetFilter"
                class="rounded-xl border border-[#DCE6E4] px-5 py-2.5 text-sm font-semibold text-[#527D82] hover:bg-[#F4F8F7]"
            >
                Reset Filter
            </button>
            <p id="jumlahHasil" class="text-sm text-[#7A9290]"></p>
        </div>
    </div>

    {{-- Daftar lokasi --}}
    <div class="overflow-hidden rounded-2xl border border-[#E2EBE9] bg-white">

        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-[#E2EBE9] p-5">
            <div>
                <h2 class="font-bold text-[#365F65]">Daftar Lokasi</h2>
                <p class="mt-1 text-sm text-[#7A9290]">
                    Pilih lokasi kosong untuk menghubungkannya dengan kerja sama ini.
                </p>
            </div>
            <span class="rounded-full bg-[#EAF4EF] px-3 py-1.5 text-xs font-semibold text-[#287451]">
                Lokasi tersedia
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[900px] text-left text-sm">
                <thead class="bg-[#F4F8F7] text-[#527D82]">
                    <tr>
                        <th class="px-5 py-4 font-semibold">Kode Ruang</th>
                        <th class="px-5 py-4 font-semibold">Terminal</th>
                        <th class="px-5 py-4 font-semibold">Lantai</th>
                        <th class="px-5 py-4 font-semibold">Jenis Ruangan</th>
                        <th class="px-5 py-4 font-semibold">Nama Lokasi</th>
                        <th class="px-5 py-4 font-semibold">Nomor RO</th>
                        <th class="px-5 py-4 font-semibold">Luas</th>
                        <th class="px-5 py-4 font-semibold">Status</th>
                        <th class="px-5 py-4 text-center font-semibold">Aksi</th>
                    </tr>
                </thead>

                <tbody id="daftarLokasi" class="divide-y divide-[#E2EBE9]">
                    @forelse ($lokasis as $item)
                        <tr
                            class="baris-lokasi hover:bg-[#FAFCFB]"
                            data-cari="{{ strtolower($item->kode_ruang . ' ' . $item->lokasi . ' ' . ($item->nomor_ro ?? '')) }}"
                            data-terminal="{{ $item->terminal }}"
                            data-lantai="{{ $item->lantai }}"
                            data-jenis="{{ $item->jenis_ruangan }}"
                            data-status="{{ $item->status }}"
                        >
                            <td class="px-5 py-4 font-semibold text-[#365F65]">
                                {{ $item->kode_ruang }}
                            </td>

                            <td class="px-5 py-4 text-[#527D82]">
                                {{ $item->terminal }}
                            </td>

                            <td class="px-5 py-4 text-[#527D82]">
                                {{ is_numeric($item->lantai) ? str_pad((string) $item->lantai, 2, '0', STR_PAD_LEFT) : ($item->lantai ?: '-') }}
                            </td>

                            <td class="px-5 py-4 text-[#527D82]">
                                {{ $item->jenis_ruangan ?: '-' }}
                            </td>

                            <td class="px-5 py-4 text-[#527D82]">
                                {{ $item->lokasi }}
                            </td>

                            <td class="px-5 py-4 text-[#527D82]">
                                {{ $item->nomor_ro ?: '-' }}
                            </td>

                            <td class="px-5 py-4 text-[#527D82]">
                                {{ $item->luas_area !== null ? $item->luas_area . ' m²' : '-' }}
                            </td>

                            <td class="px-5 py-4">
                                @if ($item->status === 'Kosong')
                                    <span class="rounded-full bg-green-50 px-3 py-1 text-xs font-semibold text-green-700">
                                        Kosong
                                    </span>
                                @else
                                    <span class="rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-700">
                                        {{ $item->status }}
                                    </span>
                                @endif
                            </td>

                            <td class="px-5 py-4 text-center">
                                @if ($item->status === 'Kosong' && !$item->tenant_id)
                                    <form
                                        method="POST"
                                        action="{{ route('lokasi.simpan-pilihan', $kerjaSama) }}"
                                        onsubmit="return confirm('Pilih lokasi {{ $item->kode_ruang }} untuk kerja sama ini?')"
                                    >
                                        @csrf
                                        <input type="hidden" name="lokasi_id" value="{{ $item->id }}">
                                        <button
                                            type="submit"
                                            class="rounded-lg bg-[#365F65] px-4 py-2 font-semibold text-white transition hover:bg-[#294F55]"
                                        >
                                            Pilih Lokasi
                                        </button>
                                    </form>
                                @else
                                    <span class="text-xs text-[#8BA5A2]">Tidak tersedia</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-5 py-12 text-center">
                                <div class="mx-auto flex max-w-md flex-col items-center">
                                    <div class="mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-[#F4F8F7]">
                                        <svg
                                            class="h-6 w-6 text-[#8BA5A2]"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="1.7"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M3 21h18M5 21V7l8-4v18M19 21V11l-6-4M9 9v.01M9 12v.01M9 15v.01M9 18v.01"
                                            />
                                        </svg>
                                    </div>
                                    <h3 class="font-bold text-[#365F65]">
                                        Belum ada lokasi yang tersedia
                                    </h3>
                                    <p class="mt-2 text-sm text-[#7A9290]">
                                        Tidak ada lokasi kosong yang bisa dipilih saat ini.
                                        Tambahkan lokasi melalui menu Lokasi, lalu kembali ke halaman ini.
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div id="hasilTidakAda" class="hidden px-5 py-8 text-center text-sm text-[#7A9290]">
            Tidak ada lokasi yang cocok dengan pencarian atau filter.
        </div>
    </div>

    {{-- Kembali --}}
    <div>
        <a
            href="{{ route('kerja-sama') }}"
            class="inline-flex rounded-xl border border-[#DCE6E4] px-5 py-3 text-sm font-semibold text-[#527D82] transition hover:bg-white"
        >
            ← Kembali ke Kerja Sama
        </a>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const cari = document.getElementById('cariLokasi');
    const terminal = document.getElementById('filterTerminal');
    const lantai = document.getElementById('filterLantai');
    const jenis = document.getElementById('filterJenis');
    const status = document.getElementById('filterStatus');
    const reset = document.getElementById('resetFilter');
    const baris = Array.from(document.querySelectorAll('.baris-lokasi'));
    const jumlahHasil = document.getElementById('jumlahHasil');
    const hasilTidakAda = document.getElementById('hasilTidakAda');

    function terapkanFilter() {
        const kata = cari.value.trim().toLowerCase();
        let jumlah = 0;

        baris.forEach(function (row) {
            const cocok =
                row.dataset.cari.includes(kata) &&
                (!terminal.value || row.dataset.terminal === terminal.value) &&
                (!lantai.value || row.dataset.lantai === lantai.value) &&
                (!jenis.value || row.dataset.jenis === jenis.value) &&
                (!status.value || row.dataset.status === status.value);

            row.classList.toggle('hidden', !cocok);

            if (cocok) {
                jumlah++;
            }
        });

        jumlahHasil.textContent = jumlah + ' lokasi ditampilkan';
        hasilTidakAda.classList.toggle('hidden', jumlah > 0 || baris.length === 0);
    }

    [cari, terminal, lantai, jenis, status].forEach(function (elemen) {
        elemen.addEventListener('input', terapkanFilter);
        elemen.addEventListener('change', terapkanFilter);
    });

    reset.addEventListener('click', function () {
        cari.value = '';
        terminal.value = '';
        lantai.value = '';
        jenis.value = '';
        status.value = '';
        terapkanFilter();
    });

    terapkanFilter();
});
</script>
@endsection