<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

test('filter tahun di luar rentang ditolak', function () {
    $this->get('/laporan?tahun=1999')->assertSessionHasErrors('tahun');
    $this->get('/laporan?tahun=2101')->assertSessionHasErrors('tahun');
});

test('filter terminal selain T1 dan T2 ditolak', function () {
    $this->get('/laporan?terminal=T3')->assertSessionHasErrors('terminal');
});

test('drill-down dengan type tidak dikenal ditolak', function () {
    $this->get('/laporan/drilldown?type=rahasia')->assertSessionHasErrors('type');
});

test('drill-down type status wajib punya value yang valid', function () {
    $this->get('/laporan/drilldown?type=status')->assertSessionHasErrors('value');
    $this->get('/laporan/drilldown?type=status&value=Selesai')->assertSessionHasErrors('value');
});

test('halaman setelah login tidak disimpan di cache', function () {
    $cache = $this->get('/dashboard')->headers->get('Cache-Control');

    expect($cache)->toContain('no-store');
});
