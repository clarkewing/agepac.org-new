<?php

use Illuminate\Support\Str;

it('capitalizes each part of a name', function (string $name, string $expected) {
    expect(Str::nameCase($name))->toBe($expected);
})->with([
    'lower case' => ['jean', 'Jean'],
    'upper case' => ['MERMOZ', 'Mermoz'],
    'hyphenated' => ['jean-pierre', 'Jean-Pierre'],
    'accented initial' => ['élodie', 'Élodie'],
    'accented upper case' => ['ÉLODIE', 'Élodie'],
    'particle' => ['de gaulle', 'de Gaulle'],
    'prefix' => ['mcdonald', 'McDonald'],
    'apostrophe' => ["o'brien", "O'Brien"],
    'extra spaces' => ['  jean   marc ', 'Jean Marc'],
]);
