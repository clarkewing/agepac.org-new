@use(Illuminate\Support\Str)
@use(Laravel\Head\Facades\Head)
@use(League\CommonMark\Extension\FrontMatter\FrontMatterExtension)

@php
    $document = new FrontMatterExtension()->getFrontMatterParser()
        ->parse(file_get_contents(resource_path("markdown/$terms.md")));

    ['title' => $title, 'description' => $description] = $document->getFrontMatter();

    Head::title($title)->description($description);
@endphp

<x-public::layout>
    <div class="relative overflow-hidden bg-white py-16">
        <div class="hidden lg:absolute lg:inset-y-0 lg:block lg:h-full lg:w-full">
            <div class="relative mx-auto h-full max-w-prose text-lg" aria-hidden="true">
                <x-public::pattern.dots
                    class="absolute top-12 left-full translate-x-32 transform"
                    width="404"
                    height="384"
                />
                <x-public::pattern.dots
                    class="absolute top-1/2 right-full -translate-x-32 -translate-y-1/2 transform"
                    width="404"
                    height="384"
                />
                <x-public::pattern.dots
                    class="absolute bottom-12 left-full translate-x-32 transform"
                    width="404"
                    height="384"
                />
            </div>
        </div>
        <div class="relative px-4 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-prose text-lg">
                <h1>
                    <span class="text-wedgewood-500 block text-center text-base font-semibold tracking-wide uppercase">
                        Juridique
                    </span>
                    <span class="mt-2 block text-center text-3xl leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">
                        {{ $title }}
                    </span>
                </h1>
            </div>

            <div class="prose prose-cyan prose-lg mx-auto mt-12 text-gray-500">
                {!! Str::markdown($document->getContent()) !!}
            </div>
        </div>
    </div>
</x-public::layout>
