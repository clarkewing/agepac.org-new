<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum Gender: string implements HasLabel
{
    case MALE = 'M';
    case FEMALE = 'F';
    case OTHER = 'O';
    case UNDECLARED = 'U';

    public function getLabel(): string
    {
        return __('genders.'.strtolower($this->name));
    }
}
