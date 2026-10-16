<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueueAfterCommit;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccountApproved extends Notification implements ShouldQueueAfterCommit
{
    use Queueable;

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
            ->subject(__('notifications.account-approved.subject'))
            ->greeting(__('notifications.account-approved.greeting', ['name' => $notifiable->first_name]))
            ->line(__('notifications.account-approved.intro'))
            ->line(__('notifications.account-approved.access'))
            ->action(__('notifications.account-approved.action'), route('dashboard'));
    }
}
