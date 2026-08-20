<?php

namespace App\Filament\Resources\Users\Tables;

use App\Enums\ClassCourse;
use App\Models\User;
use App\Services\Authorization\Role;
use Filament\Actions\EditAction;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('fields.name.label'))
                    ->icon(fn (User $record): ?Heroicon => $record->role !== null ? Heroicon::ShieldCheck : null)
                    ->iconPosition(IconPosition::After)
                    ->tooltip(fn (User $record): ?string => $record->getRole()?->name)
                    ->searchable(['first_name', 'last_name'])
                    ->sortable(['last_name', 'first_name']),
                TextColumn::make('username')
                    ->label(__('fields.username.label'))
                    ->searchable(),
                TextColumn::make('email')
                    ->label(__('fields.email.label'))
                    ->searchable(),
                TextColumn::make('class')
                    ->label(__('admin.users.columns.class'))
                    ->state(fn (User $record): ?string => $record->shortClass()),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('class_course')
                    ->label(__('fields.class-course.label'))
                    ->options(ClassCourse::class),
                SelectFilter::make('class_year')
                    ->label(__('fields.class-year.label'))
                    ->searchable()
                    ->options(fn (): array => User::query()
                        ->whereNotNull('class_year')
                        ->distinct()
                        ->orderByDesc('class_year')
                        ->pluck('class_year', 'class_year')
                        ->all()
                    ),
                SelectFilter::make('role')
                    ->label(__('fields.role.label'))
                    ->options(fn (): array => collect(Role::all())
                        ->map(fn (Role $role): string => $role->name)
                        ->all()
                    ),
                TernaryFilter::make('approved')
                    ->label(__('admin.users.filters.approved'))
                    ->nullable()
                    ->attribute('approved_at')
                    ->queries(
                        true: fn (Builder $query) => $query->approved(),
                        false: fn (Builder $query) => $query->unapproved(),
                        blank: fn (Builder $query) => $query,
                    ),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
