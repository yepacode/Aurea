<?php

namespace App\Mail;

use App\Models\Referral;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Notifica a la clienta REFERRER cuando su amiga completa la primera compra
 * pagada y ambas reciben sus puntos del programa "Recomienda y gana".
 */
class ReferralConverted extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Referral $referral,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '¡Ganaste puntos! Tu amiga ya hizo su primera compra en Áurea 💛',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.referral-converted',
            with: [
                'referrer'      => $this->referral->referrer,
                'referred'      => $this->referral->referred,
                'pointsEarned'  => (int) $this->referral->reward_referrer_points,
            ],
        );
    }
}
