@props(['item'])

@php
    // Icons are stored as validated Blade Icons names (e.g. `heroicon-o-home`);
    // Flux takes the bare name plus the variant the set prefix encodes.
    preg_match('/^heroicon-([a-z])-(.+)$/', $item->icon ?? '', $matches);

    $icon = $matches[2] ?? null;
    $iconVariant = match ($matches[1] ?? null) {
        'o' => 'outline',
        's' => 'solid',
        'm' => 'mini',
        'c' => 'micro',
        default => null,
    };
@endphp

<flux:navlist.item
    :href="$item->url"
    :icon="$icon"
    :icon:variant="$iconVariant"
    :badge="$item->data['badge'] ?? null"
    :badge:color="$item->data['badge_color'] ?? 'zinc'"
    :target="$item->target"
    :current="$item->isActive()"
>
    {{ $item->title }}
</flux:navlist.item>
