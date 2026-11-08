<?php

namespace App\Console\Commands;

use App\Services\EtlLokasiAktivasi;
use Illuminate\Console\Command;
use Throwable;

class EtlLokasiAktivasiCommand extends Command
{
    protected $signature = 'etl:lokasi-aktivasi {--pemicu=artisan : artisan, jadwal, atau manual}';

    protected $description = 'ETL: ringkas data Lokasi (okupansi) dan Aktivasi ke tabel ringkasan untuk laporan.';

    public function handle(EtlLokasiAktivasi $etl): int
    {
        $this->info('Extract, Transform, Load dimulai...');

        try {
            $hasil = $etl->jalankan((string) $this->option('pemicu'));
        } catch (Throwable $e) {
            $this->error('ETL gagal: ' . $e->getMessage());

            return self::FAILURE;
        }

        $this->info('Selesai. Baris diekstrak: ' . $hasil['diekstrak']);
        $this->line('  Ringkasan okupansi : ' . $hasil['okupansi'] . ' baris');
        $this->line('  Ringkasan aktivasi : ' . $hasil['aktivasi'] . ' baris');

        return self::SUCCESS;
    }
}
