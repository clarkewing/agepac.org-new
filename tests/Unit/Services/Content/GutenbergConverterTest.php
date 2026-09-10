<?php

use App\Services\Content\GutenbergConverter;

function convertToMarkdown(string $html): string
{
    return resolve(GutenbergConverter::class)->toMarkdown($html);
}

it('converts paragraphs and links to markdown', function () {
    $html = <<<'HTML'
        <!-- wp:paragraph -->
        <p>Envoyer un mail à :</p>
        <!-- /wp:paragraph -->
        <!-- wp:paragraph {"align":"center"} -->
        <p style="text-align:center"><a href="mailto:bonjour@agepac.org"><strong>bonjour@agepac.org</strong></a></p>
        <!-- /wp:paragraph -->
        HTML;

    expect(convertToMarkdown($html))
        ->toContain('Envoyer un mail à :')
        ->toContain('[**bonjour@agepac.org**](mailto:bonjour@agepac.org)')
        ->not->toContain('wp:paragraph')
        ->not->toContain('<p');
});

it('converts image blocks to markdown images', function () {
    $html = <<<'HTML'
        <!-- wp:image -->
        <figure class="wp-block-image"><img src="https://members.agepac.org/laravel-filemanager/photos/3481/Trombinoscopes/20S.PNG" alt=""/></figure>
        <!-- /wp:image -->
        HTML;

    expect(convertToMarkdown($html))
        ->toContain('![](https://members.agepac.org/laravel-filemanager/photos/3481/Trombinoscopes/20S.PNG)');
});

it('converts headings to atx headings', function () {
    $html = <<<'HTML'
        <!-- wp:heading -->
        <h2>LCE kesako ?</h2>
        <!-- /wp:heading -->
        HTML;

    expect(convertToMarkdown($html))
        ->toContain('## LCE kesako ?');
});

it('converts file blocks to a single link without the download button', function () {
    $html = <<<'HTML'
        <!-- wp:file {"href":"https://members.agepac.org/laravel-filemanager/files/3386/Accord.pdf"} -->
        <div class="wp-block-file"><a href="https://members.agepac.org/laravel-filemanager/files/3386/Accord.pdf">Accord LCE</a><a href="https://members.agepac.org/laravel-filemanager/files/3386/Accord.pdf" class="wp-block-file__button" download>Download</a></div>
        <!-- /wp:file -->
        HTML;

    expect(convertToMarkdown($html))
        ->toContain('[Accord LCE](https://members.agepac.org/laravel-filemanager/files/3386/Accord.pdf)')
        ->not->toContain('Download');
});

it('drops spacer blocks', function () {
    $html = <<<'HTML'
        <!-- wp:spacer -->
        <div style="height:100px" aria-hidden="true" class="wp-block-spacer"></div>
        <!-- /wp:spacer -->
        <!-- wp:paragraph -->
        <p>After the spacer.</p>
        <!-- /wp:paragraph -->
        HTML;

    expect(convertToMarkdown($html))
        ->toBe("After the spacer.\n");
});
