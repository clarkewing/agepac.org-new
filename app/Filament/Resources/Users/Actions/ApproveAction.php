<?php

namespace App\Filament\Resources\Users\Actions;

use App\Actions\ApproveUser;
use App\Models\User;
use Filament\Actions\Action;

class ApproveAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'approve';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('admin.users.actions.approve.label'))
            ->requiresConfirmation()
            ->modalHeading(__('admin.users.actions.approve.modal.heading'))
            ->modalDescription(__('admin.users.actions.approve.modal.description'))
            ->authorize('approve')
            ->successNotificationTitle(__('admin.users.actions.approve.success'))
            ->action(function (User $record): void {
                resolve(ApproveUser::class)($record);

                $this->success();
            });
    }
}
