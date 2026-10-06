@extends('layouts.app')

@section('page-title', 'Detail Lokasi')

@section('content')

<div class="max-w-4xl space-y-6">

    {{-- HEADER --}}

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

        <div>

            <p class="text-sm text-[#8BA5A2]">
                Management / Lokasi
            </p>

            <h2 class="mt-1 text-2xl font-bold text-[#365F65]">
                Detail Lokasi
            </h2>

            <p class="mt-1 text-sm text-[#7A9290]">
                Informasi lengkap lokasi.
            </p>

        </div>


        <a
            href="{{ route('lokasi.edit', $lokasi) }}"
            class="inline-flex items-center justify-center rounded-xl
                   bg-[#365F65]
                   px-5 py-3
                   text-sm font-semibold
                   text-white
                   hover:bg-[#294F55]
                   transition"
        >
            Edit Lokasi
        </a>

    </div>


    {{-- DETAIL --}}

    <div class="rounded-2xl border border-[#E2EBE9] bg-white p-6 sm:p-8">

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">


            {{-- KODE RUANG --}}

            <div>
                <p class="text-xs font-semibold text-[#8BA5A2]">
                    Kode Ruang
                </p>

                <p class="mt-1 text-sm font-semibold text-[#365F65]">
                    {{ $lokasi->kode_ruang ?: '-' }}
                </p>
            </div>


            {{-- TERMINAL --}}

            <div>
                <p class="text-xs font-semibold text-[#8BA5A2]">
                    Terminal
                </p>

                <p class="mt-1 text-sm font-semibold text-[#365F65]">
                    {{ $lokasi->terminal ?: '-' }}
                </p>
            </div>


            {{-- NOMOR RO --}}

            <div>
                <p class="text-xs font-semibold text-[#8BA5A2]">
                    Nomor RO
                </p>

                <p class="mt-1 text-sm font-semibold text-[#365F65]">
                    {{ $lokasi->nomor_ro ?: '-' }}
                </p>
            </div>


            {{-- LOKASI --}}

            <div>
                <p class="text-xs font-semibold text-[#8BA5A2]">
                    Lokasi
                </p>

                <p class="mt-1 text-sm font-semibold text-[#365F65]">
                    {{ $lokasi->lokasi ?: '-' }}
                </p>
            </div>


            {{-- LUAS --}}

            <div>
                <p class="text-xs font-semibold text-[#8BA5A2]">
                    Luas Area
                </p>

                <p class="mt-1 text-sm font-semibold text-[#365F65]">

                    @if($lokasi->luas_area)
                        {{ number_format($lokasi->luas_area, 2, ',', '.') }} m²
                    @else
                        -
                    @endif

                </p>
            </div>


            {{-- STATUS --}}

            <div>
                <p class="text-xs font-semibold text-[#8BA5A2]">
                    Status
                </p>

                <div class="mt-2">

                    @if($lokasi->status === 'Terisi')

                        <span class="inline-flex rounded-full bg-[#E8F0EE] px-3 py-1 text-xs font-semibold text-[#365F65]">
                            Terisi
                        </span>

                    @else

                        <span class="inline-flex rounded-full bg-[#F3F6F5] px-3 py-1 text-xs font-semibold text-[#7A9290]">
                            Kosong
                        </span>

                    @endif

                </div>
            </div>


            {{-- TENANT --}}

            <div class="md:col-span-2">

                <p class="text-xs font-semibold text-[#8BA5A2]">
                    Tenant
                </p>

                <p class="mt-1 text-sm font-semibold text-[#365F65]">

                    @if($lokasi->tenant)
                        {{ $lokasi->tenant->nama_tenant }}
                    @else
                        Belum ditempati
                    @endif

                </p>

            </div>


            {{-- CATATAN --}}

            <div class="md:col-span-2">

                <p class="text-xs font-semibold text-[#8BA5A2]">
                    Keterangan
                </p>

                <p class="mt-1 text-sm leading-6 text-[#527D82]">
                    {{ $lokasi->catatan ?: '-' }}
                </p>

            </div>

        </div>

    </div>


    {{-- BACK --}}

    <div>

        <a
            href="{{ route('lokasi.index') }}"
            class="text-sm font-semibold text-[#527D82] hover:text-[#365F65]"
        >
            ← Kembali ke Data Lokasi
        </a>

    </div>

</div>

@endsection