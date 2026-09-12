<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum NavItemType: string implements HasLabel
{
    case GROUP = 'group';
    case PAGE = 'page';
    case URL = 'url';

    public function getLabel(): string
    {
        return __("fields.nav-type.options.{$this->value}");
    }
}
