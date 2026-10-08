@extends('layouts.app')

@section('page-title', 'Detail Tenant')

@section('content')

    {{-- =========================================================
         HEADER HALAMAN
    ========================================================== --}}
    <div style="margin-bottom: 28px;">

        <div
            style="
                display: flex;
                align-items: center;
                gap: 12px;
            "
        >

            {{-- BACK BUTTON --}}
            <a
                href="{{ route('tenant.index') }}"
                title="Kembali ke Tenant"
                style="
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    width: 36px;
                    height: 36px;
                    flex-shrink: 0;
                    border-radius: 12px;
                    border: 1px solid #DCE6E4;
                    background: #FFFFFF;
                    color: #527D82;
                    text-decoration: none;
                "
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    style="width: 16px; height: 16px;"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="1.8"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M19 12H5"
                    />

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M12 19l-7-7 7-7"
                    />
                </svg>
            </a>

            <div>

                <h2
                    style="
                        margin: 0;
                        font-size: 24px;
                        line-height: 1.3;
                        font-weight: 700;
                        color: #365F65;
                    "
                >
                    Detail Tenant
                </h2>

                <p
                    style="
                        margin: 6px 0 0;
                        font-size: 14px;
                        font-weight: 400;
                        color: #8BA5A2;
                    "
                >
                    Informasi lengkap tenant dan data kerja sama yang terhubung.
                </p>

            </div>

        </div>

    </div>


    {{-- =========================================================
         INFORMASI KERJA SAMA
    ========================================================== --}}
    <div
        style="
            margin-bottom: 24px;
            overflow: hidden;
            border-radius: 18px;
            border: 1px solid #DCE6E4;
            background: #FFFFFF;
            box-shadow: 0 4px 14px rgba(54, 95, 101, 0.05);
        "
    >

        <div
            style="
                padding: 24px 36px;
                border-bottom: 1px solid #E4EBE9;
            "
        >

            <h3
                style="
                    margin: 0;
                    font-size: 18px;
                    line-height: 1.4;
                    font-weight: 700;
                    color: #365F65;
                "
            >
                Informasi Kerja Sama
            </h3>

            <p
                style="
                    margin: 6px 0 0;
                    font-size: 14px;
                    font-weight: 400;
                    color: #8BA5A2;
                "
            >
                Data kerja sama yang terhubung dengan tenant ini.
            </p>

        </div>


        <div style="padding: 8px 36px 16px;">

            {{-- MITRA USAHA --}}
            <div
                style="
                    display: grid;
                    grid-template-columns: 220px 1fr;
                    gap: 24px;
                    padding: 20px 0;
                    border-bottom: 1px solid #EEF2F1;
                "
            >
                <div
                    style="
                        font-size: 14px;
                        font-weight: 600;
                        color: #527D82;
                    "
                >
                    Mitra Usaha
                </div>

                <div
                    style="
                        font-size: 15px;
                        font-weight: 400;
                        color: #365F65;
                    "
                >
                    {{ $tenant->kerjaSama->mitra_usaha ?? $tenant->mitra_usaha ?? '-' }}
                </div>
            </div>


            {{-- NAMA BRAND --}}
            <div
                style="
                    display: grid;
                    grid-template-columns: 220px 1fr;
                    gap: 24px;
                    padding: 20px 0;
                    border-bottom: 1px solid #EEF2F1;
                "
            >
                <div
                    style="
                        font-size: 14px;
                        font-weight: 600;
                        color: #527D82;
                    "
                >
                    Nama Brand/Tenant
                </div>

                <div
                    style="
                        font-size: 15px;
                        font-weight: 400;
                        color: #365F65;
                    "
                >
                    {{ $tenant->kerjaSama->brand ?? $tenant->nama_tenant ?? '-' }}
                </div>
            </div>


            {{-- JENIS USAHA --}}
            <div
                style="
                    display: grid;
                    grid-template-columns: 220px 1fr;
                    gap: 24px;
                    padding: 20px 0;
                    border-bottom: 1px solid #EEF2F1;
                "
            >
                <div
                    style="
                        font-size: 14px;
                        font-weight: 600;
                        color: #527D82;
                    "
                >
                    Jenis Usaha
                </div>

                <div
                    style="
                        font-size: 15px;
                        font-weight: 400;
                        color: #365F65;
                    "
                >
                    {{ $tenant->kerjaSama->jenis_usaha ?? $tenant->jenis_usaha ?? '-' }}
                </div>
            </div>


            {{-- BENTUK KERJA SAMA --}}
            <div
                style="
                    display: grid;
                    grid-template-columns: 220px 1fr;
                    gap: 24px;
                    padding: 20px 0;
                    border-bottom: 1px solid #EEF2F1;
                "
            >
                <div
                    style="
                        font-size: 14px;
                        font-weight: 600;
                        color: #527D82;
                    "
                >
                    Bentuk Kerja Sama
                </div>

                <div
                    style="
                        font-size: 15px;
                        font-weight: 400;
                        color: #365F65;
                    "
                >
                    {{ $tenant->kerjaSama->bentuk_kerja_sama ?? $tenant->bentuk_kerja_sama ?? '-' }}
                </div>
            </div>


            {{-- PIC COMMERCIAL --}}
            <div
                style="
                    display: grid;
                    grid-template-columns: 220px 1fr;
                    gap: 24px;
                    padding: 20px 0;
                "
            >
                <div
                    style="
                        font-size: 14px;
                        font-weight: 600;
                        color: #527D82;
                    "
                >
                    PIC Commercial
                </div>

                <div
                    style="
                        font-size: 15px;
                        font-weight: 400;
                        color: #365F65;
                    "
                >
                    {{ $tenant->kerjaSama->pic_commercial ?? '-' }}
                </div>
            </div>

        </div>

    </div>


    {{-- =========================================================
         INFORMASI LEGALITAS TENANT
    ========================================================== --}}
    <div
        style="
            margin-bottom: 24px;
            overflow: hidden;
            border-radius: 18px;
            border: 1px solid #DCE6E4;
            background: #FFFFFF;
            box-shadow: 0 4px 14px rgba(54, 95, 101, 0.05);
        "
    >

        <div
            style="
                padding: 24px 36px;
                border-bottom: 1px solid #E4EBE9;
            "
        >

            <h3
                style="
                    margin: 0;
                    font-size: 18px;
                    line-height: 1.4;
                    font-weight: 700;
                    color: #365F65;
                "
            >
                Informasi Legalitas Tenant
            </h3>

        </div>


        <div style="padding: 8px 36px 16px;">

            {{-- NPWP --}}
            <div
                style="
                    display: grid;
                    grid-template-columns: 220px 1fr;
                    gap: 24px;
                    padding: 20px 0;
                    border-bottom: 1px solid #EEF2F1;
                "
            >
                <div
                    style="
                        font-size: 14px;
                        font-weight: 600;
                        color: #527D82;
                    "
                >
                    NPWP
                </div>

                <div
                    style="
                        font-size: 15px;
                        font-weight: 400;
                        color: #365F65;
                    "
                >
                    {{ $tenant->npwp ?: '-' }}
                </div>
            </div>


            {{-- NIB --}}
            <div
                style="
                    display: grid;
                    grid-template-columns: 220px 1fr;
                    gap: 24px;
                    padding: 20px 0;
                    border-bottom: 1px solid #EEF2F1;
                "
            >
                <div
                    style="
                        font-size: 14px;
                        font-weight: 600;
                        color: #527D82;
                    "
                >
                    NIB
                </div>

                <div
                    style="
                        font-size: 15px;
                        font-weight: 400;
                        color: #365F65;
                    "
                >
                    {{ $tenant->nib ?: '-' }}
                </div>
            </div>


            {{-- BADAN USAHA --}}
            <div
                style="
                    display: grid;
                    grid-template-columns: 220px 1fr;
                    gap: 24px;
                    padding: 20px 0;
                    border-bottom: 1px solid #EEF2F1;
                "
            >
                <div
                    style="
                        font-size: 14px;
                        font-weight: 600;
                        color: #527D82;
                    "
                >
                    Bentuk Badan Usaha
                </div>

                <div
                    style="
                        font-size: 15px;
                        font-weight: 400;
                        color: #365F65;
                    "
                >
                    @switch($tenant->bentuk_badan_usaha)

                        @case('PT')
                            Perseroan Terbatas (PT)
                            @break

                        @case('CV')
                            Commanditaire Vennootschap (CV)
                            @break

                        @case('Perorangan')
                            Usaha Perorangan
                            @break

                        @default
                            {{ $tenant->bentuk_badan_usaha ?: '-' }}

                    @endswitch
                </div>
            </div>


            {{-- ALAMAT --}}
            <div
                style="
                    display: grid;
                    grid-template-columns: 220px 1fr;
                    gap: 24px;
                    padding: 20px 0;
                "
            >
                <div
                    style="
                        font-size: 14px;
                        font-weight: 600;
                        color: #527D82;
                    "
                >
                    Alamat Perusahaan/Usaha
                </div>

                <div
                    style="
                        font-size: 15px;
                        font-weight: 400;
                        color: #365F65;
                        line-height: 1.6;
                    "
                >
                    {{ $tenant->alamat_perusahaan ?: '-' }}
                </div>
            </div>

        </div>

    </div>

        {{-- =========================================================
         INFORMASI PIC TENANT
    ========================================================== --}}
    <div
        style="
            margin-bottom: 24px;
            overflow: hidden;
            border-radius: 18px;
            border: 1px solid #DCE6E4;
            background: #FFFFFF;
            box-shadow: 0 4px 14px rgba(54, 95, 101, 0.05);
        "
    >

        {{-- HEADER CARD --}}
        <div
            style="
                padding: 24px 36px;
                border-bottom: 1px solid #E4EBE9;
            "
        >

            <h3
                style="
                    margin: 0;
                    font-size: 18px;
                    line-height: 1.4;
                    font-weight: 700;
                    color: #365F65;
                "
            >
                Informasi PIC Tenant
            </h3>

            <p
                style="
                    margin: 6px 0 0;
                    font-size: 14px;
                    line-height: 1.5;
                    font-weight: 400;
                    color: #8BA5A2;
                "
            >
                Informasi PIC yang bertanggung jawab dari pihak tenant.
            </p>

        </div>


        {{-- DATA PIC --}}
        <div style="padding: 8px 36px 16px;">

            {{-- NAMA PIC --}}
            <div
                style="
                    display: grid;
                    grid-template-columns: 220px 1fr;
                    gap: 24px;
                    padding: 20px 0;
                    border-bottom: 1px solid #EEF2F1;
                "
            >

                <div
                    style="
                        font-size: 14px;
                        font-weight: 600;
                        color: #527D82;
                    "
                >
                    Nama PIC
                </div>

                <div
                    style="
                        font-size: 15px;
                        font-weight: 400;
                        color: #365F65;
                    "
                >
                    {{ $tenant->nama_pic ?: '-' }}
                </div>

            </div>


            {{-- JABATAN --}}
            <div
                style="
                    display: grid;
                    grid-template-columns: 220px 1fr;
                    gap: 24px;
                    padding: 20px 0;
                    border-bottom: 1px solid #EEF2F1;
                "
            >

                <div
                    style="
                        font-size: 14px;
                        font-weight: 600;
                        color: #527D82;
                    "
                >
                    Jabatan
                </div>

                <div
                    style="
                        font-size: 15px;
                        font-weight: 400;
                        color: #365F65;
                    "
                >
                    {{ $tenant->jabatan_pic ?: '-' }}
                </div>

            </div>


            {{-- NO. HP --}}
            <div
                style="
                    display: grid;
                    grid-template-columns: 220px 1fr;
                    gap: 24px;
                    padding: 20px 0;
                    border-bottom: 1px solid #EEF2F1;
                "
            >

                <div
                    style="
                        font-size: 14px;
                        font-weight: 600;
                        color: #527D82;
                    "
                >
                    No. HP
                </div>

                <div
                    style="
                        font-size: 15px;
                        font-weight: 400;
                        color: #365F65;
                    "
                >
                    {{ $tenant->no_hp_pic ?: '-' }}
                </div>

            </div>


            {{-- EMAIL --}}
            <div
                style="
                    display: grid;
                    grid-template-columns: 220px 1fr;
                    gap: 24px;
                    padding: 20px 0;
                "
            >

                <div
                    style="
                        font-size: 14px;
                        font-weight: 600;
                        color: #527D82;
                    "
                >
                    Email
                </div>

                <div
                    style="
                        font-size: 15px;
                        font-weight: 400;
                        color: #365F65;
                    "
                >
                    {{ $tenant->email_pic ?: '-' }}
                </div>

            </div>

        </div>

    </div>


    {{-- =========================================================
         TOMBOL AKSI
    ========================================================== --}}
    <div
        style="
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 12px;
            padding-bottom: 8px;
        "
    >

        {{-- KEMBALI --}}
        <a
            href="{{ route('tenant.index') }}"
            style="
                display: inline-flex;
                align-items: center;
                justify-content: center;
                height: 48px;
                padding: 0 24px;
                border-radius: 12px;
                border: 1px solid #DCE6E4;
                background: #FFFFFF;
                color: #527D82;
                font-size: 14px;
                font-weight: 600;
                text-decoration: none;
            "
        >
            Kembali
        </a>


        {{-- EDIT --}}
        <a
            href="{{ route('tenant.edit', $tenant) }}"
            style="
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                height: 48px;
                padding: 0 22px;
                border-radius: 12px;
                border: none;
                background: #527D82;
                color: #FFFFFF;
                font-size: 14px;
                font-weight: 600;
                text-decoration: none;
            "
        >

            <svg
                xmlns="http://www.w3.org/2000/svg"
                width="17"
                height="17"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
            >
                <path d="M12 20h9"/>
                <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4Z"/>
            </svg>

            Edit Data Tenant

        </a>

    </div>

@endsection