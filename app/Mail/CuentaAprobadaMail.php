<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CuentaAprobadaMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '¡Tu cuenta ha sido aprobada! - Todotex',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.cuenta-aprobada',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}