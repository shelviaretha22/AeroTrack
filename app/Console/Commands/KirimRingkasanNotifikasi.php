<?php

namespace App\Console\Commands;

use App\Mail\RingkasanNotifikasiMail;
use App\Models\User;
use App\Services\NotifikasiService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class KirimRingkasanNotifikasi extends Command
{
    protected $signature = 'notifikasi:kirim-ringkasan {--user= : Kirim hanya ke ID pengguna ini}';

    protected $description = 'Kirim email ringkasan notifikasi "perlu tindakan" ke pengguna yang mengaktifkannya.';

    public function handle(NotifikasiService $notifikasi): int
    {
        $pengguna = User::where('email_notifications', true)
            ->when($this->option('user'), fn ($q, $id) => $q->whereKey($id))
            ->get();

        $terkirim = 0;

        foreach ($pengguna as $user) {
            $items = $notifikasi->untuk($user)
                ->where('kategori', 'tindakan')
                ->where('dibaca', false)
                ->values();

            if ($items->isEmpty()) {
                continue;
            }

            Mail::to($user->email)->send(new RingkasanNotifikasiMail($user, $items));
            $terkirim++;
        }

        $this->info("Ringkasan terkirim ke {$terkirim} dari {$pengguna->count()} pengguna.");

        return self::SUCCESS;
    }
}
