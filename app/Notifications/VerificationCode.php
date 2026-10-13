<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VerificationCode extends Notification
{
    public function __construct(
        public string $code,
        public int $lifetimeMinutes,
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(User $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(User $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('notifications.verification-code.subject'))
            ->greeting(__('notifications.verification-code.greeting', ['name' => $notifiable->first_name]))
            ->line(__('notifications.verification-code.intro'))
            ->line("**{$this->code}**")
            ->line(__('notifications.verification-code.expiry', ['minutes' => $this->lifetimeMinutes]));
    }
}
