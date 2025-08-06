@props(['author', 'photo', 'alt' => ''])

<div class="relative pt-64 pb-10 rounded-2xl shadow-xl overflow-hidden">
    <img
        class="absolute inset-0 h-full w-full object-cover object-top"
        src="{{ $photo }}"
        alt="{{ $alt }}"
    />
    <div class="absolute inset-0 bg-wedgewood-500 mix-blend-multiply opacity-60"></div>
    <div class="absolute inset-0 bg-linear-to-t from-cyan-700 opacity-90"></div>
    <div class="relative px-8">
        <blockquote class="mt-16">
            <div class="relative text-lg font-medium text-white md:grow">
                <x-public::icon.left-quotes class="absolute top-0 left-0 transform -translate-x-3 -translate-y-2 h-8 w-8 text-cyan-400 opacity-50" />
                <p class="relative">{{ $slot }}</p>
            </div>

            <footer class="mt-4">
                <p class="text-base font-semibold text-cyan-200">{!! $author !!}</p>
            </footer>
        </blockquote>
    </div>
</div>
