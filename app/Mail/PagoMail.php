<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;

class PagoMail extends Mailable
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
            subject: 'Nuevo Comprobante de Pago - ' . $this->data['banco'],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.pago',
        );
    }

    public function attachments(): array
    {
        $attachments = [];
        
        if (!empty($this->data['archivo_path'])) {
            $attachments[] = Attachment::fromPath($this->data['archivo_path'])
                ->as($this->data['archivo_nombre'])
                ->withMime($this->data['archivo_mime']);
        }
        
        return $attachments;
    }
}