<?php

use App\Enums\ClassCourse;

it('returns an array of options', function () {
    expect(ClassCourse::options())
        ->toBeArray()
        ->toBe([
            'Cursus Prépa ATPL',
            'EPL/S',
            'EPL/U',
            'EPL/P',
            'EPL',
            'EPT',
        ]);
});
