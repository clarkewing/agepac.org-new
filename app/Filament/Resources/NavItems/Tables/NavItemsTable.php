<?php

namespace App\Filament\Resources\NavItems\Tables;

use App\Models\NavItem;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class NavItemsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('label')
                    ->label(__('fields.label.label'))
                    ->state(fn (NavItem $record): string => ($record->parent_id ? '↳ ' : '').$record->label()),
                TextColumn::make('type')
                    ->label(__('fields.nav-type.label'))
                    ->badge(),
                TextColumn::make('page.path')
                    ->label(__('fields.path.label'))
                    ->prefix('/pages/'),
                IconColumn::make('disabled')
                    ->label(__('fields.disabled.label'))
                    ->icon(fn (bool $state): ?Heroicon => $state ? Heroicon::EyeSlash : null)
                    ->color('danger'),
            ])
            ->reorderable('order')
            ->defaultSort('order')
            ->paginated(false)
            ->recordActions([
                EditAction::make()->slideOver(),
                DeleteAction::make(),
            ]);
    }
}
