<?php

use App\Services\Content\HtmlContentDiff;

function diffContent(string $expected, string $actual, bool $ignoreAlignment = false): array
{
    return resolve(HtmlContentDiff::class)->diff($expected, $actual, $ignoreAlignment);
}

it('compares equivalent structures equal despite different wrappers', function () {
    $gutenberg = '<figure class="wp-block-image"><img src="https://example.test/a.png" alt=""></figure>';
    $markdown = '<p><img src="https://example.test/a.png" alt=""></p>';

    expect(diffContent($gutenberg, $markdown))->toBe([]);
});

it('detects a lost link', function () {
    $expected = '<p>Voir <a href="https://example.test">le site</a></p>';
    $actual = '<p>Voir le site</p>';

    expect(diffContent($expected, $actual))->not->toBe([]);
});

it('detects a lost image', function () {
    $expected = '<p><img src="https://example.test/a.png" alt=""><img src="https://example.test/b.png" alt=""></p>';
    $actual = '<p><img src="https://example.test/a.png" alt=""></p>';

    expect(diffContent($expected, $actual))->not->toBe([]);
});

it('detects a changed heading level', function () {
    expect(diffContent('<h2>Titre</h2>', '<h3>Titre</h3>'))->not->toBe([]);
});

it('detects lost alignment unless explicitly ignored', function () {
    $expected = '<p style="text-align:center">Centré</p>';
    $actual = '<p>Centré</p>';

    expect(diffContent($expected, $actual))->not->toBe([])
        ->and(diffContent($expected, $actual, ignoreAlignment: true))->toBe([]);
});

it('ignores the download button of file blocks by design', function () {
    $expected = '<div class="wp-block-file"><a href="https://example.test/f.pdf">Fichier</a><a href="https://example.test/f.pdf" class="wp-block-file__button" download>Download</a></div>';
    $actual = '<p><a href="https://example.test/f.pdf">Fichier</a></p>';

    expect(diffContent($expected, $actual))->toBe([]);
});

it('detects reordered content', function () {
    $expected = '<p>Un</p><p>Deux</p>';
    $actual = '<p>Deux</p><p>Un</p>';

    expect(diffContent($expected, $actual))->toBe(['reordered content']);
});

it('ignores insignificant whitespace', function () {
    expect(diffContent(
        "<p>Du texte\n    sur deux lignes</p>",
        '<p>Du texte sur deux lignes</p>',
    ))->toBe([]);
});
