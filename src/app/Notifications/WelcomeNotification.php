<?php

// app/Notifications/WelcomeNotification.php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class WelcomeNotification extends Notification
{
    use Queueable;

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('ようこそ Market App へ！')
            ->line('ご登録ありがとうございます。')
            ->line('さっそく商品を見てみましょう！')
            ->action('商品一覧へ', url('/'))
            ->line('今後ともよろしくお願いいたします。');
    }
}
