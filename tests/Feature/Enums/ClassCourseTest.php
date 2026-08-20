<?php

use App\Enums\ClassCourse;

it('returns an array of options', function () {
    expect(ClassCourse::options())
        ->toBeArray()
        ->toBe([
            'Cycle Préparatoire ATPL',
            'EPL/S',
            'EPL/U',
            'EPL/P',
            'EPL',
            'EPT',
        ]);
});
