<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Support\Collection;

class RingkasanNotifikasiMail extends Mailable
{
    public function __construct(
        public User $user,
        public Collection $items,
    ) {
    }

    public function envelope(): Envelope
    {
        $n = $this->items->count();

        return new Envelope(
            subject: "AeroTrack: {$n} hal perlu tindakan Anda",
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.ringkasan-notifikasi');
    }
}
