<?php

namespace App\Services\Content;

use Dom\Element;
use Dom\HTMLDocument;
use Dom\Node;
use Dom\Text;

/**
 * Compares two rendered HTML fragments by their content rather than their
 * markup: both sides are tokenized into a flat list of block signatures so
 * equivalent structures like <figure><img></figure> and <p><img></p> compare
 * equal, while any lost text, link, image, or heading shows up as a
 * difference.
 */
class HtmlContentDiff
{
    /**
     * Elements whose content is part of the surrounding text flow. Anything
     * else is treated as a block and tokenized separately.
     */
    protected const array INLINE_ELEMENTS = ['a', 'img', 'br', 'strong', 'b', 'em', 'i', 'span', 'code', 'u', 's', 'del', 'ins', 'mark', 'small', 'sub', 'sup', 'abbr'];

    /**
     * @return list<string> Human-readable differences; empty when the
     *                      fragments carry the same content.
     */
    public function diff(string $expected, string $actual, bool $ignoreAlignment = false): array
    {
        $expectedTokens = $this->tokenize($expected, $ignoreAlignment);
        $actualTokens = $this->tokenize($actual, $ignoreAlignment);

        $differences = [];

        foreach (array_diff($expectedTokens, $actualTokens) as $token) {
            $differences[] = "missing: {$token}";
        }

        foreach (array_diff($actualTokens, $expectedTokens) as $token) {
            $differences[] = "added: {$token}";
        }

        if ($differences === [] && $expectedTokens !== $actualTokens) {
            $differences[] = 'reordered content';
        }

        return $differences;
    }

    /**
     * @return list<string>
     */
    public function tokenize(string $html, bool $ignoreAlignment = false): array
    {
        $document = HTMLDocument::createFromString(
            "<!doctype html><html><head><meta charset=\"utf-8\"></head><body>{$html}</body></html>",
            LIBXML_NOERROR,
        );

        $tokens = [];

        $this->walk($document->body, $tokens, $ignoreAlignment);

        return $tokens;
    }

    /**
     * @param  list<string>  $tokens
     */
    protected function walk(Node $node, array &$tokens, bool $ignoreAlignment, string $prefix = ''): void
    {
        foreach ($node->childNodes as $child) {
            if (! $child instanceof Element || $this->isIgnored($child)) {
                continue;
            }

            $tag = strtolower($child->tagName);

            // Inline content was already captured in the enclosing block's
            // signature.
            if (in_array($tag, static::INLINE_ELEMENTS, true)) {
                continue;
            }

            if (in_array($tag, ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'], true)) {
                $this->pushBlock($prefix.$tag, $child, $tokens, $ignoreAlignment);

                continue;
            }

            if ($tag === 'hr') {
                $tokens[] = $prefix.'hr';

                continue;
            }

            // Any other element is a block wrapper: its direct inline content
            // forms one anonymous block (so <li><img></li>, <p><img></p> and
            // <figure><img></figure> compare equal), and nested blocks are
            // tokenized on their own.
            $this->pushBlock($prefix.'p', $child, $tokens, $ignoreAlignment);

            $this->walk($child, $tokens, $ignoreAlignment, $tag === 'blockquote' ? 'q>'.$prefix : $prefix);
        }
    }

    /**
     * @param  list<string>  $tokens
     */
    protected function pushBlock(string $label, Element $element, array &$tokens, bool $ignoreAlignment): void
    {
        $signature = $this->collapseWhitespace($this->inlineSignature($element));

        if ($signature === '') {
            return;
        }

        $alignment = $ignoreAlignment ? null : $this->alignment($element);

        $tokens[] = $label.'|'.$signature.($alignment ? "|align:{$alignment}" : '');
    }

    /**
     * The signature of an element's direct inline content; nested block
     * elements are tokenized separately by walk() and excluded here.
     */
    protected function inlineSignature(Element $element): string
    {
        $signature = '';

        foreach ($element->childNodes as $child) {
            if ($child instanceof Text) {
                $signature .= $child->textContent;

                continue;
            }

            if (! $child instanceof Element || $this->isIgnored($child)) {
                continue;
            }

            $tag = strtolower($child->tagName);

            if (! in_array($tag, static::INLINE_ELEMENTS, true)) {
                continue;
            }

            $signature .= match ($tag) {
                'a' => $this->linkSignature($child),
                'img' => '!['.$child->getAttribute('alt').']('.$this->normalizeUrl($child->getAttribute('src')).')',
                'br' => "\u{23CE}",
                'strong', 'b' => '**'.$this->inlineSignature($child).'**',
                'em', 'i' => '*'.$this->inlineSignature($child).'*',
                default => $this->inlineSignature($child),
            };
        }

        return $signature;
    }

    protected function linkSignature(Element $element): string
    {
        $text = $this->collapseWhitespace($this->inlineSignature($element));
        $href = $this->normalizeUrl($element->getAttribute('href'));

        // A link whose text is its own URL is equivalent to the bare URL:
        // the GFM pipeline autolinks bare URLs when rendering anyway.
        if ($text === $href) {
            return $text;
        }

        return "[{$text}]({$href})";
    }

    /**
     * Percent-encoding differences (spaces, accents) between the legacy
     * markup and markdown-rendered URLs are not content differences.
     */
    protected function normalizeUrl(?string $url): string
    {
        return rawurldecode($url ?? '');
    }

    protected function isIgnored(Element $element): bool
    {
        $classes = ' '.$element->getAttribute('class').' ';

        return str_contains($classes, ' wp-block-file__button ')
            || str_contains($classes, ' wp-block-spacer ');
    }

    protected function alignment(Element $element): ?string
    {
        if (preg_match('/text-align:\s*(center|right|justify)/', $element->getAttribute('style') ?? '', $matches)) {
            return $matches[1];
        }

        return null;
    }

    protected function collapseWhitespace(string $value): string
    {
        $value = preg_replace('/\s+/u', ' ', $value);

        // Whitespace around a line break carries no meaning once rendered,
        // and a break at the very start or end of a block is invisible.
        $value = preg_replace('/\s*\x{23CE}\s*/u', "\u{23CE}", $value);
        $value = preg_replace('/^\x{23CE}+|\x{23CE}+$/u', '', $value);

        return trim($value);
    }
}
