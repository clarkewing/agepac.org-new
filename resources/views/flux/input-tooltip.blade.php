@php
    $tooltipAttributes = $attributes->only([
        'position',
        'toggleable',
    ])->merge([
        'position' => 'bottom',
        'toggleable' => true,
    ]);

    $buttonAttributes = $attributes->except([
        'position',
        'toggleable',
    ])->merge([
        'variant' => 'subtle',
        'class' => '-me-1',
        'square' => true,
        'size' => 'sm',
        'inset' => 'left right',
    ]);
@endphp

<flux:tooltip :attributes="$tooltipAttributes">
    <flux:button
        :attributes="$buttonAttributes"
        icon="information-circle"
        icon:variant="micro"
    />

    <flux:tooltip.content class="max-w-56">
        {{ $slot }}
    </flux:tooltip.content>
</flux:tooltip>
