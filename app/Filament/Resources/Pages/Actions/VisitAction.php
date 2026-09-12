<?php

namespace App\Filament\Resources\Pages\Actions;

use App\Models\Page;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;

class VisitAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'visit';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('admin.pages.actions.visit'))
            ->icon(Heroicon::ArrowTopRightOnSquare)
            ->url(fn (Page $record): string => $record->url())
            ->openUrlInNewTab();
    }
}
