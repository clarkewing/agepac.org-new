<?php

namespace App\Filament\Resources\Pages\Tables;

use App\Enums\PageFormat;
use App\Models\Page;
use Filament\Actions\EditAction;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class PagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label(__('fields.title.label'))
                    ->icon(fn (Page $record): Heroicon => $record->restricted ? Heroicon::LockClosed : Heroicon::LockOpen)
                    ->iconColor(fn (Page $record): ?string => $record->restricted ? null : 'danger')
                    ->iconPosition(IconPosition::After)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('path')
                    ->label(__('fields.path.label'))
                    ->prefix('/pages/')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('format')
                    ->label(__('fields.format.label'))
                    ->badge(),
                TextColumn::make('published_at')
                    ->label(__('fields.published-at.label'))
                    ->state(fn (Page $record): string => $record->published_at?->calendar()
                        ?? __('admin.pages.columns.unpublished')
                    )
                    ->badge()
                    ->color(fn (Page $record): string => match (true) {
                        $record->isPublished() => 'success',
                        $record->isScheduled() => 'warning',
                        default => 'gray',
                    })
                    ->icon(fn (Page $record): ?Heroicon => match (true) {
                        $record->isPublished() => Heroicon::Check,
                        $record->isScheduled() => Heroicon::Clock,
                        default => null,
                    })
                    ->tooltip(fn (Page $record): ?string => $record->isScheduled() ? __('admin.pages.columns.scheduled') : null)
                    ->sortable(),
            ])
            ->defaultSort('path')
            ->filters([
                SelectFilter::make('format')
                    ->label(__('fields.format.label'))
                    ->options(PageFormat::class),
                TernaryFilter::make('restricted')
                    ->label(__('fields.restricted.label')),
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
