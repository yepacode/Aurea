<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReviewRequest extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '¿Cómo te fue con tu compra? Cuéntanos 🌸 · Belleza Áurea',
        );
    }

    public function content(): Content
    {
        // Producto principal del pedido para enlazar directo a dejar reseña.
        $firstItem = $this->order->items->first();
        $product = $firstItem?->product;

        return new Content(
            markdown: 'emails.review-request',
            with: [
                'order'   => $this->order,
                'product' => $product,
                'url'     => $product
                    ? route('products.show', $product->slug).'#resenas'
                    : route('products.index'),
            ],
        );
    }
}
