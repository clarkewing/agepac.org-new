@props(['item'])

<flux:navlist.item
    :href="$item->linkUrl()"
    :icon="$item->icon"
    :badge="$item->badge"
    :badge:color="$item->badge_color ?? 'zinc'"
    :target="$item->target"
>
    {{ $item->label() }}
</flux:navlist.item>
