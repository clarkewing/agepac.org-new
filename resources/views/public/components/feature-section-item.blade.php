<div class="relative">
    <dt>
        <div
            {{ $icon->attributes->class([
                'absolute flex items-center justify-center h-12 w-12 rounded-md font-bold',
                'bg-gray-300' => ! Str::contains($icon->attributes->get('class'), 'bg-'),
                'text-2xl text-white' => ! Str::contains($icon->attributes->get('class'), 'text-'),
            ]) }}
        >
            {{ $icon }}
        </div>
        <div class="ml-16 flex items-center justify-between">
            <p class="text-lg leading-6 font-medium text-gray-900">
                {{ $title }}
            </p>
            @isset($badge)
                <span
                    {{ $badge->attributes->class([
                        'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                        'bg-wedgewood-100' => ! Str::contains($badge->attributes->get('class'), 'bg-'),
                        'text-wedgewood-800' => ! Str::contains($badge->attributes->get('class'), 'text-'),
                    ]) }}
                >
                    {{ $badge }}
                </span>
            @endisset
        </div>
    </dt>
    <dd class="mt-2 ml-16 text-base text-justify text-gray-500">
        {{ $description }}
    </dd>
</div>
