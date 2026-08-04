<?php

namespace App\Mail;

use App\Models\BankTransferSetting;
use App\Models\Order;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Envelope;

class OrderConfirmation extends Mailable
{
    public array $bankDetails = [];

    public function __construct(
        public Order $order,
    ) {
        if ($order->payment_method === 'transfer') {
            $this->bankDetails = [
                'bank_name' => BankTransferSetting::get('bank_name', ''),
                'account_holder' => BankTransferSetting::get('account_holder', ''),
                'clabe' => BankTransferSetting::get('clabe', ''),
                'account_number' => BankTransferSetting::get('account_number', ''),
                'reference_instructions' => BankTransferSetting::get('reference_instructions', ''),
                'additional_notes' => BankTransferSetting::get('additional_notes', ''),
            ];
        }
    }

    public function envelope(): Envelope
    {
        $subject = $this->order->payment_method === 'transfer'
            ? "Pedido #{$this->order->id} — Datos para transferencia — Belleza Áurea"
            : "Pedido #{$this->order->id} confirmado — Belleza Áurea";

        return new Envelope(
            subject: $subject,
            replyTo: [
                new Address(config('mail.contacto'), 'Belleza Áurea'),
            ],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.order-confirmation',
        );
    }
}
