<?php

namespace App\Filament\Resources\Users\Actions;

use App\Models\User;
use Filament\Actions\Action;

class ResendVerificationAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'resendVerification';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('admin.users.actions.resend-verification.label'))
            ->requiresConfirmation()
            ->modalHeading(__('admin.users.actions.resend-verification.modal.heading'))
            ->modalDescription(fn (User $record): string => __('admin.users.actions.resend-verification.modal.description', ['email' => $record->email]))
            ->visible(fn (User $record): bool => ! $record->hasVerifiedEmail())
            ->successNotificationTitle(__('admin.users.actions.resend-verification.success'))
            ->action(function (User $record): void {
                $record->sendEmailVerificationNotification();

                $this->success();
            });
    }
}
