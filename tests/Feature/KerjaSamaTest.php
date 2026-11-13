<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('tamu dialihkan ke login', function () {
    $this->get('/kontrak')->assertRedirect('/login');
});

test('mitra usaha wajib diisi', function () {
    $this->actingAs(User::factory()->create())
        ->post('/kerja-sama', ['mitra_usaha' => ''])
        ->assertSessionHasErrors('mitra_usaha');
});

test('tanggal berakhir tidak boleh sebelum mulai', function () {
    $this->actingAs(User::factory()->create())
        ->post('/kerja-sama', [
            'mitra_usaha' => 'PT Uji',
            'tanggal_mulai' => '2026-12-10',
            'tanggal_berakhir' => '2026-12-01',
        ])
        ->assertSessionHasErrors('tanggal_berakhir');
});

test('jenis usaha harus dari daftar', function () {
    $this->actingAs(User::factory()->create())
        ->post('/kerja-sama', ['mitra_usaha' => 'PT Uji', 'jenis_usaha' => 'Otomotif'])
        ->assertSessionHasErrors('jenis_usaha');
});
