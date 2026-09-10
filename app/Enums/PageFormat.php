<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum PageFormat: string implements HasLabel
{
    case MARKDOWN = 'markdown';
    case HTML = 'html';

    public function getLabel(): string
    {
        return match ($this) {
            self::MARKDOWN => 'Markdown',
            self::HTML => 'HTML',
        };
    }
}
