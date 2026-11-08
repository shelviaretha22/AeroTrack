<?php

use App\Models\User;

// Pendaftaran mandiri ditutup secara bawaan (config auth.registrasi_terbuka).
test('registration screen redirects to login when closed', function () {
    config(['auth.registrasi_terbuka' => false]);

    $this->get('/register')->assertRedirect(route('login'));
});

test('registration is rejected when closed', function () {
    config(['auth.registrasi_terbuka' => false]);

    $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'Rahasia!12345',
        'password_confirmation' => 'Rahasia!12345',
    ])->assertRedirect(route('login'));

    $this->assertGuest();
    expect(User::where('email', 'test@example.com')->exists())->toBeFalse();
});

test('registration screen can be rendered when open', function () {
    config(['auth.registrasi_terbuka' => true, 'auth.registrasi_kode' => '']);

    $this->get('/register')->assertStatus(200);
});

test('new users can register when open', function () {
    config(['auth.registrasi_terbuka' => true, 'auth.registrasi_kode' => '']);

    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'Rahasia!12345',
        'password_confirmation' => 'Rahasia!12345',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
});

test('invitation code is required when configured', function () {
    config(['auth.registrasi_terbuka' => true, 'auth.registrasi_kode' => 'KODE-RAHASIA']);

    $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'Rahasia!12345',
        'password_confirmation' => 'Rahasia!12345',
        'kode_undangan' => 'salah',
    ])->assertSessionHasErrors('kode_undangan');

    $this->assertGuest();
});

test('weak password is rejected at registration', function () {
    config(['auth.registrasi_terbuka' => true, 'auth.registrasi_kode' => '']);

    foreach (['password', 'Pendek1!', 'tanpaangkaDanSimbol', 'huruf-kecil-123!'] as $lemah) {
        $this->post('/register', [
            'name' => 'Test User',
            'email' => 'lemah@example.com',
            'password' => $lemah,
            'password_confirmation' => $lemah,
        ])->assertSessionHasErrors('password');
    }

    $this->assertGuest();
});
