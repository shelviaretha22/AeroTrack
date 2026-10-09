
@extends('layouts.app')

@section('page-title', 'Profile & Pengaturan')

@section('content')
<div class="profile-page">

    <div class="profile-heading">
        <div>
            <h1>Profile & Pengaturan</h1>
            <p>Kelola informasi akun, keamanan, dan preferensi AeroTrack.</p>
        </div>
        <span class="profile-role">
            <span class="status-dot"></span>
            Commercial
        </span>
    </div>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-error">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-error">
            <strong>Periksa kembali data yang dimasukkan.</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- FOTO PROFIL DAN INFORMASI SINGKAT --}}
    <section class="profile-card profile-overview">
        <div class="avatar-wrapper">
            @if (auth()->user()->avatar_path)
                <img
                    src="{{ asset('storage/' . auth()->user()->avatar_path) }}"
                    alt="Foto profil"
                    class="profile-avatar"
                >
            @else
                <div class="profile-avatar avatar-placeholder">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
            @endif
        </div>

        <div class="profile-overview-info">
            <h2>{{ auth()->user()->name }}</h2>
            <p>{{ auth()->user()->email }}</p>
            <span class="profile-role-small">Commercial</span>
        </div>

        <div class="profile-overview-action">
            <button
                type="button"
                class="btn-secondary"
                onclick="document.getElementById('photo-input').click()"
            >
                <span>↑</span> Ganti Foto
            </button>
        </div>

        <form
            id="photo-form"
            action="{{ route('profile.photo.update') }}"
            method="POST"
            enctype="multipart/form-data"
            class="hidden-form"
        >
            @csrf
            @method('PATCH')

            <input
                type="file"
                name="avatar"
                id="photo-input"
                accept="image/jpeg,image/png,image/webp"
                onchange="document.getElementById('photo-form').submit()"
            >
        </form>
    </section>

    {{-- INFORMASI AKUN --}}
    <section class="profile-card">
        <div class="section-heading">
            <div class="section-icon">♙</div>
            <div>
                <h2>Informasi Akun</h2>
                <p>Perbarui informasi dasar akun Anda.</p>
            </div>
        </div>

        <form action="{{ route('profile.update') }}" method="POST">
            @csrf
            @method('PATCH')

            <div class="form-grid">
                <div class="form-group">
                    <label for="name">Nama Lengkap</label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name', auth()->user()->name) }}"
                        placeholder="Masukkan nama lengkap"
                        required
                        maxlength="255"
                    >
                </div>

                <div class="form-group">
                    <label for="email">Alamat Email</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email', auth()->user()->email) }}"
                        placeholder="nama@email.com"
                        required
                        maxlength="255"
                    >
                </div>

                <div class="form-group">
                    <label for="role">Role Pengguna</label>
                    <input
                        type="text"
                        id="role"
                        value="Commercial"
                        disabled
                    >
                    <small>Role mengikuti hak akses akun.</small>
                </div>

                <div class="form-group">
                    <label for="created_at">Tanggal Bergabung</label>
                    <input
                        type="text"
                        id="created_at"
                        value="{{ auth()->user()->created_at?->format('d F Y') ?? '-' }}"
                        disabled
                    >
                </div>
            </div>

            <div class="form-footer">
                <button type="submit" class="btn-primary">
                    Simpan Informasi
                </button>
            </div>
        </form>
    </section>

    {{-- KEAMANAN DAN PASSWORD --}}
    <section class="profile-card">
        <div class="section-heading">
            <div class="section-icon">♧</div>
            <div>
                <h2>Keamanan Akun</h2>
                <p>Gunakan password yang kuat untuk menjaga akun Anda.</p>
            </div>
        </div>

        <form action="{{ route('profile.password.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-grid">
                <div class="form-group full-width">
                    <label for="current_password">Password Saat Ini</label>
                    <input
                        type="password"
                        id="current_password"
                        name="current_password"
                        placeholder="Masukkan password saat ini"
                        autocomplete="current-password"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="password">Password Baru</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Minimal 8 karakter"
                        autocomplete="new-password"
                        minlength="8"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="password_confirmation">Konfirmasi Password</label>
                    <input
                        type="password"
                        id="password_confirmation"
                        name="password_confirmation"
                        placeholder="Ulangi password baru"
                        autocomplete="new-password"
                        minlength="8"
                        required
                    >
                </div>
            </div>

            <div class="form-footer">
                <button type="submit" class="btn-primary">
                    Perbarui Password
                </button>
            </div>
        </form>
    </section>

    {{-- PREFERENSI --}}
    <section class="profile-card">
        <div class="section-heading">
            <div class="section-icon">⚙</div>
            <div>
                <h2>Preferensi</h2>
                <p>Atur pengalaman penggunaan AeroTrack.</p>
            </div>
        </div>

        <form action="{{ route('profile.preferences.update') }}" method="POST">
            @csrf
            @method('PATCH')

            <div class="preference-row">
                <div class="preference-description">
                    <h3>Tampilan Aplikasi</h3>
                    <p>Pilih tampilan yang nyaman digunakan.</p>
                </div>

                <select name="theme_preference" aria-label="Tampilan aplikasi">
                    <option
                        value="light"
                        @selected(old('theme_preference', auth()->user()->theme_preference ?? 'light') === 'light')
                    >
                        Terang
                    </option>
                    <option
                        value="dark"
                        @selected(old('theme_preference', auth()->user()->theme_preference ?? 'light') === 'dark')
                    >
                        Gelap
                    </option>
                </select>
            </div>

            <div class="preference-row">
                <div class="preference-description">
                    <h3>Notifikasi Email</h3>
                    <p>Izinkan pemberitahuan dikirim melalui email.</p>
                </div>

                <label class="switch">
                    <input
                        type="checkbox"
                        name="email_notifications"
                        value="1"
                        @checked(old('email_notifications', auth()->user()->email_notifications ?? true))
                    >
                    <span class="slider"></span>
                </label>
            </div>

            <div class="form-footer">
                <button type="submit" class="btn-primary">
                    Simpan Preferensi
                </button>
            </div>
        </form>
    </section>

    {{-- INFORMASI SISTEM --}}
    <section class="profile-card system-info">
        <div class="system-info-icon">i</div>
        <div>
            <h3>Akun AeroTrack</h3>
            <p>
                Informasi profil digunakan untuk mengidentifikasi pengguna
                dalam sistem monitoring persewaan tenant bandara.
            </p>
        </div>
        <span class="system-version">AeroTrack</span>
    </section>

