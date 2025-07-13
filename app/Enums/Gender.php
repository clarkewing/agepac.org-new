<?php

namespace App\Enums;

enum Gender: string
{
    case MALE = 'M';
    case FEMALE = 'F';
    case OTHER = 'O';
    case UNDECLARED = 'U';

    public function label(): string
    {
        return __('genders.'.strtolower($this->name));
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn ($case) => [$case->value => $case->label()])
            ->all();
    }
}
