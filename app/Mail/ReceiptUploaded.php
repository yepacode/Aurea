<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReceiptUploaded extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '📎 Comprobante subido — Pedido #'.$this->order->id.' · Belleza Áurea',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.receipt-uploaded',
            with: [
                'order' => $this->order,
                'receiptUrl' => $this->order->payment_receipt ? asset('storage/'.$this->order->payment_receipt) : null,
                'adminOrderUrl' => route('admin.orders.show', $this->order),
            ],
        );
    }
}
