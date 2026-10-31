
@extends('layouts.app')
@section('page-title', 'Input Tenant')
@section('content')
    {{-- HEADER HALAMAN --}}
    <div style="margin-bottom: 28px;">
        <div style="display: flex; align-items: center; gap: 12px;">
            <a href="{{ route('tenant.index') }}" title="Kembali ke Tenant"
               style="display: flex; align-items: center; justify-content: center; width: 36px; height: 36px; flex-shrink: 0; border-radius: 12px; border: 1px solid #DCE6E4; background: #FFFFFF; color: #527D82; text-decoration: none;">
                <svg xmlns="http://www.w3.org/2000/svg" style="width: 16px; height: 16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 12H5"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h2 style="margin: 0; font-size: 24px; line-height: 1.3; font-weight: 700; color: #365F65;">Input Data Tenant</h2>
                <p style="margin: 6px 0 0; font-size: 14px; font-weight: 400; color: #8BA5A2;">Lengkapi informasi tenant berdasarkan data kerja sama yang telah dibuat.</p>
            </div>
        </div>
    </div>

    {{-- FORM --}}
    <form action="{{ route('tenant.store') }}" method="POST">
        @csrf
        <input type="hidden" name="kerja_sama_id" value="{{ $kerjaSama->id }}">
        <input type="hidden" name="mitra_usaha" value="{{ $kerjaSama->mitra_usaha }}">
        <input type="hidden" name="nama_tenant" value="{{ $kerjaSama->brand }}">
        <input type="hidden" name="jenis_usaha" value="{{ $kerjaSama->jenis_usaha }}">
        <input type="hidden" name="bentuk_kerja_sama" value="{{ $kerjaSama->bentuk_kerja_sama }}">

        {{-- INFORMASI KERJA SAMA --}}
        <div style="margin-bottom: 24px; overflow: hidden; border-radius: 18px; border: 1px solid #DCE6E4; background: #FFFFFF; box-shadow: 0 4px 14px rgba(54, 95, 101, 0.05);">
            <div style="padding: 24px 36px; border-bottom: 1px solid #E4EBE9;">
                <h3 style="margin: 0; font-size: 18px; line-height: 1.4; font-weight: 700; color: #365F65;">Informasi Kerja Sama</h3>
                <p style="margin: 6px 0 0; font-size: 14px; font-weight: 400; color: #8BA5A2;">Informasi berikut diambil otomatis dari data Kerja Sama.</p>
            </div>
            <div style="padding: 8px 36px 16px;">
                <div style="display: grid; grid-template-columns: 220px 1fr; gap: 24px; padding: 20px 0; border-bottom: 1px solid #EEF2F1;">
                    <div style="font-size: 14px; font-weight: 600; color: #527D82;">Mitra Usaha</div>
                    <div style="font-size: 15px; font-weight: 400; color: #365F65;">{{ $kerjaSama->mitra_usaha ?: '-' }}</div>
                </div>
                <div style="display: grid; grid-template-columns: 220px 1fr; gap: 24px; padding: 20px 0; border-bottom: 1px solid #EEF2F1;">
                    <div style="font-size: 14px; font-weight: 600; color: #527D82;">Nama Brand/Tenant</div>
                    <div style="font-size: 15px; font-weight: 400; color: #365F65;">{{ $kerjaSama->brand ?: '-' }}</div>
                </div>
                <div style="display: grid; grid-template-columns: 220px 1fr; gap: 24px; padding: 20px 0; border-bottom: 1px solid #EEF2F1;">
                    <div style="font-size: 14px; font-weight: 600; color: #527D82;">Jenis Usaha</div>
                    <div style="font-size: 15px; font-weight: 400; color: #365F65;">{{ $kerjaSama->jenis_usaha ?: '-' }}</div>
                </div>
                <div style="display: grid; grid-template-columns: 220px 1fr; gap: 24px; padding: 20px 0; border-bottom: 1px solid #EEF2F1;">
                    <div style="font-size: 14px; font-weight: 600; color: #527D82;">Bentuk Kerja Sama</div>
                    <div style="font-size: 15px; font-weight: 400; color: #365F65;">{{ $kerjaSama->bentuk_kerja_sama ?: '-' }}</div>
                </div>
                <div style="display: grid; grid-template-columns: 220px 1fr; gap: 24px; padding: 20px 0;">
                    <div style="font-size: 14px; font-weight: 600; color: #527D82;">PIC Commercial</div>
                    <div style="font-size: 15px; font-weight: 400; color: #365F65;">{{ $kerjaSama->pic_commercial ?: '-' }}</div>
                </div>
            </div>
        </div>

        {{-- INFORMASI KOMERSIAL TENANT --}}
        <div style="margin-bottom: 24px; overflow: hidden; border-radius: 18px; border: 1px solid #DCE6E4; background: #FFFFFF; box-shadow: 0 4px 14px rgba(54, 95, 101, 0.05);">
            <div style="padding: 24px 36px; border-bottom: 1px solid #E4EBE9;">
                <h3 style="margin: 0; font-size: 18px; line-height: 1.4; font-weight: 700; color: #365F65;">Informasi Komersial Tenant</h3>
                <p style="margin: 6px 0 0; font-size: 14px; line-height: 1.5; color: #8BA5A2;">
                    Masukkan nilai minimum omzet bulanan dan persentase revenue sharing sesuai kesepakatan kerja sama.
                </p>
            </div>

            <div style="padding: 28px 36px 32px;">
                {{-- GTO --}}
                <div style="margin-bottom: 24px;">
                    <label for="gto" style="display: block; margin-bottom: 10px; font-size: 15px; font-weight: 600; color: #527D82;">
                        GTO (Guaranteed Turnover) <span style="color: #B96B60;">*</span>
                    </label>
                    <div style="position: relative;">
                        <span style="position: absolute; left: 22px; top: 50%; transform: translateY(-50%); color: #527D82; font-size: 15px;">Rp</span>
                        <input type="number" id="gto" name="gto" value="{{ old('gto') }}" min="0" step="0.01" required placeholder="Contoh: 20000000"
                               style="display: block; width: 100%; height: 56px; box-sizing: border-box; padding: 0 22px 0 52px; border: 1px solid #DCE6E4; border-radius: 14px; background: #F7F9F8; color: #365F65; font-family: inherit; font-size: 15px; outline: none;">
                    </div>
                    <p style="margin: 7px 0 0; color: #8BA5A2; font-size: 13px; line-height: 1.5;">Nilai minimum omzet yang disepakati untuk setiap bulan.</p>
                    @error('gto')
                        <p style="margin: 7px 0 0; color: #B96B60; font-size: 13px;">{{ $message }}</p>
                    @enderror
                </div>

                {{-- REVENUE SHARING --}}
                <div>
                    <label for="revenue_sharing" style="display: block; margin-bottom: 10px; font-size: 15px; font-weight: 600; color: #527D82;">
                        Revenue Sharing <span style="color: #B96B60;">*</span>
                    </label>
                    <div style="position: relative;">
                        <input type="number" id="revenue_sharing" name="revenue_sharing" value="{{ old('revenue_sharing') }}" min="0" max="100" step="0.01" required placeholder="Contoh: 10"
                               style="display: block; width: 100%; height: 56px; box-sizing: border-box; padding: 0 52px 0 22px; border: 1px solid #DCE6E4; border-radius: 14px; background: #F7F9F8; color: #365F65; font-family: inherit; font-size: 15px; outline: none;">
                        <span style="position: absolute; right: 22px; top: 50%; transform: translateY(-50%); color: #527D82; font-size: 15px;">%</span>
                    </div>
                    <p style="margin: 7px 0 0; color: #8BA5A2; font-size: 13px; line-height: 1.5;">Persentase bagi hasil sesuai kesepakatan mitra dan AP/Komersil.</p>
                    @error('revenue_sharing')
                        <p style="margin: 7px 0 0; color: #B96B60; font-size: 13px;">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- INFORMASI LEGALITAS TENANT --}}
        <div style="margin-bottom: 24px; overflow: hidden; border-radius: 18px; border: 1px solid #DCE6E4; background: #FFFFFF; box-shadow: 0 4px 14px rgba(54, 95, 101, 0.05);">
            <div style="padding: 24px 36px; border-bottom: 1px solid #E4EBE9;">
                <h3 style="margin: 0; font-size: 18px; line-height: 1.4; font-weight: 700; color: #365F65;">Informasi Legalitas Tenant</h3>
                <p style="margin: 6px 0 0; font-size: 14px; line-height: 1.5; color: #8BA5A2;">Masukkan informasi legalitas perusahaan atau usaha tenant.</p>
            </div>

            <div style="padding: 28px 36px 32px;">
                {{-- NPWP --}}
                <div style="margin-bottom: 24px;">
                    <label for="npwp" style="display: block; margin-bottom: 10px; font-size: 15px; font-weight: 600; color: #527D82;">
                        NPWP <span style="color: #B96B60;">*</span>
                    </label>
                    <input type="text" id="npwp" name="npwp" value="{{ old('npwp') }}" placeholder="Masukkan nomor NPWP" inputmode="numeric" autocomplete="off" required
                           style="display: block; width: 100%; height: 56px; box-sizing: border-box; padding: 0 22px; border: 1px solid #DCE6E4; border-radius: 14px; background: #F7F9F8; color: #365F65; font-family: inherit; font-size: 15px; outline: none;">
                    @error('npwp')
                        <p style="margin: 7px 0 0; color: #B96B60; font-size: 13px;">{{ $message }}</p>
                    @enderror
                </div>

                {{-- NIB --}}
                <div style="margin-bottom: 24px;">
                    <label for="nib" style="display: block; margin-bottom: 10px; font-size: 15px; font-weight: 600; color: #527D82;">
                        NIB <span style="color: #B96B60;">*</span>
                    </label>
                    <input type="text" id="nib" name="nib" value="{{ old('nib') }}" placeholder="Masukkan nomor NIB" inputmode="numeric" autocomplete="off" required
                           style="display: block; width: 100%; height: 56px; box-sizing: border-box; padding: 0 22px; border: 1px solid #DCE6E4; border-radius: 14px; background: #F7F9F8; color: #365F65; font-family: inherit; font-size: 15px; outline: none;">
                    @error('nib')
                        <p style="margin: 7px 0 0; color: #B96B60; font-size: 13px;">{{ $message }}</p>
                    @enderror
                </div>

                {{-- BENTUK BADAN USAHA --}}
                <div style="margin-bottom: 24px;">
                    <label for="bentuk_badan_usaha" style="display: block; margin-bottom: 10px; font-size: 15px; font-weight: 600; color: #527D82;">
                        Bentuk Badan Usaha <span style="color: #B96B60;">*</span>
                    </label>
                    <div style="position: relative;">
                        <select id="bentuk_badan_usaha" name="bentuk_badan_usaha" required
                                style="display: block; width: 100%; height: 56px; box-sizing: border-box; padding: 0 52px 0 22px; border: 1px solid #DCE6E4; border-radius: 14px; background: #F7F9F8; color: #365F65; font-family: inherit; font-size: 15px; outline: none; appearance: none; -webkit-appearance: none; -moz-appearance: none; cursor: pointer;">
                            <option value="">Pilih bentuk badan usaha</option>
                            <option value="PT" {{ old('bentuk_badan_usaha') == 'PT' ? 'selected' : '' }}>Perseroan Terbatas (PT)</option>
                            <option value="CV" {{ old('bentuk_badan_usaha') == 'CV' ? 'selected' : '' }}>Commanditaire Vennootschap (CV)</option>
                            <option value="Firma" {{ old('bentuk_badan_usaha') == 'Firma' ? 'selected' : '' }}>Firma</option>
                            <option value="Koperasi" {{ old('bentuk_badan_usaha') == 'Koperasi' ? 'selected' : '' }}>Koperasi</option>
                            <option value="Perorangan" {{ old('bentuk_badan_usaha') == 'Perorangan' ? 'selected' : '' }}>Usaha Perorangan</option>
                            <option value="Lainnya" {{ old('bentuk_badan_usaha') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                        <span style="position: absolute; top: 50%; right: 20px; transform: translateY(-50%); pointer-events: none; color: #527D82; font-size: 15px;">▼</span>
                    </div>
                    @error('bentuk_badan_usaha')
                        <p style="margin: 7px 0 0; color: #B96B60; font-size: 13px;">{{ $message }}</p>
                    @enderror
                </div>

                {{-- ALAMAT PERUSAHAAN / USAHA --}}
                <div>
                    <label for="alamat_perusahaan" style="display: block; margin-bottom: 10px; font-size: 15px; font-weight: 600; color: #527D82;">
                        Alamat Perusahaan/Usaha <span style="color: #B96B60;">*</span>
                    </label>
                    <textarea id="alamat_perusahaan" name="alamat_perusahaan" rows="4" required placeholder="Masukkan alamat perusahaan atau usaha"
                              style="display: block; width: 100%; min-height: 120px; box-sizing: border-box; padding: 17px 22px; border: 1px solid #DCE6E4; border-radius: 14px; background: #F7F9F8; color: #365F65; font-family: inherit; font-size: 15px; line-height: 1.6; outline: none; resize: vertical;">{{ old('alamat_perusahaan') }}</textarea>
                    @error('alamat_perusahaan')
                        <p style="margin: 7px 0 0; color: #B96B60; font-size: 13px;">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- INFORMASI PIC TENANT --}}
        <div style="margin-bottom: 24px; overflow: hidden; border-radius: 18px; border: 1px solid #DCE6E4; background: #FFFFFF; box-shadow: 0 4px 14px rgba(54, 95, 101, 0.05);">
            <div style="padding: 24px 36px; border-bottom: 1px solid #E4EBE9;">
                <h3 style="margin: 0; font-size: 18px; line-height: 1.4; font-weight: 700; color: #365F65;">Informasi PIC Tenant</h3>
                <p style="margin: 6px 0 0; font-size: 14px; line-height: 1.5; font-weight: 400; color: #8BA5A2;">Masukkan informasi PIC yang bertanggung jawab dari pihak tenant.</p>
            </div>

            <div style="padding: 28px 36px 32px;">
                {{-- NAMA PIC --}}
                <div style="margin-bottom: 24px;">
                    <label for="nama_pic" style="display: block; margin-bottom: 10px; font-size: 15px; font-weight: 600; color: #527D82;">
                        Nama PIC <span style="color: #B96B60;">*</span>
                    </label>
                    <input type="text" id="nama_pic" name="nama_pic" value="{{ old('nama_pic') }}" placeholder="Masukkan nama PIC tenant" autocomplete="off" required
                           style="display: block; width: 100%; height: 56px; box-sizing: border-box; padding: 0 22px; border: 1px solid #DCE6E4; border-radius: 14px; background: #F7F9F8; color: #365F65; font-family: inherit; font-size: 15px; outline: none;">
                    @error('nama_pic')
                        <p style="margin: 7px 0 0; color: #B96B60; font-size: 13px;">{{ $message }}</p>
                    @enderror
                </div>

                {{-- JABATAN --}}
                <div style="margin-bottom: 24px;">
                    <label for="jabatan_pic" style="display: block; margin-bottom: 10px; font-size: 15px; font-weight: 600; color: #527D82;">
                        Jabatan <span style="color: #B96B60;">*</span>
                    </label>
                    <input type="text" id="jabatan_pic" name="jabatan_pic" value="{{ old('jabatan_pic') }}" placeholder="Masukkan jabatan PIC" autocomplete="off" required
                           style="display: block; width: 100%; height: 56px; box-sizing: border-box; padding: 0 22px; border: 1px solid #DCE6E4; border-radius: 14px; background: #F7F9F8; color: #365F65; font-family: inherit; font-size: 15px; outline: none;">
                    @error('jabatan_pic')
                        <p style="margin: 7px 0 0; color: #B96B60; font-size: 13px;">{{ $message }}</p>
                    @enderror
                </div>

                {{-- NO. HP --}}
                <div style="margin-bottom: 24px;">
                    <label for="no_hp_pic" style="display: block; margin-bottom: 10px; font-size: 15px; font-weight: 600; color: #527D82;">
                        No. HP <span style="color: #B96B60;">*</span>
                    </label>
                    <input type="text" id="no_hp_pic" name="no_hp_pic" value="{{ old('no_hp_pic') }}" placeholder="Masukkan nomor HP PIC" inputmode="numeric" autocomplete="off" required
                           style="display: block; width: 100%; height: 56px; box-sizing: border-box; padding: 0 22px; border: 1px solid #DCE6E4; border-radius: 14px; background: #F7F9F8; color: #365F65; font-family: inherit; font-size: 15px; outline: none;">
                    @error('no_hp_pic')
                        <p style="margin: 7px 0 0; color: #B96B60; font-size: 13px;">{{ $message }}</p>
                    @enderror
                </div>

                {{-- EMAIL --}}
                <div>
                    <label for="email_pic" style="display: block; margin-bottom: 10px; font-size: 15px; font-weight: 600; color: #527D82;">
                        Email <span style="color: #B96B60;">*</span>
                    </label>
                    <input type="email" id="email_pic" name="email_pic" value="{{ old('email_pic') }}" placeholder="Masukkan email PIC" autocomplete="email" required
                           style="display: block; width: 100%; height: 56px; box-sizing: border-box; padding: 0 22px; border: 1px solid #DCE6E4; border-radius: 14px; background: #F7F9F8; color: #365F65; font-family: inherit; font-size: 15px; outline: none;">
                    @error('email_pic')
                        <p style="margin: 7px 0 0; color: #B96B60; font-size: 13px;">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- TOMBOL AKSI --}}
        <div style="display: flex; justify-content: flex-end; align-items: center; gap: 12px; padding-bottom: 8px;" class="flex-col-reverse sm:flex-row">
            <a href="{{ route('tenant.index') }}"
               style="display: inline-flex; align-items: center; justify-content: center; height: 48px; padding: 0 24px; border-radius: 12px; border: 1px solid #DCE6E4; background: #FFFFFF; color: #527D82; font-size: 14px; font-weight: 600; text-decoration: none; cursor: pointer;">
                Batal
            </a>

            <button type="submit"
                    style="display: inline-flex; align-items: center; justify-content: center; gap: 8px; height: 48px; padding: 0 22px; border: none; border-radius: 12px; background: #527D82; color: #FFFFFF; font-family: inherit; font-size: 14px; font-weight: 600; cursor: pointer;">
                <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M5 12l4 4L19 6"/>
                </svg>
                Simpan Data Tenant
            </button>
        </div>
    </form>
@endsection
