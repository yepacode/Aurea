<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class CustomerResetPasswordNotification extends ResetPassword
{
    public function toMail($notifiable): MailMessage
    {
        $url = route('customer.password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ]);

        return (new MailMessage)
            ->subject('Restablecer tu contraseña · Belleza Áurea')
            ->greeting('Hola '.($notifiable->name ?? '').',')
            ->line('Recibimos una solicitud para restablecer la contraseña de tu cuenta en Belleza Áurea.')
            ->action('Restablecer contraseña', $url)
            ->line('Este enlace expira en 60 minutos.')
            ->line('Si no solicitaste este cambio, ignora este correo.')
            ->salutation('Con cariño, Belleza Áurea 💛');
    }
}
