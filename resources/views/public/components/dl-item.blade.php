@blaze

<div {{ $attributes }}>
    <dt class="space-y-5">
        @isset($icon)
            <div class="bg-vermilion-400 flex h-12 w-12 items-center justify-center rounded-md text-white">
                {{ $icon }}
            </div>
        @endisset
        <p class="text-lg leading-6 font-medium text-gray-900">{{ $title }}</p>
    </dt>
    <dd class="mt-2 text-base text-gray-500">{{ $slot }}</dd>
</div>
