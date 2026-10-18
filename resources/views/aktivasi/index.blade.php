@extends('layouts.app')

@section('page-title', 'Aktivasi')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-sm text-gray-500">Management / Aktivasi</p>
            <h1 class="mt-1 text-2xl font-bold text-gray-800">
                Monitoring Aktivasi
            </h1>
            <p class="mt-1 text-sm text-gray-500">
                Monitoring pengajuan aktivasi setelah dokumen Berita Acara tersedia.
            </p>
        </div>
    </div>

    {{-- Notifikasi --}}
    @if (session('success'))
        <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <p class="font-semibold">Data belum berhasil disimpan.</p>
            <ul class="mt-2 list-inside list-disc">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Statistik --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">

        <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
            <p class="text-sm text-gray-500">Total Kerja Sama Siap Aktivasi</p>
            <div class="mt-3 flex items-center justify-between">
                <h2 class="text-3xl font-bold text-gray-800">{{ $totalAktivasi }}</h2>
                <span class="rounded-xl bg-gray-100 p-3 text-gray-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h7l5 5v11a2 2 0 0 1-2 2Z"/>
                    </svg>
                </span>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
            <p class="text-sm text-gray-500">Menunggu Keputusan</p>
            <div class="mt-3 flex items-center justify-between">
                <h2 class="text-3xl font-bold text-amber-600">{{ $menunggu }}</h2>
                <span class="rounded-xl bg-amber-50 p-3 text-amber-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="9" stroke-width="1.8"/>
                        <path stroke-linecap="round" stroke-width="1.8" d="M12 7v5l3 2"/>
                    </svg>
                </span>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
            <p class="text-sm text-gray-500">Disetujui</p>
            <div class="mt-3 flex items-center justify-between">
                <h2 class="text-3xl font-bold text-green-600">{{ $disetujui }}</h2>
                <span class="rounded-xl bg-green-50 p-3 text-green-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m5 12 4 4L19 6"/>
                    </svg>
                </span>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
            <p class="text-sm text-gray-500">Ditolak</p>
            <div class="mt-3 flex items-center justify-between">
                <h2 class="text-3xl font-bold text-red-600">{{ $ditolak }}</h2>
                <span class="rounded-xl bg-red-50 p-3 text-red-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m7 7 10 10M17 7 7 17"/>
                    </svg>
                </span>
            </div>
        </div>

    </div>

    {{-- Daftar Aktivasi --}}
    <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">

        <div class="border-b border-gray-100 p-5">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h2 class="text-lg font-bold text-gray-800">Daftar Pengajuan Aktivasi</h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Kerja sama yang sudah memiliki dokumen BA.
                    </p>
                </div>

                <form method="GET" action="{{ route('aktivasi') }}"
                    class="flex flex-col gap-3 sm:flex-row">

                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari mitra, brand, nomor..."
                        class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm outline-none focus:border-teal-700 focus:ring-2 focus:ring-teal-100 sm:w-64">

                    <select name="status"
                        class="rounded-xl border border-gray-200 px-4 py-2.5 text-sm outline-none focus:border-teal-700 focus:ring-2 focus:ring-teal-100">
                        <option value="">Semua Status</option>
                        @foreach (['Belum Diajukan', 'Menunggu Keputusan', 'Disetujui', 'Ditolak'] as $pilihanStatus)
                            <option value="{{ $pilihanStatus }}" @selected(request('status') === $pilihanStatus)>
                                {{ $pilihanStatus }}
                            </option>
                        @endforeach
                    </select>

                    <button type="submit"
                        class="rounded-xl bg-[#365F65] px-5 py-2.5 text-sm font-semibold text-white hover:bg-[#294b50]">
                        Cari
                    </button>

                    @if (request()->hasAny(['search', 'status']))
                        <a href="{{ route('aktivasi') }}"
                            class="rounded-xl border border-gray-200 px-4 py-2.5 text-center text-sm font-medium text-gray-600 hover:bg-gray-50">
                            Reset
                        </a>
                    @endif
                </form>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[1100px] text-left text-sm">
                <thead class="bg-[#EEF6F3] text-xs uppercase tracking-wide text-[#365F65]">
                    <tr>
                        <th class="px-5 py-4 font-semibold">No.</th>
                        <th class="px-5 py-4 font-semibold">Nomor Pengajuan</th>
                        <th class="px-5 py-4 font-semibold">Mitra / Brand</th>
                        <th class="px-5 py-4 font-semibold">Tanggal Pengajuan</th>
                        <th class="px-5 py-4 font-semibold">Status</th>
                        <th class="px-5 py-4 font-semibold">Tanggal Keputusan</th>
                        <th class="px-5 py-4 font-semibold">Dokumen</th>
                        <th class="px-5 py-4 text-center font-semibold">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    @forelse ($dataAktivasi as $index => $kerjaSama)
                        @php
                            $aktivasi = $kerjaSama->aktivasi;
                            $status = $aktivasi?->status ?? 'Belum Diajukan';

                            $badgeClass = match ($status) {
                                'Disetujui' => 'bg-green-50 text-green-700',
                                'Ditolak' => 'bg-red-50 text-red-700',
                                'Menunggu Keputusan' => 'bg-amber-50 text-amber-700',
                                default => 'bg-gray-100 text-gray-600',
                            };
                        @endphp

                        <tr class="transition hover:bg-gray-50/70">
                            <td class="px-5 py-4 text-gray-500">{{ $index + 1 }}</td>

                            <td class="px-5 py-4">
                                <p class="font-semibold text-gray-800">
                                    {{ $aktivasi?->nomor_pengajuan ?? 'Belum dibuat' }}
                                </p>
                                <p class="mt-1 text-xs text-gray-400">
                                    KS-{{ str_pad($kerjaSama->id, 4, '0', STR_PAD_LEFT) }}
                                </p>
                            </td>

                            <td class="px-5 py-4">
                                <p class="font-semibold text-gray-800">
                                    {{ $kerjaSama->mitra_usaha ?: '-' }}
                                </p>
                                <p class="mt-1 text-xs text-gray-500">
                                    {{ $kerjaSama->brand ?: $kerjaSama->tenant?->nama_tenant ?: '-' }}
                                </p>
                            </td>

                            <td class="px-5 py-4 text-gray-600">
                                {{ $aktivasi?->tanggal_pengajuan?->format('d M Y') ?? '-' }}
                            </td>

                            <td class="px-5 py-4">
                                <span class="inline-flex whitespace-nowrap rounded-full px-3 py-1.5 text-xs font-semibold {{ $badgeClass }}">
                                    {{ $status }}
                                </span>
                            </td>

                            <td class="px-5 py-4 text-gray-600">
                                {{ $aktivasi?->tanggal_keputusan?->format('d M Y') ?? '-' }}
                            </td>

                            <td class="px-5 py-4">
                                <div class="flex flex-col items-start gap-2">
                                    @if ($aktivasi?->surat_pengajuan_path)
                                        <a href="{{ route('aktivasi.surat', $aktivasi->id) }}"
                                            target="_blank" rel="noopener"
                                            class="text-xs font-semibold text-[#365F65] underline">
                                            Lihat surat pengajuan
                                        </a>
                                    @endif

                                    @if ($aktivasi?->dokumen_path)
                                        <a href="{{ route('aktivasi.dokumen-keputusan', $aktivasi->id) }}"
                                            class="text-xs font-semibold text-green-700 underline">
                                            Unduh keputusan
                                        </a>
                                    @endif

                                    @if (!$aktivasi?->surat_pengajuan_path && !$aktivasi?->dokumen_path)
                                        <span class="text-xs text-gray-400">Belum ada dokumen</span>
                                    @endif
                                </div>
                            </td>

                            <td class="px-5 py-4 text-center">
                                @if (!$aktivasi || $status === 'Ditolak')
                                    <form method="POST"
                                        action="{{ route('aktivasi.request', $kerjaSama->id) }}"
                                        onsubmit="return confirm('{{ $status === 'Ditolak' ? 'Buat pengajuan aktivasi baru? Riwayat pengajuan sebelumnya tetap disimpan.' : 'Buat pengajuan aktivasi untuk kerja sama ini?' }}')">
                                        @csrf
                                        <button type="submit"
                                            class="whitespace-nowrap rounded-lg bg-[#365F65] px-3 py-2 text-xs font-semibold text-white transition hover:bg-[#294b50]">
                                            {{ $status === 'Ditolak' ? 'Ajukan Ulang' : 'Request Aktivasi' }}
                                        </button>
                                    </form>

                                @elseif ($status === 'Menunggu Keputusan')
                                    <button type="button"
                                        onclick="document.getElementById('keputusan-modal-{{ $aktivasi->id }}').showModal()"
                                        class="whitespace-nowrap rounded-lg border border-[#365F65] px-3 py-2 text-xs font-semibold text-[#365F65] transition hover:bg-[#EEF6F3]">
                                        Catat Keputusan
                                    </button>

                                @elseif ($status === 'Disetujui')
                                    <span class="inline-flex whitespace-nowrap rounded-lg bg-green-50 px-3 py-2 text-xs font-semibold text-green-700">
                                        Keputusan terkunci
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-14 text-center">
                                <p class="font-semibold text-gray-700">
                                    Data aktivasi belum ditemukan
                                </p>
                                <p class="mt-1 text-sm text-gray-500">
                                    Pastikan dokumen BA tersedia atau ubah kata kunci pencarian.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="flex flex-col gap-2 border-t border-gray-100 px-5 py-4 text-sm text-gray-500 sm:flex-row sm:items-center sm:justify-between">
            <p>Menampilkan {{ $dataAktivasi->count() }} kerja sama</p>
            <p>Dokumen BA menjadi prasyarat pengajuan aktivasi.</p>
        </div>
    </div>

    {{-- Modal pencatatan keputusan --}}
    @foreach ($dataAktivasi as $kerjaSama)
        @php
            $aktivasi = $kerjaSama->aktivasi;
        @endphp

        @if ($aktivasi && $aktivasi->status === 'Menunggu Keputusan')
            <dialog id="keputusan-modal-{{ $aktivasi->id }}"
                class="w-[calc(100%-2rem)] max-w-xl rounded-2xl p-0 shadow-2xl backdrop:bg-gray-900/40">

                <form method="POST"
                    action="{{ route('aktivasi.keputusan', $aktivasi->id) }}"
                    enctype="multipart/form-data">
                    @csrf

                    <div class="flex items-start justify-between border-b border-gray-100 p-5 sm:p-6">
                        <div>
                            <h2 class="text-lg font-bold text-gray-800">Catat Keputusan Aktivasi</h2>
                            <p class="mt-1 text-sm text-gray-500">
                                {{ $aktivasi->nomor_pengajuan }}
                            </p>
                        </div>

                        <button type="button"
                            onclick="document.getElementById('keputusan-modal-{{ $aktivasi->id }}').close()"
                            class="rounded-lg px-3 py-1 text-xl text-gray-400 hover:bg-gray-100"
                            aria-label="Tutup">
                            &times;
                        </button>
                    </div>

                    <div class="max-h-[65vh] space-y-5 overflow-y-auto p-5 sm:p-6">
                        <div class="rounded-xl bg-amber-50 p-4 text-sm text-amber-800">
                            Masukkan keputusan sesuai dokumen resmi yang diterima dari kantor pusat.
                            Status tidak dapat diedit kembali setelah disimpan.
                        </div>

                        <div>
                            <label for="status-{{ $aktivasi->id }}"
                                class="mb-2 block text-sm font-semibold text-gray-700">
                                Hasil Keputusan <span class="text-red-500">*</span>
                            </label>
                            <select id="status-{{ $aktivasi->id }}" name="status" required
                                class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100">
                                <option value="">Pilih keputusan</option>
                                <option value="Disetujui">Disetujui</option>
                                <option value="Ditolak">Ditolak</option>
                            </select>
                        </div>

                        <div>
                            <label for="tanggal-keputusan-{{ $aktivasi->id }}"
                                class="mb-2 block text-sm font-semibold text-gray-700">
                                Tanggal Keputusan <span class="text-red-500">*</span>
                            </label>
                            <input id="tanggal-keputusan-{{ $aktivasi->id }}"
                                type="date" name="tanggal_keputusan" required
                                max="{{ now()->toDateString() }}"
                                class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100">
                        </div>

                        <div>
                            <label for="dokumen-keputusan-{{ $aktivasi->id }}"
                                class="mb-2 block text-sm font-semibold text-gray-700">
                                Dokumen Keputusan Resmi <span class="text-red-500">*</span>
                            </label>
                            <input id="dokumen-keputusan-{{ $aktivasi->id }}"
                                type="file" name="dokumen_keputusan" required
                                accept=".pdf,.jpg,.jpeg,.png"
                                class="block w-full rounded-xl border border-gray-200 p-3 text-sm file:mr-3 file:rounded-lg file:border-0 file:bg-[#EEF6F3] file:px-3 file:py-2 file:font-semibold file:text-[#365F65]">
                            <p class="mt-1 text-xs text-gray-500">
                                PDF, JPG, JPEG, atau PNG. Maksimal 5 MB.
                            </p>
                        </div>

                        <div>
                            <label for="catatan-{{ $aktivasi->id }}"
                                class="mb-2 block text-sm font-semibold text-gray-700">
                                Catatan
                            </label>
                            <textarea id="catatan-{{ $aktivasi->id }}"
                                name="catatan" rows="3" maxlength="2000"
                                placeholder="Tambahkan catatan keputusan jika diperlukan..."
                                class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100"></textarea>
                        </div>
                    </div>

                    <div class="flex flex-col-reverse gap-3 border-t border-gray-100 p-5 sm:flex-row sm:justify-end sm:p-6">
                        <button type="button"
                            onclick="document.getElementById('keputusan-modal-{{ $aktivasi->id }}').close()"
                            class="rounded-xl border border-gray-200 px-5 py-3 text-sm font-semibold text-gray-600 hover:bg-gray-50">
                            Batal
                        </button>

                        <button type="submit"
                            onclick="return confirm('Pastikan keputusan dan dokumen resmi sudah benar. Keputusan tidak dapat diedit setelah disimpan.');"
                            class="rounded-xl bg-[#365F65] px-5 py-3 text-sm font-semibold text-white hover:bg-[#294b50]">
                            Simpan Keputusan
                        </button>
                    </div>
                </form>
            </dialog>
        @endif
    @endforeach

</div>
@endsection