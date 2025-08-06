@props(['type' => 'success'])

@php
    $icon = 'heroicon-m-' . match ($type) {
        'info' => 'information-circle',
        'success' => 'check-circle',
        'warning' => 'exclamation-triangle',
        'danger' => 'exclamation-circle',
        'error' => 'x-circle',
    };

    $color = match ($type) {
        'info' => 'blue',
        'success' => 'green',
        'warning' => 'yellow',
        'danger' => 'red',
        'error' => 'red',
    };
@endphp

<div {{ $attributes->class([
    'rounded-md p-4',
    'bg-blue-50' => $color === 'blue',
    'bg-green-50' => $color === 'green',
    'bg-yellow-50' => $color === 'yellow',
    'bg-red-50' => $color === 'red',
]) }}>
    <div class="flex">
        <div class="shrink-0">
            <x-dynamic-component
                :component="$icon"
                @class([
                    'h-5 w-5',
                    'text-blue-400' => $color === 'blue',
                    'text-green-400' => $color === 'green',
                    'text-yellow-400' => $color === 'yellow',
                    'text-red-400' => $color === 'red',
                ])
            />
        </div>
        <div class="ml-3 flex-1 md:flex md:justify-between">
            <div class="space-y-2">
                @isset($header)
                    <h3 @class([
                        'text-sm font-medium',
                        'text-blue-800' => $color === 'blue',
                        'text-green-800' => $color === 'green',
                        'text-yellow-800' => $color === 'yellow',
                        'text-red-800' => $color === 'red',
                    ])>
                        {{ $header }}
                    </h3>
                @endisset
                <p @class([
                    'text-sm',
                    'text-blue-700' => $color === 'blue',
                    'text-green-700' => $color === 'green',
                    'text-yellow-700' => $color === 'yellow',
                    'text-red-700' => $color === 'red',
                ])>
                    {{ $slot }}
                </p>
            </div>
        </div>
    </div>
</div>
