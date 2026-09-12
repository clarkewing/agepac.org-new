<?php

namespace App\Filament\Resources\NavItems;

use App\Filament\Resources\NavItems\Pages\ManageNavItems;
use App\Filament\Resources\NavItems\Schemas\NavItemForm;
use App\Filament\Resources\NavItems\Tables\NavItemsTable;
use App\Models\NavItem;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class NavItemResource extends Resource
{
    protected static ?string $model = NavItem::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBars3;

    public static function getModelLabel(): string
    {
        return __('admin.nav.label');
    }

    public static function getNavigationLabel(): string
    {
        return __('admin.nav.title');
    }

    public static function form(Schema $schema): Schema
    {
        return NavItemForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return NavItemsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageNavItems::route('/'),
        ];
    }
}
