<?php

namespace App\Filament\Resources\Users\Actions;

use App\Models\User;
use App\Services\Authorization\Role;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;

class AssignRoleAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'assignRole';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('admin.users.actions.assign-role.label'))
            ->icon(Heroicon::OutlinedShieldCheck)
            ->modalWidth(Width::Small)
            ->schema([
                Select::make('role')
                    ->label(__('fields.role.label'))
                    ->placeholder(__('admin.users.actions.assign-role.placeholder'))
                    ->options(fn (): array => collect(Role::all())
                        ->map(fn (Role $role): string => $role->name)
                        ->all()
                    ),
            ])
            ->fillForm(fn (User $record): array => [
                'role' => $record->role,
            ])
            ->disabled(fn (User $record): bool => ! $record->isApproved() || $record->is(auth()->user()))
            ->tooltip(fn (User $record): ?string => $record->isApproved()
                ? null
                : __('admin.users.actions.assign-role.unapproved'))
            ->successNotificationTitle(__('admin.users.actions.assign-role.success'))
            ->action(function (array $data, User $record): void {
                $record->assignRole($data['role'] ?: null);

                $this->success();
            });
    }
}
