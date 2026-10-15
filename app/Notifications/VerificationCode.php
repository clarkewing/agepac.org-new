<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Notifications\Messages\MailMessage;
use Spatie\OneTimePasswords\Notifications\OneTimePasswordNotification;

/**
 * The email carrying a one-time code, in the app's own words rather than the
 * one-time-passwords package's.
 */
class VerificationCode extends OneTimePasswordNotification
{
    /**
     * @param  User  $notifiable
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('notifications.verification-code.subject'))
            ->greeting(__('notifications.verification-code.greeting', ['name' => $notifiable->first_name]))
            ->line(__('notifications.verification-code.intro'))
            ->line("**{$this->oneTimePassword->password}**")
            ->line(__('notifications.verification-code.expiry', [
                'time' => $this->oneTimePassword->expires_at->timezone('Europe/Paris')->isoFormat('LT'),
            ]));
    }
}
