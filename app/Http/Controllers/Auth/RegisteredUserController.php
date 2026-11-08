<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Pendaftaran ditutup secara bawaan (REGISTRASI_TERBUKA=false).
     * Jika dibuka, REGISTRASI_KODE (opsional) menjadi kode undangan.
     */
    private function ditutup(): ?RedirectResponse
    {
        if (config('auth.registrasi_terbuka')) {
            return null;
        }

        return redirect()->route('login')->withErrors([
            'email' => 'Pendaftaran akun ditutup. Hubungi administrator untuk dibuatkan akun.',
        ]);
    }

    /**
     * Display the registration view.
     */
    public function create(): View|RedirectResponse
    {
        return $this->ditutup() ?? view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        if ($tutup = $this->ditutup()) {
            return $tutup;
        }

        $kodeUndangan = (string) config('auth.registrasi_kode');

        if ($kodeUndangan !== '' && ! hash_equals($kodeUndangan, (string) $request->input('kode_undangan'))) {
            AuditLog::catat('Autentikasi', 'Pendaftaran ditolak: kode undangan salah', null, null, 'peringatan');

            throw ValidationException::withMessages([
                'kode_undangan' => 'Kode undangan tidak valid.',
            ]);
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        // Email belum diverifikasi, jadi diarahkan ke halaman verifikasi.
        return redirect(route('dashboard', absolute: false));
    }
}
