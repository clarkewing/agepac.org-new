<?php

namespace App\Services\Content;

use Illuminate\Support\Str;
use League\CommonMark\Extension\CommonMark\Node\Inline\Link;
use League\CommonMark\Node\Block\Paragraph;
use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;
use League\CommonMark\Util\HtmlElement;

class DownloadLinkRenderer implements NodeRendererInterface
{
    /**
     * @var list<string>
     */
    protected const array DOCUMENT_EXTENSIONS = ['pdf'];

    /**
     * @param  Link  $node
     */
    public function render(Node $node, ChildNodeRendererInterface $childRenderer): ?HtmlElement
    {
        if (! $this->isStandaloneDocumentLink($node)) {
            return null;
        }

        return new HtmlElement('a', [
            'href' => $node->getUrl(),
            'class' => 'download-button',
            'download' => '',
        ], $childRenderer->renderNodes($node->children()));
    }

    /**
     * A link qualifies when it points at a document and is its paragraph's
     * sole content — a document referenced mid-sentence stays a text link.
     */
    protected function isStandaloneDocumentLink(Link $link): bool
    {
        $extension = Str::lower(pathinfo(uri($link->getUrl())->path(), PATHINFO_EXTENSION));

        return in_array($extension, static::DOCUMENT_EXTENSIONS, true)
            && $link->parent() instanceof Paragraph
            && $link->previous() === null
            && $link->next() === null;
    }
}
