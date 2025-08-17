<?php

use App\Enums\Gender;

it('provides a label', function (Gender $case, string $label) {
    app()->setLocale('en');

    expect($case->label())->toBe($label);
})->with([
    [Gender::MALE, 'Male'],
    [Gender::FEMALE, 'Female'],
    [Gender::OTHER, 'Other'],
    [Gender::UNDECLARED, 'Prefer not to say'],
]);

it('returns an array of options', function () {
    expect(Gender::options())
        ->toBeArray()
        ->toBe([
            'M' => __('genders.male'),
            'F' => __('genders.female'),
            'O' => __('genders.other'),
            'U' => __('genders.undeclared'),
        ]);
});
