<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendLoginOtpNotification extends Notification
{
    use Queueable;

    public function __construct(public string $code)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Votre code de vérification AgriKoom : ' . $this->code)
            ->greeting('Bonjour,')
            ->line('Voici votre code de vérification pour vous inscrire ou vous connecter sur la plateforme AgriKoom Sénégal :')
            ->line('**' . $this->code . '**')
            ->line('Ce code est valable pendant 15 minutes. Ne le partagez avec personne.')
            ->line('Si vous n\'êtes pas à l\'origine de cette demande, vous pouvez ignorer cet e-mail.')
            ->salutation('L\'équipe AgriKoom Sénégal');
    }
}

