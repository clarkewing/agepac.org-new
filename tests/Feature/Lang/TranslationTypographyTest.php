<?php

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Typography
|--------------------------------------------------------------------------
|
| Translations use typographic characters: curly apostrophes (’), a real
| ellipsis (…) and the quotation marks of each language, “ ” in English
| and « » in French. French also puts a narrow no-break space (U+202F)
| before ? ! and ; and a no-break space (U+00A0) before : and inside « »,
| so lines never break just before the punctuation. Placeholders such as
| :name are not punctuation.
|
*/

const NARROW_NO_BREAK_SPACE = "\u{202F}";
const NO_BREAK_SPACE = "\u{00A0}";

// Datasets resolve before the app boots, so paths are built from this file's location.
dataset('language files', fn () => collect(glob(dirname(__DIR__, 3).'/lang/*/*.php'))
    ->mapWithKeys(fn (string $path) => [basename(dirname($path)).'/'.basename($path) => [$path]])
    ->all());

dataset('english language files', fn () => collect(glob(dirname(__DIR__, 3).'/lang/en/*.php'))
    ->mapWithKeys(fn (string $path) => [basename($path) => [$path]])
    ->all());

dataset('french language files', fn () => collect(glob(dirname(__DIR__, 3).'/lang/fr/*.php'))
    ->mapWithKeys(fn (string $path) => [basename($path) => [$path]])
    ->all());

it('uses curly apostrophes', function (string $path) {
    expect(offendingStrings($path, "/'/u"))->toBe([]);
})->with('language files');

it('uses a real ellipsis character', function (string $path) {
    expect(offendingStrings($path, '/\.{3}/u'))->toBe([]);
})->with('language files');

it('uses curly quotation marks in English', function (string $path) {
    expect(offendingStrings($path, '/["«»]/u'))->toBe([]);
})->with('english language files');

it('uses guillemets in French', function (string $path) {
    expect(offendingStrings($path, '/["“”]/u'))->toBe([]);
})->with('french language files');

it('puts a narrow no-break space before question and exclamation marks and semicolons', function (string $path) {
    expect(offendingStrings($path, '/[^'.NARROW_NO_BREAK_SPACE.'][?!;]/u'))->toBe([]);
})->with('french language files');

it('puts a no-break space before colons', function (string $path) {
    // A colon followed by a letter starts a placeholder (:name) and one
    // followed by a slash is part of a URL; neither punctuates a sentence.
    expect(offendingStrings($path, '/[^'.NO_BREAK_SPACE.']:(?![\p{L}\/])/u'))->toBe([]);
})->with('french language files');

it('puts a no-break space inside guillemets', function (string $path) {
    expect(offendingStrings($path, '/«(?!'.NO_BREAK_SPACE.')|(?<!'.NO_BREAK_SPACE.')»/u'))->toBe([]);
})->with('french language files');

/**
 * The translated strings in the file matching the pattern, keyed by their dot path.
 *
 * @return array<string, string>
 */
function offendingStrings(string $path, string $pattern): array
{
    return collect(Arr::dot(require $path))
        // Multi-line strings are code samples, whose punctuation is syntax.
        ->filter(fn (mixed $value) => is_string($value) && ! str_contains($value, "\n") && preg_match($pattern, $value))
        ->map(fn (string $value) => Str::limit($value, 80))
        ->all();
}
