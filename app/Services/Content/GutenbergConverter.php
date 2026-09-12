<?php

namespace App\Services\Content;

use League\HTMLToMarkdown\Converter\TableConverter;
use League\HTMLToMarkdown\HtmlConverter;

/**
 * Converts legacy Gutenberg block HTML (as stored by nova-gutenberg) into
 * GitHub Flavored Markdown. Conversion is inherently lossy for presentation
 * details (alignment, download buttons, spacers); ImportLegacyPages verifies
 * every conversion round-trip and falls back to HTML when content would be
 * lost.
 */
class GutenbergConverter
{
    public function toMarkdown(string $gutenbergHtml): string
    {
        $converter = new HtmlConverter([
            'header_style' => 'atx',
            'strip_tags' => true,
            'use_autolinks' => false,
            'remove_nodes' => 'script style',
        ]);

        $converter->getEnvironment()->addConverter(new TableConverter);

        $markdown = $converter->convert($this->normalize($gutenbergHtml));

        return trim(preg_replace("/\n{3,}/", "\n\n", $markdown))."\n";
    }

    /**
     * Strip Gutenberg's block delimiter comments and the markup that has no
     * markdown equivalent by design: the duplicate "Download" button link of
     * file blocks and empty spacer blocks.
     */
    protected function normalize(string $html): string
    {
        $html = preg_replace('/<!--\s*\/?wp:[^>]*?-->/s', '', $html);

        $html = preg_replace('/<a\b[^>]*\bwp-block-file__button\b[^>]*>(?:(?!<\/a>).)*<\/a>/s', '', $html);

        $html = preg_replace('/<div\b[^>]*\bwp-block-spacer\b[^>]*>\s*<\/div>/s', '', $html);

        // Figures are inline-invisible to the converter, so adjacent image
        // blocks would run together into one markdown paragraph. Rewriting
        // them as divs gives each one block-level separation.
        $html = preg_replace(['/<figure\b[^>]*>/', '/<\/figure>/'], ['<div>', '</div>'], $html);

        // Legacy filemanager URLs contain literal spaces, which would break
        // markdown link and image syntax.
        return static::encodeUrlAttributes($html);
    }

    /**
     * Percent-encode the spaces in src and href attribute values, so URLs
     * survive markdown syntax and can be matched without worrying about
     * whitespace. ImportLegacyPages applies the same normalization to pages
     * it keeps as HTML.
     */
    public static function encodeUrlAttributes(string $html): string
    {
        return preg_replace_callback(
            '/\b(src|href)="([^"]*)"/',
            fn (array $matches): string => $matches[1].'="'.str_replace(' ', '%20', $matches[2]).'"',
            $html,
        );
    }
}
