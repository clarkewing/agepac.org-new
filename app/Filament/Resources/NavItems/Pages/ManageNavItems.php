<?php

namespace App\Filament\Resources\NavItems\Pages;

use App\Filament\Resources\NavItems\NavItemResource;
use App\Models\NavItem;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageNavItems extends ManageRecords
{
    protected static string $resource = NavItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->slideOver()
                ->mutateDataUsing(fn (array $data): array => $data + [
                    'order' => (NavItem::max('order') ?? 0) + 1,
                ]),
        ];
    }
}
