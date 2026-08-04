<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class OrderAdminNotification extends Mailable
{
    public function __construct(
        public Order $order,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Nuevo pedido #{$this->order->id} — Belleza Áurea",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.order-admin-notification',
        );
    }
}
