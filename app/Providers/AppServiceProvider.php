<?php

namespace App\Providers;

use App\Models\User;
use App\Services\NotifikasiService;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
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
