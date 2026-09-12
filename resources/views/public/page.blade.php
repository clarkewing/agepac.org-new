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
            <x-page-article
                class="max-w-prose text-lg"
                eyebrow:class="text-wedgewood-500 text-center text-base tracking-wide"
                title:class="text-center leading-8 font-extrabold text-gray-900 sm:text-4xl"
                body:class="prose-cyan prose-lg mt-12 text-gray-500"
                :$page
            />
        </div>
    </div>
</x-public::layout>
