<?php

use App\Models\Aktivasi;
use App\Models\AktivasiRiwayat;
use App\Models\KerjaSama;
use App\Models\Lokasi;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function buatKs(string $mitra, ?string $statusAktivasi, ?int $hariPengajuan, ?int $hariKeputusan, ?array $tenant, ?string $terminal): KerjaSama
{
    $ks = KerjaSama::forceCreate([
        'mitra_usaha' => $mitra, 'brand' => "Brand $mitra", 'jenis_usaha' => 'F&B',
        'bentuk_kerja_sama' => 'Revenue Sharing', 'tanggal_pengajuan' => now()->subDays(30),
        'status' => 'Draft', 'pic_commercial' => 'PIC',
    ]);

    if ($statusAktivasi) {
        Aktivasi::forceCreate([
            'kerja_sama_id' => $ks->id, 'nomor_pengajuan' => 'AKT-' . $ks->id,
            'tanggal_pengajuan' => now()->subDays($hariPengajuan)->toDateString(),
            'tanggal_keputusan' => $hariKeputusan !== null ? now()->subDays($hariKeputusan)->toDateString() : null,
            'status' => $statusAktivasi,
        ]);
    }

    if ($tenant) {
        $t = Tenant::forceCreate([
            'kerja_sama_id' => $ks->id, 'nama_tenant' => $mitra, 'mitra_usaha' => $mitra, 'jenis_usaha' => 'F&B',
            'bentuk_kerja_sama' => 'Revenue Sharing', 'nama_pic' => 'PIC', 'no_hp_pic' => '0800',
            'gto' => $tenant[0], 'revenue_sharing' => $tenant[1],
        ]);

        if ($terminal) {
            Lokasi::create([
                'terminal' => $terminal, 'lantai' => '1', 'jenis_ruangan' => 'Store',
                'kode_ruang' => "$terminal-$mitra", 'lokasi' => "Ruang $mitra", 'luas_area' => 10,
                'status' => 'Terisi', 'tenant_id' => $t->id,
            ]);
        }
    }

    return $ks;
}

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->ks1 = buatKs('A', 'Disetujui', 10, 4, [100000000, 10], 'T1');          // aktif, umur 6, potensi 10jt
    $this->ks2 = buatKs('B', 'Menunggu Keputusan', 20, null, [50000000, 20], 'T1'); // umur 20, potensi 10jt
    $this->ks3 = buatKs('C', 'Menunggu Keputusan', 3, null, [40000000, 10], 'T2');  // umur 3, potensi 4jt
    $this->ks4 = buatKs('D', 'Ditolak', 12, 2, null, null);                         // umur 10, tanpa lokasi
    $this->ks5 = buatKs('E', null, null, null, null, null);                         // Menunggu BA
});

it('drilldown: status -> terminal -> kerja sama -> riwayat', function () {
    $r = $this->actingAs($this->user)->getJson(route('aktivasi.drilldown'))->assertOk()->json();
    expect($r['kunci'])->toBe('status')
        ->and(count($r['items']))->toBe(5)
        ->and($r['ringkasan']['total'])->toBe(5)
        ->and($r['ringkasan']['aktif'])->toBe(1);
    $per = collect($r['items'])->pluck('total', 'nilai');
    expect($per['Menunggu Keputusan'])->toBe(2)->and($per['Disetujui'])->toBe(1)
        ->and($per['Ditolak'])->toBe(1)->and($per['Menunggu BA'])->toBe(1)->and($per['Belum Diajukan'])->toBe(0);

    $r = $this->actingAs($this->user)->getJson(route('aktivasi.drilldown', ['status' => 'Menunggu Keputusan']))->json();
    expect($r['kunci'])->toBe('terminal')
        ->and(collect($r['items'])->pluck('total', 'nilai')->all())->toBe(['T1' => 1, 'T2' => 1])
        ->and($r['ringkasan']['rata_hari'])->toEqual(11.5);   // (20 + 3) / 2

    $r = $this->actingAs($this->user)->getJson(route('aktivasi.drilldown', ['status' => 'Ditolak']))->json();
    expect(collect($r['items'])->pluck('nilai')->all())->toBe(['Belum ada lokasi']);

    $r = $this->actingAs($this->user)->getJson(route('aktivasi.drilldown', ['status' => 'Menunggu Keputusan', 'terminal' => 'T1']))->json();
    expect($r['kunci'])->toBe('kerja_sama')->and(count($r['daftar']))->toBe(1)
        ->and($r['daftar'][0]['mitra'])->toBe('B')->and($r['daftar'][0]['umur_hari'])->toBe(20);

    AktivasiRiwayat::forceCreate(['kerja_sama_id' => $this->ks2->id, 'user_id' => $this->user->id, 'komponen' => 'Aktivasi', 'keterangan' => 'Pengajuan dikirim']);
    $r = $this->actingAs($this->user)->getJson(route('aktivasi.drilldown', ['status' => 'Menunggu Keputusan', 'terminal' => 'T1', 'kerja_sama' => $this->ks2->id]))->json();
    expect($r['kunci'])->toBe('riwayat')->and(count($r['riwayat']))->toBe(1)
        ->and($r['riwayat'][0]['oleh'])->toBe($this->user->name)->and($r['ks']['mitra'])->toBe('B');
});

