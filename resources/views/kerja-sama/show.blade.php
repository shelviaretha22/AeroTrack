@extends('layouts.app')

@section('page-title', 'Detail Kerja Sama')

@section('content')

<div class="py-2">
    <div class="max-w-6xl mx-auto">

        {{-- HEADER --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-7">

            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.15em] text-[#8BA5A2]">
                    Management
                </p>

                <h2 class="mt-1 text-2xl font-bold text-[#365F65]">
                    Detail Kerja Sama
                </h2>

                <p class="mt-1 text-sm text-[#8BA5A2]">
                    Informasi lengkap data kerja sama mitra usaha.
                </p>
            </div>

            <div class="flex items-center gap-3">

                <a
                    href="{{ route('kerja-sama.edit', $kerjaSama->id) }}"
                    class="inline-flex items-center gap-2
                           px-4 py-2.5
                           rounded-xl
                           bg-[#365F65]
                           text-white
                           text-sm
                           font-semibold
                           hover:bg-[#294F55]
                           transition"
                >
                    ✏️ Edit
                </a>

                <a
                    href="{{ route('kerja-sama') }}"
                    class="inline-flex items-center gap-2
                           px-4 py-2.5
                           rounded-xl
                           bg-white
                           border border-[#DCE6E4]
                           text-[#365F65]
                           text-sm
                           font-semibold
                           hover:bg-[#F3F6F5]
                           transition"
                >
                    ← Kembali
                </a>

            </div>

        </div>


        {{-- STATUS --}}
        <div class="mb-6">

            @if ($kerjaSama->status === 'Disetujui')

                <span class="inline-flex items-center gap-2
                             px-3 py-1.5
                             rounded-full
                             text-xs font-semibold
                             bg-[#E4F2EC]
                             text-[#39745C]">
                    <span class="w-2 h-2 rounded-full bg-[#39745C]"></span>
                    Disetujui
                </span>

            @elseif ($kerjaSama->status === 'Proses')

                <span class="inline-flex items-center gap-2
                             px-3 py-1.5
                             rounded-full
                             text-xs font-semibold
                             bg-[#FFF4D9]
                             text-[#9A7428]">
                    <span class="w-2 h-2 rounded-full bg-[#9A7428]"></span>
                    Proses
                </span>

            @elseif ($kerjaSama->status === 'Ditolak')

                <span class="inline-flex items-center gap-2
                             px-3 py-1.5
                             rounded-full
                             text-xs font-semibold
                             bg-[#FCE7E7]
                             text-[#B54B4B]">
                    <span class="w-2 h-2 rounded-full bg-[#B54B4B]"></span>
                    Ditolak
                </span>

            @else

                <span class="inline-flex items-center gap-2
                             px-3 py-1.5
                             rounded-full
                             text-xs font-semibold
                             bg-[#EEF2F1]
                             text-[#6B8582]">
                    <span class="w-2 h-2 rounded-full bg-[#6B8582]"></span>
                    Draft
                </span>

            @endif

        </div>


        {{-- INFORMASI MITRA --}}
        <div class="bg-white rounded-2xl border border-[#DCE6E4] overflow-hidden mb-6">

            <div class="px-6 py-5 border-b border-[#DCE6E4]">
                <h3 class="text-base font-bold text-[#365F65]">
                    Informasi Mitra
                </h3>

                <p class="mt-1 text-xs text-[#8BA5A2]">
                    Informasi dasar mengenai mitra usaha.
                </p>
            </div>


            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">

                <div>
                    <p class="text-xs font-semibold text-[#8BA5A2]">
                        Mitra Usaha
                    </p>

                    <p class="mt-1 text-sm font-semibold text-[#365F65]">
                        {{ $kerjaSama->mitra_usaha }}
                    </p>
                </div>


                <div>
                    <p class="text-xs font-semibold text-[#8BA5A2]">
                        Brand
                    </p>

                    <p class="mt-1 text-sm font-semibold text-[#365F65]">
                        {{ $kerjaSama->brand ?: '-' }}
                    </p>
                </div>


                <div>
                    <p class="text-xs font-semibold text-[#8BA5A2]">
                        Jenis Usaha
                    </p>

                    <p class="mt-1 text-sm font-semibold text-[#365F65]">
                        {{ $kerjaSama->jenis_usaha }}
                    </p>
                </div>


                <div>
                    <p class="text-xs font-semibold text-[#8BA5A2]">
                        Bentuk Kerja Sama
                    </p>

                    <p class="mt-1 text-sm font-semibold text-[#365F65]">
                        {{ $kerjaSama->bentuk_kerja_sama }}
                    </p>
                </div>

            </div>

        </div>


        {{-- INFORMASI PROSES --}}
        <div class="bg-white rounded-2xl border border-[#DCE6E4] overflow-hidden mb-6">

            <div class="px-6 py-5 border-b border-[#DCE6E4]">
                <h3 class="text-base font-bold text-[#365F65]">
                    Informasi Proses Kerja Sama
                </h3>

                <p class="mt-1 text-xs text-[#8BA5A2]">
                    Informasi mengenai proses dan periode kerja sama.
                </p>
            </div>


            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">

                <div>
                    <p class="text-xs font-semibold text-[#8BA5A2]">
                        Tanggal Pengajuan
                    </p>

                    <p class="mt-1 text-sm font-semibold text-[#365F65]">
                        {{ $kerjaSama->tanggal_pengajuan?->format('d F Y') ?? '-' }}
                    </p>
                </div>


                <div>
                    <p class="text-xs font-semibold text-[#8BA5A2]">
                        Status
                    </p>

                    <p class="mt-1 text-sm font-semibold text-[#365F65]">
                        {{ $kerjaSama->status }}
                    </p>
                </div>


                <div>
                    <p class="text-xs font-semibold text-[#8BA5A2]">
                        Tanggal Mulai
                    </p>

                    <p class="mt-1 text-sm font-semibold text-[#365F65]">
                        {{ $kerjaSama->tanggal_mulai?->format('d F Y') ?? '-' }}
                    </p>
                </div>


                <div>
                    <p class="text-xs font-semibold text-[#8BA5A2]">
                        Tanggal Berakhir
                    </p>

                    <p class="mt-1 text-sm font-semibold text-[#365F65]">
                        {{ $kerjaSama->tanggal_berakhir?->format('d F Y') ?? '-' }}
                    </p>
                </div>

            </div>

        </div>


        {{-- INFORMASI PENGAJUAN --}}
        <div class="bg-white rounded-2xl border border-[#DCE6E4] overflow-hidden mb-6">

            <div class="px-6 py-5 border-b border-[#DCE6E4]">
                <h3 class="text-base font-bold text-[#365F65]">
                    Informasi Pengajuan
                </h3>
            </div>


            <div class="p-6 space-y-6">

                <div>
                    <p class="text-xs font-semibold text-[#8BA5A2]">
                        PIC Commercial
                    </p>

                    <p class="mt-1 text-sm font-semibold text-[#365F65]">
                        {{ $kerjaSama->pic_commercial }}
                    </p>
                </div>


                <div>
                    <p class="text-xs font-semibold text-[#8BA5A2]">
                        Catatan
                    </p>

                    <div class="mt-2 p-4 rounded-xl bg-[#F3F6F5]">
                        <p class="text-sm leading-6 text-[#527D82]">
                            {{ $kerjaSama->catatan ?: 'Tidak ada catatan.' }}
                        </p>
                    </div>
                </div>

            </div>

        </div>


        {{-- INFORMASI SISTEM --}}
        <div class="bg-white rounded-2xl border border-[#DCE6E4] overflow-hidden">

            <div class="px-6 py-5 border-b border-[#DCE6E4]">
                <h3 class="text-base font-bold text-[#365F65]">
                    Informasi Sistem
                </h3>
            </div>

            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">

                <div>
                    <p class="text-xs font-semibold text-[#8BA5A2]">
                        Dibuat
                    </p>

                    <p class="mt-1 text-sm text-[#527D82]">
                        {{ $kerjaSama->created_at?->format('d F Y, H:i') ?? '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs font-semibold text-[#8BA5A2]">
                        Terakhir Diperbarui
                    </p>

                    <p class="mt-1 text-sm text-[#527D82]">
                        {{ $kerjaSama->updated_at?->format('d F Y, H:i') ?? '-' }}
                    </p>
                </div>

            </div>

        </div>

    </div>
</div>

@endsection