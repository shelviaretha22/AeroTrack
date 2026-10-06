@extends('layouts.app')

@section('page-title', 'Input Tenant')

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
                    Input Data Tenant
                </h2>

                <p
                    style="
                        margin: 6px 0 0;
                        font-size: 14px;
                        color: #8BA5A2;
                    "
                >
                    Tambahkan informasi lengkap tenant yang bekerja sama dengan Commercial.
                </p>

            </div>

        </div>

    </div>



    {{-- =========================================================
         FORM
    ========================================================== --}}
    <form
        action="{{ route('tenant.store') }}"
        method="POST"
    >

        @csrf


        {{-- =====================================================
             INFORMASI TENANT
        ====================================================== --}}
        <div
            style="
                margin-bottom: 32px;
                overflow: hidden;
                border-radius: 18px;
                border: 1px solid #DCE6E4;
                background: #FFFFFF;
                box-shadow: 0 1px 3px rgba(54, 95, 101, 0.04);
            "
        >

            {{-- HEADER CARD --}}
            <div
                style="
                    padding: 24px 36px;
                    border-bottom: 1px solid #E8EFED;
                "
            >

                <h3
                    style="
                        margin: 0;
                        font-size: 16px;
                        line-height: 1.4;
                        font-weight: 700;
                        color: #365F65;
                    "
                >
                    Informasi Tenant
                </h3>

                <p
                    style="
                        margin: 6px 0 0;
                        font-size: 14px;
                        line-height: 1.5;
                        color: #8BA5A2;
                    "
                >
                    Masukkan informasi dasar mengenai tenant.
                </p>

            </div>


            {{-- BODY CARD --}}
            <div
                style="
                    padding: 28px 36px 32px;
                "
            >

                <div
                    class="grid grid-cols-1 md:grid-cols-2"
                    style="
                        column-gap: 24px;
                        row-gap: 24px;
                    "
                >

                    {{-- =================================================
                         MITRA USAHA
                    ================================================== --}}
                    <div>

                        <label
                            for="mitra_usaha"
                            style="
                                display: block;
                                margin-bottom: 8px;
                                font-size: 14px;
                                line-height: 1.4;
                                font-weight: 600;
                                color: #527D82;
                            "
                        >
                            Mitra Usaha
                            <span style="color: #B96B60;">*</span>
                        </label>

                        <input
                            type="text"
                            id="mitra_usaha"
                            name="mitra_usaha"
                            value="{{ old('mitra_usaha') }}"
                            placeholder="Masukkan nama mitra usaha"
                            style="
                                display: block;
                                width: 100%;
                                height: 56px;
                                box-sizing: border-box;
                                border-radius: 14px;
                                border: 1px solid #E2EBE9;
                                background: #F7F9F8;
                                padding: 0 18px;
                                font-size: 14px;
                                color: #365F65;
                                outline: none;
                            "
                        >

                        @error('mitra_usaha')
                            <p
                                style="
                                    margin: 6px 0 0;
                                    font-size: 12px;
                                    color: #B96B60;
                                "
                            >
                                {{ $message }}
                            </p>
                        @enderror

                    </div>



                    {{-- =================================================
                         NAMA TENANT
                    ================================================== --}}
                    <div>

                        <label
                            for="nama_tenant"
                            style="
                                display: block;
                                margin-bottom: 8px;
                                font-size: 14px;
                                line-height: 1.4;
                                font-weight: 600;
                                color: #527D82;
                            "
                        >
                            Nama Tenant
                            <span style="color: #B96B60;">*</span>
                        </label>

                        <input
                            type="text"
                            id="nama_tenant"
                            name="nama_tenant"
                            value="{{ old('nama_tenant') }}"
                            placeholder="Masukkan nama tenant / brand"
                            style="
                                display: block;
                                width: 100%;
                                height: 56px;
                                box-sizing: border-box;
                                border-radius: 14px;
                                border: 1px solid #E2EBE9;
                                background: #F7F9F8;
                                padding: 0 18px;
                                font-size: 14px;
                                color: #365F65;
                                outline: none;
                            "
                        >

                        @error('nama_tenant')
                            <p
                                style="
                                    margin: 6px 0 0;
                                    font-size: 12px;
                                    color: #B96B60;
                                "
                            >
                                {{ $message }}
                            </p>
                        @enderror

                    </div>



                    {{-- =================================================
                         JENIS USAHA
                    ================================================== --}}
                    <div>

                        <label
                            for="jenis_usaha"
                            style="
                                display: block;
                                margin-bottom: 8px;
                                font-size: 14px;
                                line-height: 1.4;
                                font-weight: 600;
                                color: #527D82;
                            "
                        >
                            Jenis Usaha
                            <span style="color: #B96B60;">*</span>
                        </label>

                        <select
                            id="jenis_usaha"
                            name="jenis_usaha"
                            style="
                                display: block;
                                width: 100%;
                                height: 56px;
                                box-sizing: border-box;
                                border-radius: 14px;
                                border: 1px solid #E2EBE9;
                                background: #F7F9F8;
                                padding: 0 18px;
                                font-size: 14px;
                                color: #365F65;
                                outline: none;
                            "
                        >

                            <option
                                value=""
                                disabled
                                {{ old('jenis_usaha') ? '' : 'selected' }}
                            >
                                Pilih jenis usaha
                            </option>

                            <option
                                value="F&B"
                                {{ old('jenis_usaha') === 'F&B' ? 'selected' : '' }}
                            >
                                F&B
                            </option>

                            <option
                                value="Retail"
                                {{ old('jenis_usaha') === 'Retail' ? 'selected' : '' }}
                            >
                                Retail
                            </option>

                            <option
                                value="Duty Free Retail"
                                {{ old('jenis_usaha') === 'Duty Free Retail' ? 'selected' : '' }}
                            >
                                Duty Free Retail
                            </option>

                            <option
                                value="Service Commercial"
                                {{ old('jenis_usaha') === 'Service Commercial' ? 'selected' : '' }}
                            >
                                Service Commercial
                            </option>

                            <option
                                value="Lainnya"
                                {{ old('jenis_usaha') === 'Lainnya' ? 'selected' : '' }}
                            >
                                Lainnya
                            </option>

                        </select>

                        @error('jenis_usaha')
                            <p
                                style="
                                    margin: 6px 0 0;
                                    font-size: 12px;
                                    color: #B96B60;
                                "
                            >
                                {{ $message }}
                            </p>
                        @enderror

                    </div>



                    {{-- =================================================
                         BENTUK KERJA SAMA
                    ================================================== --}}
                    <div>

                        <label
                            for="bentuk_kerja_sama"
                            style="
                                display: block;
                                margin-bottom: 8px;
                                font-size: 14px;
                                line-height: 1.4;
                                font-weight: 600;
                                color: #527D82;
                            "
                        >
                            Bentuk Kerja Sama
                            <span style="color: #B96B60;">*</span>
                        </label>

                        <select
                            id="bentuk_kerja_sama"
                            name="bentuk_kerja_sama"
                            style="
                                display: block;
                                width: 100%;
                                height: 56px;
                                box-sizing: border-box;
                                border-radius: 14px;
                                border: 1px solid #E2EBE9;
                                background: #F7F9F8;
                                padding: 0 18px;
                                font-size: 14px;
                                color: #365F65;
                                outline: none;
                            "
                        >

                            <option
                                value=""
                                disabled
                                {{ old('bentuk_kerja_sama') ? '' : 'selected' }}
                            >
                                Pilih bentuk kerja sama
                            </option>

                            <option
                                value="Sewa"
                                {{ old('bentuk_kerja_sama') === 'Sewa' ? 'selected' : '' }}
                            >
                                Sewa
                            </option>

                            <option
                                value="Bagi Hasil"
                                {{ old('bentuk_kerja_sama') === 'Bagi Hasil' ? 'selected' : '' }}
                            >
                                Bagi Hasil
                            </option>

                            <option
                                value="Sewa dan Bagi Hasil"
                                {{ old('bentuk_kerja_sama') === 'Sewa dan Bagi Hasil' ? 'selected' : '' }}
                            >
                                Sewa dan Bagi Hasil
                            </option>

                            <option
                                value="Lainnya"
                                {{ old('bentuk_kerja_sama') === 'Lainnya' ? 'selected' : '' }}
                            >
                                Lainnya
                            </option>

                        </select>

                        @error('bentuk_kerja_sama')
                            <p
                                style="
                                    margin: 6px 0 0;
                                    font-size: 12px;
                                    color: #B96B60;
                                "
                            >
                                {{ $message }}
                            </p>
                        @enderror

                    </div>



                    {{-- =================================================
                         NPWP
                    ================================================== --}}
                    <div class="md:col-span-2">

                        <label
                            for="npwp"
                            style="
                                display: block;
                                margin-bottom: 8px;
                                font-size: 14px;
                                line-height: 1.4;
                                font-weight: 600;
                                color: #527D82;
                            "
                        >
                            NPWP

                            <span
                                style="
                                    font-weight: 400;
                                    color: #9AAEAB;
                                "
                            >
                                (Opsional)
                            </span>

                        </label>

                        <input
                            type="text"
                            id="npwp"
                            name="npwp"
                            value="{{ old('npwp') }}"
                            placeholder="Masukkan nomor NPWP"
                            style="
                                display: block;
                                width: 100%;
                                height: 56px;
                                box-sizing: border-box;
                                border-radius: 14px;
                                border: 1px solid #E2EBE9;
                                background: #F7F9F8;
                                padding: 0 18px;
                                font-size: 14px;
                                color: #365F65;
                                outline: none;
                            "
                        >

                        @error('npwp')
                            <p
                                style="
                                    margin: 6px 0 0;
                                    font-size: 12px;
                                    color: #B96B60;
                                "
                            >
                                {{ $message }}
                            </p>
                        @enderror

                    </div>

                </div>

            </div>

        </div>



        {{-- =====================================================
             INFORMASI PIC TENANT
        ====================================================== --}}
        <div
            style="
                margin-bottom: 32px;
                overflow: hidden;
                border-radius: 18px;
                border: 1px solid #DCE6E4;
                background: #FFFFFF;
                box-shadow: 0 1px 3px rgba(54, 95, 101, 0.04);
            "
        >

            {{-- HEADER CARD --}}
            <div
                style="
                    padding: 24px 36px;
                    border-bottom: 1px solid #E8EFED;
                "
            >

                <h3
                    style="
                        margin: 0;
                        font-size: 16px;
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
                        color: #8BA5A2;
                    "
                >
                    Masukkan informasi person in charge (PIC) dari tenant.
                </p>

            </div>


            {{-- BODY CARD --}}
            <div
                style="
                    padding: 28px 36px 32px;
                "
            >

                <div
                    class="grid grid-cols-1 md:grid-cols-2"
                    style="
                        column-gap: 24px;
                        row-gap: 24px;
                    "
                >

                    {{-- =================================================
                         NAMA PIC
                    ================================================== --}}
                    <div>

                        <label
                            for="nama_pic"
                            style="
                                display: block;
                                margin-bottom: 8px;
                                font-size: 14px;
                                line-height: 1.4;
                                font-weight: 600;
                                color: #527D82;
                            "
                        >
                            Nama PIC
                            <span style="color: #B96B60;">*</span>
                        </label>

                        <input
                            type="text"
                            id="nama_pic"
                            name="nama_pic"
                            value="{{ old('nama_pic') }}"
                            placeholder="Masukkan nama PIC"
                            style="
                                display: block;
                                width: 100%;
                                height: 56px;
                                box-sizing: border-box;
                                border-radius: 14px;
                                border: 1px solid #E2EBE9;
                                background: #F7F9F8;
                                padding: 0 18px;
                                font-size: 14px;
                                color: #365F65;
                                outline: none;
                            "
                        >

                        @error('nama_pic')
                            <p
                                style="
                                    margin: 6px 0 0;
                                    font-size: 12px;
                                    color: #B96B60;
                                "
                            >
                                {{ $message }}
                            </p>
                        @enderror

                    </div>



                    {{-- =================================================
                         JABATAN PIC
                    ================================================== --}}
                    <div>

                        <label
                            for="jabatan_pic"
                            style="
                                display: block;
                                margin-bottom: 8px;
                                font-size: 14px;
                                line-height: 1.4;
                                font-weight: 600;
                                color: #527D82;
                            "
                        >
                            Jabatan PIC

                            <span
                                style="
                                    font-weight: 400;
                                    color: #9AAEAB;
                                "
                            >
                                (Opsional)
                            </span>

                        </label>

                        <input
                            type="text"
                            id="jabatan_pic"
                            name="jabatan_pic"
                            value="{{ old('jabatan_pic') }}"
                            placeholder="Contoh: Manager / Owner"
                            style="
                                display: block;
                                width: 100%;
                                height: 56px;
                                box-sizing: border-box;
                                border-radius: 14px;
                                border: 1px solid #E2EBE9;
                                background: #F7F9F8;
                                padding: 0 18px;
                                font-size: 14px;
                                color: #365F65;
                                outline: none;
                            "
                        >

                        @error('jabatan_pic')
                            <p
                                style="
                                    margin: 6px 0 0;
                                    font-size: 12px;
                                    color: #B96B60;
                                "
                            >
                                {{ $message }}
                            </p>
                        @enderror

                    </div>



                    {{-- =================================================
                         NO HP PIC
                    ================================================== --}}
                    <div>

                        <label
                            for="no_hp_pic"
                            style="
                                display: block;
                                margin-bottom: 8px;
                                font-size: 14px;
                                line-height: 1.4;
                                font-weight: 600;
                                color: #527D82;
                            "
                        >
                            No. HP PIC
                            <span style="color: #B96B60;">*</span>
                        </label>

                        <input
                            type="tel"
                            id="no_hp_pic"
                            name="no_hp_pic"
                            value="{{ old('no_hp_pic') }}"
                            placeholder="Contoh: 081234567890"
                            style="
                                display: block;
                                width: 100%;
                                height: 56px;
                                box-sizing: border-box;
                                border-radius: 14px;
                                border: 1px solid #E2EBE9;
                                background: #F7F9F8;
                                padding: 0 18px;
                                font-size: 14px;
                                color: #365F65;
                                outline: none;
                            "
                        >

                        @error('no_hp_pic')
                            <p
                                style="
                                    margin: 6px 0 0;
                                    font-size: 12px;
                                    color: #B96B60;
                                "
                            >
                                {{ $message }}
                            </p>
                        @enderror

                    </div>



                    {{-- =================================================
                         EMAIL PIC
                    ================================================== --}}
                    <div>

                        <label
                            for="email_pic"
                            style="
                                display: block;
                                margin-bottom: 8px;
                                font-size: 14px;
                                line-height: 1.4;
                                font-weight: 600;
                                color: #527D82;
                            "
                        >
                            Email PIC

                            <span
                                style="
                                    font-weight: 400;
                                    color: #9AAEAB;
                                "
                            >
                                (Opsional)
                            </span>

                        </label>

                        <input
                            type="email"
                            id="email_pic"
                            name="email_pic"
                            value="{{ old('email_pic') }}"
                            placeholder="Contoh: pic@perusahaan.com"
                            style="
                                display: block;
                                width: 100%;
                                height: 56px;
                                box-sizing: border-box;
                                border-radius: 14px;
                                border: 1px solid #E2EBE9;
                                background: #F7F9F8;
                                padding: 0 18px;
                                font-size: 14px;
                                color: #365F65;
                                outline: none;
                            "
                        >

                        @error('email_pic')
                            <p
                                style="
                                    margin: 6px 0 0;
                                    font-size: 12px;
                                    color: #B96B60;
                                "
                            >
                                {{ $message }}
                            </p>
                        @enderror

                    </div>

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
                    height: 44px;
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


            {{-- SIMPAN --}}
            <button
                type="submit"
                style="
                    display: inline-flex;
                    align-items: center;
                    justify-content: center;
                    height: 44px;
                    padding: 0 24px;
                    border: 0;
                    border-radius: 12px;
                    background: #365F65;
                    color: #FFFFFF;
                    font-size: 14px;
                    font-weight: 600;
                    cursor: pointer;
                "
            >

                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    style="
                        width: 16px;
                        height: 16px;
                        margin-right: 8px;
                    "
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="1.8"
                >

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M5 12l4 4L19 6"
                    />

                </svg>

                Simpan Data Tenant

            </button>

        </div>

    </form>

@endsection