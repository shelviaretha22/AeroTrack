
@extends('layouts.app')

@section('page-title', 'Identitas Perusahaan')

@section('content')
<div class="company-page">

    <div class="company-heading">
        <div>
            <h1>Identitas Perusahaan</h1>
            <p>Kelola informasi perusahaan yang digunakan sebagai Pihak Pertama dalam dokumen kontrak.</p>
        </div>

        <a href="{{ route('profile.index') }}" class="btn-secondary">
            Kembali ke Profil
        </a>
    </div>

    @if (session('success'))
        <div class="alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert-error">
            <strong>Periksa kembali data yang dimasukkan.</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form
        action="{{ route('profile.perusahaan.update') }}"
        method="POST"
        class="company-card"
    >
        @csrf
        @method('PUT')

        <div class="section-heading">
            <div class="section-icon">🏢</div>
            <div>
                <h2>Informasi Perusahaan</h2>
                <p>Informasi identitas resmi perusahaan.</p>
            </div>
        </div>

        <div class="form-grid">

            <div class="form-group full-width">
                <label for="nama_perusahaan">Nama Perusahaan</label>
                <input
                    type="text"
                    id="nama_perusahaan"
                    name="nama_perusahaan"
                    value="{{ old('nama_perusahaan', $perusahaan->nama_perusahaan) }}"
                    required
                >
            </div>

            <div class="form-group">
                <label for="domisili">Domisili</label>
                <input
                    type="text"
                    id="domisili"
                    name="domisili"
                    value="{{ old('domisili', $perusahaan->domisili) }}"
                >
            </div>

            <div class="form-group">
                <label for="telepon">Nomor Telepon</label>
                <input
                    type="text"
                    id="telepon"
                    name="telepon"
                    value="{{ old('telepon', $perusahaan->telepon) }}"
                >
            </div>

            <div class="form-group full-width">
                <label for="alamat">Alamat Perusahaan</label>
                <textarea
                    id="alamat"
                    name="alamat"
                    rows="3"
                >{{ old('alamat', $perusahaan->alamat) }}</textarea>
            </div>

        </div>

        <div class="section-heading signatory-heading">
            <div class="section-icon">✍</div>
            <div>
                <h2>Informasi Penandatangan</h2>
                <p>Data pejabat yang mewakili perusahaan dalam dokumen kontrak.</p>
            </div>
        </div>

        <div class="form-grid">

            <div class="form-group">
                <label for="nama_penandatangan">Nama Penandatangan</label>
                <input
                    type="text"
                    id="nama_penandatangan"
                    name="nama_penandatangan"
                    value="{{ old('nama_penandatangan', $perusahaan->nama_penandatangan) }}"
                >
            </div>

            <div class="form-group">
                <label for="jabatan_penandatangan">Jabatan</label>
                <input
                    type="text"
                    id="jabatan_penandatangan"
                    name="jabatan_penandatangan"
                    value="{{ old('jabatan_penandatangan', $perusahaan->jabatan_penandatangan) }}"
                >
            </div>

            <div class="form-group full-width">
                <label for="email">Email Perusahaan</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email', $perusahaan->email) }}"
                >
            </div>

        </div>

        <div class="form-footer">
            <a href="{{ route('profile.index') }}" class="btn-secondary">
                Batal
            </a>

            <button type="submit" class="btn-primary">
                Simpan Perubahan
            </button>
        </div>

    </form>

</div>

<style>
.company-page {
    --company-green: #246b53;
    --company-green-dark: #19523f;
    --company-muted: #7b8982;
    --company-border: #e5ebe8;
    color: #263b33;
    width: 100%;
    max-width: 1200px;
    margin: 0 auto;
    padding-bottom: 32px;
}

.company-page * {
    box-sizing: border-box;
}

.company-heading {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
    margin-bottom: 24px;
}

.company-heading h1 {
    margin: 0 0 8px;
    font-size: 26px;
    font-weight: 750;
}

.company-heading p,
.section-heading p {
    margin: 0;
    color: var(--company-muted);
    font-size: 13px;
    line-height: 1.7;
}

.company-card {
    padding: 26px;
    border: 1px solid var(--company-border);
    border-radius: 16px;
    background: #fff;
    box-shadow: 0 3px 12px rgba(24, 54, 40, 0.025);
}

.section-heading {
    display: flex;
    align-items: center;
    gap: 13px;
    margin-bottom: 24px;
}

.section-heading h2 {
    margin: 0 0 5px;
    font-size: 16px;
    font-weight: 750;
}

.section-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    width: 42px;
    height: 42px;
    border-radius: 12px;
    background: #eaf3ed;
    color: var(--company-green);
    font-size: 20px;
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 20px;
}

.form-group {
    display: flex;
    flex-direction: column;
    min-width: 0;
    gap: 8px;
}

.form-group label {
    color: #42544a;
    font-size: 12px;
    font-weight: 700;
}

.form-group input,
.form-group textarea {
    width: 100%;
    padding: 12px 13px;
    border: 1px solid #dfe7e2;
    border-radius: 9px;
    background: #fff;
    color: #304239;
    font: inherit;
    font-size: 13px;
    outline: none;
    transition: border-color 0.2s, box-shadow 0.2s;
}

.form-group input {
    min-height: 44px;
}

.form-group textarea {
    resize: vertical;
    min-height: 90px;
}

.form-group input:focus,
.form-group textarea:focus {
    border-color: var(--company-green);
    box-shadow: 0 0 0 3px rgba(36, 107, 83, 0.1);
}

.full-width {
    grid-column: 1 / -1;
}

.signatory-heading {
    margin-top: 34px;
    padding-top: 28px;
    border-top: 1px solid #edf1ee;
}

.form-footer {
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    margin-top: 28px;
    padding-top: 22px;
    border-top: 1px solid #edf1ee;
}

.btn-primary,
.btn-secondary {
    display: inline-flex;
    justify-content: center;
    align-items: center;
    min-height: 42px;
    padding: 11px 18px;
    border-radius: 9px;
    font: inherit;
    font-size: 13px;
    font-weight: 650;
    text-decoration: none;
    cursor: pointer;
}

.btn-primary {
    border: 1px solid var(--company-green);
    background: var(--company-green);
    color: white;
}

.btn-primary:hover {
    background: var(--company-green-dark);
}

.btn-secondary {
    border: 1px solid #dce6df;
    background: white;
    color: var(--company-green);
}

.alert-success,
.alert-error {
    padding: 14px 16px;
    margin-bottom: 18px;
    border-radius: 10px;
    font-size: 13px;
    line-height: 1.7;
}

.alert-success {
    border: 1px solid #c7e5d1;
    background: #eef9f1;
    color: #226c40;
}

.alert-error {
    border: 1px solid #f0cece;
    background: #fff2f2;
    color: #a33232;
}

.alert-error ul {
    margin: 8px 0 0;
    padding-left: 20px;
}

@media (max-width: 640px) {
    .company-heading {
        align-items: flex-start;
        flex-direction: column;
    }

    .company-card {
        padding: 18px;
    }

    .form-grid {
        grid-template-columns: 1fr;
        gap: 17px;
    }

    .full-width {
        grid-column: auto;
    }

    .form-footer {
        flex-direction: column-reverse;
    }

    .form-footer > * {
        width: 100%;
    }
}
</style>
@endsection
