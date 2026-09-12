<?php

namespace App\Filament\Resources\Pages\Schemas;

use App\Enums\PageFormat;
use App\Filament\Components\PageBodyEditor;
use App\Services\Content\GutenbergConverter;
use App\Services\Content\PageRenderer;
use Filament\Actions\Action;
use Filament\Forms\Components\CodeEditor;
use Filament\Forms\Components\CodeEditor\Enums\Language;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Callout;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\HtmlString;

use function Illuminate\Support\enum_value;

class PageForm
{
    public static function configure(Schema $schema): Schema
    {
        $format = fn (Get $get): ?PageFormat => PageFormat::tryFrom((string) enum_value($get('format')));

        return $schema
            ->columns(['lg' => 1, 'xl' => 5, '2xl' => 3])
            ->components([
                Section::make(__('admin.pages.sections.content'))
                    ->columnSpan(['xl' => 3, '2xl' => 2])
                    ->components([
                        TextInput::make('title')
                            ->label(__('fields.title.label'))
                            ->required()
                            ->maxLength(255),
                        TextInput::make('path')
                            ->label(__('fields.path.label'))
                            ->prefix('/pages/')
                            ->placeholder(__('fields.path.placeholder'))
                            ->required()
                            ->maxLength(255)
                            ->regex('/^[a-z0-9]+([a-z0-9\-\/][a-z0-9]+)*$/')
                            ->unique(ignoreRecord: true),
                        PageBodyEditor::make('body')
                            ->label(__('fields.body.label'))
                            ->visible(fn (Get $get): bool => $format($get) === PageFormat::MARKDOWN)
                            ->required(),
                        CodeEditor::make('html_body')
                            ->statePath('body')
                            ->key('html_body')
                            ->label(__('fields.body.label'))
                            ->language(Language::Html)
                            ->visible(fn (Get $get): bool => $format($get) === PageFormat::HTML)
                            ->required(),
                        Callout::make(__('admin.pages.warnings.public_attachments.title'))
                            ->description(__('admin.pages.warnings.public_attachments.description'))
                            ->danger()
                            ->footer(new HtmlString(<<<'HTML'
                                <ul class="list-inside list-disc space-y-1">
                                    <template x-for="url in [...new Set([...($get('body') ?? '').matchAll(/(?:https?:\/\/[^\s\/)\x22\x27<]+)?\/attachments\/[^\s)\x22\x27<]+/g)].map((match) => decodeURIComponent(match[0])))]">
                                        <li x-text="url"></li>
                                    </template>
                                </ul>
                                HTML))
                            ->visibleJs(<<<'JS'
                                ! Number($get('restricted')) && ($get('body') ?? '').includes('/attachments/')
                                JS),
                    ]),
                Section::make(__('admin.pages.sections.settings'))
                    ->columnSpan(['xl' => 2, '2xl' => 1])
                    ->components([
                        Select::make('format')
                            ->label(__('fields.format.label'))
                            ->options(PageFormat::class)
                            ->default(PageFormat::MARKDOWN)
                            ->live()
                            ->afterStateUpdated(function (mixed $state, Get $get, Set $set): void {
                                if (blank($body = $get('body'))) {
                                    return;
                                }

                                $set('body', match (PageFormat::tryFrom((string) enum_value($state))) {
                                    PageFormat::MARKDOWN => resolve(GutenbergConverter::class)->toMarkdown($body),
                                    PageFormat::HTML => resolve(PageRenderer::class)->renderBody(PageFormat::MARKDOWN, $body)->toHtml(),
                                    default => $body,
                                });
                            })
                            ->required(),
                        Callout::make(__('admin.pages.warnings.converted'))
                            ->warning()
                            ->visibleJs(<<<'JS'
                                !! $get('body')
                                JS),
                        ToggleButtons::make('restricted')
                            ->label(__('fields.visibility.label'))
                            ->boolean(__('fields.visibility.options.restricted'), __('fields.visibility.options.public'))
                            ->colors([1 => 'gray', 0 => 'danger'])
                            ->icons([1 => Heroicon::LockClosed, 0 => Heroicon::LockOpen])
                            ->grouped()
                            ->default(true)
                            ->helperText(__('admin.pages.help.restricted')),
                        DateTimePicker::make('published_at')
                            ->label(__('fields.published-at.label'))
                            ->default(now())
                            ->suffixActions([
                                Action::make('now')
                                    ->label(__('admin.pages.actions.now'))
                                    ->icon(Heroicon::Clock)
                                    ->action(fn (Set $set) => $set('published_at', now()->format('Y-m-d H:i:s'))),
                                Action::make('clear')
                                    ->label(__('admin.pages.actions.clear'))
                                    ->icon(Heroicon::XMark)
                                    ->action(fn (Set $set) => $set('published_at', null)),
                            ])
                            ->helperText(__('admin.pages.help.published_at')),
                    ]),
            ]);
    }
}
