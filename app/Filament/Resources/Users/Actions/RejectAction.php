<?php

namespace App\Filament\Resources\Users\Actions;

use Filament\Actions\DeleteAction;

/**
 * Turns down an account pending approval by deleting it.
 */
class RejectAction extends DeleteAction
{
    public static function getDefaultName(): ?string
    {
        return 'reject';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('admin.users.actions.reject.label'))
            ->modalHeading(__('admin.users.actions.reject.modal.heading'))
            ->modalDescription(__('admin.users.actions.reject.modal.description'))
            ->modalSubmitActionLabel(__('admin.users.actions.reject.label'))
            ->successNotificationTitle(__('admin.users.actions.reject.success'));
    }
}
