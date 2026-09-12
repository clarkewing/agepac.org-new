<?php

use App\Enums\PageFormat;
use App\Models\Page;
use App\Services\Content\PageRenderer;
use Illuminate\Support\HtmlString;

function renderMarkdown(string $body): string
{
    return resolve(PageRenderer::class)->renderBody(PageFormat::MARKDOWN, $body)->toHtml();
}

function renderHtml(string $body): string
{
    return resolve(PageRenderer::class)->renderBody(PageFormat::HTML, $body)->toHtml();
}

it('renders markdown as github flavored markdown', function () {
    expect(renderMarkdown("## Titre\n\nDu texte avec un [lien](https://agepac.org) et du ~~contenu barré~~."))
        ->toContain('<h2>Titre</h2>')
        ->toContain('<a href="https://agepac.org">lien</a>')
        ->toContain('<del>contenu barré</del>');
});

it('renders safe raw html inside markdown, github-style', function () {
    expect(renderMarkdown('Du texte <u>souligné</u> et un<br>retour à la ligne.'))
        ->toContain('<u>souligné</u>')
        ->toContain('<br');
});

it('sanitizes dangerous raw html inside markdown', function () {
    expect(renderMarkdown("Hello <script>alert('xss')</script><iframe src=\"https://evil.test\"></iframe>"))
        ->toContain('Hello')
        ->not->toContain('<script')
        ->not->toContain('<iframe');
});

it('allows tel links in both formats', function () {
    expect(renderMarkdown('[Appeler](tel:+33612345678)'))
        ->toContain('href="tel:')
        ->toContain('Appeler</a>');

    expect(renderHtml('<a href="tel:+33612345678">Appeler</a>'))
        ->toContain('href="tel:');
});

it('sanitizes html bodies but keeps gutenberg presentation attributes', function () {
    expect(renderHtml('<p style="text-align:center" class="has-text-align-center">Centré</p><script>alert("xss")</script><figure class="wp-block-image"><img src="https://members.agepac.org/photo.png" alt="Photo"></figure>'))
        ->toContain('style="text-align:center"')
        ->toContain('class="wp-block-image"')
        ->toContain('<img src="https://members.agepac.org/photo.png" alt="Photo"')
        ->not->toContain('<script')
        ->not->toContain('alert(');
});

it('removes unsafe links in both formats', function () {
    expect(renderMarkdown('[click](javascript:alert(1))'))
        ->not->toContain('javascript:');

    expect(renderHtml('<a href="javascript:alert(1)">click</a>'))
        ->not->toContain('javascript:');
});

it('renders a standalone document link as a download button', function () {
    expect(renderMarkdown('[Accord LCE](https://squawk.agepac.org/attachments/uuid/accord-lce.pdf)'))
        ->toContain('<a href="https://squawk.agepac.org/attachments/uuid/accord-lce.pdf" class="download-button" download>Accord LCE</a>');
});

it('keeps a document link inside a sentence as a plain link', function () {
    expect(renderMarkdown('Voir [le règlement](https://example.test/reglement.pdf) pour les détails.'))
        ->not->toContain('download-button')
        ->toContain('<a href="https://example.test/reglement.pdf">le règlement</a>');
});

it('never renders non-document links as download buttons', function () {
    expect(renderMarkdown("[Photothèque](https://photos.app.goo.gl/xyz)\n\n![](https://example.test/photo.png)"))
        ->not->toContain('download-button');
});

it('strips front matter from rendered markdown', function () {
    expect(renderMarkdown("---\ndescription: Une page de test.\n---\n\n## Titre"))
        ->toContain('<h2>Titre</h2>')
        ->not->toContain('description')
        ->not->toContain('---');
});

it('parses front matter from markdown pages only', function () {
    $renderer = resolve(PageRenderer::class);

    $markdown = Page::factory()->make(['body' => "---\ndescription: Une page de test.\n---\n\nContenu"]);
    $plain = Page::factory()->make(['body' => 'Contenu']);
    $html = Page::factory()->html()->make(['body' => '<p>Contenu</p>']);

    expect($renderer->frontMatter($markdown))->toBe(['description' => 'Une page de test.'])
        ->and($renderer->frontMatter($plain))->toBe([])
        ->and($renderer->frontMatter($html))->toBe([]);
});

it('renders a page through its format as blade-safe html', function () {
    $page = Page::factory()->make([
        'body' => '**gras**',
        'format' => PageFormat::MARKDOWN,
    ]);

    expect(resolve(PageRenderer::class)->render($page))
        ->toBeInstanceOf(HtmlString::class)
        ->toHtml()->toContain('<strong>gras</strong>');
});
