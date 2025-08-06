@use(Illuminate\Support\Str)

@php
    $terms = file_get_contents(resource_path("markdown/$terms.md"));

    if (preg_match('/^# ([^\n]+)\n/', $terms, $matches)) {
        $title = $matches[1];
        unset($matches);

        $terms = Str::after($terms, "# $title");
    }
@endphp

<x-public::layout :title="$title">
    <div class="relative py-16 bg-white overflow-hidden">
        <div class="hidden lg:block lg:absolute lg:inset-y-0 lg:h-full lg:w-full">
            <div class="relative h-full text-lg max-w-prose mx-auto" aria-hidden="true">
               <x-public::pattern.dots class="absolute top-12 left-full transform translate-x-32" width="404" height="384" />
               <x-public::pattern.dots class="absolute top-1/2 right-full transform -translate-y-1/2 -translate-x-32" width="404" height="384" />
               <x-public::pattern.dots class="absolute bottom-12 left-full transform translate-x-32" width="404" height="384" />
            </div>
        </div>
        <div class="relative px-4 sm:px-6 lg:px-8">
            <div class="text-lg max-w-prose mx-auto">
                <h1>
                    <span class="block text-base text-center text-wedgewood-500 font-semibold tracking-wide uppercase">
                        Juridique
                    </span>
                    <span class="mt-2 block text-3xl text-center leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">
                        {{ $title }}
                    </span>
                </h1>
            </div>

            <div class="mt-12 prose prose-cyan prose-lg text-gray-500 mx-auto">
                {!! Str::markdown($terms) !!}
            </div>
        </div>
    </div>
</x-public::layout>
