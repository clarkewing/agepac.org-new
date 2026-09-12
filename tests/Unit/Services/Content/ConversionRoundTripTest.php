<?php

use App\Enums\PageFormat;
use App\Services\Content\GutenbergConverter;
use App\Services\Content\HtmlContentDiff;
use App\Services\Content\PageRenderer;

/**
 * End-to-end checks of the import pipeline on real legacy page shapes:
 * convert Gutenberg HTML to markdown, render both through the sanitizing
 * pipeline, and verify no content is lost.
 */
function roundTripDiff(string $gutenbergHtml, bool $ignoreAlignment = false): array
{
    $renderer = resolve(PageRenderer::class);

    $original = $renderer->renderBody(PageFormat::HTML, preg_replace('/<!--\s*\/?wp:[^>]*?-->/s', '', $gutenbergHtml));
    $converted = $renderer->renderBody(PageFormat::MARKDOWN, resolve(GutenbergConverter::class)->toMarkdown($gutenbergHtml));

    return resolve(HtmlContentDiff::class)->diff($original, $converted, $ignoreAlignment);
}

it('round-trips a trombinoscope page cleanly', function () {
    $html = <<<'HTML'
        <!-- wp:image -->
        <figure class="wp-block-image"><img src="https://members.agepac.org/laravel-filemanager/photos/3481/Trombinoscopes/20S.PNG" alt=""/></figure>
        <!-- /wp:image -->
        <!-- wp:image -->
        <figure class="wp-block-image"><img src="https://members.agepac.org/laravel-filemanager/photos/3481/Trombinoscopes/20U.PNG" alt=""/></figure>
        <!-- /wp:image -->
        HTML;

    expect(roundTripDiff($html))->toBe([]);
});

it('round-trips headings, paragraphs and file blocks cleanly', function () {
    $html = <<<'HTML'
        <!-- wp:heading -->
        <h2>LCE kesako ?</h2>
        <!-- /wp:heading -->
        <!-- wp:paragraph -->
        <p>La LCE est accessible ici : <a href="https://docs.google.com/spreadsheets/d/xyz/edit">https://docs.google.com/spreadsheets/d/xyz/edit</a></p>
        <!-- /wp:paragraph -->
        <!-- wp:file {"href":"https://members.agepac.org/laravel-filemanager/files/3386/Accord.pdf"} -->
        <div class="wp-block-file"><a href="https://members.agepac.org/laravel-filemanager/files/3386/Accord.pdf">Accord LCE</a><a href="https://members.agepac.org/laravel-filemanager/files/3386/Accord.pdf" class="wp-block-file__button" download>Download</a></div>
        <!-- /wp:file -->
        <!-- wp:separator -->
        <hr class="wp-block-separator"/>
        <!-- /wp:separator -->
        HTML;

    expect(roundTripDiff($html))->toBe([]);
});

it('flags centered paragraphs as differing by alignment only', function () {
    $html = <<<'HTML'
        <!-- wp:paragraph {"align":"center"} -->
        <p style="text-align:center">AGEPAC<br>7 avenue Edouard Belin<br>31400 Toulouse</p>
        <!-- /wp:paragraph -->
        HTML;

    expect(roundTripDiff($html))->not->toBe([])
        ->and(roundTripDiff($html, ignoreAlignment: true))->toBe([]);
});