it('drilldown: validasi, login, dan data tidak ada', function () {
    $this->getJson(route('aktivasi.drilldown'))->assertUnauthorized();
    $this->actingAs($this->user)->getJson(route('aktivasi.drilldown', ['status' => "x' OR '1'='1"]))->assertStatus(422);
    $this->actingAs($this->user)->getJson(route('aktivasi.drilldown', ['kerja_sama' => 99999]))->assertNotFound();
    $this->actingAs($this->user)->getJson(route('aktivasi.drilldown', ['status' => 'Disetujui', 'terminal' => "x' OR '1'='1"]))
        ->assertOk()->assertJsonPath('ringkasan.total', 0);
});

it('what-if: bawaan menghitung aktif, potensi, dan SLA', function () {
    $r = $this->actingAs($this->user)->getJson(route('aktivasi.whatif'))->assertOk()->json();

    expect($r['sekarang']['total'])->toBe(5)
        ->and($r['sekarang']['aktif'])->toBe(1)
        ->and($r['sekarang']['tingkat'])->toEqual(20)
        ->and($r['sekarang']['menunggu_keputusan'])->toBe(2)
        ->and($r['sekarang']['belum_diajukan'])->toBe(0)
        ->and($r['sekarang']['menunggu_ba'])->toBe(1)
        ->and($r['sekarang']['ditolak'])->toBe(1)
        ->and($r['sekarang']['potensi_bulan'])->toEqual(10000000)
        // historis: A = 6 hari, D = 10 hari -> 8
        ->and($r['sekarang']['rata_hari_keputusan'])->toEqual(8)
        // setuju 100% dari 2 menunggu, belum diajukan 0
        ->and($r['skenario']['aktif'])->toBe(3)
        ->and($r['skenario']['tingkat'])->toEqual(60)
        ->and($r['skenario']['selisih_tingkat'])->toEqual(40)
        ->and($r['skenario']['potensi_tambahan'])->toEqual(14000000)
        ->and($r['skenario']['potensi_bulan'])->toEqual(24000000)
        ->and($r['skenario']['potensi_tahun'])->toEqual(288000000)
        // SLA 14 hari: hanya B (20 hari) yang lewat
        ->and($r['sla']['melewati'])->toBe(1)->and($r['sla']['dari'])->toBe(2)
        ->and($r['sla']['terlama'][0]['mitra'])->toBe('B')->and($r['sla']['terlama'][0]['lewat'])->toBeTrue();
});

it('what-if: parameter mengubah hasil', function () {
    $r = $this->actingAs($this->user)->getJson(route('aktivasi.whatif', ['setuju_menunggu' => 50, 'ajukan_belum' => 0, 'sla' => 2]))->json();
    expect($r['skenario']['aktif'])->toBe(2)                       // 1 + round(2*0.5)
        ->and($r['skenario']['potensi_tambahan'])->toEqual(7000000)
        ->and($r['sla']['melewati'])->toBe(2);                     // 20 dan 3 hari > 2

    $r = $this->actingAs($this->user)->getJson(route('aktivasi.whatif', ['setuju_menunggu' => 0, 'ajukan_belum' => 0]))->json();
    expect($r['skenario']['aktif'])->toBe(1)->and($r['skenario']['selisih_tingkat'])->toEqual(0)
        ->and($r['skenario']['potensi_tambahan'])->toEqual(0);
});

it('what-if: validasi dan login', function () {
    $this->getJson(route('aktivasi.whatif'))->assertUnauthorized();
    foreach ([['setuju_menunggu' => 101], ['setuju_menunggu' => -1], ['ajukan_belum' => 'abc'], ['sla' => 0], ['sla' => 400]] as $q) {
        $this->actingAs($this->user)->getJson(route('aktivasi.whatif', $q))->assertStatus(422);
    }
});

it('what-if: tanpa data tidak error', function () {
    KerjaSama::query()->delete();
    $this->actingAs($this->user)->getJson(route('aktivasi.whatif'))->assertOk()
        ->assertJsonPath('sekarang.total', 0)->assertJsonPath('skenario.tingkat', 0)->assertJsonPath('sla.dari', 0);
    $this->actingAs($this->user)->getJson(route('aktivasi.drilldown'))->assertOk()->assertJsonPath('ringkasan.total', 0);
});

it('halaman aktivasi memuat bagian analisis', function () {
    $this->actingAs($this->user)->get(route('aktivasi'))->assertOk()
        ->assertSee('Analisis Aktivasi')->assertSee('aktivasiDrilldown')->assertSee('aktivasiWhatIf');
});
