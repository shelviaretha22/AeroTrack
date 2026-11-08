<?php

namespace App\Providers;

use App\Models\AuditLog;
use App\Models\User;
use App\Services\NotifikasiService;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Throwable;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Kebijakan password untuk semua form (register, ganti password, reset):
        // minimal 10 karakter, huruf besar dan kecil, angka, dan simbol.
        Password::defaults(fn () => Password::min(10)->mixedCase()->numbers()->symbols());

        // Catat waktu dan IP login; yang lama disimpan sebagai "sebelumnya".
        Event::listen(Login::class, function (Login $event) {
            if (! $event->user instanceof User) {
                return;
            }

            try {
                $event->user->forceFill([
                    'previous_login_at' => $event->user->last_login_at,
                    'previous_login_ip' => $event->user->last_login_ip,
                    'last_login_at' => now(),
                    'last_login_ip' => request()->ip(),
                ])->saveQuietly();
            } catch (Throwable $e) {
                // Kolom belum dimigrasi: jangan menghalangi login.
                report($e);
            }
        });

        // ---- Audit log (accounting): kejadian autentikasi ----
        Event::listen(Login::class, function (Login $event) {
            $userId = $event->user->getAuthIdentifier();

            // Peringatan: login dari IP yang belum pernah dipakai akun ini.
            // Dicek sebelum catatan "Login berhasil" yang sekarang ditulis.
            try {
                $riwayat = AuditLog::where('user_id', $userId)->where('aksi', 'Login berhasil');
                $ip = (string) request()->ip();

                if ((clone $riwayat)->exists() && ! (clone $riwayat)->where('ip_address', $ip)->exists()) {
                    AuditLog::catat(
                        'Autentikasi',
                        'Login dari IP baru',
                        "Login dari IP {$ip} yang belum pernah dipakai akun ini. Jika bukan Anda, segera ganti password.",
                        null,
                        'peringatan',
                        $userId
                    );
                }
            } catch (Throwable $e) {
                report($e); // pemeriksaan ini tidak boleh menghalangi login
            }

            AuditLog::catat('Autentikasi', 'Login berhasil', null, null, 'info', $userId);
        });

        Event::listen(Failed::class, function (Failed $event) {
            // Email yang dicoba dicatat; password tidak pernah dicatat.
            $email = (string) ($event->credentials['email'] ?? '-');

            AuditLog::catat(
                'Autentikasi',
                'Login gagal',
                'Percobaan login dengan email ' . $email . '.',
                null,
                'peringatan',
                $event->user?->getAuthIdentifier()
            );
        });

        Event::listen(Lockout::class, function (Lockout $event) {
            AuditLog::catat(
                'Autentikasi',
                'Akun dikunci sementara',
                'Terlalu banyak percobaan login untuk email ' . (string) $event->request->input('email') . '.',
                null,
                'peringatan'
            );
        });

        Event::listen(Logout::class, function (Logout $event) {
            if ($event->user) {
                AuditLog::catat('Autentikasi', 'Logout', null, null, 'info', $event->user->getAuthIdentifier());
            }
        });

        Event::listen(PasswordReset::class, function (PasswordReset $event) {
            AuditLog::catat('Akun', 'Password direset lewat email', null, null, 'peringatan', $event->user->getAuthIdentifier());
        });

        // Data lonceng notifikasi untuk header layout.
        View::composer('layouts.app', function ($view) {
            $ringkas = null;

            if (Auth::check()) {
                try {
                    $ringkas = app(NotifikasiService::class)->ringkas(Auth::user());
                } catch (Throwable $e) {
                    // Tabel belum dimigrasi atau data bermasalah:
                    // header tetap tampil tanpa notifikasi.
                    report($e);
                }
            }

            $view->with('notifRingkas', $ringkas);
        });
    }
}
