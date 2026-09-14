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
 * Correo al ADMIN cuando un cliente envía la solicitud de mayorista.
 * Se dispara desde Account\WholesaleController@submit.
 */
class WholesaleRequestReceived extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Customer $customer)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nueva solicitud de mayorista — '.$this->customer->wholesaler_company_name,
            replyTo: [
                new Address($this->customer->email, $this->customer->name ?: $this->customer->email),
            ],
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.wholesale.request-received');
    }
}
