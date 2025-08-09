@props([
    'id' => uniqid(),
    'width',
    'height',
])

<svg
    width="{{ $width }}"
    height="{{ $height }}"
    viewBox="0 0 {{ $width }} {{ $height }}"
    {{ $attributes->merge(['fill' => 'none', 'aria-hidden' => 'true']) }}
>
    <defs>
        <pattern id="{{ $id }}" x="0" y="0" width="20" height="20" patternUnits="userSpaceOnUse">
            <rect x="0" y="0" width="4" height="4" class="text-gray-200" fill="currentColor" />
        </pattern>
    </defs>

    <rect width="{{ $width }}" height="{{ $height }}" fill="url(#{{ $id }})" />
</svg>
