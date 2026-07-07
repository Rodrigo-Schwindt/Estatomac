<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NuevoRegistroMail extends Mailable
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
            subject: 'Nuevo Cliente Registrado - ' . $this->data['nombre'],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.nuevo-registro',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}