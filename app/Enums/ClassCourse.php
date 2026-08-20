<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ClassCourse: string implements HasLabel
{
    case PREPA_ATPL = 'Cursus Prépa ATPL';
    case EPL_S = 'EPL/S';
    case EPL_U = 'EPL/U';
    case EPL_P = 'EPL/P';
    case EPL = 'EPL';
    case EPT = 'EPT';

    public function getLabel(): string
    {
        return match ($this) {
            self::PREPA_ATPL => 'Cycle Préparatoire ATPL',
            default => $this->value,
        };
    }

    public function getShortLabel(): string
    {
        return match ($this) {
            self::PREPA_ATPL => 'Prépa ATPL',
            default => $this->getLabel(),
        };
    }
}
