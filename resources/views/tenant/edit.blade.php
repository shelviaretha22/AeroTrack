@extends('layouts.app')

@section('page-title', 'Edit Data Tenant')

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
                    Edit Data Tenant
                </h2>

                <p
                    style="
                        margin: 6px 0 0;
                        font-size: 14px;
                        line-height: 1.5;
                        font-weight: 400;
                        color: #8BA5A2;
                    "
                >
                    Perbarui informasi tenant yang telah tersimpan sebelumnya.
                </p>

            </div>

        </div>

    </div>


    {{-- =========================================================
         FORM EDIT
    ========================================================== --}}
    <form
        action="{{ route('tenant.update', $tenant) }}"
        method="POST"
    >

        @csrf
        @method('PUT')


        {{-- =====================================================
             INFORMASI KERJA SAMA
        ====================================================== --}}
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
                    Informasi Kerja Sama
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
                    Informasi berikut berasal dari data Kerja Sama dan tidak dapat diubah dari halaman ini.
                </p>

            </div>


            {{-- DATA KERJA SAMA --}}
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

                {{-- =====================================================
             INFORMASI LEGALITAS TENANT
        ====================================================== --}}
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
                    Informasi Legalitas Tenant
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
                    Perbarui informasi legalitas perusahaan atau usaha tenant.
                </p>

            </div>


            <div style="padding: 28px 36px 32px;">

                {{-- NPWP --}}
                <div style="margin-bottom: 24px;">

                    <label
                        for="npwp"
                        style="
                            display: block;
                            margin-bottom: 10px;
                            font-size: 15px;
                            line-height: 1.4;
                            font-weight: 600;
                            color: #527D82;
                        "
                    >
                        NPWP
                        <span
                            style="
                                color: #B96B60;
                                font-weight: 600;
                            "
                        >
                            *
                        </span>
                    </label>

                    <input
                        type="text"
                        id="npwp"
                        name="npwp"
                        value="{{ old('npwp', $tenant->npwp) }}"
                        placeholder="Masukkan nomor NPWP"
                        inputmode="numeric"
                        autocomplete="off"
                        style="
                            display: block;
                            width: 100%;
                            height: 56px;
                            box-sizing: border-box;
                            padding: 0 22px;
                            border: 1px solid #DCE6E4;
                            border-radius: 14px;
                            background: #F7F9F8;
                            color: #365F65;
                            font-family: inherit;
                            font-size: 15px;
                            font-weight: 400;
                            outline: none;
                        "
                    >

                    @error('npwp')
                        <p
                            style="
                                margin: 7px 0 0;
                                color: #B96B60;
                                font-size: 13px;
                                line-height: 1.4;
                                font-weight: 400;
                            "
                        >
                            {{ $message }}
                        </p>
                    @enderror

                </div>


                {{-- NIB --}}
                <div style="margin-bottom: 24px;">

                    <label
                        for="nib"
                        style="
                            display: block;
                            margin-bottom: 10px;
                            font-size: 15px;
                            line-height: 1.4;
                            font-weight: 600;
                            color: #527D82;
                        "
                    >
                        NIB
                        <span
                            style="
                                color: #B96B60;
                                font-weight: 600;
                            "
                        >
                            *
                        </span>
                    </label>

                    <input
                        type="text"
                        id="nib"
                        name="nib"
                        value="{{ old('nib', $tenant->nib) }}"
                        placeholder="Masukkan nomor NIB"
                        inputmode="numeric"
                        autocomplete="off"
                        style="
                            display: block;
                            width: 100%;
                            height: 56px;
                            box-sizing: border-box;
                            padding: 0 22px;
                            border: 1px solid #DCE6E4;
                            border-radius: 14px;
                            background: #F7F9F8;
                            color: #365F65;
                            font-family: inherit;
                            font-size: 15px;
                            font-weight: 400;
                            outline: none;
                        "
                    >

                    @error('nib')
                        <p
                            style="
                                margin: 7px 0 0;
                                color: #B96B60;
                                font-size: 13px;
                                line-height: 1.4;
                                font-weight: 400;
                            "
                        >
                            {{ $message }}
                        </p>
                    @enderror

                </div>


                {{-- BENTUK BADAN USAHA --}}
                <div style="margin-bottom: 24px;">

                    <label
                        for="bentuk_badan_usaha"
                        style="
                            display: block;
                            margin-bottom: 10px;
                            font-size: 15px;
                            line-height: 1.4;
                            font-weight: 600;
                            color: #527D82;
                        "
                    >
                        Bentuk Badan Usaha
                        <span
                            style="
                                color: #B96B60;
                                font-weight: 600;
                            "
                        >
                            *
                        </span>
                    </label>

                    <div style="position: relative;">

                        <select
                            id="bentuk_badan_usaha"
                            name="bentuk_badan_usaha"
                            required
                            style="
                                display: block;
                                width: 100%;
                                height: 56px;
                                box-sizing: border-box;
                                padding: 0 52px 0 22px;
                                border: 1px solid #DCE6E4;
                                border-radius: 14px;
                                background: #F7F9F8;
                                color: #365F65;
                                font-family: inherit;
                                font-size: 15px;
                                font-weight: 400;
                                outline: none;
                                appearance: none;
                                -webkit-appearance: none;
                                -moz-appearance: none;
                                cursor: pointer;
                            "
                        >

                            <option value="">
                                Pilih bentuk badan usaha
                            </option>

                            <option
                                value="PT"
                                {{ old('bentuk_badan_usaha', $tenant->bentuk_badan_usaha) == 'PT' ? 'selected' : '' }}
                            >
                                Perseroan Terbatas (PT)
                            </option>

                            <option
                                value="CV"
                                {{ old('bentuk_badan_usaha', $tenant->bentuk_badan_usaha) == 'CV' ? 'selected' : '' }}
                            >
                                Commanditaire Vennootschap (CV)
                            </option>

                            <option
                                value="Firma"
                                {{ old('bentuk_badan_usaha', $tenant->bentuk_badan_usaha) == 'Firma' ? 'selected' : '' }}
                            >
                                Firma
                            </option>

                            <option
                                value="Koperasi"
                                {{ old('bentuk_badan_usaha', $tenant->bentuk_badan_usaha) == 'Koperasi' ? 'selected' : '' }}
                            >
                                Koperasi
                            </option>

                            <option
                                value="Perorangan"
                                {{ old('bentuk_badan_usaha', $tenant->bentuk_badan_usaha) == 'Perorangan' ? 'selected' : '' }}
                            >
                                Usaha Perorangan
                            </option>

                            <option
                                value="Lainnya"
                                {{ old('bentuk_badan_usaha', $tenant->bentuk_badan_usaha) == 'Lainnya' ? 'selected' : '' }}
                            >
                                Lainnya
                            </option>

                        </select>

                        <span
                            style="
                                position: absolute;
                                top: 50%;
                                right: 20px;
                                transform: translateY(-50%);
                                pointer-events: none;
                                color: #527D82;
                                font-size: 15px;
                                line-height: 1;
                            "
                        >
                            ▼
                        </span>

                    </div>

                    @error('bentuk_badan_usaha')
                        <p
                            style="
                                margin: 7px 0 0;
                                color: #B96B60;
                                font-size: 13px;
                                line-height: 1.4;
                                font-weight: 400;
                            "
                        >
                            {{ $message }}
                        </p>
                    @enderror

                </div>


                {{-- ALAMAT --}}
                <div>

                    <label
                        for="alamat_perusahaan"
                        style="
                            display: block;
                            margin-bottom: 10px;
                            font-size: 15px;
                            line-height: 1.4;
                            font-weight: 600;
                            color: #527D82;
                        "
                    >
                        Alamat Perusahaan/Usaha
                        <span
                            style="
                                color: #B96B60;
                                font-weight: 600;
                            "
                        >
                            *
                        </span>
                    </label>

                    <textarea
                        id="alamat_perusahaan"
                        name="alamat_perusahaan"
                        rows="4"
                        placeholder="Masukkan alamat perusahaan atau usaha"
                        style="
                            display: block;
                            width: 100%;
                            min-height: 120px;
                            box-sizing: border-box;
                            padding: 17px 22px;
                            border: 1px solid #DCE6E4;
                            border-radius: 14px;
                            background: #F7F9F8;
                            color: #365F65;
                            font-family: inherit;
                            font-size: 15px;
                            font-weight: 400;
                            line-height: 1.6;
                            outline: none;
                            resize: vertical;
                        "
                    >{{ old('alamat_perusahaan', $tenant->alamat_perusahaan) }}</textarea>

                    @error('alamat_perusahaan')
                        <p
                            style="
                                margin: 7px 0 0;
                                color: #B96B60;
                                font-size: 13px;
                                line-height: 1.4;
                                font-weight: 400;
                            "
                        >
                            {{ $message }}
                        </p>
                    @enderror

                </div>

            </div>

        </div>

                {{-- =====================================================
             INFORMASI PIC TENANT
        ====================================================== --}}
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
                    Perbarui informasi PIC yang bertanggung jawab dari pihak tenant.
                </p>

            </div>


            <div style="padding: 28px 36px 32px;">

                {{-- NAMA PIC --}}
                <div style="margin-bottom: 24px;">

                    <label
                        for="nama_pic"
                        style="
                            display: block;
                            margin-bottom: 10px;
                            font-size: 15px;
                            line-height: 1.4;
                            font-weight: 600;
                            color: #527D82;
                        "
                    >
                        Nama PIC
                        <span
                            style="
                                color: #B96B60;
                                font-weight: 600;
                            "
                        >
                            *
                        </span>
                    </label>

                    <input
                        type="text"
                        id="nama_pic"
                        name="nama_pic"
                        value="{{ old('nama_pic', $tenant->nama_pic) }}"
                        placeholder="Masukkan nama PIC tenant"
                        autocomplete="off"
                        style="
                            display: block;
                            width: 100%;
                            height: 56px;
                            box-sizing: border-box;
                            padding: 0 22px;
                            border: 1px solid #DCE6E4;
                            border-radius: 14px;
                            background: #F7F9F8;
                            color: #365F65;
                            font-family: inherit;
                            font-size: 15px;
                            font-weight: 400;
                            outline: none;
                        "
                    >

                    @error('nama_pic')
                        <p
                            style="
                                margin: 7px 0 0;
                                color: #B96B60;
                                font-size: 13px;
                                line-height: 1.4;
                                font-weight: 400;
                            "
                        >
                            {{ $message }}
                        </p>
                    @enderror

                </div>


                {{-- JABATAN --}}
                <div style="margin-bottom: 24px;">

                    <label
                        for="jabatan_pic"
                        style="
                            display: block;
                            margin-bottom: 10px;
                            font-size: 15px;
                            line-height: 1.4;
                            font-weight: 600;
                            color: #527D82;
                        "
                    >
                        Jabatan
                        <span
                            style="
                                color: #B96B60;
                                font-weight: 600;
                            "
                        >
                            *
                        </span>
                    </label>

                    <input
                        type="text"
                        id="jabatan_pic"
                        name="jabatan_pic"
                        value="{{ old('jabatan_pic', $tenant->jabatan_pic) }}"
                        placeholder="Masukkan jabatan PIC"
                        autocomplete="off"
                        style="
                            display: block;
                            width: 100%;
                            height: 56px;
                            box-sizing: border-box;
                            padding: 0 22px;
                            border: 1px solid #DCE6E4;
                            border-radius: 14px;
                            background: #F7F9F8;
                            color: #365F65;
                            font-family: inherit;
                            font-size: 15px;
                            font-weight: 400;
                            outline: none;
                        "
                    >

                    @error('jabatan_pic')
                        <p
                            style="
                                margin: 7px 0 0;
                                color: #B96B60;
                                font-size: 13px;
                                line-height: 1.4;
                                font-weight: 400;
                            "
                        >
                            {{ $message }}
                        </p>
                    @enderror

                </div>


                {{-- NO. HP --}}
                <div style="margin-bottom: 24px;">

                    <label
                        for="no_hp_pic"
                        style="
                            display: block;
                            margin-bottom: 10px;
                            font-size: 15px;
                            line-height: 1.4;
                            font-weight: 600;
                            color: #527D82;
                        "
                    >
                        No. HP
                        <span
                            style="
                                color: #B96B60;
                                font-weight: 600;
                            "
                        >
                            *
                        </span>
                    </label>

                    <input
                        type="text"
                        id="no_hp_pic"
                        name="no_hp_pic"
                        value="{{ old('no_hp_pic', $tenant->no_hp_pic) }}"
                        placeholder="Masukkan nomor HP PIC"
                        inputmode="numeric"
                        autocomplete="off"
                        style="
                            display: block;
                            width: 100%;
                            height: 56px;
                            box-sizing: border-box;
                            padding: 0 22px;
                            border: 1px solid #DCE6E4;
                            border-radius: 14px;
                            background: #F7F9F8;
                            color: #365F65;
                            font-family: inherit;
                            font-size: 15px;
                            font-weight: 400;
                            outline: none;
                        "
                    >

                    @error('no_hp_pic')
                        <p
                            style="
                                margin: 7px 0 0;
                                color: #B96B60;
                                font-size: 13px;
                                line-height: 1.4;
                                font-weight: 400;
                            "
                        >
                            {{ $message }}
                        </p>
                    @enderror

                </div>


                {{-- EMAIL --}}
                <div>

                    <label
                        for="email_pic"
                        style="
                            display: block;
                            margin-bottom: 10px;
                            font-size: 15px;
                            line-height: 1.4;
                            font-weight: 600;
                            color: #527D82;
                        "
                    >
                        Email
                        <span
                            style="
                                color: #B96B60;
                                font-weight: 600;
                            "
                        >
                            *
                        </span>
                    </label>

                    <input
                        type="email"
                        id="email_pic"
                        name="email_pic"
                        value="{{ old('email_pic', $tenant->email_pic) }}"
                        placeholder="Masukkan email PIC"
                        autocomplete="email"
                        style="
                            display: block;
                            width: 100%;
                            height: 56px;
                            box-sizing: border-box;
                            padding: 0 22px;
                            border: 1px solid #DCE6E4;
                            border-radius: 14px;
                            background: #F7F9F8;
                            color: #365F65;
                            font-family: inherit;
                            font-size: 15px;
                            font-weight: 400;
                            outline: none;
                        "
                    >

                    @error('email_pic')
                        <p
                            style="
                                margin: 7px 0 0;
                                color: #B96B60;
                                font-size: 13px;
                                line-height: 1.4;
                                font-weight: 400;
                            "
                        >
                            {{ $message }}
                        </p>
                    @enderror

                </div>

            </div>

        </div>


        {{-- =====================================================
             TOMBOL AKSI
        ====================================================== --}}
        <div
            style="
                display: flex;
                justify-content: flex-end;
                align-items: center;
                gap: 12px;
                padding-bottom: 8px;
            "
            class="flex-col-reverse sm:flex-row"
        >

            {{-- BATAL --}}
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
                Batal
            </a>


            {{-- SIMPAN PERUBAHAN --}}
            <button
                type="submit"
                style="
                    display: inline-flex;
                    align-items: center;
                    justify-content: center;
                    gap: 8px;
                    height: 48px;
                    padding: 0 22px;
                    border: none;
                    border-radius: 12px;
                    background: #527D82;
                    color: #FFFFFF;
                    font-family: inherit;
                    font-size: 14px;
                    font-weight: 600;
                    cursor: pointer;
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
                    <path d="M5 12l4 4L19 6"/>
                </svg>

                Simpan Perubahan

            </button>

        </div>

    </form>

@endsection