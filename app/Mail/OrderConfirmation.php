<?php

namespace App\Mail;

use App\Models\BankTransferSetting;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderConfirmation extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public array $bankDetails = [];

    public function __construct(
        public Order $order,
    ) {
        if ($order->payment_method === 'transfer') {
            $this->bankDetails = [
                'bank_name'       => BankTransferSetting::get('bank_name', ''),
                'account_holder'  => BankTransferSetting::get('account_holder', ''),
                'nit'             => BankTransferSetting::get('nit', ''),
                'account_type'    => BankTransferSetting::get('account_type', ''),
                'account_number'  => BankTransferSetting::get('account_number', ''),
                'document_type'   => BankTransferSetting::get('document_type', ''),
                'document_number' => BankTransferSetting::get('document_number', ''),
                'reference_instructions' => BankTransferSetting::get('reference_instructions', ''),
                'additional_notes'       => BankTransferSetting::get('additional_notes', ''),
                // Medios digitales colombianos — el email pinta cada bloque
                // solo si _enabled == true y el número está cargado.
                'nequi_enabled'              => (bool) BankTransferSetting::get('nequi_enabled', ''),
                'nequi_number'               => BankTransferSetting::get('nequi_number', ''),
                'nequi_instructions'         => BankTransferSetting::get('nequi_instructions', ''),
                'daviplata_enabled'          => (bool) BankTransferSetting::get('daviplata_enabled', ''),
                'daviplata_number'           => BankTransferSetting::get('daviplata_number', ''),
                'daviplata_instructions'     => BankTransferSetting::get('daviplata_instructions', ''),
                'bancolombia_amano_enabled'  => (bool) BankTransferSetting::get('bancolombia_amano_enabled', ''),
                'bancolombia_amano_number'   => BankTransferSetting::get('bancolombia_amano_number', ''),
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
