<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\Actions\ApproveAction;
use App\Filament\Resources\Users\Actions\AssignRoleAction;
use App\Filament\Resources\Users\UserResource;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ApproveAction::make(),
            AssignRoleAction::make(),
        ];
    }
}
