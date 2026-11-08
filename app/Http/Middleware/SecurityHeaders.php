<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Header keamanan untuk semua respons web.
 *
 * Catatan CSP: Alpine.js (build standar dari CDN) memakai evaluasi ekspresi,
 * sehingga 'unsafe-eval' dan 'unsafe-inline' masih dibutuhkan. Untuk
 * mengetatkannya, ganti ke build "@alpinejs/csp" dan pindahkan script inline.
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $header = $response->headers;

        // SAMEORIGIN (bukan DENY) agar pratinjau PDF di iframe satu situs tetap jalan.
        $header->set('X-Frame-Options', 'SAMEORIGIN');
        $header->set('X-Content-Type-Options', 'nosniff');
        $header->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $header->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=()');
        $header->set('Cross-Origin-Opener-Policy', 'same-origin');

        $nama = config('keamanan.csp_report_only')
            ? 'Content-Security-Policy-Report-Only'
            : 'Content-Security-Policy';

        $header->set($nama, $this->csp());

        if ($request->isSecure()) {
            $header->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $response;
    }

    private function csp(): string
    {
        $skrip = ["'self'", "'unsafe-inline'", "'unsafe-eval'", 'https://cdn.jsdelivr.net'];
        $gaya = ["'self'", "'unsafe-inline'", 'https://fonts.googleapis.com'];
        $koneksi = ["'self'"];

        // Saat pengembangan, Vite menyajikan aset dari server lokal.
        $hot = public_path('hot');

        if (is_file($hot)) {
            $alamat = rtrim(trim((string) file_get_contents($hot)), '/');

            if ($alamat !== '') {
                $skrip[] = $alamat;
                $gaya[] = $alamat;
                $koneksi[] = $alamat;
                $koneksi[] = preg_replace('/^http/', 'ws', $alamat);
            }
        }

        return implode('; ', [
            "default-src 'self'",
            'script-src ' . implode(' ', $skrip),
            'style-src ' . implode(' ', $gaya),
            "font-src 'self' https://fonts.gstatic.com data:",
            "img-src 'self' data: blob:",
            'connect-src ' . implode(' ', $koneksi),
            "frame-src 'self' blob:",
            "object-src 'self' blob:",
            "base-uri 'self'",
            "form-action 'self'",
            "frame-ancestors 'self'",
        ]);
    }
}
