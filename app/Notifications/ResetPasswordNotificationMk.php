<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotificationMk extends ResetPassword
{
    public function toMail($notifiable): MailMessage
    {
        $resetUrl = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        $expireMinutes = config('auth.passwords.' . config('auth.defaults.passwords') . '.expire');

        return (new MailMessage)
            ->subject('Известување за ресетирање лозинка')
            ->greeting('Здраво!')
            ->line('Ја добивате оваа е-порака затоа што беше побарано ресетирање на лозинката за вашата сметка.')
            ->action('Ресетирај лозинка', $resetUrl)
            ->line('Овој линк за ресетирање на лозинката ќе истече за ' . $expireMinutes . ' минути.')
            ->line('Доколку не сте побарале ресетирање на лозинката, не е потребна понатамошна акција.');
    }
}