</div>

<style>
    .profile-page {
        --profile-green: #246b53;
        --profile-green-dark: #19523f;
        --profile-bg: #f3f6f5;
        --profile-border: #e5ebe8;
        --profile-text: #263b33;
        --profile-muted: #7b8982;
        color: var(--profile-text);
        width: 100%;
        max-width: 1200px;
        margin: 0 auto;
        padding-bottom: 32px;
    }

    .profile-page * {
        box-sizing: border-box;
    }

    .profile-heading {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        margin-bottom: 24px;
    }

    .profile-heading h1 {
        margin: 0 0 8px;
        font-size: clamp(22px, 3vw, 28px);
        font-weight: 750;
        letter-spacing: -0.7px;
    }

    .profile-heading p,
    .section-heading p {
        margin: 0;
        color: var(--profile-muted);
        font-size: 13px;
        line-height: 1.7;
    }

    .profile-role,
    .profile-role-small {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 12px;
        border-radius: 999px;
        background: #e7f2ec;
        color: var(--profile-green);
        font-size: 12px;
        font-weight: 700;
        white-space: nowrap;
    }

    .status-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: #2c9368;
    }

    .profile-card {
        padding: 24px;
        margin-bottom: 20px;
        border: 1px solid var(--profile-border);
        border-radius: 16px;
        background: #fff;
        box-shadow: 0 3px 12px rgba(24, 54, 40, 0.025);
    }

    .profile-overview {
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .avatar-wrapper {
        flex-shrink: 0;
    }

    .profile-avatar {
        display: flex;
        width: 76px;
        height: 76px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #e6f0ea;
    }

    .avatar-placeholder {
        justify-content: center;
        align-items: center;
        background: #dcece2;
        color: var(--profile-green);
        font-size: 30px;
        font-weight: 750;
    }

    .profile-overview-info {
        flex: 1;
        min-width: 0;
    }

    .profile-overview-info h2 {
        margin: 0 0 6px;
        font-size: 19px;
        font-weight: 750;
        overflow-wrap: anywhere;
    }

    .profile-overview-info p {
        margin: 0 0 10px;
        color: var(--profile-muted);
        font-size: 13px;
        overflow-wrap: anywhere;
    }

    .profile-role-small {
        padding: 5px 10px;
        font-size: 11px;
    }

    .btn-primary,
    .btn-secondary {
        display: inline-flex;
        justify-content: center;
        align-items: center;
        gap: 8px;
        min-height: 42px;
        padding: 11px 18px;
        border-radius: 9px;
        font: inherit;
        font-size: 13px;
        font-weight: 650;
        cursor: pointer;
        transition: background 0.2s, border-color 0.2s;
    }

    .btn-primary {
        border: 1px solid var(--profile-green);
        background: var(--profile-green);
        color: white;
    }

    .btn-primary:hover {
        background: var(--profile-green-dark);
    }

    .btn-secondary {
        border: 1px solid #dce6df;
        background: #fff;
        color: var(--profile-green);
    }

    .btn-secondary:hover {
        background: #f0f6f2;
    }

    .section-heading {
        display: flex;
        align-items: center;
        gap: 13px;
        margin-bottom: 25px;
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
        color: var(--profile-green);
        font-size: 22px;
    }

    .section-heading h2 {
        margin: 0 0 5px;
        font-size: 16px;
        font-weight: 750;
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
    .preference-row select {
        width: 100%;
        min-width: 0;
        min-height: 44px;
        padding: 11px 13px;
        border: 1px solid #dfe7e2;
        border-radius: 9px;
        background: #fff;
        color: #304239;
        font: inherit;
        font-size: 13px;
        outline: none;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .form-group input:focus,
    .preference-row select:focus {
        border-color: var(--profile-green);
        box-shadow: 0 0 0 3px rgba(36, 107, 83, 0.1);
    }

    .form-group input:disabled {
        background: #f4f7f5;
        color: #78867e;
        cursor: not-allowed;
    }

    .form-group small {
        color: var(--profile-muted);
        font-size: 11px;
    }

    .full-width {
        grid-column: 1 / -1;
    }

    .form-footer {
        display: flex;
        justify-content: flex-end;
        margin-top: 24px;
        padding-top: 20px;
        border-top: 1px solid #edf1ee;
    }

    .preference-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        padding: 17px 0;
        border-bottom: 1px solid #edf1ee;
    }

    .preference-row:first-of-type {
        padding-top: 0;
    }

    .preference-row select {
        width: 150px;
    }

    .preference-description h3 {
        margin: 0 0 6px;
        font-size: 13px;
        font-weight: 700;
    }

    .preference-description p {
        margin: 0;
        color: var(--profile-muted);
        font-size: 12px;
        line-height: 1.6;
    }

    .switch {
        position: relative;
        display: inline-flex;
        width: 44px;
        height: 25px;
        flex-shrink: 0;
    }

    .switch input {
        width: 0;
        height: 0;
        opacity: 0;
    }

    .slider {
        position: absolute;
        inset: 0;
        border-radius: 99px;
        background: #d6dfd9;
        cursor: pointer;
        transition: background 0.2s;
    }

    .slider::before {
        content: "";
        position: absolute;
        width: 19px;
        height: 19px;
        top: 3px;
        left: 3px;
        border-radius: 50%;
        background: #fff;
        box-shadow: 0 1px 3px #0002;
        transition: transform 0.2s;
    }

    .switch input:checked + .slider {
        background: var(--profile-green);
    }

    .switch input:checked + .slider::before {
        transform: translateX(19px);
    }

    .switch input:focus-visible + .slider {
        outline: 3px solid #b9d9c8;
        outline-offset: 2px;
    }

    .system-info {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        background: #f7faf8;
    }

    .system-info-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        width: 30px;
        height: 30px;
        border: 1px solid #cbded1;
        border-radius: 50%;
        color: var(--profile-green);
        font-weight: 750;
    }

    .system-info h3 {
        margin: 0 0 6px;
        font-size: 13px;
    }

    .system-info p {
        margin: 0;
        color: var(--profile-muted);
        font-size: 12px;
        line-height: 1.7;
    }

    .system-version {
        margin-left: auto;
        color: var(--profile-muted);
        font-size: 11px;
        white-space: nowrap;
    }

    .alert {
        padding: 14px 16px;
        margin-bottom: 18px;
        border-radius: 10px;
        font-size: 13px;
        line-height: 1.7;
    }

    .alert ul {
        margin: 8px 0 0;
        padding-left: 20px;
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

    .hidden-form {
        display: none;
    }

    @media (max-width: 640px) {
        .profile-heading {
            align-items: flex-start;
            flex-direction: column;
        }

        .profile-card {
            padding: 18px;
            border-radius: 13px;
        }

        .profile-overview {
            flex-wrap: wrap;
            gap: 14px;
        }

        .profile-avatar {
            width: 62px;
            height: 62px;
        }

        .profile-overview-info {
            flex-basis: calc(100% - 80px);
        }

        .profile-overview-action {
            width: 100%;
        }

        .profile-overview-action button {
            width: 100%;
        }

        .form-grid {
            grid-template-columns: minmax(0, 1fr);
            gap: 17px;
        }

        .full-width {
            grid-column: auto;
        }

        .preference-row {
            align-items: flex-start;
            gap: 12px;
        }

        .preference-row select {
            width: 115px;
        }

        .form-footer .btn-primary {
            width: 100%;
        }

        .system-version {
            display: none;
        }
    }
</style>
@endsection