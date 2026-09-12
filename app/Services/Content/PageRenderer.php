<?php

namespace App\Services\Content;

use App\Enums\PageFormat;
use App\Models\Page;
use Illuminate\Support\HtmlString;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\CommonMark\Node\Inline\Link;
use League\CommonMark\Extension\FrontMatter\FrontMatterExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\MarkdownConverter;
use Symfony\Component\HtmlSanitizer\HtmlSanitizer;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;

/**
 * The single rendering pipeline for page bodies: markdown is rendered as GFM
 * with raw HTML stripped, and every output — markdown or HTML — passes
 * through the sanitizer. Page bodies must never be rendered any other way.
 */
class PageRenderer
{
    public function render(Page $page): HtmlString
    {
        return $this->renderBody($page->format, $page->body);
    }

    public function renderBody(PageFormat $format, string $body): HtmlString
    {
        $html = match ($format) {
            PageFormat::MARKDOWN => $this->renderMarkdown($body),
            PageFormat::HTML => $body,
        };

        return new HtmlString($this->sanitize($html));
    }

    /**
     * The front matter of a markdown page's body, following the public terms
     * pages' convention (e.g. a `description` for the page head). HTML pages
     * have none.
     *
     * @return array<string, mixed>
     */
    public function frontMatter(Page $page): array
    {
        if ($page->format !== PageFormat::MARKDOWN) {
            return [];
        }

        $frontMatter = new FrontMatterExtension()->getFrontMatterParser()
            ->parse($page->body)
            ->getFrontMatter();

        return is_array($frontMatter) ? $frontMatter : [];
    }

    protected function renderMarkdown(string $markdown): string
    {
        // Raw HTML in markdown is rendered, GitHub-style: the sanitizer
        // downstream is the enforcement layer, exactly as for html pages.
        $environment = new Environment([
            'html_input' => 'allow',
            'allow_unsafe_links' => false,
            'max_nesting_level' => 50,
        ]);

        $environment->addExtension(new CommonMarkCoreExtension);
        $environment->addExtension(new FrontMatterExtension);
        $environment->addExtension(new GithubFlavoredMarkdownExtension);
        $environment->addRenderer(Link::class, new DownloadLinkRenderer, priority: 10);

        return (new MarkdownConverter($environment))->convert($markdown)->getContent();
    }

    /**
     * The allowlist covers standard content markup plus the class and style
     * attributes that Gutenberg block markup relies on (wp-block-* classes,
     * text-align styles).
     */
    protected function sanitize(string $html): string
    {
        $config = (new HtmlSanitizerConfig)
            ->allowSafeElements()
            ->allowLinkSchemes(['https', 'http', 'mailto', 'tel'])
            ->allowRelativeLinks()
            ->allowMediaSchemes(['https', 'http'])
            ->allowRelativeMedias()
            ->allowAttribute('class', '*')
            ->allowAttribute('style', '*')
            ->allowAttribute('download', ['a'])
            // The default limit (20KB) TRUNCATES silently; -1 disables it.
            // Input is admin-authored and bounded by the body column, so a
            // DoS-guard here would only ever manifest as silent content loss.
            // Reinstate a limit if this pipeline ever renders untrusted input.
            ->withMaxInputLength(-1);

        return (new HtmlSanitizer($config))->sanitize($html);
    }
}
