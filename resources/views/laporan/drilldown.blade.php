@extends('layouts.app')

@section('title', 'Detail Laporan')

@section('content')
<div class="w-full px-6 py-6">

    {{-- HEADER --}}
    <div class="mb-6">
        <a
            href="{{ route('laporan', ['tahun' => $tahun]) }}"
            class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-blue-600 mb-4"
        >
            <span>&larr;</span>
            Kembali ke Laporan
        </a>

        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">
                    {{ $judul }}
                </h1>

                <p class="mt-1 text-sm text-gray-500">
                    {{ $deskripsi }}
                </p>
            </div>

            <div class="text-sm text-gray-500">
                Periode: <span class="font-semibold text-gray-700">{{ $tahun }}</span>
            </div>
        </div>
    </div>

    {{-- JUMLAH DATA --}}
    <div class="mb-5 rounded-xl border border-gray-200 bg-white p-5">
        <p class="text-sm text-gray-500">Total Data</p>

        <p class="mt-1 text-2xl font-bold text-gray-800">
            {{ $data->count() }}
        </p>
    </div>

    {{-- TABEL DATA --}}
    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white">

        <div class="border-b border-gray-200 px-5 py-4">
            <h2 class="font-semibold text-gray-800">
                Daftar Data Laporan
            </h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[1100px] text-left text-sm">

                <thead class="bg-gray-50 text-gray-600">
                    <tr>
                        <th class="px-5 py-4 font-semibold">No</th>
                        <th class="px-5 py-4 font-semibold">No. Kerja Sama</th>
                        <th class="px-5 py-4 font-semibold">Mitra Usaha</th>
                        <th class="px-5 py-4 font-semibold">Brand</th>
                        <th class="px-5 py-4 font-semibold">Jenis Usaha</th>
                        <th class="px-5 py-4 font-semibold">Terminal</th>
                        <th class="px-5 py-4 font-semibold">Kode Ruang</th>
                        <th class="px-5 py-4 font-semibold">No. Kontrak Mitra</th>
                        <th class="px-5 py-4 font-semibold">Status</th>
                        <th class="px-5 py-4 text-center font-semibold">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">

                    @forelse ($data as $index => $item)
                        <tr class="hover:bg-gray-50">

                            <td class="whitespace-nowrap px-5 py-4 text-gray-600">
                                {{ $index + 1 }}
                            </td>

                            <td class="whitespace-nowrap px-5 py-4 font-medium text-gray-700">
                                {{ $item['nomor_kerja_sama'] }}
                            </td>

                            <td class="whitespace-nowrap px-5 py-4 text-gray-700">
                                {{ $item['mitra_usaha'] }}
                            </td>

                            <td class="whitespace-nowrap px-5 py-4 text-gray-700">
                                {{ $item['brand'] }}
                            </td>

                            <td class="whitespace-nowrap px-5 py-4 text-gray-700">
                                {{ $item['jenis_usaha'] }}
                            </td>

                            <td class="whitespace-nowrap px-5 py-4 text-gray-700">
                                {{ $item['terminal'] }}
                            </td>

                            <td class="whitespace-nowrap px-5 py-4 text-gray-700">
                                {{ $item['kode_ruang'] }}
                            </td>

                            <td class="whitespace-nowrap px-5 py-4 text-gray-700">
                                {{ $item['nomor_kontrak'] }}
                            </td>

                            <td class="whitespace-nowrap px-5 py-4">
                                <span class="inline-flex rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-700">
                                    {{ $item['status'] }}
                                </span>
                            </td>

                            <td class="whitespace-nowrap px-5 py-4 text-center">
                                <a
                                    href="{{ $item['url'] }}"
                                    class="font-medium text-blue-600 hover:text-blue-800"
                                >
                                    Detail
                                </a>
                            </td>

                        </tr>

                    @empty
                        <tr>
                            <td colspan="10" class="px-5 py-12 text-center text-gray-500">
                                Tidak ada data yang ditemukan untuk kategori ini.
                            </td>
                        </tr>
                    @endforelse

                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection