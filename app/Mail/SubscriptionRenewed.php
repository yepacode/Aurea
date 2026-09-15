<?php

namespace App\Mail;

use App\Models\Order;
use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SubscriptionRenewed extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Subscription $subscription,
        public Order $order,
    ) {}

    public function envelope(): Envelope
    {
        $month = now()->translatedFormat('F');
        return new Envelope(
            subject: "📦 Tu ritual Áurea de {$month} está en camino",
            replyTo: [new Address(config('mail.contacto'), 'Belleza Áurea')],
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.subscriptions.renewed');
    }
}
