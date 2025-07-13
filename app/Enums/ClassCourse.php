<?php

namespace App\Enums;

enum ClassCourse: string
{
    case PREPA_ATPL = 'Cursus Prépa ATPL';
    case EPL_S = 'EPL/S';
    case EPL_U = 'EPL/U';
    case EPL_P = 'EPL/P';
    case EPL = 'EPL';
    case EPT = 'EPT';

    public static function options(): array
    {
        return array_column(self::cases(), 'value');
    }
}
