@use(Datlechin\FilamentMenuBuilder\Models\Menu)

@php($tree = Menu::location('sidebar')?->tree() ?? [])

@foreach ($tree as $node)
    @if ($node->type === 'text' && filled($node->children))
        <flux:navlist.group
            :heading="$node->title"
            :expandable="(bool) ($node->data['expandable'] ?? false)"
            :expanded="(bool) ($node->data['expanded'] ?? true) || $node->isActiveTrail()"
            class="grid"
        >
            @foreach ($node->children as $child)
                <x-sidebar-nav.item :item="$child" />
            @endforeach
        </flux:navlist.group>
    @elseif ($node->type !== 'text')
        <x-sidebar-nav.item :item="$node" />
    @endif
@endforeach
