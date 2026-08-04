<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Envelope;

class OrderStatusUpdate extends Mailable
{
    public string $statusLabel;

    public function __construct(
        public Order $order,
    ) {
        $this->statusLabel = match ($order->status) {
            'pending' => 'Pendiente',
            'confirmed' => 'Confirmada',
            'shipped' => 'Enviada',
            'delivered' => 'Entregada',
            'cancelled' => 'Cancelada',
            default => $order->status,
        };
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Pedido #{$this->order->id} — {$this->statusLabel} — Belleza Áurea",
            replyTo: [
                new Address(config('mail.contacto'), 'Belleza Áurea'),
            ],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.order-status-update',
        );
    }
}
