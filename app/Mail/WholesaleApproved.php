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
 * Correo al CLIENTE cuando el admin aprueba su solicitud de mayorista.
 * Se dispara desde Admin\WholesaleAdminController@approve.
 */
class WholesaleApproved extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Customer $customer)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '¡Bienvenida al programa mayorista de Belleza Áurea! 💛',
            replyTo: [
                new Address(config('mail.contacto'), 'Belleza Áurea'),
            ],
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.wholesale.approved');
    }
}
