<?php

namespace App\Filament;

use App\Filament\MenuItemTypes\LinkItemType;
use App\Filament\MenuItemTypes\PageItemType;
use App\Filament\MenuItemTypes\TextItemType;
use App\Models\MenuItem;
use App\Models\Page;
use Datlechin\FilamentMenuBuilder\FilamentMenuBuilderPlugin;
use Datlechin\FilamentMenuBuilder\ItemTypes\ItemType;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

class MenuBuilder
{
    public static function make(): FilamentMenuBuilderPlugin
    {
        return FilamentMenuBuilderPlugin::make()
            ->navigationLabel(fn (): string => __('admin.nav.title'))
            ->navigationIcon('heroicon-o-bars-3')
            ->addLocations([
                'sidebar' => __('admin.nav.locations.sidebar'),
            ])
            ->itemTypes([
                TextItemType::make(),
                LinkItemType::make(),
                PageItemType::make(Page::class)
                    ->label(__('admin.nav.types.page'))
                    ->titleAttribute('title'),
            ])
            // Text items act as group headings: only they may hold children,
            // and they may never be nested themselves.
            ->allowNesting(fn (MenuItem $item, ?MenuItem $parent): bool => $parent === null || ($parent->isGroupHeading() && ! $item->isGroupHeading()))
            ->addMenuItemFields(fn (ItemType $type): array => [
                TextInput::make('data.badge')
                    ->label(__('fields.badge.label'))
                    ->visible(! $type instanceof TextItemType),
                Select::make('data.badge_color')
                    ->label(__('fields.badge-color.label'))
                    ->options(__('fields.badge-color.options'))
                    ->visible(! $type instanceof TextItemType)
                    ->visibleJs(<<<'JS'
                        !! $get('data.badge')
                        JS),
                Toggle::make('data.expandable')
                    ->label(__('fields.expandable.label'))
                    ->visible($type instanceof TextItemType),
                Toggle::make('data.expanded')
                    ->label(__('fields.expanded.label'))
                    ->default(true)
                    ->visible($type instanceof TextItemType)
                    ->visibleJs(<<<'JS'
                        !! $get('data.expandable')
                        JS),
            ]);
    }
}
