<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StudentWelcomeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $password;

    public function __construct($password)
    {
        $this->password = $password;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Добредојдовте! Вашата сметка е креирана')
            ->greeting('Здраво, ' . $notifiable->name . '!')
            ->line('Вашата сметка е успешно креирана во системот за едукација.')
            ->line('За да ја активирате сметката, кликнете на копчето подолу и поставете си сопствена лозинка.')
            ->action('Постави лозинка', url(config('app.url') . '/reset-password'))
            ->line('Доколку не сте го побарале ова, слободно игнорирајте ја пораката.');
    }
}
