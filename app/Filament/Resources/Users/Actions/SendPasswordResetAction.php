<?php

namespace App\Filament\Resources\Users\Actions;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Password;

class SendPasswordResetAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'sendPasswordReset';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('admin.users.actions.send-password-reset.label'))
            ->icon(Heroicon::OutlinedKey)
            ->color('gray')
            ->requiresConfirmation()
            ->modalHeading(__('admin.users.actions.send-password-reset.modal.heading'))
            ->modalDescription(fn (User $record): string => __('admin.users.actions.send-password-reset.modal.description', ['email' => $record->email]))
            ->successNotificationTitle(__('admin.users.actions.send-password-reset.success'))
            ->action(function (User $record): void {
                // Mint the token directly instead of going through the broker's
                // sendResetLink(), whose throttle must not block an admin resend.
                $record->sendPasswordResetNotification(Password::createToken($record));

                $this->success();
            });
    }
}
