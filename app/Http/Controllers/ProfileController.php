<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use App\Models\AktivasiRiwayat;
use App\Models\MasterPerusahaan;
use App\Services\NotifikasiService;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // ---- Ringkasan aktivitas (dari riwayat perubahan di menu Aktivasi)
        $riwayatUser = AktivasiRiwayat::where('user_id', $user->id);

        $statistik = [
            'total' => (clone $riwayatUser)->count(),
            'tigaPuluhHari' => (clone $riwayatUser)
                ->where('created_at', '>=', now()->subDays(30))
                ->count(),
            'kerjaSama' => (clone $riwayatUser)
                ->distinct()
                ->count('kerja_sama_id'),
        ];

        $perKomponen = (clone $riwayatUser)
            ->select('komponen', DB::raw('COUNT(*) as total'))
            ->groupBy('komponen')
            ->orderByDesc('total')
            ->pluck('total', 'komponen');

        // 14 hari terakhir, termasuk hari tanpa aktivitas.
        $perHari = (clone $riwayatUser)
            ->where('created_at', '>=', now()->subDays(13)->startOfDay())
            ->get(['created_at'])
            ->groupBy(fn ($r) => $r->created_at->format('Y-m-d'))
            ->map->count();

        $grafikHarian = collect(range(13, 0))->map(function ($mundur) use ($perHari) {
            $tanggal = now()->subDays($mundur);

            return [
                'tanggal' => $tanggal,
                'jumlah' => (int) ($perHari[$tanggal->format('Y-m-d')] ?? 0),
            ];
        });

        $aktivitasTerakhir = AktivasiRiwayat::with('kerjaSama')
            ->where('user_id', $user->id)
            ->latest()
            ->limit(8)
            ->get();

        // ---- Sesi login aktif (hanya jika sesi disimpan di database)
        $sesi = collect();
        $sesiTersedia = config('session.driver') === 'database';

        if ($sesiTersedia) {
            $sesi = DB::table(config('session.table', 'sessions'))
                ->where('user_id', $user->id)
                ->orderByDesc('last_activity')
                ->get()
                ->map(fn ($s) => [
                    'ip' => $s->ip_address,
                    'agen' => $this->bacaAgen($s->user_agent),
                    'aktif' => \Illuminate\Support\Carbon::createFromTimestamp($s->last_activity),
                    'ini' => $s->id === $request->session()->getId(),
                ]);
        }

        return view('profile.index', [
            'statistik' => $statistik,
            'perKomponen' => $perKomponen,
            'grafikHarian' => $grafikHarian,
            'aktivitasTerakhir' => $aktivitasTerakhir,
            'sesi' => $sesi,
            'sesiTersedia' => $sesiTersedia,
            'jenisNotifikasi' => NotifikasiService::JENIS,
            'preferensiNotifikasi' => $user->preferensiNotifikasi(),
        ]);
    }

    /** Keluarkan akun dari semua sesi lain (butuh password saat ini). */
    public function keluarkanSesiLain(Request $request)
    {
        $request->validate([
            'password_sesi' => ['required', 'current_password'],
        ], [
            'password_sesi.required' => 'Masukkan password untuk mengeluarkan perangkat lain.',
            'password_sesi.current_password' => 'Password yang dimasukkan tidak sesuai.',
        ]);

        if (config('session.driver') !== 'database') {
            return back()->with('error', 'Fitur ini membutuhkan penyimpanan sesi di database.');
        }

        $dihapus = DB::table(config('session.table', 'sessions'))
            ->where('user_id', $request->user()->id)
            ->where('id', '!=', $request->session()->getId())
            ->delete();

        return back()->with(
            'success',
            $dihapus > 0
                ? "{$dihapus} sesi di perangkat lain telah dikeluarkan."
                : 'Tidak ada sesi lain yang aktif.'
        );
    }

    /** Ringkas user agent menjadi browser + sistem operasi. */
    private function bacaAgen(?string $agen): array
    {
        $agen = (string) $agen;

        $browser = match (true) {
            str_contains($agen, 'Edg/') => 'Edge',
            str_contains($agen, 'OPR/') || str_contains($agen, 'Opera') => 'Opera',
            str_contains($agen, 'Firefox/') => 'Firefox',
            str_contains($agen, 'Chrome/') => 'Chrome',
            str_contains($agen, 'Safari/') => 'Safari',
            default => 'Browser lain',
        };

        $os = match (true) {
            str_contains($agen, 'Windows') => 'Windows',
            str_contains($agen, 'iPhone') || str_contains($agen, 'iPad') => 'iOS',
            str_contains($agen, 'Android') => 'Android',
            str_contains($agen, 'Mac OS X') => 'macOS',
            str_contains($agen, 'Linux') => 'Linux',
            default => 'Perangkat tidak dikenal',
        };

        return [
            'browser' => $browser,
            'os' => $os,
            'seluler' => in_array($os, ['iOS', 'Android'], true),
        ];
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
        ]);

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return back()->with(
            'success',
            'Informasi akun berhasil diperbarui.'
        );
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => [
                'required',
                'confirmed',
                Password::min(8),
            ],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with(
            'success',
            'Password berhasil diperbarui.'
        );
    }

    public function updatePhoto(Request $request)
    {
        $validated = $request->validate([
            'avatar' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
        ]);

        $user = $request->user();

        $oldPhoto = $user->avatar_path;

        $path = $request->file('avatar')->store(
            'profile-photos',
            'public'
        );

        $user->update([
            'avatar_path' => $path,
        ]);

        if ($oldPhoto) {
            Storage::disk('public')->delete($oldPhoto);
        }

        return back()->with(
            'success',
            'Foto profil berhasil diperbarui.'
        );
    }

    public function updatePreferences(Request $request)
    {
        $validated = $request->validate([
            'theme_preference' => [
                'required',
                Rule::in(['light', 'dark']),
            ],
            'email_notifications' => [
                'sometimes',
                'boolean',
            ],
            'notif' => ['sometimes', 'array'],
        ]);

        // Checkbox yang tidak dicentang tidak terkirim, jadi dianggap mati.
        $dipilih = (array) $request->input('notif', []);

        $preferensi = collect(array_keys(NotifikasiService::JENIS))
            ->mapWithKeys(fn ($jenis) => [$jenis => isset($dipilih[$jenis])])
            ->all();

        $request->user()->update([
            'theme_preference' => $validated['theme_preference'],
            'email_notifications' => $request->boolean(
                'email_notifications'
            ),
            'notification_preferences' => $preferensi,
        ]);

        return back()->with(
            'success',
            'Preferensi berhasil disimpan.'
        );
    }

        // MENAMPILKAN HALAMAN MASTER PERUSAHAAN
    public function editPerusahaan()
    {
        $perusahaan = MasterPerusahaan::firstOrFail();

        return view('profile.perusahaan', compact('perusahaan'));
    }

    // MENYIMPAN PERUBAHAN MASTER PERUSAHAAN
    public function updatePerusahaan(Request $request)
    {
        $perusahaan = MasterPerusahaan::firstOrFail();

        $validated = $request->validate([
            'nama_perusahaan' => ['required', 'string', 'max:255'],
            'domisili' => ['nullable', 'string', 'max:255'],
            'alamat' => ['nullable', 'string'],
            'nama_penandatangan' => ['nullable', 'string', 'max:255'],
            'jabatan_penandatangan' => ['nullable', 'string', 'max:255'],
            'telepon' => ['nullable', 'string', 'max:30'],
            'fax' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
        ]);

        $perusahaan->update($validated);

        return redirect()
            ->route('profile.perusahaan.edit')
            ->with('success', 'Identitas perusahaan berhasil diperbarui.');
    }
}