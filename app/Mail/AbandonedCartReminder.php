<?php

namespace App\Mail;

use App\Models\AbandonedCart;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AbandonedCartReminder extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public AbandonedCart $cart) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Dejaste algo en tu carrito 🛍️ · Belleza Áurea',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.abandoned-cart',
            with: [
                'cart'  => $this->cart,
                'items' => $this->cart->items ?? [],
                'url'   => route('cart.index'),
            ],
        );
    }
}
