<?php

namespace App\Mail;

use App\Models\NpsResponse;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NpsRequest extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public NpsResponse $npsResponse) {}

    public function envelope(): Envelope
    {
        $order = $this->npsResponse->order;
        $name  = $order?->customer?->name
            ? preg_split('/\s+/', trim($order->customer->name))[0]
            : 'clienta';

        return new Envelope(
            subject: "¿Cómo te fue con tu compra, {$name}? · Belleza Áurea",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.nps-request',
            with: [
                'nps'      => $this->npsResponse,
                'order'    => $this->npsResponse->order,
                'customer' => $this->npsResponse->order?->customer,
                'token'    => $this->npsResponse->token,
            ],
        );
    }
}
