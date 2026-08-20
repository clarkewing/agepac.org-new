<?php

use App\Enums\Gender;

it('provides a label', function (Gender $case, string $label) {
    app()->setLocale('en');

    expect($case->getLabel())->toBe($label);
})->with([
    [Gender::MALE, 'Male'],
    [Gender::FEMALE, 'Female'],
    [Gender::OTHER, 'Other'],
    [Gender::UNDECLARED, 'Prefer not to say'],
]);
