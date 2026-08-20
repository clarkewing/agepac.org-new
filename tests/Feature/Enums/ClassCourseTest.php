<?php

use App\Enums\ClassCourse;

it('provides a label', function (ClassCourse $case, string $label) {
    expect($case->getLabel())->toBe($label);
})->with([
    [ClassCourse::PREPA_ATPL, 'Cycle Préparatoire ATPL'],
    [ClassCourse::EPL_S, 'EPL/S'],
    [ClassCourse::EPL_U, 'EPL/U'],
    [ClassCourse::EPL_P, 'EPL/P'],
    [ClassCourse::EPL, 'EPL'],
    [ClassCourse::EPT, 'EPT'],
]);

it('provides a short label', function (ClassCourse $case, string $label) {
    expect($case->getShortLabel())->toBe($label);
})->with([
    [ClassCourse::PREPA_ATPL, 'Prépa ATPL'],
    [ClassCourse::EPL_S, 'EPL/S'],
    [ClassCourse::EPL_U, 'EPL/U'],
    [ClassCourse::EPL_P, 'EPL/P'],
    [ClassCourse::EPL, 'EPL'],
    [ClassCourse::EPT, 'EPT'],
]);
