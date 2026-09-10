<?php

use App\Enums\PageFormat;
use App\Models\Page;
use App\Services\Content\PageRenderer;

it('renders markdown as github flavored markdown', function () {
    $html = resolve(PageRenderer::class)->renderBody(PageFormat::MARKDOWN, "## Titre\n\nDu texte avec un [lien](https://agepac.org) et du ~~contenu barré~~.");

    expect($html)
        ->toContain('<h2>Titre</h2>')
        ->toContain('<a href="https://agepac.org">lien</a>')
        ->toContain('<del>contenu barré</del>');
});

it('renders safe raw html inside markdown, github-style', function () {
    $html = resolve(PageRenderer::class)->renderBody(PageFormat::MARKDOWN, 'Du texte <u>souligné</u> et un<br>retour à la ligne.');

    expect($html)
        ->toContain('<u>souligné</u>')
        ->toContain('<br');
});

it('sanitizes dangerous raw html inside markdown', function () {
    $html = resolve(PageRenderer::class)->renderBody(PageFormat::MARKDOWN, "Hello <script>alert('xss')</script><iframe src=\"https://evil.test\"></iframe>");

    expect($html)
        ->toContain('Hello')
        ->not->toContain('<script')
        ->not->toContain('<iframe');
});

it('allows tel links in both formats', function () {
    expect(resolve(PageRenderer::class)->renderBody(PageFormat::MARKDOWN, '[Appeler](tel:+33612345678)'))
        ->toContain('href="tel:')
        ->toContain('Appeler</a>');

    expect(resolve(PageRenderer::class)->renderBody(PageFormat::HTML, '<a href="tel:+33612345678">Appeler</a>'))
        ->toContain('href="tel:');
});

it('sanitizes html bodies but keeps gutenberg presentation attributes', function () {
    $body = '<p style="text-align:center" class="has-text-align-center">Centré</p><script>alert("xss")</script><figure class="wp-block-image"><img src="https://members.agepac.org/photo.png" alt="Photo"></figure>';

    $html = resolve(PageRenderer::class)->renderBody(PageFormat::HTML, $body);

    expect($html)
        ->toContain('style="text-align:center"')
        ->toContain('class="wp-block-image"')
        ->toContain('<img src="https://members.agepac.org/photo.png" alt="Photo"')
        ->not->toContain('<script')
        ->not->toContain('alert(');
});

it('removes unsafe links in both formats', function () {
    expect(resolve(PageRenderer::class)->renderBody(PageFormat::MARKDOWN, '[click](javascript:alert(1))'))
        ->not->toContain('javascript:');

    expect(resolve(PageRenderer::class)->renderBody(PageFormat::HTML, '<a href="javascript:alert(1)">click</a>'))
        ->not->toContain('javascript:');
});

it('renders a standalone document link as a download button', function () {
    $html = resolve(PageRenderer::class)->renderBody(PageFormat::MARKDOWN, '[Accord LCE](https://squawk.agepac.org/attachments/uuid/accord-lce.pdf)');

    expect($html)
        ->toContain('<a href="https://squawk.agepac.org/attachments/uuid/accord-lce.pdf" class="download-button" download>Accord LCE</a>');
});

it('keeps a document link inside a sentence as a plain link', function () {
    $html = resolve(PageRenderer::class)->renderBody(PageFormat::MARKDOWN, 'Voir [le règlement](https://example.test/reglement.pdf) pour les détails.');

    expect($html)
        ->not->toContain('download-button')
        ->toContain('<a href="https://example.test/reglement.pdf">le règlement</a>');
});

it('never renders non-document links as download buttons', function () {
    $html = resolve(PageRenderer::class)->renderBody(PageFormat::MARKDOWN, "[Photothèque](https://photos.app.goo.gl/xyz)\n\n![](https://example.test/photo.png)");

    expect($html)
        ->not->toContain('download-button');
});

it('renders a page through its format', function () {
    $page = Page::factory()->make([
        'body' => '**gras**',
        'format' => PageFormat::MARKDOWN,
    ]);

    expect(resolve(PageRenderer::class)->render($page))
        ->toContain('<strong>gras</strong>');
});
