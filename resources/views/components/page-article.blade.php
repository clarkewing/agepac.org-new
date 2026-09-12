@use(App\Services\Content\PageRenderer)
@use(Laravel\Head\Facades\Head)

@props(['page'])

@php
    $headerClass = $attributes->pluck('header:class');
    $titleClass = $attributes->pluck('title:class');
    $bodyClass = $attributes->pluck('body:class');

    $html = resolve(PageRenderer::class)->render($page);

    Head::title($page->title);
@endphp

<article {{ $attributes->class(['mx-auto w-full [:where(&)]:max-w-3xl']) }}>
    <hgroup @class(['space-y-2', $headerClass])>
        <h1 @class(['text-3xl tracking-tight [:where(&)]:font-bold', $titleClass])>{{ $page->title }}</h1>
    </hgroup>

    <div @class(['page-body prose max-w-none [:where(&)]:mt-8 dark:[:where(&)]:prose-invert', $bodyClass])>
        {{ $html }}
    </div>
</article>
