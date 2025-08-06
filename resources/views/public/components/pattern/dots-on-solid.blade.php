@props(['width' => 640, 'height' => 784])

@php($id = Str::uuid())

<svg
    width="{{ $width }}"
    height="{{ $height }}"
    viewBox="0 0 {{ $width }} {{ $height }}"
    {{ $attributes->merge(['fill' => 'none', 'aria-hidden' => 'true']) }}
>
    <defs>
        <pattern
            id="{{ $id }}"
            x="{{ $width * 118 / 640 }}"
            y="0"
            width="20"
            height="20"
            patternUnits="userSpaceOnUse"
        >
            <rect
                x="0"
                y="0"
                width="4"
                height="4"
                class="text-gray-200"
                fill="currentColor"
            />
        </pattern>
    </defs>

    <rect
        y="{{ $height * 72 / 784 }}"
        width="{{ $width }}"
        height="{{ $width }}"
        class="text-gray-50"
        fill="currentColor"
    />
    <rect
        x="{{ $width * 118 / 640 }}"
        width="{{ $width * 404 / 640 }}"
        height="{{ $height }}"
        fill="url(#{{ $id }})"
    />
</svg>
