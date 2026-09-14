<?php

namespace App\Mail;

use App\Models\Customer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Correo al CLIENTE cuando el admin rechaza su solicitud de mayorista.
 * El motivo va en $customer->wholesaler_notes.
 */
class WholesaleRejected extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Customer $customer)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Sobre tu solicitud de mayorista — Belleza Áurea',
            replyTo: [
                new Address(config('mail.contacto'), 'Belleza Áurea'),
            ],
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.wholesale.rejected');
    }
}
