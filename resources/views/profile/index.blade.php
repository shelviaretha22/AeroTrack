
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
            {{ auth()->user()->peran }}
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
            <span class="profile-role-small">{{ auth()->user()->peran }}</span>
            <p class="mt-2 text-xs text-[#8BA5A2]">
                Bergabung {{ auth()->user()->created_at?->locale('id')->isoFormat('D MMMM YYYY') ?? '-' }}
                @if (auth()->user()->previous_login_at)
                    · Login sebelumnya {{ auth()->user()->previous_login_at->locale('id')->diffForHumans() }}
                @endif
            </p>
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

    {{-- RINGKASAN AKTIVITAS --}}
    @php
        $maksHarian = max(1, (int) $grafikHarian->max('jumlah'));
        $totalKomponen = max(1, (int) $perKomponen->sum());
        $warnaKomponen = ['Kontrak Fisik' => '#527D82', 'Kontrak Sistem' => '#6C8EBF', 'Aktivasi' => '#D6A14D'];
    @endphp
    <section class="profile-card">
        <div class="section-heading">
            <div class="section-icon">▥</div>
            <div>
                <h2>Ringkasan Aktivitas</h2>
                <p>Perubahan yang Anda lakukan di menu Aktivasi.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">

            <div class="grid grid-cols-3 gap-3 lg:grid-cols-1">
                @foreach ([
                    ['Total aktivitas', $statistik['total'], 'semua waktu'],
                    ['30 hari terakhir', $statistik['tigaPuluhHari'], 'perubahan dicatat'],
                    ['Kerja sama ditangani', $statistik['kerjaSama'], 'kerja sama berbeda'],
                ] as [$label, $angka, $ket])
                    <div class="rounded-xl border border-[#E2EBE9] bg-[#F8FAF9] p-4">
                        <p class="text-xs font-medium text-[#7A9290]">{{ $label }}</p>
                        <p class="mt-1 text-2xl font-bold text-[#365F65]">{{ $angka }}</p>
                        <p class="text-[11px] text-[#8BA5A2]">{{ $ket }}</p>
                    </div>
                @endforeach
            </div>

            <div class="rounded-xl border border-[#E2EBE9] p-4 lg:col-span-2">
                <div class="flex items-center justify-between gap-3">
                    <h3 class="text-sm font-bold text-[#365F65]">14 hari terakhir</h3>
                    <span class="text-xs text-[#8BA5A2]">puncak {{ $maksHarian }} per hari</span>
                </div>

                <div class="mt-4 flex h-32 items-end gap-1.5" role="img"
                     aria-label="Grafik aktivitas harian, total {{ $grafikHarian->sum('jumlah') }} dalam 14 hari">
                    @foreach ($grafikHarian as $hari)
                        <div class="group flex h-full flex-1 flex-col items-center justify-end gap-1.5"
                             title="{{ $hari['tanggal']->locale('id')->isoFormat('D MMM') }}: {{ $hari['jumlah'] }} aktivitas">
                            <div class="w-full rounded-t-md {{ $hari['jumlah'] > 0 ? 'bg-[#365F65] group-hover:bg-[#4F8189]' : 'bg-[#E7EFEC]' }}"
                                 style="height: {{ $hari['jumlah'] > 0 ? max(8, ($hari['jumlah'] / $maksHarian) * 100) : 4 }}%"></div>
                            <span class="text-[9px] text-[#8BA5A2] {{ $loop->index % 2 === 0 ? '' : 'hidden sm:block' }}">{{ $hari['tanggal']->format('d') }}</span>
                        </div>
                    @endforeach
                </div>

                @if ($perKomponen->isNotEmpty())
                    <div class="mt-5 border-t border-[#E2EBE9] pt-4">
                        <div class="flex h-2.5 overflow-hidden rounded-full bg-[#E7EFEC]" role="img" aria-label="Komposisi aktivitas per komponen">
                            @foreach ($perKomponen as $komponen => $n)
                                <div style="width: {{ ($n / $totalKomponen) * 100 }}%; background: {{ $warnaKomponen[$komponen] ?? '#8BA5A2' }}"
                                     title="{{ $komponen }}: {{ $n }}"></div>
                            @endforeach
                        </div>
                        <div class="mt-2 flex flex-wrap gap-x-4 gap-y-1 text-xs text-[#7A9290]">
                            @foreach ($perKomponen as $komponen => $n)
                                <span class="inline-flex items-center gap-1.5">
                                    <span class="h-2.5 w-2.5 rounded-full" style="background: {{ $warnaKomponen[$komponen] ?? '#8BA5A2' }}"></span>
                                    {{ $komponen }} <strong class="text-[#365F65]">{{ $n }}</strong>
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>

    {{-- AKTIVITAS TERAKHIR --}}
    <section class="profile-card">
        <div class="section-heading">
            <div class="section-icon">◷</div>
            <div>
                <h2>Aktivitas Terakhir</h2>
                <p>Delapan perubahan terbaru yang Anda lakukan.</p>
            </div>
        </div>

        @forelse ($aktivitasTerakhir as $r)
            @php
                $warnaTitik = ['Kontrak Fisik' => 'bg-[#527D82]', 'Kontrak Sistem' => 'bg-[#6C8EBF]', 'Aktivasi' => 'bg-[#D6A14D]'][$r->komponen] ?? 'bg-[#8BA5A2]';
                $ks = $r->kerjaSama;
            @endphp
            <div class="relative flex gap-4 pb-5 last:pb-0">
                @unless ($loop->last)
                    <span class="absolute left-[5px] top-4 h-full w-px bg-[#E2EBE9]" aria-hidden="true"></span>
                @endunless
                <span class="relative z-10 mt-1.5 h-[11px] w-[11px] shrink-0 rounded-full ring-4 ring-white {{ $warnaTitik }}"></span>

                <div class="min-w-0 flex-1">
                    <p class="text-sm text-[#365F65]">{{ $r->keterangan }}</p>
                    <div class="mt-1 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-[#8BA5A2]">
                        <span class="rounded-full bg-[#F3F6F5] px-2 py-0.5 font-medium text-[#527D82]">{{ $r->komponen }}</span>
                        @if ($ks)
                            <a href="{{ route('aktivasi.show', $ks->id) }}" class="font-semibold text-[#527D82] hover:underline">
                                {{ $ks->mitra_usaha ?: 'KS-' . str_pad($ks->id, 4, '0', STR_PAD_LEFT) }}
                            </a>
                        @endif
                        <span title="{{ $r->created_at->format('d M Y H:i') }}">{{ $r->created_at->locale('id')->diffForHumans() }}</span>
                    </div>
                </div>
            </div>
        @empty
            <p class="rounded-xl bg-[#F8FAF9] px-4 py-8 text-center text-sm text-[#7A9290]">
                Belum ada aktivitas. Perubahan yang Anda catat di menu Aktivasi akan muncul di sini.
            </p>
        @endforelse
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
                        value="{{ auth()->user()->peran }}"
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

                <div class="form-group"
                     x-data="{
                        pw: '',
                        get skor() {
                            if (!this.pw) return 0;
                            let s = 0;
                            if (this.pw.length >= 8) s++;
                            if (this.pw.length >= 12) s++;
                            if (/[a-z]/.test(this.pw) && /[A-Z]/.test(this.pw)) s++;
                            if (/[0-9]/.test(this.pw)) s++;
                            if (/[^A-Za-z0-9]/.test(this.pw)) s++;
                            return s <= 2 ? 1 : (s === 3 ? 2 : (s === 4 ? 3 : 4));
                        },
                        get label() { return ['', 'Lemah', 'Cukup', 'Kuat', 'Sangat kuat'][this.skor]; },
                        get warna() { return ['', '#D14B4B', '#D6A14D', '#31966A', '#365F65'][this.skor]; }
                     }">
                    <label for="password">Password Baru</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        x-model="pw"
                        placeholder="Minimal 8 karakter"
                        autocomplete="new-password"
                        minlength="8"
                        required
                    >

                    <div class="mt-2" x-show="pw" style="display:none;">
                        <div class="flex gap-1" aria-hidden="true">
                            <template x-for="i in 4" :key="i">
                                <span class="h-1.5 flex-1 rounded-full transition-colors"
                                      :style="'background:' + (i <= skor ? warna : '#E7EFEC')"></span>
                            </template>
                        </div>
                        <p class="mt-1.5 text-xs" aria-live="polite">
                            Kekuatan: <strong :style="'color:' + warna" x-text="label"></strong>
                            <span class="text-[#8BA5A2]"> · campur huruf besar-kecil, angka, dan simbol</span>
                        </p>
                    </div>
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

    {{-- PERANGKAT DAN SESI --}}
    <section class="profile-card">
        <div class="section-heading">
            <div class="section-icon">▣</div>
            <div>
                <h2>Perangkat &amp; Sesi Aktif</h2>
                <p>Tempat akun Anda sedang masuk. Keluarkan yang tidak Anda kenali.</p>
            </div>
        </div>

        @php $user = auth()->user(); @endphp

        <div class="mb-5 grid grid-cols-1 gap-3 sm:grid-cols-2">
            <div class="rounded-xl border border-[#E2EBE9] bg-[#F8FAF9] p-4">
                <p class="text-xs font-medium text-[#7A9290]">Login sesi ini</p>
                <p class="mt-1 text-sm font-bold text-[#365F65]">{{ $user->last_login_at?->locale('id')->isoFormat('D MMM YYYY, HH:mm') ?? '-' }}</p>
                <p class="text-xs text-[#8BA5A2]">IP {{ $user->last_login_ip ?: '-' }}</p>
            </div>
            <div class="rounded-xl border border-[#E2EBE9] bg-[#F8FAF9] p-4">
                <p class="text-xs font-medium text-[#7A9290]">Login sebelumnya</p>
                <p class="mt-1 text-sm font-bold text-[#365F65]">{{ $user->previous_login_at?->locale('id')->isoFormat('D MMM YYYY, HH:mm') ?? 'Belum ada' }}</p>
                <p class="text-xs text-[#8BA5A2]">IP {{ $user->previous_login_ip ?: '-' }}</p>
            </div>
        </div>

        @if ($sesiTersedia)
            <ul class="divide-y divide-[#E2EBE9] rounded-xl border border-[#E2EBE9]">
                @forelse ($sesi as $s)
                    <li class="flex items-center gap-4 px-4 py-3.5">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#EAF2F1] text-[#365F65]" aria-hidden="true">
                            @if ($s['agen']['seluler'])
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="7" y="2" width="10" height="20" rx="2"/><path d="M11 18h2"/></svg>
                            @else
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="12" rx="2"/><path d="M8 20h8M12 16v4"/></svg>
                            @endif
                        </span>

                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-semibold text-[#365F65]">
                                {{ $s['agen']['browser'] }} di {{ $s['agen']['os'] }}
                                @if ($s['ini'])
                                    <span class="ml-1.5 rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-bold text-emerald-700">Perangkat ini</span>
                                @endif
                            </p>
                            <p class="text-xs text-[#8BA5A2]">
                                IP {{ $s['ip'] ?: '-' }} · aktif {{ $s['aktif']->locale('id')->diffForHumans() }}
                            </p>
                        </div>
                    </li>
                @empty
                    <li class="px-4 py-6 text-center text-sm text-[#7A9290]">Tidak ada sesi tercatat.</li>
                @endforelse
            </ul>

            @if ($sesi->where('ini', false)->count() > 0)
                <form action="{{ route('profile.sesi.keluar-lainnya') }}" method="POST"
                      class="mt-5 flex flex-col gap-3 sm:flex-row sm:items-end"
                      onsubmit="return confirm('Keluarkan akun dari semua perangkat lain?');">
                    @csrf
                    <div class="form-group flex-1">
                        <label for="password_sesi">Password saat ini</label>
                        <input type="password" id="password_sesi" name="password_sesi"
                               placeholder="Untuk konfirmasi" autocomplete="current-password" required>
                    </div>
                    <button type="submit" class="btn-secondary" style="color:#a33232;border-color:#f0cece;">
                        Keluarkan dari perangkat lain
                    </button>
                </form>
            @endif
        @else
            <p class="rounded-xl bg-[#F8FAF9] px-4 py-6 text-center text-sm text-[#7A9290]">
                Daftar sesi tersedia bila sesi disimpan di database (<code>SESSION_DRIVER=database</code>).
            </p>
        @endif
    </section>

    {{-- PREFERENSI --}}
    <section class="profile-card" id="preferensi">
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
                    <option value="dark" disabled>
                        Gelap (segera hadir)
                    </option>
                </select>
            </div>

            <div class="preference-row">
                <div class="preference-description">
                    <h3>Ringkasan Email Harian</h3>
                    <p>Kirim email setiap pagi (07.00) berisi hal yang perlu tindakan dan belum dibaca. Email hanya dikirim bila ada isinya.</p>
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

            <div class="mt-6">
                <h3 class="text-sm font-bold text-[#365F65]">Jenis notifikasi yang ingin diterima</h3>
                <p class="mt-1 text-xs text-[#8BA5A2]">Berlaku untuk lonceng di header, halaman Notifikasi, dan email ringkasan.</p>

                @foreach (['tindakan' => 'Perlu tindakan', 'info' => 'Informasi'] as $kategori => $judulKategori)
                    <p class="mb-1 mt-5 text-[11px] font-bold uppercase tracking-wide text-[#8BA5A2]">{{ $judulKategori }}</p>

                    @foreach ($jenisNotifikasi as $kode => $info)
                        @continue($info['kategori'] !== $kategori)
                        <div class="preference-row">
                            <div class="preference-description">
                                <h3>{{ $info['label'] }}</h3>
                                <p>{{ $info['deskripsi'] }}</p>
                            </div>

                            <label class="switch">
                                <input
                                    type="checkbox"
                                    name="notif[{{ $kode }}]"
                                    value="1"
                                    @checked($preferensiNotifikasi[$kode] ?? true)
                                >
                                <span class="slider"></span>
                            </label>
                        </div>
                    @endforeach
                @endforeach
            </div>

            <div class="form-footer">
                <button type="submit" class="btn-primary">
                    Simpan Preferensi
                </button>
            </div>
        </form>
    </section>

       </section>

    {{-- IDENTITAS PERUSAHAAN --}}
    <section class="profile-card">
        <div class="section-heading">
            <div class="section-icon">🏢</div>
            <div>
                <h2>Identitas Perusahaan</h2>
                <p>Kelola informasi perusahaan yang digunakan dalam dokumen kontrak AeroTrack.</p>
            </div>
        </div>

        <div class="preference-row">
            <div class="preference-description">
                <h3>Master Perusahaan</h3>
                <p>
                    Atur nama perusahaan, alamat, domisili, serta informasi
                    penandatangan sebagai Pihak Pertama dalam dokumen kontrak.
                </p>
            </div>

            <a href="{{ route('profile.perusahaan.edit') }}" class="btn-primary">
                Kelola Identitas
            </a>
        </div>
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
        --profile-green: #365F65;
        --profile-green-dark: #294F55;
        --profile-bg: #f3f6f5;
        --profile-border: #E2EBE9;
        --profile-text: #365F65;
        --profile-muted: #7A9290;
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
        background: #EAF2F1;
        color: var(--profile-green);
        font-size: 12px;
        font-weight: 700;
        white-space: nowrap;
    }

    .status-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: #4F8189;
    }

    .profile-card {
        padding: 24px;
        margin-bottom: 20px;
        border: 1px solid var(--profile-border);
        border-radius: 16px;
        background: #fff;
        box-shadow: 0 3px 12px rgba(54, 95, 101, 0.04);
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
        border: 3px solid #DCE9E7;
    }

    .avatar-placeholder {
        justify-content: center;
        align-items: center;
        background: #DCE9E7;
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
        border: 1px solid #DCE6E4;
        background: #fff;
        color: var(--profile-green);
    }

    .btn-secondary:hover {
        background: #F3F6F5;
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
        background: #EAF2F1;
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
        color: #527D82;
        font-size: 12px;
        font-weight: 700;
    }

    .form-group input,
    .preference-row select {
        width: 100%;
        min-width: 0;
        min-height: 44px;
        padding: 11px 13px;
        border: 1px solid #DCE6E4;
        border-radius: 9px;
        background: #fff;
        color: #365F65;
        font: inherit;
        font-size: 13px;
        outline: none;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .form-group input:focus,
    .preference-row select:focus {
        border-color: var(--profile-green);
        box-shadow: 0 0 0 3px rgba(82, 125, 130, 0.15);
    }

    .form-group input:disabled {
        background: #F3F6F5;
        color: #7A9290;
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
        border-top: 1px solid #E2EBE9;
    }

    .preference-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        padding: 17px 0;
        border-bottom: 1px solid #E2EBE9;
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
        background: #C9D6D3;
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
        outline: 3px solid #B6C7C4;
        outline-offset: 2px;
    }

    .system-info {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        background: #F8FAF9;
    }

    .system-info-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        width: 30px;
        height: 30px;
        border: 1px solid #DCE6E4;
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