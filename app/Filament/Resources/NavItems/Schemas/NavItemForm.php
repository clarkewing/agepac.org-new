<?php

namespace App\Filament\Resources\NavItems\Schemas;

use App\Enums\NavItemType;
use App\Models\NavItem;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

use function Illuminate\Support\enum_value;

class NavItemForm
{
    public static function configure(Schema $schema): Schema
    {
        $type = fn (Get $get): ?NavItemType => NavItemType::tryFrom((string) enum_value($get('type')));

        return $schema
            ->components([
                Select::make('type')
                    ->label(__('fields.nav-type.label'))
                    ->options(NavItemType::class)
                    ->default(NavItemType::PAGE)
                    ->live()
                    ->required(),
                Select::make('page_id')
                    ->label(__('fields.page.label'))
                    ->relationship('page', 'title')
                    ->searchable()
                    ->preload()
                    ->visible(fn (Get $get): bool => $type($get) === NavItemType::PAGE)
                    ->required(fn (Get $get): bool => $type($get) === NavItemType::PAGE),
                TextInput::make('url')
                    ->label(__('fields.url.label'))
                    ->url()
                    ->visible(fn (Get $get): bool => $type($get) === NavItemType::URL)
                    ->required(fn (Get $get): bool => $type($get) === NavItemType::URL),
                TextInput::make('label.fr')
                    ->label(__('fields.label.label').' (FR)')
                    ->helperText(fn (Get $get): ?string => $type($get) === NavItemType::PAGE ? __('admin.nav.help.label') : null)
                    ->required(fn (Get $get): bool => $type($get) !== NavItemType::PAGE),
                TextInput::make('label.en')
                    ->label(__('fields.label.label').' (EN)'),
                Select::make('parent_id')
                    ->label(__('fields.parent.label'))
                    ->options(fn (): array => NavItem::query()
                        ->where('type', NavItemType::GROUP)
                        ->orderBy('order')
                        ->get()
                        ->mapWithKeys(fn (NavItem $group): array => [$group->id => $group->label()])
                        ->all())
                    ->visible(fn (Get $get): bool => $type($get) !== NavItemType::GROUP),
                TextInput::make('icon')
                    ->label(__('fields.icon.label'))
                    ->helperText(__('admin.nav.help.icon'))
                    ->visible(fn (Get $get): bool => $type($get) !== NavItemType::GROUP),
                TextInput::make('badge')
                    ->label(__('fields.badge.label'))
                    ->visible(fn (Get $get): bool => $type($get) !== NavItemType::GROUP),
                Select::make('badge_color')
                    ->label(__('fields.badge-color.label'))
                    ->options(__('fields.badge-color.options'))
                    ->visible(fn (Get $get): bool => filled($get('badge'))),
                Select::make('target')
                    ->label(__('fields.target.label'))
                    ->options(__('fields.target.options'))
                    ->placeholder(__('fields.target.placeholder'))
                    ->visible(fn (Get $get): bool => $type($get) !== NavItemType::GROUP),
                Toggle::make('collapsible')
                    ->label(__('fields.collapsible.label'))
                    ->visible(fn (Get $get): bool => $type($get) === NavItemType::GROUP),
                Toggle::make('disabled')
                    ->label(__('fields.disabled.label')),
            ]);
    }
}
