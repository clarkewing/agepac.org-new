@use(App\Services\Content\PageRenderer)
@use(Laravel\Head\Facades\Head)

@props(['page'])

@php
    $headerClass = $attributes->pluck('header:class');
    $eyebrowClass = $attributes->pluck('eyebrow:class');
    $titleClass = $attributes->pluck('title:class');
    $bodyClass = $attributes->pluck('body:class');

    $renderer = resolve(PageRenderer::class);

    $html = $renderer->render($page);
    $frontMatter = $renderer->frontMatter($page);

    $eyebrow = $frontMatter['eyebrow'] ?? null;

    Head::title($page->title);

    if (filled($frontMatter['description'] ?? null)) {
        Head::description($frontMatter['description']);
    }
@endphp

<article {{ $attributes->class(['mx-auto w-full [:where(&)]:max-w-3xl']) }}>
    <hgroup @class(['space-y-2', $headerClass])>
        @if (filled($eyebrow))
            <p @class(['font-semibold uppercase [:where(&)]:text-sm [:where(&)]:tracking-wider [:where(&)]:text-zinc-500 dark:[:where(&)]:text-zinc-400', $eyebrowClass])>
                {{ $eyebrow }}
            </p>
        @endif

        <h1 @class(['text-3xl tracking-tight [:where(&)]:font-bold', $titleClass])>{{ $page->title }}</h1>
    </hgroup>

    <div @class(['page-body prose max-w-none dark:prose-invert [:where(&)]:mt-8', $bodyClass])>{{ $html }}</div>
</article>
