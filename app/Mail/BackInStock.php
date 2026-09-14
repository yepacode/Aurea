<?php

namespace App\Mail;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BackInStock extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Product $product) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '¡'.$this->product->name.' ya está disponible! · Belleza Áurea',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.back-in-stock',
            with: [
                'product' => $this->product,
                'url'     => route('products.show', $this->product->slug),
            ],
        );
    }
}
