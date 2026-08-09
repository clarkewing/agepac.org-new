@blaze

<div class="relative">
    <h2 class="text-universe text-center text-3xl leading-8 font-extrabold tracking-tight sm:text-4xl">{{ $title }}</h2>
    @isset($description)
        <p class="mx-auto mt-4 max-w-3xl text-center text-xl text-gray-500">{{ $description }}</p>
    @endisset
</div>
