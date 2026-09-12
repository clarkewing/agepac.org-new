@use(App\Enums\NavItemType)
@use(App\Models\NavItem)

@php
    $items = NavItem::query()
        ->with(['page', 'children.page'])
        ->whereNull('parent_id')
        ->orderBy('order')
        ->get()
        ->filter->isVisible();
@endphp

@foreach ($items as $item)
    @if ($item->type === NavItemType::GROUP)
        @php($children = $item->children->filter->isVisible())

        @if ($children->isNotEmpty())
            <flux:navlist.group :heading="$item->label()" :expandable="$item->collapsible" class="grid">
                @foreach ($children as $child)
                    <x-sidebar-nav.item :item="$child" />
                @endforeach
            </flux:navlist.group>
        @endif
    @else
        <x-sidebar-nav.item :item="$item" />
    @endif
@endforeach
