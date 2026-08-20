<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Enums\ClassCourse;
use App\Enums\Gender;
use App\Filament\Resources\Users\Actions\ApproveAction;
use App\Filament\Resources\Users\Actions\ResendVerificationAction;
use App\Filament\Resources\Users\Actions\SendPasswordResetAction;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(['md' => 5, 'lg' => 2, 'xl' => 3])
            ->components([
                Section::make(__('admin.users.sections.identity'))
                    ->columns(2)
                    ->columnSpan(['md' => 3, 'lg' => 2])
                    ->components([
                        TextInput::make('first_name')
                            ->label(__('fields.first-name.label'))
                            ->required(),
                        TextInput::make('last_name')
                            ->label(__('fields.last-name.label'))
                            ->required(),
                        Select::make('gender')
                            ->columnStart(1)
                            ->label(__('fields.gender.label'))
                            ->placeholder(__('fields.gender.placeholder'))
                            ->options(Gender::class)
                            ->required(),
                        DatePicker::make('birth_date')
                            ->columnStart(1)
                            ->label(__('fields.birth-date.label')),
                        Fieldset::make(__('admin.users.sections.class'))
                            ->columnSpanFull()
                            ->components([
                                Select::make('class_course')
                                    ->label(__('fields.class-course.label'))
                                    ->placeholder(__('fields.class-course.placeholder'))
                                    ->options(function (?User $record): array {
                                        return collect(ClassCourse::cases())
                                            // EPL is being phased out — it only signals uncertainty
                                            // about the actual course, so it stays selectable solely
                                            // for users who already carry it.
                                            ->reject(fn (ClassCourse $course): bool => $course === ClassCourse::EPL
                                                && $record?->class_course !== ClassCourse::EPL)
                                            ->mapWithKeys(fn (ClassCourse $course): array => [$course->value => $course->getLabel()])
                                            ->all();
                                    }),
                                TextInput::make('class_year')
                                    ->label(__('fields.class-year.label'))
                                    ->numeric()
                                    ->minValue(1949) // ENAC's founding year.
                                    ->maxValue(now()->year + 1),
                            ]),
                    ]),
                Group::make()
                    ->columns(['md' => 2, 'xl' => 1])
                    ->columnSpan(['md' => 2, 'xl' => 1])
                    ->components([
                        Section::make(__('admin.users.sections.account'))
                            ->columnSpan(['md' => 'full', 'lg' => 1])
                            ->components([
                                TextInput::make('username')
                                    ->label(__('fields.username.label'))
                                    ->required()
                                    ->unique(ignoreRecord: true),
                                TextInput::make('email')
                                    ->label(__('fields.email.label'))
                                    ->email()
                                    ->required()
                                    ->unique(ignoreRecord: true),
                                TextInput::make('phone')
                                    ->label(__('fields.phone.label'))
                                    ->tel(),
                                Actions::make([
                                    SendPasswordResetAction::make(),
                                ])
                                    ->label(__('fields.password.label'))
                                    ->fullWidth(),
                            ]),
                        Section::make(__('admin.users.sections.history'))
                            ->columnSpan(['md' => 'full', 'lg' => 1])
                            ->components([
                                TextEntry::make('created_at')
                                    ->label(__('admin.users.timestamps.created_at'))
                                    ->dateTime(),
                                TextEntry::make('updated_at')
                                    ->label(__('admin.users.timestamps.updated_at'))
                                    ->dateTime(),
                                TextEntry::make('email_verified_at')
                                    ->label(__('admin.users.timestamps.email_verified_at'))
                                    ->dateTime()
                                    ->placeholder('—')
                                    ->hintAction(ResendVerificationAction::make()),
                                TextEntry::make('approved_at')
                                    ->label(__('admin.users.timestamps.approved_at'))
                                    ->dateTime()
                                    ->placeholder('—')
                                    ->hintAction(ApproveAction::make()),
                            ]),
                    ]),
            ]);
    }
}
