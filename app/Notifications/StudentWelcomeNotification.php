<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StudentWelcomeNotification extends Notification
{
    use Queueable;

    protected string $resetUrl;

    public function __construct(string $resetUrl)
    {
        $this->resetUrl = $resetUrl;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Welcome! Your account has been created')
            ->greeting('Hello, ' . $notifiable->name . '!')
            ->line('Your account has been successfully created in the education system.')
            ->line('To activate your account, click the button below and set your own password.')
            ->action('Set Password', $this->resetUrl)
            ->line('If you did not expect this email, you can safely ignore it.');
    }
}
