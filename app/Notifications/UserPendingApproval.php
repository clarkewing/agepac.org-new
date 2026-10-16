<?php

namespace App\Notifications;

use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueueAfterCommit;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserPendingApproval extends Notification implements ShouldQueueAfterCommit
{
    use Queueable;

    public function __construct(public User $user) {}

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
            ->subject(__('notifications.user-pending-approval.subject'))
            ->greeting(__('notifications.user-pending-approval.greeting', ['name' => $notifiable->first_name]))
            ->line(__('notifications.user-pending-approval.intro', [
                'name' => $this->user->name,
                'class' => $this->user->class,
            ]))
            ->line(__('notifications.user-pending-approval.cta'))
            ->action(
                __('notifications.user-pending-approval.action'),
                UserResource::getUrl('edit', ['record' => $this->user]),
            );
    }
}
