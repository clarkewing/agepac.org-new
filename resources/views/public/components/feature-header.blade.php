<div class="relative">
    <h2 class="text-center text-3xl leading-8 font-extrabold tracking-tight text-universe sm:text-4xl">
        {{ $title }}
    </h2>
    @isset($description)
        <p class="mt-4 max-w-3xl mx-auto text-center text-xl text-gray-500">
            {{ $description }}
        </p>
    @endisset
</div>
